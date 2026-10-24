<?php
/**
 * Plugin Name: Fix Polylang Clean URLs
 * Description: 给 Polylang 生成的后缀 URL(如 /ko/services-ko/)提供干净别名
 *              (如 /ko/services/),两个地址都能正常访问,干净地址作为 canonical。
 *
 * 背景:Polylang 免费版要求各语言页面的 post_name 全局唯一,
 *      所以 ko 页 slug 必须是 "services-ko",生成的 URL 是 /ko/services-ko/。
 *      这里通过 rewrite 规则加别名,让 /ko/services/ 也能用。
 */

// 1) 加别名规则:/{lang}/{slug}/ → 实际后缀 pagename
add_filter('rewrite_rules_array', function ($rules) {
    $new = $rules;

    foreach (['ko', 'vi'] as $lang) {
        // /ko/services/ → lang=ko&pagename=services-ko
        $new['^(' . $lang . ')/([^/]+)/?$'] = 'index.php?lang=$matches[1]&pagename=$matches[2]-' . $lang;
    }

    return $new;
}, 99);

// 2) 别名页面的 canonical 指向干净 URL
add_filter('redirect_canonical', function ($redirect_url, $requested_url) {
    if (is_admin() || !function_exists('pll_current_language')) return $redirect_url;

    $lang = pll_current_language('slug');
    $req  = wp_parse_url($requested_url, PHP_URL_PATH);
    if (!$req || !$lang) return $redirect_url;

    // /ko/services-ko/ → canonical 到 /ko/services/
    if (preg_match('#^/' . $lang . '/([a-z0-9-]+)-' . $lang . '/?$#', $req, $m)) {
        return home_url('/' . $lang . '/' . $m[1] . '/');
    }
    return $redirect_url;
}, 10, 2);
