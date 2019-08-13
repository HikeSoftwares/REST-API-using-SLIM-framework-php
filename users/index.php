<?php
require '../../vendor/autoload.php';

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\UploadedFile;

require '../dbconn.php';
require 'pdo.php';
$sql = new users();

$app = new Slim\App();

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization, AUTH_TOKEN, API_KEY, enctype')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

$app->get('/get/userslist/{from}/{no_of_records}/', function($request, $response, $args){  
    $token = $request->getHeaderLine('Authorization');
    global $sql; 
    $array['status'] = '200';
    $array['message'] = 'Request Successfull !';
    $array['response']= $sql->getUsersList($token, $args['from'], $args['no_of_records']);
    return $response->withStatus(200)
    ->withHeader("Content-Type","application/json")
    ->write(json_encode($array));
});

$app->post('/post/createuser/', function($request, $response, $args) {   
    $token = $request->getHeaderLine('Authorization');
    $post = $request->getParsedBody();  
    
    if(!isset($_POST['name'])){ $post['name'] = ''; }
    if(!isset($_POST['email'])){ $post['email'] = ''; }
    if(!isset($_POST['password'])){ $post['password'] = ''; }
    if(!isset($_POST['phone'])){ $post['phone'] = ''; }
    if(!isset($_POST['user_type'])){ $post['user_type'] = ''; }
    if(!isset($_POST['user_role'])){ $post['user_role'] = ''; }
    if(!isset($_POST['organization'])){ $post['organization'] = ''; }
    if(!isset($_POST['location'])){ $post['location'] = ''; }

    $imgname = "";
    if(isset($_FILES['image']) ){
        if($_FILES['image']['error'] === 0){
            $files = $_FILES['image'];
            $imgname = date("Ymdhis")."_".$files['name'];
            move_uploaded_file($files['tmp_name'], "../../uploads/users/".$imgname);
        }
    }

    global $sql;
    $array['status'] = '200';
    $array['message'] = 'Request Successfull !';        
    $array['response']= $sql->createUser($token, $post['name'], $post['email'], $post['password'], $post['phone'], $imgname, $post['user_type'], $post['user_role'], $post['organization'], $post['location']);
    return $response->withStatus(200)
    ->withHeader("Content-Type","application/json")
    ->write(json_encode($array));
});




$app->post('/post/resend_email_otp/', function($request, $response, $args) {   
    $API_KEY = $request->getHeaderLine('Authorization');
    $post = $request->getParsedBody(); 
    
    if(!isset($_POST['users_id'])){ $post['users_id'] = ''; }

    global $sql;
    $array['status'] = '200';
    $array['message'] = 'Request Successfull !';        
    $array['response']= $sql->resendEmailOTP($API_KEY, $post['users_id']);
    return $response->withStatus(200)
    ->withHeader("Content-Type","application/json")
    ->write(json_encode($array));
});


$app->get('/get/userdetails/{users_id}/', function($request, $response, $args) {   
    $token = $request->getHeaderLine('Authorization');
    global $sql;     
    $array['status'] = '200';
    $array['message'] = 'Request Successfull !';        
    $array['response']= $sql->getUserDetails($token, $args['users_id']);
    return $response->withStatus(200)
    ->withHeader("Content-Type","application/json")
    ->write(json_encode($array));
});

$app->delete('/get/deleteuser/{users_id}/', function($request, $response, $args) {   
    $token = $request->getHeaderLine('Authorization');
    global $sql;
    $array['status'] = '200';
    $array['message'] = 'Request Successfull !';        
    $array['response']= $sql->deleteUser($token, $args['users_id']);
    return $response->withStatus(200)
    ->withHeader("Content-Type","application/json")
    ->write(json_encode($array));
});

$app->post('/update/status/', function($request, $response) {   
    $token = $request->getHeaderLine('Authorization');
    $post = $request->getParsedBody();  

    if(!isset($_POST['users_id'])){ $post['users_id'] = ''; }
    if(!isset($_POST['status'])){ $post['status'] = ''; }

    global $sql;     
    $array['status'] = '200';
    $array['message'] = 'Request Successfull !';        
    $array['response']= $sql->updateUserStatus($token, $post['users_id'], $post['status']);
    return $response->withStatus(200)
    ->withHeader("Content-Type","application/json")
    ->write(json_encode($array));
});

$app->post('/update/details/', function($request, $response) {   
    $token = $request->getHeaderLine('Authorization');
    $post = $request->getParsedBody();  
    
    if(!isset($_POST['users_id'])){ $post['users_id'] = ''; }
    if(!isset($_POST['name'])){ $post['name'] = ''; }
    if(!isset($_POST['email'])){ $post['email'] = ''; }
    if(!isset($_POST['phone'])){ $post['phone'] = ''; }
    if(!isset($_POST['user_type'])){ $post['user_type'] = ''; }
    if(!isset($_POST['user_role'])){ $post['user_role'] = ''; }

    $imgname = "";
    if(isset($_FILES['image']) ){
        if($_FILES['image']['error'] === 0){
            $files = $_FILES['image'];
            $imgname = date("Ymdhis")."_".$files['name'];
            move_uploaded_file($files['tmp_name'], "../../uploads/users/".$imgname);
        }
    }

    global $sql;
    $array['status'] = '200';
    $array['message'] = 'Request Successfull !';
    $array['response']= $sql->updateUserDetails($token, $post['users_id'], $post['name'], $post['email'], $post['phone'], $imgname, $post['user_type'], $post['user_role']);
    return $response->withStatus(200)
    ->withHeader("Content-Type","application/json")
    ->write(json_encode($array));
});

$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});
$app->run();