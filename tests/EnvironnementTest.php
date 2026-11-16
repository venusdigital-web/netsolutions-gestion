<?php 

declare(strict_types = 1); 
namespace App;

use App\Environnement; 
use PHPUnit\Framework\Attributes\DataProvider;

use PHPUnit\Framework\TestCase; 

final class EnvironnementTest extends TestCase {
    public static function versions(): array {
        return [
            ['8.5.12', 'corrigée'], 
            ['8.4.27', 'corrigée'], 
            ['8.3.36', 'corrigée'], 
            ['8.2.34', 'vulnérable'], 
            ['8.1.34', 'fin de vie'], 
            ['7.4.33', 'fin de vie']
        ];
    }

    #[DataProvider('versions')]
    public function testStatut(string $version, string $attendu):void {
        $this->assertSame($attendu, Environnement::statutPhp($version)); 
    }
}
