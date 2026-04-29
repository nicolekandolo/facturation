SYSTEME DE FACTURATION - TP PHP PROCEDURAL

1) Prerequis
- PHP 8+
- Serveur local (Laragon, XAMPP, etc.)

2) Installation locale
- Placez le dossier "facturation" dans le dossier web de votre serveur.
- Ouvrez dans le navigateur : http://localhost/facturation/

3) Comptes par défaut
- Super Admin : admin / admin123
- Manager : manager / manager123
- Caissier : caissier / caissier123

4) Roles
- caissier : facture
- manager : facture + produits + rapports
- super_admin : tout + gestion des comptes

5) Donnees
- Produits : data/produits.json
- Factures : data/factures.json
- Utilisateurs : data/utilisateurs.json

6) Fonctionnalites
- Authentification avec roles
- Gestion des produits (CRUD)
- Creation de factures avec scanner code-barres
- Mise a jour automatique du stock
- Rapports journaliers et mensuels
- Gestion des comptes utilisateurs (super admin uniquement)
- Interface responsive et moderne
