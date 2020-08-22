<?php
namespace AG_YT_Video_Embedder;

class Crud
{

    // DB TABLE
    const TABLE_NAME = 'ag_yt_video_embedder';
  
    const DEBUG = '0';  // 0 = NO debug output
    // 1 = screen output of debug statements
    const LOGGING = '1';  // 0 = NO log output
  
    // default link for single video widget/shortcode
    const DEFAULT_LINK = 'https://www.youtube.com/watch?v=BHACKCNDMW8';
  
    // embed baseurl
    const EMBED_BASE_URL = 'https://www.youtube.com/embed/';
    // embed baseurl in privacy-enhanced mode
    const EMBED_BASE_URL_PRIVACY = 'https://www.youtube-nocookie.com/embed/';

  public function __construct()
  {

  }
  public function __destruct()
  {
  }

  /**
   * Post actions switcher function
   */
  public function video_embedder_post_action()
  {
    if (self::DEBUG) {
      $info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__ . "<br>";
      echo '<div class="notice notice-info is-dismissible">' . $info_text . '</p></div>';
    }
    if (self::LOGGING) {
      global $ag_yt_video_embedder_log;
      $ag_yt_video_embedder_log->logInfo("Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
    }

    global $id;

    if (isset($_POST) && !empty($_POST)) {
      $listaction = $_POST['listaction'];

      if (isset($_POST['videoid'])) {
        $id = absint(intval($_POST['videoid'], 10));
      }

      switch ($listaction) {
          // add new member
        case 'insert':
          include AG_YT_VIDEO_EMBEDDER_PLUGIN_DIR . '/pages/embedded_video_insert_template.php';
          break;

          // edit member
        case 'edit':
          include AG_YT_VIDEO_EMBEDDER_PLUGIN_DIR . '/pages/embedded_video_edit_template.php';
          break;

          // list elements
        case 'list':
          include AG_YT_VIDEO_EMBEDDER_PLUGIN_DIR . '/pages/embedded_video_list_template.php';
          break;

          // handler function when updating
        case 'handleupdate':
          $this->handle_update();
          include AG_YT_VIDEO_EMBEDDER_PLUGIN_DIR . '/pages/embedded_video_list_template.php';
          break;

          // handler function when deleting
        case 'handledelete':
          $this->handle_delete();
          include AG_YT_VIDEO_EMBEDDER_PLUGIN_DIR . '/pages/embedded_video_list_template.php';
          break;

          // handler function when inserting new member
        case 'handleinsert':
          // if no form error, goto list page (main menu)
          if ( $this->handle_insert() ) {
            include AG_YT_VIDEO_EMBEDDER_PLUGIN_DIR . '/pages/embedded_video_list_template.php';
          } else {
            // show insert form again
            include AG_YT_VIDEO_EMBEDDER_PLUGIN_DIR . '/pages/embedded_video_insert_template.php';
          }
          break;

        default:
          // ???
          echo '<h2>Nothing found.</h2>';
      }
    } else {
      include AG_YT_VIDEO_EMBEDDER_PLUGIN_DIR . '/pages/embedded_video_list_template.php';
    }
  }



  /**
   * Get list of all members
   * @return void
   */
  function embedded_video_list()
  {
    if (self::DEBUG) {
      $info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__ . "<br>";
      echo '<div class="notice notice-info is-dismissible">' . $info_text . '</p></div>';
    }
    if (self::LOGGING) {
      global $ag_yt_video_embedder_log;
      $ag_yt_video_embedder_log->logInfo("Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
    }

    try {
      // note: current_user_can() always returns false if the user is not logged in
      if (!current_user_can('manage_options')) {
        throw new PermissionsException('You do not have sufficent permissions to view this page.');
        wp_die('You do not have sufficent permissions to view this page.');
      }

      $this->video_embedder_post_action();
      // include AG_YT_VIDEO_EMBEDDER_PLUGIN_DIR . '/pages/embedded_video_list.php';


    } catch (PermissionsException $ex) {
      echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
      if (self::LOGGING) {
        global $ag_yt_video_embedder_log;
        $ag_yt_video_embedder_log->logError($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }
    } catch (\Exception $ex) {
      echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
      if (self::LOGGING) {
        global $ag_yt_video_embedder_log;
        $ag_yt_video_embedder_log->logError($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }
    }
  }


  /**
   * Insert
   * @return void
   */
  function embedded_video_list_insert()
  {
    if (self::DEBUG) {
      $info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__ . "<br>";
      echo '<div class="notice notice-info is-dismissible">' . $info_text . '</p></div>';
    }
    if (self::LOGGING) {
      global $ag_yt_video_embedder_log;
      $ag_yt_video_embedder_log->logInfo("Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
    }

    try {
      global $wpdb;
      if (!current_user_can('manage_options')) {
        throw new PermissionsException('You do not have sufficent permissions to view this page.');
        wp_die('You do not have sufficent permissions to view this page.');
      }

      if (!empty($_POST)) {
        $this->video_embedder_post_action();
      } else {
        include AG_YT_VIDEO_EMBEDDER_PLUGIN_DIR . '/pages/embedded_video_insert_template.php';
      }
    } catch (PermissionsException $ex) {
      echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
      if (self::LOGGING) {
        global $ag_yt_video_embedder_log;
        $ag_yt_video_embedder_log->logError($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }
    } catch (\Exception $ex) {
      echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
      if (self::LOGGING) {
        global $ag_yt_video_embedder_log;
        $ag_yt_video_embedder_log->logError($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }
    }
  }



  /**
   * Insert new record, add new team member
   * @return void
   */
  function handle_insert()
  {
    if (self::DEBUG) {
      $info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__ . "<br>";
      echo '<div class="notice notice-info is-dismissible">' . $info_text . '</p></div>';
    }
    if (self::LOGGING) {
      global $ag_yt_video_embedder_log;
      $ag_yt_video_embedder_log->logInfo("Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
    }
    global $wpdb;

    // get sanitized form values from inputs
    $sanitizedData = $this->get_form_input_values();
    

    try {
      // title and url are required fields
      if (!empty($sanitizedData['title']) && !empty($sanitizedData['url'])) {
        // prepare query, update table
        $res = $wpdb->insert(
          $wpdb->prefix . self::TABLE_NAME,
          array(
            'title'             => $sanitizedData['title'],
            'url'               => $sanitizedData['url'],
            'privacy_enhanced'  => $sanitizedData['privacy_enhanced'],
            'start_time'        => $sanitizedData['start_time'],
            'autoplay'          => $sanitizedData['autoplay'],
            'captions'          => $sanitizedData['captions']
          ),
          array('%s', '%s', '%d', '%d', '%d', '%d') // data format
        );
      } else {
        return false;
      }

      if ($res === false) {
        throw new InsertRecordException('Database Error: Unable to insert new video record into table.');
      } else {
        echo '<div class="notice notice-success is-dismissible"><p>Video details successfully saved.' . '</p></div>';
      }
    } catch (InsertRecordException $ex) {
      echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
      if (self::LOGGING) {
        global $ag_yt_video_embedder_log;
        $ag_yt_video_embedder_log->logFatal($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }
    } catch (DBQueryException $ex) {
      echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
      if (self::LOGGING) {
        global $ag_yt_video_embedder_log;
        $ag_yt_video_embedder_log->logFatal($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }
    } catch (\Exception $ex) {
      echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
      if (self::LOGGING) {
        global $ag_yt_video_embedder_log;
        $ag_yt_video_embedder_log->logError($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }
    }
    return true;
  }


  /**
   * Edit/Update current video
   * @return void
   */
  function handle_update()
  {
    if (self::DEBUG) {
      $info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__ . "<br>";
      echo '<div class="notice notice-info is-dismissible">' . $info_text . '</p></div>';
    }
    if (self::LOGGING) {
      global $ag_yt_video_embedder_log;
      $ag_yt_video_embedder_log->logInfo("Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
    }

    try {
      global $wpdb;

      // get sanitized form values from inputs
      $sanitizedData = $this->get_form_input_values();

      // prepare query, update table
      $res = $wpdb->update(
        $wpdb->prefix . self::TABLE_NAME,
        array(
          'title'             => $sanitizedData['title'],
          'url'               => $sanitizedData['url'],
          'privacy_enhanced'  => $sanitizedData['privacy_enhanced'],
          'start_time'        => $sanitizedData['start_time'],
          'autoplay'          => $sanitizedData['autoplay'],
          'captions'          => $sanitizedData['captions']
        ),
        array('id'  => $sanitizedData['id']), // where clause
        array('%s', '%s', '%d', '%d', '%d', '%d'), // data format
        array('%d') // where format
      );


      if ($res === false) {
        throw new UpdateRecordException('Database Error: Unable to update team member data/record.');
      } else {
        echo '<div class="notice notice-success is-dismissible"><p>Team member data successfully updated.</p></div>';
        if (self::LOGGING) {
          global $ag_yt_video_embedder_log;
          $ag_yt_video_embedder_log->logInfo("Team member data successfully updated - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
        }
      }
    } catch (UpdateRecordException $ex) {
      echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
      if (self::LOGGING) {
        global $ag_yt_video_embedder_log;
        $ag_yt_video_embedder_log->logFatal($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }
    } catch (DBQueryException $ex) {
      echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
      if (self::LOGGING) {
        global $ag_yt_video_embedder_log;
        $ag_yt_video_embedder_log->logFatal($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }
    } catch (\Exception $ex) {
      echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
      if (self::LOGGING) {
        global $ag_yt_video_embedder_log;
        $ag_yt_video_embedder_log->logError($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }
    }
  }



  /**
   * delete current video
   * @return void
   */
  function handle_delete()
  {
    if (self::DEBUG) {
      $info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__ . "<br>";
      echo '<div class="notice notice-info is-dismissible">' . $info_text . '</p></div>';
    }
    if (self::LOGGING) {
      global $ag_yt_video_embedder_log;
      $ag_yt_video_embedder_log->logInfo("Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
    }

    try {

      global $wpdb;

      if (isset($_POST['videoid'])) {
        $id = $_POST['videoid'];

        // prepare get statement protect against SQL inject attacks!
        $sql = $wpdb->prepare("DELETE FROM " . $wpdb->prefix . self::TABLE_NAME . " WHERE id = %d", $id);

        // perform query
        $res = $wpdb->query($sql);

        if ($res === false) {
          throw new DeleteRecordException('Database error: Unable to delete team member.');
        } else {
          echo '<div class="notice notice-success is-dismissible"><p>Team member successfully deleted.' . '</p></div>';
        }
      }
    } catch (DeleteRecordException $ex) {
      echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
      if (self::LOGGING) {
        global $ag_yt_video_embedder_log;
        $ag_yt_video_embedder_log->logFatal($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }
    } catch (DBQueryException $ex) {
      echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
      if (self::LOGGING) {
        global $ag_yt_video_embedder_log;
        $ag_yt_video_embedder_log->logFatal($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }
    } catch (\Exception $ex) {
      echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
      if (self::LOGGING) {
        global $ag_yt_video_embedder_log;
        $ag_yt_video_embedder_log->logError($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }
    }
  }




  /**
   * Get form input, sanitize values
   * @return array associative
   */
  public function get_form_input_values()
  {
    if (self::DEBUG) {
      $info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__;
      echo '<div class="notice notice-info is-dismissible">' . $info_text . '</p></div>';
    }
    if (self::LOGGING) {
      global $ag_yt_video_embedder_log;
      $ag_yt_video_embedder_log->logInfo("Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
    }
    // store escaped user input field values
    $formValues = array();


    if (isset($_POST['videoid'])) {
      $id = $this->sanitize_input($_POST['videoid'], 'integer');
      $id = intval($id, 10);
      $formValues['id'] = absint($id);
    }







    try {
      if (isset($_POST['title']) && !empty($_POST['title'])) {

        $title = $this->sanitize_input($_POST['title']);
        $formValues['title'] = $title;

      } else {
        $this->titleInputError = 'Title is a required field!';
        echo '<div class="notice notice-error">' . $this->titleInputError . '</p></div>';
        throw new RequiredInputException($this->titleInputError);
      }
    } catch (RequiredInputException $ex) {
      echo '<div class="notice notice-error">' . $ex->getMessage() . '</p></div>';
      if (self::LOGGING) {
        global $ag_yt_video_embedder_log;
        $ag_yt_video_embedder_log->logFatal($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }
    } catch (FormException $ex) {
      echo '<div class="notice notice-error">' . $ex->getMessage() . '</p></div>';
      if (self::LOGGING) {
        global $ag_yt_video_embedder_log;
        $ag_yt_video_embedder_log->logFatal($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }
    } catch (\Exception $ex) {
      echo '<div class="notice notice-error">' . $ex->getMessage() . '</p></div>';
      if (self::LOGGING) {
        global $ag_yt_video_embedder_log;
        $ag_yt_video_embedder_log->logError($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }
    }

    try {
      if (isset($_POST['url']) && !empty($_POST['url'])) {
        $url = $this->sanitize_input($_POST['url'], 'url');
        $formValues['url'] = $url;
      } else {
        $this->urlInputError = 'Video url is a required field!';
        echo '<div class="notice notice-error">' . $this->urlInputError . '</p></div>';
        throw new RequiredInputException($this->urlInputError);
      }
    } catch (RequiredInputException $ex) {
      echo '<div class="notice notice-error">' . $ex->getMessage() . '</p></div>';
      if (self::LOGGING) {
        global $ag_yt_video_embedder_log;
        $ag_yt_video_embedder_log->logError($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }
    } catch (FormException $ex) {
      echo '<div class="notice notice-error">' . $ex->getMessage() . '</p></div>';
      if (self::LOGGING) {
        global $ag_yt_video_embedder_log;
        $ag_yt_video_embedder_log->logError($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }
    } catch (\Exception $ex) {
      echo '<div class="notice notice-error">' . $ex->getMessage() . '</p></div>';
      if (self::LOGGING) {
        global $ag_yt_video_embedder_log;
        $ag_yt_video_embedder_log->logError($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }
    }







    if (isset($_POST['privacy_enhanced'])) {
      // $privacy_enhanced = $this->sanitize_input($_POST['privacy_enhanced']);
      $formValues['privacy_enhanced'] = 1;
    } else {
      $formValues['privacy_enhanced'] = 0;
    }

    // start time set: positive integer, default: 0
    if (isset($_POST['start_time']) && !empty($_POST['start_time'])) {
      $start_time = $this->sanitize_input($_POST['start_time'], 'integer');
      $start_time = intval($start_time, 10);
      $formValues['start_time'] = absint($start_time);
    } else {
      $formValues['start_time'] = 0;
    }

    // autoplay checked: 1, unchecked: 0
    if (isset($_POST['autoplay'])) {
      // $autoplay = $this->sanitize_input($_POST['autoplay']);
      $formValues['autoplay'] = 1;
    } else {
      $formValues['autoplay'] = 0;
    }

    if (isset($_POST['captions'])) {
      // $captions = $this->sanitize_input($_POST['captions']);
      $formValues['captions'] = 1;
    } else {
      $formValues['captions'] = 0;
    }

    return $formValues;
  }




  /**
   * Sanitizes input values
   * strips tags, more sanitization needed!
   * @return string
   */
  public function sanitize_input($input, $type = "text")
  {
    if (self::DEBUG) {
      $info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__;
      echo '<div class="notice notice-info is-dismissible">' . $info_text . '</p></div>';
    }
    if (self::LOGGING) {
      global $ag_yt_video_embedder_log;
      $ag_yt_video_embedder_log->logInfo("Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
    }

    $input = wp_strip_all_tags(trim($input));

    switch ($type) {
      case 'text':
        $input = esc_html($input);
        break;
      case 'url':
        // sanitize_url() deprecated! 
        $input = esc_url($input);
        break;
      case 'integer':
        $input = filter_var($input, FILTER_SANITIZE_NUMBER_INT);
        break;
      case 'checkbox':
        $input = filter_var($input, FILTER_SANITIZE_NUMBER_INT);
        break;
      default:;
    }


    return $input;
  }
}
