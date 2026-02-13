<?php

namespace App;

class EnvatoClient {
    private $apiToken;
    private $cacheDir;
    private $cacheTime = 600; // 10 minutes

    public function __construct() {
        $this->apiToken = Config::getApiToken();
        $this->cacheDir = __DIR__ . '/../cache/';
        
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    private function request($url, $useCache = true) {
        $cacheFile = $this->cacheDir . md5($url) . '.json';

        // Check cache
        if ($useCache && file_exists($cacheFile) && (time() - filemtime($cacheFile) < $this->cacheTime)) {
            return json_decode(file_get_contents($cacheFile), true);
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->apiToken}",
            "User-Agent: EnvatoTemplateDirectory/1.0"
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 403 || $httpCode === 401) {
             // Handle authorization error (maybe return a specialized error structure)
             return ['error' => 'API Authorization Failed. Check your token.'];
        }

        if ($response) {
            if ($useCache) {
                file_put_contents($cacheFile, $response);
            }
            return json_decode($response, true);
        }

        return [];
    }

    // Uses search endpoint to get richer data (images) for new files
    public function getNewFiles() {
        // Search blank term, sort by date, site themeforest
        $endpoint = "https://api.envato.com/v1/discovery/search/search/item?site=themeforest.net&sort_by=date";
        return $this->request($endpoint);
    }

    // https://api.envato.com/v1/market/categories:themeforest.json
    // Returns a hierarchical tree of categories based on 'path'
    public function getCategories() {
        $data = $this->request('https://api.envato.com/v1/market/categories:themeforest.json');
        
        if (!isset($data['categories'])) {
            return [];
        }

        return ['categories' => $this->buildCategoryTree($data['categories'])];
    }

    private function buildCategoryTree($categories) {
        $tree = [];
        $index = []; // Map path to reference

        // Sort by path length to ensure parents come before children generally, 
        // though Envato API usually returns parents first.
        usort($categories, function($a, $b) {
            return strlen($a['path']) - strlen($b['path']);
        });

        foreach ($categories as $cat) {
            $path = $cat['path'];
            
            // Exclude specific categories and their children
            if ($path === 'courses' || strpos($path, 'courses/') === 0) {
                continue;
            }

            $parts = explode('/', $path);
            $cat['children'] = [];
            
            // Store reference
            $index[$path] = $cat;

            if (count($parts) === 1) {
                // Top level
                $tree[] = &$index[$path];
            } else {
                // Child
                $parentPath = implode('/', array_slice($parts, 0, -1));
                if (isset($index[$parentPath])) {
                    $index[$parentPath]['children'][] = &$index[$path];
                } else {
                    // Fallback if parent missing, add to root
                    $tree[] = &$index[$path];
                }
            }
        }
        
        // Break references
        unset($index);
        return $tree;
    }

    // https://api.envato.com/v1/discovery/search/search/item?term=...
    public function searchItems($query, $category = '') {
        $endpoint = "https://api.envato.com/v1/discovery/search/search/item?site=themeforest.net";
        
        if (!empty($query)) {
            $endpoint .= "&term=" . urlencode($query);
        }
        
        if (!empty($category)) {
            $endpoint .= "&category=" . urlencode($category);
        }
        
        // Disable cache if search query is present
        $useCache = empty($query);
        return $this->request($endpoint, $useCache);
    }

    // https://api.envato.com/v3/market/catalog/item?id=...
    public function getItem($id) {
        $endpoint = "https://api.envato.com/v3/market/catalog/item?id=" . urlencode($id);
        return $this->request($endpoint, false);
    }
}
