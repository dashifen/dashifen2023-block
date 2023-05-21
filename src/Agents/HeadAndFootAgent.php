<?php

namespace Dashifen\Theme2023\Agents;

use Dashifen\Theme2023\Theme;
use Dashifen\WPHandler\Agents\AbstractThemeAgent;
use Dashifen\WPHandler\Handlers\HandlerException;

/**
 * HeadAndFootAgent
 *
 * This agent encapsulates any changes that are made to the document's head
 * and footer.  In essence, if an action or filter is hooked to wp_head,
 * wp_footer, or other actions that happen within the DOM's <head> element,
 * you can find them here.
 *
 * @property Theme $handler
 */
class HeadAndFootAgent extends AbstractThemeAgent
{
  /**
   * initialize
   *
   * Uses addAction and/or addFilter to attach protected methods of this object
   * to the ecosystem of WordPress action and filter hooks.
   *
   * @return void
   * @throws HandlerException
   */
  public function initialize(): void
  {
    if (!$this->isInitialized()) {
      $this->addAction('wp_head', 'addPreConnections');
      $this->addAction('wp_enqueue_scripts', 'addAssets');
      $this->addFilter('pre_get_document_title', 'customizeTitle');
    }
  }
  
  /**
   * addPreConnections
   *
   * Adds preconnect <link> elements to the <head> of the document.
   *
   * @return void
   */
  protected function addPreConnections(): void
  {
    $connections = [
      'https://fonts.googleapis.com' => '',
      'https://fonts.gstatic.com'    => 'crossorigin',
    ];
    
    $link = /** @lang text */
      '<link rel="preconnect" href="%s" %s>';
    
    foreach ($connections as $connection => $modification) {
      echo sprintf($link, $connection, $modification) . PHP_EOL;
    }
  }
  
  /**
   * addAssets
   *
   * Enqueues the theme's scripts and styles.
   *
   * @return void
   */
  protected function addAssets(): void
  {
    $this->enqueue('//fonts.googleapis.com/css2?family=Noto+Sans+Display&family=Roboto+Slab&display=swap');
  }
  
  /**
   * customizeTitle
   *
   * Makes sure the <title> tag is exactly the way we want it to be.
   *
   * @return string
   */
  protected function customizeTitle(): string
  {
    return !is_front_page()
      ? sprintf('%s | %s', get_the_title(), 'Dashifen.com')
      : 'Dashifen.com';
  }
}