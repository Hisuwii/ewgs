<?php
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('BASE', ($host === 'localhost' || $host === '127.0.0.1') ? '/ewgs' : '');
