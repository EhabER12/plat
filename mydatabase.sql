create database yousef_db2;
use yousef_db2;


-- ----------------------------
-- الجداول الأساسية
-- ----------------------------

-- 1. جدول المستخدمين
CREATE TABLE IF NOT EXISTS Users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. جدول أدوار المستخدمين (دعم أدوار متعددة)
CREATE TABLE IF NOT EXISTS User_Roles (
    user_id INT,
    role ENUM('admin', 'instructor', 'student', 'parent') NOT NULL,
    PRIMARY KEY (user_id, role),
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- 3. جدول التصنيفات مع التصنيفات الفرعية
CREATE TABLE IF NOT EXISTS Categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    parent_category_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_category_id) REFERENCES Categories(category_id) ON DELETE SET NULL
);

-- 4. جدول الدورات (مع آلية الموافقة)
CREATE TABLE IF NOT EXISTS Courses (
    course_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    instructor_id INT,
    category_id INT,
    approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (instructor_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES Categories(category_id) ON DELETE SET NULL
);

-- 5. جدول الفيديوهات
CREATE TABLE IF NOT EXISTS Course_Videos (
    video_id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    video_url VARCHAR(500) NOT NULL,
    duration INT NOT NULL,
    position INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES Courses(course_id) ON DELETE CASCADE
);

-- 6. جدول الاشتراكات (مع منع التكرار)
CREATE TABLE IF NOT EXISTS Enrollments (
    enrollment_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    course_id INT,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    FOREIGN KEY (student_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES Courses(course_id) ON DELETE CASCADE,
    UNIQUE (student_id, course_id)
);

-- 7. جدول المدفوعات والمحفظة
CREATE TABLE IF NOT EXISTS Payments (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    course_id INT,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('credit_card', 'paypal', 'bank_transfer', 'wallet') NOT NULL,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    FOREIGN KEY (student_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES Courses(course_id) ON DELETE CASCADE
);

-- 8. جدول الكوبونات
CREATE TABLE IF NOT EXISTS Coupons (
    coupon_id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE NOT NULL,
    discount_percentage DECIMAL(5,2) CHECK (discount_percentage BETWEEN 0 AND 100),
    expiration_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 9. جدول تطبيق الكوبونات
CREATE TABLE IF NOT EXISTS Payment_Coupons (
    payment_coupon_id INT PRIMARY KEY AUTO_INCREMENT,
    payment_id INT,
    coupon_id INT,
    discount_percentage DECIMAL(5,2) NOT NULL,
    FOREIGN KEY (payment_id) REFERENCES Payments(payment_id) ON DELETE CASCADE,
    FOREIGN KEY (coupon_id) REFERENCES Coupons(coupon_id) ON DELETE CASCADE
);

-- 10. جدول المحادثات
CREATE TABLE IF NOT EXISTS Chats (
    chat_id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT,
    is_private BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES Courses(course_id) ON DELETE CASCADE
);

-- 11. جدول المشاركين في المحادثات
CREATE TABLE IF NOT EXISTS Chat_Participants (
    participant_id INT PRIMARY KEY AUTO_INCREMENT,
    chat_id INT,
    user_id INT,
    FOREIGN KEY (chat_id) REFERENCES Chats(chat_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- 12. جدول الرسائل
CREATE TABLE IF NOT EXISTS Messages (
    message_id INT PRIMARY KEY AUTO_INCREMENT,
    chat_id INT,
    sender_id INT,
    message TEXT NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (chat_id) REFERENCES Chats(chat_id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- 13. جدول الشكاوى والدعم الفني
CREATE TABLE IF NOT EXISTS Support_Tickets (
    ticket_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('open', 'in_progress', 'resolved', 'closed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- 14. جدول سجلات المراجعة
CREATE TABLE IF NOT EXISTS Audit_Logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(50) NOT NULL,
    target_table VARCHAR(50),
    target_id INT,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

-- 15. جدول ربط ولي الأمر بالطالب
CREATE TABLE IF NOT EXISTS Parent_Student (
    parent_id INT,
    student_id INT,
    PRIMARY KEY (parent_id, student_id),
    FOREIGN KEY (parent_id) REFERENCES Users(user_id),
    FOREIGN KEY (student_id) REFERENCES Users(user_id)
);

-- 16. جدول تقدم الطالب
CREATE TABLE IF NOT EXISTS Student_Progress (
    progress_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    course_id INT,
    completion_percentage DECIMAL(5,2),
    last_accessed TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES Users(user_id),
    FOREIGN KEY (course_id) REFERENCES Courses(course_id)
);

-- 17. جدول سحب الأرباح للمدرسين
CREATE TABLE IF NOT EXISTS Withdrawals (
    withdrawal_id INT PRIMARY KEY AUTO_INCREMENT,
    instructor_id INT,
    amount DECIMAL(10,2),
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (instructor_id) REFERENCES Users(user_id)
);

-- 18. جدول الامتحانات والأسئلة
CREATE TABLE IF NOT EXISTS Exams (
    exam_id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT,
    title VARCHAR(255) NOT NULL,
    duration INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES Courses(course_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Questions (
    question_id INT PRIMARY KEY AUTO_INCREMENT,
    exam_id INT,
    question_text TEXT NOT NULL,
    correct_answer VARCHAR(255) NOT NULL,
    FOREIGN KEY (exam_id) REFERENCES Exams(exam_id) ON DELETE CASCADE
);

-- 19. جدول تقييمات الكورسات والمدرسين
CREATE TABLE IF NOT EXISTS Ratings (
    rating_id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT,
    instructor_id INT,
    student_id INT,
    rating_value DECIMAL(3,2) CHECK (rating_value BETWEEN 0 AND 5),
    comment TEXT,
    FOREIGN KEY (course_id) REFERENCES Courses(course_id),
    FOREIGN KEY (instructor_id) REFERENCES Users(user_id),
    FOREIGN KEY (student_id) REFERENCES Users(user_id)
);

-- 20. جدول الإشعارات
CREATE TABLE IF NOT EXISTS Notifications (
    notification_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

-- 21. جدول المواد التعليمية (كتب، مذكرات)
CREATE TABLE IF NOT EXISTS Course_Materials (
    material_id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT,
    title VARCHAR(255) NOT NULL,
    file_url VARCHAR(500) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES Courses(course_id)
);

-- 22. جدول الحركات المالية (شجرة الحسابات)
CREATE TABLE IF NOT EXISTS Financial_Ledger (
    ledger_id INT PRIMARY KEY AUTO_INCREMENT,
    transaction_type ENUM('revenue', 'expense', 'profit') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    description TEXT,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ----------------------------
-- الإجراءات المخزنة والفهارس
-- ----------------------------

-- إجراءات إنشاء المحادثات
DELIMITER //
CREATE PROCEDURE CreateGroupChat (
    IN p_course_id INT
)
BEGIN
    INSERT INTO Chats (course_id, is_private, created_at)
    VALUES (p_course_id, FALSE, NOW());
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE CreatePrivateChat (
    IN p_user1_id INT,
    IN p_user2_id INT
)
BEGIN
    DECLARE user1_exists INT;
    DECLARE user2_exists INT;
    
    SELECT COUNT(*) INTO user1_exists FROM Users WHERE user_id = p_user1_id;
    SELECT COUNT(*) INTO user2_exists FROM Users WHERE user_id = p_user2_id;
    
    IF user1_exists = 1 AND user2_exists = 1 THEN
        INSERT INTO Chats (is_private, created_at) VALUES (TRUE, NOW());
        SET @new_chat_id = LAST_INSERT_ID();
        INSERT INTO Chat_Participants (chat_id, user_id) 
        VALUES (@new_chat_id, p_user1_id), (@new_chat_id, p_user2_id);
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'One or both users do not exist';
    END IF;
END //
DELIMITER ;

-- إجراء إرسال الرسائل
DELIMITER //
CREATE PROCEDURE SendMessage (
    IN p_chat_id INT,
    IN p_sender_id INT,
    IN p_message TEXT
)
BEGIN
    INSERT INTO Messages (chat_id, sender_id, message, sent_at)
    VALUES (p_chat_id, p_sender_id, p_message, NOW());
END //
DELIMITER ;

-- فهارس لتحسين الأداء
CREATE INDEX idx_email ON Users(email);
CREATE INDEX idx_course_id ON Enrollments(course_id);
CREATE INDEX idx_instructor_id ON Courses(instructor_id);