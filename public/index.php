<?php

require_once __DIR__ . '/../vendor/autoload.php';
use App\Database\Database;
use App\Utility\ErrorHandler;
use App\Utility\CorsHandler;
use App\Utility\RateLimiter;

ErrorHandler::register();

try {
    $dbConfig = [
        'DB_HOST' => getenv('DB_HOST'),
        'DB_USER' => getenv('DB_USER'),
        'DB_PASS' => getenv('DB_PASS'),
        'DB_DATABASE' => getenv('DB_DATABASE'),
        'DB_DRIVER' => getenv('DB_DRIVER') ?: 'mysql',
        'DB_PORT' => getenv('DB_PORT') ?: '3306'  // Add this line with default fallback    
    ];
    
    Database::initialize($dbConfig);
    
    CorsHandler::addHeaders();
    CorsHandler::handlePreflight();
    
    $connection = Database::getConnection();
    
    $clientIp = $_SERVER['REMOTE_ADDR'];

    if (!RateLimiter::isAllowed($clientIp)) {
        header('Content-Type: application/json');
        http_response_code(429); 
        RateLimiter::addRateLimitHeaders($clientIp);
        echo json_encode([
            'errors' => [['message' => 'Too Many Requests. Please try again later.']]
        ]);
        exit;
    }
    
    
    RateLimiter::addRateLimitHeaders($clientIp);
    
    $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
        $r->post('/graphql', [App\Controller\GraphQL::class, 'handle']);
    });
    
    // In your public/index.php file
    $uri = $_SERVER['REQUEST_URI'];
    // Strip query string and normalize slashes
    $uri = parse_url($uri, PHP_URL_PATH);
    $uri = '/' . trim($uri, '/'); // This ensures the URI starts with a slash

    $routeInfo = $dispatcher->dispatch(
        $_SERVER['REQUEST_METHOD'],
        $uri
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