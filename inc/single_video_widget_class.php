<?php
namespace AG_YT_Video_Embedder;

class Embed_Single extends \WP_Widget
{

  // default link for single video widget/shortcode
  const DEFAULT_LINK = 'https://www.youtube.com/watch?v=BHACKCNDMW8';

  // embed baseurl
  const EMBED_BASE_URL = 'https://www.youtube.com/embed/';
  // embed baseurl in privacy-enhanced mode
  const EMBED_BASE_URL_PRIVACY = 'https://www.youtube-nocookie.com/embed/';


  /**
   * Sets up a new AG_YT_Video_Embed_Single widget instance.
   *
   * @since 2.8.0
   */
  public function __construct()
  {
    $widget_ops = array(
      'classname'                   => 'widget_single_yt_embed',
      'description'                 => __('Single Youtube video embed widget.', 'ag-yt-video-embedder'),
      'customize_selective_refresh' => true,
    );

    parent::__construct('single_yt_embed', __('Single Youtube Video Embed', 'ag-yt-video-embedder'), $widget_ops);
    $this->alt_option_name = 'widget_single_yt_embed';
  }

  /**
   * Outputs the content for the current AG_YT_Video_Embed_Single widget instance.
   *
   * @since 2.8.0
   *
   * @param array $args     Display arguments including 'before_title', 'after_title',
   *                        'before_widget', and 'after_widget'.
   * @param array $instance Settings for the current Recent Reviews widget instance.
   */
  public function widget($args, $instance)
  {
    extract($args);

    if (!isset($args['widget_id'])) {
      $args['widget_id'] = $this->id;
    }

    $title = (!empty($instance['title'])) ? $instance['title'] : __('', 'ag-yt-video-embedder');

    /** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
    // $title = apply_filters('widget_title', $title, $instance, $this->id_base);

    $url = $instance['url'];

    // generate embed url
    $url_exploded = explode('=', $url);
    $url_exploded = $url_exploded[1];

    // get properties
    $privacy = $instance['privacy_enhanced'];
    $start_time = $instance['start_time'];
    $autoplay = $instance['autoplay'];
    $captions = $instance['captions'];

    // if privacy-enhanced mode on
    if ($privacy == 1) {
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


    // require view, populate values
    require AG_YT_VIDEO_EMBEDDER_PLUGIN_DIR . '/views/single_video_widget_template.php';
  }

  /**
   * Handles updating the settings for the current AG_YT_Video_Embed_Single widget instance.
   *
   * @since 2.8.0
   *
   * @param array $new_instance New settings for this instance as input by the user via
   *                            WP_Widget::form().
   * @param array $old_instance Old settings for this instance.
   * @return array Updated settings to save.
   */
  public function update($new_instance, $old_instance)
  {
    $instance           = $old_instance;
    $instance['title']  = sanitize_text_field($new_instance['title']);
    $instance['url']    = sanitize_text_field($new_instance['url']);

    $instance['start_time']         = filter_var($new_instance['start_time'], FILTER_SANITIZE_NUMBER_INT);
    $instance['privacy_enhanced']   = $new_instance['privacy_enhanced'] ? 1 : 0;
    $instance['autoplay']           = $new_instance['autoplay'];
    $instance['captions']           = $new_instance['captions'];

    return $instance;
  }

  /**
   * Outputs the settings form for the AG_YT_Video_Embed_Single widget.
   *
   * @since 2.8.0
   *
   * @param array $instance Current settings.
   */
  public function form($instance)
  {
    $title              = isset($instance['title']) ? esc_attr($instance['title']) : '';
    $url                = isset($instance['url']) ? esc_url($instance['url']) : self::DEFAULT_LINK;
    $start_time         = isset($instance['start_time']) ? intval(esc_attr($instance['start_time']), 10) : 0;
    $privacy_enhanced   = esc_attr($instance['privacy_enhanced']);
    $autoplay           = esc_attr($instance['autoplay']);
    $captions           = esc_attr($instance['captions']);

?>
    <div>
      <p><?php _e('Adds a single embedded Youtube video widget.', 'ag-yt-video-embedder'); ?></p>
      <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'ag-yt-video-embedder'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
      </p>

      <p>
        <label for="<?php echo $this->get_field_id('url'); ?>"><?php _e('Youtube video link to embed:', 'ag-yt-video-embedder'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('url'); ?>" name="<?php echo $this->get_field_name('url'); ?>" type="url" value="<?php echo $url; ?>" />
      </p>

      <p>
        <label for="<?php echo $this->get_field_id('start_time'); ?>"><?php _e('Start time in seconds:', 'ag-yt-video-embedder'); ?></label>
        <input size="4" class="widefat" id="<?php echo $this->get_field_id('start_time'); ?>" name="<?php echo $this->get_field_name('start_time'); ?>" type="number" min="0" step="1" value="<?php echo $start_time; ?>" />
      </p>

      <p>
        <label for="<?php echo $this->get_field_id('privacy_enhanced'); ?>"><?php _e('Enhanced Privacy mode:', 'ag-yt-video-embedder'); ?></label>
        <input type="checkbox" name="<?php echo $this->get_field_name('privacy_enhanced'); ?>" value="1" <?php checked($privacy_enhanced, 1); ?> />
      </p>

      <p>
        <label for="<?php echo $this->get_field_id('autoplay'); ?>"><?php _e('Autoplay:', 'ag-yt-video-embedder'); ?></label>
        <input type="checkbox" name="<?php echo $this->get_field_name('autoplay'); ?>" value="1" <?php checked($autoplay, 1); ?> />
      </p>

      <p>
        <label for="<?php echo $this->get_field_id('captions'); ?>"><?php _e('Captions:', 'ag-yt-video-embedder'); ?></label>
        <input type="checkbox" name="<?php echo $this->get_field_name('captions'); ?>" value="1" <?php checked($captions, 1); ?> />
      </p>


    </div>
<?php
  }
}
