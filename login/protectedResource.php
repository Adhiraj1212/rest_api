<?php
require '../inc/dbcon.php';
require '../vendor/autoload.php';
use \Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");

$secretKey = 'mydvlssuperuser123'; // TODO: replace with your secret key

if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    $arr = explode(" ", $authHeader);
    $jwt = $arr[1];

    if ($jwt) {
        try {
            $decoded = JWT::decode($jwt, $secretKey, array('HS512'));

            // If decode is successful, the user is authenticated
            // You can access user data stored in the JWT like this:
            $userId = $decoded->data->id;
            $username = $decoded->data->username;

            echo json_encode(array(
                "message" => "Access granted:",
                "data" => $decoded->data
            ));
            // TODO: Use $userId and $username to authorize access to resources

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
