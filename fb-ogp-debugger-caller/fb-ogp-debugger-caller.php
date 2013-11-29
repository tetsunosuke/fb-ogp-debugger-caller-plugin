<?php
/*
Plugin Name: Facebook OGP Debugger caller
Plugin URI: https://github.com/tetsunosuke/fb-ogp-debugger-caller-plugin
Description: FacebookのOGPのデバッガページを呼び出します
Version: 1.0.0
Author: ITO Tetsunosuke
Author URI: https://github.com/tetsunosuke/
License: 
License URI: 
*/


// see: http://codex.wordpress.org/Post_Status_Transitions
function fb_ogp_debugger_caller($new_status, $old_status, $post) {
    if ($new_status === "publish" && $old_status === 'future') {
        $permalink = get_permalink($post->ID);
        $api_url = "https://graph.facebook.com/";
        $data = array(
            "id" => $permalink,
            "scrape" => "true",
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,            $api_url);
        curl_setopt($curl, CURLOPT_POST,           1);
        curl_setopt($curl, CURLOPT_POSTFIELDS,     $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSLVERSION,     1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);

        $result = curl_exec($curl);

        if (curl_errno($curl)) {
            error_log(curl_error());
        }
        curl_close($curl);


        $resultAsJson = json_decode($result);
        if (isset($resultAsJson->error)) {
            error_log($resultAsJson->error->message);
        } else if (isset($resultAsJson->url)) {
            // successful
            error_log(sprintf("OGP Plugin Scrape was successful, the url: %s", $resultAsJson->url));
        } else {
            error_log($result);
        }
    }
}
add_action( 'transition_post_status', 'fb_ogp_debugger_caller', 10, 3 );
