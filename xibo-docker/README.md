### **Installation**
- Pour installer Xibo CMS avec Docker, suivez les instructions détaillées dans le **Manuel Xibo** : [Manuel Xibo](http://xibo.org.uk/manual-tempel/en/install_cms.html).
- Vous aurez besoin de deux fichiers principaux :
  1. `docker-compose.yaml` : Configuration pour démarrer les conteneurs.
  2. `config.env` : Fichier de configuration des variables d'environnement (à créer à partir de `config.env.template`).

---

### **Structure du répertoire**
Le projet est organisé comme suit :

#### **/containers**
- Contient les fichiers de configuration (**Dockerfile**) pour les conteneurs Docker de Xibo CMS et XMR (Xibo Message Relay).
- Ces conteneurs sont préconstruits et disponibles sur **Docker Hub** sous les noms `xibosignage/xibo-cms` et `xibosignage/xibo-xmr`.

#### **DATA_DIR/shared**
- Dossier contenant les données de l'installation Xibo :
  - **Bibliothèque média** : `/shared/cms/library`
  - **Base de données** : `/shared/db`
  - **Sauvegardes automatiques** : `/shared/backup` (sauvegardes quotidiennes de la base de données).
  - **Thèmes personnalisés** : `/shared/cms/web/theme/custom`
  - **Modules personnalisés** : `/shared/cms/custom`
  - **Scripts utilisateur** : `/shared/cms/web/userscripts` (pour héberger des fichiers PHP ou autres ressources externes).

---
xibo_admin et mdp : password 