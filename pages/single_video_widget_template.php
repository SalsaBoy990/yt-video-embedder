<?php
echo $before_widget;
if ($title) {
    echo $before_title . $title . $after_title;
}
?>
<div class="single-video-embed">
    <div class="embed-responsive embed-responsive-16by9 m-ml-16px">
        <iframe class="embed-responsive-item" src="<?php echo $url_embed; ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe>
    </div>
</div>
<?php
echo $after_widget;
