<?php 
declare(strict_types=1); 

namespace App; 

final class Environnement {

    public static function statutPhp (string $version) : string {
        if (version_compare($version, '8.5.12', '>=')) {
            return "corrigée";
        }

        elseif (version_compare($version, '8.4.27', '>=') && version_compare($version, '8.5.0', '<')) {
            return "corrigée";
        }

        elseif (version_compare($version, '8.3.36', '>=') && version_compare($version, '8.4.0', '<')) {
            return "corrigée"; 
        }

        elseif (version_compare($version, '8.2.35', '>=') && version_compare($version, '8.3.0', '<')) {
            return "corrigée"; 
        }

        elseif(version_compare($version, '8.2.0', '<=')) {
            return "fin de vie"; 
        }
        
        else {
            return "vulnérable"; 
        }
    }
}

