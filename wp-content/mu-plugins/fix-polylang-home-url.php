<?php
/**
 * Plugin Name: Fix Polylang Home URL
 * Description: 把 /ko/home-ko/ 这类带 slug 的首页 URL 规范到 /ko/,
 *              并让语言切换器指向干净的首页地址。
 */

// 1) 首页重定向:/ko/home-ko/ → /ko/
add_action('template_redirect', function () {
    if (is_admin() || !function_exists('pll_current_language')) return;
    if (!is_page()) return;

    $lang = pll_current_language('slug');
    $slug = get_post_field('post_name', get_queried_object_id());

    // 首页 slug 形如 home / home-ko / home-vi
    if ($slug && preg_match('/^home(-[a-z]{2})?$/', $slug)) {
        $target = $lang === pll_default_language('slug')
            ? home_url('/')
            : home_url('/' . $lang . '/');

        wp_safe_redirect($target, 301);
        exit;
    }
}, 1);

// 2) 语言切换器:用干净的首页 URL
add_filter('pll_home_url', function ($url, $lang) {
    if (!$lang) return $url;
    return home_url('/' . $lang . '/');
}, 10, 2);

// 3) 关掉 canonical redirect —— 它会把 /zh/ 规范化到 /,
//    但老板希望三种语言都有明确前缀,客户点切换器时地址不跳。
add_filter('redirect_canonical', function ($redirect_url, $requested_url) {
    if (is_admin() || !function_exists('pll_current_language')) return $redirect_url;

    // 只拦截「语言首页」这一种情况
    $lang  = pll_current_language('slug');
    $req   = wp_parse_url($requested_url, PHP_URL_PATH);
    $home  = wp_parse_url(home_url(), PHP_URL_PATH);

    if ($req && $home && rtrim($req, '/') === rtrim($home, '/' . $lang, '/')) {
        return false;
    }
    return $redirect_url;
}, 10, 2);
