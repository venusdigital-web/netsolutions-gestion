<?php
// Administration : toutes les demandes de tous les clients. Réservé au support (rôle admin).
require __DIR__ . '/../../src/bootstrap.php';

use App\Auth;
use App\Demandes;
use App\Vue;
use App\Environnement; 

Auth::exigerAdmin();
$version= PHP_VERSION; 
echo Vue::e($version); 
$resultat=Environnement::statutPhp(PHP_VERSION); 
echo Vue::e($resultat); 
