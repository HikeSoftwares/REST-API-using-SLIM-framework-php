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

$app->post('/post/createcompetitors/', function($request, $response, $args) {   
    $token = $request->getHeaderLine('Authorization');
    $post = $request->getParsedBody();
    
    if(!isset($_POST['competitors_name'])){ $post['competitors_name'] = ''; }
    if(!isset($_POST['facebook_url'])){ $post['facebook_url'] = ''; }
    if(!isset($_POST['twitter_url'])){ $post['twitter_url'] = ''; }
    if(!isset($_POST['twitter_handle'])){ $post['twitter_handle'] = ''; }
    
    $imgname = "";
    if(isset($_FILES['competitors_logo']) ){
        if($_FILES['competitors_logo']['error'] === 0){
            $files = $_FILES['competitors_logo'];
            $imgname = date("Ymdhis")."_".$files['name'];
            move_uploaded_file($files['tmp_name'], "../../uploads/competitors/".$imgname);
        }
    }
    
    global $sql;
    $array['status'] = '200';
    $array['message'] = 'Request Successfull !';        
    $array['response']= $sql->createCompetitors($token, $post['competitors_name'], $post['facebook_url'], $post['twitter_url'], $post['twitter_handle'], $imgname);
    return $response->withStatus(200)
    ->withHeader("Content-Type","application/json")
    ->write(json_encode($array));
});

$app->get('/get/competitorslist/{from}/{no_of_records}/', function($request, $response, $args){  
    $token = $request->getHeaderLine('Authorization');
    global $sql; 
    $array['status'] = '200';
    $array['message'] = 'Request Successfull !';
    $array['response']= $sql->getCompetitorsList($token, $args['from'], $args['no_of_records']);
    return $response->withStatus(200)
    ->withHeader("Content-Type","application/json")
    ->write(json_encode($array));
});

$app->get('/get/competitorsdetails/{competitors_id}/', function($request, $response, $args) {   
    $token = $request->getHeaderLine('Authorization');
    global $sql;
    $array['status'] = '200';
    $array['message'] = 'Request Successfull !';
    $array['response']= $sql->getCompetitorsDetails($token, $args['competitors_id']);
    return $response->withStatus(200)
    ->withHeader("Content-Type","application/json")
    ->write(json_encode($array));
});

$app->post('/update/competitor/', function($request, $response) {   
    $token = $request->getHeaderLine('Authorization');
    $post = $request->getParsedBody();  

    if(!isset($_POST['competitors_id'])){ $post['competitors_id'] = ''; }
    if(!isset($_POST['competitors_name'])){ $post['competitors_name'] = ''; }
    if(!isset($_POST['facebook_url'])){ $post['facebook_url'] = ''; }
    if(!isset($_POST['twitter_url'])){ $post['twitter_url'] = ''; }
    if(!isset($_POST['twitter_handle'])){ $post['twitter_handle'] = ''; }
    if(!isset($_POST['competitors_status'])){ $post['competitors_status'] = ''; }

    $imgname = "";
    if(isset($_FILES['competitors_logo']) ){
        if($_FILES['competitors_logo']['error'] === 0){
            $files = $_FILES['competitors_logo'];
            $imgname = date("Ymdhis")."_".$files['name'];
            move_uploaded_file($files['tmp_name'], "../../uploads/competitors/".$imgname);
        }
    }

    global $sql;
    $array['status'] = '200';
    $array['message'] = 'Request Successfull !';
    $array['response']= $sql->updateCompetitor($token, $post['competitors_id'], $post['competitors_name'], $post['facebook_url'], $post['twitter_url'], $post['twitter_handle'], $post['competitors_status'], $imgname);
    return $response->withStatus(200)
    ->withHeader("Content-Type","application/json")
    ->write(json_encode($array));
});

$app->delete('/get/deletecompetitor/{competitors_id}/', function($request, $response, $args) {   
    $token = $request->getHeaderLine('Authorization');
    global $sql;
    $array['status'] = '200';
    $array['message'] = 'Request Successfull !';
    $array['response']= $sql->deleteCompetitor($token, $args['competitors_id']);
    return $response->withStatus(200)
    ->withHeader("Content-Type","application/json")
    ->write(json_encode($array));
});

$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});
$app->run();