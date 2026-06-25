<?php
/**
 * Toyla-test Bilibo color swatches.
 */

if (!defined('ABSPATH')) {
    exit;
}

// Bilibo       false-    ,
//  true   ,        .
define('TOYLA_BILIBO_BAG_AVAILABLE', false);

add_action('woocommerce_cart_loaded_from_session', function ($cart): void {
    if (TOYLA_BILIBO_BAG_AVAILABLE) {
        return;
    }

    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        if (($cart_item['toyla_bilibo_bag'] ?? '') === 'yes') {
            unset(
                $cart->cart_contents[$cart_item_key]['toyla_bilibo_bag'],
                $cart->cart_contents[$cart_item_key]['toyla_bilibo_bag_qty']
            );
        }
    }
});

function toyla_bilibo_is_target_product(): bool {
    return function_exists('is_product') && is_product() && in_array((int) get_the_ID(), [2757, 2769, 2794], true);
}

function toyla_bilibo_is_cart_target_product(int $product_id): bool {
    return in_array($product_id, [2757, 2769, 2794], true);
}

function toyla_bilibo_has_set_option(int $product_id): bool {
    return in_array($product_id, [2757, 2769, 2794], true);
}

function toyla_bilibo_set_price(int $product_id): float {
    return $product_id === 2794 ? 250.0 : 450.0;
}

function toyla_bilibo_bag_price(): float {
    return 5.0;
}

function toyla_bilibo_cart_item_is_in_stock(array $cart_item): bool {
    if (!empty($cart_item['toyla_preorder_stock'])) {
        return false;
    }

    $product_id = (int) ($cart_item['product_id'] ?? 0);
    $variation_id = (int) ($cart_item['variation_id'] ?? 0);
    $product = $product_id ? wc_get_product($product_id) : false;
    $stock_product = wc_get_product($variation_id ?: $product_id);

    return (
        $product &&
        $stock_product &&
        $product->get_stock_status() === 'instock' &&
        $stock_product->get_stock_status() === 'instock'
    );
}

function toyla_bilibo_swatch_map(int $product_id): array {
    $pastel = [
        'baby-pink' => [
            'label' => 'ბეიბი ვარდისფერი',
            'color' => '#f4b7c3',
        ],
        'beige' => [
            'label' => 'ბეჟი',
            'color' => '#d8cfbd',
        ],
        'white' => [
            'label' => 'თეთრი',
            'color' => '#f7f7f2',
        ],
        'mint' => [
            'label' => 'პიტნისფერი',
            'color' => '#8fe3d0',
        ],
        'ice-blue' => [
            'label' => 'ცისფერი',
            'color' => '#b7def4',
        ],
        'lilac' => [
            'label' => 'იასამნისფერი',
            'color' => '#b8a0df',
        ],
    ];

    if (in_array($product_id, [2757, 2794], true)) {
        return [
            'red' => [
                'label' => 'წითელი',
                'color' => '#d92d2b',
            ],
            'stapilosperi' => [
                'label' => 'სტაფილოსფერი',
                'color' => '#f47b20',
            ],
            'yellow' => [
                'label' => 'ყვითელი',
                'color' => '#f7d84d',
            ],
            'green' => [
                'label' => 'მწვანე',
                'color' => '#35a866',
            ],
            'blue' => [
                'label' => 'ლურჯი',
                'color' => '#2185d0',
            ],
            'purple' => [
                'label' => 'იასამნისფერი',
                'color' => '#8b5fd3',
            ],
        ];
    }

    return $pastel;
}

add_action('woocommerce_before_variations_form', function () {
    if (!toyla_bilibo_is_target_product()) {
        return;
    }

    $product_id = (int) get_the_ID();
    $image_id = get_post_thumbnail_id($product_id);
    $image = $image_id ? wp_get_attachment_image($image_id, 'thumbnail', false, ['class' => 'toyla-bilibo-brand__image']) : '';

    echo '<div class="toyla-bilibo-brand">';
    echo $image;
    echo '<div class="toyla-bilibo-brand__text">';
    echo '<strong>Bilibo</strong>';
    echo '<span>აირჩიე ფერი</span>';
    echo '</div>';
    echo '</div>';

    if (toyla_bilibo_has_set_option($product_id)) {
        $product = wc_get_product($product_id);
        $single_price = $product ? $product->get_variation_price('min', true) : 0;
        $set_price = toyla_bilibo_set_price($product_id);

        echo '<div class="toyla-bilibo-pack">';
        echo '<span class="toyla-bilibo-pack__title">აირჩიე რაოდენობა</span>';
        echo '<div class="toyla-bilibo-pack__options">';
        echo '<button type="button" class="toyla-bilibo-pack__option is-selected" data-pack="single">';
        echo '<strong>1 ცალი</strong><span>' . wp_kses_post(wc_price($single_price)) . '</span>';
        echo '</button>';
        echo '<button type="button" class="toyla-bilibo-pack__option" data-pack="set6">';
        echo '<strong>6-ცალიანი ნაკრები</strong><span>ყველა ფერი · ' . wp_kses_post(wc_price($set_price)) . '</span>';
        echo '</button>';
        echo '</div>';
        echo '<input type="hidden" name="toyla_bilibo_pack" value="single">';
        echo '</div>';
    }

    $bag_image_url = content_url('/uploads/2026/06/Bilibo_Bag.jpg');

    if (!TOYLA_BILIBO_BAG_AVAILABLE) {
        echo '<div class="toyla-bilibo-bag toyla-bilibo-bag--unavailable" aria-disabled="true">';
        echo '<img class="toyla-bilibo-bag__image" src="' . esc_url($bag_image_url) . '" alt="Bilibo ჩანთა">';
        echo '<span class="toyla-bilibo-bag__text">';
        echo '<strong>ჩანთა</strong>';
        echo '<small>დროებით არ არის ხელმისაწვდომი</small>';
        echo '</span>';
        echo '</div>';
        return;
    }

    echo '<label class="toyla-bilibo-bag">';
    echo '<input type="checkbox" name="toyla_bilibo_bag" value="yes">';
    echo '<span class="toyla-bilibo-bag__control" aria-hidden="true"></span>';
    echo '<img class="toyla-bilibo-bag__image" src="' . esc_url($bag_image_url) . '" alt="Bilibo ჩანთა">';
    echo '<span class="toyla-bilibo-bag__text">';
    echo '<strong>დაამატე ჩანთა</strong>';
    echo '<small>1 ცალი ჩანთა · +' . wp_kses_post(wc_price(toyla_bilibo_bag_price())) . '</small>';
    echo '</span>';
    echo '<span class="toyla-bilibo-bag__quantity" hidden>';
    echo '<button type="button" class="toyla-bilibo-bag__qty-btn" data-action="minus" aria-label="ჩანთის რაოდენობის შემცირება">−</button>';
    echo '<output class="toyla-bilibo-bag__qty-value">1</output>';
    echo '<button type="button" class="toyla-bilibo-bag__qty-btn" data-action="plus" aria-label="ჩანთის რაოდენობის გაზრდა">+</button>';
    echo '</span>';
    echo '<input type="hidden" name="toyla_bilibo_bag_qty" value="1">';
    echo '</label>';
});

add_filter('woocommerce_add_cart_item_data', function (array $cart_item_data, int $product_id): array {
    if (!toyla_bilibo_has_set_option($product_id)) {
        return $cart_item_data;
    }

    $pack = isset($_POST['toyla_bilibo_pack'])
        ? sanitize_key(wp_unslash($_POST['toyla_bilibo_pack']))
        : 'single';

    if ($pack === 'set6') {
        $cart_item_data['toyla_bilibo_pack'] = 'set6';
    }

    return $cart_item_data;
}, 10, 2);

add_filter('woocommerce_add_cart_item_data', function (array $cart_item_data, int $product_id): array {
    $bag = isset($_POST['toyla_bilibo_bag'])
        ? sanitize_key(wp_unslash($_POST['toyla_bilibo_bag']))
        : '';

    if (TOYLA_BILIBO_BAG_AVAILABLE && toyla_bilibo_is_cart_target_product($product_id) && $bag === 'yes') {
        $bag_qty = isset($_POST['toyla_bilibo_bag_qty'])
            ? absint(wp_unslash($_POST['toyla_bilibo_bag_qty']))
            : 1;
        $cart_item_data['toyla_bilibo_bag'] = 'yes';
        $cart_item_data['toyla_bilibo_bag_qty'] = max(1, min(20, $bag_qty));
    }

    if (($cart_item_data['toyla_bilibo_pack'] ?? '') === 'set6') {
        $cart_item_data['toyla_original_price'] = toyla_bilibo_set_price($product_id);
    }

    return $cart_item_data;
}, 100, 2);

add_action('woocommerce_add_to_cart', function (): void {
    if (!function_exists('WC') || !WC()->cart) {
        return;
    }

    $groups = [];

    foreach (WC()->cart->cart_contents as $cart_item_key => $cart_item) {
        if (!toyla_bilibo_is_cart_target_product((int) ($cart_item['product_id'] ?? 0))) {
            continue;
        }

        $identity = wp_json_encode([
            'product_id' => (int) ($cart_item['product_id'] ?? 0),
            'variation_id' => (int) ($cart_item['variation_id'] ?? 0),
            'variation' => $cart_item['variation'] ?? [],
            'pack' => $cart_item['toyla_bilibo_pack'] ?? 'single',
        ]);

        $groups[$identity][] = $cart_item_key;
    }

    foreach ($groups as $cart_item_keys) {
        if (count($cart_item_keys) < 2) {
            continue;
        }

        $target_key = array_shift($cart_item_keys);
        $target = WC()->cart->cart_contents[$target_key];
        $total_quantity = max(1, (int) ($target['quantity'] ?? 1));
        $total_bags = ($target['toyla_bilibo_bag'] ?? '') === 'yes'
            ? max(1, (int) ($target['toyla_bilibo_bag_qty'] ?? 1))
            : 0;

        foreach ($cart_item_keys as $duplicate_key) {
            $duplicate = WC()->cart->cart_contents[$duplicate_key];
            $total_quantity += max(1, (int) ($duplicate['quantity'] ?? 1));

            if (($duplicate['toyla_bilibo_bag'] ?? '') === 'yes') {
                $total_bags += max(1, (int) ($duplicate['toyla_bilibo_bag_qty'] ?? 1));
            }

            unset(WC()->cart->cart_contents[$duplicate_key]);
        }

        WC()->cart->cart_contents[$target_key]['quantity'] = $total_quantity;

        if ($total_bags > 0) {
            WC()->cart->cart_contents[$target_key]['toyla_bilibo_bag'] = 'yes';
            WC()->cart->cart_contents[$target_key]['toyla_bilibo_bag_qty'] = min(20, $total_bags);
        } else {
            unset(
                WC()->cart->cart_contents[$target_key]['toyla_bilibo_bag'],
                WC()->cart->cart_contents[$target_key]['toyla_bilibo_bag_qty']
            );
        }
    }

    WC()->cart->set_session();
}, 100);

add_action('woocommerce_before_calculate_totals', function ($cart): void {
    if (is_admin() && !wp_doing_ajax()) {
        return;
    }

    foreach ($cart->get_cart() as $cart_item) {
        $product_id = (int) ($cart_item['product_id'] ?? 0);

        if (!toyla_bilibo_cart_item_is_in_stock($cart_item)) {
            if (
                isset($cart_item['data']) &&
                is_a($cart_item['data'], 'WC_Product')
            ) {
                $cart_item['data']->set_price(0);
            }
            continue;
        }

        $quantity = max(1, (int) ($cart_item['quantity'] ?? 1));
        $bag_qty = ($cart_item['toyla_bilibo_bag'] ?? '') === 'yes'
            ? max(1, (int) ($cart_item['toyla_bilibo_bag_qty'] ?? 1))
            : 0;
        $bag_total = toyla_bilibo_bag_price() * $bag_qty;
        $base_price = 0.0;

        if (
            ($cart_item['toyla_bilibo_pack'] ?? '') === 'set6' &&
            isset($cart_item['data']) &&
            is_a($cart_item['data'], 'WC_Product')
        ) {
            $base_price = toyla_bilibo_set_price($product_id);
        } elseif (
            isset($cart_item['data']) &&
            is_a($cart_item['data'], 'WC_Product')
        ) {
            $variation_id = (int) ($cart_item['variation_id'] ?? 0);
            $fresh_product = wc_get_product($variation_id ?: $product_id);
            if ($fresh_product) {
                $base_price = (float) $fresh_product->get_price();
            }
        }

        if (
            $base_price > 0 &&
            isset($cart_item['data']) &&
            is_a($cart_item['data'], 'WC_Product')
        ) {
            $cart_item['data']->set_price($base_price + ($bag_total / $quantity));
        }
    }
});

add_filter('woocommerce_get_item_data', function (array $item_data, array $cart_item): array {
    if (($cart_item['toyla_bilibo_pack'] ?? '') === 'set6') {
        $item_data = array_values(array_filter($item_data, function (array $item): bool {
            $key = isset($item['key']) ? wp_strip_all_tags((string) $item['key']) : '';
            return $key !== 'ფერი';
        }));

        $item_data[] = [
            'key' => 'შეფუთვა',
            'value' => '6-ცალიანი ნაკრები (ყველა ფერი)',
        ];
    }

    if (($cart_item['toyla_bilibo_bag'] ?? '') === 'yes') {
        $bag_qty = max(1, (int) ($cart_item['toyla_bilibo_bag_qty'] ?? 1));
        $item_data[] = [
            'key' => 'ჩანთა',
            'value' => $bag_qty . ' ცალი (+' . wc_price(toyla_bilibo_bag_price() * $bag_qty) . ')',
            'display' => $bag_qty . ' ცალი (+' . wp_strip_all_tags(wc_price(toyla_bilibo_bag_price() * $bag_qty)) . ')',
        ];
    }

    return $item_data;
}, 20, 2);

add_action('woocommerce_after_cart_item_name', function (array $cart_item, string $cart_item_key): void {
    if (($cart_item['toyla_bilibo_bag'] ?? '') !== 'yes') {
        return;
    }

    $bag_qty = max(1, (int) ($cart_item['toyla_bilibo_bag_qty'] ?? 1));

    echo '<div class="toyla-cart-bag-quantity" data-cart-key="' . esc_attr($cart_item_key) . '">';
    echo '<span>ჩანთის რაოდენობა</span>';
    echo '<div class="toyla-cart-bag-quantity__control">';
    echo '<button type="button" data-action="minus" aria-label="ჩანთის რაოდენობის შემცირება">−</button>';
    echo '<output>' . esc_html((string) $bag_qty) . '</output>';
    echo '<button type="button" data-action="plus" aria-label="ჩანთის რაოდენობის გაზრდა">+</button>';
    echo '</div>';
    echo '</div>';
}, 20, 2);

function toyla_bilibo_ajax_update_bag_quantity(): void {
    check_ajax_referer('toyla_bilibo_update_bag', 'nonce');

    if (!TOYLA_BILIBO_BAG_AVAILABLE) {
        wp_send_json_error(['message' => 'ჩანთა დროებით არ არის ხელმისაწვდომი.']);
    }

    if (!function_exists('WC') || !WC()->cart) {
        wp_send_json_error(['message' => 'კალათა ვერ მოიძებნა.']);
    }

    $cart_item_key = isset($_POST['cart_item_key'])
        ? wc_clean(wp_unslash($_POST['cart_item_key']))
        : '';
    $bag_qty = isset($_POST['bag_qty']) ? (int) wp_unslash($_POST['bag_qty']) : 0;
    $cart = WC()->cart->get_cart();

    if (!$cart_item_key || !isset($cart[$cart_item_key])) {
        wp_send_json_error(['message' => 'პროდუქტი კალათაში ვერ მოიძებნა.']);
    }

    if ($bag_qty <= 0) {
        unset(
            WC()->cart->cart_contents[$cart_item_key]['toyla_bilibo_bag'],
            WC()->cart->cart_contents[$cart_item_key]['toyla_bilibo_bag_qty']
        );
    } else {
        WC()->cart->cart_contents[$cart_item_key]['toyla_bilibo_bag'] = 'yes';
        WC()->cart->cart_contents[$cart_item_key]['toyla_bilibo_bag_qty'] = min(20, $bag_qty);
    }

    WC()->cart->set_session();
    WC()->cart->calculate_totals();

    wp_send_json_success([
        'bag_qty' => max(0, min(20, $bag_qty)),
    ]);
}

add_action('wp_ajax_toyla_bilibo_update_bag_qty', 'toyla_bilibo_ajax_update_bag_quantity');
add_action('wp_ajax_nopriv_toyla_bilibo_update_bag_qty', 'toyla_bilibo_ajax_update_bag_quantity');

function toyla_bilibo_mini_cart_total(): void {
    if (!function_exists('WC') || !WC()->cart) {
        return;
    }

    $total = 0.0;

    foreach (WC()->cart->get_cart() as $cart_item) {
        if (!toyla_bilibo_cart_item_is_in_stock($cart_item)) {
            continue;
        }

        $product_id = (int) ($cart_item['product_id'] ?? 0);
        $variation_id = (int) ($cart_item['variation_id'] ?? 0);
        $stock_product = wc_get_product($variation_id ?: $product_id);
        $quantity = max(1, (int) ($cart_item['quantity'] ?? 1));
        $bag_qty = ($cart_item['toyla_bilibo_bag'] ?? '') === 'yes'
            ? max(1, (int) ($cart_item['toyla_bilibo_bag_qty'] ?? 1))
            : 0;

        if (($cart_item['toyla_bilibo_pack'] ?? '') === 'set6') {
            $unit_price = toyla_bilibo_set_price($product_id);
        } else {
            $unit_price = (float) $stock_product->get_price();
        }

        $total += ($unit_price * $quantity) + (toyla_bilibo_bag_price() * $bag_qty);
    }

    echo '<strong>სულ:</strong> ';
    echo wp_kses_post(wc_price($total));
}

add_action('wp_loaded', function (): void {
    remove_action(
        'woocommerce_widget_shopping_cart_total',
        'woocommerce_widget_shopping_cart_subtotal',
        10
    );
    add_action(
        'woocommerce_widget_shopping_cart_total',
        'toyla_bilibo_mini_cart_total',
        10
    );
}, 100);

add_action('wp_footer', function (): void {
    if (!function_exists('is_cart') || !is_cart()) {
        return;
    }
    ?>
    <style>
        .toyla-cart-bag-quantity {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 8px;
            color: #5f5a54;
            font-size: 13px;
        }

        .toyla-cart-bag-quantity__control {
            display: grid;
            grid-template-columns: 28px 28px 28px;
            align-items: center;
            border: 1px solid #d8d2c8;
            border-radius: 6px;
            background: #fff;
            overflow: hidden;
        }

        .toyla-cart-bag-quantity__control button {
            width: 28px;
            height: 30px;
            padding: 0;
            border: 0;
            border-radius: 0;
            background: #fff;
            color: #222;
            font-size: 17px;
            cursor: pointer;
        }

        .toyla-cart-bag-quantity__control button:hover {
            background: #f2eee8;
        }

        .toyla-cart-bag-quantity__control output {
            text-align: center;
            color: #222;
            font-size: 13px;
        }

        .toyla-cart-bag-quantity.is-loading {
            opacity: .55;
            pointer-events: none;
        }
    </style>
    <script>
        (function () {
            var ajaxUrl = <?php echo wp_json_encode(admin_url('admin-ajax.php')); ?>;
            var nonce = <?php echo wp_json_encode(wp_create_nonce('toyla_bilibo_update_bag')); ?>;

            document.addEventListener('click', function (event) {
                var button = event.target.closest('.toyla-cart-bag-quantity button[data-action]');
                if (!button) {
                    return;
                }

                var wrap = button.closest('.toyla-cart-bag-quantity');
                var output = wrap ? wrap.querySelector('output') : null;
                var cartItemKey = wrap ? wrap.dataset.cartKey : '';
                if (!wrap || !output || !cartItemKey || wrap.classList.contains('is-loading')) {
                    return;
                }

                var current = parseInt(output.textContent, 10) || 1;
                var next = button.dataset.action === 'plus'
                    ? Math.min(20, current + 1)
                    : Math.max(0, current - 1);
                var body = new URLSearchParams();

                body.set('action', 'toyla_bilibo_update_bag_qty');
                body.set('nonce', nonce);
                body.set('cart_item_key', cartItemKey);
                body.set('bag_qty', String(next));
                wrap.classList.add('is-loading');

                fetch(ajaxUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
                    body: body.toString()
                })
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (result) {
                        if (!result || !result.success) {
                            throw new Error('Bag quantity update failed');
                        }

                        window.location.reload();
                    })
                    .catch(function () {
                        wrap.classList.remove('is-loading');
                    });
            });
        }());
    </script>
    <?php
});

add_filter('woocommerce_cart_item_name', function (string $name, array $cart_item): string {
    if (($cart_item['toyla_bilibo_pack'] ?? '') !== 'set6') {
        return $name;
    }

    $parent = wc_get_product((int) ($cart_item['product_id'] ?? 0));
    return $parent ? $parent->get_name() . ' — 6-ცალიანი ნაკრები' : $name;
}, 20, 2);

add_filter('woocommerce_cart_item_price', function (string $price_html, array $cart_item): string {
    $product_id = (int) ($cart_item['product_id'] ?? 0);

    if (($cart_item['toyla_bilibo_pack'] ?? '') === 'set6') {
        return wc_price(toyla_bilibo_set_price($product_id));
    }

    if (($cart_item['toyla_bilibo_bag'] ?? '') === 'yes') {
        $variation_id = (int) ($cart_item['variation_id'] ?? 0);
        $fresh_product = wc_get_product($variation_id ?: $product_id);
        if ($fresh_product) {
            return wc_price((float) $fresh_product->get_price());
        }
    }

    return $price_html;
}, 100, 2);

add_filter('woocommerce_cart_item_subtotal', function (string $subtotal, array $cart_item): string {
    if (
        ($cart_item['toyla_bilibo_bag'] ?? '') !== 'yes' ||
        !empty($cart_item['toyla_preorder_stock'])
    ) {
        return $subtotal;
    }

    $product_id = (int) ($cart_item['product_id'] ?? 0);
    $quantity = max(1, (int) ($cart_item['quantity'] ?? 1));
    $bag_qty = max(1, (int) ($cart_item['toyla_bilibo_bag_qty'] ?? 1));
    $base_price = 0.0;

    if (($cart_item['toyla_bilibo_pack'] ?? '') === 'set6') {
        $base_price = toyla_bilibo_set_price($product_id);
    } else {
        $variation_id = (int) ($cart_item['variation_id'] ?? 0);
        $fresh_product = wc_get_product($variation_id ?: $product_id);
        if ($fresh_product) {
            $base_price = (float) $fresh_product->get_price();
        }
    }

    return $base_price > 0
        ? wc_price(($base_price * $quantity) + (toyla_bilibo_bag_price() * $bag_qty))
        : $subtotal;
}, 100, 2);

add_filter('woocommerce_cart_item_thumbnail', function (string $thumbnail, array $cart_item): string {
    if (($cart_item['toyla_bilibo_pack'] ?? '') !== 'set6') {
        return $thumbnail;
    }

    $parent = wc_get_product((int) ($cart_item['product_id'] ?? 0));
    return $parent ? $parent->get_image('woocommerce_thumbnail') : $thumbnail;
}, 20, 2);

add_filter('woocommerce_cart_item_permalink', function ($permalink, array $cart_item) {
    if (($cart_item['toyla_bilibo_pack'] ?? '') !== 'set6') {
        return $permalink;
    }

    $parent = wc_get_product((int) ($cart_item['product_id'] ?? 0));
    return $parent ? $parent->get_permalink() : $permalink;
}, 20, 2);

add_action('woocommerce_checkout_create_order_line_item', function ($item, $cart_item_key, $values): void {
    if (($values['toyla_bilibo_pack'] ?? '') === 'set6') {
        $parent = wc_get_product((int) ($values['product_id'] ?? 0));
        if ($parent) {
            $item->set_name($parent->get_name() . ' — 6-ცალიანი ნაკრები');
        }

        $item->delete_meta_data('pa_feri');
        $item->delete_meta_data('ფერი');
        $item->add_meta_data('შეფუთვა', '6-ცალიანი ნაკრები (ყველა ფერი)', true);
    }

    if (($values['toyla_bilibo_bag'] ?? '') === 'yes') {
        $bag_qty = max(1, (int) ($values['toyla_bilibo_bag_qty'] ?? 1));
        $item->add_meta_data(
            'ჩანთა',
            $bag_qty . ' ცალი (+' . wp_strip_all_tags(wc_price(toyla_bilibo_bag_price() * $bag_qty)) . ')',
            true
        );
    }
}, 20, 3);

add_action('wp_footer', function () {
    if (!toyla_bilibo_is_target_product()) {
        return;
    }

    $product_id = (int) get_the_ID();
    $swatches = toyla_bilibo_swatch_map($product_id);
    $set_image = [];
    $set_image_id = get_post_thumbnail_id($product_id);

    if ($set_image_id) {
        $set_image_src = wp_get_attachment_image_src($set_image_id, 'woocommerce_single');
        $set_image_full = wp_get_attachment_image_src($set_image_id, 'full');

        if ($set_image_src) {
            $set_image = [
                'src' => $set_image_src[0],
                'srcset' => wp_get_attachment_image_srcset($set_image_id, 'woocommerce_single') ?: '',
                'sizes' => wp_get_attachment_image_sizes($set_image_id, 'woocommerce_single') ?: '',
                'full_src' => $set_image_full ? $set_image_full[0] : $set_image_src[0],
                'full_src_w' => $set_image_full ? $set_image_full[1] : $set_image_src[1],
                'full_src_h' => $set_image_full ? $set_image_full[2] : $set_image_src[2],
                'alt' => get_post_meta($set_image_id, '_wp_attachment_image_alt', true),
                'gallery_thumbnail_src' => wp_get_attachment_image_url($set_image_id, 'woocommerce_gallery_thumbnail') ?: '',
            ];
        }
    }
    ?>
    <style>
        .toyla-bilibo-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0 0 16px;
            padding: 12px;
            border: 1px solid #e7e1d8;
            background: #fff;
            border-radius: 8px;
        }

        .toyla-bilibo-brand__image {
            width: 58px;
            height: 58px;
            border-radius: 8px;
            object-fit: cover;
            background: #f7f4ef;
        }

        .toyla-bilibo-brand__text {
            display: grid;
            gap: 2px;
            color: #252525;
        }

        .toyla-bilibo-brand__text strong {
            font-size: 18px;
            line-height: 1.2;
            letter-spacing: 0;
        }

        .toyla-bilibo-brand__text span {
            font-size: 14px;
            color: #6d665d;
        }

        .toyla-bilibo-pack {
            margin: 0 0 18px;
        }

        .toyla-bilibo-pack__title {
            display: block;
            margin: 0 0 8px;
            color: #252525;
            font-size: 16px;
            font-weight: 600;
        }

        .toyla-bilibo-pack__options {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .toyla-bilibo-pack__option {
            display: grid;
            gap: 3px;
            min-height: 64px;
            padding: 10px 12px;
            border: 1px solid #d8d2c8;
            border-radius: 8px;
            background: #fff;
            color: #222;
            text-align: left;
            cursor: pointer;
        }

        .toyla-bilibo-pack__option:hover,
        .toyla-bilibo-pack__option.is-selected {
            border-color: #222;
            box-shadow: 0 0 0 2px rgba(34, 34, 34, .08);
        }

        .toyla-bilibo-pack__option strong {
            font-size: 15px;
            letter-spacing: 0;
        }

        .toyla-bilibo-pack__option span {
            color: #6d665d;
            font-size: 13px;
        }

        .toyla-bilibo-pack__option.is-selected {
            background: #f8f5f0;
        }

        .toyla-bilibo-bag {
            display: flex;
            align-items: center;
            gap: 11px;
            min-height: 58px;
            margin: 0 0 18px;
            padding: 10px 12px;
            border: 1px solid #d8d2c8;
            border-radius: 8px;
            background: #fff;
            color: #222;
            cursor: pointer;
        }

        .toyla-bilibo-bag input {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
        }

        .toyla-bilibo-bag--unavailable {
            cursor: not-allowed;
            background: #f5f3ef;
            opacity: .65;
        }

        .toyla-bilibo-bag--unavailable .toyla-bilibo-bag__image {
            filter: grayscale(1);
        }

        .toyla-bilibo-bag--unavailable .toyla-bilibo-bag__text small {
            color: #a33;
        }

        .toyla-bilibo-bag__control {
            position: relative;
            width: 22px;
            height: 22px;
            flex: 0 0 22px;
            border: 1px solid #aaa39a;
            border-radius: 4px;
            background: #fff;
        }

        .toyla-bilibo-bag__image {
            width: 52px;
            height: 52px;
            flex: 0 0 52px;
            border-radius: 6px;
            object-fit: contain;
            background: #fff;
        }

        .toyla-bilibo-bag input:checked + .toyla-bilibo-bag__control {
            border-color: #222;
            background: #222;
        }

        .toyla-bilibo-bag input:checked + .toyla-bilibo-bag__control::after {
            content: "";
            position: absolute;
            left: 7px;
            top: 3px;
            width: 5px;
            height: 10px;
            border: solid #fff;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .toyla-bilibo-bag:has(input:checked) {
            border-color: #222;
            background: #f8f5f0;
            box-shadow: 0 0 0 2px rgba(34, 34, 34, .08);
        }

        .toyla-bilibo-bag__text {
            display: grid;
            gap: 2px;
            min-width: 0;
        }

        .toyla-bilibo-bag__text strong {
            font-size: 15px;
            letter-spacing: 0;
        }

        .toyla-bilibo-bag__text small {
            color: #6d665d;
            font-size: 13px;
        }

        .toyla-bilibo-bag__quantity {
            display: grid;
            grid-template-columns: 30px 28px 30px;
            align-items: center;
            margin-left: auto;
            border: 1px solid #d8d2c8;
            border-radius: 6px;
            background: #fff;
            overflow: hidden;
        }

        .toyla-bilibo-bag__quantity[hidden] {
            display: none;
        }

        .toyla-bilibo-bag__qty-btn {
            width: 30px;
            height: 32px;
            padding: 0;
            border: 0;
            border-radius: 0;
            background: #fff;
            color: #222;
            font-size: 18px;
            line-height: 1;
            cursor: pointer;
        }

        .toyla-bilibo-bag__qty-btn:hover {
            background: #f2eee8;
        }

        .toyla-bilibo-bag__qty-value {
            min-width: 28px;
            color: #222;
            font-size: 14px;
            text-align: center;
        }

        .toyla-bilibo-set-active .toyla-bilibo-swatches {
            display: none;
        }

        .toyla-bilibo-swatches {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin: 8px 0 12px;
        }

        .toyla-bilibo-swatch {
            display: flex;
            align-items: center;
            gap: 8px;
            min-height: 44px;
            padding: 8px 10px;
            border: 1px solid #d8d2c8;
            border-radius: 8px;
            background: #fff;
            color: #222;
            font-size: 14px;
            line-height: 1.2;
            cursor: pointer;
            transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
        }

        .toyla-bilibo-swatch:hover,
        .toyla-bilibo-swatch.is-selected {
            border-color: #222;
            box-shadow: 0 0 0 2px rgba(34, 34, 34, .08);
        }

        .toyla-bilibo-swatch:active {
            transform: translateY(1px);
        }

        .toyla-bilibo-swatch__dot {
            width: 24px;
            height: 24px;
            flex: 0 0 24px;
            border-radius: 50%;
            border: 1px solid rgba(0, 0, 0, .18);
            box-shadow: inset 0 0 0 2px rgba(255, 255, 255, .65);
        }

        .toyla-bilibo-swatch__label {
            overflow-wrap: anywhere;
        }

        .toyla-bilibo-select-hidden select[name="attribute_pa_feri"] {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }

        .toyla-bilibo-native-hidden {
            display: none !important;
        }

        body.postid-2757 .woocommerce-product-gallery__image,
        body.postid-2757 .woocommerce-product-gallery__image a,
        body.postid-2769 .woocommerce-product-gallery__image,
        body.postid-2769 .woocommerce-product-gallery__image a,
        body.postid-2794 .woocommerce-product-gallery__image,
        body.postid-2794 .woocommerce-product-gallery__image a {
            background: #fff;
        }

        body.postid-2757 .woocommerce-product-gallery__image img,
        body.postid-2769 .woocommerce-product-gallery__image img,
        body.postid-2794 .woocommerce-product-gallery__image img {
            width: 100%;
            max-height: 560px;
            object-fit: contain;
            object-position: center;
        }

        body.postid-2757 .woocommerce-product-gallery__wrapper,
        body.postid-2769 .woocommerce-product-gallery__wrapper,
        body.postid-2794 .woocommerce-product-gallery__wrapper {
            background: #fff;
        }

        @media (max-width: 480px) {
            .toyla-bilibo-swatches {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>
    <script>
        (function () {
            var swatches = <?php echo wp_json_encode($swatches, JSON_UNESCAPED_UNICODE); ?>;
            var setImage = <?php echo wp_json_encode($set_image, JSON_UNESCAPED_UNICODE); ?>;
            var initialGalleryImage = null;

            function triggerChange(element) {
                element.dispatchEvent(new Event('change', { bubbles: true }));
                if (window.jQuery) {
                    window.jQuery(element).trigger('change');
                }
            }

            function buildSwatches() {
                var select = document.querySelector('select[name="attribute_pa_feri"]');
                if (!select || document.querySelector('.toyla-bilibo-swatches')) {
                    return;
                }

                var row = select.closest('tr') || select.parentElement;
                if (row) {
                    row.classList.add('toyla-bilibo-select-hidden');
                }

                var wrap = document.createElement('div');
                wrap.className = 'toyla-bilibo-swatches';

                Array.prototype.forEach.call(select.options, function (option) {
                    var slug = option.value;
                    if (!slug || !swatches[slug]) {
                        return;
                    }

                    var button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'toyla-bilibo-swatch';
                    button.dataset.value = slug;
                    button.setAttribute('aria-label', swatches[slug].label);

                    var dot = document.createElement('span');
                    dot.className = 'toyla-bilibo-swatch__dot';
                    dot.style.backgroundColor = swatches[slug].color;

                    var label = document.createElement('span');
                    label.className = 'toyla-bilibo-swatch__label';
                    label.textContent = swatches[slug].label;

                    button.appendChild(dot);
                    button.appendChild(label);
                    wrap.appendChild(button);
                });

                select.parentElement.insertBefore(wrap, select);

                Array.prototype.forEach.call(select.parentElement.children, function (child) {
                    if (child !== wrap && child !== select) {
                        child.classList.add('toyla-bilibo-native-hidden');
                    }
                });

                wrap.addEventListener('click', function (event) {
                    var button = event.target.closest('.toyla-bilibo-swatch');
                    if (!button) {
                        return;
                    }

                    select.value = button.dataset.value;
                    triggerChange(select);
                    updateSelected();
                    window.setTimeout(updateImageFromSelection, 80);
                });

                function updateSelected() {
                    Array.prototype.forEach.call(wrap.querySelectorAll('.toyla-bilibo-swatch'), function (button) {
                        button.classList.toggle('is-selected', button.dataset.value === select.value);
                    });
                }

                select.addEventListener('change', function () {
                    updateSelected();
                    window.setTimeout(updateImageFromSelection, 80);
                });
                updateSelected();
            }

            function setupPackOption() {
                var pack = document.querySelector('.toyla-bilibo-pack');
                var input = document.querySelector('input[name="toyla_bilibo_pack"]');
                var select = document.querySelector('select[name="attribute_pa_feri"]');
                var form = document.querySelector('form.variations_form');
                if (!pack || !input || !select || !form) {
                    return;
                }

                captureInitialGalleryImage();

                pack.addEventListener('click', function (event) {
                    var button = event.target.closest('.toyla-bilibo-pack__option');
                    if (!button) {
                        return;
                    }

                    var mode = button.dataset.pack === 'set6' ? 'set6' : 'single';
                    input.value = mode;
                    form.classList.toggle('toyla-bilibo-set-active', mode === 'set6');

                    Array.prototype.forEach.call(pack.querySelectorAll('.toyla-bilibo-pack__option'), function (option) {
                        option.classList.toggle('is-selected', option === button);
                    });

                    if (mode === 'set6') {
                        var firstColor = Array.prototype.find.call(select.options, function (option) {
                            return option.value && !option.disabled;
                        });
                        if (firstColor && select.value !== firstColor.value) {
                            select.value = firstColor.value;
                            triggerChange(select);
                        }
                    }

                    if (mode === 'set6') {
                        window.setTimeout(showSetImage, 120);
                        window.setTimeout(showSetImage, 350);
                        window.setTimeout(goToFirstGallerySlide, 160);
                        window.setTimeout(goToFirstGallerySlide, 420);
                    } else if (select.value) {
                        window.setTimeout(updateImageFromSelection, 80);
                    }
                });
            }

            function setupBagOption() {
                var bag = document.querySelector('.toyla-bilibo-bag');
                var checkbox = bag ? bag.querySelector('input[name="toyla_bilibo_bag"]') : null;
                var quantity = bag ? bag.querySelector('.toyla-bilibo-bag__quantity') : null;
                var quantityInput = bag ? bag.querySelector('input[name="toyla_bilibo_bag_qty"]') : null;
                var quantityValue = bag ? bag.querySelector('.toyla-bilibo-bag__qty-value') : null;
                if (!bag || !checkbox || !quantity || !quantityInput || !quantityValue) {
                    return;
                }

                function render() {
                    quantity.hidden = !checkbox.checked;
                    quantityValue.textContent = quantityInput.value;
                }

                checkbox.addEventListener('change', render);

                quantity.addEventListener('click', function (event) {
                    var button = event.target.closest('.toyla-bilibo-bag__qty-btn');
                    if (!button) {
                        return;
                    }

                    event.preventDefault();
                    event.stopPropagation();

                    var current = parseInt(quantityInput.value, 10) || 1;
                    if (button.dataset.action === 'plus') {
                        current = Math.min(20, current + 1);
                    } else {
                        current = Math.max(1, current - 1);
                    }

                    quantityInput.value = String(current);
                    render();
                });

                render();
            }

            function goToFirstGallerySlide() {
                var form = document.querySelector('form.variations_form');
                if (!form || !form.classList.contains('toyla-bilibo-set-active')) {
                    return;
                }

                var firstPagination = document.querySelector('.splide__pagination__page[aria-label="Go to slide 1"]');
                if (firstPagination) {
                    firstPagination.click();
                }

                var firstThumbnail = document.querySelector('.bt-woo-gallery-thumbnail');
                if (firstThumbnail && !firstThumbnail.classList.contains('is-active')) {
                    firstThumbnail.click();
                }
            }

            function captureInitialGalleryImage() {
                var image = document.querySelector('.woocommerce-product-gallery__image img, .woocommerce-product-gallery img');
                if (!image || initialGalleryImage) {
                    return;
                }

                initialGalleryImage = {
                    src: image.getAttribute('src') || '',
                    srcset: image.getAttribute('srcset') || '',
                    sizes: image.getAttribute('sizes') || '',
                    full_src: image.getAttribute('data-large_image') || (
                        image.parentElement && image.parentElement.tagName === 'A'
                            ? image.parentElement.getAttribute('href') || ''
                            : ''
                    ),
                    full_src_w: image.getAttribute('data-large_image_width') || '',
                    full_src_h: image.getAttribute('data-large_image_height') || '',
                    alt: image.getAttribute('alt') || '',
                    gallery_thumbnail_src: image.getAttribute('data-thumb') || ''
                };
            }

            function showSetImage() {
                var form = document.querySelector('form.variations_form');
                var image = document.querySelector('.woocommerce-product-gallery__image img, .woocommerce-product-gallery img');
                var targetImage = initialGalleryImage && initialGalleryImage.src ? initialGalleryImage : setImage;
                if (!form || !form.classList.contains('toyla-bilibo-set-active') || !image || !targetImage.src) {
                    return;
                }

                image.setAttribute('src', targetImage.src);

                if (targetImage.srcset) {
                    image.setAttribute('srcset', targetImage.srcset);
                } else {
                    image.removeAttribute('srcset');
                }
                if (targetImage.sizes) {
                    image.setAttribute('sizes', targetImage.sizes);
                }
                if (targetImage.alt) {
                    image.setAttribute('alt', targetImage.alt);
                }
                if (targetImage.full_src) {
                    image.setAttribute('data-large_image', targetImage.full_src);
                    if (image.parentElement && image.parentElement.tagName === 'A') {
                        image.parentElement.setAttribute('href', targetImage.full_src);
                    }
                }
                if (targetImage.full_src_w) {
                    image.setAttribute('data-large_image_width', targetImage.full_src_w);
                }
                if (targetImage.full_src_h) {
                    image.setAttribute('data-large_image_height', targetImage.full_src_h);
                }
                if (targetImage.gallery_thumbnail_src) {
                    image.setAttribute('data-thumb', targetImage.gallery_thumbnail_src);
                }

                goToFirstGallerySlide();
            }

            function updateImageFromSelection() {
                var form = document.querySelector('form.variations_form');
                var select = document.querySelector('select[name="attribute_pa_feri"]');
                var image = document.querySelector('.woocommerce-product-gallery__image img, .woocommerce-product-gallery img');
                if (!form || !select || !select.value || !image) {
                    return;
                }

                if (form.classList.contains('toyla-bilibo-set-active')) {
                    showSetImage();
                    return;
                }

                var variations = [];
                try {
                    variations = JSON.parse(form.getAttribute('data-product_variations') || '[]');
                } catch (error) {
                    return;
                }

                var selected = variations.find(function (variation) {
                    return variation &&
                        variation.attributes &&
                        variation.attributes.attribute_pa_feri === select.value &&
                        variation.image &&
                        variation.image.src;
                });

                if (!selected) {
                    return;
                }

                var variationImage = selected.image;
                image.setAttribute('src', variationImage.src);

                if (variationImage.srcset) {
                    image.setAttribute('srcset', variationImage.srcset);
                }
                if (variationImage.sizes) {
                    image.setAttribute('sizes', variationImage.sizes);
                }
                if (variationImage.alt) {
                    image.setAttribute('alt', variationImage.alt);
                }
                if (variationImage.full_src) {
                    image.setAttribute('data-large_image', variationImage.full_src);
                    if (image.parentElement && image.parentElement.tagName === 'A') {
                        image.parentElement.setAttribute('href', variationImage.full_src);
                    }
                }
                if (variationImage.full_src_w) {
                    image.setAttribute('data-large_image_width', variationImage.full_src_w);
                }
                if (variationImage.full_src_h) {
                    image.setAttribute('data-large_image_height', variationImage.full_src_h);
                }
                if (variationImage.gallery_thumbnail_src) {
                    image.setAttribute('data-thumb', variationImage.gallery_thumbnail_src);
                }
            }

            function preserveClassicGallery() {
                if (
                    (!document.body.classList.contains('postid-2757') && !document.body.classList.contains('postid-2769') && !document.body.classList.contains('postid-2794')) ||
                    !window.jQuery
                ) {
                    return;
                }

                var $ = window.jQuery;
                var originalThumbs = [];

                function captureThumbs() {
                    var $thumbs = $('.flex-control-thumbs li');
                    if (!$thumbs.length || originalThumbs.length) {
                        return;
                    }

                    $thumbs.each(function () {
                        originalThumbs.push($(this).clone(true, true));
                    });
                }

                function restoreMissingThumbs() {
                    if (!originalThumbs.length) {
                        return;
                    }

                    var $list = $('.flex-control-thumbs');
                    if (!$list.length) {
                        return;
                    }

                    var existing = {};
                    $list.find('img').each(function () {
                        var src = $(this).attr('src') || $(this).attr('data-src') || '';
                        if (src) {
                            existing[src.replace(/-\d+x\d+(?=\.[a-z]+$)/i, '')] = true;
                        }
                    });

                    originalThumbs.forEach(function ($thumb) {
                        var src = $thumb.find('img').attr('src') || $thumb.find('img').attr('data-src') || '';
                        var key = src.replace(/-\d+x\d+(?=\.[a-z]+$)/i, '');
                        if (key && !existing[key]) {
                            $list.append($thumb.clone(true, true));
                            existing[key] = true;
                        }
                    });
                }

                captureThumbs();
                $('.variations_form').on('found_variation reset_data', function () {
                    window.setTimeout(restoreMissingThumbs, 120);
                });
            }

            function syncVariationImage() {
                if (!window.jQuery) {
                    return;
                }

                var $ = window.jQuery;
                var originalImage = null;

                function getMainImage() {
                    return $('.woocommerce-product-gallery__image img').first();
                }

                function captureOriginalImage() {
                    var $img = getMainImage();
                    if (!$img.length || originalImage) {
                        return;
                    }

                    originalImage = {
                        src: $img.attr('src') || '',
                        srcset: $img.attr('srcset') || '',
                        sizes: $img.attr('sizes') || '',
                        large: $img.attr('data-large_image') || '',
                        largeWidth: $img.attr('data-large_image_width') || '',
                        largeHeight: $img.attr('data-large_image_height') || '',
                        alt: $img.attr('alt') || ''
                    };
                }

                function applyImage(image) {
                    var $img = getMainImage();
                    if (!$img.length || !image || !image.src) {
                        return;
                    }

                    $img.attr('src', image.src);
                    if (image.srcset) {
                        $img.attr('srcset', image.srcset);
                    }
                    if (image.sizes) {
                        $img.attr('sizes', image.sizes);
                    }
                    if (image.full_src) {
                        $img.attr('data-large_image', image.full_src);
                        $img.closest('a').attr('href', image.full_src);
                    }
                    if (image.full_src_w) {
                        $img.attr('data-large_image_width', image.full_src_w);
                    }
                    if (image.full_src_h) {
                        $img.attr('data-large_image_height', image.full_src_h);
                    }
                    if (image.alt) {
                        $img.attr('alt', image.alt);
                    }
                }

                captureOriginalImage();

                $('.variations_form').on('found_variation', function (event, variation) {
                    if ($(this).hasClass('toyla-bilibo-set-active')) {
                        window.setTimeout(showSetImage, 20);
                        return;
                    }

                    if (variation && variation.image) {
                        applyImage(variation.image);
                    }
                });

                $('.variations_form').on('reset_data', function () {
                    if (originalImage) {
                        applyImage({
                            src: originalImage.src,
                            srcset: originalImage.srcset,
                            sizes: originalImage.sizes,
                            full_src: originalImage.large,
                            full_src_w: originalImage.largeWidth,
                            full_src_h: originalImage.largeHeight,
                            alt: originalImage.alt
                        });
                    }
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function () {
                    buildSwatches();
                    setupPackOption();
                    setupBagOption();
                    preserveClassicGallery();
                    syncVariationImage();
                });
            } else {
                buildSwatches();
                setupPackOption();
                setupBagOption();
                preserveClassicGallery();
                syncVariationImage();
            }
        }());
    </script>
    <?php
});
