<?php
/*
Plugin Name: MIF Monitoring Plugin
Plugin URI: http://mif.vspu.ru
Description: Плагин для получения данных для мониторинга
Author: Алексей Н. Сергеев
Version: 0.1
*/


// Увеличить счетчик посещений страниц указанного типа

add_filter( 'the_content', 'mif_monitoring_inc' );

function mif_monitoring_inc( $content ) 
{
    global $post;

    $monitoring = get_option( 'mif-monitoring' );

    if ( ! isset( $monitoring[$post->post_type] ) ) $monitoring[$post->post_type] = 0;
    $monitoring[$post->post_type]++;

    update_option( 'mif-monitoring', $monitoring, false );

    return $content;
}


// Показать все счетчики

add_filter( 'the_content', 'mif_monitoring_show' );

function mif_monitoring_show( $content ) 
{
    if ( ! access_ip() ) return $content;
    
    if ( isset( $_REQUEST['monitoring'] ) && $_REQUEST['monitoring'] == 'show' ) {
        
        global $post;
        $monitoring = get_option( 'mif-monitoring' );
        p($monitoring);
        
    }

    return $content;
}


// Показать данные нужного счетчика

add_action( 'init', 'mif_monitoring_data' );

function mif_monitoring_data() 
{
    if ( ! access_ip() ) return;
    
    if ( isset( $_REQUEST['monitoring'] ) && isset( $_REQUEST['type'] ) && $_REQUEST['monitoring'] == 'data' ) {

        $monitoring = get_option( 'mif-monitoring' );
        $key = sanitize_key( $_REQUEST['type'] );

        $data = ( isset( $monitoring[$key] ) ) ? $monitoring[$key] : 0;
    
        echo 'DATA:'.$data;
        exit;

    }

}


// Проверка доступа по IP

function access_ip()
{
    // Список разрешенных сетей

    $arr = array( '10.2' );

    // Добавить в список разрешенных ту сеть, где сам сервер

    $server = $_SERVER['SERVER_ADDR'];
    $s = explode( '.', $server );
    $arr[] = $s[0] . '.' . $s[1] . '.' . $s[2]; 

    // Проверить

    $ret = false;
    foreach ( $arr as $ip ) if ( preg_match( '/^' . $ip . '/', $_SERVER['REMOTE_ADDR'] ) ) $ret = true;

    return $ret;
}



?>
