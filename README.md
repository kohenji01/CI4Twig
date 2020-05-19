# CI4Twig

CodeIgniter4用のTwig組み込みです。

# Install

Composerで導入可能です。CI4やTwigも予めComposerでインストールしておいてください。

```bash
$ composer require sarah-systems/ci4twig
```

# Usage
デフォルトでは、Twig関連のパスは自体は次のような設定で動作するようになっています。

| 種類         | パス                        |
|:-------------|:----------------------------|
| テンプレート | app/Views                   |
| キャッシュ   | writable/twig/cache         |

`writable`が正しく書き込み可能な場合、下の3つのディレクトリは自動的に生成されます。
もしエラーが起きた場合は、これらのディレクトリを作って適切なパーミッションを与えることで動作します。

もしこのパスを変更したい場合は`.env`ファイルに次のパラメータをセットすることで、任意のパスに変更可能です。

```bash
CI4Twig.TemplateDir = /path/to/TemplateDir
CI4Twig.CacheDir = /path/to/CacheDir
```

これ以外にも`.env`では、TwigのDebugフラグのOn/Offとデフォルトの拡張子を設定できます。

```bash
CI4Twig.Debug = 1 または 0
CI4Twig.DefaultTemplateExtension = .html.twig
```

デフォルトの拡張子を設定すると、view()関数で

### view()
CI4のview関数をTwig用にCI4Twigというnamespaceで定義しています。

利用する際は`app`ディレクトリ直下の`Common.php`に次を追記してください。

```php
require_once ROOTPATH . "vendor/sarah-systems/ci4twig/src/Common.php";
```

使用法はCI4のview関数と同じですが、関数の利用時には名前空間を指定するか、事前にエイリアスを張ってください。

```php
\CI4Twig\view('template.html.twig');
```

または

```php
use function CI4Twig\view as view;
view('template.html.twig');
```

拡張子`.html.twig`（`CI4Twig.DefaultTemplateExtension`で設定されたものです。無指定の場合は`.html.twig`）は省略可能です。

```php
\CI4Twig\view('template');
```

view関数の第2パラメータはTwig変数`$CI`にアサインされます。

```php
$data = [ 'apple' , 'banana' , 'lemon' ];
\CI4Twig\view('template',$data);
```

Twigのtemplate上では
```twig
{{ CI.0 }} ← appleが表示されます。
```

第3引数の$optionsは無視されます。

### Service

CI4のServiceが利用可能です。

```php
use CI4Twig\Config\Services;

$time = date('Y-m-d H:i:s');
$twig = Services::twig();
$twig->Environment->addGlobal('time',$time);
$twig->Environment->display('template.html.twig');
```

# License
The source code is licensed MIT. The website content is licensed CC BY 4.0,see LICENSE.