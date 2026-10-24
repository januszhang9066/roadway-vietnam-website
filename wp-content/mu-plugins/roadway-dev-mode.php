<?php
/**
 * Plugin Name: Roadway Dev Mode
 * Description: 本地开发时把 siteurl 临时指向 localhost,避免 301 跳走。生产环境自动关闭。
 * Version: 1.0
 */

// 只在 CLI 或明确的本地请求下生效
add_filter('option_home', 'roadway_dev_home');
add_filter('option_siteurl', 'roadway_dev_siteurl');
add_filter('pre_option_home', 'roadway_dev_home');
add_filter('pre_option_siteurl', 'roadway_dev_siteurl');

function roadway_dev_is_local() {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    if (!$host) {
        return false;
    }
    return (strpos($host, '127.0.0.1') !== false
         || strpos($host, 'localhost') !== false
         || strpos($host, ':8080') !== false
         || strpos($host, ':8085') !== false);
}

function roadway_dev_home($value) {
    if (!roadway_dev_is_local()) {
        return $value;
    }
    $scheme = 'http';
    $port = (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80') ? ':' . $_SERVER['SERVER_PORT'] : '';
    return $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? '127.0.0.1:8080') . $port;
}

function roadway_dev_siteurl($value) {
    return roadway_dev_home($value);
}
