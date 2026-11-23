# Changelog

[日本語](CHANGELOG.md) | English

## 0.2.0

- Added the `saveData`, `cache`, `cache_name`, and `debug` rendering options to the third argument of `\CI4Twig\view()`.
- Fixed template names that already include the configured extension. An empty extension disables completion.
- Passed the second argument as the render-local Twig variable `CI`.
- Made `CI4Twig.Debug` accept `true`/`false` and `1`/`0`.
- Added integration tests and Japanese and English documentation.

### Upgrading

When omitted, `saveData` follows CI4's view configuration. Previously retained array data remains available to a render with `['saveData' => false]`. See the [README](README.en.md#rendering-options) for per-call data and HTML cache settings.
