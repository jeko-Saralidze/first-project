# Toyla Bilibo Variations and Cart - 2026-06-25

## Final Live State

- All 9 Bilibo parent products are `instock`.
- All 18 variations of the three configurable products are `instock`.
- Color swatches, variation images and six-piece set selection are active.
- The optional Bilibo bag is temporarily unavailable on test and live.

## Configurable Products

| Product | Live ID | Variations | Single | Set |
|---|---:|---|---:|---:|
| Bilibo Maxi - Classic | `2757` | `3437`-`3442` | `85 ₾` | `450 ₾` |
| Bilibo Maxi - Pastel | `2769` | `3443`-`3448` | `85 ₾` | `450 ₾` |
| Bilibo MIDI - Classic | `2794` | `3449`-`3454` | `50 ₾` | `250 ₾` |

## Bag Feature Flag

```php
define('TOYLA_BILIBO_BAG_AVAILABLE', false);
```

With the flag disabled:

- the unavailable bag card remains visible;
- no bag checkbox or quantity control is rendered;
- new or manually submitted bag metadata is ignored;
- legacy bag metadata is removed from cart sessions;
- bag values do not affect cart totals.

## Verified Artifacts

- `toyla-bilibo-color-swatch-live-2026-06-25.php`
- `toyla-cart-audit-bag-unavailable-live.ps1`
- Plugin SHA-256:
  - `A6595EDCA8CB7F6FA03596B31F33D46DC0D2DDF62EC9F6158F5B0877682D8CCB`
- PHP lint:
  - no syntax errors

## Cart Audit

| Scenario | Expected | Actual |
|---|---:|---:|
| Classic x2, submitted bag ignored | `170 ₾` | `170 ₾` |
| Classic set x2, submitted bag ignored | `900 ₾` | `900 ₾` |
| Pastel x2 | `170 ₾` | `170 ₾` |
| MIDI x2 | `100 ₾` | `100 ₾` |
| MIDI set x2 | `500 ₾` | `500 ₾` |

All isolated live scenarios passed.

## Live Backups

- `/home/toylage/wordpress-backups/toyla-bilibo-before-bag-disable-20260625-200834.php`
- `/home/toylage/wordpress-backups/toyla-bilibo-stock-before-instock-20260625-200834.json`

