{{-- Submits the auth form with fetch: errors show without a reload, typed input stays, and the button can't be
     double-clicked. Without JavaScript the form still posts normally. Expects a form[data-ajax] and #form-errors. --}}
<script>
(function () {
    const form = document.querySelector('form[data-ajax]');
    const box = document.getElementById('form-errors');
    const btn = form.querySelector('button[type="submit"]');
    const label = btn.innerHTML;

    function showErrors(messages) {
        const list = document.createElement('ul');
        list.className = 'list-disc list-inside';
        messages.forEach(m => { const li = document.createElement('li'); li.textContent = m; list.appendChild(li); });
        box.replaceChildren(list);
        box.hidden = false;
        box.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        if (btn.disabled) return;

        btn.disabled = true;
        btn.innerHTML = 'Memproses...';
        form.querySelectorAll('[aria-invalid]').forEach(el => { el.removeAttribute('aria-invalid'); el.style.borderColor = ''; });

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form),
                credentials: 'same-origin',
            });
            const data = await res.json().catch(() => ({}));

            if (res.ok && data.redirect) {
                btn.innerHTML = 'Berhasil, mengalihkan...';
                window.location.href = data.redirect;
                return;
            }

            if (res.status === 422 && data.errors) {
                Object.keys(data.errors).forEach(name => {
                    const input = form.querySelector('[name="' + name + '"]');
                    if (input) { input.setAttribute('aria-invalid', 'true'); input.style.borderColor = '#ef4444'; }
                });
                form.querySelector('[aria-invalid]')?.focus();
                showErrors(Object.values(data.errors).flat());
            } else if (res.status === 429) {
                showErrors(['Terlalu banyak percobaan. Tunggu sebentar lalu coba lagi.']);
            } else if (res.status === 419) {
                showErrors(['Halaman sudah terlalu lama dibuka. Muat ulang halaman lalu coba lagi.']);
            } else {
                showErrors(['Terjadi kesalahan di server. Coba lagi beberapa saat lagi.']);
            }
        } catch (err) {
            showErrors(['Koneksi terputus. Periksa internetmu lalu coba lagi.']);
        }

        btn.disabled = false;
        btn.innerHTML = label;
    });
})();
</script>
