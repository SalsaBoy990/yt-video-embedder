<?php

namespace AG\YTVideoEmbedder\Input;

defined('ABSPATH') or die();


trait FormInput
{
    use \AG\YTVideoEmbedder\Log\Logger;

    /**
     * Get form input, sanitize values
     * @return array associative
     */
    public function getFormInputValues()
    {
        // debug log and log to file
        $this->logger(\AG_YTVIDEO_EMBEDDER_DEBUG, \AG_YTVIDEO_EMBEDDER_LOGGING);

        // store escaped user input field values
        $formValues = array();


        if ($_POST['videoid'] ?? 0) {
            $id = $this->sanitizeInput($_POST['videoid'], 'integer');
            $id = intval($id, 10);
            $formValues['id'] = absint($id);
        }

        try {
            if (($_POST['title'] ?? 0) && !empty($_POST['title'])) {
                $title = $this->sanitizeInput($_POST['title']);
                $formValues['title'] = $title;
            } else {
                $this->titleInputError = 'Title is a required field!';
                echo '<div class="notice notice-error">' . $this->titleInputError . '</p></div>';
                throw new RequiredInputException($this->titleInputError);
            }
        } catch (RequiredInputException $ex) {
            echo '<div class="notice notice-error">' . $ex->getMessage() . '</p></div>';
            // debug log and log to file
            $this->exceptionLogger(\AG_YTVIDEO_EMBEDDER_LOGGING, $ex);
        } catch (FormException $ex) {
            echo '<div class="notice notice-error">' . $ex->getMessage() . '</p></div>';
            $this->exceptionLogger(\AG_YTVIDEO_EMBEDDER_LOGGING, $ex);
        } catch (\Exception $ex) {
            echo '<div class="notice notice-error">' . $ex->getMessage() . '</p></div>';
            $this->exceptionLogger(\AG_YTVIDEO_EMBEDDER_LOGGING, $ex);
        }

        try {
            if (isset($_POST['url']) && !empty($_POST['url'])) {
                $url = $this->sanitizeInput($_POST['url'], 'url');
                $formValues['url'] = $url;
            } else {
                $this->urlInputError = 'Video url is a required field!';
                echo '<div class="notice notice-error">' . $this->urlInputError . '</p></div>';
                throw new RequiredInputException($this->urlInputError);
            }
        } catch (RequiredInputException $ex) {
            echo '<div class="notice notice-error">' . $ex->getMessage() . '</p></div>';
            $this->exceptionLogger(\AG_YTVIDEO_EMBEDDER_LOGGING, $ex);
        } catch (FormException $ex) {
            echo '<div class="notice notice-error">' . $ex->getMessage() . '</p></div>';
            $this->exceptionLogger(\AG_YTVIDEO_EMBEDDER_LOGGING, $ex);
        } catch (\Exception $ex) {
            echo '<div class="notice notice-error">' . $ex->getMessage() . '</p></div>';
            $this->exceptionLogger(\AG_YTVIDEO_EMBEDDER_LOGGING, $ex);
        }





        if (isset($_POST['privacy_enhanced'])) {
            // $privacy_enhanced = $this->sanitizeInput($_POST['privacy_enhanced']);
            $formValues['privacy_enhanced'] = 1;
        } else {
            $formValues['privacy_enhanced'] = 0;
        }

        // start time set: positive integer, default: 0
        if (isset($_POST['start_time']) && !empty($_POST['start_time'])) {
            $start_time = $this->sanitizeInput($_POST['start_time'], 'integer');
            $start_time = intval($start_time, 10);
            $formValues['start_time'] = absint($start_time);
        } else {
            $formValues['start_time'] = 0;
        }

        // autoplay checked: 1, unchecked: 0
        if (isset($_POST['autoplay'])) {
            // $autoplay = $this->sanitizeInput($_POST['autoplay']);
            $formValues['autoplay'] = 1;
        } else {
            $formValues['autoplay'] = 0;
        }

        if ($_POST['captions'] ?? 0) {
            // $captions = $this->sanitizeInput($_POST['captions']);
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
    public function sanitizeInput($input, string $type = "text")
    {
        // debug log and log to file
        $this->logger(\AG_YTVIDEO_EMBEDDER_DEBUG, \AG_YTVIDEO_EMBEDDER_LOGGING);

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
            default:
                ;
        }

        return $input;
    }
}
