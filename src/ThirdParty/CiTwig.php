<?php
/**
 * =============================================================================================
 *  Project: CI4Twig
 *  File: CiTwig.php
 *  Date: 2020/05/15 17:50
 *  Author: Shoji Ogura <kohenji@sarahsytems.com>
 *  Copyright (c) 2020. SarahSystems lpc.
 *  This software is released under the MIT License, see LICENSE.txt.
 * =============================================================================================
 *
 */

namespace CI4Twig\ThirdParty;

use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

class CiTwig
{
    public $FilesystemLoader;
    public $Environment;
    
    protected $default_view_dir = APPPATH . 'Views';
    protected $default_cache_dir = WRITEPATH . 'twig' . DIRECTORY_SEPARATOR . 'cache';
    
    public function __construct()
    {
        $debug = (bool)$_ENV['CI4Twig.Debug'] ?? false;
        
        $this->FilesystemLoader = new FilesystemLoader( $_ENV['CI4Twig.TemplateDir'] ?? $this->default_view_dir );
        $this->Environment = new Environment(
            $this->FilesystemLoader, [
            'cache' => $_ENV['CI4Twig.CacheDir'] ?? $this->default_cache_dir,
            'debug' => $debug,
        ]
        );
        if ($debug) {
            $this->Environment->addExtension(new DebugExtension());
        }
    }
}