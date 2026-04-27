<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function utilisateur_connecte() {
    return isset($_SESSION['utilisateur']);
}

function utilisateur_actuel() {
    if (utilisateur_connecte()) {
        return $_SESSION['utilisateur'];
    }
    return null;
}

function role_actuel() {
    $user = utilisateur_actuel();
    if ($user && isset($user['role'])) {
        return $user['role'];
    }
    return '';
}

function rediriger_accueil_role() {
    $role = role_actuel();
    if ($role === 'caissier') {
        header('Location: /facturation/modules/facturation/nouvelle-facture.php');
        exit;
    }
    header('Location: /facturation/index.php');
    exit;
}

function exiger_connexion() {
    if (!utilisateur_connecte()) {
        header('Location: /facturation/auth/login.php');
        exit;
    }
}

function exiger_role($roles_autorises) {
    exiger_connexion();
    if (!in_array(role_actuel(), $roles_autorises)) {
        $_SESSION['message_erreur'] = 'Acces non autorise pour votre role.';
        rediriger_accueil_role();
    }
}
