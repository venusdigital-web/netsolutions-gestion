<?php 

declare(strict_types=1); 

require '../src/Environnement.php'; 
use App\Environnement; 

$version= PHP_VERSION; 
$resultat = Environnement::statutPhp($version); 
echo var_dump($resultat); 
