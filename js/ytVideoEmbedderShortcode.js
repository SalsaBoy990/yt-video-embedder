;jQuery(document).ready(function ($) {
  
  // only send AJAX request when the weather now widget is added
  if ($("#ag-yt-video-embedder-shortcode").length > 0) {
    var data = {
      action: "ag_yt_video_embedder_shortcode_ajax_action",
      security: YTVideoEmbedderAjaxShortcode.security,
      args: {
        'heading':        'Video Collection',
        'heading_level':  'h2',
        'font_weight':    '700'
      }
    };
  
    console.log(YTVideoEmbedderAjaxShortcode.ajaxurl);
  
    $.ajax({
      type: "POST",
      url: YTVideoEmbedderAjaxShortcode.ajaxurl,
      data: data,
      dataType: "json",
    })
      .done(function ($response) {
        console.log($response);
        $results = $response.data;
        console.log("AG YT Video Embbedder AJAX Shortcode - OK response.");
      
        $("#ag-yt-video-embedder-shortcode").html($results);
      })
      .fail(function () {
        console.log("AG YT Video Embbedder AJAX Shortcode response error.");
      })
      .always(function () {
        console.log("AG YT Video Embbedder AJAX Shortcode finished.");
      });
  }
  
  
});