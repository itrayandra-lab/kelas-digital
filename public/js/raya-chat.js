/* RAYA chat widget (adapted from Lunaray's AILUNA): lazy-built panel, safe text rendering, sessionStorage history. */
(function () {
    'use strict';

    var root = document.getElementById('rayaChatRoot');
    var fab = document.getElementById('rayaFab');
    if (!root || !fab) return;

    var ENDPOINT = root.dataset.endpoint;
    var AVATAR = root.dataset.avatar;
    var CONTACT = root.dataset.contact;
    var EMAIL = root.dataset.email || '';
    var WA = root.dataset.wa || '';
    var WA_DISPLAY = root.dataset.waDisplay || '';
    var STORE = 'raya_chat_v1';
    var MAX_CHARS = 800;
    var HANDOFF_INTRO = 'Halo tim RayAcademy, tadi saya bertanya ke RAYA di website dan ingin melanjutkan dengan tim. Ini yang saya tanyakan:';
    var GREETING = 'Hai, saya RAYA, AI Learning Advisor RayAcademy. Ceritakan apa yang ingin Anda pelajari, pengalaman Anda, atau keterampilan yang ingin dikembangkan. Saya bantu carikan kelas yang cocok.';
    var QUICK = [
        'Saya pemula, mulai dari kelas apa?',
        'Ada kelas gratis?',
        'Saya ingin belajar AI untuk kerja',
        'Saya ingin belajar marketing dan branding',
        'Ada event atau workshop terdekat?',
        'Pelatihan untuk tim perusahaan'
    ];

    var panel, body, input, sendBtn, typingEl, handoff;
    var messages = load();
    var sending = false;

    function load() {
        try {
            var raw = sessionStorage.getItem(STORE);
            var list = raw ? JSON.parse(raw) : [];
            return Array.isArray(list) ? list.slice(-30) : [];
        } catch (e) { return []; }
    }

    function save() {
        try { sessionStorage.setItem(STORE, JSON.stringify(messages.slice(-30))); } catch (e) { /* storage unavailable */ }
    }

    function el(tag, cls, text) {
        var n = document.createElement(tag);
        if (cls) n.className = cls;
        if (text != null) n.textContent = text;
        return n;
    }

    function icon(path) {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' + path + '</svg>';
    }

    // ----- Safe rendering: escape everything first, then allow a tiny markdown subset -----
    function esc(s) {
        return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function inline(s) {
        s = esc(s);
        s = s.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
        s = s.replace(/\[([^\]]+)\]\((\/(?![\/\\])[^)\s]*)\)/g, '<a href="$2">$1</a>');
        // Links to anywhere else show as plain text.
        s = s.replace(/\[([^\]]+)\]\((https?:\/\/[^)\s]+)\)/g, '$1');
        return linkContacts(s);
    }

    // Official channels only: the exact email and WhatsApp number from the page's own data become tappable.
    function linkContacts(html) {
        if (EMAIL) {
            html = html.split(EMAIL).join('<a href="mailto:' + EMAIL + '">' + EMAIL + '</a>');
        }
        if (WA && WA_DISPLAY) {
            html = html.split(WA_DISPLAY).join('<a href="https://wa.me/' + WA + '" target="_blank" rel="noopener">' + WA_DISPLAY + '</a>');
        }
        return html;
    }

    function render(text) {
        var out = [];
        var list = null;
        text.split('\n').forEach(function (line) {
            var m = line.match(/^\s*-\s+(.*)$/);
            if (m) {
                if (!list) { list = []; }
                list.push('<li>' + inline(m[1]) + '</li>');
                return;
            }
            if (list) { out.push('<ul>' + list.join('') + '</ul>'); list = null; }
            if (line.trim() !== '') out.push('<p>' + inline(line) + '</p>');
        });
        if (list) out.push('<ul>' + list.join('') + '</ul>');
        return out.join('');
    }

    function addMessage(m, persist) {
        var row = el('div', 'rc-msg ' + (m.role === 'user' ? 'is-user' : 'is-bot') + (m.error ? ' is-error' : ''));
        var bubble = el('div', 'rc-bubble');
        if (m.role === 'user') {
            bubble.textContent = m.content;
        } else {
            bubble.innerHTML = render(m.content);
            if (m.links && m.links.length) {
                var links = el('div', 'rc-links');
                m.links.forEach(function (l) {
                    if (typeof l.url !== 'string') return;
                    // Browsers read "/\host" like "//host", so a backslash after the slash is just as external.
                    var internal = l.url.charAt(0) === '/' && l.url.charAt(1) !== '/' && l.url.charAt(1) !== '\\';
                    if (!internal) return;
                    var a = el('a', null, l.label);
                    a.href = l.url;
                    links.appendChild(a);
                });
                if (links.childNodes.length) bubble.appendChild(links);
            }
        }
        row.appendChild(bubble);
        body.insertBefore(row, typingEl);
        body.scrollTop = body.scrollHeight;
        if (persist) { messages.push(m); save(); }
        updateHandoff();
    }

    // The visitor's last questions travel as the WhatsApp draft, so the team picks up the context. Nothing is sent to our server.
    function updateHandoff() {
        var asked = messages
            .filter(function (m) { return m.role === 'user'; })
            .slice(-3)
            .map(function (m) { return '- ' + (m.content.length > 150 ? m.content.slice(0, 147) + '...' : m.content); });
        handoff.hidden = !WA || !asked.length;
        handoff.href = 'https://wa.me/' + WA + '?text=' + encodeURIComponent(HANDOFF_INTRO + '\n' + asked.join('\n'));
    }

    // ----- Panel -----
    function build() {
        panel = el('section', 'rc-panel');
        panel.id = 'rayaPanel';
        panel.setAttribute('role', 'dialog');
        panel.setAttribute('aria-label', 'Chat dengan RAYA');

        var head = el('div', 'rc-head');
        var img = el('img');
        img.src = AVATAR; img.alt = ''; img.width = 42; img.height = 42;
        var text = el('div', 'rc-head-text');
        text.appendChild(el('h2', 'rc-title', 'RAYA'));
        text.appendChild(el('div', 'rc-sub', 'AI Learning Advisor RayAcademy'));
        var reset = el('button', 'rc-icon-btn');
        reset.type = 'button'; reset.setAttribute('aria-label', 'Mulai percakapan baru'); reset.title = 'Percakapan baru';
        reset.innerHTML = icon('<path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/>');
        reset.addEventListener('click', resetChat);
        var close = el('button', 'rc-icon-btn');
        close.type = 'button'; close.setAttribute('aria-label', 'Tutup chat'); close.title = 'Tutup';
        close.innerHTML = icon('<path d="M18 6 6 18"/><path d="m6 6 12 12"/>');
        close.addEventListener('click', closePanel);
        head.appendChild(img); head.appendChild(text); head.appendChild(reset); head.appendChild(close);

        body = el('div', 'rc-body');
        body.setAttribute('role', 'log');
        body.setAttribute('aria-live', 'polite');
        typingEl = el('div', 'rc-msg rc-typing');
        typingEl.hidden = true;
        var tb = el('div', 'rc-bubble');
        tb.appendChild(el('i')); tb.appendChild(el('i')); tb.appendChild(el('i'));
        typingEl.appendChild(tb);
        body.appendChild(typingEl);

        handoff = el('a', 'rc-handoff');
        handoff.target = '_blank'; handoff.rel = 'noopener'; handoff.hidden = true;
        handoff.innerHTML = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.5 14.4c-.3-.1-1.8-.9-2-1-.3-.1-.5-.1-.7.1-.2.3-.8 1-.9 1.2-.2.2-.3.2-.6.1-.3-.1-1.3-.5-2.4-1.5-.9-.8-1.5-1.8-1.7-2.1-.2-.3 0-.5.1-.6l.4-.5.3-.5c.1-.2 0-.4 0-.5l-.9-2.2c-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.5s1.1 2.9 1.2 3.1c.1.2 2.1 3.2 5.1 4.5 2.5 1 3 .8 3.6.7.6-.1 1.8-.7 2-1.4.2-.7.2-1.3.2-1.4-.1-.2-.3-.3-.6-.4ZM12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Z"/></svg>Lanjutkan di WhatsApp';

        var form = el('form', 'rc-form');
        input = el('textarea', 'rc-input');
        input.rows = 1; input.maxLength = MAX_CHARS; input.placeholder = 'Tulis pertanyaan Anda...';
        input.setAttribute('aria-label', 'Pesan untuk RAYA');
        sendBtn = el('button', 'rc-send');
        sendBtn.type = 'submit'; sendBtn.setAttribute('aria-label', 'Kirim');
        sendBtn.innerHTML = icon('<path d="M5 12h14"/><path d="m13 6 6 6-6 6"/>');
        form.appendChild(input); form.appendChild(sendBtn);

        var note = el('div', 'rc-note');
        note.appendChild(document.createTextNode('RAYA adalah asisten AI dan bisa keliru. Jangan bagikan data pribadi. Detail kelas selalu tercantum di halaman kelasnya. '));
        var contact = el('a', null, 'Lihat semua kelas');
        contact.href = CONTACT;
        note.appendChild(contact);

        panel.appendChild(head); panel.appendChild(body); panel.appendChild(handoff); panel.appendChild(form); panel.appendChild(note);
        root.appendChild(panel);

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            send(input.value);
        });
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) {
                e.preventDefault();
                send(input.value);
            }
        });
        input.addEventListener('input', function () {
            input.style.height = 'auto';
            input.style.height = Math.min(input.scrollHeight, 110) + 'px';
        });
        panel.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closePanel();
        });

        paintAll();
    }

    function paintAll() {
        Array.prototype.slice.call(body.querySelectorAll('.rc-msg:not(.rc-typing), .rc-quick')).forEach(function (n) { n.remove(); });
        addMessage({ role: 'assistant', content: GREETING }, false);
        messages.forEach(function (m) { addMessage(m, false); });
        if (!messages.some(function (m) { return m.role === 'user'; })) showQuick();
    }

    function showQuick() {
        var wrap = el('div', 'rc-quick');
        QUICK.forEach(function (q) {
            var b = el('button', null, q);
            b.type = 'button';
            b.addEventListener('click', function () { send(q); });
            wrap.appendChild(b);
        });
        body.insertBefore(wrap, typingEl);
        body.scrollTop = body.scrollHeight;
    }

    function openPanel(prefill) {
        if (!panel) build();
        panel.classList.add('is-open');
        fab.classList.add('is-hidden');
        fab.setAttribute('aria-expanded', 'true');
        body.scrollTop = body.scrollHeight;
        if (typeof prefill === 'string' && prefill.trim()) {
            send(prefill);
        } else {
            setTimeout(function () { input.focus(); }, 60);
        }
    }

    function closePanel() {
        if (!panel) return;
        panel.classList.remove('is-open');
        fab.classList.remove('is-hidden');
        fab.setAttribute('aria-expanded', 'false');
        fab.focus();
    }

    function resetChat() {
        messages = [];
        save();
        paintAll();
        input.focus();
    }

    // ----- Sending -----
    function setBusy(state) {
        sending = state;
        sendBtn.disabled = state;
        typingEl.hidden = !state;
        if (state) body.scrollTop = body.scrollHeight;
    }

    function send(raw) {
        var text = (raw || '').trim().slice(0, MAX_CHARS);
        if (!text || sending) return;

        var quick = body.querySelector('.rc-quick');
        if (quick) quick.remove();

        var history = messages
            .filter(function (m) { return !m.error; })
            .slice(-8)
            .map(function (m) { return { role: m.role, content: m.content }; });

        addMessage({ role: 'user', content: text }, true);
        input.value = '';
        input.style.height = 'auto';
        setBusy(true);

        var ctrl = typeof AbortController === 'function' ? new AbortController() : null;
        var timer = setTimeout(function () { if (ctrl) ctrl.abort(); }, 40000);

        fetch(ENDPOINT, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': root.dataset.csrf || '' },
            body: JSON.stringify({ message: text, history: history }),
            signal: ctrl ? ctrl.signal : undefined,
            credentials: 'same-origin'
        }).then(function (res) {
            return res.json().catch(function () { return {}; }).then(function (data) { return { res: res, data: data }; });
        }).then(function (r) {
            if (r.res.ok && r.data.reply) {
                addMessage({ role: 'assistant', content: r.data.reply, links: r.data.links || [] }, true);
            } else if (r.res.status === 419) {
                addMessage({ role: 'assistant', error: true, content: 'Sesi halaman sudah kedaluwarsa. Muat ulang halaman lalu kirim lagi pesannya.' }, true);
            } else if (r.res.status === 429) {
                addMessage({ role: 'assistant', error: true, content: 'Terlalu banyak pesan dalam waktu singkat. Mohon tunggu sebentar lalu coba lagi.' }, true);
            } else {
                addMessage({ role: 'assistant', error: true, content: r.data.message || 'Maaf, RAYA sedang tidak bisa menjawab. Silakan coba lagi sebentar lagi.' }, true);
            }
        }).catch(function () {
            addMessage({ role: 'assistant', error: true, content: 'Koneksi terputus atau terlalu lama. Silakan coba lagi.' }, true);
        }).then(function () {
            clearTimeout(timer);
            setBusy(false);
            input.focus();
        });
    }

    // ----- Wiring -----
    fab.addEventListener('click', function () { openPanel(); });
    window.rayaOpen = openPanel;

    // Any "Tanya RAYA" link on the site (homepage section, FAQ) opens the chat.
    document.addEventListener('click', function (e) {
        var a = e.target.closest ? e.target.closest('a[href*="#tanya-raya"]') : null;
        if (!a || e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || e.button === 1) return;
        e.preventDefault();
        openPanel();
    });

    // Let the label peek out once per session so first-time visitors notice the badge.
    try {
        if (!sessionStorage.getItem('raya_teased')) {
            setTimeout(function () {
                fab.classList.add('is-expanded');
                setTimeout(function () { fab.classList.remove('is-expanded'); }, 5000);
                sessionStorage.setItem('raya_teased', '1');
            }, 3500);
        }
    } catch (e) { /* ignore */ }
})();
