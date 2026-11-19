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
        if (mainEl && !document.body.classList.contains('tools')) {
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

(function () {
    'use strict';
    document.addEventListener('DOMContentLoaded', function () {
        if (!document.body.classList.contains('infos')) return;
        var timer;
        function redraw() {
            if (!window.google || !google.visualization) return;
            document.querySelectorAll('script[id^="yourls_graph"]').forEach(function (script) {
                var chart = document.getElementById('visualization_' + script.id.replace('yourls_graph', ''));
                if (chart && chart.getClientRects().length && chart.clientWidth > 0 && typeof window[script.id] === 'function') {
                    window[script.id]();
                }
            });
        }
        var tabs = document.getElementById('tabs');
        if (tabs) tabs.addEventListener('click', function (event) {
            if (event.target.closest('ul.toggle_display a')) requestAnimationFrame(redraw);
        });
        window.addEventListener('resize', function () {
            clearTimeout(timer);
            timer = setTimeout(redraw, 150);
        });
    });
})();


(function () {
    'use strict';
    document.addEventListener('DOMContentLoaded', function () {
        if (!document.body.classList.contains('tools')) return;
        var main = document.querySelector('#wrap > main');
        if (!main) return;
        // Group existing content without rebuilding bookmarklet URLs or API examples.
        var section;
        Array.from(main.childNodes).forEach(function (node) {
            if (node.nodeType === 1 && node.tagName === 'H2') {
                section = document.createElement('section');
                section.className = 'sb-tools-section';
                main.insertBefore(section, node);
            }
            if (section) section.appendChild(node);
        });
        main.querySelectorAll('table.tblSorter').forEach(function (table) {
            var wrapper = document.createElement('div');
            wrapper.className = 'sb-tools-table';
            wrapper.tabIndex = 0;
            wrapper.setAttribute('role', 'region');
            wrapper.setAttribute('aria-label', 'Bookmarklets');
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        });
    });
})();

(function () {
    'use strict';
    document.addEventListener('DOMContentLoaded', function () {
        if (!document.body.classList.contains('plugins')) return;
        var filter = document.getElementById('toggle_plugins');
        if (filter) {
            // Keep YOURLS' existing click handler; replace only its inline appearance.
            filter.removeAttribute('style');
            filter.textContent = 'filter';
            filter.setAttribute('aria-label', 'Toggle active/inactive plugins');
            filter.setAttribute('role', 'button');
            filter.tabIndex = 0;
            filter.setAttribute('aria-pressed', 'false');
            filter.addEventListener('click', function () {
                var row = document.querySelector('#main_table tr.inactive');
                filter.setAttribute('aria-pressed', row && getComputedStyle(row).display === 'none' ? 'true' : 'false');
            });
            filter.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    filter.click();
                }
            });
        }
        var table = document.querySelector('main > table#main_table');
        if (table) {
            var wrapper = document.createElement('div');
            wrapper.className = 'sb-plugins-table';
            wrapper.tabIndex = 0;
            wrapper.setAttribute('role', 'region');
            wrapper.setAttribute('aria-label', 'Plugins');
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        }
    });
})();

(function () {
    'use strict';
    document.addEventListener('DOMContentLoaded', function () {
        var root = document.querySelector('.sb-theme-settings');
        var form = document.getElementById('sb-theme-form');
        if (!root || !form) return;
        var logo = root.querySelector(':scope > .uk-card');
        var fields = Array.from(form.querySelectorAll('.sb-settings-grid > fieldset'));
        var panels = [logo].concat(fields).filter(Boolean);
        var tabs = document.createElement('div');
        tabs.className = 'sb-config-tabs';
        tabs.setAttribute('role', 'tablist');
        tabs.setAttribute('aria-label', 'Template settings');
        root.insertBefore(tabs, logo || form);
        var buttons = [];
        var storageKey = 'sb-uikit-config-tab';
        function activate(index, focus) {
            panels.forEach(function (panel, i) {
                panel.hidden = i !== index;
                buttons[i].setAttribute('aria-selected', i === index ? 'true' : 'false');
                buttons[i].tabIndex = i === index ? 0 : -1;
            });
            form.hidden = fields.indexOf(panels[index]) === -1;
            if (focus) buttons[index].focus();
            try { sessionStorage.setItem(storageKey, String(index)); } catch (e) {}
        }
        panels.forEach(function (panel, index) {
            var heading = panel.querySelector('legend, h3');
            var button = document.createElement('button');
            button.type = 'button';
            button.textContent = heading ? heading.textContent : 'Settings';
            button.id = 'sb-config-tab-' + index;
            panel.id = 'sb-config-panel-' + index;
            panel.setAttribute('role', 'tabpanel');
            panel.setAttribute('aria-labelledby', button.id);
            button.setAttribute('role', 'tab');
            button.setAttribute('aria-controls', panel.id);
            button.addEventListener('click', function () { activate(index, false); });
            button.addEventListener('keydown', function (event) {
                var next;
                if (event.key === 'ArrowRight') next = (index + 1) % panels.length;
                if (event.key === 'ArrowLeft') next = (index + panels.length - 1) % panels.length;
                if (event.key === 'Home') next = 0;
                if (event.key === 'End') next = panels.length - 1;
                if (next !== undefined) { event.preventDefault(); activate(next, true); }
            });
            buttons.push(button);
            tabs.appendChild(button);
        });
        // Reveal invalid fields before native validation tries to focus them.
        form.addEventListener('invalid', function (event) {
            var panel = event.target.closest('fieldset');
            var index = panels.indexOf(panel);
            if (index >= 0) activate(index, false);
        }, true);
        var initial = 1;
        try {
            var saved = sessionStorage.getItem(storageKey);
            if (saved !== null && /^\d+$/.test(saved) && Number(saved) < panels.length) initial = Number(saved);
        } catch (e) {}
        root.classList.add('sb-config-tabbed');
        activate(initial, false);
    });
})();

(function () {
    'use strict';
    document.addEventListener('DOMContentLoaded', function () {
        if (!document.body.classList.contains('index') && !document.body.classList.contains('bookmark')) return;
        var table = document.getElementById('main_table');
        var title = document.querySelector('.sb-links-title');
        if (!table || !title) return;
        var card = document.createElement('section');
        card.className = 'uk-card uk-card-default uk-card-body sb-table-card';
        title.parentNode.insertBefore(card, title);
        card.appendChild(title);
        var scroll = document.createElement('div');
        scroll.className = 'sb-links-table-scroll';
        card.appendChild(scroll);
        scroll.appendChild(table);
    });
})();
