<?php
/**
 * Plugin Name: Fix Polylang Directory URLs
 * Description: 强制 Polylang 使用目录式语言 URL(/zh/ /ko/ /vi/),并重建 rewrite 规则。
 *              修复 Polylang 3.8.11 + WP 7.x 下 rewrite 规则未生成导致语言页 404 的问题。
 */

// 1) 强制 links model 为 Directory
add_filter('pll_links_model', function ($class) {
    if (class_exists('PLL_Links_Directory')) {
        return 'PLL_Links_Directory';
    }
    return $class;
}, 5);

// 2) 确保 force_lang = 1(目录式)
add_option('polylang', ['force_lang' => 1, 'hide_default' => 0, 'rewrite' => 1]);

// 3) 语言切换时清 rewrite 缓存,避免旧规则残留
add_action('after_switch_theme', function () {
    delete_option('rewrite_rules');
    flush_rewrite_rules(false);
});

// 4) 提供带语言前缀的 URL 助手(不依赖 Polylang 的 URL 生成)
function rw_lang_home($slug) {
    $home = home_url('/');
    return rtrim($home, '/') . '/' . $slug . '/';
}
