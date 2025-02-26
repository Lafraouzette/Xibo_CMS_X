# Guide Xibo CMS Docker

## **comment lancer le CMS**
- Vous aurez besoin de deux fichiers principaux :
  1. `docker-compose.yaml` : Configuration pour démarrer les conteneurs.
  2. `config.env` : Fichier de configuration des variables d'environnement (à créer à partir de `config.env.template`).
  3. Lancer le fichier docker-compose.yml : ```sh 
  docker-compose -f C:\Users\USER\Desktop\Xibo_X\xibo-docker\docker-compose.yml up --build 
  ```
  4. xibo_admin et mdp : password 

## **Les services**
- Consulte le fichier docker-compose.yml pour voir tous les services 
- pricipqlement nous avons besoin de : 
1. Base des donnees : MYSQL 
2. XMR
3. Serveur CMS 

