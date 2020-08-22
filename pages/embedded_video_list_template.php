<?php
// display member list in a admin table
// GET request
global $wpdb;
$valid = true;

$sql = "SELECT * FROM " . $wpdb->prefix . self::TABLE_NAME;

$formData = $wpdb->get_results($sql);

// print_r($formData);

if (!$formData) {
  $valid = false;
  echo $sql . '- This form is invalid.';
}

?>
<h1 class="mt1 mb1"><?php _e('Manage Your Youtube Video Embeds', 'ag-yt-video-embedder'); ?></h1>
<form action="" method="post" class="mb1">
  <input type="hidden" name="listaction" value="insert">
  <button type="submit" class="button-primary"><span class="ag-yt-video-embedder dashicons dashicons-plus"></span><?php _e('Add new video', 'ag-yt-video-embedder'); ?></button>
</form>

<div class="ag-yt-video-embedder-wrapper">
  <table class="ag-yt-video-embedder widefat table table-striped">
    <thead>
      <tr>
        <!-- <th scope="col">#</th> -->
        <th scope="col"><?php _e('Action', 'ag-yt-video-embedder'); ?></th>
        <th scope="col"><?php _e('Title', 'ag-yt-video-embedder'); ?></th>
        <th scope="col"><?php _e('Video', 'ag-yt-video-embedder'); ?></th>
        <th scope="col"><?php _e('Url', 'ag-yt-video-embedder'); ?></th>
        <th scope="col">
          <?php
          printf(
            '%s <a href="%s" target="blank"><span class="ag-yt-video-embedder dashicons dashicons-info"></span></a>',
            __('Privacy mode', 'ag-yt-video-embedder'),
            esc_url('https://support.google.com/youtube/answer/171780?hl=en')
          );
          ?>
        </th>
        <th scope="col"><?php _e('Start in secs', 'ag-yt-video-embedder'); ?></th>
        <th scope="col"><?php _e('Autoplay', 'ag-yt-video-embedder'); ?></th>
        <th scope="col"><?php _e('Captions', 'ag-yt-video-embedder'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php
      if ($valid) :
        foreach ($formData as $row) :
          $id               = $row->id;
          $title            = $row->title;
          $url              = $row->url;
          $privacy_enhanced = $row->privacy_enhanced;
          $start_time       = $row->start_time;
          $autoplay         = $row->autoplay;
          $captions         = $row->captions;

          $url_exploded = explode('=', $url);
          $url_embed = self::EMBED_BASE_URL_PRIVACY . $url_exploded[1];
      ?>
          <tr>
            <form action="" method="post">
              <input type="hidden" name="listaction" value="edit">
              <input type="hidden" name="videoid" value="<?php echo $id ?>">
              <!-- <td><?php echo $id; ?></td> -->
              <td>
                <div class="btn-group" role="group">
                  <button type="submit" class="button-secondary"><span class="ag-yt-video-embedder dashicons dashicons-edit"></span><?php _e('Edit', 'ag-yt-video-embedder'); ?></button>
                </div>
              </td>

              <td class="medium-col"><?php echo $title; ?></td>
              <td class="medium-col">
                <div class="single-video-embed">
                  <div class="embed-responsive embed-responsive-16by9 m-ml-16px">
                    <iframe class="embed-responsive-item" src="<?php echo $url_embed; ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe>
                  </div>
                </div>
              <td class="medium-col"><a href="<?php echo $url; ?>" target="blank"><?php echo $url; ?></a></td>
              <td class="small-col"><?php echo ($privacy_enhanced == 1) ? __('enabled', 'ag-yt-video-embedder') : __('disabled', 'ag-yt-video-embedder'); ?></td>
              <td class="small-col"><?php echo $start_time; ?></td>
              <td class="small-col"><?php echo ($autoplay == 1) ? __('enabled', 'ag-yt-video-embedder') : __('disabled', 'ag-yt-video-embedder'); ?></td>
              <td class="small-col"><?php echo ($captions == 1) ? __('enabled', 'ag-yt-video-embedder') : __('disabled', 'ag-yt-video-embedder'); ?></td>
            </form>
          </tr>
      <?php
        endforeach;
      endif;
      ?>
    </tbody>
  </table>
  <?php printf(
    '<p>%s <a href="https://support.google.com/youtube/answer/171780?hl=en" target="blank">%s</a>.</p>',
    __('Learn more about', 'ag-yt-video-embedder'),
    __('Youtube video embed settings', 'ag-yt-video-embedder')
  ); ?>
</div>