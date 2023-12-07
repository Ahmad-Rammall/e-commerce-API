<?php
include("../connection.php");
include("../decodeJWT.php");

$auth = $_SERVER['HTTP_AUTHORIZATION'];
$decoded = decodeJWTs($auth);

if ($decoded && property_exists($decoded, 'sellerId')) {
    $productId = $_POST['productId'];

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

    // Delete From Products Table
    $delete_query = $mysqli->prepare("DELETE FROM products WHERE ProductId = ?");
    $delete_query->bind_param("i", $productId);
    $delete_done1 = $delete_query->execute();

    // Delete From Sellers_Products Table
    $delete_query = $mysqli->prepare("DELETE FROM seller_products WHERE (ProductId = ? AND SellerID = ?)");
    $delete_query->bind_param("ii", $productId, $sellerId);
    $delete_done2 = $delete_query->execute();

    if ($delete_done1 && $delete_done2) {
        echo json_encode('Product Deleted');
    } else {
        echo json_encode('Error Deleting Product');
    }
} else {
    echo json_encode('Not Authorized');
}
