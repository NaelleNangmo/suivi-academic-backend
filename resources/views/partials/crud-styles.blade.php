<style>
    .page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; }
    .page-header h1 { font-size:1.6rem; font-weight:700; }
    .btn { display:inline-flex; align-items:center; gap:.4rem; padding:.55rem 1.2rem; border-radius:8px; font-size:.875rem; font-weight:600; cursor:pointer; border:none; text-decoration:none; transition:all .15s; }
    .btn-primary { background:#4f46e5; color:#fff; }
    .btn-primary:hover { background:#3730a3; }
    .btn-success { background:#10b981; color:#fff; }
    .btn-success:hover { background:#059669; }
    .btn-danger  { background:#ef4444; color:#fff; }
    .btn-danger:hover  { background:#dc2626; }
    .btn-warning { background:#f59e0b; color:#fff; }
    .btn-warning:hover { background:#d97706; }
    .btn-secondary { background:#f1f5f9; color:#1e293b; border:1px solid #e2e8f0; }
    .btn-secondary:hover { background:#e2e8f0; }
    .btn-sm { padding:.35rem .8rem; font-size:.78rem; }
    .card { background:#fff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,.06); overflow:hidden; }
    .card-header { padding:1.25rem 1.5rem; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; }
    .card-title { font-size:1rem; font-weight:600; }
    .table-wrap { overflow-x:auto; }
    table { width:100%; border-collapse:collapse; }
    thead th { background:#f8fafc; padding:.85rem 1rem; text-align:left; font-size:.8rem; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.05em; border-bottom:1px solid #e2e8f0; }
    tbody td { padding:.85rem 1rem; border-bottom:1px solid #f1f5f9; font-size:.875rem; vertical-align:middle; }
    tbody tr:last-child td { border-bottom:none; }
    tbody tr:hover { background:#fafafa; }
    .badge { display:inline-block; padding:.2rem .6rem; border-radius:20px; font-size:.72rem; font-weight:600; }
    .badge-green  { background:#d1fae5; color:#065f46; }
    .badge-red    { background:#fee2e2; color:#991b1b; }
    .badge-blue   { background:#dbeafe; color:#1e40af; }
    .badge-amber  { background:#fef3c7; color:#92400e; }
    .badge-purple { background:#ede9fe; color:#5b21b6; }
    .badge-gray   { background:#f1f5f9; color:#475569; }
    .empty { text-align:center; padding:3rem; color:#94a3b8; }
    .empty-icon { font-size:2.5rem; margin-bottom:.75rem; }
    /* Modal */
    .overlay { position:fixed; inset:0; background:rgba(0,0,0,.45); display:flex; align-items:center; justify-content:center; z-index:200; padding:1rem; opacity:0; visibility:hidden; transition:opacity .2s,visibility .2s; }
    .overlay.open { opacity:1; visibility:visible; }
    .modal { background:#fff; border-radius:14px; width:100%; max-width:520px; max-height:90vh; overflow-y:auto; transform:translateY(16px); transition:transform .2s; box-shadow:0 20px 40px rgba(0,0,0,.15); }
    .overlay.open .modal { transform:translateY(0); }
    .modal-head { padding:1.25rem 1.5rem; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; }
    .modal-head h2 { font-size:1.1rem; font-weight:700; }
    .modal-close { background:none; border:none; font-size:1.4rem; cursor:pointer; color:#94a3b8; line-height:1; padding:.2rem; }
    .modal-close:hover { color:#1e293b; }
    .modal-body { padding:1.5rem; }
    .form-group { margin-bottom:1.1rem; }
    .form-group label { display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:.35rem; }
    .form-group input, .form-group select, .form-group textarea {
        width:100%; padding:.6rem .85rem; border:1.5px solid #e2e8f0; border-radius:8px;
        font-size:.875rem; font-family:inherit; color:#1e293b; background:#fff;
        transition:border-color .15s, box-shadow .15s;
    }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
        outline:none; border-color:#4f46e5; box-shadow:0 0 0 3px rgba(79,70,229,.1);
    }
    .form-group input:disabled { background:#f8fafc; color:#94a3b8; cursor:not-allowed; }
    .form-hint { font-size:.75rem; color:#94a3b8; margin-top:.25rem; }
    .modal-foot { display:flex; justify-content:flex-end; gap:.75rem; padding-top:1rem; border-top:1px solid #e2e8f0; margin-top:1rem; }
    /* Toast */
    #toast { position:fixed; bottom:1.5rem; right:1.5rem; padding:.85rem 1.25rem; border-radius:10px; font-size:.875rem; font-weight:500; color:#fff; z-index:999; opacity:0; transform:translateY(8px); transition:all .3s; pointer-events:none; max-width:320px; }
    #toast.show { opacity:1; transform:translateY(0); }
    #toast.success { background:#10b981; }
    #toast.error   { background:#ef4444; }
    .actions { display:flex; gap:.4rem; }
    .text-muted { color:#94a3b8; font-size:.8rem; }
    .alert { padding:.85rem 1rem; border-radius:8px; margin-bottom:1.25rem; font-size:.875rem; font-weight:500; }
    .alert-success { background:#d1fae5; color:#065f46; border:1px solid #a7f3d0; }
    .alert-error   { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; }
</style>
