<?php
namespace AG_YT_Video_Embedder;

class Embed_Group extends \WP_Widget
{
  // DB TABLE
  const TABLE_NAME = 'ag_yt_video_embedder';
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
      'classname'                   => 'widget_group_yt_embed',
      'description'                 => __('Youtube video group embed widget.', 'ag-yt-video-embedder'),
      'customize_selective_refresh' => true,
    );

    parent::__construct('group_yt_embed', __('Youtube Video Group Embed', 'ag-yt-video-embedder'), $widget_ops);
    $this->alt_option_name = 'widget_group_yt_embed';
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

    $heading = (!empty($instance['heading'])) ? $instance['heading'] : __('', 'ag-yt-video-embedder');


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

    
    // require view, populate values
    require AG_YT_VIDEO_EMBEDDER_PLUGIN_DIR . '/views/group_video_widget_template.php';
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
    $instance['heading']  = sanitize_text_field($new_instance['heading']);

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
    $heading   = isset($instance['heading']) ? esc_attr($instance['heading']) : '';

?>
    <div>
      <p><?php _e('Adds your embedded Youtube video group widget.', 'ag-yt-video-embedder'); ?></p>
      <p>
        <label for="<?php echo $this->get_field_id('heading'); ?>"><?php _e('Heading:', 'ag-yt-video-embedder'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('heading'); ?>" name="<?php echo $this->get_field_name('heading'); ?>" type="text" value="<?php echo $heading; ?>" />
      </p>

    </div>
<?php
  }


  
  public function create_embed_video_links(
    $url,
    $privacy_enhanced,
    $start_time,
    $autoplay,
    $captions
  ) {

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
