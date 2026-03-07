<?php

/**
 * Set a flash message
 */
function setFlash($type, $message) {
    $_SESSION['flash'][$type] = $message;
}

/**
 * Get and remove a flash message
 */
function getFlash($type) {
    if (isset($_SESSION['flash'][$type])) {
        $message = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $message;
    }
    return null;
}

/**
 * Check if flash message exists
 */
function hasFlash($type) {
    return isset($_SESSION['flash'][$type]);
}

/**
 * Display toast notification (Bootstrap Toast style)
 */
function displayFlash() {
    $colors = [
        'success' => '#2e7d32',
        'error'   => '#b02a37',
        'warning' => '#856404',
        'info'    => '#1565c0',
    ];
    $icons = [
        'success' => 'bi-check-circle',
        'error'   => 'bi-x-circle',
        'warning' => 'bi-exclamation-circle',
        'info'    => 'bi-info-circle',
    ];
    $output = '';

    foreach (['success', 'error', 'warning', 'info'] as $type) {
        if (hasFlash($type)) {
            $msg = htmlspecialchars(getFlash($type));
            $output .= '
            <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999;">
                <div class="toast flash-toast align-items-center text-white border-0"
                     style="background-color:' . $colors[$type] . ';" role="alert" aria-live="assertive">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi ' . $icons[$type] . ' me-2"></i> ' . $msg . '
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            </div>';
        }
    }
    return $output;
}
