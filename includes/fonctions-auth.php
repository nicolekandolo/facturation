<?php
require_once __DIR__ . '/fonctions-produits.php';

function verifier_connexion($identifiant, $mot_de_passe, $fichier) {
    $utilisateurs = lire_json($fichier);

    foreach ($utilisateurs as $utilisateur) {
        if ($utilisateur['identifiant'] === $identifiant && $utilisateur['actif'] === true) {
            if (password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
                return $utilisateur;
            }
        }
    }

    return null;
}

function ajouter_utilisateur($nouvel_utilisateur, $fichier) {
    $utilisateurs = lire_json($fichier);
    $utilisateurs[] = $nouvel_utilisateur;
    ecrire_json($fichier, $utilisateurs);
}

function supprimer_utilisateur($identifiant, $fichier) {
    $utilisateurs = lire_json($fichier);
    $resultat = [];

    foreach ($utilisateurs as $utilisateur) {
        if ($utilisateur['identifiant'] !== $identifiant) {
            $resultat[] = $utilisateur;
        }
    }

    ecrire_json($fichier, $resultat);
}
