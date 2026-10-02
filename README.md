# Architecture de la Banque - API Gateway

Ce projet est une architecture de micro-services pour une application bancaire, incluant une API Gateway développée en PHP.

## 🚀 Lancement du projet avec Docker

Suivez ces étapes pour démarrer l'environnement complet.

### 📋 Prérequis

*   [Docker](https://docs.docker.com/get-docker/)
*   [Docker Compose](https://docs.docker.com/compose/install/)

### 🛠️ Installation et démarrage

1.  **Démarrer les services :**
    Exécutez la commande suivante à la racine du projet pour construire les images et lancer les conteneurs en arrière-plan :

    ```bash
    docker-compose up -d --build
    ```

2.  **Vérifier que les services tournent :**

    ```bash
    docker ps
    ```

### 🔗 Accès aux services

| Service | URL / Port | Informations |
| :--- | :--- | :--- |
| **FRONT** | `http://localhost:3000/` | Front |
| **API Gateway** | `http://localhost:8080` | Point d'entrée principal |
| **phpMyAdmin** | `http://localhost:8081` | Pour l'administration de la BDD MySQL |

### 📂 Structure des services

*   **gateway-service/** : Code source et configuration de la Gateway.
*   **gateway-service/nginx/** : Configuration Nginx spécifique.
*   **auth-service/** : Micro-service d'authentification (Login, Register) générant des tokens JWT.
*   **compte-service/** : Micro-service de gestion des comptes en PHP (Symfony, Architecture Hexagonale).
*   **client-service/** : Micro-service de gestion des clients en PHP (Symfony, Architecture Hexagonale).
*   **transaction-service/** : Micro-service gérant les virements, en appelant le Service Compte via HTTP.
*   **db** : Base de données MySQL 8.0 pour la persistance des données.
*   **phpmyadmin** : Interface web d'administration de la base de données.

### ⚙️ Commandes utiles

*   **Arrêter les services :**
    ```bash
    docker-compose down
    ```

*   **Voir les logs en temps réel :**
    ```bash
    docker-compose logs -f
    ```

*   **Entrer dans le conteneur PHP :**
    ```bash
    docker exec -it gateway-php bash
    ```

*   **Recharger les dépendances Composer (si nécessaire) :**
    ```bash
    docker exec -it gateway-php composer install
    ```

*   **Lancer les tests (PHPUnit) :**
    ```bash
    docker exec auth-php vendor/bin/phpunit
    docker exec compte-php vendor/bin/phpunit
    docker exec client-php vendor/bin/phpunit
    docker exec transaction-php vendor/bin/phpunit
    ```

*   **Vérifier et formater le code (PHPStan & PHP-CS-Fixer) :**
    ```bash
    # Service Auth
    docker exec auth-php vendor/bin/phpstan analyse src/ --level=6
    docker exec auth-php vendor/bin/php-cs-fixer fix

    # Service Compte
    docker exec compte-php vendor/bin/phpstan analyse src/ --level=6
    docker exec compte-php vendor/bin/php-cs-fixer fix src/ tests/

    # Service Client
    docker exec client-php vendor/bin/phpstan analyse src/ --level=6
    docker exec client-php vendor/bin/php-cs-fixer fix

    # Service Transaction
    docker exec transaction-php vendor/bin/phpstan analyse src/ --level=6 --memory-limit=512M
    docker exec transaction-php vendor/bin/php-cs-fixer fix
    ```

---
*Dylan BAUDSON, Grégory WOLFF, Guillaume CROUZET*
