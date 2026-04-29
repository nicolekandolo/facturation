<?php

/**
 * Gestion de session
 */
require_once __DIR__ . '/../config/config.php';

function session_init()
{
    session_start_safe();

    if (!empty($_SESSION['user'])) {
        $_SESSION['last_activity'] = time();
    }
}

function session_check_timeout()
{
    session_start_safe();

    if (!isset($_SESSION['last_activity'])) {
        return true;
    }

    if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
        session_destroy();
        return false;
    }

    $_SESSION['last_activity'] = time();
    return true;
}
