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
