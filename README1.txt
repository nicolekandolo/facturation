# Système de Facturation POS (FC)

## Vue d'ensemble

Ce dépôt contient une application de caisse (POS) et de facturation orientée commerce de détail.
Le périmètre de production actif est le dossier `facturation/`.
Le dossier `php/` est un périmètre legacy/sandbox à traiter séparément.

Objectifs fonctionnels principaux :
- gestion des articles (catalogue + stock),
- scan code-barres caisse,
- construction de facture HT/TVA/TTC,
- encaissement multi-mode (espèces, carte, mobile),
- persistance JSON horodatée des factures,
- administration des comptes par rôles.

---

## Arborescence officielle

```text
facturation/
  index.php
  config/
    config.php
  auth/
    login.php
    logout.php
    session.php
  modules/
    produits/
      enregistrer.php
      lire.php
      liste.php
    facturation/
      nouvelle-facture.php
      calcul.php
      afficher-facture.php
  admin/
    gestion-comptes.php
    ajouter-compte.php
    supprimer-compte.php
  data/
    produits.json
    factures.json
    utilisateurs.json
  includes/
    header.php
    footer.php
    fonctions-produits.php
    fonctions-factures.php
    fonctions-auth.php
    fonctions.php
  assets/
    css/style.css
    js/scanner.js
  rapports/
    rapport-journalier.php
    rapport-mensuel.php
  README.txt
```

---

## Stack technique

- **Langage serveur** : PHP (procédural, architecture modulaire par domaine)
- **Stockage** : fichiers JSON (`data/*.json`) avec verrouillage d'écriture
- **Front** : HTML + JS vanilla + TailwindCSS (CDN)
- **Session/Auth** : session PHP + contrôle de rôles
- **Devise métier** : `FC`
- **Fiscalité** : TVA globale à `16%`

---

## Architecture applicative

### 1) Couche Configuration
- `config/config.php` centralise :
  - chemins systèmes,
  - constantes de devise,
  - taux TVA,
  - formats date/heure,
  - rôles applicatifs.

### 2) Couche Services (includes)
- `includes/fonctions.php` : primitives utilitaires (JSON output, redirection, format prix, calcul TVA/TTC).
- `includes/fonctions-auth.php` : session sécurisée, garde d'authentification, garde de rôle.
- `includes/fonctions-produits.php` : CRUD produit + recherche code-barres + décrément stock.
- `includes/fonctions-factures.php` : génération ID facture, normalisation lignes, calcul agrégats HT/TVA/TTC, persistance.

### 3) Couche API métier (modules)
- `modules/produits/lire.php` : recherche produit par code-barres.
- `modules/facturation/calcul.php` : calcul des totaux sur lignes article.
- `modules/facturation/nouvelle-facture.php` : validation + création facture + update stock.

### 4) Couche Présentation
- `index.php` : poste caisse complet (catalogue, scan, panier, paiement, aperçu facture, impression).
- `modules/facturation/afficher-facture.php` : consultation et filtrage.
- `admin/*.php` : gestion des comptes.

---

## Flux caisse détaillé (scan -> facture)

1. Le caissier scanne/saisit un code-barres.
2. Le front appelle `POST /facturation/modules/produits/lire.php`.
3. Le serveur lit `data/produits.json` et renvoie le produit si trouvé.
4. Le front affiche automatiquement désignation + prix HT.
5. Le caissier saisit la quantité vendue.
6. La ligne est ajoutée au panier et les totaux sont recalculés.
7. Après sélection d'un moyen de paiement, un aperçu facture est affiché.
8. À validation, `POST /facturation/modules/facturation/nouvelle-facture.php` persiste :
   - détails article par article,
   - total HT,
   - TVA,
   - total TTC,
   - horodatage (`date` + `heure`),
   - identité caissier,
   - mode de paiement.
9. Le stock est décrémenté en cohérence avec les quantités vendues.

---

## Contrats API (résumé)

### `POST /facturation/modules/produits/lire.php`
**Body**
```json
{ "barcode": "3017620422001" }
```
**Réponse**
```json
{ "success": true, "product": { "id": "p1", "name": "Huile de palme 1L", "price": 1200, "stock": 45, "barcode": "3017620422001" } }
```

### `POST /facturation/modules/facturation/calcul.php`
**Body**
```json
{ "articles": [{ "prix_unitaire_ht": 1200, "quantite": 2 }] }
```
**Réponse**
```json
{ "success": true, "taux_tva": 16, "total_ht": 2400, "tva": 384, "total_ttc": 2784 }
```

### `POST /facturation/modules/facturation/nouvelle-facture.php`
**Body**
```json
{
  "items": [
    { "code_barre": "3017620422001", "nom": "Huile de palme 1L", "prix_unitaire_ht": 1200, "quantite": 2 }
  ],
  "caissier": "dan.mbo",
  "customer_name": "Client A",
  "payment_method": "cash"
}
```
**Réponse**
```json
{ "success": true, "invoice": { "id_facture": "FAC-20260417-001", "total_ht": 2400, "tva": 384, "total_ttc": 2784 } }
```

---

## Modèle de données

### Produit (`data/produits.json`)
```json
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
```

### Facture (`data/factures.json`)
```json
{
  "id_facture": "FAC-20260417-001",
  "date": "2026-04-17",
  "heure": "10:35:22",
  "caissier": "dan.mbo",
  "paiement": "Espèces",
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
```

---

## Sécurité et gouvernance

- Contrôle d'accès par rôles : `caissier`, `manager`, `super_admin`.
- Garde d'auth systématique sur pages et endpoints critiques.
- Persistance JSON avec `LOCK_EX` pour limiter les collisions d'écriture.
- Entrées utilisateur validées côté serveur (présence, types, cohérence métier).
- Recommandation production :
  - migrer vers base SQL transactionnelle,
  - hash de mots de passe fort (`password_hash`),
  - journalisation des opérations sensibles.

---

## Exécution locale (recommandée)

Depuis la racine du dépôt :

```bash
cd facturation
php -S 127.0.0.1:8000
```

Accès :
- `http://127.0.0.1:8000/auth/login.php`
- après login, redirection vers `index.php` (POS)

Comptes de test (à adapter) :
- `dan.mbo / password` (caissier)
- `manager / password` (manager)
- `admin / password` (super_admin)

---

## Politique de maintenance

1. Toute évolution fonctionnelle se fait dans `facturation/`.
2. Ne pas mélanger les changements avec `php/` sans plan de migration explicite.
3. Conserver la cohérence devise (`FC`) et TVA (`16%`) dans UI, API et persistance.
4. Vérifier la syntaxe PHP avant livraison (`php -l`).
5. Documenter les endpoints et impacts data dans `facturation/README.txt`.

---

## Références internes

- Documentation détaillée du module : `facturation/README.txt`
- Point d'entrée caisse : `facturation/index.php`
- API facture : `facturation/modules/facturation/nouvelle-facture.php`
- Données métier : `facturation/data/*.json`
