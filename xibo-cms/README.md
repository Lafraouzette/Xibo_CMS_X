# Xibo CMS

Bienvenue dans le projet Xibo CMS. Ce fichier README vous fournira des informations de base pour vous aider à démarrer avec la modification de Xibo CMS.

## Prérequis

Avant de commencer, assurez-vous d'avoir les éléments suivants installés sur votre machine :

- PHP >= 7.2
- MySQL >= 5.6
- Apache ou Nginx
- Composer

## Installation

1. Clonez le dépôt du projet :
    ```bash
    git clone https://github.com/xibosignage/xibo-cms.git
    ```

2. Accédez au répertoire du projet :
    ```bash
    cd xibo-cms
    ```

3. Installez les dépendances avec Composer :
    ```bash
    composer install
    ```

4. Configurez votre environnement en copiant le fichier `.env.example` en `.env` et en modifiant les paramètres nécessaires.

5. Exécutez les migrations de base de données :
    ```bash
    php artisan migrate
    ```

## Démarrage

Pour démarrer le serveur de développement, exécutez la commande suivante :
```bash
php artisan serve
```

Accédez ensuite à l'application via `http://localhost:8000`.

## Contribution

Les contributions sont les bienvenues ! Veuillez lire le fichier `CONTRIBUTING.md` pour plus de détails sur le processus de contribution.

## Licence

Ce projet est sous licence [MIT](LICENSE).

## Contact

Pour toute question ou assistance, veuillez ouvrir une issue sur le dépôt GitHub.

Merci d'utiliser Xibo CMS !