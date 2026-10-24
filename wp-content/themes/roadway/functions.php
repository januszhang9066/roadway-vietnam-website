<?php
/**
 * Roadway Vietnam 主题
 * 越南北部韩资/中资制造业工厂 AI 解决方案
 */

// ---------- 基础设置 ----------
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');
    register_nav_menus([
        'primary' => __('主导航', 'roadway'),
        'footer'  => __('页脚导航', 'roadway'),
    ]);
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('roadway-style', get_stylesheet_uri(), [], '1.0.0');
    // 内联关键 CSS,减少请求
    wp_add_inline_style('roadway-style', ':root{--rw-ease:cubic-bezier(.4,0,.2,1)}');
});

// Astra 的 header/footer 由本主题接管
add_action('astra_header_footer', function () {});

// ---------- 关闭不必要的 WP 头 ----------
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wp_shortlink_wp_head');
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('admin_print_styles', 'print_emoji_styles');

// ---------- 当前语言 ----------
function rw_lang() {
    if (function_exists('pll_current_language')) {
        $l = pll_current_language('slug');
        if ($l) return $l;
    }
    return 'zh';
}

// ---------- 字段读取 ----------
/**
 * 读取自定义字段(按语言)
 * 优先 _rw_{key}_{lang} → _rw_{key} → 默认值
 */
function rw_f($key, $default = '') {
    $lang = rw_lang();
    $v = get_post_meta(get_the_ID(), '_rw_' . $key . '_' . $lang, true);
    if ($v === '' || $v === false) {
        $v = get_post_meta(get_the_ID(), '_rw_' . $key, true);
    }
    return ($v === '' || $v === false) ? $default : $v;
}

/** 多行字段 → 数组 */
function rw_list($key, $default = []) {
    $v = rw_f($key, '');
    if (!$v) return $default;
    $lines = array_filter(array_map('trim', preg_split('/\n/', $v)));
    return array_values($lines);
}

/** 读取全局字段(不依赖文章) */
function rw_g($key, $default = '') {
    $lang = rw_lang();
    $v = get_option('rw_' . $key . '_' . $lang, '');
    if ($v === '' || $v === false) {
        $v = get_option('rw_' . $key, '');
    }
    return ($v === '' || $v === false) ? $default : $v;
}

function rw_glist($key, $default = []) {
    $v = rw_g($key, '');
    if (!$v) return $default;
    $lines = array_filter(array_map('trim', preg_split('/\n/', $v)));
    return array_values($lines);
}

// ---------- 字段 schema(用于后台编辑框) ----------
function rw_schema() {
    return [
        'hero' => [
            'label' => 'Hero 区',
            'fields' => [
                ['key' => 'hero_eyebrow', 'label' => '眉标', 'type' => 'text'],
                ['key' => 'hero_title',   'label' => '主标题', 'type' => 'textarea'],
                ['key' => 'hero_desc',    'label' => '副标题', 'type' => 'textarea'],
                ['key' => 'hero_cta1',    'label' => '主按钮文案', 'type' => 'text'],
                ['key' => 'hero_cta2',    'label' => '次按钮文案', 'type' => 'text'],
            ],
        ],
        'stats' => [
            'label' => '数据带',
            'fields' => [
                ['key' => 'stats_title', 'label' => '标题', 'type' => 'text'],
                ['key' => 'stats_items', 'label' => '每行一条(数字|说明)', 'type' => 'textarea'],
            ],
        ],
        'capability' => [
            'label' => '核心能力',
            'fields' => [
                ['key' => 'cap_title', 'label' => '标题', 'type' => 'text'],
                ['key' => 'cap_desc',  'label' => '描述', 'type' => 'textarea'],
                ['key' => 'cap_items', 'label' => '每行一条(图标|标题|说明)', 'type' => 'textarea'],
            ],
        ],
        'industry' => [
            'label' => '行业矩阵',
            'fields' => [
                ['key' => 'ind_title', 'label' => '标题', 'type' => 'text'],
                ['key' => 'ind_desc',  'label' => '描述', 'type' => 'textarea'],
                ['key' => 'ind_items', 'label' => '每行一条(行业|场景|标签)', 'type' => 'textarea'],
            ],
        ],
        'process' => [
            'label' => '实施流程',
            'fields' => [
                ['key' => 'proc_title', 'label' => '标题', 'type' => 'text'],
                ['key' => 'proc_desc',  'label' => '描述', 'type' => 'textarea'],
                ['key' => 'proc_items', 'label' => '每行一条(阶段|标题|说明)', 'type' => 'textarea'],
            ],
        ],
        'why' => [
            'label' => '为什么选 Roadway',
            'fields' => [
                ['key' => 'why_title', 'label' => '标题', 'type' => 'text'],
                ['key' => 'why_desc',  'label' => '描述', 'type' => 'textarea'],
                ['key' => 'why_items', 'label' => '每行一条(标题|说明)', 'type' => 'textarea'],
            ],
        ],
        'cta' => [
            'label' => '底部 CTA',
            'fields' => [
                ['key' => 'cta_title', 'label' => '标题', 'type' => 'text'],
                ['key' => 'cta_desc',  'label' => '描述', 'type' => 'textarea'],
                ['key' => 'cta_btn',   'label' => '按钮文案', 'type' => 'text'],
            ],
        ],
        'services' => [
            'label' => '服务页',
            'fields' => [
                ['key' => 'svc_intro',  'label' => '导语', 'type' => 'textarea'],
                ['key' => 'svc_items',  'label' => '每行一条(编号|标题|说明|要点1|要点2|要点3)', 'type' => 'textarea'],
                ['key' => 'svc_deliver', 'label' => '交付物(每行一条)', 'type' => 'textarea'],
            ],
        ],
        'cases' => [
            'label' => '案例页',
            'fields' => [
                ['key' => 'case_intro', 'label' => '导语', 'type' => 'textarea'],
                ['key' => 'case_items', 'label' => '每行一条(行业|标题|问题|方案|成效)', 'type' => 'textarea'],
            ],
        ],
        'contact' => [
            'label' => '联系页',
            'fields' => [
                ['key' => 'contact_intro', 'label' => '导语', 'type' => 'textarea'],
                ['key' => 'contact_items', 'label' => '每行一条(类型|值)', 'type' => 'textarea'],
            ],
        ],
    ];
}

// ---------- 后台编辑框 ----------
add_action('add_meta_boxes', function () {
    foreach (rw_schema() as $group) {
        add_meta_box('rw_fields_' . $group['label'], 'Roadway 内容 — ' . $group['label'], 'rw_render_metabox', 'page', 'normal', 'high');
    }
});

function rw_render_metabox($post, $box) {
    $lang = rw_lang();
    $schema = rw_schema();
    $key = null;
    foreach ($schema as $k => $g) {
        if ('rw_fields_' . $g['label'] === $box['id']) { $key = $k; break; }
    }
    if (!$key) return;
    $group = $schema[$key];

    wp_nonce_field('rw_save_meta', 'rw_meta_nonce');
    echo '<p style="margin:0 0 12px;color:#64748B;font-size:13px">当前语言:<strong>' . esc_html(strtoupper($lang)) . '</strong> — 每个语言独立保存</p>';

    foreach ($group['fields'] as $f) {
        $val = get_post_meta($post->ID, '_rw_' . $f['key'] . '_' . $lang, true);
        if ($val === '' || $val === false) {
            $val = get_post_meta($post->ID, '_rw_' . $f['key'], true);
        }
        echo '<p style="margin-bottom:14px">';
        echo '<label style="display:block;font-weight:600;margin-bottom:5px;font-size:13px">' . esc_html($f['label']) . ' <code style="font-weight:400;color:#94A3B8">' . esc_html($f['key']) . '</code></label>';
        if ($f['type'] === 'textarea') {
            echo '<textarea name="rw_meta[' . esc_attr($f['key']) . ']" rows="5" style="width:100%;font-family:ui-monospace,Menlo,monospace;font-size:13px;line-height:1.6">' . esc_textarea($val) . '</textarea>';
        } else {
            echo '<input type="text" name="rw_meta[' . esc_attr($f['key']) . ']" value="' . esc_attr($val) . '" style="width:100%">';
        }
        echo '</p>';
    }
}

add_action('save_post', function ($post_id, $post, $update) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['rw_meta_nonce']) || !wp_verify_nonce($_POST['rw_meta_nonce'], 'rw_save_meta')) return;
    if (!is_object($post) || $post->post_type !== 'page') return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (!isset($_POST['rw_meta']) || !is_array($_POST['rw_meta'])) return;

    $lang = rw_lang();
    foreach ($_POST['rw_meta'] as $k => $v) {
        update_post_meta($post_id, '_rw_' . sanitize_key($k) . '_' . $lang, wp_kses_post($v));
    }
}, 10, 3);

// ---------- 语言切换器 ----------
/**
 * 生成语言切换按钮
 * 注意:pll_languages_list() 默认返回 slug 字符串数组,不带 url;
 *       要拿各语言首页 URL,得用 pll_home_url() / PLL 内部对象。
 */
function rw_lang_switcher() {
    if (!function_exists('pll_home_url') || !function_exists('pll_languages_list')) return '';

    $slugs = pll_languages_list(['hide_if_no_translation' => false]);
    if (!is_array($slugs) || empty($slugs)) return '';

    $current = rw_lang();
    $out = '<div class="rw-lang">';

    foreach ($slugs as $slug) {
        if (!is_string($slug)) continue;

        // 各语言首页 URL:pll_home_url($slug)
        $url = function_exists('pll_home_url') ? pll_home_url($slug) : home_url('/' . $slug . '/');

        // 标签:语言名
        $name = $slug;
        if (function_exists('pll_translate_string')) {
            $labels = [
                'zh' => '中文',
                'ko' => '한국어',
                'vi' => 'Tiếng Việt',
                'en' => 'English',
                'ja' => '日本語',
            ];
            $name = isset($labels[$slug]) ? $labels[$slug] : strtoupper($slug);
        }

        $cls = ($slug === $current) ? ' class="is-active" aria-current="true"' : '';
        $out .= '<a href="' . esc_url($url) . '"' . $cls . '>' . esc_html($name) . '</a>';
    }

    return $out . '</div>';
}

// ---------- 页面标题(按语言) ----------
function rw_page_title($key, $default = '') {
    return rw_f($key, $default);
}

// ---------- 模板文件注册 ----------
add_filter('theme_page_templates', function ($templates) {
    return array_merge($templates, [
        'page-services.php' => __('Roadway 服务页', 'roadway'),
        'page-cases.php'    => __('Roadway 案例页', 'roadway'),
        'page-contact.php'  => __('Roadway 联系页', 'roadway'),
        'page-blog.php'     => __('Roadway 博客页', 'roadway'),
    ]);
});

// ---------- 去掉 Astra 默认侧栏/页脚 ----------
add_action('after_setup_theme', function () {
    remove_action('astra_sidebar', 'astra_get_sidebar');
}, 20);

// ---------- 面包屑 ----------
function rw_breadcrumb() {
    $items = [];
    $items[] = ['url' => home_url('/'), 'label' => rw_lang() === 'zh' ? '首页' : (rw_lang() === 'ko' ? '홈' : 'Trang chủ')];

    if (is_page()) {
        $id = get_queried_object_id();
        $anc = array_reverse(get_post_ancestors($id));
        foreach ($anc as $a) {
            $items[] = ['url' => get_permalink($a), 'label' => get_the_title($a)];
        }
        $items[] = ['url' => get_permalink($id), 'label' => get_the_title($id)];
    }
    $out = '<nav class="rw-crumb">';
    $last = count($items) - 1;
    foreach ($items as $i => $it) {
        if ($i === $last) {
            $out .= '<span>' . esc_html($it['label']) . '</span>';
        } else {
            $out .= '<a href="' . esc_url($it['url']) . '">' . esc_html($it['label']) . '</a><span class="rw-crumb__sep">/</span>';
        }
    }
    return $out . '</nav>';
}

// ---------- 去掉 WordPress 自动段落包裹 ----------
function rw_strip($html) {
    return trim($html);
}
