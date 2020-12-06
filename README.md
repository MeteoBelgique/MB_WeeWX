MB_WeeWX
========

## Introduction
MB_WeeWX est un script php pour envoyer ses données météo vers MeteoBelgique à partir du programme WeeWX.
Le code repose sur une installation WeeWX MySQL et un transfert des données par FTP.
Pour participer au réseau, visitez https://www.meteobelgique.be

## Pré-requis
- Une installation WeeWX fonctionnelle
- Une base de données de type MySQL
- Les identifiants de connexion au serveur FTP, plus d'infos via https://www.meteobelgique.be

## Installation
### Installation de git et php
Git est nécessaire pour récupérer les fichiers depuis GitHub.
php est le langage utilisé pour ce script.
```
sudo apt-get update
sudo apt-get install git
sudo apt-get install php-cli php-mysql
```

### Récupération du script
Placez-vous dans un répertoire. Par exemple /home/pi
```
cd /home/pi
```

Récupérez le script.
```
git clone https://github.com/MeteoBelgique/MB_WeeWX.git
```

### Configuration
Rendez-vous dans le répertoire du script
```
cd MB_WeeWX
```

Editez le fichier config.php
```
sudo nano config.php
```
**Paramètres base de données**

Les paramètres à donner sont des paramètres classiques pour une connexion à une base de données: serveur, nom d'utilisateur, mot de passe, nom de base de données, nom de la table.
Ces paramètres permettront de donner accès à la base de données gérée par WeeWX.
```
$config['sqlServer'] = 'localhost';
$config['sqlUser'] = 'username';
$config['sqlPassword'] = 'password';
$config['sqlDatabase'] = 'weewx';
$config['sqlTable'] = 'archive';
```

**Paramètres FTP**

Des paramètres sont à renseigner pour envoyer les données par FTP. Ils vous seront communiqués après avoir demandé votre intégration au réseau.
```
$config['ftpHost'] = 'hostname';
$config['ftpUser'] = 'username';
$config['ftpPassword'] = 'password';
```

**Paramètres divers**

Deux paramètres à donner ici:
- Le temps (intervalle en minutes) entre deux enregistrements par votre datalogger;
- Le nombre de jours à envoyer, par défaut 2. Cela peut être utile si la transmission s'est interrompue pendant quelques jours.
```
$config['interval'] = 5;
$config['days'] = 2;
```
### Test d'envoi
Pour tester le script vous pouvez simplement utiliser la commande suivante:
```
sudo php meteobelgique.php
```

Les erreurs devraient s'afficher. N'hésitez pas à nous contacter si vous ne savez pas les résoudre par vous-même.

### Automatisation
Vous pouvez facilement exécuter ce script automatiquement.
Ouvrez le gestionnaire des tâches cron avec la commande:
```
sudo crontab -e
```

Ajoutez la ligne suivante pour exécuter le script toutes les 10 minutes.
```
*/10 * * * * php /home/pi/MB_WeeWX/meteobelgique.php
```

Et voilà, les données sont envoyées toutes les 10 minutes, n'hésitez pas à nous contacter pour tout problème :-)
