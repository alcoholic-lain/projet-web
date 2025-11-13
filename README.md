# Innovation DB - Projet Web

## Description
Plateforme de gestion d'innovations spatiales avec un système de catégories, commentaires et votes.

Auteur: Hichem Challakhi 🚀

## Structure du Projet

```
projet-web/
├── model/
│   ├── config/
│   │   └── data-base.php         # Configuration PDO
│   └── Innovation/
│       ├── Category.php          # Modèle Category
│       ├── Innovation.php        # Modèle Innovation
│       ├── Commentaire.php       # Modèle Commentaire
│       ├── Vote.php              # Modèle Vote
│       ├── PieceJointe.php       # Modèle PieceJointe
│       └── schema.sql            # Schéma de base de données
├── controller/
│   └── components/
│       ├── CategoryController.php    # API REST pour les catégories
│       └── InnovationController.php  # API REST pour les innovations
├── veiw/
│   ├── Admin/                    # BackOffice
│   │   ├── index.html           # Tableau de bord admin
│   │   ├── a_Innovation.html    # Liste des innovations (admin)
│   │   ├── a_Category.html      # Liste des catégories (admin)
│   │   ├── add_Category.html    # Ajouter une catégorie
│   │   └── edit_Category.html   # Modifier une catégorie
│   └── Client/                  # FrontOffice
│       ├── src/
│       │   ├── list_Innovation.html    # Liste des innovations
│       │   ├── add_Innovation.html     # Ajouter une innovation
│       │   └── details_Innovation.html # Détails d'une innovation
│       └── assets/
│           ├── css/
│           └── js/
│               ├── innovation.js      # Module JS innovations
│               └── category.js        # Module JS catégories
└── data set/
    └── user_data.csv
```

## Installation

### 1. Prérequis
- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache/Nginx)
- PDO MySQL extension

### 2. Configuration de la base de données

1. Créer la base de données :
```bash
mysql -u root -p < model/Innovation/schema.sql
```

2. Configurer les identifiants dans `model/config/data-base.php` :
```php
private $host = "localhost";
private $db_name = "innovation_db";
private $username = "root";
private $password = "";
```

### 3. Lancement du projet

Placer le projet dans le dossier web du serveur (htdocs, www, etc.) et accéder à :

**FrontOffice :**
```
http://localhost/projet-web/veiw/Client/src/list_Innovation.html
```

**BackOffice :**
```
http://localhost/projet-web/veiw/Admin/index.html
```

## Fonctionnalités

### BackOffice (Admin)
✅ **CRUD Catégories**
- Créer une nouvelle catégorie
- Lire/Afficher toutes les catégories
- Modifier une catégorie existante
- Supprimer une catégorie

✅ **Gestion des Innovations**
- Valider/Rejeter les innovations soumises
- Voir les détails complets

### FrontOffice (Client)
✅ **CRUD Innovations**
- Soumettre une nouvelle innovation
- Voir la liste des innovations
- Voir les détails d'une innovation

✅ **Contrôles d'entrée**
- Validation des champs obligatoires
- Sanitization des données (htmlspecialchars, strip_tags)

## API REST - CategoryController

**Base URL:** `/controller/components/CategoryController.php`

### Endpoints

#### GET - Lire toutes les catégories
```
GET /CategoryController.php
Response: { "records": [...] }
```

#### GET - Lire une catégorie
```
GET /CategoryController.php?id=1
Response: { "id": 1, "nom": "...", "description": "...", "date_creation": "..." }
```

#### POST - Créer une catégorie
```
POST /CategoryController.php
Body: { "nom": "Nouvelle catégorie", "description": "Description..." }
Response: { "message": "Category created successfully." }
```

#### PUT - Mettre à jour une catégorie
```
PUT /CategoryController.php
Body: { "id": 1, "nom": "Nom modifié", "description": "Description..." }
Response: { "message": "Category updated successfully." }
```

#### DELETE - Supprimer une catégorie
```
DELETE /CategoryController.php
Body: { "id": 1 }
Response: { "message": "Category deleted successfully." }
```

## Sécurité

- Sanitization des entrées avec `htmlspecialchars()` et `strip_tags()`
- Utilisation de requêtes préparées PDO (protection contre SQL injection)
- Validation des données côté client et serveur

## Technologies utilisées

- **Backend:** PHP, PDO, MySQL
- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **Architecture:** MVC (Model-View-Controller)
- **API:** RESTful

## License

Projet académique - 2025
