<?php

require_once __DIR__ . '/../vendor/autoload.php';
use App\Database\Database;
use App\Utility\ErrorHandler;
use App\Utility\CorsHandler;
use App\Utility\RateLimiter;

ErrorHandler::register();

try {
    Database::initialize(parse_ini_file(__DIR__ . '/../.env'));
    
    CorsHandler::addHeaders();
    CorsHandler::handlePreflight();
    
    $connection = Database::getConnection();
    
    $clientIp = $_SERVER['REMOTE_ADDR'];

    if (!RateLimiter::isAllowed($clientIp)) {
        header('Content-Type: application/json');
        http_response_code(429); // Too Many Requests
        RateLimiter::addRateLimitHeaders($clientIp);
        echo json_encode([
            'errors' => [['message' => 'Too Many Requests. Please try again later.']]
        ]);
        exit;
    }
    
    // Add rate limit headers to successful responses
    RateLimiter::addRateLimitHeaders($clientIp);
    
    $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
        $r->post('/graphql', [App\Controller\GraphQL::class, 'handle']);
    });
    
    $routeInfo = $dispatcher->dispatch(
        $_SERVER['REQUEST_METHOD'],
        $_SERVER['REQUEST_URI']
    );
    
    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode([
                'errors' => [['message' => 'Not Found']]
            ]);
            break;
            
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            $allowedMethods = $routeInfo[1];
            header('Content-Type: application/json');
            http_response_code(405);
            echo json_encode([
                'errors' => [['message' => 'Method Not Allowed. Allowed methods: ' . implode(', ', $allowedMethods)]]
            ]);
            break;
            
        case FastRoute\Dispatcher::FOUND:
            $handler = $routeInfo[1];
            $vars = $routeInfo[2];
            echo $handler($vars);
            break;
    }
} catch (Throwable $e) {
    ErrorHandler::handleException($e);
}

ErrorHandler::unregister();