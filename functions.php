<?php

namespace Dashifen;

use Dashifen\Theme2023\Theme;
use Dashifen\Exception\Exception;
use Dashifen\Theme2023\Agents\SilencingAgent;
use Dashifen\Theme2023\Agents\CoreRemovalAgent;
use Dashifen\WPHandler\Agents\Collection\Factory\AgentCollectionFactory;

if (version_compare(PHP_VERSION, '8.0', '<')) {
  $message = 'The Dashifen 2023 block theme requires at least PHP 8.0; you\'re using %s';
  exit(sprintf($message, PHP_VERSION));
}

if (!class_exists(Theme::class)) {
  require_once 'vendor/autoload.php';
}

(function() {
  try {
    
    // by instantiating these objects within the scope of this anonymous
    // function, it means that the rest of the site won't have access to them.
    // this avoids anyone else trying to re-initialize or access any other
    // public methods of our classes unless they're also static.
    
    $theme = new Theme();
    
    $agentCollectionFactory = new AgentCollectionFactory();
    $agentCollectionFactory->registerAgent(CoreRemovalAgent::class);
    $agentCollectionFactory->registerAgent(SilencingAgent::class);
    
    $theme->setAgentCollection($agentCollectionFactory);
    $theme->initialize();
  } catch (Exception $e) {
    
    // the catcher simply dumps the exception to the screen when WP_DEBUG is
    // set.  otherwise, it'll try to write things to the log where we can fix
    // them later.  granted, if the theme throws an exception, we're pretty
    // much screwed.
    
    Theme::catcher($e);
  }
})();
