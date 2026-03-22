<!-- DataTables CSS -->
<link rel="stylesheet" href="<?= BASE ?>/public/css/dataTables.bootstrap5.min.css">

<style>
    /* ── Override table-dark header to use theme green ──── */
    table.dataTable thead.table-dark th,
    .table-dark > thead > tr > th {
        background-color: #4b6b4b !important;
        border-color: #3a5a3a !important;
        color: #fff !important;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        padding: 13px 16px !important;
        text-align: center !important;
        vertical-align: middle !important;
        white-space: nowrap;
    }

    /* ── Table body ─────────────────────────────────────── */
    table.dataTable {
        border-collapse: collapse !important;
        font-size: 15px;
        width: 100% !important;
        background: #fff;
    }
    table.dataTable tbody td {
        text-align: center !important;
        vertical-align: middle !important;
        padding: 12px 16px !important;
        border-top: 1px solid #eef0f2 !important;
        border-left: none !important;
        border-right: none !important;
        color: #1a1a1a;
        font-weight: 500;
        line-height: 1.5;
    }
    table.dataTable tbody tr:first-child td {
        border-top: none !important;
    }
    table.dataTable tbody td:last-child {
        white-space: nowrap;
    }
    table.dataTable tbody tr:nth-child(even) {
        background-color: #f7faf7;
    }
    table.dataTable tbody tr:nth-child(odd) {
        background-color: #ffffff;
    }
    table.dataTable tbody tr:hover {
        background-color: #eef4ee !important;
        transition: background-color 0.12s ease;
    }

    /* ── Remove outer borders ───────────────────────────── */
    table.dataTable,
    .table-responsive {
        border: none !important;
    }

    /* ── Wrapper card ───────────────────────────────────── */
    .dataTables_wrapper {
        background: #fff;
        border: 1px solid #e5e9e5;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        padding-bottom: 4px;
    }

    /* ── Controls row ───────────────────────────────────── */
    .dataTables_wrapper .row:first-child {
        background: #f5f7f5;
        border-bottom: 1px solid #e8ece8;
        border-radius: 12px 12px 0 0;
        padding: 10px 6px;
        margin: 0;
    }
    .dataTables_wrapper .dataTables_length label,
    .dataTables_wrapper .dataTables_filter label {
        font-size: 13px;
        color: #555;
        font-weight: 500;
        margin: 0;
    }
    .dataTables_wrapper .dataTables_filter {
        text-align: right;
    }
    .dataTables_wrapper .dataTables_length select {
        appearance: none;
        -webkit-appearance: none;
        padding: 5px 30px 5px 10px;
        border-radius: 7px;
        border: 1px solid #d0d8d0;
        font-size: 13px;
        background-color: #fff;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 16 16'%3E%3Cpath fill='%234b6b4b' d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        color: #333;
        cursor: pointer;
    }
    .dataTables_wrapper .dataTables_length select:focus {
        border-color: #4b6b4b;
        outline: none;
        box-shadow: 0 0 0 2px rgba(75,107,75,0.15);
    }
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 7px;
        padding: 5px 12px;
        border: 1px solid #d0d8d0;
        font-size: 13px;
        background: #fff;
        color: #333;
        min-width: 200px;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #4b6b4b;
        box-shadow: 0 0 0 2px rgba(75,107,75,0.15);
        outline: none;
    }

    /* ── Bottom bar ─────────────────────────────────────── */
    .dataTables_wrapper .row:last-child {
        padding: 8px 6px 4px;
        margin: 0;
    }
    .dataTables_wrapper .dataTables_info {
        font-size: 13px;
        color: #444;
        font-weight: 500;
        padding-top: 8px;
    }
    .dataTables_wrapper .dataTables_paginate {
        padding-top: 4px;
    }
    .dataTables_wrapper .pagination {
        gap: 4px;
        flex-wrap: wrap;
        margin: 0;
    }
    .dataTables_wrapper .pagination .page-item .page-link {
        border-radius: 8px !important;
        border: 1px solid #d8e4d8;
        color: #4b6b4b;
        font-size: 13px;
        font-weight: 500;
        padding: 6px 13px;
        background: #fff;
        transition: background 0.13s, color 0.13s, border-color 0.13s;
        box-shadow: none;
        outline: none;
    }
    .dataTables_wrapper .pagination .page-item .page-link:hover {
        background: #e8f2e8;
        border-color: #4b6b4b;
        color: #2e4e2e;
    }
    .dataTables_wrapper .pagination .page-item.active .page-link {
        background: #4b6b4b;
        border-color: #4b6b4b;
        color: #fff;
        font-weight: 700;
        box-shadow: 0 2px 6px rgba(75,107,75,0.3);
    }
    .dataTables_wrapper .pagination .page-item.disabled .page-link {
        background: #f7f7f7;
        border-color: #e8e8e8;
        color: #bbb;
        cursor: default;
    }

    /* ── Dark Mode ──────────────────────────────────────── */
    body.dark-mode div.dt-processing,
    body.dark-mode .dataTables_processing {
        background: rgba(30,30,30,0.9) !important;
    }
    body.dark-mode div.dt-processing::after,
    body.dark-mode .dataTables_processing::after {
        border-color: #2a3f2a;
        border-top-color: #4b6b4b;
    }

    body.dark-mode .dataTables_wrapper {
        background: #1e1e1e;
        border-color: #2a2a2a;
        box-shadow: 0 2px 10px rgba(0,0,0,0.3);
    }
    body.dark-mode .dataTables_wrapper .row:first-child {
        background: #252525;
        border-bottom-color: #2a2a2a;
    }
    body.dark-mode table.dataTable {
        background: #1e1e1e;
        color: #1a1a1a;
    }
    body.dark-mode table.dataTable tbody td {
        border-top-color: #2a2a2a !important;
        color: #1a1a1a;
    }
    body.dark-mode table.dataTable tbody tr:nth-child(even) {
        background-color: #252525;
    }
    body.dark-mode table.dataTable tbody tr:nth-child(odd) {
        background-color: #1e1e1e;
    }
    body.dark-mode table.dataTable tbody tr:hover {
        background-color: #2e3e2e !important;
    }
    body.dark-mode .dataTables_wrapper .dataTables_length label,
    body.dark-mode .dataTables_wrapper .dataTables_filter label {
        color: #bbb;
    }
    body.dark-mode .dataTables_wrapper .dataTables_length select {
        background-color: #2d2d2d;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 16 16'%3E%3Cpath fill='%23aaaaaa' d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        border-color: #3a3a3a;
        color: #e0e0e0;
    }
    body.dark-mode .dataTables_wrapper .dataTables_filter input {
        background: #2d2d2d;
        border-color: #3a3a3a;
        color: #e0e0e0;
    }
    body.dark-mode .dataTables_wrapper .dataTables_info {
        color: #bbb;
    }
    body.dark-mode .dataTables_wrapper .pagination .page-item .page-link {
        background: #252525;
        border-color: #3a3a3a;
        color: #bbb;
    }
    body.dark-mode .dataTables_wrapper .pagination .page-item .page-link:hover {
        background: #3a5a3a;
        border-color: #3a5a3a;
        color: #fff;
    }
    body.dark-mode .dataTables_wrapper .pagination .page-item.active .page-link {
        background: #4b6b4b !important;
        border-color: #4b6b4b !important;
        color: #fff !important;
        font-weight: 700;
    }
    body.dark-mode .dataTables_wrapper .pagination .page-item.disabled .page-link {
        color: #555;
        border-color: #2a2a2a;
        background: #1a1a1a;
        cursor: default;
    }

    /* ── Mobile ──────────────────────────────────────────── */
    @media (max-width: 576px) {
        .container-fluid {
            padding-left: 8px !important;
            padding-right: 8px !important;
        }
        .dataTables_wrapper .row > div[class*="col-"] {
            width: 100% !important;
            max-width: 100% !important;
            flex: 0 0 100% !important;
            padding-left: 0;
            padding-right: 0;
        }
        .dataTables_wrapper .row:first-child {
            flex-direction: column;
            gap: 10px;
            padding: 14px 14px;
            margin: 0;
        }
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            width: 100% !important;
            max-width: 100%;
            text-align: left !important;
        }
        .dataTables_wrapper .dataTables_length label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }
        .dataTables_wrapper .dataTables_length select {
            padding: 7px 30px 7px 10px;
            font-size: 14px;
        }
        .dataTables_wrapper .dataTables_filter label {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }
        .dataTables_wrapper .dataTables_filter input {
            flex: 1;
            min-width: 0;
            box-sizing: border-box;
            padding: 7px 12px;
            font-size: 14px;
        }
        .dataTables_wrapper {
            max-width: 100%;
            overflow-x: hidden;
        }
        .dataTables_wrapper .dt-row {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        table.dataTable {
            width: auto !important;
            min-width: 100%;
            font-size: 14px;
        }
        table.dataTable tbody td {
            padding: 11px 12px !important;
            font-weight: 400;
        }
        table.dataTable thead.table-dark th,
        .table-dark > thead > tr > th {
            padding: 11px 12px !important;
            font-size: 12px;
        }
        .dataTables_wrapper .row:last-child {
            flex-direction: column;
            align-items: stretch;
            gap: 8px;
            padding: 14px 14px;
            margin: 0;
        }
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            width: 100% !important;
            text-align: center !important;
            padding-top: 0;
            font-size: 14px;
        }
        .dataTables_wrapper .pagination {
            justify-content: center;
            margin: 0;
        }
        .dataTables_wrapper .pagination .page-item .page-link {
            padding: 8px 14px;
            font-size: 14px;
        }
        table.dataTable tbody .btn-sm {
            font-size: 0 !important;
            padding: 5px 9px;
        }
        table.dataTable tbody .btn-sm i {
            font-size: 13px !important;
        }
    }
</style>

<!-- DataTables JS -->
<script src="<?= BASE ?>/public/js/jquery.dataTables.min.js"></script>
<script src="<?= BASE ?>/public/js/dataTables.bootstrap5.min.js"></script>
<script>
    // Suppress DataTables' built-in alert popup (tn/7) and show a toast instead
    $.fn.dataTable.ext.errMode = 'none';
    $(document).on('error.dt', function (e, settings, techNote, message) {
        console.error('[DataTables]', message);
        if (typeof showToast === 'function') {
            showToast('error', 'Failed to load table data. Please refresh the page.');
        }
    });
</script>
