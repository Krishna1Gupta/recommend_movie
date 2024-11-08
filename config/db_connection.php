<?php
// db_connection.php
$servername = "localhost";  // Change to your database server
$username = "root";         // Your database username
$password = "****";       // Your database password
$dbname = "movie_recommender_system";  // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}
?>
