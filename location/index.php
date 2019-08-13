<?php
require '../../vendor/autoload.php';

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\UploadedFile;

require '../dbconn.php';
require 'pdo.php';
$sql = new pos();

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

$app->get('/get/locationlist/{from}/{no_of_records}/', function($request, $response, $args){  
    $token = $request->getHeaderLine('Authorization');
    global $sql; 
    $array['status'] = '200';
    $array['message'] = 'Request Successfull !';
    $array['response']= $sql->getlocationList($token, $args['from'], $args['no_of_records']);
    return $response->withStatus(200)
    ->withHeader("Content-Type","application/json")
    ->write(json_encode($array));
});

$app->post('/post/createlocation/', function($request, $response, $args) {   
    $token = $request->getHeaderLine('Authorization');
    $post = $request->getParsedBody(); 
    
    if(!isset($_POST['location_name'])){ $post['location_name'] = ''; }
    
    global $sql;
    $array['status'] = '200';
    $array['message'] = 'Request Successfull !';        
    $array['response']= $sql->createlocation($token, $post['location_name']);
    return $response->withStatus(200)
    ->withHeader("Content-Type","application/json")
    ->write(json_encode($array));
});

$app->delete('/get/deletelocation/{location_id}/', function($request, $response, $args) {   
    $token = $request->getHeaderLine('Authorization');
    global $sql;
    $array['status'] = '200';
    $array['message'] = 'Request Successfull !';
    $array['response']= $sql->deletelocation($token, $args['location_id']);
    return $response->withStatus(200)
    ->withHeader("Content-Type","application/json")
    ->write(json_encode($array));
});

$app->post('/update/location/', function($request, $response) {   
    $token = $request->getHeaderLine('Authorization');
    $post = $request->getParsedBody();  

    if(!isset($_POST['location_id'])){ $post['location_id'] = ''; }
    if(!isset($_POST['location_name'])){ $post['location_name'] = ''; }
    if(!isset($_POST['location_status'])){ $post['location_status'] = ''; }

    global $sql;
    $array['status'] = '200';
    $array['message'] = 'Request Successfull !';
    $array['response']= $sql->updatelocation($token, $post['location_id'], $post['location_name'], $post['location_status']);
    return $response->withStatus(200)
    ->withHeader("Content-Type","application/json")
    ->write(json_encode($array));
});

$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});
$app->run();