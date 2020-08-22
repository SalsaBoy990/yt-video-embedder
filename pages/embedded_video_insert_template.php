<h1><?php _e('Add new Youtube video', 'ag-yt-video-embedder'); ?></h1>


<div class="card bg-light">
  <div class="card-header">

    <h3 class="card-title">
      <?php _e('Add video details', 'ag-yt-video-embedder'); ?>
    </h3>
  </div>
  <div class="card-body">
    <div>
      <form action="#" method="post">
        <input type="hidden" name="videoid" value="">
        <div class="form-group mbhalf">
          <label for="title"><?php _e('Title', 'ag-yt-video-embedder'); ?></label><br />
          <input type="text" class="form-control regular-text" name="title" value="" />
        </div>
        <div class="form-group mbhalf">
          <label for="url"><?php _e('Url', 'ag-yt-video-embedder'); ?></label><br />
          <input type="url" class="form-control regular-text" name="url" value="" />
        </div>
        <div class="form-group mbhalf">
          <label for="start_time"><?php _e('Start time (in secs)', 'ag-yt-video-embedder'); ?></label><br />
          <input type="number" class="form-control" name="start_time" value="0" />
        </div>
        <div class="form-group mbhalf">
          <label for="privacy_enhanced">
            <?php
            printf(
              '%s <a href="%s" target="blank"><span class="ag-yt-video-embedder dashicons dashicons-info"></span></a>',
              __('Enhanced Privacy mode', 'ag-yt-video-embedder'),
              esc_url('https://support.google.com/youtube/answer/171780?hl=en')
            );
            ?>
          </label><br />
          <input type="checkbox" class="form-control regular-text" name="privacy_enhanced" value="Privacy Enhanced" />
        </div>

        <div class="form-group mbhalf">
          <label for="autoplay"><?php _e('Autoplay', 'ag-yt-video-embedder'); ?></label><br />
          <input type="checkbox" class="form-control regular-text" name="autoplay" value="Autoplay" />
        </div>
        <div class="form-group mbhalf">
          <label for="captions"><?php _e('Captions', 'ag-yt-video-embedder'); ?></label><br />
          <input type="checkbox" class="form-control regular-text" name="captions" value="Video Captions" />
        </div>

        <div class="mt1">
          <button type="submit" name="listaction" value="handleinsert" class="button-primary"><?php _e('Add embedded video', 'ag-yt-video-embedder'); ?></button>
        </div>
      </form>
    </div>
  </div>
</div>