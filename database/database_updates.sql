-- جدول الرسائل بين المستخدمين والأدمن
CREATE TABLE IF NOT EXISTS user_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    from_user_id INT NOT NULL,
    to_user_id INT NOT NULL,
    message TEXT NOT NULL,
    request_id INT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (from_user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (to_user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (request_id) REFERENCES adoption_requests(id) ON DELETE SET NULL
);

-- إضافة عمود created_at لجدول adoption_requests إذا لم يكن موجود
ALTER TABLE adoption_requests 
ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- إضافة عمود adopted_at لجدول adoption_requests
ALTER TABLE adoption_requests 
ADD COLUMN IF NOT EXISTS adopted_at TIMESTAMP NULL;

-- التأكد من وجود عمود status في جدول animals
ALTER TABLE animals 
ADD COLUMN IF NOT EXISTS status ENUM('Available', 'Reserved', 'Adopted') DEFAULT 'Available';

-- إضافة أعمدة التبني لجدول animals
ALTER TABLE animals 
ADD COLUMN IF NOT EXISTS adopted_by INT NULL,
ADD COLUMN IF NOT EXISTS adopted_at TIMESTAMP NULL,
ADD FOREIGN KEY (adopted_by) REFERENCES users(id) ON DELETE SET NULL;
