<?php
echo $before_widget;
if ($heading) {
    echo $before_title . $heading . $after_title;
}

// foreach ($formData as $row) {
//   $video_title      = $row->title;
//   $url              = $row->url;
//   $privacy_enhanced = $row->privacy_enhanced;
//   $start_time       = $row->start_time;
//   $autoplay         = $row->autoplay;
//   $captions         = $row->captions;

//   // create embed url
//   $url_embed = $this->createEmbedVideoLinks(
//      $url,
//      $privacy_enhanced,
//     $start_time,
//     $autoplay,
//     $captions
//   );

?>
<!-- <div class="ag_yt_video_embedder_video_item single-video-embed ">
    <span><?php // echo $video_title 
            ?></span>
    <div class="embed-responsive embed-responsive-16by9 m-ml-16px">
      <iframe class="embed-responsive-item" src="<?php // echo $url_embed; 
                                                    ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe>
    </div>
  </div> -->
<?php
// }
?>
<div id="ag-yt-video-embedder-widget">
</div>
<?php
echo $after_widget;
?>