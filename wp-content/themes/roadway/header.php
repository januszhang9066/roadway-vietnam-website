<?php
/**
 * Roadway Vietnam — 页头
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="https://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="rw-skip" href="#rw-main"><?php echo esc_html(rw_lang() === 'zh' ? '跳到主要内容' : (rw_lang() === 'ko' ? '본문으로 건너뛰기' : 'Chuyển đến nội dung')); ?></a>

<header class="rw-header">
  <div class="rw-container rw-header__inner">
    <a class="rw-logo" href="<?php echo esc_url(home_url('/')); ?>">
      <span class="rw-logo__mark" aria-hidden="true">
        <svg viewBox="0 0 32 32" width="32" height="32" fill="none">
          <rect width="32" height="32" rx="8" fill="#046BD2"/>
          <path d="M9 22V10h3.4l4.1 7.2V10H20v12h-3.3l-4.2-7.3V22H9z" fill="#fff"/>
        </svg>
      </span>
      <span class="rw-logo__text">
        <strong>Roadway</strong>
        <em><?php echo esc_html(rw_lang() === 'zh' ? '越南制造业 AI' : (rw_lang() === 'ko' ? '베트남 제조 AI' : 'AI cho sản xuất VN')); ?></em>
      </span>
    </a>

    <nav id="rw-nav" class="rw-nav" aria-label="<?php echo esc_attr(rw_lang() === 'zh' ? '主导航' : (rw_lang() === 'ko' ? '주요 메뉴' : 'Điều hướng')); ?>">
      <?php
      $l = rw_lang();
      $nav = $l === 'zh' ? [
          ['服务', '/services/'],
          ['行业方案', '/cases/'],
          ['洞察', '/blog/'],
          ['联系', '/contact/'],
      ] : ($l === 'ko' ? [
          ['서비스', '/ko/services/'],
          ['산업 솔루션', '/ko/cases/'],
          ['인사이트', '/ko/blog/'],
          ['문의하기', '/ko/contact/'],
      ] : [
          ['Dịch vụ', '/vi/services/'],
          ['Giải pháp ngành', '/vi/cases/'],
          ['Góc nhìn', '/vi/blog/'],
          ['Liên hệ', '/vi/contact/'],
      ]);
      foreach ($nav as $n) {
          printf('<a href="%s">%s</a>', esc_url(home_url($n[1])), esc_html($n[0]));
      }
      ?>
    </nav>

    <div class="rw-header__actions">
      <?php echo rw_lang_switcher(); ?>
      <a class="rw-btn rw-btn--primary rw-btn--sm" href="<?php echo esc_url(home_url(rw_lang() === 'zh' ? '/contact/' : (rw_lang() === 'ko' ? '/ko/contact/' : '/vi/contact/'))); ?>">
        <?php echo esc_html(rw_lang() === 'zh' ? '预约咨询' : (rw_lang() === 'ko' ? '상담 신청' : 'Đặt lịch tư vấn')); ?>
      </a>
    </div>

    <button class="rw-burger" id="rw-burger" aria-label="Menu" aria-expanded="false" aria-controls="rw-nav">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

<main id="rw-main" class="rw-main">
