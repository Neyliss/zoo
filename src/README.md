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

Vérifier que tous les composant sont installés et que votre projet symfony peut être lancé : 
symfony check:requirements

Créer un fichier .env.local et configuré l'accés à la base de données MySQL :
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"

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
