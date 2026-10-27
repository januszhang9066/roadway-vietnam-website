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
    // 移动端导航(汉堡菜单)交互脚本
    wp_enqueue_script('roadway-nav', get_template_directory_uri() . '/assets/js/nav.js', [], '1.0.0', true);
});

// Astra 的 header/footer 由本主题接管
// 不能删:删了 Astra 父主题会自己输出 header/footer,导致双 header/双 footer
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

/**
 * 多行字段 → 数组
 *
 * 两种数据格式都要支持(数据库里两种都有):
 *   A. 多行格式:一行一条,行内用 | 分字段
 *      适用 stats_items / cap_items / case_items / contact_items
 *      → 按换行切,行内的 | 留给模板 explode('|')
 *
 *   B. 单行格式:整条值用 | 分隔,没有换行
 *      适用 svc_deliver(每个 | 段就是一条交付物)
 *      → 按 | 分
 *
 * 注意:绝不能按 " / " 切。数据里有 "对接 Lark / Telegram / 企业微信"
 * 这种内容,按 " / " 切会把一行拦腰截断。
 */
function rw_list($key, $default = []) {
    $v = rw_f($key, '');
    if (!$v) return $default;
    $v = trim($v);
    if ($v === '') return $default;

    // 有换行 → 多行格式,按行分
    if (preg_match('/\R/u', $v)) {
        $lines = array_filter(array_map('trim', preg_split('/\R/u', $v)));
        return array_values($lines);
    }

    // 无换行 → 单行格式,按 | 分
    $parts = array_filter(array_map('trim', explode('|', $v)));
    return $parts ? array_values($parts) : $default;
}

/**
 * 单行多字段字段 → 数组(每行 = 一组 | 分隔的字段)
 *
 * 用于 svc_items:数据是「序号|标题|描述|要点1|要点2」,
 * 但整条值写在一行里、没有换行,所以 rw_list 会把它按 | 拆散。
 * 这个函数保持整条不拆,由模板 explode('|') 处理字段。
 */
function rw_rows($key, $default = []) {
    $v = rw_f($key, '');
    if (!$v) return $default;
    $v = trim($v);
    if ($v === '') return $default;

    // 有换行 → 已经是多行格式,直接按行分
    if (preg_match('/\R/u', $v)) {
        $lines = array_filter(array_map('trim', preg_split('/\R/u', $v)));
        return array_values($lines);
    }

    // 单行:整条就是一组字段,包成单元素数组
    return [$v];
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
                ['key' => 'stats_items', 'label' => '每行一条(数字|说明)', 'type' => 'textarea'],
            ],
        ],
        'capability' => [
            'label' => '核心能力',
            'fields' => [
                ['key' => 'cap_eyebrow', 'label' => '眉标', 'type' => 'text'],
                ['key' => 'cap_title', 'label' => '标题', 'type' => 'text'],
                ['key' => 'cap_desc',  'label' => '描述', 'type' => 'textarea'],
                ['key' => 'cap_items', 'label' => '每行一条(图标|标题|说明)', 'type' => 'textarea'],
            ],
        ],
        'industry' => [
            'label' => '行业矩阵',
            'fields' => [
                ['key' => 'ind_eyebrow', 'label' => '眉标', 'type' => 'text'],
                ['key' => 'ind_title', 'label' => '标题', 'type' => 'text'],
                ['key' => 'ind_desc',  'label' => '描述', 'type' => 'textarea'],
                ['key' => 'ind_items', 'label' => '每行一条(行业|场景|标签)', 'type' => 'textarea'],
            ],
        ],
        'process' => [
            'label' => '实施流程',
            'fields' => [
                ['key' => 'proc_eyebrow', 'label' => '眉标', 'type' => 'text'],
                ['key' => 'proc_title', 'label' => '标题', 'type' => 'text'],
                ['key' => 'proc_desc',  'label' => '描述', 'type' => 'textarea'],
                ['key' => 'proc_items', 'label' => '每行一条(阶段|标题|说明)', 'type' => 'textarea'],
            ],
        ],
        'why' => [
            'label' => '为什么选 Roadway',
            'fields' => [
                ['key' => 'why_eyebrow', 'label' => '眉标', 'type' => 'text'],
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

    // 后台编辑语境下 rw_lang() 可能取到前台语言,优先用文章自身语言(Polylang)
    $lang = rw_post_lang($post_id);
    foreach ($_POST['rw_meta'] as $k => $v) {
        update_post_meta($post_id, '_rw_' . sanitize_key($k) . '_' . $lang, wp_kses_post($v));
    }
}, 10, 3);

/**
 * 读取某篇文章所属语言(后台保存时比 pll_current_language 更可靠)
 */
function rw_post_lang($post_id) {
    if (function_exists('pll_get_post_language')) {
        $l = pll_get_post_language($post_id, 'slug');
        if ($l) return $l;
    }
    return rw_lang();
}

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

// ---------- 模板文件注册 ----------
add_filter('theme_page_templates', function ($templates) {
    return array_merge($templates, [
        'page-services.php' => __('Roadway 服务页', 'roadway'),
        'page-cases.php'    => __('Roadway 案例页', 'roadway'),
        'page-contact.php'  => __('Roadway 联系页', 'roadway'),
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

// ---------- 联系表单处理(admin-post) ----------
/**
 * 前端表单 action=rw_contact 提交到 admin-post.php,由这里接收。
 * 防 spam:honeypot + nonce;入库:post(type=rw_inquiry);通知:admin_email。
 */
add_action('admin_post_nopriv_rw_contact', 'rw_handle_contact');
add_action('admin_post_rw_contact', 'rw_handle_contact');
function rw_handle_contact() {
    // honeypot:隐藏字段被填写 → 视为机器人,静默跳回
    if (!empty($_POST['company_website'])) {
        wp_safe_redirect(wp_get_referer() ?: home_url('/contact/'));
        exit;
    }
    $nonce_ok = isset($_POST['rw_form_nonce']) && wp_verify_nonce($_POST['rw_form_nonce'], 'rw_contact_submit');

    $name    = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    $company = isset($_POST['company']) ? sanitize_text_field(wp_unslash($_POST['company'])) : '';
    $email   = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $phone   = isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '';
    $scale   = isset($_POST['scale']) ? sanitize_text_field(wp_unslash($_POST['scale'])) : '';
    $message = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';

    $redirect = wp_get_referer() ?: home_url('/contact/');
    $error    = '';

    if (!$nonce_ok) {
        $error = 'form';
    } elseif ($name === '' || $company === '' || $message === '' || !is_email($email)) {
        $error = 'required';
    } else {
        $post_id = wp_insert_post([
            'post_type'   => 'post',
            'post_status' => 'private',
            'post_title'  => sprintf('[询盘] %s · %s · %s', $company, $name, gmdate('Y-m-d H:i')),
            'post_content' => sprintf(
                "公司: %s\n姓名: %s\n邮箱: %s\n电话: %s\n工厂规模: %s\n\n需求:\n%s",
                $company, $name, $email, $phone, $scale, $message
            ),
        ]);
        if (!is_wp_error($post_id)) {
            update_post_meta($post_id, '_rw_inquiry_email', $email);
            @wp_mail(get_option('admin_email'),
                sprintf('Roadway 新询盘:%s(%s)', $company, $name),
                wp_strip_all_tags((string) get_post_field('post_content', $post_id)));
        }
        $redirect = add_query_arg('inquiry', 'success', $redirect);
    }

    if ($error) {
        $redirect = add_query_arg(['inquiry' => 'error', 'reason' => $error,
            'rw_name' => rawurlencode($name), 'rw_company' => rawurlencode($company),
            'rw_email' => rawurlencode($email)], $redirect);
    }
    wp_safe_redirect($redirect);
    exit;
}
