<?php

namespace AG\YTVideoEmbedder\Log;

defined('ABSPATH') or die();

/**
 * trait for logging
 * @param global $log
 */
trait Logger
{

    public function logger(int $debug = 0, int $logging = 1): void
    {
        if ($debug) {
            $info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__;
            echo '<div class="notice notice-info is-dismissible">' . $info_text . '</p></div>';
        }
        if ($logging) {
            global $company_team_log;
            $company_team_log->logInfo("Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
        }
    }

    public function exceptionLogger(int $logging = 1, object $ex = null, string $errorLvl = 'fatal'): void
    {
        if ($logging) {
            global $company_team_log;

            switch ($errorLvl) {
                case 'info':
                    $company_team_log->logInfo(
                        $ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__
                    );
                    break;
                case 'fatal':
                    $company_team_log->logFatal(
                        $ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__
                    );
                    break;
                case 'error':
                    $company_team_log->logError(
                        $ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__
                    );
                    break;
                default:
                    $company_team_log->logInfo(
                        $ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__
                    );
            }
        }
    }
}
