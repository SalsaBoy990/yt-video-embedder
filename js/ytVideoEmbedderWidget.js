;jQuery(document).ready(function ($) {
  
  // only send AJAX request when the weather now widget is added
  if ($("#ag-yt-video-embedder-widget").length > 0) {
    var data = {
      action: "ag_yt_video_embedder_widget_ajax_action",
      security: YTVideoEmbedderAjaxWidget.security,
    };
  
    console.log(YTVideoEmbedderAjaxWidget.ajaxurl);
  
    $.ajax({
      type: "POST",
      url: YTVideoEmbedderAjaxWidget.ajaxurl,
      data: data,
      dataType: "json",
    })
      .done(function ($response) {
        console.log($response);
        $results = $response.data;
        console.log("AG YT Video Embbedder AJAX Widget - OK response.");
        

        var $ytVideoGroupWidget = '<div class="ag_yt_video_embedder_video_item single-video-embed ">'
        
          $results.forEach($element => {
            $ytVideoGroupWidget += '<span>' + $element.video_title + '</span>';
            $ytVideoGroupWidget += '<div class="embed-responsive embed-responsive-16by9 m-ml-16px">';
            $ytVideoGroupWidget += '<iframe class="embed-responsive-item" src="' + $element.embed_url
              + '" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe>';
            $ytVideoGroupWidget += '</div>';
          });
          $ytVideoGroupWidget += '</div>';
      
        $("#ag-yt-video-embedder-widget").html($ytVideoGroupWidget);
      })
      .fail(function () {
        console.log("AG YT Video Embbedder AJAX Widget response error.");
      })
      .always(function () {
        console.log("AG YT Video Embbedder AJAX Widget finished.");
      });
  }
  
  
});