<!-- DataTables CSS - Include in <head> -->
<link rel="stylesheet" href="/ewgs/public/css/dataTables.bootstrap5.min.css">

<style>
    /* DataTable Custom Styles */
    .table thead th,
    table.dataTable thead th {
        text-align: center !important;
        vertical-align: middle !important;
    }
    .table tbody td,
    table.dataTable tbody td {
        text-align: center !important;
        vertical-align: middle !important;
    }
    .dataTables_wrapper {
        position: relative;
        z-index: 1;
    }
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_length {
        position: relative;
        z-index: 2;
    }
    .dataTables_wrapper .dataTables_length select {
        padding: 5px 30px 5px 10px;
        pointer-events: auto;
    }
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 8px;
        padding: 5px 10px;
        border: 1px solid #ddd;
        pointer-events: auto;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #4b6b4b;
        box-shadow: 0 0 0 3px rgba(75, 107, 75, 0.15);
        outline: none;
    }

    /* Dark Mode for DataTables */
    body.dark-mode .table {
        color: #fff;
    }
    body.dark-mode .table thead {
        background-color: #2d2d2d;
    }
    body.dark-mode .table-striped > tbody > tr:nth-of-type(odd) {
        background-color: rgba(255, 255, 255, 0.05);
    }
    body.dark-mode .table-striped > tbody > tr:nth-of-type(even) {
        background-color: #1e1e1e;
    }
    body.dark-mode .table-hover > tbody > tr:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }
    body.dark-mode .dataTables_wrapper .dataTables_length,
    body.dark-mode .dataTables_wrapper .dataTables_filter,
    body.dark-mode .dataTables_wrapper .dataTables_info,
    body.dark-mode .dataTables_wrapper .dataTables_paginate {
        color: #e0e0e0;
    }
    body.dark-mode .dataTables_wrapper .dataTables_length select,
    body.dark-mode .dataTables_wrapper .dataTables_filter input {
        background-color: #2d2d2d;
        border-color: #444;
        color: #fff;
    }
    body.dark-mode .dataTables_wrapper .dataTables_paginate .paginate_button {
        color: #e0e0e0 !important;
    }
    body.dark-mode .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #3a5a3a !important;
        border-color: #3a5a3a !important;
        color: #fff !important;
    }
    body.dark-mode .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #4b6b4b !important;
        border-color: #4b6b4b !important;
        color: #fff !important;
    }
    body.dark-mode .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        color: #666 !important;
    }
</style>

<!-- DataTables JS -->
<script src="/ewgs/public/js/jquery.dataTables.min.js"></script>
<script src="/ewgs/public/js/dataTables.bootstrap5.min.js"></script>
