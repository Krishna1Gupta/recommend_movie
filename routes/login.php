<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow_Origin: *");

// Content-Type: application/json Use Case: When you're building an API or web service that returns data in JSON, it's important to specify the content type so that the client knows how to parse the data.
// header("Access-Control-Allow-Methods: POST"): This header tells the client which HTTP methods are allowed when making requests to the server. In this case, it specifies that the POST method is allowed.
//header("Access-Control-Allow-Origin: *"); This header is part of CORS (Cross-Origin Resource Sharing), which controls which origins (domains) are allowed to make requests to the server.Wildcard (*) means that any domain can make requests to the server (this is considered as opening up the server to any cross-domain requests).

// Include the database connection
include_once 'config/db_connection.php';
include_once 'config/functions.php';

function login(){
    global $conn;
    $input = json_decode(file_get_contents("php://input"),true);

    if(!isset($input['email']) || !isset($input['password'])){
        echo json_encode(array("status"=>"error","message"=> "Email and password are required"));
        http_response_code(400);
        return;
    }

    $input_email = trim($input['email']);
    $input_password = trim($input['password']);

    //Validate email format
    if(!filter_var($input_email,FILTER_VALIDATE_EMAIL)){
        echo json_encode(array("status"=>"error","message"=>"Invalid email format"));
        http_response_code(400);
        exit();
    }
    
    try{

        $query = "Select user_id,password_hash from musers where email = ?";
        $stmt = $conn->prepare($query);
        $stmt-> bind_param('s',$input_email);
        $stmt->execute();
        // Fetch user data
        $result = $stmt->get_result();

        if ($result->num_rows === 0){
            // redirect('get_started.html');
            echo json_encode(array("status" => "error", "redirect" => "get_started.html"));
            exit();
        }

        $user = $result->fetch_assoc();
        $stored_password = $user['password_hash'];

        if(password_verify($input_password,$stored_password)){
            echo json_encode(array("status"=>"success","message"=>"Login Successfully"));
            http_response_code(200);
        }
        else{
            echo json_encode(array("status"=>"error","message"=>"Invalid Credentials"));
            http_response_code(401);
            
        }
        
    }
        catch(mysqli_sql_exception  $e){
            echo json_encode(array("status"=>"error","message" => "Internal server error.". $e->getMessage()));
        http_response_code(500);
        }
        
    // Close the prepared statement
    $stmt->close();

    // Close the database connection
    $conn->close();

}


// Handling forgot password redirection
function handleForgotPassword() {
    redirect('/forgot_password.php');
}

// Check if request is for login or forgot password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);

    if (isset($input['action']) && $input['action'] == 'forgot_password') {
        handleForgotPassword();
    } else {
        login();
    }
} else {
    echo json_encode(array("message" => "Invalid request method."));
    http_response_code(405); // Method not allowed
}
?>

