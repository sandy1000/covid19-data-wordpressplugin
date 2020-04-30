<?php

namespace PThemes_Covid19;

class Remote {

    public static function get($url = "", $args = [],$parseJSON=true) {

        $args["timeout"]=60;
        foreach ([true, false] as $tf) {
            $args['sslverify'] = $tf;
            $query             = wp_remote_get($url, $args);
            if (!is_wp_error($query)) {
                break;
            }
        }
        if (is_wp_error($query)) {
            return new \WP_Error('request', 'error-making-request');
        }
        $body    = wp_remote_retrieve_body($query);
        $content = $parseJSON?json_decode($body, false):$body;
        if (empty($content)) {
            return new \WP_Error('request', "no-data");
        }
        return $content;
    }
}