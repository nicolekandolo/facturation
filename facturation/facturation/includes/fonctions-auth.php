<?php

/**
 * Fonctions d'authentification et de gestion de session
 */

function session_start_safe()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function auth_check()
{
    session_start_safe();
    if (empty($_SESSION['user'])) {
        header('Location: /facturation/auth/login.php');
        exit;
    }
    return $_SESSION['user'];
}

function auth_api_check()
{
    session_start_safe();
    if (empty($_SESSION['user'])) {
        json_error('Non authentifié', 401);
    }
    return $_SESSION['user'];
}

function require_role($user, ...$roles)
{
    if (!in_array($user['role'], $roles)) {
        json_error('Accès refusé', 403);
    }
}

function auth_login($username, $password, $user_data)
{
    session_start_safe();
    $_SESSION['user'] = $user_data;
    return true;
}

function auth_logout()
{
    session_start_safe();
    unset($_SESSION['user']);
    session_destroy();
    header('Location: /facturation/auth/login.php');
    exit;
}
