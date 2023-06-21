<?php

// header("Access-Control-Allow-Origin: *");
// header("Content-Type: application/json");
// header("Access-Control-Allow-Method: GET");
// header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Method, Authorization, X-Requested-With");

// include('function.php');

// $requestMethod = $_SERVER["REQUEST_METHOD"];

// if($requestMethod == "GET"){
//     $vendor_id = isset($_GET['vendor_id']) ? $_GET['vendor_id'] : null;
//     $productList = getProductList($vendor_id);
//     echo $productList;
// }
// else
// {
//     $data=[
//         'status' => 405,
//         'message' => $requestMethod . ' Method Not Allowed',
//     ];
//     header("HTTP/1.0 405 Method Not Allowed");
//     echo json_encode($data);
// }

require '../inc/dbcon.php';
require '../vendor/autoload.php';
use \Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");

$secretKey = 'mydvlssuperuser123';

if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    $arr = explode(" ", $authHeader);
    $jwt = $arr[1];

    if ($jwt) {
        try {
            $decoded = JWT::decode($jwt, $secretKey, array('HS512'));
            $userId = $decoded->data->id;
            $username = $decoded->data->username;
            $VendorID = $decoded->data->vendorID;

            // At this point, the user is authenticated, and you can provide the requested data.

            // For example, let's fetch and return all products:

            $query = "SELECT * FROM products";
                $vendor_id = mysqli_real_escape_string($conn, $VendorID); // Prevent SQL injection
                $query .= " WHERE VendorID = '$vendor_id'";

            $result= mysqli_query($conn, $query);

            if ($result->num_rows > 0) {
                $products = [];
                while($row = $result->fetch_assoc()) {
                    $products[] = $row;
                }
                echo json_encode($products);
            } else {
                echo json_encode([]);
            }

        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode([
                "status" => 401,
                "message" => "Access denied. Error: " . $e->getMessage()
            ]);
            exit(0);
        }
    }
} else {
    http_response_code(401);
    echo json_encode([
        "status" => 401,
        "message" => "Access denied. No token provided."
    ]);
    exit(0);
}


?>
