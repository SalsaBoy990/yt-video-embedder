<?php

// constants
require_once 'constants.php';


// create log file
require_once 'utils/create_log.php';


// requires custom exception classes
require_once 'utils/custom_exceptions.php';


// requires custom css styling for frontend and admin
require_once 'inc/add_styles.php';


// requires shortcodes
require_once 'inc/create_shortcodes.php';


// requires single and group video widgets
require_once 'inc/single_video_widget_class.php';
require_once 'inc/group_video_widget_class.php';


// requires crud class
require_once 'inc/crud.php';