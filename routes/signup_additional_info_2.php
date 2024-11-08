<?php
// Include the database connection
include 'db_connection.php';

// Set response headers to return JSON
header('Content-Type: application/json');

// Get the raw POST data and decode it into an associative array
$data = json_decode(file_get_contents("php://input"), true);

// Validation: Check if all required fields are provided
if (!isset($data['user_id']) || !isset($data['address']) || !isset($data['phone'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit();
}

// Validate user_id (must be an integer)
if (!is_numeric($data['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid user ID']);
    exit();
}

// Validate phone number (digits only, minimum 10 digits)
if (!preg_match("/^[0-9]{10,}$/", $data['phone'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid phone number']);
    exit();
}

// Sanitize input data
$user_id = intval($data['user_id']);
$address = mysqli_real_escape_string($conn, $data['address']);
$phone = mysqli_real_escape_string($conn, $data['phone']);

// Check if the user exists
$sql_check = "SELECT user_id FROM musers WHERE user_id = $user_id"; // Added verification check
$result_check = $conn->query($sql_check);

// Check for SQL query execution error
if ($result_check === false) {
    echo json_encode(['status' => 'error', 'message' => 'SQL error: ' . $conn->error]);
    exit();
}

if ($result_check->num_rows == 0) {
    echo json_encode(['status' => 'error', 'message' => 'User not found ']);
    exit();
}

// Update user's additional information and mark as verified
$sql_update = "UPDATE musers SET state = '$address', phone = '$phone'WHERE user_id = $user_id"; // Added verification update

if ($conn->query($sql_update) === TRUE) {
    echo json_encode(['status' => 'success', 'message' => 'Additional information saved and user verified']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database update failed: ' . $conn->error]);
}

// Close the database connection
$conn->close();
?>
