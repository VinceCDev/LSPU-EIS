<?php
// Define allowed routes
$allowed_routes = ['signup', 'login', 'dashboard'];

// Get the route from URL
$route = isset($_GET['route']) ? $_GET['route'] : 'login';

// Prevent directory traversal attacks
$route = preg_replace('/[^a-z0-9_-]/i', '', $route);

// Check if the route is allowed
if (in_array($route, $allowed_routes)) {
    require $route . '.php';
} else {
    http_response_code(404);
    echo "404 Page Not Found";
}
