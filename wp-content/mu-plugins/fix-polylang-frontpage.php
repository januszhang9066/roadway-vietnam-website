<?php
/**
 * Plugin Name: Fix Polylang Front Page
 * Description: 修 Polylang 三语首页 — 让 /zh/ /ko/ /vi/ 各自显示对应语言的首页
 * Version: 1.0
 */

// Polylang 在 pll_language_defined 时手动补 page_on_front / page_for_posts
add_action('pll_language_defined', function ($lang) {
    if (!$lang || is_wp_error($lang)) {
        return;
    }

    $slug = is_string($lang) ? $lang : (isset($lang->slug) ? $lang->slug : '');
    if (!$slug) {
        return;
    }

    // 找到该语言的静态首页
    $front_id = pll_get_post(get_option('page_on_front'), $slug);
    $posts_id = pll_get_post(get_option('page_for_posts'), $slug);

    if ($front_id) {
        $GLOBALS['polylang']->curlang->page_on_front = $front_id;
    }
    if ($posts_id) {
        $GLOBALS['polylang']->curlang->page_for_posts = $posts_id;
    }
}, 5);

// 兜底:如果 curlang 还没有 page_on_front,直接查
add_filter('pll_home_url_white_list', function ($white_list) {
    return $white_list;
});

// 请求时兜底 — 确保每种语言都有 page_on_front
add_action('template_redirect', function () {
    if (is_admin() || !function_exists('pll_current_language')) {
        return;
    }
    $curlang = pll_current_language();
    if (!$curlang || !isset($GLOBALS['polylang']->curlang)) {
        return;
    }
    if (empty($GLOBALS['polylang']->curlang->page_on_front)) {
        $front_id = pll_get_post((int) get_option('page_on_front'), $curlang);
        if ($front_id) {
            $GLOBALS['polylang']->curlang->page_on_front = $front_id;
        }
    }
}, 1);
