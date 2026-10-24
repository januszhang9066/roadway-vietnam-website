<?php
/**
 * WordPress 配置示例文件
 *
 * 复制为 wp-config.php 后填入真实值。
 * ⚠️ wp-config.php 已被 .gitignore 排除,永远不会进 Git 仓库。
 *
 * Roadway 定制要点见下方「必须添加的常量」一节。
 */

// ============================================================
// 必须修改的数据库信息
// ============================================================
define( 'DB_NAME', '[REDACTED]' );
define( 'DB_USER', '[REDACTED]' );
define( 'DB_PASSWORD', '[REDACTED]' );
define( 'DB_HOST', '127.0.0.1' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );

// ============================================================
// 必须修改的盐和密钥(用 https://api.wordpress.org/secret-key/1.1/salt 生成)
// ============================================================
define( 'AUTH_KEY', '[REDACTED]' );
define( 'SECURE_AUTH_KEY', '[REDACTED]' );
define( 'LOGGED_IN_KEY', '[REDACTED]' );
define( 'NONCE_KEY', '[REDACTED]' );
define( 'AUTH_SALT', '[REDACTED]' );
define( 'SECURE_AUTH_SALT', '[REDACTED]' );
define( 'LOGGED_IN_SALT', '[REDACTED]' );
define( 'NONCE_SALT', '[REDACTED]' );

$table_prefix = 'wp_';

// ============================================================
// 必须添加的常量 —— Roadway 越南环境专用
// ============================================================

// 1) 阻断所有外部 HTTP 请求
//    原因:WordPress 每次访问都去连 WordPress.org / api.wordpress.com,
//    在越南网络环境下会 30 秒超时,最终抛出 fatal error 白屏。
//    后果:插件更新、主题更新、wp.org 的功能全部不可用(可接受)。
if ( ! defined( 'WP_HTTP_BLOCK_EXTERNAL' ) ) {
    define( 'WP_HTTP_BLOCK_EXTERNAL', true );
}

// 2) 允许访问的域名白名单(如果确实需要某个外部 API,加进来)
//    注意:加入白名单后仍可能被墙导致超时,谨慎。
if ( ! defined( 'WP_ACCESSIBLE_HOSTS' ) ) {
    define( 'WP_ACCESSIBLE_HOSTS', 'api.qwen.ai,dashscope.aliyuncs.com' );
}

// 3) 关闭自动更新(配合上面的外部请求阻断)
//    否则后台每次打开都会尝试连 wp.org
if ( ! defined( 'AUTOMATIC_UPDATER_DISABLED' ) ) {
    define( 'AUTOMATIC_UPDATER_DISABLED', true );
}
if ( ! defined( 'WP_AUTO_UPDATE_CORE' ) ) {
    define( 'WP_AUTO_UPDATE_CORE', false );
}

// 4) 正式域名(不要用带端口的临时地址)
//    roadwayvn.com 部署到 ECS 后,home 和 siteurl 都用这个。
// if ( ! defined( 'WP_HOME' ) ) {
//     define( 'WP_HOME', 'https://roadwayvn.com' );
// }
// if ( ! defined( 'WP_SITEURL' ) ) {
//     define( 'WP_SITEURL', 'https://roadwayvn.com' );
// }

// ============================================================
// 标准 WordPress 配置(保持原样)
// ============================================================
define( 'WP_DEBUG', false );
define( 'WP_DEBUG_LOG', false );
define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_POST_REVISIONS', 10 );
define( 'FS_METHOD', 'direct' );

// ============================================================
// 载入 wp-settings.php
// ============================================================
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}

require_once ABSPATH . 'wp-settings.php';
