# Roadway Vietnam — 三语官方网站

越南北部韩资 / 中资制造业工厂 AI 落地服务商官方网站。

**网址**: https://roadwayvn.com
**语言**: 中文 / 한국어 / Tiếng Việt(Polylang 独立 URL)

---

## 技术栈

| 层 | 选型 | 说明 |
|---|---|---|
| CMS | WordPress 7.x | |
| 主题 | Astra 子主题 `roadway` | 自研,不依赖 Elementor |
| 多语言 | Polylang(免费版) | 三套独立 URL |
| SEO | Rank Math | 三语独立 title / description |
| 字体 | 系统字体栈 | `-apple-system` / `Noto Sans KR` / `Noto Sans SC` |

---

## 目录结构

```
wp-content/
├── themes/roadway/          # 自研主题(全部页面模板)
│   ├── style.css             # 视觉系统 + 全站样式(1247 行)
│   ├── functions.php         # 三语字段系统、后台编辑器、SEO、页面逻辑
│   ├── header.php            # 页头 + 语言切换器
│   ├── footer.php            # 4 栏页脚 + CTA
│   ├── front-page.php        # 首页 9 区块
│   ├── page-services.php     # 服务页模板
│   ├── page-cases.php        # 行业方案页模板
│   └── page-contact.php      # 联系页模板
└── mu-plugins/              # 强制加载插件(PHP 内核级修复)
    ├── fix-locale.php                # 修复 WP 7.x + Polylang 的 locale 警告
    ├── fix-plylang-static-pages.php  # 三语首页映射
    ├── fix-polylang-frontpage.php    # 三语首页关联补丁
    ├── fix-polylang-directory.php    # 强制 Directory 式语言 URL
    ├── fix-polylang-home-url.php     # 首页 URL 规范化 + 关 canonical 跳转
    ├── fix-polylang-clean-urls.php   # 干净 URL 别名(/ko/services/)
    └── roadway-dev-mode.php          # 本地开发辅助
router.php                  # PHP 内置服务器路由(仅本地开发用)
```

---

## URL 结构

```
/zh/                中文首页
/ko/                한국어 홈페이지
/vi/                Trang chủ
/zh/services/       中文服务页
/ko/services/       한국어 서비스
/vi/services/       Dịch vụ
```

语言切换器在右上角,按 Polylang 翻译关系跳转到同一内容的对应语言页。

---

## 本地开发

```bash
# 1. 启动 PHP 内置服务器
cd html/
php -S 0.0.0.0:8088 router.php

# 2. 用 Host 头模拟正式域名(否则会被 canonical 重定向)
curl -H "Host: roadwayvn.com" http://127.0.0.1:8088/zh/
```

### router.php 是干什么的

PHP 内置服务器(`php -S`)**不读 `.htaccess`**,所以 WordPress 的漂亮链接全部失效。
`router.php` 从数据库读 `rewrite_rules` option,自己实现一遍 rewrite 匹配。

关键点:WordPress 存的 rewrite key 形如 `"^....$"`(自带 `^` 前缀和 `$` 后缀),
拼 PCRE 时必须先剥掉,否则会变成 `#^^...$$#` 永远匹配失败。

生产环境用 Apache / Nginx + 标准 WordPress rewrite,`router.php` 不需要。

---

## 生产部署

### 环境要求

- PHP 8.2+
- MySQL 8.0+ / MariaDB 10.6+
- Apache(mod_rewrite)或 Nginx
- WordPress 6.5+

### 步骤

```bash
# 1. 复制主题和 mu-plugins
cp -r wp-content/themes/roadway /var/www/html/wp-content/themes/
cp -r wp-content/mu-plugins/* /var/www/html/wp-content/mu-plugins/

# 2. 配置 wp-config.php(见 wp-config.example.php)
#    必须加外部 HTTP 阻断,否则 WP 会连 WordPress.org 超时导致 fatal error

# 3. 启用主题和插件
wp theme activate roadway
wp plugin activate polylang rank-math

# 4. 设置固定链接并刷新规则
wp rewrite structure '/%postname%/' --hard
wp rewrite flush --hard

# 5. 建三语页面(见 docs/PAGES.md)
```

---

## 三语内容管理

内容不写死在模板里,全部存在 post meta:

| meta key | 用途 |
|---|---|
| `_rw_svc_items_{lang}` | 服务页条目 |
| `_rw_case_items_{lang}` | 案例页条目 |
| `_rw_contact_items_{lang}` | 联系页信息 |
| `_rw_cta_title_{lang}` | CTA 标题 |

`lang` 取值 `zh` / `ko` / `vi`。

后台编辑框在 `functions.php` 里注册,编辑页面时可直接填。

### 案例页条目格式

用 `|` 分隔字段,换行分隔条目:

```
标题|副标题|描述|要点1;要点2;要点3
```

---

## 视觉系统

| 项 | 值 |
|---|---|
| 主品牌蓝 | `#046BD2` |
| 蓝色色板 | 50 `#EEF5FE` → 950 `#021F42`(11 档) |
| 中性灰 | 50 `#F8FAFC` → 950 `#020617`(13 档) |
| 语义色 | 成功 `#0F9D58` / 警告 `#D97706` / 危险 `#DC2626` / 信息 `#0369A1` |
| 间距 | 4px 基准 |
| 圆角 | 7 档 |
| 容器 | 1200px |

---

## 已知问题 / 注意事项

1. **Polylang 免费版要求各语言 slug 全局唯一**
   所以 ko/vi 页面的 `post_name` 必须带后缀(`services-ko`)。
   `fix-polylang-clean-urls.php` 用 rewrite 别名让 `/ko/services/` 也能访问。

2. **`redirect_lang` 必须关掉**
   否则访问 `/zh/` 会被 301 到根路径,语言切换器行为不一致。

3. **WordPress 会连 WordPress.org**
   被墙环境下每次访问可能 30 秒超时。`wp-config.php` 必须加:
   ```php
   define( 'WP_HTTP_BLOCK_EXTERNAL', true );
   define( 'AUTOMATIC_UPDATER_DISABLED', true );
   define( 'WP_AUTO_UPDATE_CORE', false );
   ```

---

## License

© Roadway Vietnam. 保留所有权利。
