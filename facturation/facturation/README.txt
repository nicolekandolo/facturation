╔══════════════════════════════════════════════════════════════════════════════╗
║                                                                              ║
║                        SYSTÈME DE FACTURATION PRO                           ║
║                      Gestion des Factures en CDF                            ║
║                                                                              ║
╚══════════════════════════════════════════════════════════════════════════════╝

STRUCTURE DU PROJET
═════════════════════════════════════════════════════════════════════════════

facturation/
├── index.php                      → Page principale (POS/Facturation)
├── config/
│   └── config.php                 → Configuration (devise, TVA, constantes)
├── auth/
│   ├── login.php                  → Page de connexion
│   ├── logout.php                 → Déconnexion
│   └── session.php                → Gestion de session
├── modules/
│   ├── produits/
│   │   ├── liste.php              → Liste des produits
│   │   ├── enregistrer.php        → Créer un nouveau produit
│   │   └── lire.php               → API - Lire produit par code-barres
│   └── facturation/
│       ├── nouvelle-facture.php   → API - Créer une facture
│       └── afficher-facture.php   → Affichage et filtrage des factures
├── admin/
│   ├── gestion-comptes.php        → Gestion des utilisateurs
│   ├── ajouter-compte.php         → Ajouter un utilisateur
│   └── supprimer-compte.php       → Supprimer un utilisateur
├── data/
│   ├── produits.json              → Base de données des produits
│   ├── factures.json              → Base de données des factures
│   └── utilisateurs.json          → Base de données des utilisateurs
├── includes/
│   ├── header.php                 → En-tête HTML et navigation
│   ├── footer.php                 → Pied de page HTML
│   ├── fonctions-auth.php         → Authentification et session
│   ├── fonctions-produits.php     → Gestion des produits
│   └── fonctions-factures.php     → Gestion des factures
├── assets/
│   ├── css/
│   │   └── style.css              → Feuille de styles
│   └── js/
│       └── scanner.js             → Scanner de code-barres
├── rapports/
│   ├── rapport-journalier.php     → Rapport journalier
│   └── rapport-mensuel.php        → Rapport mensuel
└── README.txt                      → Ce fichier

CONFIGURATION ET PARAMÈTRES
═════════════════════════════════════════════════════════════════════════════

DEVISE:
  • Symbole: CDF (Franc Congolais)
  • Séparateur décimal: , (virgule)
  • Séparateur de milliers: (espace)
  • Format: 1 250,00 CDF

TVA:
  • Taux: 16% (remplace l'ancien 20%)
  • Applicable sur tous les articles
  • Calcul automatique dans les factures

COMPTES DE TEST
═════════════════════════════════════════════════════════════════════════════

  Identifiant     Mot de passe    Rôle
  ─────────────────────────────────────
  dan.mbo         password        Caissier
  manager         password        Manager
  admin           password        Super Administrateur

RÔLES ET PERMISSIONS
═════════════════════════════════════════════════════════════════════════════

CAISSIER:
  • Accès à la facturation (POS)
  • Peut scanner les codes-barres
  • Peut créer des factures
  • Peut encaisser les paiements

MANAGER:
  • Tous les droits du Caissier
  • Gestion des produits et stocks
  • Consultation des factures
  • Rapports de ventes

SUPER ADMINISTRATEUR:
  • Tous les droits
  • Gestion des comptes utilisateurs
  • Accès à toute l'administration
  • Configuration du système

PROCESSUS DE FACTURATION
═════════════════════════════════════════════════════════════════════════════

1. CONNEXION
   • Accéder à /facturation/auth/login.php
   • S'identifier avec identifiant et mot de passe
   • Redirection vers le POS (index.php)

2. SCANNER DE CODE-BARRES
   • Cliquer sur le bouton "Scanner"
   • Positionner le lecteur code-barres
   • Le lecteur envoie automatiquement le code
   • Le produit est ajouté au panier avec quantité 1

3. CONSTRUCTION DU PANIER
   • Recherche par nom ou code-barres dans le catalogue
   • Clic sur un produit pour l'ajouter (quantité 1)
   • Augmenter la quantité si répété
   • Retirer un article avec le bouton "Retirer"

4. CALCUL AUTOMATIQUE
   • Sous-total HT: Somme des prix unitaires × quantités
   • TVA (16%): Sous-total HT × 0.16
   • Total TTC: Sous-total HT + TVA
   • Affichage en temps réel

5. MOYEN DE PAIEMENT
   • Trois options: Espèces, Carte, Mobile
   • Si Espèces: Entrer le montant reçu
   • Calcul automatique du rendu si applicable
   • Autres moyens: simple enregistrement

6. CRÉATION DE LA FACTURE
   • Cliquer sur "Encaisser"
   • La facture est créée et sauvegardée en JSON
   • Les stocks sont automatiquement mis à jour
   • Affichage de la facture avec détails complets
   • Option pour imprimer

7. FORMAT DE LA FACTURE
   ═════════════════════════════════════════════════════════════
   N° Facture: FAC-20260417-001
   Date: 2026-04-17
   Heure: 10:35:22
   Caissier: dan.mbo
   
   Désignation              Prix Unit.    Qté   Sous-total
   ─────────────────────────────────────────────────────────
   Huile de palme 1L      1 200 CDF       2     2 400 CDF
   Savon de Marseille 450g  450 CDF       5     2 250 CDF
   
   TOTAL HT                                      4 650 CDF
   TVA (16%)                                       744 CDF
   ═════════════════════════════════════════════════════════════
   NET À PAYER                                   5 394 CDF
   ═════════════════════════════════════════════════════════════

STRUCTURE JSON DES FACTURES
═════════════════════════════════════════════════════════════════════════════

{
  "id_facture": "FAC-20260417-001",
  "date": "2026-04-17",
  "heure": "10:35:22",
  "caissier": "dan.mbo",
  "client": "Jean Dupont",
  "articles": [
    {
      "code_barre": "3017620422001",
      "nom": "Huile de palme 1L",
      "prix_unitaire_ht": 1200,
      "quantite": 2,
      "sous_total_ht": 2400
    }
  ],
  "total_ht": 2400,
  "tva": 384,
  "total_ttc": 2784
}

GESTION DES PRODUITS
═════════════════════════════════════════════════════════════════════════════

AFFICHER LA LISTE:
  • Accès: Manager et Super Admin
  • URL: /facturation/modules/produits/liste.php
  • Affiche tous les produits avec stocks
  • Alertes si stock < seuil minimum

AJOUTER UN PRODUIT:
  • Accès: Manager et Super Admin
  • URL: /facturation/modules/produits/enregistrer.php
  • Champs requis:
    - Nom du produit
    - Prix HT (en CDF)
    - Code-barres (optionnel)
    - Catégorie (optionnel)
    - Stock initial (défaut: 0)
    - Seuil alerte stock (défaut: 5)

STRUCTURE JSON DES PRODUITS:
  {
    "id": "p1",
    "name": "Huile de palme 1L",
    "price": 1200,
    "stock": 45,
    "barcode": "3017620422001",
    "category": "Alimentaire",
    "min_stock_alert": 10,
    "created_at": "2026-01-15 08:00:00"
  }

GESTION DES COMPTES
═════════════════════════════════════════════════════════════════════════════

ACCÈS:
  • Super Administrateur uniquement
  • URL: /facturation/admin/gestion-comptes.php

RÔLES DISPONIBLES:
  • super_admin: Accès total
  • manager: Gestion produits et factures
  • caissier: Accès POS uniquement

CONSULTATIONS DES FACTURES
═════════════════════════════════════════════════════════════════════════════

ACCÈS:
  • Manager et Super Admin
  • URL: /facturation/modules/facturation/afficher-facture.php

FILTRES DISPONIBLES:
  • Par date (défaut: jour actuel)
  • Par caissier
  • Tous les caissiers

STATISTIQUES AFFICHÉES:
  • Total du jour (TTC)
  • Nombre de factures
  • Montant moyen par facture

SÉCURITÉ ET AUTHENTIFICATION
═════════════════════════════════════════════════════════════════════════════

SESSIONS:
  • Timeout: 1 heure d'inactivité
  • Déconnexion automatique après timeout
  • Stocker en SESSION['user']

ACCÈS:
  • Chaque page vérifie l'authentification
  • Redirection vers login si non authentifié
  • Vérification du rôle pour actions sensibles

NOTES IMPORTANTES
═════════════════════════════════════════════════════════════════════════════

1. FORMATS DE DATE/HEURE:
   • Date: Y-m-d (2026-04-17)
   • Heure: H:i:s (10:35:22)
   • Datetime: Y-m-d H:i:s

2. NUMÉROTATION FACTURES:
   • Format: FAC-YYYYMMDD-NNN
   • Réinitialisation quotidienne
   • Exemple: FAC-20260417-001

3. STOCKS:
   • Mise à jour automatique à la facturation
   • Alerte si stock < seuil minimum
   • Vérification disponibilité avant création facture

4. DONNÉES JSON:
   • Sauvegarde formatée (JSON_PRETTY_PRINT)
   • UTF-8 non échappé (JSON_UNESCAPED_UNICODE)
   • Fichier verrouillé pendant écriture (LOCK_EX)

DÉPANNAGE ET AIDE
═════════════════════════════════════════════════════════════════════════════

PROBLÈME: "Non authentifié"
  → Vérifiez vos identifiants
  → Peut être un timeout de session
  → Reconnectez-vous

PROBLÈME: "Produit non trouvé au scan"
  → Vérifiez que le code-barres est correct
  → Le produit existe-t-il dans la base?
  → Vérifiez le format du code-barres

PROBLÈME: Les factures ne s'affichent pas
  → Vérifiez les filtres de date et caissier
  → Vérifiez que les factures existent (data/factures.json)
  → Assurez-vous d'avoir le rôle Manager ou Super Admin

PROBLÈME: Stock incorrect
  → Vérifiez le fichier data/produits.json
  → Vérifiez que les mises à jour de stock sont enregistrées
  → Cherchez les factures qui concernent ce produit

API ENDPOINTS
═════════════════════════════════════════════════════════════════════════════

POST /facturation/modules/produits/lire.php
  • Corps: {"barcode": "3017620422001"}
  • Réponse: {success: true, product: {...}}

POST /facturation/modules/facturation/nouvelle-facture.php
  • Corps: {
      items: [{code_barre, nom, prix_unitaire_ht, quantite}, ...],
      caissier: "username",
      customer_name: "nom"
    }
  • Réponse: {success: true, invoice: {...}}

CONTACT ET SUPPORT
═════════════════════════════════════════════════════════════════════════════

Version: 1.0
Date: 2026-04-17
Auteur: Système de Facturation PRO
Devise: CDF (Franc Congolais)
TVA: 16%

═════════════════════════════════════════════════════════════════════════════
