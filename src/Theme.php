<?php

namespace Dashifen\Theme2023;

use Dashifen\WPHandler\Handlers\HandlerException;
use Dashifen\WPHandler\Handlers\Themes\AbstractThemeHandler;

class Theme extends AbstractThemeHandler
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
    $this->addAction('init', 'maybeDisableEmojis', PHP_INT_MAX);
    $this->addAction('init', 'removeSvgFilters', PHP_INT_MAX);
    $this->addAction('wp_enqueue_scripts', 'disableDefaultStyles', PHP_INT_MAX);
  }
  
  /**
   * disableEmojis
   *
   * Disables the code that would turn emoticons in to emoji.  Now that we can
   * put emojis into our text with the keyboard, we don't need it 👍🏻
   *
   * @return void
   */
  protected function maybeDisableEmojis(): void
  {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
  }
  
  /**
   * removeSvgFilters
   *
   * Removes the SVG information that's added to the DOM by Core because we're
   * not using them and, therefore, this speeds up the site a bit.
   *
   * @return void
   */
  protected function removeSvgFilters(): void
  {
    remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
    remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');
  }
  
  /**
   * disableDefaultStyles
   *
   * Turns off the block-library generated CSS that appears as inline styles
   * in the DOM.
   *
   * @return void
   */
  protected function disableDefaultStyles(): void
  {
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    
    if (!is_user_logged_in()) {
      
      // the dashicons styles are needed for the WP admin bar.  but, if the
      // visitor isn't logged in, then they don't see that bar.  because this
      // theme doesn't use dashicons except in the admin bar, we therefore
      // deregister them for anonymous visitors to help speed up the site.
      
      wp_deregister_style('dashicons');
    }
  }
}