# Toyla Product Card Title Branding - 2026-06-19

## Summary

- Live shop product card title readability was updated through WPCode.
- The change was tested first on `http://toyla-test.local/shop/`.
- Live deployment was completed on `https://toyla.ge/shop/`.
- Product data was not changed: no prices, descriptions, images, inventory, categories, SEO fields, or product order were edited during this title/card update.

## Live WPCode snippet

- Snippet title: `Toyla product card title and brand styling`
- WPCode snippet ID: `3392`
- Status: `Active`
- Type: `PHP Snippet`

## What the snippet does

- Adds product card title CSS on shop/archive/front-page product grids.
- Expands title display from the previous clipped 2-line behavior to a controlled 4-line title area.
- Sets card title typography:
  - desktop: `font-size: 15px`, `line-height: 20px`, `min-height/max-height: 80px`
  - mobile: `font-size: 14px`, `line-height: 19px`, `min-height/max-height: 76px`
- A follow-up mobile fix added generic `ul.products li.product .woocommerce-loop-product__title` selectors and explicit `height`, `white-space`, `overflow`, `display`, `-webkit-box-orient`, and `-webkit-line-clamp` rules so mobile product grids cannot fall back to the theme's old 2-line treatment.
- Detects the brand at the beginning of each product card title and wraps it in:

```html
<span class="toyla-card-brand">Brand</span>
```

- Supported brand prefixes:
  - `Stapelstein®`
  - `Stapelstein`
  - `Clixo`
  - `Connetix`
  - `Bilibo`
  - `Modu`
- Styles the detected brand in teal (`#0f8f9d`), bold, and slightly larger (`1.12em`).
- `Stapelstein®` was explicitly corrected so the registered mark is styled together with the brand name.
- The brand regex was updated from a whitespace escape to a plain-space check, `(?= |$)`, because WPCode stripped the backslash from `\s` during saving and temporarily prevented mobile/updated pages from wrapping brand names.

## Safety notes

- The change is isolated to WPCode and only outputs CSS/JS on shop-like product grid contexts:
  - `is_shop()`
  - product taxonomy archives
  - front page product widgets
- It does not run in admin.
- It does not touch WooCommerce product records or database product fields.
- It does not change cart, checkout, payment, order, email, Pixel, product schema, robots, sitemap, or SEO indexing logic.
- Rollback is simple: deactivate WPCode snippet ID `3392`.

## Test environment result

Tested on `http://toyla-test.local/shop/`.

- Desktop before fix: multiple product card titles were clipped by the 2-line title area.
- Mobile before fix: more titles were clipped because of the narrower card width.
- Test fix result:
  - brand styling worked
  - `Stapelstein®` styling included `®`
  - title clipping was removed on checked cards
  - no card overlap was detected

## Live verification

Checked live after activation at `https://toyla.ge/shop/`.

- CSS style tag present: `#toyla-live-product-card-title-brand-css`
- JS script tag present: `#toyla-live-product-card-brand-script`
- First 12 visible product cards:
  - branded count: `12/12`
  - clipped titles: `0`
  - card overlap pairs: `0`
- Mobile visual screenshot after the follow-up fix:
  - `C:\Users\JEKO\outputs\toyla-mobile-after-brand-fix.png`
  - confirms brand color is visible on mobile (`Clixo`, `Connetix`)
  - confirms product cards remain aligned without overlap
- Verified brand examples:
  - `Clixo`
  - `Connetix`
  - `Stapelstein®`
  - `Modu`

## Deployment notes

- A second CSS-only height override was considered for a 5-line title area, but it was not left active.
- Actual live change is the single active WPCode snippet ID `3392`.
- Follow-up update to snippet ID `3392` added mobile-safe generic selectors and fixed the WPCode-stripped regex issue.
- Local backup/prepared snippets:
  - `C:\Users\JEKO\toyla-live-product-card-title-brand-snippet-2026-06-19.php`
  - `C:\Users\JEKO\toyla-live-product-card-title-height-override-2026-06-19.php`

## Follow-up

- Visually inspect shop after cache/browser refresh on desktop and mobile.
- If future products have longer titles than the current grid tolerates, adjust snippet ID `3392` title height from 4 lines to a larger controlled height.
- Keep this as a WPCode snippet unless there is a later cleanup pass to consolidate frontend snippets into the child theme.
