# 三语页面管理

WordPress Post ID 对照表:

| 内容 | 中文 (zh) | 한국어 (ko) | Tiếng Việt (vi) | 模板 |
|---|---|---|---|---|
| 首页 | 7 | 11 | 15 | `front-page.php` |
| 服务 | 8 | 12 | 16 | `page-services.php` |
| 联系 | 9 | 13 | 17 | `page-contact.php` |
| 行业方案 | 19 | 20 | 21 | `page-cases.php` |
| 博客 | 10 | 14 | 18 | — |

> Post ID 取决于实际安装顺序。换机器部署时用下面的命令查。

---

## 查询页面 ID

```bash
cd html/
wp post list --post_type=page --fields=ID,post_title,post_name --format=table
```

```bash
# 带语言列
wp eval '
foreach (get_posts(["post_type"=>"page","numberposts"=>-1]) as $p) {
    printf("#%d  %-6s %-16s %s\n",
        $p->ID,
        pll_get_post_language($p->ID),
        $p->post_name,
        $p->post_title);
}'
```

---

## 翻译关联

```bash
# 首页
wp eval 'pll_save_post_translations([7=>7, 11=>11, 15=>15]);'
# 服务页
wp eval 'pll_save_post_translations([8=>8, 12=>12, 16=>16]);'
# 联系页
wp eval 'pll_save_post_translations([9=>9, 13=>13, 17=>17]);'
# 行业方案页
wp eval 'pll_save_post_translations([19=>19, 20=>20, 21=>21]);'
# 博客
wp eval 'pll_save_post_translations([10=>10, 14=>14, 18=>18]);'
```

---

## 设置页面语言

```bash
wp eval '
wp_set_object_terms(7,  "zh", "language");
wp_set_object_terms(11, "ko", "language");
wp_set_object_terms(15, "vi", "language");
'
```

---

## 设置 slug

Polylang 免费版要求各语言 slug 全局唯一,所以 ko/vi 必须带语言后缀:

```bash
wp eval '
global $wpdb;
$map = [
  11=>"home-ko",     12=>"services-ko",  13=>"contact-ko",  14=>"blog-ko",
  15=>"home-vi",     16=>"services-vi",  17=>"contact-vi",  18=>"blog-vi",
  20=>"cases-ko",    21=>"cases-vi",
];
foreach ($map as $id=>$slug) {
    $wpdb->update($wpdb->posts, ["post_name"=>$slug], ["ID"=>$id]);
    clean_post_cache($id);
}
echo "slugs updated\n";'
```

⚠️ 用 `$wpdb->update` 而不是 `wp_update_post` —— 后者会触发 WordPress 的全局唯一性检查,
把 `services-ko` 改成 `services-ko-2`。

---

## 注入内容(meta)

内容存在 post meta,key 规则是 `_rw_{字段}_{语言}`。

```bash
# 读取
wp eval 'echo get_post_meta(8, "_rw_svc_items_zh", true);'

# 写入(base64 避免换行符被 shell 吃掉)
wp eval '
$val = "第一行|第二行|第三行";
update_post_meta(8, "_rw_svc_items_zh", base64_decode(base64_encode($val)));
echo "ok\n";'
```

### 字段清单

| 字段 | 用在 |
|---|---|
| `_rw_svc_intro_{lang}` | 服务页引言 |
| `_rw_svc_items_{lang}` | 服务页条目 |
| `_rw_svc_deliver_{lang}` | 交付物清单 |
| `_rw_case_intro_{lang}` | 行业方案页引言 |
| `_rw_case_items_{lang}` | 行业方案条目 |
| `_rw_contact_intro_{lang}` | 联系页引言 |
| `_rw_contact_items_{lang}` | 联系信息 |
| `_rw_cta_title_{lang}` | CTA 标题 |
| `_rw_cta_desc_{lang}` | CTA 描述 |
| `_rw_cta_btn_{lang}` | CTA 按钮文字 |

### 条目分隔格式

```
标题|副标题|描述|要点1;要点2;要点3
```

换行 = 新条目。

---

## 设置模板

```bash
wp eval '
$map = [8=>"page-services", 12=>"page-services", 16=>"page-services",
        19=>"page-cases",   20=>"page-cases",   21=>"page-cases",
        9=>"page-contact",  13=>"page-contact",  17=>"page-contact"];
foreach ($map as $id=>$tpl) { update_post_meta($id, "_wp_page_template", $tpl); }
echo "templates set\n";'
```

---

## SEO(Rank Math)

```bash
wp eval '
update_post_meta(7, "rank_math_title", "...");
update_post_meta(7, "rank_math_description", "...");
echo "seo set\n";'
```

---

## Polylang 配置

```bash
wp option get polylang
```

关键字段:

| 字段 | 值 | 说明 |
|---|---|---|
| `force_lang` | `1` | Directory 式 URL(`/zh/` `/ko/` `/vi/`) |
| `hide_default` | `0` | 默认语言也带前缀 |
| `rewrite` | `1` | 启用漂亮链接 |
| `redirect_lang` | `0` | **必须关**,否则 `/zh/` 会被 301 到根路径 |

修改后必须刷新 rewrite:

```bash
wp rewrite flush --hard
```

---

## 验证清单

```bash
# 15 个页面全部应返回 200
for lang in zh ko vi; do
  for page in "" services cases contact blog; do
    u="/$lang/$page/"
    [ -z "$page" ] && u="/$lang/"
    code=$(curl -sL -o /dev/null -w "%{http_code}" -H "Host: roadwayvn.com" "http://127.0.0.1:8088$u")
    echo "$code  $u"
  done
done
```

---

## 已知坑

1. **Polylang 免费版 slug 全局唯一** → ko/vi 必须带后缀,用 rewrite 别名提供干净 URL

2. **`redirect_lang` 默认开** → `/zh/` 会 301 到 `/`,必须关掉

3. **`save_post` hook 参数数** → `add_action('save_post', fn, 10, 3)`,用 2 参会 fatal

4. **`pll_languages_list()` 返回 slug 字符串数组** → 取 URL 要用 `pll_home_url($slug)`

5. **WordPress 连 wp.org 超时** → `WP_HTTP_BLOCK_EXTERNAL`

6. **WordPress 存 rewrite key 带 `^` 和 `$`** → router 拼正则时必须先剥掉
