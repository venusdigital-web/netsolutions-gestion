<?php
declare(strict_types=1);

namespace Tests;

use App\Vue;
use PHPUnit\Framework\TestCase;

/** Exemple de test : une méthode par cas, un nom qui dit ce qui est vérifié. */
final class VueTest extends TestCase
{
    public function testEchappeLesBalises(): void
    {
        $this->assertSame('&lt;script&gt;alert(1)&lt;/script&gt;', Vue::e('<script>alert(1)</script>'));
    }

    public function testEchappeLesGuillemets(): void
    {
        $this->assertSame('Le &quot;Petit&quot; Fournil', Vue::e('Le "Petit" Fournil'));
    }

    public function testAccepteNull(): void
    {
        $this->assertSame('', Vue::e(null));
    }
}
