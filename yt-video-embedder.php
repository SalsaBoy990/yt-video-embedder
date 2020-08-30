<?php

namespace AG\YTVideoEmbedder;

defined('ABSPATH') or die();

/*
Plugin Name: Youtube Video Embedder
Plugin URI: https://github.com/SalsaBoy990/yt-video-embedder
Description: You can easily embed videos from Youtube etc. responsively with Bootstrap 4. Multiple widgets and shortcodes available.
Version: 1.0
Author: András Gulácsi
Author URI: https://github.com/SalsaBoy990
License: GPLv2 or later
Text Domain: ag-yt-video-embedder
Domain Path: /languages
*/

require_once 'requires.php';

use AG\YTVideoEmbedder\YTVideoEmbedder as YTVideoEmbedder;

use AG\YTVideoEmbedder\Shortcodes\Shortcodes as Shortcodes;

use AG\YTVideoEmbedder\Crud\Crud as Crud;

use AG\YTVideoEmbedder\Log\KLogger as Klogger;


$ag_yt_video_embedder_log_file_path = plugin_dir_path(__FILE__) . '/log';

$ag_yt_video_embedder_log = new KLogger($ag_yt_video_embedder_log_file_path, KLogger::INFO);


// instantiate class, with dependency injection
$ag_yt_video_embedder = new YTVideoEmbedder(
    new Crud(),
    new Shortcodes()
);


// ACTIVATE, DEACTIVATE, UNINSTALL PLUGIN
// we don't need to do anything when deactivation, just creating debug and log messages
register_deactivation_hook(__FILE__, function () {
});

// create table if not exists when activating the plugin
register_activation_hook(__FILE__, '\AG\YTVideoEmbedder\YTVideoEmbedder::activateVideoEmbedder');

// delete table when uninstalling the plugin
register_uninstall_hook(__FILE__, '\AG\YTVideoEmbedder\YTVideoEmbedder::deleteVideoEmbedder');
