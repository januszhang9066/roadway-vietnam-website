/* Roadway 移动端导航:汉堡按钮开合 */
(function () {
    'use strict';
    var burger = document.getElementById('rw-burger');
    var nav = document.getElementById('rw-nav');
    if (!burger || !nav) return;

    function close() {
        nav.classList.remove('is-open');
        burger.classList.remove('is-open');
        burger.setAttribute('aria-expanded', 'false');
    }

    burger.addEventListener('click', function () {
        var open = nav.classList.toggle('is-open');
        burger.classList.toggle('is-open', open);
        burger.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    // 点击菜单项后自动收起(移动端体验)
    nav.addEventListener('click', function (e) {
        if (e.target.closest('a')) close();
    });

    // Esc 键关闭
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') close();
    });

    // 窗口放大回桌面尺寸时重置状态
    window.addEventListener('resize', function () {
        if (window.innerWidth > 860) close();
    });
})();
