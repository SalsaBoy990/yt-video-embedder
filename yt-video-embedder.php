<?php

namespace AG_YT_Video_Embedder;
/*
Plugin Name: Youtube Video Embedder
Plugin URI: https://github.com/SalsaBoy990/yt-video-embedder
Description: You can easily embed videos from Youtube etc. responsively with Bootstrap 4. Multiple widgets and shortcodes available.
Version: 0.1
Author: András Gulácsi
Author URI: https://github.com/SalsaBoy990
License: GPLv2 or later
Text Domain: ag-yt-video-embedder
Domain Path: /languages
*/

require_once 'requires.php';



class AG_YT_Video_Embedder
{
  const br = '<br />';

  // TEXT DOMAIN
  const TEXT_DOMAIN = 'ag-yt-video-embedder';

  // DB TABLE
  const TABLE_NAME = 'ag_yt_video_embedder';

  // DB VERSION
  const DB_VERSION = '1.0';
  const OPTIONS_NAME = 'ag_yt_video_embedder_db_version';

  // expressions are not allowed for constans properties,
  // initialize a global constant instead
  // const PLUGIN_DIR = plugin_dir_path(__FILE__);

  const DEBUG = '0';  // 0 = NO debug output
  // 1 = screen output of debug statements
  const LOGGING = '1';  // 0 = NO log output

  // default link for single video widget/shortcode
  const DEFAULT_LINK = 'https://www.youtube.com/watch?v=BHACKCNDMW8';

  // embed baseurl
  const EMBED_BASE_URL = 'https://www.youtube.com/embed/';
  // embed baseurl in privacy-enhanced mode
  const EMBED_BASE_URL_PRIVACY = 'https://www.youtube-nocookie.com/embed/';


  // to store crud dependency
  public $crud;

  // to store shortcodes dependency
  public $shortcodes;


  // add some styling methods to the plugin (frontend and admin)
  use Add_Styles;

  // create shortcodes methods
  // use AG_YT_Video_Embedder_Shortcodes;
  // use AG_YT_Video_Embedder_Crud;


  /**
   * Constructor
   * - add hooks here
   * - needs crud dependency injected
   * @param AG_YT_Video_Embedder/Crud
   * @param AG_YT_Video_Embedder/Shortcodes
   * @return void
   */
  public function __construct(
    Crud $crud,
    Shortcodes $shortcodes
  ) {

    // initialize crud class
    $this->crud = $crud;

    // initialize shortcode class
    $this->shortcodes = $shortcodes;

    // load textdomain
    add_action('plugins_loaded', array($this, 'load_textdomain'));

    // add menu for the plugin
    add_action('admin_menu', array($this, 'embedder_admin_menu'));

    // register shortcode to view a single responsive YT video
    add_shortcode('single_video_embed', array($this->shortcodes, 'single_video_shortcode'));

    // register shortcode to view a group of YT videos (2-col grid responsive)
    add_shortcode('group_video_embed', array($this->shortcodes, 'group_video_shortcode'));


    // put bootstrap css into head (if your theme hase booststrap support, uncomment the 2 lines below
    add_action('wp_head', array($this, 'add_bootstrap_style'));
    add_filter('style_loader_tag', array($this, 'add_bootstrap_css_cdn_attributes'), 10, 2);


    // put the css into admin head
    add_action('admin_head', array($this, 'add_backend_styles'));

    // put the css before end of </body>
    add_action('wp_enqueue_scripts', array($this, 'add_frontend_styles'));


    // hook for our widget implementation
    add_action('widgets_init', array($this, 'register_widgets'));
  }



  // destructor
  public function ____destruct()
  {
  }


  // METHODS

  public static function load_textdomain()
  {
    // modified slightly from https://gist.github.com/grappler/7060277#file-plugin-name-php

    $domain = self::TEXT_DOMAIN;
    $locale = apply_filters('plugin_locale', get_locale(), $domain);

    load_textdomain($domain, trailingslashit(\WP_LANG_DIR) . $domain . '/' . $domain . '-' . $locale . '.mo');
    load_plugin_textdomain($domain, FALSE, basename(dirname(__FILE__)) . '/languages/');
  }

  // add integrity and crossorigin attributes
  public function add_bootstrap_css_cdn_attributes($html, $handle)
  {
    if ('ag-yt-embedder-bootstrap-css' === $handle) {
      return str_replace("media='all'", "integrity='sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z' crossorigin='anonymous'", $html);
    }
    return $html;
  }


  /**
   * Register the new widget.
   *
   * @see 'widgets_init'
   */
  public function register_widgets()
  {
    register_widget('\AG_YT_Video_Embedder\Embed_Single');
    register_widget('\AG_YT_Video_Embedder\Embed_Group');
  }



  /**
   * Register admin menu page
   * @return void
   */
  function embedder_admin_menu()
  {
    add_menu_page(
      __('Youtube Video Embedder', 'ag-yt-video-embedder'), // page title
      __('Your Embedded Video List', 'ag-yt-video-embedder'), // menu title
      'manage_options', // capability
      'youtube_video_embed_list', // menu slug
      array($this->crud, 'embedded_video_list'), // callback
      'dashicons-video-alt3' // icon
    );

    add_submenu_page(
      'youtube_video_embed_list', //parent slug
      __('Add new video', 'ag-yt-video-embedder'), // page title
      __('Add new', 'ag-yt-video-embedder'),  // menu title
      'manage_options', // capability
      'youtube_video_insert', // menu slug
      array($this->crud, 'embedded_video_list_insert') // callback
    );
  }


  /**
   * Create a wp db table (if not exists) when plugin is activated
   */
  public static function video_embedder_create_table()
  {
    global $wpdb;

    $table_name = $wpdb->prefix . self::TABLE_NAME;

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(100) NOT NULL,
    `url` VARCHAR(200) NOT NULL,
    `privacy_enhanced` BOOLEAN NOT NULL,
    `start_time` INT(10) UNSIGNED NOT NULL,
    `autoplay` BOOLEAN NOT NULL,
    `captions` BOOLEAN NOT NULL,
    PRIMARY KEY (`id`)
  ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // check if option already exists
    if (get_option(self::OPTIONS_NAME) == false) {
      // add new option if option not exists
      add_option(self::OPTIONS_NAME, self::DB_VERSION);
    } else {
      // update option if exists
      update_option(self::OPTIONS_NAME, self::DB_VERSION);
    }

    return;
  }


  // This code will only run when plugin is deleted
  // it will drop the custom database table, delete wp_option record (if exists)
  public static function video_embedder_delete_table()
  {

    global $wpdb;
    $table_name = $wpdb->prefix . self::TABLE_NAME;
    $wpdb->query("DROP TABLE IF EXISTS $table_name");

    // check if option exists, then delete
    if (!get_option(self::OPTIONS_NAME) === false) {
      delete_option(self::OPTIONS_NAME);
    }

    echo '<div class="notice notice-success is-dismissible">Youtube Video Embedder plugin successfully uninstalled.</p></div>';
  }

  // used for debugging and logging purposes
  public static function video_embedder_deactivation()
  {
  }
}


// instantiate class, with dependency injection
$ag_yt_video_embedder_class = new AG_YT_Video_Embedder(
  new Crud(),
  new Shortcodes()
);


// ACTIVATE, DEACTIVATE, UNINSTALL PLUGIN
// we don't need to do anything when deactivation, just creating debug and log messages
register_deactivation_hook(__FILE__, '\AG_YT_Video_Embedder\AG_YT_Video_Embedder::video_embedder_deactivation');

// create table if not exists when activating the plugin
register_activation_hook(__FILE__, '\AG_YT_Video_Embedder\AG_YT_Video_Embedder::video_embedder_create_table');

// delete table when uninstalling the plugin
register_uninstall_hook(__FILE__, '\AG_YT_Video_Embedder\AG_YT_Video_Embedder::video_embedder_delete_table');
