<?php
include("../connection.php");
include("../decodeJWT.php");

$auth = $_SERVER['HTTP_AUTHORIZATION'];
$decoded = decodeJWTs($auth);

if ($decoded && property_exists($decoded, 'sellerId')) {
    $productId = $_POST['productId'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // Test If The Seller Owns The Item
    $query = $mysqli->prepare("SELECT SellerID FROM seller_products WHERE ProductID = ?");
    $query->bind_param("i", $productId);
    $query->execute();
    $query->store_result();
    $query->bind_result($sellerId);
    $query->fetch();

    if($sellerId != $decoded->sellerId){
        echo json_encode('Not Authorized');
        exit();
    }

    // Update From Products Table
    $update_query = $mysqli->prepare("UPDATE products SET Name = ?, Price = ?, Stock = ?, Description = ? WHERE ProductID = ?");
    $update_query->bind_param("siisi", $name, $price, $stock, $description, $productId);
    $update_done = $update_query->execute();

    if ($update_done) {
        echo json_encode('Product Updated');
    } else {
        echo json_encode('Error Updating Product');
    }
} else {
    echo json_encode('Not Authorized');
}
