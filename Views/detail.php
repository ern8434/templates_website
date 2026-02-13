<div class="detail-view">
    <div class="detail-left">
         <?php 
            $image = '';
            if (isset($item['previews']['landscape_preview']['landscape_url'])) {
                $image = $item['previews']['landscape_preview']['landscape_url'];
            } elseif (isset($item['previews']['live_site']['url'])) {
                 // Sometimes preview might just be the link
            }
         ?>
         <?php if ($image): ?>
            <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="detail-img">
         <?php else: ?>
             <div style="height:300px; background:#ddd; display:flex; align-items:center; justify-content:center; color:#777;">No valid preview image available</div>
         <?php endif; ?>
    </div>
    <div class="detail-right">
        <h1><?php echo htmlspecialchars($item['name']); ?></h1>
        <p class="meta">
            By <?php echo htmlspecialchars($item['author_username']); ?> 
            in <?php echo htmlspecialchars($item['classification']); ?>
        </p>
        
        <div class="price-box" style="font-size: 2rem; color: var(--primary-color); margin: 1rem 0;">
            <?php echo isset($item['price_cents']) ? '$' . ($item['price_cents'] / 100) : ''; ?>
        </div>

        <div style="margin-bottom: 2rem;">
            <a href="<?php echo htmlspecialchars($item['url']); ?>" target="_blank" class="btn" style="font-size: 1.2rem; padding: 1rem 2rem;">Preview &amp; Buy Template</a>
        </div>
    </div>
</div>

<div class="detail-full" style="margin-bottom: 4rem;">
    <div class="description">
        <?php 
            $desc = $item['description_html'] ?? $item['description'] ?? '';
            // If it's HTML, allow some formatting tags
            if (strpos($desc, '<') !== false) {
                $allowedTags = '<a><b><i><strong><em><p><br><ul><li><ol><h3><h4><h5>';
                $html = strip_tags($desc, $allowedTags);
                // Add target="_blank" to links if not present
                $html = preg_replace('/<a\s+(?![^>]*target=)([^>]+)>/i', '<a $1 target="_blank">', $html);
                // Add rel="nofollow" to links if not present
                echo preg_replace('/<a\s+(?![^>]*rel=)([^>]+)>/i', '<a $1 rel="nofollow">', $html);
            } else {
                // If it's plain text, preserve newlines
                echo nl2br(htmlspecialchars($desc));
            }
        ?>
    </div>
    
    <?php if (isset($item['attributes'])): ?>
        <div class="attributes" style="margin-top: 2rem; border-top: 1px solid #ddd; padding-top: 1rem;">
            <h3>Attributes</h3>
            <ul>
                <?php foreach ($item['attributes'] as $attr): ?>
                    <li><strong><?php echo htmlspecialchars($attr['name']); ?>:</strong> <?php echo htmlspecialchars(is_array($attr['value']) ? implode(', ', $attr['value']) : $attr['value']); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (isset($item['tags'])): ?>
        <div class="tags" style="margin-top: 1rem;">
            <strong>Tags:</strong> 
            <?php echo htmlspecialchars(implode(', ', $item['tags'])); ?>
        </div>
    <?php endif; ?>

    <div style="margin-top: 3rem;">
        <a href="<?php echo htmlspecialchars($item['url']); ?>" target="_blank" class="btn" style="font-size: 1.2rem; padding: 1rem 2rem;">Preview &amp; Buy Template</a>
    </div>
</div>
