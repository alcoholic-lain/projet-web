#  Innovation  -- Plateforme de Gestion des Categories/Innovations (TunisSpace)

Plateforme web futuriste permettant aux utilisateurs de consulter des
innovations liées à l'espace, et offrant un BackOffice administrateur
pour gérer les catégories et innovations.

🎓 *Projet académique -- Cycle Ingénieur*\
👤 *Développé par : Hichem Challakhi & Team*\
⚙️ *Technos : PHP -- MySQL -- HTML -- CSS -- JavaScript*\
📁 *Architecture MVC simplifiée*

------------------------------------------------------------------------

## 📂 Structure complète du projet

Voici l'arborescence complète du projet :

    projet-web/
    │
    ├── test.php
    │
    ├── .idea/
    │
    ├── controller/
    │   └── components/
    │       ├── CategoryController.php
    │       ├── InnovationController.php
    │
    ├── data set/
    │   └── user_data.csv
    │
    ├── model/
    │   ├── config/
    │   │   └── data-base.php
    │   │
    │   └── Innovation/
    │       ├── Category.php
    │       ├── Commentaire.php
    │       ├── Innovation.php
    │       ├── PieceJointe.php
    │       ├── Vote.php
    │       └── schema.sql
    │
    ├── veiw/
    │   ├── Admin/
    │   │   ├── add_Category.html
    │   │   ├── a_Category.html
    │   │   ├── a_Innovation.html
    │   │   ├── edit_Category.html
    │   │   ├── edit_Innovation.html
    │   │   └── index.html
    │   │
    │   └── Client/
    │       ├── assets/
    │       │   ├── css/
    │       │   │   ├── 1.css
    │       │   │   ├── admin.css
    │       │   │   ├── style_i_list.css
    │       │   │   └── user.css
    │       │   │
    │       │   ├── js/
    │       │   │   ├── category.js
    │       │   │   ├── category_details.js
    │       │   │   ├── dashboard.js
    │       │   │   └── innovation.js
    │       │   │
    │       │   └── video/
    │       │       └── space.mp4
    │       │
    │       └── src/
    │           ├── add_Innovation.html
    │           ├── categories.html
    │           ├── category_details.html
    │           ├── details_Innovation.html
    │           ├── index.html
    │           └── list_Innovation.html

------------------------------------------------------------------------

## ⭐ Fonctionnalités principales

### 👨‍🚀 Front Office

-   Affichage des catégories\
-   Page détails catégorie\
-   Liste des innovations\
-   Page détail innovation\
-   UI thème spatial futuriste

### 🛠️ Back Office

-   Dashboard administrateur\
-   CRUD complet catégories\
-   CRUD complet innovations\
-   Graphique (répartition des innovations par catégorie)

------------------------------------------------------------------------

## 🗄️ Base de données

Le projet utilise une base **MySQL**.\
Le script SQL est disponible dans :

    model/Innovation/schema.sql
------------------------------------------------------------------------

### 👥 Équipe
    
 *HICHEM CHALLAKHI*\
 *MOHAMED TAER AYARI*\
 *ZAKARIA BEN OUIRANE*\
 *AHMED RIDHA LAZHARI*\
 *NADHEM SAIDANI*\
 *AHMED ALLANI*
------------------------------------------------------------------------
# 🌌 Merci d'utiliser TUNISPACE !
