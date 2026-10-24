<?php
/**
 * Plugin Name: Fix Locale for Polylang + WordPress 7.1.3
 * Description: WordPress 7.1.3 的 WP_Translation_Controller::set_locale() 严格要求 string 参数。
 *              当 Polylang 在 init 时 reset locale,可能临时为 NULL,导致致命错误。
 *              这个 mu-plugin 强制 locale 永远是有效 string。
 * Author: Roadway Vietnam
 * Version: 1.0
 */

// 1. pre_determine_locale:必须返回 string
add_filter('pre_determine_locale', function($locale) {
    if (empty($locale) || !is_string($locale)) {
        return 'en_US';
    }
    return $locale;
}, 1);

// 2. determine_locale:保证返回 string
add_filter('determine_locale', function($locale) {
    if (empty($locale) || !is_string($locale)) {
        return 'en_US';
    }
    return $locale;
}, 9999);

// 3. locale:保证返回 string
add_filter('locale', function($locale) {
    if (empty($locale) || !is_string($locale)) {
        return 'en_US';
    }
    return $locale;
}, 9999);

// 4. 如果 Polylang 有自己的语言,优先用 Polylang 的
add_action('plugins_loaded', function() {
    if (function_exists('pll_current_language') && function_exists('pll_default_language')) {
        $pll_lang = pll_current_language();
        if (!$pll_lang) {
            $pll_lang = pll_default_language();
        }
        if ($pll_lang && function_exists('switch_to_locale')) {
            // 不在这里 switch,Polylang 自己处理
        }
    }
}, 1);
