<?php

/**
 * Created by PhpStorm.
 * User: frank
 * Date: 21.02.17
 * Time: 15:37
 */
class Materialpool_Statistic
{
    static public function log( $post_id, $post_type  ) {
        global $wpdb;
        $wpdb->mp_stats = $wpdb->prefix . 'mp_stats';
        $timestamp = time();

        $wpdb->query( $wpdb->prepare( " 
            INSERT INTO $wpdb->mp_stats 
            (  `object`, `day`, `hour`, `month`, `year`, `posttype`  )
            VALUES ( %d,%s,%s,%s,%s,%s  )
            ",
            $post_id,
            date( "d", $timestamp ),
            date( "H", $timestamp ),
            date( "m", $timestamp ),
            date( "Y", $timestamp ),
            $post_type
        ));
    }


}