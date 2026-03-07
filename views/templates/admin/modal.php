<style>
    /* Modal Styles */
    .modal-content {
        border-radius: 10px;
        border: none;
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    }
    .modal-header {
        background-color: #4b6b4b;
        color: white;
        border-radius: 10px 10px 0 0;
        padding: 15px 20px;
    }
    .modal-header .btn-close {
        filter: invert(1);
    }
    .modal-body {
        padding: 25px;
    }
    .modal-body .form-label {
        font-weight: 500;
        color: #333;
        margin-bottom: 5px;
    }
    .modal-body .form-control {
        border-radius: 8px;
        padding: 10px 15px;
        border: 1px solid #ddd;
    }
    .modal-body .form-control:focus {
        border-color: #4b6b4b;
        box-shadow: 0 0 0 3px rgba(75, 107, 75, 0.15);
    }
    .modal-footer {
        padding: 15px 25px;
        border-top: 1px solid #eee;
    }
    .btn-save {
        background-color: #4b6b4b;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 500;
    }
    .btn-save:hover {
        background-color: #3a5a3a;
        color: white;
    }
    .btn-cancel {
        background-color: #6c757d;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 500;
    }
    .btn-cancel:hover {
        background-color: #5a6268;
        color: white;
    }
    .btn-del {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 500;
    }
    .btn-del:hover {
        background-color: #bb2d3b;
        color: white;
    }

    /* Dark Mode for Modal */
    body.dark-mode .modal-content {
        background-color: #1e1e1e;
        color: #fff;
    }
    body.dark-mode .modal-header {
        background-color: #2e4e2e;
        border-bottom: 1px solid #333;
    }
    body.dark-mode .modal-body {
        background-color: #1e1e1e;
    }
    body.dark-mode .modal-body .form-label {
        color: #e0e0e0;
    }
    body.dark-mode .modal-body .form-control {
        background-color: #2d2d2d;
        border-color: #444;
        color: #fff;
    }
    body.dark-mode .modal-body .form-control:focus {
        border-color: #4b6b4b;
        box-shadow: 0 0 0 3px rgba(75, 107, 75, 0.3);
    }
    body.dark-mode .modal-footer {
        background-color: #1e1e1e;
        border-top: 1px solid #333;
    }
    body.dark-mode .btn-save {
        background-color: #5a8a5a;
        color: #fff;
    }
    body.dark-mode .btn-save:hover {
        background-color: #6a9a6a;
        color: #fff;
    }
    body.dark-mode .btn-cancel {
        background-color: #888;
        color: #fff;
    }
    body.dark-mode .btn-cancel:hover {
        background-color: #999;
        color: #fff;
    }
    body.dark-mode .modal-body .text-muted {
        color: #aaa !important;
    }
</style>
