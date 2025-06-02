# Projet Ligue 1

## Description
Ligue 1 est un projet PHP permettant de gérer les données liées à la Ligue 1 de football. Le projet utilise une architecture MVC (Modèle-Vue-Contrôleur) pour garantir une meilleure organisation du code et faciliter les évolutions futures.

Le projet est conçu pour fonctionner sur un environnement local avec XAMPP et s'articule autour de plusieurs fonctionnalités, incluant des interfaces utilisateur, des systèmes de gestion de bases de données et une navigation dynamique grâce à un routeur personnalisé.

## Structure du projet
Voici une vue d'ensemble de la structure du projet :

```
Ligue1/
|-- avatars/          # Contient les avatars des utilisateurs
|-- config/           # Configuration du projet (fichiers de paramètres)
|-- Controleur/       # Contient les contrôleurs (logique d'application)
|-- img/              # Images statiques utilisées dans le projet
|-- logs/             # Journaux d'erreurs ou d'activité
|-- Models/           # Contient les modèles (gestion des données et interactions avec la base de données)
|-- nbproject/        # Fichiers de projet NetBeans (si applicable)
|-- style/            # Feuilles de style (CSS)
|-- Vues/             # Contient les fichiers de vues (interfaces utilisateur)
|-- .htaccess         # Configuration Apache pour la réécriture des URL
|-- check_email.php   # Validation des emails
|-- footer.php        # Pied de page commun
|-- index.php         # Point d'entrée principal du projet
|-- logout.php        # Gestion de la déconnexion
|-- menu.php          # Menu de navigation commun
|-- Router.php        # Gestion des routes du projet
|-- utils.php         # Fonctions utilitaires communes
```

## Fonctionnalités principales

- **Validation d'email** :
  Le fichier `check_email.php` permet de vérifier la validité des emails soumis par les utilisateurs.

- **Gestion des sessions** :
  Le fichier `logout.php` permet de déconnecter un utilisateur en toute sécurité.

- **Système de navigation** :
  Le fichier `Router.php` gère la répartition des requêtes vers les bons contrôleurs et actions.

- **Architecture MVC** :
  Le projet est divisé en trois grandes couches :
  - **Modèle** : Situé dans le dossier `Models`, il gère les interactions avec la base de données.
  - **Vue** : Situé dans le dossier `Vues`, il gère l'affichage des interfaces utilisateur.
  - **Contrôleur** : Situé dans le dossier `Controleur`, il gère la logique d'application et relie les modèles aux vues.

- **Gestion des journaux** :
  Le dossier `logs` permet de stocker les fichiers de logs pour le suivi des erreurs ou des événements.

## Prérequis

- **Serveur web** : Apache (inclus dans XAMPP)
- **PHP** : Version 7.4 ou supérieure
- **Base de données** : MySQL (inclus dans XAMPP)
- **Navigateurs** : Tout navigateur récent compatible avec les standards modernes (Chrome, Firefox, Edge, etc.)

## Installation

1. Clonez ce dépôt dans le répertoire `htdocs` de XAMPP :
   ```bash
   git clone https://github.com/votre-utilisateur/ligue1.git
   ```

2. Importez la base de données en utilisant le fichier SQL approprié fourni avec le projet :
   ```bash
   mysql -u root -p < createLigue1_v2.sql
   ```

3. Démarrez le serveur Apache et MySQL via XAMPP.

4. Accédez à l'application depuis votre navigateur à l'adresse suivante :
   ```
   http://localhost/ligue1
   ```

## Contribution

Les contributions sont les bienvenues ! Veuillez suivre les étapes suivantes :

1. Forkez ce dépôt.
2. Créez une branche pour votre fonctionnalité :
   ```bash
   git checkout -b nouvelle-fonctionnalite
   ```
3. Soumettez vos modifications via une Pull Request.

## Auteur
Projet réalisé par Lucas Burdier dans le cadre d'une application PHP pour la gestion de données sportives.

## Licence
Ce projet est sous licence [MIT](LICENSE).

