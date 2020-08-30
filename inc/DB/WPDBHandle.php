<?php

namespace AG\YTVideoEmbedder\DB;

defined('ABSPATH') or die();

class WPDBHandle
{
    public function __construct()
    {
    }
    public function __destruct()
    {
    }

    public function list(string $tableName)
    {
        global $wpdb;
 
        $sql = "SELECT * FROM " . $wpdb->prefix . $tableName;
    
        $res = $wpdb->get_results($sql);
    
        return $res;
    }

    protected function insert(string $tableName, array $sanitizedData): bool
    {
        global $wpdb;

        // insert record
        $res = $wpdb->insert(
            $wpdb->prefix . $tableName,
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

        return $res;
    }
    protected function update(string $tableName, array $sanitizedData): bool
    {
        global $wpdb;
        // prepare query, update table
        $res = $wpdb->update(
            $wpdb->prefix . $tableName,
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
        return $res;
    }

    protected function delete(int $id): bool
    {
        global $wpdb;
        // prepare get statement protect against SQL inject attacks!
        $sql = $wpdb->prepare("DELETE FROM " . $wpdb->prefix . "ag_yt_video_embedder WHERE id = %d", $id);

        // perform query
        $res = $wpdb->query($sql);

        return $res;
    }
}
