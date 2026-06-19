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
Disallow: /*wc-ajax=*

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

## 2026-06-15 Live Verification Update

- `robots.txt` was rechecked live and returns `200`.
- `robots.txt` includes the additional WooCommerce AJAX crawl rule: `Disallow: /*wc-ajax=*`.
- `https://toyla.ge/shop/?filtering=1&filter_product_brand=stapelstein` returns `200` with `follow, noindex` and canonical `https://toyla.ge/shop/`.
- `https://toyla.ge/cart/` returns `200` with `noindex, follow`.
- `https://toyla.ge/product-category/samagido-tamashebi/` returns `301` to `https://toyla.ge/shop/`.
- `https://toyla.ge/product/ექიმის-ნაკრები/` remains `404`, which is acceptable unless a real replacement product is confirmed.
- `https://toyla.ge/cgi-sys/suspendedpage.cgi` still returns `200` with title `Account Suspended`; this remains the open soft 404/server-level cleanup item.
- Key pages checked for old test-domain leakage: homepage, shop, brand archive, cart, checkout, and the Stapelstein product. None contained `toyla-test.local`.
- Full sitemap crawl for `toyla-test.local` was attempted from PowerShell but timed out; key-page spot checks are clean.

Stapelstein product check:

- URL checked: `https://toyla.ge/product/stapelstein-ცისარტყელას-შიგნით-პა/`
- Status: `200`.
- Robots: `follow, index`.
- Canonical: self-canonical.
- Product schema is present.
- `brand`, `shippingDetails`, and `hasMerchantReturnPolicy` are present in schema.
- Old English text `Inside Set warm pastel` is no longer present.
- Meta Pixel ID, `ViewContent`, and `AddToCart` are present.

Checkout source test with cart state:

- Product added to cart: ID `2459`.
- Checkout page returned `200`.
- Checkout source includes Meta Pixel ID `1689393822482338`.
- Checkout source includes `InitiateCheckout`.
- Checkout source includes browser event id.
- Checkout source includes Flitt/payment indicators.
- Meta Events Manager UI showed only partial realtime receipt, but follow-up browser network-level debug confirmed actual Meta dispatch.
- Isolated Chrome + test code `TEST2337` confirmed `ViewContent` was posted to `facebook.com/tr/` from the product page with Pixel ID `1689393822482338` and browser `eventID`.
- Checkout with cart state confirmed `InitiateCheckout` was posted to `facebook.com/tr/` with Pixel ID `1689393822482338`, browser `eventID`, `content_ids ["200106_2459"]`, value `230.0`, currency `gel`, and HTTP `200`.
- `PageView` and `AddToCart` were also confirmed as browser network requests.

Live Store API brand check:

- Public products: `94`.
- Products missing visible brand: `0`.
- Brand distribution: Bilibo `9`, Clixo `23`, Connetix `25`, Kidlupe `4`, Modu `9`, Stapelstein `24`.
- Brand archive `/brand/clixo/` returns `200`, is indexable, and no longer outputs Product schema.

## 2026-06-15 Meta Catalogue Update

- Meta Commerce Manager data source checked: catalog `2182571532579185`.
- Existing source: `New data feed for Products from toyla.ge`.
- Source type: `Data file / Manual upload`.
- Before update, the source contained only `24` products, so Brand filters showed only partial brand coverage.
- Generated update file: `C:\Users\JEKO\Documents\Toyla-Meta-Catalog-All-94-With-Brands-2026-06-15.csv`.
- File contents verified before upload:
  - Rows: `94`.
  - Missing brand: `0`.
  - Missing image: `0`.
  - Brands: Bilibo `9`, Clixo `23`, Connetix `25`, Kidlupe `4`, Modu `9`, Stapelstein `24`.
- User uploaded the CSV manually through Meta's `Re-upload data file` flow because Chrome extension local file upload was blocked.
- Upload result verified in Meta UI:
  - Products: `94`.
  - Status: `All good`.
  - Last update: `15 Jun at 15:02`.
- Products Brand filter verified after upload:
  - `connetix` `25`
  - `stapelstein` `24`
  - `clixo` `23`
  - `bilibo` `9`
  - `modu` `9`
  - `kidlupe` `4`
- Counts now match the live Toyla Store API exactly.

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
3. Meta Events Manager activity: browser-side dispatch is confirmed for `PageView`, `ViewContent`, `AddToCart`, and `InitiateCheckout`; note that realtime Meta Test Events UI did not list every event despite confirmed `facebook.com/tr/` requests.
4. Meta Ads preflight: confirm business verification/current status, billing/ad account restrictions, Pixel event freshness/history, objective, budget, audience, creative, and destination. `Purchase` was not re-tested in this pass and should only be tested with an intentional test order.
5. Meta catalog feed maintenance: replace the one-off manual CSV process with a scheduled feed or live CTX Feed setup so future product changes do not require manual uploads.
   Current blocker: CTX brand mapping still collapses to `Toyla=94`, and a WPCode CSV endpoint attempt was blocked by LiteSpeed `403`, so the verified manual CSV remains the live source for now.
6. WooCommerce emails: verify live processing/completed/refund emails and send test emails.
7. Code cleanup: consolidate duplicate filters, frontend text overrides, cart JS/source of truth, and WPCode/child-theme logic after critical flows are verified.

## 2026-06-19 Shop Card Visual Update

- Live WPCode snippet added and activated: `Toyla product card title and brand styling`, ID `3392`.
- Purpose: product card title readability and brand-name visual emphasis on shop/archive/front-page product grids.
- This is a frontend visual update only.
- No SEO, robots, sitemap, schema, Pixel, CAPI, checkout, cart, payment, product data, catalog, or GSC validation logic was changed.
- Live verification on `/shop/`:
  - style tag present: `#toyla-live-product-card-title-brand-css`
  - script tag present: `#toyla-live-product-card-brand-script`
  - first 12 product cards: branded `12/12`
  - clipped titles: `0`
  - card overlaps: `0`
- Follow-up mobile correction:
  - generic product title selectors added to snippet ID `3392`
  - WPCode-stripped regex escape fixed by using `(?= |$)`
  - mobile screenshot verified at `C:\Users\JEKO\outputs\toyla-mobile-after-brand-fix.png`
- Detailed note: `toyla-product-card-title-branding-2026-06-19.md`.
