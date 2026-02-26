# Serial Validator - Languages

This folder contains translation files for the Serial Validator plugin.

## Translation Files

- **serial-validator.pot** - Translation template file
- **serial-validator-{locale}.po** - Translation files for specific locales
- **serial-validator-{locale}.mo** - Compiled translation files

## How to Translate

### Using WP-CLI (Recommended)

Generate POT file:
```bash
wp i18n make-pot . languages/serial-validator.pot
```

Create translation for specific locale:
```bash
wp i18n make-po languages/serial-validator.pot languages/serial-validator-de_DE.po
```

### Using Poedit

1. Download and install [Poedit](https://poedit.net/)
2. Open Poedit
3. Click "Create new translation"
4. Select `languages/serial-validator.pot`
5. Choose your language
6. Translate all strings
7. Save as `serial-validator-{locale}.po`
8. Poedit will automatically generate the `.mo` file

### Manual Method

1. Copy `serial-validator.pot` to `serial-validator-{locale}.po`
2. Replace `{locale}` with your language code (e.g., `de_DE`, `fr_FR`)
3. Edit the file and translate all `msgstr` strings
4. Compile to `.mo` using:
   ```bash
   msgfmt serial-validator-{locale}.po -o serial-validator-{locale}.mo
   ```

## Supported Locales

Common WordPress locale codes:

- `de_DE` - German (Germany)
- `fr_FR` - French (France)
- `es_ES` - Spanish (Spain)
- `it_IT` - Italian (Italy)
- `pt_BR` - Portuguese (Brazil)
- `nl_NL` - Dutch (Netherlands)
- `pl_PL` - Polish (Poland)
- `ru_RU` - Russian (Russia)
- `ja` - Japanese
- `zh_CN` - Chinese (Simplified)
- `ar` - Arabic

See full list: https://translate.wordpress.org/

## Testing Translations

1. Place `.po` and `.mo` files in this `languages/` folder
2. Change WordPress language in Settings > General
3. Visit the plugin pages to see translations
4. Frontend shortcode/widget will also use translations

## Contributing Translations

To contribute translations:

1. Create translation files for your language
2. Test thoroughly in WordPress
3. Submit via plugin support forum or repository

## Text Domain

All translatable strings use text domain: `serial-validator`

## Translation Context

The plugin uses standard WordPress translation functions:
- `__()` - Returns translated string
- `_e()` - Echoes translated string
- `esc_html__()` - Returns escaped translated string
- `esc_html_e()` - Echoes escaped translated string
- `esc_attr__()` - Returns attribute-safe translated string

## Need Help?

For translation questions:
- WordPress Translator Handbook: https://make.wordpress.org/polyglots/handbook/
- Poedit Documentation: https://poedit.net/trac/wiki/Doc
- WP-CLI i18n Commands: https://developer.wordpress.org/cli/commands/i18n/

Thank you for helping translate Serial Validator! 🌍
