<?php

/**
 * Déconnexion
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/fonctions-auth.php';

session_start_safe();
unset($_SESSION['user']);
session_destroy();

header('Location: /facturation/auth/login.php');
exit;
