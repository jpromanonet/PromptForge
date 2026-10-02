(function () {
  'use strict';

  function $(sel, root) {
    return (root || document).querySelector(sel);
  }

  function $$(sel, root) {
    return Array.prototype.slice.call((root || document).querySelectorAll(sel));
  }

  function initSidebar() {
    var shell = $('.app-shell');
    var toggle = $('#sidebar-toggle');
    var backdrop = $('.sidebar-backdrop');
    if (!shell || !toggle) return;

    function open() {
      shell.classList.add('sidebar-open');
      toggle.setAttribute('aria-expanded', 'true');
    }
    function close() {
      shell.classList.remove('sidebar-open');
      toggle.setAttribute('aria-expanded', 'false');
    }
    toggle.addEventListener('click', function () {
      shell.classList.contains('sidebar-open') ? close() : open();
    });
    if (backdrop) backdrop.addEventListener('click', close);
  }

  function resolveTheme(pref) {
    if (pref === 'dark' || pref === 'light') return pref;
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
      return 'dark';
    }
    return 'light';
  }

  function applyTheme(pref) {
    var root = document.documentElement;
    var theme = resolveTheme(pref);
    root.setAttribute('data-theme', theme);
    root.setAttribute('data-theme-pref', pref);
    try {
      localStorage.setItem('pf-theme', pref);
    } catch (e) {}
    return theme;
  }

  function initThemeToggle() {
    var root = document.documentElement;
    var pref = root.getAttribute('data-theme-pref') || 'system';
    try {
      var stored = localStorage.getItem('pf-theme');
      if (stored) pref = stored;
    } catch (e) {}
    applyTheme(pref);

    if (window.matchMedia) {
      var mq = window.matchMedia('(prefers-color-scheme: dark)');
      var onChange = function () {
        var currentPref = root.getAttribute('data-theme-pref') || 'system';
        if (currentPref === 'system') applyTheme('system');
      };
      if (mq.addEventListener) mq.addEventListener('change', onChange);
      else if (mq.addListener) mq.addListener(onChange);
    }

    $$('[data-theme-toggle]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var current = root.getAttribute('data-theme') || 'light';
        var next = current === 'dark' ? 'light' : 'dark';
        applyTheme(next);

        var url = btn.getAttribute('data-theme-url');
        var csrf = btn.getAttribute('data-csrf') || '';
        if (!url) return;

        var body = new URLSearchParams();
        body.set('_csrf', csrf);
        body.set('theme', next);
        fetch(url, {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: body.toString(),
          credentials: 'same-origin'
        }).catch(function () {});
      });
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    initSidebar();
    initThemeToggle();
  });
})();
