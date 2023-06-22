<?php
require '../inc/dbcon.php';
require '../vendor/autoload.php';
use \Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");

$secretKey = 'mydvlssuperuser123'; // TODO: replace with your secret key

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        $arr = explode(" ", $authHeader);
        $jwt = $arr[1];

        if ($jwt) {
            try {
                /** @var stdClass $decoded */
                $decoded = JWT::decode($jwt, $secretKey, array('HS512'));

                // If decode is successful, the user is authenticated
                // You can access user data stored in the JWT like this:
                $vendorId = $decoded->data->vendorID;

                // Get JSON data from POST request
                $data = json_decode(file_get_contents("php://input"));

                if (!empty($data->ProductID) && !empty($data->ProductName) && !empty($data->Description) && !empty($data->SKU) && !empty($data->Price)) {

                    $productID = $data->ProductID;
                    $productName = $data->ProductName;
                    $description = $data->Description;
                    $price = $data->Price;
                    $sku = $data->SKU;

                    // insert into database
                    $sql = "INSERT INTO products (VendorID, ProductID, ProductName, Description,Price, sku, CreatedAt) VALUES ('$vendorId', '$productID', '$productName', '$description','$price','$sku', NOW())";

                    if ($conn->query($sql) === TRUE) {
                        echo json_encode(["message" => "New record created successfully"]);
                    } else {
                        echo json_encode(["message" => "Error: " . $conn->error]);
                    }

                } else {
                    http_response_code(400);
                    echo json_encode(["message" => "Missing required data"]);
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
}
