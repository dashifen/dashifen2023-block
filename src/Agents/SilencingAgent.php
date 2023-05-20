<?php

namespace Dashifen\Theme2023\Agents;

use WP_Admin_Bar;
use Dashifen\Theme2023\Theme;
use Dashifen\WPHandler\Agents\AbstractThemeAgent;
use Dashifen\WPHandler\Handlers\HandlerException;

/**
 * SilencingAgent
 *
 * A silly name for an agent that turns off everything to do with Comments when
 * using this theme.  This should both help to reduce bloat in the Dashboard
 * but also prevent spammers from trying to cram comments into the databasde
 * when we're not going to be looking at them.
 *
 * @property Theme $handler
 */
class SilencingAgent extends AbstractThemeAgent
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
      $this->addAction('admin_init', 'removeCommentSupport', PHP_INT_MAX);
      $this->addAction('admin_init', 'removeDashboardCommentWidget', PHP_INT_MAX);
      $this->addAction('admin_init', 'redirectCommentRequests', PHP_INT_MAX);
      $this->addAction('admin_menu', 'removeCommentMenuItem', PHP_INT_MAX);
      $this->addAction('admin_bar_menu', 'removeAdminBarCommentItems', PHP_INT_MAX);
      
      // these last three removals can utilize Core functions as their
      // callbacks.  because these are not a part of this object, we don't
      // use the addFilter method to do so.  instead, we use core's add_filter
      // function
      
      add_filter('pings_open', '__return_false', PHP_INT_MAX);
      add_filter('comments_open', '__return_false', PHP_INT_MAX);
      add_filter('comments_array', '__return_empty_array', PHP_INT_MAX);
    }
  }
  
  /**
   * removeCommentSupport
   *
   * Removes the support for comments (and trackbacks) from any post types
   * that support them.
   *
   * @return void
   */
  protected function removeCommentSupport()
  {
    // for each of our post types we want to see if they support either
    // comments or trackbacks (or both).  if so, we remove that support.
    
    foreach (get_post_types() as $postType) {
      foreach (['comments', 'trackbacks'] as $feature) {
        if (post_type_supports($postType, $feature)) {
          remove_post_type_support($postType, $feature);
        }
      }
    }
  }
  
  /**
   * removeDashboardCommentWidget
   *
   * Removes the dashboard "Recent Comments" widget because we won't need it
   * if we're turning off comments entirely.
   *
   * @return void
   */
  protected function removeDashboardCommentWidget(): void
  {
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
  }
  
  /**
   * redirectCommentRequests
   *
   * If someone tries to visit the pages that handle comments, we send them
   * back to the Dashboard homepage.
   *
   * @return void
   */
  protected function redirectCommentRequests()
  {
    global $pagenow;
    
    if ($pagenow === 'edit-comments.php' || $pagenow === 'options-discussion.php') {
      wp_redirect(admin_url());
      exit;
    }
  }
  
  /**
   * removeCommentMenuItem
   *
   * Removes the comments menu item from the Dashboard.
   *
   * @return void
   */
  protected function removeCommentMenuItem()
  {
    remove_menu_page('edit-comments.php');
    remove_submenu_page('options-general.php', 'options-discussion.php');
  }
  
  /**
   * removeMultisiteAdminBarCommentItems
   *
   * Removes the comments item from the admin bar for sites that the current
   * user has access to.
   *
   * @param WP_Admin_Bar $adminBar
   *
   * @return void
   */
  protected function removeAdminBarCommentItems(WP_Admin_Bar $adminBar): void
  {
    $adminBar->remove_menu('comments');
    
    if (is_multisite()) {
      foreach (get_blogs_of_user(get_current_user_id()) as $site) {
        
        // the get_blogs_of_user function returns an array of objects with no
        // schema at the moment.  but, each of those objects has a userblog_id
        // property which, conveniently enough, gives us the blog ID for the
        // site.  we can use that to remove the admin bar node that would show
        // comments for the site.
        
        $adminBar->remove_node('blog-' . $site->userblog_id . '-c');
      }
    }
  }
}