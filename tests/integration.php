<?php

use CI4Twig\Config\Services;
use CodeIgniter\Debug\Toolbar\Collectors\Views;
use Twig\Error\LoaderError;

use function CI4Twig\view;

$root = dirname(__DIR__) . DIRECTORY_SEPARATOR;
$framework = $root . 'vendor/codeigniter4/framework/';
$fixture = sys_get_temp_dir() . '/ci4twig-' . bin2hex(random_bytes(8)) . '/';
mkdir($fixture, 0700);
mkdir($fixture . 'cache');
mkdir($fixture . 'logs');

register_shutdown_function(static function () use ($fixture): void {
    $entries = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($fixture, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($entries as $entry) {
        if ($entry->isDir()) {
            rmdir($entry->getPathname());
        } else {
            unlink($entry->getPathname());
        }
    }
    rmdir($fixture);
});

define('HOMEPATH', $root);
define('CONFIGPATH', $framework . 'app/Config/');
define('PUBLICPATH', $framework . 'public/');
define('WRITEPATH', $fixture);
define('CI_DEBUG', true);

require $root . 'vendor/autoload.php';
require $framework . 'system/Test/bootstrap.php';
require $root . 'src/Common.php';

function expectSame($expected, $actual, string $case): void
{
    if ($expected !== $actual) {
        throw new RuntimeException(
            $case . ' expected ' . var_export($expected, true) . ', got ' . var_export($actual, true)
        );
    }
}

$_ENV['CI4Twig.TemplateDir'] = $fixture;
$_ENV['CI4Twig.Debug'] = 'false';
unset($_ENV['CI4Twig.DefaultTemplateExtension']);
file_put_contents($fixture . 'message.html.twig', "{{ CI.name|default('-') }}/{{ shared }}");
file_put_contents($fixture . 'value.html.twig', '{{ CI }}');
file_put_contents($fixture . 'custom.twig', 'custom');
file_put_contents($fixture . 'empty.html.twig', '');

$viewConfig = config('View');
$viewConfig->saveData = false;
$toolbar = config('Toolbar');
$toolbar->collectors = [];

$cacheConfig = config('Cache');
$cacheConfig->handler = 'file';
$cacheConfig->storePath = $fixture . 'cache/';
if (property_exists($cacheConfig, 'file')) {
    $cacheConfig->file['storePath'] = $fixture . 'cache/';
}

$twig = Services::twig();
expectSame($twig, Services::twig(), 'Shared Twig service');
expectSame(false, $twig->Environment->isDebug(), 'String false disables Twig debug');
$twig->Environment->addGlobal('shared', 'S');
$twig->Environment->addGlobal('CI', 'direct');

expectSame('A/S', view('message', ['name' => 'A']), 'Implicit extension');
expectSame('B/S', view('message.html.twig', ['name' => 'B']), 'Explicit extension');
expectSame('-/S', view('message'), 'Data stays within its render');
expectSame('scalar', view('value', 'scalar'), 'Scalar data');
expectSame('direct', $twig->Environment->render('value.html.twig'), 'Global CI remains available to direct renders');

$_ENV['CI4Twig.DefaultTemplateExtension'] = '.twig';
expectSame('custom', view('custom'), 'Custom implicit extension');
expectSame('custom', view('custom.twig'), 'Custom explicit extension');
$_ENV['CI4Twig.DefaultTemplateExtension'] = '';
expectSame('custom', view('custom.twig'), 'Empty default extension');
unset($_ENV['CI4Twig.DefaultTemplateExtension']);

$viewConfig->saveData = true;
expectSame('A/S', view('message', ['name' => 'A']), 'View config enables data retention');
expectSame('A/S', view('message', ['other' => 'B']), 'Retained values are merged');
expectSame('B/S', view('message', ['name' => 'B'], ['saveData' => false]), 'Call overrides retained values');
expectSame('A/S', view('message'), 'Transient data is discarded');

try {
    view('missing', ['name' => 'failed']);
    throw new RuntimeException('Missing template did not throw');
} catch (LoaderError $exception) {
    expectSame('A/S', view('message'), 'Failed render preserves retained data');
}

$viewConfig->saveData = false;
expectSame('B/S', view('message', ['name' => 'B'], ['saveData' => true]), 'Call enables retention');
expectSame('B/S', view('message'), 'Retained data is still visible with retention turned off');

$options = ['cache' => 60, 'cache_name' => 'integration'];
Services::cache()->save('integration', 'standard-cache-entry', 60);
expectSame('C/S', view('message', ['name' => 'C'], $options), 'Cache miss');
$toolbar->collectors = [Views::class];
expectSame(
    '<!-- DEBUG-VIEW START message.html.twig -->' . PHP_EOL . 'C/S' . PHP_EOL
        . '<!-- DEBUG-VIEW ENDED message.html.twig -->' . PHP_EOL,
    view('message', ['name' => 'D'], $options + ['saveData' => true]),
    'Cache hit applies current debug option'
);
expectSame('C/S', view('message', [], $options + ['debug' => false]), 'Cached HTML excludes debug markers');
expectSame('D/S', view('message', [], ['debug' => false]), 'Cache hit retains current data');
expectSame('standard-cache-entry', Services::cache()->get('integration'), 'Cache keys are namespaced');
$toolbar->collectors = [];

expectSame('E/S', view('message', ['name' => 'E'], ['cache' => 60]), 'Default cache key');
expectSame('E/S', view('message.html.twig', ['name' => 'F'], ['cache' => 60]), 'Explicit name shares cache key');
expectSame('F/S', view('message', ['name' => 'F'], ['cache' => 0]), 'Zero TTL bypasses cache');

$emptyCache = ['cache' => 60, 'cache_name' => 'empty'];
expectSame('', view('empty', [], $emptyCache), 'Empty HTML cache miss');
expectSame('', view('missing', [], $emptyCache), 'Empty HTML cache hit');

echo 'CI4Twig integration checks passed on PHP ' . PHP_VERSION . PHP_EOL;
