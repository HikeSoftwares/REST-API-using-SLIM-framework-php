<?php
require '../../vendor/autoload.php';

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\UploadedFile;

require '../dbconn.php';
require 'pdo.php';
$sql = new business();

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

$app->get('/get/businesslist/{from}/{no_of_records}/', function($request, $response, $args){  
    $token = $request->getHeaderLine('Authorization');
    global $sql; 
    $array['status'] = '200';
    $array['message'] = 'Request Successfull !';
    $array['response']= $sql->getBusinessList($token, $args['from'], $args['no_of_records']);
    return $response->withStatus(200)
    ->withHeader("Content-Type","application/json")
    ->write(json_encode($array));
});

$app->get('/get/businessdetails/{business_id}/', function($request, $response, $args){  
    $token = $request->getHeaderLine('Authorization');
    global $sql; 
    $array['status'] = '200';
    $array['message'] = 'Request Successfull !';
    $array['response']= $sql->getBusinessDetails($token, $args['business_id']);
    return $response->withStatus(200)
    ->withHeader("Content-Type","application/json")
    ->write(json_encode($array));
});

$app->post('/post/createbusiness/', function($request, $response, $args) {   
    $API_KEY = $request->getHeaderLine('Authorization');
    $post = $request->getParsedBody();  

    if(!isset($_POST['business_name'])){ $post['business_name'] = ''; }
    if(!isset($_POST['name'])){ $post['name'] = ''; }
    if(!isset($_POST['email'])){ $post['email'] = ''; }
    if(!isset($_POST['password'])){ $post['password'] = ''; }
    if(!isset($_POST['phone'])){ $post['phone'] = ''; }

    $business_logo = "";
    if(isset($_FILES['business_logo']) ){
        if($_FILES['business_logo']['error'] === 0){
            $files = $_FILES['business_logo'];
            $business_logo = date("Ymdhis")."_".$files['name'];
            move_uploaded_file($files['tmp_name'], "../../uploads/business/".$business_logo);
        }
    }

    global $sql;
    $array['status'] = '200';
    $array['message'] = 'Request Successfull !';        
    $array['response']= $sql->createBusiness($API_KEY, $post['business_name'], $post['name'], $post['email'], $post['password'], $post['phone'], $business_logo);
    return $response->withStatus(200)
    ->withHeader("Content-Type","application/json")
    ->write(json_encode($array));
});

$app->delete('/get/deletebusiness/{business_id}/', function($request, $response, $args) {   
    $token = $request->getHeaderLine('Authorization');
    global $sql;
    $array['status'] = '200';
    $array['message'] = 'Request Successfull !';
    $array['response']= $sql->deleteBusiness($token, $args['business_id']);
    return $response->withStatus(200)
    ->withHeader("Content-Type","application/json")
    ->write(json_encode($array));
});

$app->post('/update/business/', function($request, $response) {   
    $token = $request->getHeaderLine('Authorization');
    $post = $request->getParsedBody();  

    if(!isset($_POST['business_id'])){ $post['business_id'] = ''; }
    if(!isset($_POST['business_name'])){ $post['business_name'] = ''; }
    if(!isset($_POST['business_status'])){ $post['business_status'] = ''; }
    if(!isset($_POST['fb_app_id'])){ $post['fb_app_id'] = ''; }
    if(!isset($_POST['fb_secret_key'])){ $post['fb_secret_key'] = ''; }
    if(!isset($_POST['fb_auth_token'])){ $post['fb_auth_token'] = ''; }
    if(!isset($_POST['twitter_consumer_key'])){ $post['twitter_consumer_key'] = ''; }
    if(!isset($_POST['twitter_consumer_secret'])){ $post['twitter_consumer_secret'] = ''; }
    if(!isset($_POST['twitter_token'])){ $post['twitter_token'] = ''; }
    if(!isset($_POST['twitter_token_secret'])){ $post['twitter_token_secret'] = ''; }
    if(!isset($_POST['twitter_username'])){ $post['twitter_username'] = ''; }
    if(!isset($_POST['fb_msg_mention'])){ $post['fb_msg_mention'] = ''; }
    if(!isset($_POST['fb_msg_direct_message'])){ $post['fb_msg_direct_message'] = ''; }
    if(!isset($_POST['fb_msg_new_followers'])){ $post['fb_msg_new_followers'] = ''; }
    if(!isset($_POST['tw_msg_mention'])){ $post['tw_msg_mention'] = ''; }
    if(!isset($_POST['tw_msg_direct_message'])){ $post['tw_msg_direct_message'] = ''; }
    if(!isset($_POST['tw_msg_retweets'])){ $post['tw_msg_retweets'] = ''; }
    if(!isset($_POST['tw_msg_retweets_comments'])){ $post['tw_msg_retweets_comments'] = ''; }
    if(!isset($_POST['tw_msg_new_followers'])){ $post['tw_msg_new_followers'] = ''; }


    $business_logo = "";
    if(isset($_FILES['business_logo']) ){
        if($_FILES['business_logo']['error'] === 0){
            $files = $_FILES['business_logo'];
            $business_logo = date("Ymdhis")."_".$files['name'];
            move_uploaded_file($files['tmp_name'], "../../uploads/business/".$business_logo);
        }
    }

    global $sql;
    $array['status'] = '200';
    $array['message'] = 'Request Successfull !';
    $array['response']= $sql->updateBusiness($token, $post['business_id'], $post['business_name'], $post['business_status'], $business_logo, $post['fb_app_id'], $post['fb_secret_key'], $post['fb_auth_token'], $post['twitter_consumer_key'], $post['twitter_consumer_secret'], $post['twitter_token'], $post['twitter_token_secret'], $post['twitter_username'], $post['fb_msg_mention'], $post['fb_msg_direct_message'], $post['fb_msg_new_followers'], $post['tw_msg_mention'], $post['tw_msg_direct_message'], $post['tw_msg_retweets'], $post['tw_msg_retweets_comments'], $post['tw_msg_new_followers']);
    return $response->withStatus(200)
    ->withHeader("Content-Type","application/json")
    ->write(json_encode($array));
});

$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});
$app->run();