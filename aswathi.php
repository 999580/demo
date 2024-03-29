<?php
// Database connection 
$servername = "your servername";
$username = "your username";
$password = "your password";
$dbname = "your dbname";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//User table
$userTableSql = "CREATE TABLE IF NOT EXISTS user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(200) NOT NULL,
    password VARCHAR(200) NOT NULL
)";

$conn->query($userTableSql);

// Uploads table
$uploadsTableSql = "CREATE TABLE IF NOT EXISTS uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    file_name VARCHAR(200) NOT NULL,
    file_path VARCHAR(200) NOT NULL,
    upload_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
)";

$conn->query($uploadsTableSql);

//Logs table
$logsTableSql = "CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    ip_address VARCHAR(45) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    upload_status ENUM('success', 'rejected') NOT NULL,
    upload_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    log_message TEXT,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
)";

$conn->query($logsTableSql);
function validateFileType($fileExtension) {
    $allowedExtensions = ["jpg", "png", "pdf", "docx"];
    return in_array($fileExtension, $allowedExtensions);
}
function sanitizeAndGenerateFileName($originalFileName) {
    $sanitizedFileName = pathinfo($originalFileName, PATHINFO_FILENAME);
    $uniqueFileName = $sanitizedFileName . '_' . uniqid() . '.' . pathinfo($originalFileName, PATHINFO_EXTENSION);
    return $uniqueFileName;
}

function handleFileUpload($conn, $userId) {
    
    logToFile($conn, $userId, $_SERVER['REMOTE_ADDR'], $fileName, $uploadStatus, $logMessage);
}


function logToFile($conn, $userId, $ipAddress, $fileName, $uploadStatus, $logMessage) {
    $logInsertSql = "INSERT INTO logs (user_id, ip_address, file_name, upload_status, log_message) 
                     VALUES ('$userId', '$ipAddress', '$fileName', '$uploadStatus', '$logMessage')";
    $conn->query($logInsertSql);
}
$conn->close();
?>