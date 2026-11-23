# CI4Twig

日本語 | [English](README.en.md) | [変更履歴](CHANGELOG.md)

CodeIgniter 4 のアプリで Twig テンプレートを描画するライブラリです。

## インストールと最初の描画

CI4 アプリのルートで実行します。

```bash
composer require sarah-systems/ci4twig:^0.2
```

`app/Common.php` に次の行を追加します。

```php
require_once ROOTPATH . 'vendor/sarah-systems/ci4twig/src/Common.php';
```

`app/Views/hello.html.twig` を作成します。

```twig
<p>こんにちは、{{ CI.name }}さん。</p>
```

コントローラーのメソッドで描画結果を返します。

```php
return \CI4Twig\view('hello', ['name' => '太郎']);
```

`hello.html.twig` と拡張子まで指定することもできます。`use function CI4Twig\view;` でインポートすれば、名前空間を付けずに呼び出せます。CI4 標準のビューは `\view()` で利用できます。

## 設定

| `.env` のキー | 既定値 | 用途 |
| --- | --- | --- |
| `CI4Twig.TemplateDir` | `app/Views` | テンプレートの検索先 |
| `CI4Twig.CacheDir` | `writable/twig/cache` | Twig のコンパイルキャッシュの保存先 |
| `CI4Twig.Debug` | `false` | Twig のデバッグと `dump()` を有効化。 `true`／`false` または `1`／`0` を指定 |
| `CI4Twig.DefaultTemplateExtension` | `.html.twig` | テンプレート名に補う拡張子。空文字を指定すると補完を無効化 |

Twig のコンパイルキャッシュの保存先は、アプリから書き込めるようにしてください。

## 描画オプション

```php
return \CI4Twig\view('article', ['title' => 'お知らせ'], [
    'saveData'   => false,
    'cache'      => 60,
    'cache_name' => 'article-ja',
    'debug'      => false,
]);
```

| オプション | 動作 |
| --- | --- |
| `saveData` | 省略時は CI4 の `Config\View::$saveData` に従います。設定値がなければ `true` です。保持済みデータと今回のデータが両方配列の場合はマージし、同じ文字列キーは今回の値を優先します。 `false` は今回の結果を次の描画に保持しません。以前に保持した配列データは今回の描画でも使われます。 |
| `cache` | 正の整数を指定すると、描画済み HTML を CI4 のキャッシュサービスにその秒数だけ保存します。 |
| `cache_name` | HTML キャッシュの識別名。省略時は拡張子の補完後のテンプレート名を使います。 |
| `debug` | CI4 のデバッグと Views コレクターが有効な場合に付く HTML コメントを、`false` で抑制します。Twig のデバッグは `CI4Twig.Debug` で設定します。 |

HTML キャッシュの保存先は CI4 のキャッシュ設定で管理します。言語などで出力が変わる場合は、同じ HTML を共有できる単位で `cache_name` を分けてください。利用者固有のページや CSRF トークンを含むフォームでは、`cache` を省略してください。

## 0.1.x からの移行

```bash
composer require sarah-systems/ci4twig:^0.2 --with-all-dependencies
```

- 第3引数が有効になりました。各呼び出しのデータだけを使うには、最初の描画前に `app/Config/View.php` の `$saveData` を `false` にし、呼び出し側で保持を有効にしないでください。ビュー設定は CI4 標準ビューにも適用されます。
- 第2引数のデータは、その描画の変数 `CI` として渡します。Twig 環境を直接使うコードでは、その呼び出しに必要な変数を明示してください。

## 対応と検証

Composer の依存条件は PHP `>=7.2`、CodeIgniter `^4`、Twig `^3` です。利用できる組み合わせは各依存パッケージの要件にも従います。

このリポジトリで `composer install` を実行した後、`php tests/integration.php` で結合テストを実行できます。

[GitHub Actions の互換性テスト](.github/workflows/compatibility.yml) は、PHP 8.2〜8.5 で Composer が解決した依存関係を使って同じテストを実行します。

## Twig サービス

Twig 環境を直接使って、アプリ共通の変数や拡張を登録できます。

```php
use CI4Twig\Config\Services;

$twig = Services::twig();
$twig->Environment->addGlobal('siteName', 'Example');
echo $twig->Environment->render('hello.html.twig', ['CI' => ['name' => '太郎']]);
```

## ライセンス

ソースコードは MIT ライセンスです。詳細は [LICENSE](LICENSE) を参照してください。
