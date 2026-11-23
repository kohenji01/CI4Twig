<?php

namespace CI4Twig;

use CI4Twig\Config\Services;
use CodeIgniter\Debug\Toolbar\Collectors\Views;

final class TwigRenderer
{
    private $savedData;

    public function render(string $name, $data, array $options): string
    {
        $extension = $_ENV['CI4Twig.DefaultTemplateExtension'] ?? '.html.twig';
        if ($extension !== '' && substr($name, -strlen($extension)) !== $extension) {
            $name .= $extension;
        }

        $saveData = array_key_exists('saveData', $options)
            ? (bool) $options['saveData']
            : (bool) (config('View')->saveData ?? true);

        if (is_array($this->savedData) && is_array($data)) {
            $data = array_merge($this->savedData, $data);
        }

        $ttl = $options['cache'] ?? null;
        $cache = null;
        $output = null;
        if (is_int($ttl) && $ttl > 0) {
            $cache = Services::cache();
            $key = 'ci4twig_' . hash('sha256', $options['cache_name'] ?? $name);
            $output = $cache->get($key);
        }

        if (! is_string($output)) {
            $output = Services::twig()->Environment->render($name, ['CI' => $data]);

            if ($cache !== null) {
                $cache->save($key, $output, $ttl);
            }
        }

        if ($saveData) {
            $this->savedData = $data;
        }

        if (defined('CI_DEBUG') && CI_DEBUG && ($options['debug'] ?? true) === true
            && in_array(Views::class, config('Toolbar')->collectors ?? [], true)
        ) {
            $label = str_replace(['--', '<', '>', "\r", "\n"], ['%2D%2D', '%3C', '%3E', '%0D', '%0A'], $name);

            return '<!-- DEBUG-VIEW START ' . $label . ' -->' . PHP_EOL
                . $output . PHP_EOL
                . '<!-- DEBUG-VIEW ENDED ' . $label . ' -->' . PHP_EOL;
        }

        return $output;
    }
}
