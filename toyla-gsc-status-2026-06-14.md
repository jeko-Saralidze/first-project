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
- Existing WPCode snippet `Shop filter noindex` ID 3380 was updated with the prepared PHP enhancement from `C:\Users\JEKO\toyla-shop-filter-snippet.php`.
- Deployment verified on 2026-06-14.
- Filtered shop URL returns `follow, noindex`.
- Clean shop URL `https://toyla.ge/shop/` still returns `follow, index`.
- Filter/add-to-cart links on the tested filtered page include `nofollow`.

## Meaning Of This Change

This is a crawl-control fix through `robots.txt`. Googlebot should stop crawling WooCommerce filter/add-to-cart URLs that were creating a large `Blocked by robots.txt` report.

Meta-level filter URL handling is also deployed through WPCode. This keeps real clean shop/product/category URLs indexable while filtered shop URLs are marked `noindex, follow`.

## Next Check

- Re-check GSC validation status in 7-14 days.
- Confirm `Blocked by robots.txt` is not growing because of filter/add-to-cart URLs.
- Review product/category indexing.
- After GSC refreshes, confirm filtered URLs are no longer appearing in the Indexed group.

## Final Remaining Issues

1. Soft 404: `https://toyla.ge/cgi-sys/suspendedpage.cgi` currently returns `200`; fix at hosting/server level so it returns `404` or `410`.
2. Product/category indexing spot-check: inspect at least one product URL and one category URL in GSC.
3. Checkout + Meta Pixel live flow: verify add-to-cart -> checkout -> `InitiateCheckout` -> Flitt/payment UI -> fresh Meta Events Manager activity.
4. Meta Ads preflight: confirm business verification/current status, billing/ad account restrictions, Pixel event freshness, objective, budget, audience, creative, and destination.
5. WooCommerce emails: verify live processing/completed/refund emails and send test emails.
6. Code cleanup: consolidate duplicate filters, frontend text overrides, cart JS/source of truth, and WPCode/child-theme logic after critical flows are verified.
