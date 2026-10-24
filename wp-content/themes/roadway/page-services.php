<?php
/**
 * Roadway Vietnam — 服务页
 */
get_header();
$l = rw_lang();

$svc = rw_list('svc_items');
$deliver = rw_list('svc_deliver');
?>

<section class="rw-pagehead">
  <div class="rw-container">
    <?php echo rw_breadcrumb(); ?>
    <h1><?php echo esc_html(get_the_title()); ?></h1>
    <?php if (rw_f('svc_intro')): ?>
      <p><?php echo esc_html(rw_f('svc_intro')); ?></p>
    <?php endif; ?>
  </div>
</section>

<?php if ($svc): ?>
<section class="rw-section">
  <div class="rw-container">
    <div class="rw-svcs">
      <?php foreach ($svc as $i => $row):
          $p = array_map('trim', explode('|', $row));
          $no    = $p[0] ?? '';
          $title = $p[1] ?? '';
          $desc  = $p[2] ?? '';
          $pts   = array_slice($p, 3);
      ?>
        <article class="rw-svc">
          <div class="rw-svc__head">
            <span class="rw-svc__no"><?php echo esc_html($no); ?></span>
            <h2 class="rw-svc__title"><?php echo esc_html($title); ?></h2>
            <?php if ($desc): ?>
              <p class="rw-svc__desc"><?php echo esc_html($desc); ?></p>
            <?php endif; ?>
          </div>
          <?php if ($pts): ?>
            <ul class="rw-svc__points">
              <?php foreach ($pts as $pt): ?>
                <li><span class="rw-feature__check">✓</span><span><?php echo esc_html($pt); ?></span></li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($deliver): ?>
<section class="rw-section rw-section--gray">
  <div class="rw-container">
    <div class="rw-section__head rw-section__head--center">
      <span class="rw-eyebrow"><?php echo esc_html($l === 'zh' ? '交付物' : ($l === 'ko' ? '전달물' : 'Bàn giao')); ?></span>
      <h2 class="rw-h2"><?php echo esc_html($l === 'zh' ? '每个项目你会拿到什么' : ($l === 'ko' ? '프로젝트마다 받는 것' : 'Bạn nhận được gì')); ?></h2>
    </div>
    <div class="rw-grid rw-grid--3">
      <?php foreach ($deliver as $d): ?>
        <div class="rw-card rw-card--flat">
          <span class="rw-card__tick">✓</span>
          <p class="rw-card__text" style="color:var(--rw-gray-800);font-weight:550"><?php echo esc_html($d); ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if (rw_f('cta_title')): ?>
<section class="rw-section rw-section--tight">
  <div class="rw-container">
    <div class="rw-cta">
      <h2 class="rw-cta__title"><?php echo esc_html(rw_f('cta_title')); ?></h2>
      <?php if (rw_f('cta_desc')): ?>
        <p class="rw-cta__text"><?php echo esc_html(rw_f('cta_desc')); ?></p>
      <?php endif; ?>
      <div class="rw-cta__actions">
        <a class="rw-btn rw-btn--light rw-btn--lg" href="<?php echo esc_url(home_url($l === 'zh' ? '/contact/' : ($l === 'ko' ? '/ko/contact/' : '/vi/contact/'))); ?>">
          <?php echo esc_html(rw_f('cta_btn', $l === 'zh' ? '预约咨询' : ($l === 'ko' ? '상담 신청' : 'Đặt lịch tư vấn'))); ?>
        </a>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
