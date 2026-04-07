<div id="toast"></div>
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

function toast(msg, type='success') {
    const t = document.getElementById('toast');
    t.textContent = msg; t.className = 'show ' + type;
    setTimeout(() => t.className = '', 3000);
}

function openModal(id)  { document.getElementById(id).classList.add('open');  document.body.style.overflow='hidden'; }
function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow=''; }

document.querySelectorAll('.overlay').forEach(o => {
    o.addEventListener('click', e => { if(e.target === o) closeModal(o.id); });
});

async function apiCall(method, url, body=null) {
    const opts = { method, headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': CSRF } };
    if(body) opts.body = JSON.stringify(body);
    const res = await fetch(url, opts);
    const data = await res.json().catch(() => ({}));
    return { ok: res.ok, status: res.status, data };
}

function confirmDelete(msg) { return confirm(msg || 'Confirmer la suppression ?'); }
</script>
