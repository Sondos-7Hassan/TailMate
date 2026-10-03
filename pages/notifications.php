<?php require_once __DIR__ . '/../config/config.php';
session_start();
require_once __DIR__ . '/../config/connection.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "/auth/login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

// معالجة حذف رسالة contact
if ($isAdmin && isset($_GET['delete_contact']) && isset($_GET['id'])) {
    $messageId = (int) $_GET['id'];
    $delete = $connect->prepare("DELETE FROM contact_messages WHERE id = ?");
    $delete->execute([$messageId]);
    header("Location: " . BASE_URL . "/pages/notifications.php?tab=contact");
    exit();
}

// معالجة إرسال رسالة جديدة (من Admin إلى User أو من User إلى Admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $toUserId = (int)$_POST['to_user_id'];
    $message = trim($_POST['message']);
    $requestId = isset($_POST['request_id']) && !empty($_POST['request_id']) ? (int)$_POST['request_id'] : null;
    
    // التحقق من أن request_id موجود فعلاً إذا لم يكن null
    if ($requestId !== null) {
        $checkRequest = $connect->prepare("SELECT id FROM adoption_requests WHERE id = ?");
        $checkRequest->execute([$requestId]);
        if (!$checkRequest->fetch()) {
            $requestId = null; // إذا لم يوجد الطلب، اجعله null
        }
    }
    
    if (!empty($message)) {
        $insert = $connect->prepare("
            INSERT INTO user_messages (from_user_id, to_user_id, message, request_id, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $insert->execute([$userId, $toUserId, $message, $requestId]);
        header("Location: " . BASE_URL . "/pages/notifications.php");
        exit();
    }
}

// معالجة تعليم الرسالة كمقروءة
if (isset($_GET['mark_read']) && isset($_GET['id'])) {
    $messageId = (int)$_GET['id'];
    $update = $connect->prepare("UPDATE user_messages SET is_read = 1 WHERE id = ? AND to_user_id = ?");
    $update->execute([$messageId, $userId]);
    header("Location: " . BASE_URL . "/pages/notifications.php");
    exit();
}

// جلب الرسائل
if ($isAdmin) {
    // Admin يشوف كل الرسائل
    $messagesQuery = $connect->query("
        SELECT 
            um.*,
            u_from.name AS from_name,
            u_from.email AS from_email,
            u_to.name AS to_name,
            u_to.email AS to_email,
            ar.animal_id,
            a.name AS animal_name
        FROM user_messages um
        JOIN users u_from ON um.from_user_id = u_from.id
        JOIN users u_to ON um.to_user_id = u_to.id
        LEFT JOIN adoption_requests ar ON um.request_id = ar.id
        LEFT JOIN animals a ON ar.animal_id = a.id
        ORDER BY um.created_at DESC
    ");
} else {
    // User يشوف رسائله فقط
    $messagesQuery = $connect->prepare("
        SELECT 
            um.*,
            u_from.name AS from_name,
            u_from.email AS from_email,
            u_to.name AS to_name,
            u_to.email AS to_email,
            ar.animal_id,
            a.name AS animal_name
        FROM user_messages um
        JOIN users u_from ON um.from_user_id = u_from.id
        JOIN users u_to ON um.to_user_id = u_to.id
        LEFT JOIN adoption_requests ar ON um.request_id = ar.id
        LEFT JOIN animals a ON ar.animal_id = a.id
        WHERE um.from_user_id = ? OR um.to_user_id = ?
        ORDER BY um.created_at DESC
    ");
    $messagesQuery->execute([$userId, $userId]);
}

$messages = $messagesQuery->fetchAll(PDO::FETCH_ASSOC);

// جلب رسائل Contact إذا كان Admin
$contactMessages = [];
if ($isAdmin) {
    $contactQuery = $connect->query("
        SELECT id AS message_id, name, email, phone, message, created_at
        FROM contact_messages
        ORDER BY id DESC
    ");
    $contactMessages = $contactQuery->fetchAll(PDO::FETCH_ASSOC);
}

// جلب قائمة المستخدمين للـ Admin
$users = [];
if ($isAdmin) {
    $usersQuery = $connect->query("SELECT id, name, email FROM users WHERE is_admin = 0 ORDER BY name");
    $users = $usersQuery->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php include __DIR__ . '/../includes/head.php'; ?>

<body>
<?php if ($isAdmin): ?>
    <!-- Admin Dashboard -->
    <div class="wrapper">
        <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

        <div class="content">
            <?php include __DIR__ . '/../includes/admin_navbar.php'; ?>

            <div>
                <h2 class="mb-4">Messages</h2>

                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs mb-4" id="messagesTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo (!isset($_GET['tab']) || $_GET['tab'] == 'users') ? 'active' : ''; ?>" 
                                id="users-tab" data-bs-toggle="tab" data-bs-target="#users-messages" type="button">
                            <i class="bi bi-chat-dots"></i> User Messages
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'contact') ? 'active' : ''; ?>" 
                                id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-messages" type="button">
                            <i class="bi bi-envelope"></i> Contact Messages
                        </button>
                    </li>
                </ul>

                <!-- Tabs Content -->
                <div class="tab-content" id="messagesTabContent">
                    <!-- User Messages Tab -->
                    <div class="tab-pane fade <?php echo (!isset($_GET['tab']) || $_GET['tab'] == 'users') ? 'show active' : ''; ?>" 
                         id="users-messages" role="tabpanel">
                        
                        <!-- زر إرسال رسالة جديدة -->
                        <button class="btn mb-3" style="background-color: #E7A769; color: #5C3A21;" data-bs-toggle="modal" data-bs-target="#newMessageModal">
                            <i class="bi bi-plus-circle"></i> Send New Message
                        </button>

                <!-- قائمة الرسائل -->
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead style="background-color: #E7A769; color: #5C3A21;">
                            <tr>
                                <th>Type</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Message</th>
                                <th>Request</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($messages as $msg): ?>
                            <tr>
                                <td>
                                    <?php 
                                    // تحديد نوع الرسالة
                                    $messageType = 'New';
                                    $badgeClass = 'primary';
                                    $icon = 'envelope';
                                    
                                    if ($msg['request_id']) {
                                        $messageType = 'Request';
                                        $badgeClass = 'info';
                                        $icon = 'clipboard-check';
                                    }
                                    
                                    // التحقق من أنها رد
                                    $checkReply = $connect->prepare("SELECT id FROM user_messages WHERE to_user_id = ? AND from_user_id = ? AND request_id = ? AND id < ? LIMIT 1");
                                    $checkReply->execute([$msg['from_user_id'], $msg['to_user_id'], $msg['request_id'], $msg['id']]);
                                    if ($checkReply->fetch()) {
                                        $messageType = 'Reply';
                                        $badgeClass = 'success';
                                        $icon = 'reply';
                                    }
                                    ?>
                                    <span class="badge bg-<?php echo $badgeClass; ?>">
                                        <i class="bi bi-<?php echo $icon; ?>"></i> <?php echo $messageType; ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($msg['from_name']); ?></td>
                                <td><?php echo htmlspecialchars($msg['to_name']); ?></td>
                                <td><?php echo htmlspecialchars(substr($msg['message'], 0, 50)) . '...'; ?></td>
                                <td>
                                    <?php if ($msg['request_id']): ?>
                                        <span class="badge bg-info">Request #<?php echo $msg['request_id']; ?></span>
                                        <?php if ($msg['animal_name']): ?>
                                            <br><small><?php echo htmlspecialchars($msg['animal_name']); ?></small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('Y-m-d H:i', strtotime($msg['created_at'])); ?></td>
                                <td>
                                    <?php if ($msg['is_read']): ?>
                                        <span class="badge bg-secondary">Read</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Unread</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $msg['id']; ?>">View</button>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#replyModal<?php echo $msg['id']; ?>">Reply</button>
                                </td>
                            </tr>

                            <!-- Modal للعرض -->
                            <div class="modal fade" id="viewModal<?php echo $msg['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color: #E7A769;">
                                            <h5 class="modal-title" style="color: #5C3A21;">Message Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>From:</strong> <?php echo htmlspecialchars($msg['from_name']); ?> (<?php echo htmlspecialchars($msg['from_email']); ?>)</p>
                                            <p><strong>To:</strong> <?php echo htmlspecialchars($msg['to_name']); ?> (<?php echo htmlspecialchars($msg['to_email']); ?>)</p>
                                            <p><strong>Date:</strong> <?php echo date('Y-m-d H:i', strtotime($msg['created_at'])); ?></p>
                                            <?php if ($msg['request_id']): ?>
                                            <p><strong>Related Request:</strong> #<?php echo $msg['request_id']; ?>
                                                <?php if ($msg['animal_name']): ?>
                                                    - <?php echo htmlspecialchars($msg['animal_name']); ?>
                                                <?php endif; ?>
                                            </p>
                                            <?php endif; ?>
                                            <hr>
                                            <p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal للرد -->
                            <div class="modal fade" id="replyModal<?php echo $msg['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color: #E7A769;">
                                            <h5 class="modal-title" style="color: #5C3A21;">Reply to Message</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST">
                                            <div class="modal-body">
                                                <p><strong>Reply to:</strong> <?php echo htmlspecialchars($msg['from_name']); ?></p>
                                                <input type="hidden" name="to_user_id" value="<?php echo $msg['from_user_id']; ?>">
                                                <?php if (!empty($msg['request_id'])): ?>
                                                    <input type="hidden" name="request_id" value="<?php echo $msg['request_id']; ?>">
                                                <?php endif; ?>
                                                <textarea name="message" class="form-control" rows="4" placeholder="Type your message..." required></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" name="send_message" class="btn" style="background-color: #E7A769; color: #5C3A21;">Send</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                    </div>
                    
                    <!-- Contact Messages Tab -->
                    <div class="tab-pane fade <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'contact') ? 'show active' : ''; ?>" 
                         id="contact-messages" role="tabpanel">
                        
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead style="background-color: #E7A769; color: #5C3A21;">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Message</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($contactMessages)): ?>
                                        <?php foreach ($contactMessages as $msg): ?>
                                        <tr>
                                            <td><?php echo $msg['message_id']; ?></td>
                                            <td><?php echo htmlspecialchars($msg['name']); ?></td>
                                            <td><?php echo htmlspecialchars($msg['email']); ?></td>
                                            <td><?php echo htmlspecialchars($msg['phone'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars(substr($msg['message'], 0, 50)) . '...'; ?></td>
                                            <td><?php echo isset($msg['created_at']) ? date('Y-m-d', strtotime($msg['created_at'])) : 'N/A'; ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewContactModal<?php echo $msg['message_id']; ?>">
                                                    <i class="bi bi-eye"></i> View
                                                </button>
                                                <a href="<?= BASE_URL ?>/pages/notifications.php?delete_contact=1&id=<?php echo $msg['message_id']; ?>&tab=contact" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Are you sure you want to delete this message?')">
                                                    <i class="bi bi-trash"></i> Delete
                                                </a>
                                            </td>
                                        </tr>
                                        
                                        <!-- Modal للعرض -->
                                        <div class="modal fade" id="viewContactModal<?php echo $msg['message_id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header" style="background-color: #E7A769;">
                                                        <h5 class="modal-title" style="color: #5C3A21;">Contact Message #<?php echo $msg['message_id']; ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>Name:</strong> <?php echo htmlspecialchars($msg['name']); ?></p>
                                                        <p><strong>Email:</strong> <?php echo htmlspecialchars($msg['email']); ?></p>
                                                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($msg['phone'] ?? 'N/A'); ?></p>
                                                        <p><strong>Date:</strong> <?php echo isset($msg['created_at']) ? date('Y-m-d H:i', strtotime($msg['created_at'])) : 'N/A'; ?></p>
                                                        <hr>
                                                        <p><strong>Message:</strong></p>
                                                        <p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">No contact messages</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

    <!-- Modal لإرسال رسالة جديدة -->
    <div class="modal fade" id="newMessageModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #E7A769;">
                    <h5 class="modal-title" style="color: #5C3A21;">Send New Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">To User:</label>
                            <select name="to_user_id" class="form-select" required>
                                <option value="">Select User...</option>
                                <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['name']); ?> (<?php echo htmlspecialchars($user['email']); ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message:</label>
                            <textarea name="message" class="form-control" rows="4" placeholder="Type your message..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="send_message" class="btn" style="background-color: #E7A769; color: #5C3A21;">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- User View -->
    <?php include __DIR__ . '/../includes/user_navbar.php'; ?>
    
    <style>
        body {
            background-color: #FFF6EC;
            color: #5C3A21;
            font-family: Arial, sans-serif;
        }
        .messages-container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
        }
        .profile-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 4px 6px rgba(92, 58, 33, 0.1);
        }
        .section-title {
            color: #5C3A21;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section-title i {
            color: #E7A769;
            font-size: 1.5rem;
        }
        .message-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        .message-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.12);
        }
        .message-card.unread {
            border-left: 4px solid #E7A769;
            background: linear-gradient(to right, #fffbf5, white);
        }
        .btn-custom {
            background: linear-gradient(135deg, #E7A769, #d49659);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(231, 167, 105, 0.3);
            color: white;
        }
        .btn-outline-custom {
            border: 2px solid #E7A769;
            color: #E7A769;
            background: transparent;
            padding: 6px 15px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-outline-custom:hover {
            background: #E7A769;
            color: white;
            transform: translateY(-1px);
        }
        .badge-custom {
            background: #E7A769;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
    </style>

    <div class="messages-container">
        <div class="profile-card">
            <h2 class="section-title">
                <i class="bi bi-chat-dots"></i> My Messages
            </h2>

            <!-- زر إرسال رسالة جديدة للـ Admin -->
            <button class="btn btn-custom mb-4" data-bs-toggle="modal" data-bs-target="#sendToAdminModal">
                <i class="bi bi-envelope-plus"></i> Send Message to Admin
            </button>

            <?php if (empty($messages)): ?>
                <div class="alert alert-info" style="border-left: 4px solid #E7A769;">
                    <i class="bi bi-info-circle"></i> No messages yet.
                </div>
            <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="message-card <?php echo !$msg['is_read'] && $msg['to_user_id'] == $userId ? 'unread' : ''; ?>">
                        <div class="d-flex justify-content-between align-items-start">
                            <div style="flex: 1;">
                                <h5>
                                    <?php if ($msg['from_user_id'] == $userId): ?>
                                        <i class="bi bi-send text-muted"></i> To: <?php echo htmlspecialchars($msg['to_name']); ?>
                                    <?php else: ?>
                                        <i class="bi bi-inbox text-primary"></i> From: <?php echo htmlspecialchars($msg['from_name']); ?>
                                        <?php if (!$msg['is_read']): ?>
                                            <span class="badge badge-custom">New</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </h5>
                                <small class="text-muted">
                                    <i class="bi bi-clock"></i> <?php echo date('M d, Y - h:i A', strtotime($msg['created_at'])); ?>
                                </small>
                                <?php if ($msg['request_id']): ?>
                                    <br>
                                    <span class="badge bg-info mt-2">
                                        <i class="bi bi-file-earmark-text"></i> Request #<?php echo $msg['request_id']; ?>
                                    </span>
                                    <?php if ($msg['animal_name']): ?>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($msg['animal_name']); ?></span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            <div>
                                <?php if ($msg['to_user_id'] == $userId && !$msg['is_read']): ?>
                                    <a href="<?= BASE_URL ?>/pages/notifications.php?mark_read=1&id=<?php echo $msg['id']; ?>" class="btn btn-sm btn-outline-custom">
                                        <i class="bi bi-check2"></i> Mark as Read
                                    </a>
                                <?php endif; ?>
                                <?php if ($msg['from_user_id'] != $userId): ?>
                                    <button class="btn btn-sm btn-custom ms-2" data-bs-toggle="modal" data-bs-target="#replyModal<?php echo $msg['id']; ?>">
                                        <i class="bi bi-reply"></i> Reply
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mt-3 p-3" style="background-color: #FFF6EC; border-radius: 8px; border-left: 3px solid #E7A769;">
                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                        </div>
                    </div>
                    
                    <!-- Modal للرد على الرسالة -->
                    <?php if ($msg['from_user_id'] != $userId): ?>
                    <div class="modal fade" id="replyModal<?php echo $msg['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #E7A769;">
                                    <h5 class="modal-title" style="color: white;">
                                        <i class="bi bi-reply"></i> Reply to <?php echo htmlspecialchars($msg['from_name']); ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST">
                                    <div class="modal-body">
                                        <p><strong>Original Message:</strong></p>
                                        <p class="bg-light p-3 rounded" style="border-left: 3px solid #E7A769;">
                                            <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                                        </p>
                                        <input type="hidden" name="to_user_id" value="<?php echo $msg['from_user_id']; ?>">
                                        <?php if (!empty($msg['request_id'])): ?>
                                            <input type="hidden" name="request_id" value="<?php echo $msg['request_id']; ?>">
                                        <?php endif; ?>
                                        <label class="form-label"><strong>Your Reply:</strong></label>
                                        <textarea name="message" class="form-control" rows="4" placeholder="Type your reply..." required></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" name="send_message" class="btn btn-custom">
                                            <i class="bi bi-send"></i> Send Reply
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal لإرسال رسالة جديدة للـ Admin -->
    <div class="modal fade" id="sendToAdminModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #E7A769;">
                    <h5 class="modal-title" style="color: white;">
                        <i class="bi bi-envelope-plus"></i> Send Message to Admin
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <?php
                        // جلب admin_id (أول أدمن في النظام)
                        $adminQuery = $connect->query("SELECT id FROM users WHERE is_admin = 1 LIMIT 1");
                        $admin = $adminQuery->fetch(PDO::FETCH_ASSOC);
                        ?>
                        <input type="hidden" name="to_user_id" value="<?php echo $admin['id'] ?? 1; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label"><strong>Subject (Optional):</strong></label>
                            <input type="text" class="form-control" placeholder="e.g., Question about my adoption request">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><strong>Message:</strong></label>
                            <textarea name="message" class="form-control" rows="5" placeholder="Type your message to the admin..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="send_message" class="btn btn-custom">
                            <i class="bi bi-send"></i> Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php endif; ?>

<?php if ($isAdmin): ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php endif; ?>
