<?php

namespace App;

class Controller {
    private $client;

    public function __construct() {
        $this->client = new EnvatoClient();
    }

    private function render($view, $data = []) {
        // Fetch categories for the header menu (cached by EnvatoClient)
        $categoriesData = $this->client->getCategories();
        $categories = isset($categoriesData['categories']) ? $categoriesData['categories'] : [];

        // Add categories to data
        $data['categories'] = $categories;

        extract($data);
        require_once __DIR__ . '/../Views/layout/header.php';
        require_once __DIR__ . '/../Views/' . $view . '.php';
        require_once __DIR__ . '/../Views/layout/footer.php';
    }

    public function index() {
        $data = $this->client->getNewFiles();
        
        // Structure for view
        $items = [];
        if (isset($data['matches'])) { // Structure from search endpoint logic
           $items = $data['matches'];
        }

        $metaDescription = "Explore a wide range of high-quality website templates, including creative, business, and portfolio themes.";
        $this->render('home', ['items' => $items, 'title' => '', 'metaDescription' => $metaDescription]);
    }

    public function search() {
        $query = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_SPECIAL_CHARS);
        $category = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_SPECIAL_CHARS);
        
        $data = $this->client->searchItems($query ?? '', $category ?? '');
        
        $items = [];
        if (isset($data['matches'])) { // Structure from search endpoint
             $items = $data['matches'];
        }

        $title = "Search Results";
        if ($query) {
            $title .= " for: " . htmlspecialchars($query);
        }
        
        if ($category) {
            // Try to find the pretty name for the category
            $catName = $category; // Fallback
            $cats = $this->client->getCategories(); // It's cached, so cheap
            if (isset($cats['categories'])) {
                 // Flatten tree to find name? Or just search?
                 // Since our tree structure hides children, we need a recursive search or 
                 // just a quick loop if we assume top level for now. 
                 // Actually, if we want to find any category, we need a recursive finder.
                 // For now, let's just do a simple formatting if not found, 
                 // or implement a quick helper.
                 // Simple formatting:
                 $parts = explode('/', $category);
                 $catName = ucwords(str_replace('-', ' ', end($parts)));
            }
            
            if (!$query) {
                 // If only category, plain title
                 $title = $catName;
            } else {
                 $title .= " in " . $catName;
            }
        }

        $metaDescription = "Browse our collection of " . ($catName ?? 'website') . " templates" . ($query ? " for '" . htmlspecialchars($query) . "'" : "") . ".";
        $this->render('home', ['items' => $items, 'title' => $title, 'metaDescription' => $metaDescription]);
    }

    public function item() {
        $id = filter_var($_GET['id'] ?? null, FILTER_SANITIZE_NUMBER_INT);

        if (!$id) {
            header("Location: index.php");
            exit;
        }

        $item = $this->client->getItem($id);
        
        if (isset($item['error'])) {
             // Handle error
             echo "Error: " . $item['error'];
             return;
        }

        $metaDescription = isset($item['description']) ? mb_strimwidth(strip_tags($item['description']), 0, 160, "...") : ($item['name'] ?? 'Website Template');
        $this->render('detail', ['item' => $item, 'title' => $item['name'], 'metaDescription' => $metaDescription]);
    }
}
