<?php
/**
 * Roadway Vietnam — 行业方案 / 案例页
 */
get_header();
$l = rw_lang();
$cases = rw_list('case_items');
?>

<section class="rw-pagehead">
  <div class="rw-container">
    <?php echo rw_breadcrumb(); ?>
    <h1><?php echo esc_html(get_the_title()); ?></h1>
    <?php if (rw_f('case_intro')): ?>
      <p><?php echo esc_html(rw_f('case_intro')); ?></p>
    <?php endif; ?>
  </div>
</section>

<?php if ($cases): ?>
<section class="rw-section">
  <div class="rw-container">
    <?php foreach ($cases as $i => $row):
        $p = array_map('trim', explode('|', $row));
        $industry = $p[0] ?? '';
        $title   = $p[1] ?? '';
        $problem = $p[2] ?? '';
        $solution= $p[3] ?? '';
        $result  = $p[4] ?? '';
        $metrics = isset($p[5]) ? array_filter(array_map('trim', explode(',', $p[5]))) : [];
    ?>
      <article class="rw-case2 <?php echo ($i % 2) ? 'rw-case2--flip' : ''; ?>">
        <div class="rw-case2__body">
          <span class="rw-badge rw-badge--blue"><?php echo esc_html($industry); ?></span>
          <h2 class="rw-case2__title"><?php echo esc_html($title); ?></h2>

          <div class="rw-case2__block">
            <h3><?php echo esc_html($l === 'zh' ? '遇到的问题' : ($l === 'ko' ? '겪은 문제' : 'Vấn đề')); ?></h3>
            <p><?php echo esc_html($problem); ?></p>
          </div>

          <div class="rw-case2__block">
            <h3><?php echo esc_html($l === 'zh' ? 'Roadway 怎么做' : ($l === 'ko' ? 'Roadway 해결 방식' : 'Cách Roadway xử lý')); ?></h3>
            <p><?php echo esc_html($solution); ?></p>
          </div>

          <?php if ($metrics): ?>
            <div class="rw-case2__metrics">
              <?php foreach ($metrics as $m):
                  $mp = array_map('trim', explode(':', $m, 2));
                  if (count($mp) < 2) continue;
              ?>
                <div class="rw-case2__metric">
                  <strong><?php echo esc_html($mp[0]); ?></strong>
                  <span><?php echo esc_html($mp[1]); ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

        <div class="rw-case2__visual" aria-hidden="true">
          <div class="rw-mini">
            <div class="rw-mini__bar"><span></span><span></span><span></span></div>
            <div class="rw-mini__body">
              <div class="rw-mini__row">
                <em><?php echo esc_html($l === 'zh' ? '检测结果' : ($l === 'ko' ? '검사 결과' : 'Kết quả')); ?></em>
                <b><?php echo esc_html($result); ?></b>
              </div>
              <div class="rw-mini__bars">
                <span style="width:88%"></span>
                <span style="width:64%"></span>
                <span style="width:76%"></span>
                <span style="width:52%"></span>
                <span style="width:93%"></span>
              </div>
            </div>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
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
          <?php echo esc_html(rw_f('cta_btn', $l === 'zh' ? '聊聊你的行业' : ($l === 'ko' ? '상담 신청' : 'Đặt lịch tư vấn'))); ?>
        </a>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
