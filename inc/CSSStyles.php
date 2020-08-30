<?php

namespace AG\YTVideoEmbedder;

defined('ABSPATH') or die();

trait CSSStyles
{

    public static function addBootstrapStyle()
    {
        wp_register_style(
            'ag-yt-embedder-bootstrap-css',
            'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css'
        );
        wp_enqueue_style('ag-yt-embedder-bootstrap-css');
    }

    // add some styling to the plugin admin UI
    public static function addFrontendStyles()
    {
        wp_enqueue_style(
            'ag-yt-embedder-frontend',
            plugins_url('/yt-video-embedder/assets/css/yt-video-embed-frontend.css')
        );
    }
}
