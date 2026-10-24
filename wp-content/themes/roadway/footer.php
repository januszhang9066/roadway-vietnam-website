<?php
/**
 * Roadway Vietnam — 页脚
 */
$l = rw_lang();
?>
</main>

<footer class="rw-footer">
  <div class="rw-container">
    <div class="rw-footer__grid">
      <div>
        <div class="rw-footer__brand">Roadway Vietnam</div>
        <p style="font-size:14.5px;line-height:1.75;max-width:34ch;color:var(--rw-gray-400)">
          <?php echo esc_html($l === 'zh'
            ? '为越南北部韩资/中资制造业工厂提供 AI-Agent 系统集成。数据不出厂,三语交付,6 周上线。'
            : ($l === 'ko'
              ? '베트남 북부 한중 기업 Manufacturing 공장을 위한 AI-Agent 시스템 통합. 데이터는 공장 밖으로 나가지 않습니다.'
              : 'Tích hợp hệ thống AI-Agent cho nhà máy sản xuất Hàn Trung tại miền Bắc Việt Nam. Dữ liệu không rời khỏi nhà máy.')); ?>
        </p>
        <div style="margin-top:24px;display:flex;gap:8px;flex-wrap:wrap">
          <span class="rw-badge rw-badge--gray" style="background:rgba(255,255,255,.08);color:var(--rw-gray-300)">MES / ERP 集成</span>
          <span class="rw-badge rw-badge--gray" style="background:rgba(255,255,255,.08);color:var(--rw-gray-300)">Edge AI</span>
        </div>
      </div>

      <div>
        <div class="rw-footer__title"><?php echo esc_html($l === 'zh' ? '服务' : ($l === 'ko' ? '서비스' : 'Dịch vụ')); ?></div>
        <ul class="rw-footer__links">
          <li><a href="<?php echo esc_url(home_url($l === 'zh' ? '/services/' : ($l === 'ko' ? '/ko/services/' : '/vi/services/'))); ?>"><?php echo esc_html($l === 'zh' ? '工厂群聊 AI-Agent' : ($l === 'ko' ? '공장 그룹챗 AI-Agent' : 'AI-Agent nhóm chat')); ?></a></li>
          <li><a href="<?php echo esc_url(home_url($l === 'zh' ? '/services/' : ($l === 'ko' ? '/ko/services/' : '/vi/services/'))); ?>"><?php echo esc_html($l === 'zh' ? '生产数据可视化' : ($l === 'ko' ? '생산 데이터 시각화' : 'Trực quan hóa dữ liệu')); ?></a></li>
          <li><a href="<?php echo esc_url(home_url($l === 'zh' ? '/cases/' : ($l === 'ko' ? '/ko/cases/' : '/vi/cases/'))); ?>"><?php echo esc_html($l === 'zh' ? 'AI 视觉质检' : ($l === 'ko' ? 'AI 비전 검사' : 'Kiểm tra thị giác AI')); ?></a></li>
          <li><a href="<?php echo esc_url(home_url($l === 'zh' ? '/cases/' : ($l === 'ko' ? '/ko/cases/' : '/vi/cases/'))); ?>"><?php echo esc_html($l === 'zh' ? '3D 打印与 CNC' : ($l === 'ko' ? '3D 프린팅 & CNC' : 'In 3D & CNC')); ?></a></li>
        </ul>
      </div>

      <div>
        <div class="rw-footer__title"><?php echo esc_html($l === 'zh' ? '行业' : ($l === 'ko' ? '산업' : 'Ngành')); ?></div>
        <ul class="rw-footer__links">
          <li><a href="<?php echo esc_url(home_url($l === 'zh' ? '/cases/' : ($l === 'ko' ? '/ko/cases/' : '/vi/cases/'))); ?>"><?php echo esc_html($l === 'zh' ? '连接器 / 线束' : ($l === 'ko' ? '커넥터 / 하네스' : 'Connector / Harness')); ?></a></li>
          <li><a href="<?php echo esc_url(home_url($l === 'zh' ? '/cases/' : ($l === 'ko' ? '/ko/cases/' : '/vi/cases/'))); ?>"><?php echo esc_html($l === 'zh' ? '半导体 / 电子' : ($l === 'ko' ? '반도체 / 전자' : 'Bán dẫy / Điện tử')); ?></a></li>
          <li><a href="<?php echo esc_url(home_url($l === 'zh' ? '/cases/' : ($l === 'ko' ? '/ko/cases/' : '/vi/cases/'))); ?>"><?php echo esc_html($l === 'zh' ? '汽车 / 电动两轮' : ($l === 'ko' ? '자동차 / 2륜 전기' : 'Ô tô / Xe điện')); ?></a></li>
          <li><a href="<?php echo esc_url(home_url($l === 'zh' ? '/cases/' : ($l === 'ko' ? '/ko/cases/' : '/vi/cases/'))); ?>"><?php echo esc_html($l === 'zh' ? '家电 / 消费电子' : ($l === 'ko' ? '가전 / Consumer' : 'Gia dụng / Điện tử')); ?></a></li>
        </ul>
      </div>

      <div>
        <div class="rw-footer__title"><?php echo esc_html($l === 'zh' ? '联系' : ($l === 'ko' ? '문의' : 'Liên hệ')); ?></div>
        <ul class="rw-footer__links">
          <li><a href="mailto:zhang@roadwayvn.com">zhang@roadwayvn.com</a></li>
          <li><a href="https://roadwayvn.com" target="_blank" rel="noopener">roadwayvn.com</a></li>
          <li style="margin-top:16px;color:var(--rw-gray-500);font-size:13.5px;line-height:1.7">
            <?php echo esc_html($l === 'zh'
              ? '越南北部 · 河内 / 海防 / 太原<br>周一至周六 9:00–18:00 (GMT+7)'
              : ($l === 'ko'
                ? '베트남 북부 · 하노이 / 하이퐑 / 타이응<br>월–토 9:00–18:00 (GMT+7)'
                : 'Miền Bắc Việt Nam · Hà Nội / Hải Phòng / Thái Nguyên<br>T2–T7 9:00–18:00 (GMT+7)')); ?>
          </li>
        </ul>
      </div>
    </div>

    <div class="rw-footer__bottom">
      <span>© <?php echo esc_html(date('Y')); ?> Roadway Vietnam · Zhang Haoxiong (장호웅)</span>
      <span><?php echo esc_html($l === 'zh' ? '越南北部韩资/中资制造业工厂 AI 解决方案专家' : ($l === 'ko' ? '베트남 북부 한중 제조 공장 AI 솔루션 전문' : 'Chuyên gia giải pháp AI cho nhà máy Hàn Trung miền Bắc Việt Nam')); ?></span>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
