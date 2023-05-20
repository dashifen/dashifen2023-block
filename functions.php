<?php

namespace Dashifen;

use Dashifen\Theme2023\Theme;
use Dashifen\Exception\Exception;

if (version_compare(PHP_VERSION, '8.0', '<')) {
  $message = 'The Dashifen 2023 block theme requires at least PHP 8.0; you\'re using %s';
  exit(sprintf($message, PHP_VERSION));
}

if (!class_exists(Theme::class)) {
  require_once 'vendor/autoload.php';
}

(function() {
  try {
    $theme = new Theme();
    $theme->initialize();
  } catch (Exception $e) {
    Theme::catcher($e);
  }
})();
