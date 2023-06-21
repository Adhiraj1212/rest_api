<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");

include('../products/function.php');

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $data = json_decode(file_get_contents("php://input"));
    
    if(isset($data->UserName) && isset($data->Password)){
        $loginResponse = loginUser($data->UserName, $data->Password);
        echo $loginResponse;
    }
}
else{
    echo json_encode(["message" => "Method not allowed"]);
}
?>