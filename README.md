# VITEGourmand

Application web de commande de menus événementtiels pour << Vite & Gourmand >> (ECF Titre Professionel Développeur Web et Web Mobile).

## Stack technique

- Back-end : PHP 8.4 (architecture MVC, PDO) — compatible PHP 8.2+
- Base de données relationnelle : MariaDB 11.4 (compatible MySQL)
- Base de donnés non relationnelle : MongoDB Atlas (avis clients, satistique de commande)
- Envoi d'emails : PHPMailer + Mailtrap (environnement de test)
- Front-end : HTML5, CSS (BOOTSTRAP), JavaScript
- Déploiment : Heroku

## Prérequis

- XAMPP (Apache + MySQL + PHP 8.2.12 ou supérieur)
- [Composer](https://getcomposer.org/)
- Un compte [Mailtrap](https://mailtrap.io/) (sandbox gratuit, pour tester l'envoi d'emails)
- Un compte [MongoDB Atlas](https://www.mongodb.com/atlas) (cluster gratuit, pour les avis clients et les statistiques de commande)
- Extensions PHP requises : curl, mongodb, pdo_mysql, mbstring.
- Conteneurisation : Docker + Docker Compose (environnement de développement)

Note : Cette application a été développée en utilisant une base de données MongoDB Atlas (Cloud). Si vous optez pour un environnement local sous XAMPP, l'extension mongodb n'étant pas incluse par défaut, vous devrez télécharger le fichier php_mongodb.dll correspondant à votre version de PHP et l'activer dans votre fichier php.ini.

## Installation en local

## Installation avec Docker (recommandé)

Docker permet de lancer l'application avec un environnement identique sur toutes les machines, sans installer PHP, Apache ni MySQL : tout est fourni par des conteneurs.

### Prérequis

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (sous Windows : WSL 2 à jour, via `wsl --update`)
- Un compte Mailtrap et un compte MongoDB Atlas (voir ci-dessus)

### Architecture

| Service | Image | Rôle | Accès depuis le PC |
|---|---|---|---|
| `web` | construite depuis le `Dockerfile` (PHP 8.4 + Apache) | application | http://localhost:8080 |
| `db` | `mariadb:11.4` | base de données | `127.0.0.1:3307` (HeidiSQL, etc.) |

MongoDB Atlas et Mailtrap restent des services externes (cloud).

### Lancement

1. Cloner le dépôt :
   ```
   git clone https://github.com/NKO971/VITEGourmand.git
   cd VITEGourmand
   ```

2. Copier `.env.example` vers `.env` et renseigner les identifiants Mailtrap, MongoDB Atlas et ceux de la base de données (`DB_NAME`, `DB_USER`, `DB_PASS`, `DB_ROOT_PASS`).
   ⚠️ Ne pas utiliser les caractères `$` et `#` dans les mots de passe : ils sont interprétés par Docker Compose.

3. Construire et démarrer les conteneurs :
   ```
   docker compose up --build -d
   ```
   Au premier démarrage, la base est créée automatiquement et les scripts `database/schema.sql` puis `database/fixtures.sql` sont importés.

4. Accéder à l'application : http://localhost:8080

### Commandes utiles

| Commande | Rôle |
|---|---|
| `docker compose ps` | état des conteneurs |
| `docker compose logs -f web` | logs Apache/PHP en direct |
| `docker compose down` | arrêter et supprimer les conteneurs (les données sont conservées dans le volume) |
| `docker compose down -v` | idem **+ suppression de la base** (réinitialisation complète au prochain `up`) |
| `docker compose up --build -d` | reconstruire l'image après une modification du code |

### Connexion à la base avec un client SQL (HeidiSQL, DBeaver…)

Hôte `127.0.0.1`, port `3307`, utilisateur et mot de passe = `DB_USER` / `DB_PASS` du fichier `.env`.
Le port 3307 évite le conflit avec un MySQL local (XAMPP) sur le port 3306.

## Installation en local avec XAMPP (alternative)

1. Cloner le dépôt dans le dossier 'htdocs' de XAMPP :
   git clone https://github.com/NKO971/VITEGourmand.git

2. Installer les dépendances PHP : composer install

3. Créer la base de données MySQL et importer le schéma :
   (Les commandes ci-dessous supposent `mysql` accessible en ligne de commande et un `root` sans mot de passe, config XAMPP par défaut — à adapter selon votre installation, ou à faire via phpMyAdmin.)

Créer la base et importer le schéma (nécessite des droits de structure, donc `root`) :

```
mysql -u root -e "CREATE DATABASE vitegourmand;"
mysql -u root vitegourmand < database/schema.sql
mysql -u root vitegourmand < database/fixtures.sql
```

Copier `app/config/constants.local.php.example` vers `app/config/constants.local.php` et y renseigner vos identifiants MySQL :

```php
   <?php
   define('DB_USER', 'root');
   define('DB_PASS', '');
```

Ce fichier est ignoré par Git — s'il est absent, l'application refuse de démarrer en local avec un message explicite.


Pour des raisons de sécurité, le projet utilise en interne un utilisateur MySQL dédié à privilèges limités (`SELECT, INSERT, UPDATE, DELETE` uniquement) plutôt que `root` — voir la documentation technique pour le détail et la justification de ce choix.


```php
      <?php
      define('DB_USER', 'vitegourmand_user');
      define('DB_PASS', 'votre_mot_de_passe');
```

Note :  Sans ce fichier la connexion est impossible

4. Copier le contenu de `.env.example` dans `.env` et renseigner vos identifiants Mailtrap et MongoDB Atlas.
   Ces identifiants sont nécessaires au bon fonctionnement de l'envoi d'emails et des avis clients.

```env
MAILTRAP_USERNAME=
MAILTRAP_PASSWORD=
MAILTRAP_HOST=
MAILTRAP_PORT=

MONGO_USERNAME=
MONGO_PASSWORD=
MONGO_CLUSTER=
MONGO_APPNAME=
MONGO_DB_NAME=

# Uniquement pour Docker
DB_NAME=
DB_USER=
DB_PASS=
DB_ROOT_PASS=
```

5. Démarrer Apache et MySQL depuis le panneau de contrôle XAMPP.

6. Accéder à l'application : http://localhost/VITEGourmand/public/

## Comptes de test

Voir le manuel d'utilisation fourni avec la livraison ECF.
