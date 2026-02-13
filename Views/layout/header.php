<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo (!empty($title)) ? $title . ' | ' : ''; ?>Website Templates Directory</title>
    <link rel="icon" type="image/svg+xml" href="public/favicon.svg">
    <meta name="description" content="<?php echo isset($metaDescription) ? htmlspecialchars($metaDescription) : 'Explore high-quality website templates for your next project.'; ?>">
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <header>
        <div class="container navbar">
            <a href="index.php" class="logo">Website Templates</a>
            <div class="search-box">
                <form action="search" method="GET" class="search-form">
                    <input type="text" name="q" placeholder="Search templates..." class="search-input" value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                    <button type="submit" class="btn">Search</button>
                </form>
            </div>
        </div>
        
        <?php if (!empty($categories)): ?>
        <nav class="main-menu">
            <div class="container">
                <ul class="menu-list">
                    <li><a href="index.php" class="<?php echo (!isset($_GET['category']) && !isset($item)) ? 'active' : ''; ?>">All</a></li>
                    <?php foreach ($categories as $cat): ?>
                        <?php 
                            $isActive = false;
                            if (isset($_GET['category']) && $_GET['category'] === $cat['path']) {
                                $isActive = true;
                            } elseif (!isset($_GET['category']) && isset($item['classification'])) {
                                // Extract the main category from classification (e.g. "Site Templates / Creative" -> "Site Templates")
                                $parts = explode('/', $item['classification']);
                                $rootCategory = trim($parts[0]);
                                
                                if (strcasecmp($rootCategory, $cat['name']) === 0 || strcasecmp($rootCategory, $cat['path']) === 0) {
                                    $isActive = true;
                                }
                            }
                        ?>
                        <li>
                            <a href="search?category=<?php echo urlencode($cat['path']); ?>" 
                               class="<?php echo $isActive ? 'active' : ''; ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </nav>
        <?php endif; ?>
    </header>
    <main class="container">
