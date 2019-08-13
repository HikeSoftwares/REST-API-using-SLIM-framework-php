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

$app->get('/get/organizationlist/{from}/{no_of_records}/', function($request, $response, $args){  
    $token = $request->getHeaderLine('Authorization');
    global $sql; 
    $array['status'] = '200';
    $array['message'] = 'Request Successfull !';
    $array['response']= $sql->getOrganizationList($token, $args['from'], $args['no_of_records']);
    return $response->withStatus(200)
    ->withHeader("Content-Type","application/json")
    ->write(json_encode($array));
});

$app->post('/post/createorganization/', function($request, $response, $args) {   
    $token = $request->getHeaderLine('Authorization');
    $post = $request->getParsedBody();  

    if(!isset($_POST['organization_name'])){ $post['organization_name'] = ''; }

    global $sql;
    $array['status'] = '200';
    $array['message'] = 'Request Successfull !';        
    $array['response']= $sql->createOrganization($token, $post['organization_name']);
    return $response->withStatus(200)
    ->withHeader("Content-Type","application/json")
    ->write(json_encode($array));
});

$app->delete('/get/deleteorganization/{organization_id}/', function($request, $response, $args) {   
    $token = $request->getHeaderLine('Authorization');
    global $sql;
    $array['status'] = '200';
    $array['message'] = 'Request Successfull !';
    $array['response']= $sql->deleteOrganization($token, $args['organization_id']);
    return $response->withStatus(200)
    ->withHeader("Content-Type","application/json")
    ->write(json_encode($array));
});

$app->post('/update/organization/', function($request, $response) {   
    $token = $request->getHeaderLine('Authorization');
    $post = $request->getParsedBody();  

    if(!isset($_POST['organization_name'])){ $post['organization_name'] = ''; }
    if(!isset($_POST['organization_id'])){ $post['organization_id'] = ''; }
    if(!isset($_POST['organization_status'])){ $post['organization_status'] = ''; }

    global $sql;
    $array['status'] = '200';
    $array['message'] = 'Request Successfull !';
    $array['response']= $sql->updateOrganization($token, $post['organization_id'], $post['organization_name'], $post['organization_status']);
    return $response->withStatus(200)
    ->withHeader("Content-Type","application/json")
    ->write(json_encode($array));
});

$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});
$app->run();