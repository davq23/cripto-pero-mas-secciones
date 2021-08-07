<?php 
require_once '../vendor/autoload.php';

date_default_timezone_set('America/Caracas');
ini_set("error_log", '../logs/'.date("Y-m-d\.\l\o\g"));

use routes\Router;

$router = new Router();

$router->GET('comments/all/(:num)/(:num)', 'controllers\\CommentController::getAll');
$router->POST('email/send', 'controllers\\EmailController::send');
$router->POST('comments/new', 'controllers\\CommentsController::new');