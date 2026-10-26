# AGENTS.md — Roadway 越南三语官网

> 本文件是 Claude Code / Hermes 在本项目工作时的**唯一自动读取的规则**。
> 改动本项目前必读。改完请同步更新本文件对应段落。

## 项目是什么

越南北部(河内 / 海防 / 太原)韩资 / 中资制造业工厂 AI 落地服务商官方网站。
**网址**: https://roadwayvn.com · **三语**: 中文 / 한국어 / Tiếng Việt(Polylang 独立 URL)

## 硬约束(违反就是错)

1. **只改 `wp-content/` 里的文件**。WordPress 核心(`wp-includes/` / `wp-admin/`)和
   `wp-content/plugins/` 里的第三方插件**一律不动** —— 升级会覆盖,且引入安全风险。
2. **不动 `wp-content/mu-plugins/` 里的 7 个补丁**。它们是 Polylang 在 WP 7.x 下的
   血泪修复(locale 警告 / 三语首页映射 / Directory 式 URL / 干净别名 / canonical 规范化)。
   改这些文件前先读 `docs/PAGES.md` 里记录的问题。
3. **不装 Elementor、不装页面构建器**。所有页面模板走自研 Astra 子主题 `roadway`。
4. **三语必须同步**。任何新增/修改的文案字段,中/韩/越三份都要写,不允许留空或写占位符。
5. **`router.php` 只用于本地开发**(PHP 内置服务器)。生产环境是 Nginx,不要把
   `router.php` 的 rewrite 逻辑搬进 Nginx —— 用标准 WordPress permalink 规则。

## 技术栈

| 层 | 选型 |
|---|---|
| CMS | WordPress 7.x |
| 主题 | Astra 子主题 `roadway`(自研,所有页面模板) |
| 多语言 | Polylang 免费版,Directory 式 URL |
| SEO | Rank Math,三语独立 title / description |
| 数据库 | MariaDB(生产用云端 RDS MySQL,**不是 PostgreSQL**) |
| 字体 | 系统字体栈 `-apple-system` / `Noto Sans KR` / `Noto Sans SC` |

## 关键文件

```
wp-content/themes/roadway/
  style.css          视觉系统 + 全站样式(1247 行)
  functions.php      三语字段系统 / 后台编辑器 / SEO / 页面逻辑
  header.php         页头 + 语言切换器
  footer.php         4 栏页脚 + CTA
  front-page.php     首页 9 区块
  page-services.php  服务页
  page-cases.php     行业方案页
  page-contact.php   联系页

wp-content/mu-plugins/   7 个 Polylang 兼容补丁(只读,除非明确要修)
router.php                本地开发路由
docs/PAGES.md             页面结构与踩坑记录
```

## 数据存储约定

页面文案存在 **postmeta**,键名约定:

| 后缀 | 用途 | 例子 |
|---|---|---|
| `_zh` | 中文 | `_rw_svc_deliver_zh` |
| `_ko` | 韩文 | `_rw_svc_deliver_ko` |
| `_vi` | 越南文 | `_rw_svc_deliver_vi` |

**⚠️ 已知坑**:`rw_list()` 解析时,数据用 `|` 分隔或用换行分隔**两种都要支持**。
历史上只按换行分条,导致 `|` 分隔的 6 条交付物被并成 1 条。改这个函数时先看 `docs/PAGES.md`。

## 本地开发

```bash
cd ~/roadwayvn-website/html
php -S localhost:8080 router.php     # PHP 内置服务器
wp eval 'echo get_post_meta(8, "_rw_svc_deliver_zh", true);'   # 查数据
wp eval '...'                        # 任意 PHP 调试
```

- 数据库凭据在 `wp-config.php`
- `roadway-dev-mode.php` 提供本地调试辅助
- 改完必须三语都验一遍(至少:首页 / 服务页 / 案例页 / 联系页)

## 部署目标(2026-11)

阿里云 **新加坡** ECS `ecs.t6-c1m2.large`(2 vCPU 4 GiB),Ubuntu 22.04,ESSD 40GiB。
**不走 RDS**(WordPress 用 MariaDB,自建在 ECS 上)。

生产环境用 Nginx + PHP-FPM + MariaDB,rewrite 用 WordPress 标准 permalink,
**不要用 `router.php`**。

## 沟通规范

- 老板(janus zhang)偏好:大白话短句,不堆术语;技术诊断给「一句话结论 + 为什么 + 怎么办」
- 交付物必须同步到 Obsidian vault:`~/Desktop/越南制造业知识库/`,按主题建子文件夹
- 报告/方案/PPT 一律出中韩越三语版
