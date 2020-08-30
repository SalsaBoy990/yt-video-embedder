<?php

// always use namespaces to avoid
// class/function/const name collisions
namespace AG\YTVideoEmbedder;

defined('ABSPATH') or die();

use AG\YTVideoEmbedder\CSSStyles as CSSStyles;

use AG\YTVideoEmbedder\Widget\EmbedSingleVideo;

use AG\YTVideoEmbedder\Widget\EmbedVideoGroup;

class YTVideoEmbedder
{
    // add styling methods to the plugin
    use CSSStyles;

    // TEXT DOMAIN
    private const TEXT_DOMAIN = 'ag-yt-video-embedder';

    // DB TABLE
    private const TABLE_NAME = 'ag_yt_video_embedder';

    // DB VERSION
    private const DB_VERSION = '1.0';
    private const OPTIONS_NAME = 'ag_yt_video_embedder_db_version';

    // expressions are not allowed for constans properties,
    // initialize a global constant instead
    // const PLUGIN_DIR = plugin_dir_path(__FILE__);

    private const DEBUG = '0';  // 0 = NO debug output
    // 1 = screen output of debug statements
    private const LOGGING = '1';  // 0 = NO log output

    // default link for single video widget/shortcode
    private const DEFAULT_LINK = 'https://www.youtube.com/watch?v=BHACKCNDMW8';

    // embed baseurl
    private const EMBED_BASE_URL = 'https://www.youtube.com/embed/';
    // embed baseurl in privacy-enhanced mode
    private const EMBED_BASE_URL_PRIVACY = 'https://www.youtube-nocookie.com/embed/';

    // to store crud dependency
    private $crud;

    // to store shortcodes dependency
    private $shortcodes;


    /**
     * Constructor
     * - add hooks here
     * - needs crud dependency injected
     * @param AG\YTVideoEmbedder/Crud
     * @param AG\YTVideoEmbedder/Shortcodes
     * @return void
     */
    public function __construct(
        \AG\YTVideoEmbedder\Crud\Crud $crud,
        \AG\YTVideoEmbedder\ShortCodes\ShortCodes $shortcodes
    ) {

        // initialize crud class
        $this->crud = $crud;

        // initialize shortcode class
        $this->shortcodes = $shortcodes;


        // load textdomain
        add_action('plugins_loaded', array($this, 'loadTextdomain'));

        // add menu for the plugin
        add_action('admin_menu', array($this, 'embedderAdminMenu'));

        // register shortcode to view a single responsive YT video
        add_shortcode('single_video_embed', array($this->shortcodes, 'singleVideoShortcode'));

        // register shortcode to view a group of YT videos (2-col grid responsive)
        add_shortcode('group_video_embed', array($this->shortcodes, 'groupVideoShortcode'));

        // put bootstrap css into head (if your theme hase booststrap support, uncomment the 2 lines below
        add_action('wp_head', array($this, 'addBootstrapStyle'));
        add_filter('style_loader_tag', array($this, 'addBootstrapCSSCDNAttributes'), 10, 2);


        // put the css into admin head
        // add_action('admin_head', array($this, 'addBackendStyles'));
        add_action('admin_enqueue_scripts', array($this, 'adminLoadScripts'));

        // put the css before end of </body>
        add_action('wp_enqueue_scripts', array($this, 'addFrontendStyles'));

        // hook for our widget implementation
        add_action('widgets_init', array($this, 'registerWidgets'));


        // add ajax script
        add_action('wp_enqueue_scripts', function () {
            // for Group Widget
            wp_enqueue_script(
                'ag-yt-video-embedder-widget-js',
                plugin_dir_url(dirname(__FILE__)) . 'js/ytVideoEmbedderWidget.js',
                array('jquery')
            );

            // enable ajax on frontend
            wp_localize_script('ag-yt-video-embedder-widget-js', 'YTVideoEmbedderAjaxWidget', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'security' => wp_create_nonce('ag-ytvid-oe46szhe')
            ));


            // for Group Shortcode
            wp_enqueue_script(
                'ag-yt-video-embedder-shortcode-js',
                plugin_dir_url(dirname(__FILE__)) . 'js/ytVideoEmbedderShortcode.js',
                array('jquery')
            );

            // enable ajax on frontend
            wp_localize_script('ag-yt-video-embedder-shortcode-js', 'YTVideoEmbedderAjaxShortcode', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'security' => wp_create_nonce('ag-ytvid-r967zzinmx')
            ));
        });

        // connect AJAX request with PHP hooks Group Widget
        add_action('wp_ajax_ag_yt_video_embedder_widget_ajax_action', array($this, 'ajaxHandlerWidget'));
        add_action('wp_ajax_nopriv_ag_yt_video_embedder_widget_ajax_action', array($this, 'ajaxHandlerWidget'));

        // connect AJAX request with PHP hooks Group Shortcode
        add_action('wp_ajax_ag_yt_video_embedder_shortcode_ajax_action', array($this, 'ajaxHandlerShortcode'));
        add_action('wp_ajax_nopriv_ag_yt_video_embedder_shortcode_ajax_action', array($this, 'ajaxHandlerShortcode'));
    }



    // destructor
    public function ____destruct()
    {
    }


    public function ajaxHandlerWidget()
    {
        if (check_ajax_referer('ag-ytvid-oe46szhe', 'security')) {
            // get all videos from db
            $res = $this->crud->list(self::TABLE_NAME);

            $formData = [];

            $i = 0;
            foreach ($res as $row) {
                $video_title      = $row->title;
                $url              = $row->url;
                $privacy_enhanced = $row->privacy_enhanced;
                $start_time       = $row->start_time;
                $autoplay         = $row->autoplay;
                $captions         = $row->captions;

                // create embed url
                $url_embed = $this->createEmbedVideoLinks(
                    $url,
                    $privacy_enhanced,
                    $start_time,
                    $autoplay,
                    $captions
                );
                $formData[$i] = array(
                    'video_title' => $video_title,
                    'embed_url'   => $url_embed
                );
                $i++;
            }

            wp_send_json_success($formData, 200);
        } else {
            wp_send_json_error();
        }
        wp_die();
    }

    public function ajaxHandlerShortcode()
    {
        if (check_ajax_referer('ag-ytvid-r967zzinmx', 'security')) {
            $args = $_REQUEST;
            $args = $_REQUEST['args'];

            // generate content
            $content = $this->shortcodes->groupVideoShortcode($args);
            wp_send_json_success($content, 200);
        } else {
            wp_send_json_error();
        }
        wp_die();
    }


    // METHODS
    public function loadTextdomain(): void
    {
        // modified slightly from https://gist.github.com/grappler/7060277#file-plugin-name-php

        $domain = self::TEXT_DOMAIN;
        $locale = apply_filters('plugin_locale', get_locale(), $domain);

        load_textdomain($domain, trailingslashit(\WP_LANG_DIR) . $domain . '/' . $domain . '-' . $locale . '.mo');
        load_plugin_textdomain($domain, false, basename(dirname(__FILE__, 2)) . '/languages/');
    }


    // add integrity and crossorigin attributes
    public function addBootstrapCssCdnAttributes(string $html, string $handle): string
    {
        if ('ag-yt-embedder-bootstrap-css' === $handle) {
            return str_replace(
                "media='all'",
                "integrity='sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z' crossorigin='anonymous'",
                $html
            );
        }
        return $html;
    }


    /**
     * Register the new widget.
     *
     * @see 'widgets_init'
     */
    public function registerWidgets(): void
    {
        register_widget('\AG\YTVideoEmbedder\Widget\EmbedSingleVideo');
        register_widget('\AG\YTVideoEmbedder\Widget\EmbedVideoGroup');
    }



    /**
     * Register admin menu page
     * @return void
     */
    public function embedderAdminMenu(): void
    {
        global $ag_yt_embedder_admin_page;
        $ag_yt_embedder_admin_page = add_menu_page(
            __('Youtube Video Embedder', 'ag-yt-video-embedder'), // page title
            __('Your Embedded Video List', 'ag-yt-video-embedder'), // menu title
            'manage_options', // capability
            'youtube_video_embed_list', // menu slug
            array($this->crud, 'listTable'), // callback
            'dashicons-video-alt3' // icon
        );

        global $ag_yt_embedder_admin_submenu_page;
        $ag_yt_embedder_admin_submenu_page = add_submenu_page(
            'youtube_video_embed_list', //parent slug
            __('Add new video', 'ag-yt-video-embedder'), // page title
            __('Add new', 'ag-yt-video-embedder'),  // menu title
            'manage_options', // capability
            'youtube_video_insert', // menu slug
            array($this->crud, 'insertRecord') // callback
        );
    }

    // add some styling to the plugin admin UI
    public function adminLoadScripts($hook)
    {
        if ($hook != 'toplevel_page_youtube_video_embed_list' && $hook != 'a-beagyazott-videoid-listaja_page_youtube_video_insert'
        ) {
            return;
        }

        wp_enqueue_style(
            'ag-yt-embedder-backend',
            plugins_url('assets/css/yt-video-embed-backend.css', dirname(__FILE__, 1))
        );
    }


    public function createEmbedVideoLinks(
        string $url,
        int $privacy_enhanced,
        int $start_time,
        int $autoplay,
        int $captions
    ): string {

        // generate embed url
        $url_exploded = explode('=', $url);
        $url_exploded = $url_exploded[1];


        // if privacy-enhanced mode on
        if ($privacy_enhanced == 1) {
            $url_embed = self::EMBED_BASE_URL_PRIVACY;
        } else {
            $url_embed = self::EMBED_BASE_URL;
        }

        $url_embed = $url_embed . $url_exploded;


        // add autoplay query param
        if ($start_time > 0) {
            $url_embed .= '?&start=' . $start_time;
        }

        // add autoplay query param
        if ($autoplay == 1) {
            $url_embed .= ($start_time) ? '&autoplay=1' : '?&autoplay=1';
        }

        // add captions query param
        if ($captions == 1) {
            if ($start_time || $autoplay) {
                $url_embed .= '&cc_load_policy=1';
            } else {
                $url_embed .= '?&cc_load_policy=1';
            }
        }


        return $url_embed;
    }



    /**
     * Create a wp db table (if not exists) when plugin is activated
     */
    public static function activateVideoEmbedder(): void
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
        if (!get_option(self::OPTIONS_NAME)) {
            // add new option if option not exists
            add_option(self::OPTIONS_NAME, self::DB_VERSION);
        } else {
            // update option if exists
            update_option(self::OPTIONS_NAME, self::DB_VERSION);
        }
    }


    // This code will only run when plugin is deleted
    // it will drop the custom database table, delete wp_option record (if exists)
    public static function deleteVideoEmbedder(): void
    {
        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_NAME;
        $wpdb->query("DROP TABLE IF EXISTS $table_name");

        // check if option exists, then delete
        if (get_option(self::OPTIONS_NAME)) {
            delete_option(self::OPTIONS_NAME);
        }
        echo '<div class="notice notice-success is-dismissible">' .
            'Youtube Video Embedder plugin successfully uninstalled.</p></div>';
    }
}
