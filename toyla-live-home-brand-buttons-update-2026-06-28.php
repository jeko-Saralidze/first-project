<?php
/**
 * Toyla live homepage hero brand button update.
 *
 * Changes only the three Elementor hero buttons:
 * - 8c54882 => Stapelstein
 * - 7c595b3 => Clixo
 * - d8900b4 => MODU
 */

$wp_load = '/home/toylage/public_html/wp-load.php';

if (!file_exists($wp_load)) {
    fwrite(STDERR, "ERROR: wp-load.php not found at {$wp_load}\n");
    exit(1);
}

require_once $wp_load;

$page_id = (int) get_option('page_on_front');
if (!$page_id) {
    $front_page = get_page_by_path('მთავარი') ?: get_page_by_title('მთავარი');
    $page_id = $front_page ? (int) $front_page->ID : 1631;
}

$page = get_post($page_id);
if (!$page) {
    fwrite(STDERR, "ERROR: Homepage post not found. Tried ID {$page_id}\n");
    exit(1);
}

$elementor_data = get_post_meta($page_id, '_elementor_data', true);
if (!$elementor_data) {
    fwrite(STDERR, "ERROR: _elementor_data is empty for page {$page_id}\n");
    exit(1);
}

$backup_dir = '/home/toylage/wordpress-backups';
if (!is_dir($backup_dir) && !mkdir($backup_dir, 0755, true)) {
    fwrite(STDERR, "ERROR: Cannot create backup dir {$backup_dir}\n");
    exit(1);
}

$backup_file = $backup_dir . '/toyla-home-brand-buttons-before-' . date('Ymd-His') . '.json';
$backup = array(
    'created_at' => date('c'),
    'page_id' => $page_id,
    'post_title' => $page->post_title,
    'post_content' => $page->post_content,
    '_elementor_data' => $elementor_data,
);

file_put_contents(
    $backup_file,
    wp_json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);

$data = json_decode($elementor_data, true);
if (!is_array($data)) {
    fwrite(STDERR, "ERROR: _elementor_data JSON could not be decoded.\nBackup: {$backup_file}\n");
    exit(1);
}

$buttons = array(
    '8c54882' => array(
        'text' => 'Stapelstein',
        'url' => 'https://toyla.ge/shop/?filtering=1&filter_product_brand=138',
    ),
    '7c595b3' => array(
        'text' => 'Clixo',
        'url' => 'https://toyla.ge/shop/?filtering=1&filter_product_brand=134',
    ),
    'd8900b4' => array(
        'text' => 'MODU',
        'url' => 'https://toyla.ge/shop/?filtering=1&filter_product_brand=137',
    ),
);

$updated = array();
$walk = function (&$nodes) use (&$walk, $buttons, &$updated) {
    foreach ($nodes as &$node) {
        if (
            isset($node['widgetType'], $node['id'], $buttons[$node['id']])
            && $node['widgetType'] === 'button'
        ) {
            $id = $node['id'];
            $node['settings']['text'] = $buttons[$id]['text'];
            $node['settings']['link']['url'] = $buttons[$id]['url'];
            $updated[$id] = $buttons[$id];
        }

        if (!empty($node['elements']) && is_array($node['elements'])) {
            $walk($node['elements']);
        }
    }
};
$walk($data);

$missing = array_diff(array_keys($buttons), array_keys($updated));
if ($missing) {
    fwrite(STDERR, "ERROR: Missing Elementor button IDs: " . implode(', ', $missing) . "\nBackup: {$backup_file}\n");
    exit(1);
}

$new_elementor_data = wp_json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
update_post_meta($page_id, '_elementor_data', wp_slash($new_elementor_data));

$post_content = $page->post_content;
foreach ($buttons as $id => $button) {
    $brand_id = preg_match('/filter_product_brand=(\d+)/', $button['url'], $m) ? $m[1] : '';
    if (!$brand_id) {
        continue;
    }

    $pattern = '~(<a\s+href=["\']https?://toyla\.ge/shop/\?filtering=1(?:&|&#038;)filter_product_brand=' . preg_quote($brand_id, '~') . '["\'][^>]*>\s*)(?:ნახე\s+)?(?:პროდუქცია|Stapelstein|Clixo|MODU)(\s*</a>)~su';
    $post_content = preg_replace($pattern, '${1}' . $button['text'] . '${2}', $post_content, 1);
}

wp_update_post(array(
    'ID' => $page_id,
    'post_content' => wp_slash($post_content),
));

delete_post_meta($page_id, '_elementor_element_cache');
delete_transient('elementor_css_file_' . $page_id);

if (class_exists('\Elementor\Plugin')) {
    \Elementor\Plugin::$instance->files_manager->clear_cache();
}

clean_post_cache($page_id);

echo "OK: Homepage brand buttons updated.\n";
echo "Page ID: {$page_id}\n";
echo "Backup: {$backup_file}\n";
foreach ($updated as $id => $button) {
    echo "{$id}: {$button['text']} => {$button['url']}\n";
}
