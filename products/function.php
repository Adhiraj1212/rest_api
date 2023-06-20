<?php
require '../inc/dbcon.php';
require '../vendor/autoload.php';
use \Firebase\JWT\JWT;

function getProductList($VendorID = null){

    global $conn;

    $query="SELECT * FROM products";
    if ($VendorID !== null) {
        $vendor_id = mysqli_real_escape_string($conn, $VendorID); // Prevent SQL injection
        $query .= " WHERE VendorID = '$vendor_id'";
    }
    $query_run= mysqli_query($conn, $query);

    if($query_run){

        if(mysqli_num_rows($query_run)>0){

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data=[
                'status' => 200,
                'message' => 'Product List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 Product List Fetched Successfully");
            echo json_encode($data);
        }
        else
        {
            $data=[
                'status' => 404,
                'message' => 'Product Not Found',
            ];
            header("HTTP/1.0 404 Product Not Found");
            echo json_encode($data);
        }


    }
    else{

        $data=[
            'status' => 500,
            'message' => 'Internal Sever Error',
        ];
        header("HTTP/1.0 500 Internal Sever Error");
        echo json_encode($data);
    }

}

function getUserList(){

    global $conn;

    $query="SELECT * FROM users";
   
    $query_run= mysqli_query($conn, $query);

    if($query_run){

        if(mysqli_num_rows($query_run)>0){

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data=[
                'status' => 200,
                'message' => 'User List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 User List Fetched Successfully");
            echo json_encode($data);
        }
        else
        {
            $data=[
                'status' => 404,
                'message' => 'Users Not Found',
            ];
            header("HTTP/1.0 404 Users Not Found");
            echo json_encode($data);
        }


    }
    else{

        $data=[
            'status' => 500,
            'message' => 'Internal Sever Error',
        ];
        header("HTTP/1.0 500 Internal Sever Error");
        echo json_encode($data);
    }

}

function getPremiumUsers($subscription = null){

    global $conn;

    $query="SELECT * FROM users";
    if ($subscription !== null) {
        $user_status = mysqli_real_escape_string($conn, $subscription); // Prevent SQL injection
        $query .= " WHERE Subscription = '$user_status'";
    }
    $query_run= mysqli_query($conn, $query);

    if($query_run){

        if(mysqli_num_rows($query_run)>0){

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data=[
                'status' => 200,
                'message' => 'Subscribed Users Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 Subscribed Users Fetched Successfully");
            echo json_encode($data);
        }
        else
        {
            $data=[
                'status' => 404,
                'message' => 'Subscribed Users Not Found',
            ];
            header("HTTP/1.0 404 Subscribed Users Not Found");
            echo json_encode($data);
        }


    }
    else{

        $data=[
            'status' => 500,
            'message' => 'Internal Sever Error',
        ];
        header("HTTP/1.0 500 Internal Sever Error");
        echo json_encode($data);
    }

}

function loginUser($username, $password){
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM users WHERE UserName=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_object();

    if($user){
        if(password_verify($password, $user->Password)){
            $secretKey = 'mydvlssuperuser123'; // TODO: Replace with your secret key
            $issueDate = time();
            $expirationTime = $issueDate + 60*60; // valid for 60 minutes

            $payload = array(
                "iss" => "http://localhost", // Issuer, this can be your domain name
                "iat" => $issueDate, // Time when JWT was issued
                "exp" => $expirationTime, // Expiration time
                "data" => array(
                    "id" => $user->UserID,
                    "username" => $user->UserName
                )
            );

            $jwt = JWT::encode($payload, $secretKey, 'HS512');
            
            return json_encode([
                "status" => 200,
                "message" => "Login Successful",
                "access_token" => $jwt
            ]);
        }
    }

    return json_encode([
        "status" => 401,
        "message" => "Login failed. Invalid credentials"
    ]);
}


?>
