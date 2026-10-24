<?php
/**
 * Plugin Name: Fix Polylang Static Page Translation
 * Description: Polylang 的 pll_additional_language_data filter 在静态页场景下,
 *              没正确把 page_on_front 填到 PLL_Language 对象里。
 *              pll_language_defined 之后,手动补 page_on_front / page_for_posts。
 */

add_action('pll_language_defined', function($slug, $lang) {
    if (!function_exists('pll_get_post')) return;

    $default_page_id = (int) get_option('page_on_front');
    $default_blog_id = (int) get_option('page_for_posts');

    if ($default_page_id && empty($lang->page_on_front)) {
        $trans = pll_get_post($default_page_id, $slug);
        if ($trans) {
            $lang->page_on_front = $trans;
        }
    }

    if ($default_blog_id && empty($lang->page_for_posts)) {
        $trans = pll_get_post($default_blog_id, $slug);
        if ($trans) {
            $lang->page_for_posts = $trans;
        }
    }
}, 5, 2);
