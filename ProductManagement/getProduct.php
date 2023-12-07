<?php
include("../connection.php");
include("../decodeJWT.php");

$auth = $_SERVER['HTTP_AUTHORIZATION'];
if(decodeJWTs($auth));

$productId = $_POST['productId'];

$query = $mysqli->prepare("SELECT Name,Price,Description FROM products WHERE ProductID = ?");
$query->bind_param("i", $productId);
$query->execute();
$query->store_result();
$num_rows = $query->num_rows;
$query->bind_result($name, $price, $description);
$query->fetch();

$response = [];
if($num_rows > 0){
    $response["name"] = $name;
    $response['price'] = $price;
    $response['description'] = $description;
    echo json_encode($response);
}
else{
    echo json_encode('Product Not Found !');
}


