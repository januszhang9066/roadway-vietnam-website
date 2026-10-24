<?php
/**
 * PHP 内置服务器路由脚本 — 支持 WordPress 漂亮链接 + Polylang 语言前缀
 * 用法:php -S 0.0.0.0:8080 router.php
 *
 * PHP 内置服务器不读 .htaccess,所以要自己实现 rewrite:
 * 1. 静态文件直接返回
 * 2. 读 WP 的 rewrite_rules option,按顺序匹配
 * 3. 匹配到就把 query vars 塞进 $_GET,交给 index.php
 */

// 存在的静态文件直接返回
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

$root = __DIR__;
$file = $root . $uri;

// 目录请求 → 试 index.php
if (is_dir($file) && file_exists(rtrim($file, '/') . '/index.php')) {
    $_SERVER['SCRIPT_NAME'] = rtrim($uri, '/') . '/index.php';
    require rtrim($file, '/') . '/index.php';
    return true;
}

// 静态文件直接返回
if ($uri !== '/' && file_exists($file) && !is_dir($file)) {
    return false;
}

// ---------- 读 WordPress rewrite rules 并匹配 ----------
// 需要先加载 WP 才能拿到 option,但加载 WP 就会执行请求。
// 所以这里用轻量方式:直接读数据库拿 rewrite_rules。
$rules = null;
if (defined('ABSPATH')) {
    $rules = get_option('rewrite_rules');
} else {
    // 手动加载 wp-config 拿 DB 凭据,只取 rewrite_rules
    $wp_config = $root . '/wp-config.php';
    if (file_exists($wp_config)) {
        // 用独立连接读 option,避免完整加载 WP
        $rules = roadway_read_rewrite_rules($root);
    }
}

if (is_array($rules) && !empty($rules)) {
    $path = ($uri === '' ? '/' : $uri);
    $qs = '';

    // 关键:WordPress 的规则数组顺序 = 优先级,但 Polylang 的语言规则
    // 排在通用 pagename 规则之后,导致 /ko/services/ 被当成
    // pagename="ko/services" 而 404。
    //
    // 分三组,按优先级依次匹配:
    //   1. 别名规则  ^(ko)/([^/]+)/?$   —— 干净 URL,最高优先
    //   2. 语言规则  (ko|zh|vi)/...      —— Polylang 原生规则
    //   3. 全部规则                      —— 兜底
    $aliasRules = [];
    $langRules  = [];
    $allRules   = [];
    foreach ($rules as $regex => $query) {
        $allRules[$regex] = $query;
        $rr = ltrim((string)$regex, '^');

        // 1) 别名规则:精确匹配 ^(ko)/([^/]+)/?$ 这种形态
        //    —— 单语言前缀 + 通用 slug + 可选尾斜杠
        if (preg_match('#^\([a-z]{2,4}\)/\(\[\^/\]\+\)/\?\$$#', $rr)) {
            $aliasRules[$regex] = $query;
        }
        // 2) Polylang 多语言规则
        elseif (preg_match('#^\([a-z]{2,4}(?:\|[a-z]{2,4})+\)/#', $rr)) {
            $langRules[$regex] = $query;
        }
    }
    $ruleSets = empty($aliasRules) ? [$langRules, $allRules] : [$aliasRules, $langRules, $allRules];

    foreach ($ruleSets as $ruleSet) {
        foreach ($ruleSet as $regex => $query) {
            // WordPress 存的 rewrite key 形如 "^....$" —— 自带 ^ 前缀和 $ 后缀。
            // 拼 pattern 时必须先剥掉,否则会变成 "#^^...$$#" 永远匹配失败。
            $body = ltrim((string)$regex, '^');
            if (substr($body, -1) === '$' && substr($body, -2, 1) !== '\\') {
                $body = substr($body, 0, -1);
            }
            $pattern = '#^' . $body . '$#';
            if (!preg_match($pattern, $path, $matches)) {
                continue;
            }

            // $matches[n] 形式
            $query = preg_replace_callback('/\$matches\[(\d+)\]/', function ($m) use ($matches) {
                return isset($matches[(int)$m[1]]) ? $matches[(int)$m[1]] : '';
            }, $query);

            // $1 $2 形式
            $query = preg_replace_callback('/\$(\d)/', function ($m) use ($matches) {
                return isset($matches[(int)$m[1]]) ? $matches[(int)$m[1]] : '';
            }, $query);

            $qs = $query;
            break 2;
        }
    }

    if ($qs !== '') {
        $parts = [];
        foreach (explode('&', $qs) as $pair) {
            if ($pair === '') continue;
            $kv = explode('=', $pair, 2);
            $k = urldecode($kv[0]);
            $v = isset($kv[1]) ? urldecode($kv[1]) : '';
            $parts[$k] = $v;
            $_GET[$k] = $v;
        }
        if (!empty($parts)) {
            $_SERVER['QUERY_STRING'] = http_build_query($parts);
        }
    }
}

// 其余全部交给 WordPress 路由
$_SERVER['SCRIPT_NAME'] = '/index.php';
require $root . '/index.php';
return true;

/**
 * 轻量读取 rewrite_rules(不完整加载 WP)
 */
function roadway_read_rewrite_rules($root) {
    static $cache = null;
    if ($cache !== null) return $cache;

    $cache = false;

    // 解析 wp-config 里的 DB 凭据
    $src = @file_get_contents($root . '/wp-config.php');
    if ($src === false) return $cache;

    $defines = [];
    if (preg_match_all("/define\(\s*'([A-Z_]+)'\s*,\s*'([^']*)'\s*\)/", $src, $m, PREG_SET_ORDER)) {
        foreach ($m as $d) {
            $defines[$d[1]] = $d[2];
        }
    }

    if (empty($defines['DB_NAME']) || empty($defines['DB_USER'])) {
        return $cache;
    }

    $host = $defines['DB_HOST'] ?? '127.0.0.1';
    $port = 3306;
    if (strpos($host, ':') !== false) {
        [$host, $port] = explode(':', $host, 2);
    }

    $dsn = "mysql:host={$host};port={$port};dbname={$defines['DB_NAME']};charset=utf8mb4";
    try {
        $pdo = new PDO($dsn, $defines['DB_USER'], $defines['DB_PASSWORD'] ?? '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
            PDO::ATTR_TIMEOUT => 3,
        ]);
        $prefix = isset($defines['table_prefix']) ? $defines['table_prefix'] : 'wp_';
        if (!isset($defines['table_prefix'])) {
            if (preg_match('/\$table_prefix\s*=\s*[\'"]([^\'"]+)[\'"]/', $src, $pm)) {
                $prefix = $pm[1];
            }
        }
        $stmt = $pdo->prepare("SELECT option_value FROM {$prefix}options WHERE option_name = 'rewrite_rules' LIMIT 1");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && $row['option_value']) {
            $cache = @unserialize($row['option_value']);
            if (!is_array($cache)) $cache = false;
        }
    } catch (Throwable $e) {
        $cache = false;
    }

    return $cache;
}
