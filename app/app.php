<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\App as Application;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Bridge\Response\ErrorException;
use Bridge\Model\Equipment;

$container = new Container(require __DIR__ . '/config/config.php');

$app = new Application($container);

/**
 * route
 */
$app->get('/', function (Request $request, Response $response) use ($container) {
    $database = __DIR__ . '/equipment.db';
    
    if (!is_file($database)) {
        $sqlite = new SQLite3($database);
        $sqlite->open();
        $sqlite->close();
    }
    
    return $response;
});

$app->get('/equipment/next/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');

    try {
        $equipment = (new Equipment($this))->findByAssetNumber($id);
        
        return $response->withJson($equipment);
    } catch (ErrorException $e) {
        return $response->withJson($e->getError());
    } catch (\Exception $e) {
        throw $e;
    }
});

$app->run();
