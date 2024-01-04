<?php
//
// Script php pour l'envoi de données météo vers MeteoBelgique
// depuis le logiciel WeeWX avec base de données MySQL.
// Infos: https://github.com/MeteoBelgique/MB_WeeWX
//

//
// Configuration
//

// Type base de données
$config['dbType'] = "mysql"; //deux possibilités: mysql, sqlite

//Paramètres sqlite_array_query
$config['sqliteFile'] = "/var/lib/weewx/weewx.sdb";
$config['sqliteTable'] = "archive";

//Paramètres MySQL, pour se connecter à la base de données WeeWX
$config['sqlServer'] = 'localhost';
$config['sqlUser'] = 'username';
$config['sqlPassword'] = 'password';
$config['sqlDatabase'] = 'weewx';
$config['sqlTable'] = 'archive';

//Paramètre FTP, pour se connecter au serveur MeteoBelgique
$config['ftpHost'] = 'hostname';
$config['ftpUser'] = 'username';
$config['ftpPassword'] = 'password';

//Paramètres divers
$config['interval'] = 5; //Intervalle d'enregistrement datalogger
$config['days'] = 2; //Jours de données à envoyer
$config['getHiLo'] = false; //Mettre true si WeeWX est modifié pour recevoir les température min et max à chaque intervalle
?>
