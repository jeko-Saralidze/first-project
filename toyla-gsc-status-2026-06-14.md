# Toyla GSC Status - 2026-06-14

## Status

- Google Search Console property checked: `https://toyla.ge/`.
- `robots.txt` was updated live through Rank Math: General Settings > Edit robots.txt.
- GSC Validate Fix was started for `Blocked by robots.txt`.
- Main issue: WooCommerce filter/add-to-cart crawl noise from URLs containing `filtering=1`, `filter_product_*`, and `add-to-cart`.

## Live robots.txt

Verified live at `https://toyla.ge/robots.txt`.

```txt
User-agent: *
Disallow: /wp-admin/
Allow: /wp-admin/admin-ajax.php
Disallow: /*?filtering=1
Disallow: /*&filtering=1
Disallow: /*?filter_product_
Disallow: /*&filter_product_
Disallow: /*?add-to-cart=
Disallow: /*&add-to-cart=

Sitemap: https://toyla.ge/sitemap_index.xml
```

## GSC Snapshot Before Validation

- Page indexing last update: 2026-06-05.
- Indexed: 27.
- Not indexed: 1,296.
- Blocked by robots.txt: 883.
- Alternate page with proper canonical tag: 295.
- Excluded by noindex: 12.
- Page with redirect: 10.
- Soft 404: 1.
- Discovered currently not indexed: 75.
- Crawled currently not indexed: 20.

## Performance

- Period: last 3 months.
- Total clicks: 10.
- Total impressions: 25.
- CTR: 40%.
- Average position: 2.
- Query shown: `toyla` - 4 clicks, 6 impressions.

## Sitemap And Quality Reports

- Sitemap: `/sitemap_index.xml`.
- Submitted: 2026-05-20.
- Last read: 2026-06-05.
- Status: Success.
- Discovered pages: 101.
- Core Web Vitals: not enough usage data for mobile and desktop.
- HTTPS: 0 non-HTTPS URLs, 11 HTTPS URLs, no issues.
- Product snippets: invalid 0, valid 1. Improvements: missing `aggregateRating` 1, missing `review` 1.
- Merchant listings: invalid 0, valid 1. Shipping, return policy, GTIN/brand warnings: 0.
- Manual actions: no issues detected.
- Security issues: no issues detected.

## WordPress / Rank Math Notes

- Rank Math is active.
- Product schema is set to WooCommerce Product.
- Product categories global setting is not noindex.
- Existing WPCode snippet `Shop filter noindex` ID 3380 is active, but only uses `wp_robots`; Rank Math can still output its own robots meta.
- Prepared local PHP enhancement: `C:\Users\JEKO\toyla-shop-filter-snippet.php`.
- The PHP enhancement is not deployed yet. WPCode editor/save was blocked by Chrome clipboard/session limitations and save attempts redirected to Profile / connection lost.

## Meaning Of This Change

This is a crawl-control fix through `robots.txt`. Googlebot should stop crawling WooCommerce filter/add-to-cart URLs that were creating a large `Blocked by robots.txt` report.

This is not a meta noindex fix. If Rank Math robots meta control is needed on filter URLs later, the WPCode snippet should be updated or the logic should be deployed through the theme/plugin layer.

## Next Check

- Re-check GSC validation status in 7-14 days.
- Confirm `Blocked by robots.txt` is not growing because of filter/add-to-cart URLs.
- Review product/category indexing.
- Optionally deploy `toyla-shop-filter-snippet.php` if meta-level noindex/nofollow control is needed.
