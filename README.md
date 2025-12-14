# Tunispace - Guide Détaillé du Projet

## Description du Projet

**Tunispace** est une application web complète, conçue pour la gestion des comptes utilisateurs et administrateurs (USER), la communication en temps réel (COMS), le suivi des innovations (INOVATION), le fil d'actualité/communautaire (FEED), et le traitement structuré des réclamations (RECLAMATION).

### Objectifs

Ce README a pour but de :
* Comprendre l'importance d'un README bien structuré dans un projet GitHub.
* Rédiger un README clair, détaillé et engageant en suivant les bonnes pratiques.
* Fournir des instructions précises pour l'installation, la configuration et l'utilisation d'un projet.
* Encourager et faciliter la contribution externe grâce à une documentation bien définie.

***

## Table des Matières

- [Installation](#installation)
- [Utilisation](#utilisation)
- [Fonctionnalités Détaillées](#fonctionnalités-détaillées)
- [Contribution](#contribution)
- [Licence](#licence)

***

## Installation

Cette section décrit les étapes nécessaires pour installer et configurer le projet.

1.  **Clonez le repository :**
    ```bash
    git clone [https://github.com/alcoholic-lain/projet-web.git](https://github.com/alcoholic-lain/projet-web.git)
    cd projet-web
    ```

2.  **Configuration Environnement (Exemple WAMP/XAMPP) :**
    * Placez le dossier `projet-web` dans le répertoire de votre serveur local (`www` ou `htdocs`).
    * Démarrez Apache et MySQL depuis l'interface de WAMP/XAMPP.
    * Accédez au projet via `http://localhost/projet-web`.

3.  **[À Personnaliser] Installation des dépendances et base de données :**
    * **Dépendances Backend :** Exécutez la commande d'installation de dépendances de votre langage (ex: `composer install`).
    * **Dépendances Frontend :** Installez les paquets JS/CSS (ex: `npm install` puis `npm run dev/build`).
    * **Base de données :** Lancez les migrations (si applicable) et importez les données initiales.

***

## Utilisation

Le projet nécessite l'installation de **[Langage/Techno Principal, ex: PHP, Node.js]** et de **MySQL** pour fonctionner.

### Installation des Prérequis (Exemple avec PHP)

1.  Téléchargez PHP à partir du site officiel [PHP - Téléchargement](https://www.php.net/downloads.php).
2.  Installez PHP en suivant les instructions spécifiques à votre système d'exploitation.
3.  Vérifiez l'installation de PHP en exécutant la commande suivante dans votre terminal:
    ```bash
    php -v
    ```

### Fonctionnalités Détaillées

| Module | Rôle | Fonctionnalités Complètes |
| :--- | :--- | :--- |
| **USER** | Client | Se connecter, Passer le CAPTCHA, Recevoir des emails système, Consulter son espace, Respecter les restrictions (si banni ou limité). |
| **USER** | Admin | Gérer les comptes, Gérer les rôles, Bannir / débannir, Voir les logs, Contrôler tout le système. |
| **COMS** | Client | Basic CRUD OPS, Pop up UI, Conv search, User search in conv creation, RTC via ws\_server implementation, Real time typing indicator, Emoji picker message reaction support, Reply message. |
| **COMS** | Admin | Basic CRUD OPS, Dashboard UI, Message-conv links. |
| **INOVATION** | Client | Basic CRUD OPS, Chatbot, Search inno & category, Add innovation with attached file, My innovation(s), Comment innovation, Vote for innovation, Download attached file in innovation, Just see confirmed innovation. |
| **INOVATION** | Admin | Basic CRUD OPS, API send mail for confirmed or rejected innovation, Pagging liste of categories and innovations, Sort by of categories and innovations, Download excel or pdf for categories and innovations, Statistic on dasheboard. |
| **FEED** | Client | **Ceci est à définir** (pour Nadhem). |
| **FEED** | Admin | **Ceci est à définir** (pour Nadhem). |
| **RECLAMATION** | Client | Créer une nouvelle réclamation, Consulter la liste de ses réclamations, Voir le détail et le statut d’une réclamation, Ajouter un commentaire, Joindre des fichiers (preuves), Clôturer sa réclamation, Recevoir des notifications. |
| **RECLAMATION** | Admin/Med | Voir la liste de toutes les réclamations, Filtrer, Affecter à un agent/admin, Modifier le statut, Répondre (commentaires internes ou publics), Consulter l’historique complet, Supprimer (avec justification), Imprimer et télécharger en PDF et CSV. |

***

## Contribution

Nous vous remercions d'avance pour votre intérêt à contribuer à ce projet !

Si vous souhaitez ajouter des fonctionnalités, corriger des bugs, ou améliorer la documentation, veuillez suivre les étapes ci-dessous pour créer un **fork**, une nouvelle branche et soumettre une **pull request**.

### Comment contribuer ?

1.  **Fork le projet :** Allez sur la page GitHub du projet (`https://github.com/alcoholic-lain/projet-web.git`) et cliquez sur le bouton **Fork**.
2.  **Clonez votre fork :** Clonez le fork sur votre machine locale :
    ```bash
    git clone [https://github.com/votre-utilisateur/projet-web.git](https://github.com/votre-utilisateur/projet-web.git)
    cd projet-web
    ```
    *(Note: Remplacez `votre-utilisateur` par votre nom d'utilisateur GitHub.)*
3.  **[AJOUTER ICI]** Créer une nouvelle branche de travail.
4.  **À chaque modification, ajouter, valider et faire un push :**
    ```bash
    git add .
    git commit -m "Description de ma modification"
    git push origin main
    ```
5.  **Soumettre une Pull Request** sur le repository original.

***

## Licence

Ce projet est sous la licence **MIT**. Pour plus de détails, consultez le fichier [LICENSE](./LICENSE).

### Détails sur la licence MIT

La licence MIT est une licence de logiciel libre et très permissive. Elle permet la réutilisation, la modification, la distribution et la vente du logiciel, à condition de conserver les informations de copyright (l'avis de droit d'auteur).
