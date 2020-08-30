<?php

namespace AG\YTVideoEmbedder\Shortcodes;

defined('\ABSPATH') or die();

class Shortcodes
{
    private const DEFAULT_LINK = 'https://www.youtube.com/watch?v=BHACKCNDMW8';

    // DB TABLE
    private const TABLE_NAME = 'ag_yt_video_embedder';

    // embed baseurl
    private const EMBED_BASE_URL = 'https://www.youtube.com/embed/';

    // embed baseurl in privacy-enhanced mode
    private const EMBED_BASE_URL_PRIVACY = 'https://www.youtube-nocookie.com/embed/';


    public function __construct()
    {
    }
    public function __destruct()
    {
    }


    // create a shortcode for our single video embeds
    public function singleVideoShortcode(array $atts, string $content = null)
    {

        global $post;

        extract(shortcode_atts(array(
            'url'             => self::DEFAULT_LINK,
            'title'           => __('This is a default video, add your url in shortcode'),
            'start_time'      => 0,
            'heading_level'   => 'h4',
            'font_weight'     => '700',
            'privacy'         => 'off',
            'autoplay'        => 'off',
            'captions'        => 'off'
        ), $atts));

        // escape url
        $url = esc_url($url);
        $url_exploded = explode('=', $url);

        if ($privacy === 'on') {
            $url_embed = self::EMBED_BASE_URL_PRIVACY . $url_exploded[1];
        } else {
            $url_embed = self::EMBED_BASE_URL . $url_exploded[1];
        }

        if ($start_time > 0 || $autoplay === 'on' || $captions === 'on') {
            $url_embed .= '?';
        }

        if ($start_time > 0) {
            $url_embed .= ('start=' . $start_time);
        }
        if ($autoplay === 'on') {
            $url_embed .= '&autoplay=1';
        }
        if ($captions === 'on') {
            $url_embed .= '&cc_load_policy=1';
        }
        if ($privacy === 'on') {
            $url_embed = self::EMBED_BASE_URL_PRIVACY;
        }


        // escape user input!
        $title = esc_html($title);
        $title = wp_strip_all_tags($title);



        $font_weight = esc_html($font_weight);
        $font_weight = intval($font_weight, 10);


        if ($font_weight === 700) {
            $heading_class = 'font-weight-bold';
        } else if ($font_weight === 400) {
            $heading_class = 'font-weight-normal';
        }



        $heading_level = esc_html($heading_level);
        switch ($heading_level) {
            case 'h2':
                $title = '<h2 class="' . $heading_class . '">' . $title . '</h2>';
                break;
            case 'h3':
                $title = '<h3 class="' . $heading_class . '">' . $title . '</h3>';
                break;
            case 'h4':
                $title = '<h4 class="' . $heading_class . '">' . $title . '</h4>';
                break;
            case 'h5':
                $title = '<h5 class="' . $heading_class . '">' . $title . '</h5>';
                break;
            case 'h6':
                $title = '<h6 class="' . $heading_class . '">' . $title . '</h6>';
                break;
            default:;
        }



        // start buffering
        ob_start();

        // view
        require(AG_YT_VIDEO_EMBEDDER_PLUGIN_DIR . 'pages/single_video_shortcode.php');

        // get buffer content
        $content = ob_get_clean();

        return $content;
    }


    // create a shortcode for our video group embeds
    public function groupVideoShortcode(array $atts, $content = null): string
    {
        // display member list in a admin table
        // GET request
        global $wpdb;
        $valid = true;

        $sql = "SELECT * FROM " . $wpdb->prefix . self::TABLE_NAME;

        $formData = $wpdb->get_results($sql);

        if (!$formData) {
            $valid = false;
            echo $sql . '- This form is invalid.';
        }


        global $post;

        extract(shortcode_atts(array(
            'heading'        => __('This is a default video, add your url in shortcode'),
            'heading_level'  => 'h3',
            'font_weight'    => '700',
            'limit'          => 0,
            'order_by'       => 'date', // title
            'order'          => 'DESC', // ASC
        ), $atts));


        // escape user input!
        $heading = wp_strip_all_tags($heading);
        $heading = htmlspecialchars($heading);
        $heading = htmlentities($heading);

        $font_weight = esc_html($font_weight);
        $font_weight = intval($font_weight, 10);


        $heading_class = 'mt-5';
        if ($font_weight === 700) {
            $heading_class .= ' font-weight-bold';
        } else if ($font_weight === 400) {
            $heading_class .= ' font-weight-normal';
        }



        $heading_level = esc_html($heading_level);
        switch ($heading_level) {
            case 'h2':
                $heading = '<h2 class="' . $heading_class . '">' . $heading . '</h2>';
                break;
            case 'h3':
                $heading = '<h3 class="' . $heading_class . '">' . $heading . '</h3>';
                break;
            case 'h4':
                $heading = '<h4 class="' . $heading_class . '">' . $heading . '</h4>';
                break;
            case 'h5':
                $heading = '<h5 class="' . $heading_class . '">' . $heading . '</h5>';
                break;
            case 'h6':
                $heading = '<h6 class="' . $heading_class . '">' . $heading . '</h6>';
                break;
            default:;
        }



        // start buffering
        ob_start();

        // view
        require(AG_YT_VIDEO_EMBEDDER_PLUGIN_DIR . 'pages/group_video_shortcode.php');

        // get buffer content
        $content = ob_get_clean();

        return $content;
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
}
