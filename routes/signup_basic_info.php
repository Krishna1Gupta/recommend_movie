<?php
// Include the database connection
include 'D:/xampp/htdocs/recommend_movie/config/db_connection.php';

// Set response headers to return JSON
header('Content-Type: application/json');

// Enable detailed error logging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log incoming raw POST data
$rawPostData = file_get_contents("php://input");
file_put_contents('D:/xampp/htdocs/recommend_movie/logs/debug_log.txt', "Raw POST Data: " . $rawPostData . PHP_EOL, FILE_APPEND);

// Check database connection
if (!$conn) {
    file_put_contents('D:/xampp/htdocs/recommend_movie/logs/debug_log.txt', "Database Connection Failed: " . mysqli_connect_error() . PHP_EOL, FILE_APPEND);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit();
}

// Get the raw POST data and decode it into an associative array
$data = json_decode($rawPostData, true);
file_put_contents('D:/xampp/htdocs/recommend_movie/logs/debug_log.txt', "Data After json_decode: " . $data . PHP_EOL, FILE_APPEND);

// Handle invalid JSON input
if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    file_put_contents('D:/xampp/htdocs/recommend_movie/logs/debug_log.txt', "JSON Error: " . json_last_error_msg() . PHP_EOL, FILE_APPEND);
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON input: ' . json_last_error_msg()]);
    exit();
}

// Validation: Check if all required fields are provided
if (!isset($data['name']) || !isset($data['email']) || !isset($data['password']) || !isset($data['adult']) || !isset($data['gender'])) {
    file_put_contents('D:/xampp/htdocs/recommend_movie/logs/debug_log.txt', "Missing Fields: " . json_encode($data) . PHP_EOL, FILE_APPEND);
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit();
}

// Proceed with further validation and insertion
// ...

// Validate name (letters and spaces only)
if (!preg_match("/^[a-zA-Z ]*$/", $data['name'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid name format']);
    exit();
}

// Validate email
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
    exit();
}

// Validate password (at least 6 characters, with at least one number and one letter)
if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/", $data['password'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Password must be at least 6 characters long, contain letters and numbers']);
    exit();
}

// Sanitize input data
$name = mysqli_real_escape_string($conn, $data['name']);
$email = mysqli_real_escape_string($conn, $data['email']);
$password = password_hash($data['password'], PASSWORD_DEFAULT); // Hash the password securely
$adult = mysqli_real_escape_string($conn, $data['adult']);
$gender = mysqli_real_escape_string($conn, $data['gender']);

// Check if email already exists in the database using prepared statements
$stmt_check = $conn->prepare("SELECT user_id FROM musers WHERE email = ?");
$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    http_response_code(409); // 409 Conflict
    echo json_encode(['status' => 'error', 'message' => 'Email already exists']);
    exit();
}

// Insert basic information into the database using prepared statements
$stmt_insert = $conn->prepare("INSERT INTO musers (name, email, password_hash,adult,gender) VALUES (?, ?, ?,?,?)");
if ($stmt_insert === false) {
    // Log or output the error for debugging
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'SQL Error: ' . $conn->error]);
    exit();
}
$stmt_insert->bind_param("sssds", $name, $email, $password,$adult,$gender);

if ($stmt_insert->execute()) {
    // Get the last inserted user_id
    $user_id = $stmt_insert->insert_id;
    http_response_code(201); // 201 Created
    echo json_encode(['status' => 'success', 'message' => 'Basic information stored', 'user_id' => $user_id]);
} else {
    http_response_code(500); // 500 Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'Database insertion failed: ' . $conn->error]);
}

// Close the statement and database connection
$stmt_insert->close();
$conn->close();
?>
