<?php
/**
 * Roadway Vietnam — 联系页
 */
get_header();
$l = rw_lang();
$items = rw_list('contact_items');

// 提交结果(admin-post 处理后会带 inquiry 参数跳回本页)
$inquiry_success = isset($_GET['inquiry']) && $_GET['inquiry'] === 'success';
$inquiry_error   = isset($_GET['inquiry']) && $_GET['inquiry'] === 'error' ? ($_GET['reason'] ?? '') : '';
?>

<section class="rw-pagehead">
  <div class="rw-container">
    <?php echo rw_breadcrumb(); ?>
    <h1><?php echo esc_html(get_the_title()); ?></h1>
    <?php if (rw_f('contact_intro')): ?>
      <p><?php echo esc_html(rw_f('contact_intro')); ?></p>
    <?php endif; ?>
  </div>
</section>

<section class="rw-section">
  <div class="rw-container">
    <div class="rw-contact">
      <!-- 左:联系信息 -->
      <div class="rw-contact__info">
        <?php if ($items): ?>
          <?php foreach ($items as $row):
              $p = array_map('trim', explode('|', $row));
              $type = $p[0] ?? '';
              $val  = $p[1] ?? '';
              if (!$val) continue;
              $isMail = strpos($val, '@') !== false;
              $isTel  = preg_match('/^[+\d][\d\s\-()]+$/', $val);
          ?>
            <div class="rw-contact__item">
              <div class="rw-contact__icon">
                <?php echo $isMail ? '✉' : ($isTel ? '☎' : '◈'); ?>
              </div>
              <div>
                <div class="rw-contact__type"><?php echo esc_html($type); ?></div>
                <?php if ($isMail): ?>
                  <a class="rw-contact__val" href="mailto:<?php echo esc_attr($val); ?>"><?php echo esc_html($val); ?></a>
                <?php elseif ($isTel): ?>
                  <a class="rw-contact__val" href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $val)); ?>"><?php echo esc_html($val); ?></a>
                <?php else: ?>
                  <div class="rw-contact__val"><?php echo esc_html($val); ?></div>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>

        <div class="rw-note rw-note--ok" style="margin-top:32px">
          <?php echo esc_html($l === 'zh'
            ? '我们在越南北部有本地团队,到厂区实地调研不需要额外差旅费用。首次沟通免费,不签约也能先看方案。'
            : ($l === 'ko'
              ? '베트남 북부에 자체 팀이 있어 공장 실태 조사에 추가 출장비가 없습니다. 첫 상담은 무료이며, 계약 없이도 먼저方案的을 볼 수 있습니다.'
              : 'Chúng tôi có đội ngũ tại miền Bắc Việt Nam nên khảo sát nhà máy không mất phí đi lại. Tư vấn đầu tiên miễn phí, xem phương án trước khi ký.')); ?>
        </div>
      </div>

      <!-- 右:表单 -->
      <div class="rw-contact__form">
        <h2 class="rw-contact__formtitle">
          <?php echo esc_html($l === 'zh' ? '告诉我们你的情况' : ($l === 'ko' ? '상황을 알려주세요' : 'Cho chúng tôi biết')); ?>
        </h2>
        <p class="rw-contact__formdesc">
          <?php echo esc_html($l === 'zh'
            ? '填写下面的表单,我们会在 1 个工作日内回复。带 * 的是必填项。'
            : ($l === 'ko'
              ? '아래 양식을 작성해 주시면 1영업일 이내에 답변드리겠습니다. * 표시는 필수 항목입니다.'
              : 'Điền biểu mẫu bên dưới, chúng tôi sẽ phản hồi trong 1 ngày làm việc. Dấu * là bắt buộc.')); ?>
        </p>

        <form class="rw-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>"<?php if ($inquiry_success) echo ' hidden'; ?>>
          <input type="hidden" name="action" value="rw_contact">
          <?php wp_nonce_field('rw_contact_submit', 'rw_form_nonce'); ?>
          <input type="hidden" name="redirect_to" value="<?php echo esc_url(get_permalink()); ?>">
          <input type="text" name="company_website" value="" autocomplete="off" tabindex="-1" aria-hidden="true" style="position:absolute;left:-9999px;top:-9999px;width:1px;height:1px;opacity:0">

          <?php if ($inquiry_error === 'form'): ?>
            <p class="rw-alert" role="alert" style="background:#FEF3E2;color:#A55A05;padding:12px 16px;border-radius:8px;margin:0 0 16px"><?php echo esc_html($l === 'zh' ? '表单已过期,请重新提交。' : ($l === 'ko' ? '양식이 만료되었습니다. 다시 제출해 주세요.' : 'Biểu mẫu đã hết hạn, vui lòng gửi lại.')); ?></p>
          <?php elseif ($inquiry_error === 'required'): ?>
            <p class="rw-alert" role="alert" style="background:#FEF3E2;color:#A55A05;padding:12px 16px;border-radius:8px;margin:0 0 16px"><?php echo esc_html($l === 'zh' ? '请填写所有带 * 的必填项,并确认邮箱格式正确。' : ($l === 'ko' ? '* 표시 필수 항목을 모두 작성하고 이메일 형식을 확인해 주세요.' : 'Vui lòng điền đủ các mục có dấu * và kiểm tra định dạng email.')); ?></p>
          <?php endif; ?>

          <div class="rw-field">
            <label class="rw-label" for="rw-name">
              <?php echo esc_html($l === 'zh' ? '姓名 *' : ($l === 'ko' ? '이름 *' : 'Họ tên *')); ?>
            </label>
            <input class="rw-input" type="text" id="rw-name" name="name" required>
          </div>

          <div class="rw-field">
            <label class="rw-label" for="rw-company">
              <?php echo esc_html($l === 'zh' ? '公司名称 *' : ($l === 'ko' ? '회사명 *' : 'Tên công ty *')); ?>
            </label>
            <input class="rw-input" type="text" id="rw-company" name="company" required>
          </div>

          <div class="rw-field">
            <label class="rw-label" for="rw-email">
              <?php echo esc_html($l === 'zh' ? '工作邮箱 *' : ($l === 'ko' ? '회사 이메일 *' : 'Email công việc *')); ?>
            </label>
            <input class="rw-input" type="email" id="rw-email" name="email" required>
          </div>

          <div class="rw-field">
            <label class="rw-label" for="rw-phone">
              <?php echo esc_html($l === 'zh' ? '电话 / WhatsApp' : ($l === 'ko' ? '전화 / WhatsApp' : 'Điện thoại / WhatsApp')); ?>
            </label>
            <input class="rw-input" type="tel" id="rw-phone" name="phone">
          </div>

          <div class="rw-field">
            <label class="rw-label" for="rw-scale">
              <?php echo esc_html($l === 'zh' ? '工厂规模' : ($l === 'ko' ? '공장 규모' : 'Quy mô nhà máy')); ?>
            </label>
            <select class="rw-select" id="rw-scale" name="scale">
              <option value="">&mdash;</option>
              <option value="1-100">1–100 <?php echo esc_html($l === 'zh' ? '人' : ($l === 'ko' ? '명' : 'người')); ?></option>
              <option value="101-200">101–200 <?php echo esc_html($l === 'zh' ? '人' : ($l === 'ko' ? '명' : 'người')); ?></option>
              <option value="201-500">201–500 <?php echo esc_html($l === 'zh' ? '人' : ($l === 'ko' ? '명' : 'người')); ?></option>
              <option value="500+">500+ <?php echo esc_html($l === 'zh' ? '人' : ($l === 'ko' ? '명' : 'người')); ?></option>
            </select>
          </div>

          <div class="rw-field">
            <label class="rw-label" for="rw-need">
              <?php echo esc_html($l === 'zh' ? '想解决的问题 *' : ($l === 'ko' ? '해결하고 싶은 문제 *' : 'Vấn đề cần giải quyết *')); ?>
            </label>
            <textarea class="rw-textarea" id="rw-need" name="message" required
              placeholder="<?php echo esc_attr($l === 'zh'
                ? '例:越南线束车间每天靠微信群报产量,想用一句话自动生成报工记录'
                : ($l === 'ko'
                  ? '예: 베트남 하네스 공장의 생산량 보고를 그룹챗에 수작업으로 하고 있어, 한마디로 자동 보고서를 만들고 싶어'
                  : 'Ví dụ: Xưởng harness Việt Nam báo cáo sản lượng thủ công qua group chat, muốn tự động tạo báo cáo bằng một câu')); ?>"></textarea>
          </div>

          <button type="submit" class="rw-btn rw-btn--primary rw-btn--lg rw-btn--block">
            <?php echo esc_html($l === 'zh' ? '提交咨询' : ($l === 'ko' ? '상담 신청하기' : 'Gửi yêu cầu')); ?>
          </button>

          <p class="rw-help" style="text-align:center;margin-top:14px">
            <?php echo esc_html($l === 'zh'
              ? '提交即表示同意我们就本次咨询与您联系。'
              : ($l === 'ko'
                ? '제출하시면 상담 관련 연락을 위해 연락드린다는 점에 동의하는 것으로 간주됩니다.'
                : 'Gửi biểu mẫu đồng nghĩa với việc đồng ý để chúng tôi liên hệ về yêu cầu tư vấn này.')); ?>
          </p>
        </form>

        <?php if ($inquiry_success): ?>
          <div class="rw-alert" role="status" style="background:#E7F6EE;color:#0A7A44;padding:20px 24px;border-radius:10px;font-weight:600">
            <?php echo esc_html($l === 'zh'
              ? '已收到!我们会在 1 个工作日内回复您。'
              : ($l === 'ko'
                ? '접수되었습니다! 1영업일 이내에 답변드리겠습니다.'
                : 'Đã nhận được yêu cầu! Chúng tôi sẽ phản hồi trong 1 ngày làm việc.')); ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php
get_footer();
