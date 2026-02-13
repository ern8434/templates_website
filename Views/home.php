<?php if (empty($title)): ?>
    <div class="hero-section">
        <h1 class="hero-title">Premium Website Templates</h1>
        <p class="hero-subtitle">
            Discover a curated collection of high-quality website templates. 
            From minimalist portfolios to robust business themes, find the perfect foundation for your next web project.
        </p>
    </div>
<?php elseif (isset($title) && !empty($title)): ?>
    <h2><?php echo htmlspecialchars($title); ?></h2>
<?php endif; ?>

<div class="grid">
    <?php if (!empty($items)): ?>
        <?php foreach ($items as $item): ?>
            <?php 
                // Determine image URL safely
                $thumbnail = '';
                if (isset($item['thumbnail'])) {
                    $thumbnail = $item['thumbnail']; 
                } elseif (isset($item['previews']['icon_with_landscape_preview']['landscape_url'])) {
                     $thumbnail = $item['previews']['icon_with_landscape_preview']['landscape_url'];
                } elseif (isset($item['previews']['landscape_preview']['landscape_url'])) {
                     $thumbnail = $item['previews']['landscape_preview']['landscape_url'];
                } elseif (isset($item['previews']['icon_with_landscape_preview']['icon_url'])) {
                     $thumbnail = $item['previews']['icon_with_landscape_preview']['icon_url'];
                } elseif (isset($item['previews']['icon_preview']['icon_url'])) {
                     $thumbnail = $item['previews']['icon_preview']['icon_url'];
                } elseif (isset($item['image'])) {
                     $thumbnail = $item['image'];
                }
            ?>
            <div class="card">
                <?php if ($thumbnail): ?>
                    <img src="<?php echo htmlspecialchars($thumbnail); ?>" alt="<?php echo htmlspecialchars($item['name'] ?? 'Item'); ?>" class="card-img">
                <?php else: ?>
                    <div style="height:180px; background:#ddd; display:flex; align-items:center; justify-content:center; color:#777;">No Image</div>
                <?php endif; ?>
                
                <div class="card-body">
                    <h3 class="card-title">
                        <a href="item?id=<?php echo htmlspecialchars($item['id']); ?>">
                            <?php echo htmlspecialchars($item['name'] ?? $item['item'] ?? 'Untitled'); ?>
                        </a>
                    </h3>
                    <div class="card-meta">
                        <span>By <?php echo htmlspecialchars($item['author_username'] ?? $item['user'] ?? 'Unknown'); ?></span>
                        <!-- Price handling might differ between endpoints -->
                         <?php 
                             $price = isset($item['price_cents']) ? '$' . ($item['price_cents'] / 100) : '';
                             /* Search might not return price in all contexts or different format */
                         ?>
                         <?php if ($price): ?>
                            <strong><?php echo $price; ?></strong>
                         <?php endif; ?>
                    </div>
                     <div style="margin-top: 10px;">
                        <a href="item?id=<?php echo htmlspecialchars($item['id']); ?>" class="btn" style="width:100%; text-align:center; box-sizing:border-box;">View Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No templates found.</p>
    <?php endif; ?>
</div>
