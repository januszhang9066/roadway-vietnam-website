<?php
/**
 * Roadway Vietnam — 首页
 * 结构参考 Overview AI(37 section 精简为 9 段)+ Roadway 视觉规范
 */
get_header();

$l = rw_lang();

/* ---------- Hero ---------- */
$hero_eyebrow = rw_f('hero_eyebrow');
$hero_title   = rw_f('hero_title');
$hero_desc    = rw_f('hero_desc');
$hero_cta1    = rw_f('hero_cta1', $l === 'zh' ? '预约咨询' : ($l === 'ko' ? '상담 신청' : 'Đặt lịch'));
$hero_cta2    = rw_f('hero_cta2', $l === 'zh' ? '看行业方案' : ($l === 'ko' ? '산업 솔루션 보기' : 'Xem giải pháp'));
?>

<section class="rw-hero">
  <div class="rw-hero__bg" aria-hidden="true"></div>
  <div class="rw-container rw-hero__inner">
    <div class="rw-hero__copy">
      <?php if ($hero_eyebrow): ?>
        <span class="rw-hero__eyebrow"><?php echo esc_html($hero_eyebrow); ?></span>
      <?php endif; ?>

      <h1 class="rw-hero__title">
        <?php
        // 标题里用 \n 分段,主关键词高亮成蓝色
        $parts = explode("\n", $hero_title);
        foreach ($parts as $i => $p) {
            if ($i > 0) echo "<br>";
            $hl = strpos($p, '**') !== false;
            if ($hl) {
                $p = str_replace('**', '', $p);
                echo '<span class="rw-hl">' . esc_html($p) . '</span>';
            } else {
                echo esc_html($p);
            }
        }
        ?>
      </h1>

      <?php if ($hero_desc): ?>
        <p class="rw-hero__desc"><?php echo esc_html($hero_desc); ?></p>
      <?php endif; ?>

      <div class="rw-hero__actions">
        <a class="rw-btn rw-btn--primary rw-btn--lg" href="<?php echo esc_url(home_url($l === 'zh' ? '/contact/' : ($l === 'ko' ? '/ko/contact/' : '/vi/contact/'))); ?>">
          <?php echo esc_html($hero_cta1); ?>
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
        <a class="rw-btn rw-btn--ghost rw-btn--lg" href="<?php echo esc_url(home_url($l === 'zh' ? '/cases/' : ($l === 'ko' ? '/ko/cases/' : '/vi/cases/'))); ?>">
          <?php echo esc_html($hero_cta2); ?>
        </a>
      </div>

      <ul class="rw-hero__trust">
        <?php
        $trust = $l === 'zh'
            ? ['数据不出厂', 'MES/ERP 对接', '6 周上线', '三语交付']
            : ($l === 'ko'
                ? ['데이터 공장 내 처리', 'MES/ERP 연동', '6주 내 구축', '3개 언어']
                : ['Dữ liệu không rời nhà máy', 'Kết nối MES/ERP', 'Triển khai 6 tuần', '3 ngôn ngữ']);
        foreach ($trust as $t) {
            echo '<li><span class="rw-tick">✓</span>' . esc_html($t) . '</li>';
        }
        ?>
      </ul>
    </div>

    <!-- 抽象仪表盘插画(纯 CSS,无外部图) -->
    <div class="rw-hero__visual" aria-hidden="true">
      <div class="rw-panel">
        <div class="rw-panel__bar">
          <span></span><span></span><span></span>
          <em><?php echo esc_html($l === 'zh' ? '产线实时看板' : ($l === 'ko' ? '라인 실시간 보드' : 'Bảng thời gian thực')); ?></em>
        </div>
        <div class="rw-panel__body">
          <div class="rw-kpi">
            <div class="rw-kpi__box">
              <b><?php echo esc_html($l === 'zh' ? 'OEE' : 'OEE'); ?></b>
              <strong>87.4%</strong>
              <i class="rw-up">▲ 4.2%</i>
            </div>
            <div class="rw-kpi__box">
              <b><?php echo esc_html($l === 'zh' ? '不良率' : ($l === 'ko' ? '불량률' : 'Tỷ lệ lỗi')); ?></b>
              <strong>0.38%</strong>
              <i class="rw-down">▼ 0.11%</i>
            </div>
          </div>
          <div class="rw-chart">
            <svg viewBox="0 0 300 90" preserveAspectRatio="none" width="100%" height="90">
              <defs><linearGradient id="g1" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#046BD2" stop-opacity=".28"/>
                <stop offset="100%" stop-color="#046BD2" stop-opacity="0"/>
              </linearGradient></defs>
              <path d="M0,72 L30,64 L60,68 L90,50 L120,54 L150,38 L180,42 L210,26 L240,30 L270,16 L300,20 L300,90 L0,90 Z" fill="url(#g1)"/>
              <path d="M0,72 L30,64 L60,68 L90,50 L120,54 L150,38 L180,42 L210,26 L240,30 L270,16 L300,20" fill="none" stroke="#046BD2" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="rw-alert">
            <span class="rw-alert__dot"></span>
            <div>
              <b><?php echo esc_html($l === 'zh' ? 'Line 3 · 治具偏移' : ($l === 'ko' ? 'Line 3 · 지그 편차' : 'Line 3 · Lệch jig')); ?></b>
              <em><?php echo esc_html($l === 'zh' ? '已自动派工单 · 2 分钟前' : ($l === 'ko' ? '작업지 자동 배정 · 2분 전' : 'Đã tự động giao việc · 2 phút trước')); ?></em>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php
/* ---------- 数据带 ---------- */
$stats = rw_list('stats_items');
if ($stats):
?>
<section class="rw-stats">
  <div class="rw-container">
    <div class="rw-stats__grid">
      <?php foreach ($stats as $s):
          $p = array_map('trim', explode('|', $s));
          $num = $p[0] ?? '';
          $lab = $p[1] ?? '';
          $note = $p[2] ?? '';
      ?>
        <div class="rw-stat">
          <div class="rw-stat__num"><?php echo esc_html($num); ?></div>
          <div class="rw-stat__label"><?php echo esc_html($lab); ?></div>
          <?php if ($note): ?>
            <div class="rw-stat__note"><?php echo esc_html($note); ?></div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php
/* ---------- 核心能力 ---------- */
$cap = rw_list('cap_items');
if ($cap):
?>
<section class="rw-section rw-section--gray">
  <div class="rw-container">
    <div class="rw-section__head rw-section__head--center">
      <?php if (rw_f('cap_eyebrow')): ?>
        <span class="rw-eyebrow"><?php echo esc_html(rw_f('cap_eyebrow')); ?></span>
      <?php endif; ?>
      <h2 class="rw-h2"><?php echo esc_html(rw_f('cap_title')); ?></h2>
      <?php if (rw_f('cap_desc')): ?>
        <p class="rw-lead" style="margin-top:16px"><?php echo esc_html(rw_f('cap_desc')); ?></p>
      <?php endif; ?>
    </div>

    <div class="rw-grid rw-grid--3">
      <?php foreach ($cap as $i => $c):
          $p = array_map('trim', explode('|', $c));
          $icon = $p[0] ?? '';
          $title = $p[1] ?? '';
          $desc = $p[2] ?? '';
          $link = $p[3] ?? '';
      ?>
        <article class="rw-card">
          <?php if ($icon): ?>
            <div class="rw-card__icon rw-card__icon--<?php echo esc_attr(abs($i) % 4); ?>"><?php echo esc_html($icon); ?></div>
          <?php endif; ?>
          <h3 class="rw-card__title"><?php echo esc_html($title); ?></h3>
          <p class="rw-card__text"><?php echo esc_html($desc); ?></p>
          <?php if ($link): ?>
            <a class="rw-card__link" href="<?php echo esc_url($link); ?>">
              <?php echo esc_html($l === 'zh' ? '了解更多 →' : ($l === 'ko' ? '자세히 보기 →' : 'Tìm hiểu thêm →')); ?>
            </a>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php
/* ---------- 行业矩阵 ---------- */
$ind = rw_list('ind_items');
if ($ind):
?>
<section class="rw-section rw-section--dark">
  <div class="rw-container">
    <div class="rw-section__head">
      <?php if (rw_f('ind_eyebrow')): ?>
        <span class="rw-eyebrow"><?php echo esc_html(rw_f('ind_eyebrow')); ?></span>
      <?php endif; ?>
      <h2 class="rw-h2"><?php echo esc_html(rw_f('ind_title')); ?></h2>
      <?php if (rw_f('ind_desc')): ?>
        <p class="rw-lead" style="margin-top:16px"><?php echo esc_html(rw_f('ind_desc')); ?></p>
      <?php endif; ?>
    </div>

    <div class="rw-industries">
      <?php foreach ($ind as $i => $row):
          $p = array_map('trim', explode('|', $row));
          $name = $p[0] ?? '';
          $scene = $p[1] ?? '';
          $tags = $p[2] ?? '';
          $tagArr = array_filter(array_map('trim', explode(',', $tags)));
      ?>
        <article class="rw-ind">
          <div class="rw-ind__num"><?php echo esc_html(sprintf('%02d', $i + 1)); ?></div>
          <h3 class="rw-ind__name"><?php echo esc_html($name); ?></h3>
          <p class="rw-ind__scene"><?php echo esc_html($scene); ?></p>
          <div class="rw-ind__tags">
            <?php foreach ($tagArr as $t): ?>
              <span class="rw-badge rw-badge--ind"><?php echo esc_html($t); ?></span>
            <?php endforeach; ?>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php
/* ---------- 为什么选我们 ---------- */
$why = rw_list('why_items');
if ($why):
?>
<section class="rw-section">
  <div class="rw-container">
    <div class="rw-why">
      <div class="rw-why__left">
        <?php if (rw_f('why_eyebrow')): ?>
          <span class="rw-eyebrow"><?php echo esc_html(rw_f('why_eyebrow')); ?></span>
        <?php endif; ?>
        <h2 class="rw-h2"><?php echo esc_html(rw_f('why_title')); ?></h2>
        <?php if (rw_f('why_desc')): ?>
          <p class="rw-lead" style="margin-top:16px"><?php echo esc_html(rw_f('why_desc')); ?></p>
        <?php endif; ?>
      </div>
      <div class="rw-why__right">
        <?php foreach ($why as $w):
            $p = array_map('trim', explode('|', $w));
            $t = $p[0] ?? '';
            $d = $p[1] ?? '';
        ?>
          <div class="rw-feature">
            <span class="rw-feature__check">✓</span>
            <div>
              <div class="rw-feature__title"><?php echo esc_html($t); ?></div>
              <div class="rw-feature__text"><?php echo esc_html($d); ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<?php
/* ---------- 实施流程 ---------- */
$proc = rw_list('proc_items');
if ($proc):
?>
<section class="rw-section rw-section--gray">
  <div class="rw-container">
    <div class="rw-section__head rw-section__head--center">
      <?php if (rw_f('proc_eyebrow')): ?>
        <span class="rw-eyebrow"><?php echo esc_html(rw_f('proc_eyebrow')); ?></span>
      <?php endif; ?>
      <h2 class="rw-h2"><?php echo esc_html(rw_f('proc_title')); ?></h2>
      <?php if (rw_f('proc_desc')): ?>
        <p class="rw-lead" style="margin-top:16px"><?php echo esc_html(rw_f('proc_desc')); ?></p>
      <?php endif; ?>
    </div>

    <div class="rw-grid rw-grid--4 rw-process">
      <?php foreach ($proc as $i => $p0):
          $p = array_map('trim', explode('|', $p0));
          $stage = $p[0] ?? '';
          $title = $p[1] ?? '';
          $desc = $p[2] ?? '';
      ?>
        <div class="rw-step">
          <span class="rw-step__num"><?php echo esc_html((string)($i + 1)); ?></span>
          <?php if ($stage): ?>
            <span class="rw-step__stage"><?php echo esc_html($stage); ?></span>
          <?php endif; ?>
          <div class="rw-step__title"><?php echo esc_html($title); ?></div>
          <div class="rw-step__text"><?php echo esc_html($desc); ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php
/* ---------- CTA ---------- */
if (rw_f('cta_title')):
?>
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
        <a class="rw-btn rw-btn--outline-light rw-btn--lg" href="mailto:zhang@roadwayvn.com">
          zhang@roadwayvn.com
        </a>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<?php
get_footer();
