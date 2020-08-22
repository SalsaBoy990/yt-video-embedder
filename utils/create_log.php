<?php
namespace AG_YT_Video_Embedder;

require_once('klogger.php');

$ag_yt_video_embedder_log_file_path = plugin_dir_path(__FILE__) . 'log';

$ag_yt_video_embedder_log = new KLogger($ag_yt_video_embedder_log_file_path, KLogger::INFO);
