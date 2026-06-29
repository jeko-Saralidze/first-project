# Toyla GTranslate Languages - 2026-06-29

## Final Live State

Toyla live GTranslate now matches the tested setup:

- Default language: `ka`
- Enabled languages: `en`, `ka`, `ru`
- Selector style: `flags_code`
- Floating selector: `no`
- Compact dropdown CSS copied from the tested Toyla setup

Live backup:

```text
/home/toylage/wordpress-backups/toyla-gtranslate-before-test-language-copy-20260629-151347.json
```

## Tagline Copy Fix

Bad literal English output:

```text
Discoveries beyond the Toyla game
```

Correct English:

```text
Toyla - Discoveries Beyond Play
```

Correct Russian:

```text
Toyla - открытия за пределами игры
```

Implemented as isolated live mu-plugin:

```text
/home/toylage/public_html/wp-content/mu-plugins/toyla_tagline_translation_override.php
```

## Verification

Live homepage and shop checks:

```text
homepage_wrapper_count=2
homepage_settings_en_ka_ru_count=2
homepage_has_mobile_ru_css=True
homepage_has_tagline_override=True

shop_wrapper_count=2
shop_settings_en_ka_ru_count=2
shop_has_mobile_ru_css=True
shop_has_tagline_override=True
```

Tagline override check:

```text
has_english_override_copy=True
has_russian_override_copy=True
has_bad_phrase_in_source=False
```

Mobile selector notes:

- Both homepage and shop render two GTranslate wrappers, matching desktop/mobile header structure.
- Both wrappers receive `en`, `ka`, and `ru`.
- Mobile CSS includes `ru` positioning under `@media (max-width:767px)`.
- No separate 390px Playwright screenshot was generated because Playwright is not installed locally.

## Source Of Truth

Do not rely on the old local `fix_gtranslate.php` helper. It reflects an earlier EN+KA-only assumption. Current GTranslate source of truth is `en`, `ka`, `ru`.

## 2026-06-29 Mobile Header Follow-up

A later mobile audit found that the desktop header had been updated but the mobile header still used the old Georgian tagline:

```text
Toyla ბავშვობის ლამაზი მოგონებებისთვის
```

The mobile source was corrected to match desktop:

```text
Toyla თამაშის მიღმა აღმოჩენებია
```

The live update targeted the actual Avanam theme mod key:

```text
mobile_html_content
```

Verification after the corrected live update:

```text
homepage: status=200, languages=True, old_mobile=False, new_mobile=True, override=True, bad_english=False
shop:     status=200, languages=True, old_mobile=False, new_mobile=True, override=True, bad_english=False
product:  status=200, languages=True, old_mobile=False, new_mobile=True, override=True, bad_english=False
```

Real product page used for audit:

```text
https://toyla.ge/product/clixo-%e1%83%93%e1%83%98%e1%83%9c%e1%83%9d%e1%83%96%e1%83%90%e1%83%95%e1%83%a0%e1%83%98%e1%83%a1-%e1%83%9c%e1%83%90%e1%83%99%e1%83%a0%e1%83%94%e1%83%91%e1%83%98-22-%e1%83%aa%e1%83%90%e1%83%9a%e1%83%98/
```

## Rule For Future Text Fixes

Every text/copy fix must be adapted and verified for mobile as well as desktop before it is considered complete.

Minimum verification checklist:

```text
1. Desktop source/rendered text checked.
2. Mobile source/rendered text checked.
3. Homepage checked.
4. Shop page checked when header/global UI is involved.
5. At least one real product page checked when global header/footer/snippets are involved.
6. Old text absence checked.
7. New text presence checked.
8. Language selector still shows en/ka/ru.
```
