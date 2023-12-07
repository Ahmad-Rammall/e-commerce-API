<?php
include("../connection.php");
include("../decodeJWT.php");

$auth = $_SERVER['HTTP_AUTHORIZATION'];
$decoded = decodeJWTs($auth);

if ($decoded && property_exists($decoded,'sellerId')) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $stock = $_POST['stock'];

    // Insert Into Products Table
    $add_product_query = $mysqli->prepare("INSERT INTO products (Name, Price, Stock, Description) VALUES (?, ?, ?, ?)");
    $add_product_query->bind_param("siis", $name, $price, $stock, $description);
    $product_added = $add_product_query->execute();
    $productId = $mysqli->insert_id;

    // Insert Into Sellers_Products Table
    $add_product_query = $mysqli->prepare("INSERT INTO seller_products (SellerID, ProductID) VALUES (?, ?)");
    $add_product_query->bind_param("ii", $decoded->sellerId, $productId);
    $product_added2= $add_product_query->execute();

    if($product_added && $product_added2){
        echo json_encode('Product Added');
    }
    else{
        echo json_encode('Error Adding Product');
    }
} else {
    echo json_encode('Not Authorized');
}
