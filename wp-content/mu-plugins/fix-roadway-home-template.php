<?php
/**
 * Plugin Name: Fix Roadway Home Template
 * Description: Polylang + Astra 下首页 <main> 为空 —— 强制首页走 roadway/front-page.php。
 */

// 1) 首页强制用 roadway 的 front-page.php
add_filter('template_include', function ($template) {
    if (is_admin() || is_feed() || is_search() || is_404()) return $template;

    $is_home = is_front_page() || (function_exists('is_home') && is_home());
    if (!$is_home) return $template;

    $own = get_stylesheet_directory() . '/front-page.php';
    if (file_exists($own)) return $own;

    return $template;
}, 99);

// 2) 三语首页都算 front page(Polylang 会把 /ko/ 指向自己的静态首页)
add_filter('pll_home_url', function ($url, $lang) {
    if (!$lang) return $url;
    return home_url('/' . $lang . '/');
}, 10, 2);

// 3) 确保三语首页都被识别为 front page
add_action('template_redirect', function () {
    if (is_admin()) return;
    $lang = function_exists('pll_current_language') ? pll_current_language('slug') : null;
    if (!$lang) return;

    if (is_page() && function_exists('pll_is_translated_post')) {
        $id = get_queried_object_id();
        // slug 形如 home / home-ko / home-vi
        $slug = get_post_field('post_name', $id);
        if ($slug && preg_match('/^home(-[a-z]{2})?$/', $slug)) {
            // 已经是 front page,不用改
            return;
        }
    }
}, 1);
