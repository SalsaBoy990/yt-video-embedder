<?php

namespace AG\YTVideoEmbedder\Crud;

defined('ABSPATH') or die();

use AG\YTVideoEmbedder\DB\WPDBHandle as WPDBHandle;

use AG\YTVideoEmbedder\Input\FormInput as FormInput;


class Crud extends WPDBHandle implements CrudInterface
{
    use FormInput;

    // DB TABLE
    private const TABLE_NAME = 'ag_yt_video_embedder';

    private const DEBUG = '0';  // 0 = NO debug output
    // 1 = screen output of debug statements

    private const LOGGING = '1';  // 0 = NO log output

    // default link for single video widget/shortcode
    private const DEFAULT_LINK = 'https://www.youtube.com/watch?v=BHACKCNDMW8';

    // embed baseurl
    private const EMBED_BASE_URL = 'https://www.youtube.com/embed/';

    // embed baseurl in privacy-enhanced mode
    private const EMBED_BASE_URL_PRIVACY = 'https://www.youtube-nocookie.com/embed/';

    public function __construct()
    {
    }
    public function __destruct()
    {
    }

    /**
     * Post actions switcher function
     */
    public function postAction(): void
    {
        // debug log and log to file
        $this->logger(\AG_YTVIDEO_EMBEDDER_DEBUG, \AG_YTVIDEO_EMBEDDER_LOGGING);

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
                    $this->handleUpdate();
                    include AG_YT_VIDEO_EMBEDDER_PLUGIN_DIR . '/pages/embedded_video_list_template.php';
                    break;

                    // handler function when deleting
                case 'handledelete':
                    $this->handleDelete();
                    include AG_YT_VIDEO_EMBEDDER_PLUGIN_DIR . '/pages/embedded_video_list_template.php';
                    break;

                    // handler function when inserting new member
                case 'handleinsert':
                    // if no form error, goto list page (main menu)
                    if ($this->handleInsert()) {
                        include AG_YT_VIDEO_EMBEDDER_PLUGIN_DIR . '/pages/embedded_video_list_template.php';
                    } else {
                        // show insert form again
                        include AG_YT_VIDEO_EMBEDDER_PLUGIN_DIR . '/pages/embedded_video_insert_template.php';
                    }
                    break;

                default:
                    require AG_YT_VIDEO_EMBEDDER_PLUGIN_DIR . '/pages/embedded_video_list_template.php';
            }
        } else {
            require AG_YT_VIDEO_EMBEDDER_PLUGIN_DIR . '/pages/embedded_video_list_template.php';
        }
    }



    /**
     * Get list of all members
     * @return void
     */
    public function listTable(): void
    {
        // debug log and log to file
        $this->logger(\AG_YTVIDEO_EMBEDDER_DEBUG, \AG_YTVIDEO_EMBEDDER_LOGGING);

        try {
            // note: current_user_can() always returns false if the user is not logged in
            if (!current_user_can('manage_options')) {
                throw new PermissionsException('You do not have sufficent permissions to view this page.');
                wp_die('You do not have sufficent permissions to view this page.');
            }

            $this->postAction();
        } catch (PermissionsException $ex) {
            echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
            $this->exceptionLogger(\AG_YTVIDEO_EMBEDDER_LOGGING, $ex);
        } catch (\Exception $ex) {
            echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
            $this->exceptionLogger(\AG_YTVIDEO_EMBEDDER_LOGGING, $ex);
        }
    }


    /**
     * Insert
     * @return void
     */
    public function insertRecord(): void
    {
        // debug log and log to file
        $this->logger(\AG_YTVIDEO_EMBEDDER_DEBUG, \AG_YTVIDEO_EMBEDDER_LOGGING);

        try {
            if (!current_user_can('manage_options')) {
                throw new PermissionsException('You do not have sufficent permissions to view this page.');
                wp_die('You do not have sufficent permissions to view this page.');
            }

            if (!empty($_POST)) {
                $this->postAction();
            } else {
                include AG_YT_VIDEO_EMBEDDER_PLUGIN_DIR . '/pages/embedded_video_insert_template.php';
            }
        } catch (PermissionsException $ex) {
            echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
            $this->exceptionLogger(\AG_YTVIDEO_EMBEDDER_LOGGING, $ex);
        } catch (\Exception $ex) {
            echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
            $this->exceptionLogger(\AG_YTVIDEO_EMBEDDER_LOGGING, $ex);
        }
    }



    /**
     * Insert new record, add new team member
     * @return void
     */
    public function handleInsert(): bool
    {
        // debug log and log to file
        $this->logger(\AG_YTVIDEO_EMBEDDER_DEBUG, \AG_YTVIDEO_EMBEDDER_LOGGING);


        // !!! verify insert nonce !!!
        if (
            !isset($_POST['ytvid_embed_admin_insert_security'])
            || !wp_verify_nonce($_POST['ytvid_embed_admin_insert_security'], 'ag_ytvid_embed_insert')
        ) {
            print 'Sorry, your nonce did not verify.';
            exit;
        } else {
            // get sanitized form values from inputs
            $sanitizedData = $this->getFormInputValues();

            try {
                // title and url are required fields
                if (!empty($sanitizedData['title']) && !empty($sanitizedData['url'])) {
                    // insert record
                    $res = $this->insert(self::TABLE_NAME, $sanitizedData);

                    if ($res === false) {
                        throw new InsertRecordException(
                            'Database Error: Unable to insert new video record into table.'
                        );
                    } else {
                        echo '<div class="notice notice-success is-dismissible"><p>Video details successfully saved.' .
                            '</p></div>';
                        return true;
                    }
                }
            } catch (InsertRecordException $ex) {
                echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
                $this->exceptionLogger(\AG_YTVIDEO_EMBEDDER_LOGGING, $ex);
            } catch (DBQueryException $ex) {
                echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
                $this->exceptionLogger(\AG_YTVIDEO_EMBEDDER_LOGGING, $ex);
            } catch (\Exception $ex) {
                echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
                $this->exceptionLogger(\AG_YTVIDEO_EMBEDDER_LOGGING, $ex);
            }
        }
        return false;
    }


    /**
     * Edit/Update current video
     * @return void
     */
    public function handleUpdate(): void
    {
        // debug log and log to file
        $this->logger(\AG_YTVIDEO_EMBEDDER_DEBUG, \AG_YTVIDEO_EMBEDDER_LOGGING);


        // !!! verify edit nonce !!!
        if (
            !isset($_POST['ytvid_embed_admin_edit_security']) ||
            !wp_verify_nonce($_POST['ytvid_embed_admin_edit_security'], 'ag_ytvid_embed_edit')
        ) {
            print 'Sorry, your nonce did not verify.';
            exit;
        } else {
            try {
                // get sanitized form values from inputs
                $sanitizedData = $this->getFormInputValues();

                // prepare query, update table
                $res = $this->update(self::TABLE_NAME, $sanitizedData);

                if ($res === false) {
                    throw new UpdateRecordException('Database Error: Unable to update team member data/record.');
                } else {
                    echo '<div class="notice notice-success is-dismissible"><p>Team member data successfully updated.' .
                        '</p></div>';
                    if (self::LOGGING) {
                        global $ag_yt_video_embedder_log;
                        $ag_yt_video_embedder_log->logInfo(
                            "Team member data successfully updated - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__
                        );
                    }
                }
            } catch (UpdateRecordException $ex) {
                echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
                $this->exceptionLogger(\AG_YTVIDEO_EMBEDDER_LOGGING, $ex);
            } catch (DBQueryException $ex) {
                echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
                $this->exceptionLogger(\AG_YTVIDEO_EMBEDDER_LOGGING, $ex);
            } catch (\Exception $ex) {
                echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
                $this->exceptionLogger(\AG_YTVIDEO_EMBEDDER_LOGGING, $ex);
            }
        }
    }



    /**
     * delete current video
     * @return void
     */
    public function handleDelete(): void
    {
        // debug log and log to file
        $this->logger(\AG_YTVIDEO_EMBEDDER_DEBUG, \AG_YTVIDEO_EMBEDDER_LOGGING);
        // !!! verify edit nonce !!!
        if (
            !isset($_POST['ytvid_embed_admin_edit_security']) ||
            !wp_verify_nonce($_POST['ytvid_embed_admin_edit_security'], 'ag_ytvid_embed_edit')
        ) {
            print 'Sorry, your nonce did not verify.';
            exit;
        } else {
            try {
                if (isset($_POST['videoid'])) {
                    $id = $_POST['videoid'];

                    // delete record
                    $res = $this->delete($id);

                    if ($res === false) {
                        throw new DeleteRecordException('Database error: Unable to delete team member.');
                    } else {
                        echo '<div class="notice notice-success is-dismissible"><p>Team member successfully deleted.'
                            . '</p></div>';
                    }
                }
            } catch (DeleteRecordException $ex) {
                echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
                $this->exceptionLogger(\AG_YTVIDEO_EMBEDDER_LOGGING, $ex);
            } catch (DBQueryException $ex) {
                echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
                $this->exceptionLogger(\AG_YTVIDEO_EMBEDDER_LOGGING, $ex);
            } catch (\Exception $ex) {
                echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
                $this->exceptionLogger(\AG_YTVIDEO_EMBEDDER_LOGGING, $ex);
            }
        }
    }
}
