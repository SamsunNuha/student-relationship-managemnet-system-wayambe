<?php
// config/database.php - SRMS Database Connection & Auto-Setup
// ============================================================
// CONFIGURATION - Change these to match your XAMPP setup
// ============================================================
$host     = 'localhost';
$dbname   = 'student_relationship_db';
$username = 'root';
$password = '';         // Default XAMPP MySQL has no password
// ============================================================

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`
                DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname`");

    // Create tables
    $pdo->exec("CREATE TABLE IF NOT EXISTS `admins` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(50) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `full_name` VARCHAR(100) NOT NULL DEFAULT 'System Administrator',
        `email` VARCHAR(100) NOT NULL UNIQUE,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `departments` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `department_name` VARCHAR(100) NOT NULL UNIQUE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `students` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `student_id` VARCHAR(30) NOT NULL UNIQUE,
        `full_name` VARCHAR(100) NOT NULL,
        `dob` DATE NOT NULL,
        `gender` ENUM('Male','Female','Other') NOT NULL,
        `email` VARCHAR(100) NOT NULL UNIQUE,
        `phone` VARCHAR(20) NOT NULL,
        `address` TEXT NOT NULL,
        `department` VARCHAR(100) NOT NULL,
        `academic_year` VARCHAR(20) NOT NULL,
        `enrollment_date` DATE NOT NULL,
        `password` VARCHAR(255) NOT NULL,
        `profile_image` VARCHAR(255) DEFAULT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `messages` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `student_id` INT NOT NULL,
        `subject` VARCHAR(255) NOT NULL,
        `message` TEXT NOT NULL,
        `reply` TEXT DEFAULT NULL,
        `status` ENUM('Pending','Replied') DEFAULT 'Pending',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `announcements` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `content` TEXT NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Seed default admin if table is empty (admin123)
    $adminCount = $pdo->query("SELECT COUNT(*) FROM `admins`")->fetchColumn();
    if ($adminCount == 0) {
        $adminHash = password_hash('admin123', PASSWORD_BCRYPT);
        $pdo->prepare("INSERT INTO `admins` (username, password, full_name, email) VALUES (?,?,?,?)")
            ->execute(['admin', $adminHash, 'System Administrator', 'admin@university.edu']);
    }

    // Seed departments if empty
    $deptCount = $pdo->query("SELECT COUNT(*) FROM `departments`")->fetchColumn();
    if ($deptCount == 0) {
        $pdo->exec("INSERT INTO `departments` (department_name) VALUES
            ('Computer Science'),('Mathematics'),('Physics'),
            ('Chemistry'),('Biology'),('Statistics')");
    }

    // Seed sample students if empty (password: student123)
    $stuCount = $pdo->query("SELECT COUNT(*) FROM `students`")->fetchColumn();
    if ($stuCount == 0) {
        $stuHash = password_hash('student123', PASSWORD_BCRYPT);
        $pdo->prepare("INSERT INTO `students`
            (student_id,full_name,dob,gender,email,phone,address,department,academic_year,enrollment_date,password)
            VALUES
            ('STU-2026-001','Alice Johnson','2003-05-14','Female','alice@student.edu','555-0101','Kolannava Road, Colombo','Computer Science','3rd Year','2024-09-01',?),
            ('STU-2026-002','Bob Smith','2002-11-22','Male','bob@student.edu','555-0102','456 College St, Science Town','Mathematics','4th Year','2023-09-01',?),
            ('STU-2026-003','Carol Davis','2004-02-10','Female','carol@student.edu','555-0103','789 Oak Lane, Academic Village','Physics','2nd Year','2025-09-01',?),
            ('STU-2026-004','David Wilson','2003-08-30','Male','david@student.edu','555-0104','321 Pine Road, Innovation Campus','Chemistry','3rd Year','2024-09-01',?),
            ('STU-2026-005','Emma Brown','2005-01-18','Female','emma@student.edu','555-0105','654 Maple Street, Metro Campus','Biology','1st Year','2026-09-01',?),
            ('STU-2026-006','Frank Miller','2003-04-12','Male','frank@student.edu','555-0106','987 Cedar Way, Research Park','Statistics','3rd Year','2024-09-01',?)"
        )->execute([$stuHash,$stuHash,$stuHash,$stuHash,$stuHash,$stuHash]);
    }

    // Seed announcements if empty
    $annCount = $pdo->query("SELECT COUNT(*) FROM `announcements`")->fetchColumn();
    if ($annCount == 0) {
        $pdo->exec("INSERT INTO `announcements` (title, content) VALUES
            ('Welcome to Fall Semester 2026','We warmly welcome all new and returning students! Orientation events will take place this Thursday at the main auditorium.'),
            ('Midterm Examination Schedule Released','The preliminary schedule for midterm examinations has been posted. Please review your departmental notice boards.'),
            ('Library Extended Hours for Finals','The main university library will be open 24/7 starting next Monday during final examination preparation period.')");
    }

    // Seed sample messages if empty
    $msgCount = $pdo->query("SELECT COUNT(*) FROM `messages`")->fetchColumn();
    if ($msgCount == 0) {
        $pdo->exec("INSERT INTO `messages` (student_id, subject, message, reply, status) VALUES
            (1,'Transcript Request','Hello Admin, I would like to request an official copy of my academic transcript for an internship application.','Dear Alice, your transcript request has been processed and is ready for pickup at the Registrar Office.','Replied'),
            (2,'Course Change Request','Hi, can I switch my minor elective from Statistics 101 to Physics 201 this semester?',NULL,'Pending'),
            (3,'Address Update Confirmation','Good day! I have updated my residential address in the system. Kindly confirm receipt.','Hello Carol, your updated address has been successfully recorded in our database.','Replied')");
    }

    // One-time fix: update Alice's address
    $pdo->prepare("UPDATE `students` SET address = ? WHERE email = ? AND address = ?")
        ->execute(['Kolannava Road, Colombo', 'alice@student.edu', '123 University Ave, Tech City']);

    // One-time fix: rehash passwords if they still have the wrong dummy hash
    $wrongHash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
    $adminCheck = $pdo->prepare("SELECT id FROM admins WHERE password = ? LIMIT 1");
    $adminCheck->execute([$wrongHash]);
    if ($adminCheck->fetch()) {
        $pdo->prepare("UPDATE admins SET password = ? WHERE password = ?")
            ->execute([password_hash('admin123', PASSWORD_BCRYPT), $wrongHash]);
    }
    $stuCheck = $pdo->prepare("SELECT id FROM students WHERE password = ? LIMIT 1");
    $stuCheck->execute([$wrongHash]);
    if ($stuCheck->fetch()) {
        $pdo->prepare("UPDATE students SET password = ? WHERE password = ?")
            ->execute([password_hash('student123', PASSWORD_BCRYPT), $wrongHash]);
    }

} catch (PDOException $e) {
    die("<div style='font-family:sans-serif;padding:2rem;background:#fff5f5;color:#c53030;border:1px solid #fed7d7;border-radius:8px;max-width:600px;margin:3rem auto;'>
        <h3>&#x26A0; Database Connection Error</h3>
        <p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
        <p>Please ensure XAMPP Apache &amp; MySQL are running, then refresh the page.</p>
    </div>");
}

/**
 * Sanitize output for safe HTML rendering
 */
function sanitize($data) {
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

/**
 * Universal password verification
 */
function verifyPassword($plainPassword, $storedHash) {
    return password_verify($plainPassword, $storedHash);
}
?>
