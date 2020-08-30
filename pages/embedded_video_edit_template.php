<?php
// edit form for simple member

global $wpdb;
$valid = true;

// prepare get statement protect against SQL inject
$sql = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . self::TABLE_NAME . " WHERE id = %d", $id);

$row = $wpdb->get_row($sql);

// get the values for the current video record
$title            = $row->title;
$url              = $row->url;
$privacy_enhanced = $row->privacy_enhanced;
$start_time       = $row->start_time;
$autoplay         = $row->autoplay;
$captions         = $row->captions;

$url_exploded = explode('=', $url);
$url_embed = self::EMBED_BASE_URL_PRIVACY . $url_exploded[1];


// print_r($formData);

if (!$row) {
    $valid = false;
    echo $sql . '- This form is invalid.';
}
?>


<h1><?php _e('Edit Youtube video details', 'ag-yt-video-embedder'); ?></h1>


<div class="card bg-light">
    <div class="card-header">

        <h3 class="card-title">
            <?php _e('Video details', 'ag-yt-video-embedder'); ?>
        </h3>
    </div>
    <div class="card-body">
        <div>
            <form action="#" method="post">
                <input type="hidden" name="videoid" value="<?php echo $id; ?>">
                <?php wp_nonce_field('ag_ytvid_embed_edit', 'ytvid_embed_admin_edit_security'); ?>
                <div class="single-video-embed">
                    <div class="embed-responsive embed-responsive-16by9 m-ml-16px">
                        <iframe class="embed-responsive-item" src="<?php echo $url_embed; ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe>
                    </div>
                </div>

                <div class="form-group mbhalf">
                    <label for="title"><?php _e('Title', 'ag-yt-video-embedder'); ?></label><br />
                    <input type="text" class="form-control regular-text" name="title" value="<?php echo esc_html($title); ?>" />
                </div>

                <div class="form-group mbhalf">
                    <label for="url"><?php _e('Url', 'ag-yt-video-embedder'); ?></label><br />
                    <input type="url" class="form-control regular-text" name="url" value="<?php echo $url; ?>" />
                </div>

                <div class="form-group mbhalf">
                    <label for="start_time"><?php _e('Start time (in secs)', 'ag-yt-video-embedder'); ?></label><br />
                    <input type="number" class="form-control" name="start_time" value="<?php echo $start_time; ?>" />
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
                    <input type="checkbox" class="form-control regular-text" name="privacy_enhanced" value="<?php echo $privacy_enhanced; ?>" <?php echo $privacy_enhanced ? 'checked' : ''; ?> />
                </div>

                <div class="form-group mbhalf">
                    <label for="autoplay"><?php _e('Autoplay', 'ag-yt-video-embedder'); ?></label><br />
                    <input type="checkbox" class="form-control regular-text" name="autoplay" value="<?php echo $autoplay; ?>" <?php echo $autoplay ? 'checked' : ''; ?> />
                </div>
                <div class="form-group mbhalf">
                    <label for="captions"><?php _e('Captions', 'ag-yt-video-embedder'); ?></label><br />
                    <input type="checkbox" class="form-control regular-text" name="captions" value="<?php echo $captions; ?>" <?php echo $captions ? 'checked' : ''; ?> />
                </div>


                <div class="mt1">
                    <button type="submit" name="listaction" value="handleupdate" class="button-primary"><?php _e('Update', 'ag-yt-video-embedder'); ?></button>
                    <button type="submit" name="listaction" value="list" class="button-secondary"><?php _e('Cancel', 'ag-yt-video-embedder'); ?></button>
                    <button type="submit" name="listaction" value="handledelete" class="ag-yt-video-embedder button-secondary button-danger" onclick="return confirm('Are you sure you want to delete this video?'); "><?php _e('Delete', 'ag-yt-video-embedder'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>