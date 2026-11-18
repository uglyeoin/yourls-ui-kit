/* ============================================================
   YOURLS UIkit Skin — runtime tweaks
   ============================================================ */

(function () {
    'use strict';

    function ready(fn) {
        if (document.readyState !== 'loading') fn();
        else document.addEventListener('DOMContentLoaded', fn);
    }

    ready(function () {

        // 1. Tag the main table cells with semantic classes if YOURLS didn't.
        //    YOURLS already uses .keyword, .url, .clicks, .timestamp, .actions
        //    in modern versions — but on some older installs the columns are
        //    just <td> with no class. We add them here for styling.
        var table = document.getElementById('main_table');
        if (table) {
            var headers = table.querySelectorAll('thead th');
            var colClasses = [];
            headers.forEach(function (th) {
                var t = th.textContent.trim().toLowerCase();
                if (t.indexOf('short') !== -1) colClasses.push('keyword');
                else if (t.indexOf('url') !== -1 || t.indexOf('original') !== -1) colClasses.push('url');
                else if (t.indexOf('title') !== -1) colClasses.push('title');
                else if (t.indexOf('click') !== -1) colClasses.push('clicks');
                else if (t.indexOf('date') !== -1 || t.indexOf('time') !== -1) colClasses.push('timestamp');
                else if (t.indexOf('action') !== -1) colClasses.push('actions');
                else colClasses.push('');
            });
            table.querySelectorAll('tbody tr').forEach(function (tr) {
                tr.querySelectorAll('td').forEach(function (td, i) {
                    if (colClasses[i] && !td.classList.contains(colClasses[i])) {
                        td.classList.add(colClasses[i]);
                    }
                });
            });
        }

        // 2. Add quick "copy short URL" buttons next to each keyword link.
        document.querySelectorAll('#main_table td.keyword a').forEach(function (a) {
            if (a.dataset.sbCopyDone) return;
            a.dataset.sbCopyDone = '1';
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'sb-copy-btn';
            btn.setAttribute('uk-icon', 'icon: copy; ratio: 0.8');
            btn.setAttribute('uk-tooltip', 'Copy short URL');
            btn.style.cssText = 'background:transparent;border:0;cursor:pointer;color:#9ca3af;margin-left:6px;padding:2px 4px;vertical-align:middle';
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var url = a.href;
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(url).then(function () {
                        btn.style.color = '#32d296';
                        setTimeout(function () { btn.style.color = '#9ca3af'; }, 1500);
                    });
                } else {
                    // Fallback
                    var ta = document.createElement('textarea');
                    ta.value = url;
                    document.body.appendChild(ta);
                    ta.select();
                    try { document.execCommand('copy'); } catch (e) { }
                    document.body.removeChild(ta);
                }
            });
            a.parentNode.insertBefore(btn, a.nextSibling);
        });

        // 3. Add a body class on the login page so the CSS can re-style it.
        if (document.body && document.querySelector('#login form')) {
            document.body.classList.add('login');
        }

        // 4. Polish the "share" buttons row if present.
        document.querySelectorAll('#shareboxes .share-button, .button-share').forEach(function (el) {
            el.classList.add('uk-button', 'uk-button-default', 'uk-button-small');
        });

        // 5. Wrap the page h2 + optional description paragraph in uk-container.
        var mainEl = document.querySelector('#wrap > main, body > main');
        if (mainEl) {
            var children = mainEl.children;
            var pageH2 = null;
            for (var i = 0; i < children.length; i++) {
                if (children[i].tagName === 'H2') { pageH2 = children[i]; break; }
            }
            if (pageH2) {
                var wrap = document.createElement('div');
                wrap.className = 'uk-container sb-page-header';
                pageH2.parentNode.insertBefore(wrap, pageH2);
                wrap.appendChild(pageH2);
                var next = wrap.nextElementSibling;
                if (next && next.tagName === 'P') { wrap.appendChild(next); }
            }
        }
    });
})();

(function () {
    'use strict';
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('sb-theme-form');
        if (!form) return;
        function preview(input) {
            if (!input.checkValidity()) return;
            var key = input.dataset.themeKey;
            var unit = input.type === 'number' ? (key === 'login-angle' ? 'deg' : 'px') : '';
            document.documentElement.style.setProperty('--sb-' + key, input.value + unit);
        }
        form.querySelectorAll('[data-theme-key]').forEach(function (input) {
            preview(input);
            input.addEventListener('input', function () {
                var hex = form.querySelector('[data-color-for="' + input.id + '"]');
                if (hex) hex.value = input.value;
                preview(input);
            });
        });
        form.querySelectorAll('[data-color-for]').forEach(function (hex) {
            hex.addEventListener('input', function () {
                if (!hex.checkValidity()) return;
                var input = document.getElementById(hex.dataset.colorFor);
                input.value = hex.value;
                preview(input);
            });
        });
    });
})();
