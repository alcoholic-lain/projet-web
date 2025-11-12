<?php
require_once __DIR__ . '/../../model/Category.php';

header('Content-Type: application/json');

// Sample categories data
$categories = [
    new Category(1, 'Technology', 'Tech news and discussions', 'fa-laptop', '#3b82f6'),
    new Category(2, 'Sports', 'Sports updates and events', 'fa-basketball', '#ef4444'),
    new Category(3, 'Music', 'Music and entertainment', 'fa-music', '#8b5cf6'),
    new Category(4, 'Gaming', 'Video games and esports', 'fa-gamepad', '#10b981'),
    new Category(5, 'Science', 'Scientific discoveries', 'fa-flask', '#f59e0b'),
    new Category(6, 'Travel', 'Travel experiences and tips', 'fa-plane', '#06b6d4'),
    new Category(7, 'Food', 'Recipes and restaurants', 'fa-utensils', '#f97316'),
    new Category(8, 'Education', 'Learning and courses', 'fa-graduation-cap', '#6366f1')
];

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'list':
        echo json_encode([
            'success' => true,
            'data' => array_map(function($cat) {
                return $cat->toArray();
            }, $categories)
        ]);
        break;
        
    case 'get':
        $id = intval($_GET['id'] ?? 0);
        $category = null;
        foreach ($categories as $cat) {
            if ($cat->getId() === $id) {
                $category = $cat;
                break;
            }
        }
        if ($category) {
            echo json_encode([
                'success' => true,
                'data' => $category->toArray()
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Category not found'
            ]);
        }
        break;
        
    default:
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action'
        ]);
}
