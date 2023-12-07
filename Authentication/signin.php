<?php
include("../connection.php");

use Firebase\JWT\JWT;

$key = 'my_secret_key';

$email = $_POST["email"];
$password = $_POST["password"];

$query = $mysqli->prepare('select UserID,User_Type,Password from users where Email=?');
$query->bind_param('s', $email);
$query->execute();
$query->store_result();
$num_rows = $query->num_rows;
$query->bind_result($userId, $type, $stored_password);
$query->fetch();

$expTime = time() + 3600;

if($num_rows > 0 && password_verify($password, $stored_password)) {
    $payload = [
        'userId'=> $userId,
        'type'=> $type,
        'exp' => $expTime
    ];
    $token = JWT::encode($payload, $key, 'HS256');
    echo json_encode($token);
} else{
    echo json_encode("No User Found !");
}
