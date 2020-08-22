<?php
namespace AG_YT_Video_Embedder;

trait Add_Styles
{

  public static function add_bootstrap_style()
  {
    wp_register_style('ag-yt-embedder-bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
    wp_enqueue_style('ag-yt-embedder-bootstrap-css');

 
  }

  // add some styling to the plugin admin UI
  public static function add_backend_styles()
  {
    wp_register_style('ag-yt-embedder-backend', plugins_url('/yt-video-embedder/assets/css/yt-video-embed-backend.css'));
    wp_enqueue_style('ag-yt-embedder-backend');
  }





  // add some styling to the plugin admin UI
  public static function add_frontend_styles()
  {
    wp_register_style('ag-yt-embedder-frontend', plugins_url('/yt-video-embedder/assets/css/yt-video-embed-frontend.css'));
    wp_enqueue_style('ag-yt-embedder-frontend');
  }
}
