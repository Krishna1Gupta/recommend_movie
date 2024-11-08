
<?php
// Database connection details
$host = 'localhost';        // MySQL Workbench Host
$port = '3306';             // MySQL Workbench Port (default 3306)
$db = 'movie_recommender_system'; // Your MySQL Workbench Database name
$user = 'root';    // MySQL Workbench Username
$pass = '****';    // MySQL Workbench Password

// Create connection
$conn = new mysqli($host, $user, $pass, $db, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the posted data
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Input validation
    if (empty($username) || empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "All fields are required!"]);
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO musers (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    // Execute the query
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "User registered successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error registering user."]);
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>