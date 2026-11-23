# CI4Twig

[日本語](README.md) | English | [Changelog](CHANGELOG.en.md)

Render Twig templates in a CodeIgniter 4 app.

## Install and render your first template

From the CI4 application root, run:

```bash
composer require sarah-systems/ci4twig:^0.2
```

Add this line to `app/Common.php`:

```php
require_once ROOTPATH . 'vendor/sarah-systems/ci4twig/src/Common.php';
```

Create `app/Views/hello.html.twig`:

```twig
<p>Hello, {{ CI.name }}.</p>
```

Return the rendered HTML from your controller:

```php
return \CI4Twig\view('hello', ['name' => 'Taylor']);
```

You can also pass `hello.html.twig` or import the function with `use function CI4Twig\view;`. The native CI4 renderer remains available through `\view()`.

## Configuration

| `.env` key | Default | Purpose |
| --- | --- | --- |
| `CI4Twig.TemplateDir` | `app/Views` | Template search directory |
| `CI4Twig.CacheDir` | `writable/twig/cache` | Twig compilation cache directory |
| `CI4Twig.Debug` | `false` | Enable Twig debug mode and `dump()` with `true` or `1`. Use `false` or `0` to disable them. |
| `CI4Twig.DefaultTemplateExtension` | `.html.twig` | Extension added to template names. Set it to an empty string to disable completion. |

The application needs write access to the Twig compilation cache directory.

## Rendering options

```php
return \CI4Twig\view('article', ['title' => 'News'], [
    'saveData'   => false,
    'cache'      => 60,
    'cache_name' => 'article-en',
    'debug'      => false,
]);
```

| Option | Behavior |
| --- | --- |
| `saveData` | Defaults to CI4's `Config\View::$saveData`, or `true` if that setting has no value. When both values are arrays, the renderer merges retained and current data, with current values taking precedence on matching string keys. `false` prevents retaining the current result for later renders. Previously retained array data remains available to this render. |
| `cache` | A positive integer stores the rendered HTML in CI4's cache service for that many seconds. |
| `cache_name` | Identifies the HTML cache entry. Defaults to the template name after extension completion. |
| `debug` | Set to `false` to suppress the HTML comments emitted when CI4 debugging and the Views collector are enabled. Twig's own debug mode uses `CI4Twig.Debug`. |

CI4's cache configuration controls where rendered HTML is stored. Use separate cache names for distinct outputs such as different languages. Leave `cache` unset for personalized pages and forms that include CSRF tokens.

## Upgrade from 0.1.x

```bash
composer require sarah-systems/ci4twig:^0.2 --with-all-dependencies
```

- The third argument now controls rendering. To render using only each call's data, set `$saveData` to `false` in `app/Config/View.php` before the first render and leave retention disabled in individual calls. This configuration also applies to native CI4 views.
- The second argument becomes the `CI` variable for that render. When calling the Twig environment directly, pass the variables needed by that call.

## Compatibility and tests

Composer declares PHP `>=7.2`, CodeIgniter `^4`, and Twig `^3`. Available combinations also depend on the requirements of those packages.

Run `composer install` in this repository, then run `php tests/integration.php`.

The [GitHub Actions compatibility workflow](.github/workflows/compatibility.yml) runs the same tests on PHP 8.2 through 8.5 using the dependencies Composer resolves for each PHP version.

## Twig service

Access the Twig environment to register application globals or extensions.

```php
use CI4Twig\Config\Services;

$twig = Services::twig();
$twig->Environment->addGlobal('siteName', 'Example');
echo $twig->Environment->render('hello.html.twig', ['CI' => ['name' => 'Taylor']]);
```

## License

The source code is licensed under MIT. See [LICENSE](LICENSE).
