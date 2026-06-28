# Toyla Homepage Brand Buttons - 2026-06-28

## Final Live State

Homepage hero banner buttons were updated on live after testing first on `toyla-test.local`.

| Banner | Button Text | Destination |
|---|---|---|
| Large left hero banner | `Stapelstein` | `https://toyla.ge/shop/?filtering=1&filter_product_brand=138` |
| Top-right hero banner | `Clixo` | `https://toyla.ge/shop/?filtering=1&filter_product_brand=134` |
| Bottom-right hero banner | `MODU` | `https://toyla.ge/shop/?filtering=1&filter_product_brand=137` |

The `ყველა პროდუქტი` button remains unchanged:

```text
https://toyla.ge/shop/
```

## Test Site

Test homepage Elementor button widget IDs:

```text
8c54882 -> Stapelstein
7c595b3 -> Clixo
d8900b4 -> MODU
```

Test backup:

```text
C:\Users\JEKO\toyla-test-homepage-elementor-before-brand-only-buttons-20260628-123416.json
```

## Live Deployment

Live update was applied through cPanel Terminal using a server-side PHP script that:

- loaded WordPress through `/home/toylage/public_html/wp-load.php`;
- backed up the homepage `post_content` and `_elementor_data`;
- updated only the three Elementor button widgets;
- cleared `_elementor_element_cache`;
- cleared Elementor file cache.

Archived deployment script:

```text
toyla-live-home-brand-buttons-update-2026-06-28.php
```

Live backup:

```text
/home/toylage/wordpress-backups/toyla-home-brand-buttons-before-20260628-100927.json
```

## Live Verification

Verified from live HTML after deployment:

```text
Stapelstein    https://toyla.ge/shop/?filtering=1&filter_product_brand=138
Clixo          https://toyla.ge/shop/?filtering=1&filter_product_brand=134
MODU           https://toyla.ge/shop/?filtering=1&filter_product_brand=137
ყველა პროდუქტი https://toyla.ge/shop/
```

No WPCode snippet was changed for this task.
