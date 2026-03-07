<?php

/**
 * Validate login input
 */
function validateLoginInput($email, $password) {
    $errors = [];

    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }

    if (empty($password)) {
        $errors[] = 'Password is required';
    }

    return $errors;
}

/**
 * Validate required field
 */
function validateRequired($value, $fieldName = 'This field') {
    if (empty(trim($value))) {
        return ["{$fieldName} is required"];
    }
    return [];
}

/**
 * Validate email format
 */
function validateEmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['Invalid email format'];
    }
    return [];
}

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
