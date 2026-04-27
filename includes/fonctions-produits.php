<?php
function lire_json($chemin) {
    if (!file_exists($chemin)) {
        return [];
    }

    $contenu = file_get_contents($chemin);
    if ($contenu === false || trim($contenu) === '') {
        return [];
    }

    $data = json_decode($contenu, true);
    if (!is_array($data)) {
        return [];
    }

    return $data;
}

function ecrire_json($chemin, $tableau) {
    $json = json_encode($tableau, JSON_PRETTY_PRINT);
    file_put_contents($chemin, $json);
}

function trouver_produit_par_code($code, $fichier) {
    $produits = lire_json($fichier);
    foreach ($produits as $produit) {
        if ($produit['code_barre'] === $code) {
            return $produit;
        }
    }
    return null;
}

function ajouter_produit($produit, $fichier) {
    $produits = lire_json($fichier);
    $produits[] = $produit;
    ecrire_json($fichier, $produits);
}

function mettre_a_jour_stock($code_barre, $quantite_vendue, $fichier) {
    $produits = lire_json($fichier);
    for ($i = 0; $i < count($produits); $i++) {
        if ($produits[$i]['code_barre'] === $code_barre) {
            $produits[$i]['quantite_stock'] = $produits[$i]['quantite_stock'] - $quantite_vendue;
        }
    }
    ecrire_json($fichier, $produits);
}
