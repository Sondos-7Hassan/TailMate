<?php require_once __DIR__ . '/../config/config.php';
session_start();
require_once __DIR__ . '/../config/connection.php'; 

if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: " . BASE_URL . "/auth/login.php");
    exit();
}

// معالجة حذف رسالة
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $messageId = (int) $_GET['id'];
    $delete = $connect->prepare("DELETE FROM contact_messages WHERE id = ?");
    $delete->execute([$messageId]);
    header("Location: " . BASE_URL . "/admin/message.php");
    exit();
}

// معالجة الرد على رسالة
if (isset($_POST['reply']) && isset($_POST['message_id'])) {
    $messageId = (int) $_POST['message_id'];
    $replyText = $_POST['reply_text'];
    
    // هنا يمكن إرسال البريد الإلكتروني للمستخدم
    // يمكن إضافة كود إرسال البريد هنا
    
    header("Location: " . BASE_URL . "/admin/message.php?success=replied");
    exit();
}

$messages = $connect->query("
    SELECT id AS message_id, name, email, phone, message
    FROM contact_messages
    ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
include __DIR__ . '/../includes/head.php';
?>
<body>

  <div class="wrapper">
    <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

    <div class="content">
      <?php include __DIR__ . '/../includes/admin_navbar.php'; ?>

      <div>
        <h2 class="mb-4">Messages</h2>

        <?php if (isset($_GET['success']) && $_GET['success'] === 'replied') { ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            Reply sent successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php } ?>

        <table class="table table-bordered align-middle">
          <thead style="background-color: #E7A769; color: #5C3A21;">
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Message</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($messages)) { ?>
              <?php foreach ($messages as $msg){ ?>
              <tr>
                <td><?php echo $msg['message_id'] ?></td>
                <td><?php echo $msg['name'] ?></td>
                <td><?php echo $msg['email'] ?></td>
                <td><?php echo $msg['phone'] ?></td>
                <td><?php echo substr($msg['message'], 0, 50) . '...' ?></td>
                <td>
                  <span class="badge bg-warning text-dark">Pending</span>
                </td>
                <td>
                  <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $msg['message_id'] ?>">View</button>
                  <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#replyModal<?php echo $msg['message_id'] ?>">Reply</button>
                  <a href="<?= BASE_URL ?>/admin/message.php?action=delete&id=<?php echo $msg['message_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this message?')">Delete</a>
                </td>
              </tr>

              <!-- View Modal -->
              <div class="modal fade" id="viewModal<?php echo $msg['message_id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Message from <?php echo $msg['name'] ?></h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <p><strong>Email:</strong> <?php echo $msg['email'] ?></p>
                      <p><strong>Phone:</strong> <?php echo $msg['phone'] ?></p>
                      <p><strong>Message:</strong></p>
                      <p><?php echo nl2br($msg['message']) ?></p>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Reply Modal -->
              <div class="modal fade" id="replyModal<?php echo $msg['message_id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Reply to <?php echo $msg['name'] ?></h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                      <div class="modal-body">
                        <input type="hidden" name="message_id" value="<?php echo $msg['message_id'] ?>">
                        <div class="mb-3">
                          <label class="form-label">To: <?php echo $msg['email'] ?></label>
                        </div>
                        <div class="mb-3">
                          <label class="form-label">Your Reply:</label>
                          <textarea name="reply_text" class="form-control" rows="5" required></textarea>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="reply" class="btn btn-primary">Send Reply</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>

              <?php } ?>
            <?php } else { ?>
              <tr>
                <td colspan="7" class="text-center">No messages found</td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
