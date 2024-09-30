# ZOO
Réalisitation de mon projet


# Installation de l'environnement de travail : 
installer HOMEBREW via la commande : 
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Modifier la configuration du serveur apache sur Mac pour qu'il pointe vers votre dossier :

Installer Apache à l'aide de la commande suivante depuis le terminal  : 
brew install httpd

Ensuite ouvrer votre fichier 'httpd.conf' à l'aide de commande suivante :
sudo nano /opt/homebrew/etc/httpd/httpd.conf 

modifier les ligne :
DocumentRoot "/opt/homebrew/var/www" => DocumentRoot "/Users/your_user/Sites"
Directory "/Users/your_user/Sites"
#ServerName www.example.com:8080 => ServerName localhost

 AllowOverride controls what directives may be placed in .htaccess files.
 It can be "All", "None", or any combination of the keywords:
  AllowOverride FileInfo AuthConfig Limit

AllowOverride All
Retirer le diez la ligne :
LoadModule rewrite_module lib/httpd/modules/mod_rewrite.so


Verifier que votre serveur Apache fonctionne correctement : 
brew services list




Ensuite install composer :
Brew install composer 

installer Symfony CLI : 
brew install symfony-cli/tap/symfony-cli

Initialisation du projet :
composer create-project symfony/skeleton:"^5.4" nom_du_projet

Installer toute les dépendances une après  l'autre :
composer require symfony/security-bundle
composer require symfony/mailer
composer require symfony/http-client
composer require symfony/serializer
composer require symfony/validator
composer require api  
composer require api-platform/core  
composer require symfony/serializer-pacK 
composer require nelmio/api-doc-bundle  
composer require twig asset         

Vérifier que tous les composant sont installés et que votre projet symfony peut être lancé : 
symfony check:requirements
# Installer PostgreSQL sur MacOs  :

Je travaillais initialement sur MySQL mais des roblème de compatibilité sont survenu je suis obligé de modifier tout mon code back end en utilisant POSTGRESQL :

- installation depuis le Terminal  avec la commande :
brew install postgresql
- Démarrez le service à l'aide de la commande :
  brew services start postgresql
- Vérifier que le service fonctionne correctement :
  brew services list 


Créer un fichier .env.local et configuré l'accés à la base de données PostgreSQL :
DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5433/app?serverVersion=16&charset=utf8"

# Installer Mongodb sur MacOs  :

brew tap mongodb/brew
brew install mongodb-community@6.0

Démmarer Mongodb : 
brew services start mongodb/brew/mongodb-community

Installer l'extension Mongodb pour php :
pecl install mongodb

Installez le package alcaeus/mongo-php-adapter pour utiliser MongoDB avec Symfony :
composer require alcaeus/mongo-php-adapter

Configurez MongoDB dans votre fichier .env.local :
MONGODB_URL="mongodb://localhost:27017"

# Envoyé les données du fichier database.sql vers la BDD :
Envoyé les données du code SQL vers la BDD taper la commande suivante dans le terminal: 
mysql -u nom_utilisateur -p nom_de_la_base_de_données < setup.sql

# Installer le projet en local : 
faire la commande suivante dans le terminal afin d'installer le projet en local  :
git clone https://github.com/Neyliss/zoo.git

Lancer le projet en local :
symfony server:start   

# ATTENTION MON PROJET EST EN COURS DE MODIFICATION SUITE A DES PROBLEMES DE COMPATIBILITE SURVENU APRES UNE MAJ IOS RETRAIT DU SITE DEPLOYER TEMPORAIREMENT 