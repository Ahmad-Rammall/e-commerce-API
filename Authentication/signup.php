<?php
include("../connection.php");

$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

if (isset($_POST['type'])) {
    $type = $_POST['type'];
    if ($type === "2") {
        try {
            // Insert Into Users Table
            $add_user_query = $mysqli->prepare("INSERT INTO users (Full_Name, Email, Password, User_Type) VALUES (?, ?, ?, ?)");
            $add_user_query->bind_param("sssi", $name, $email, $password, $type);
            $user_added = $add_user_query->execute();
            $userId = $mysqli->insert_id;

            // Insert Into Sellers Table
            $add_seller_query = $mysqli->prepare("INSERT INTO sellers (UserID) VALUES (?)");
            $add_seller_query->bind_param("i", $userId);
            $seller_added = $add_seller_query->execute();

            if ($user_added && $seller_added) {
                echo json_encode('Seller Added Successfully');
            } else {
                echo json_encode('Error Adding Seller');
            }
        } catch (\Throwable $th) {
            throw $th;
        }
        exit();
    }
}

try {
    // Insert Into Users Table
    $add_user_query = $mysqli->prepare("INSERT INTO users (Full_Name, Email, Password, User_Type) VALUES (?, ?, ?, 1)");
    $add_user_query->bind_param("sss", $name, $email, $password);
    $user_added = $add_user_query->execute();

    if ($user_added) {
        echo json_encode('User Added Successfully');
    } else {
        echo json_encode('Error Adding User');
    }
} catch (\Throwable $th) {
    throw $th;
}
