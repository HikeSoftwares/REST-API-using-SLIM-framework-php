<?php
require '../../vendor/autoload.php';
require '../dbconn.php';
require 'pdo.php';
$sql = new auth();
// This Slim setting is required for the middleware to work
$app = new Slim\App(array(
    'debug' => true
));

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization, AUTH_TOKEN, API_KEY')
			->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
			->withHeader('content-type', 'application/json');
});

$app->get('/test/', function() use($app){  
	echo 'Welcom to API with RakeBlue';
});

$app->post('/post/login/', function($request, $response) {
    $api_key = $request->getHeaderLine('Authorization');
    $post = $request->getParsedBody();

    if(!isset($_POST['username'])){ $post['username'] = ''; }
    if(!isset($_POST['password'])){ $post['password'] = ''; }

    global $sql; 
    $array['status'] = '200';
    $array['message'] = 'Request Successfull !';     
    $array['response']= $sql->getUserLogin($post['username'],$post['password'], $api_key);
    return $response->withStatus(200)
    ->write(json_encode($array));
});


$app->post('/post/validate_token/', function($request, $response) {   	
    $api_key = $request->getHeaderLine('Authorization');
    $post = $request->getParsedBody();
    
    if(!isset($_POST['token'])){ $post['token'] = ''; }

    global $sql; 
    $array['status'] = '200';
    $array['message'] = 'Request Successfull !';     
    $array['response']= $sql->validateAuthToken($post['token'], $api_key);        
    return $response->withStatus(200)
    ->write(json_encode($array));
});

$app->post('/post/validate_email/', function($request, $response) {   	
    $api_key = $request->getHeaderLine('Authorization');
    $post = $request->getParsedBody();

    if(!isset($_POST['users_id'])){ $post['users_id'] = ''; }
    if(!isset($_POST['otp'])){ $post['otp'] = ''; }

    global $sql; 
    $array['status'] = '200';
    $array['message'] = 'Request Successfull !';     
    $array['response']= $sql->validateEmail($api_key, $post['users_id'], $post['otp']);        
    return $response->withStatus(200)
    ->write(json_encode($array));
});

$app->post('/post/forgot_password/', function($request, $response) {   	
    $api_key = $request->getHeaderLine('Authorization');
    $post = $request->getParsedBody();

    if(!isset($_POST['email'])){ $post['email'] = ''; }

    global $sql; 
    $array['status'] = '200';
    $array['message'] = 'Request Successfull !';     
    $array['response']= $sql->forgotPassword($api_key, $post['email']);        
    return $response->withStatus(200)
    ->write(json_encode($array));
});

$app->post('/post/change_password/', function($request, $response) {   	
    $token = $request->getHeaderLine('Authorization');
    $post = $request->getParsedBody();

    if(!isset($_POST['old_password'])){ $post['old_password'] = ''; }
    if(!isset($_POST['new_password'])){ $post['new_password'] = ''; }

    global $sql; 
    $array['status'] = '200';
    $array['message'] = 'Request Successfull !';
    $array['response']= $sql->changePassword($token, $post['old_password'], $post['new_password']);        
    return $response->withStatus(200)
    ->write(json_encode($array));
});

$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});

$app->run();
?>