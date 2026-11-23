<?php
/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the frameworks
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @link: https://codeigniter4.github.io/CodeIgniter4/
 */

namespace CI4Twig;

use CI4Twig\Config\Services;

/**
 * @param string $name
 * @param mixed $data
 * @param array $options Rendering options: saveData, cache, cache_name, debug.
 *
 * @return string
 * @throws \Twig\Error\Error
 */
function view(string $name, $data = [], array $options = []): string
{
    return Services::twigRenderer()->render($name, $data, $options);
}
