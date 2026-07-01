<?php

namespace App\Tests\Domain;

use App\Domain\Entity\Compte;
use App\Domain\Exception\CompteBloqueException;
use App\Domain\Exception\MontantInvalideException;
use App\Domain\Exception\SoldeInsuffisantException;
use PHPUnit\Framework\TestCase;

class CompteTest extends TestCase
{
    /**
     * @throws MontantInvalideException
     * @throws CompteBloqueException
     */
    public function testDeposerArgentAugmenteLeSolde(): void
    {
        $compte = new Compte('cpt_1', 'client_1', 100.0);

        $compte->deposer(50.0);

        $this->assertEquals(150.0, $compte->getSolde());
    }

    /**
     * @throws MontantInvalideException
     * @throws CompteBloqueException
     */
    public function testDeposerMontantNegatifLeveException(): void
    {
        $compte = new Compte('cpt_1', 'client_1', 100.0);

        $this->expectException(MontantInvalideException::class);

        $compte->deposer(-10.0);
    }

    /**
     * @throws SoldeInsuffisantException
     * @throws MontantInvalideException
     * @throws CompteBloqueException
     */
    public function testRetirerArgentDiminueLeSolde(): void
    {
        $compte = new Compte('cpt_1', 'client_1', 100.0);

        $compte->retirer(50.0);

        $this->assertEquals(50.0, $compte->getSolde());
    }

    /**
     * @throws MontantInvalideException
     * @throws CompteBloqueException
     */
    public function testRetirerPlusQueLeSoldeLeveException(): void
    {
        $compte = new Compte('cpt_1', 'client_1', 100.0);

        $this->expectException(SoldeInsuffisantException::class);

        $compte->retirer(150.0);
    }

    /**
     * @throws SoldeInsuffisantException
     * @throws MontantInvalideException
     */
    public function testOperationSurCompteBloqueLeveException(): void
    {
        $compte = new Compte('cpt_1', 'client_1', 100.0);
        $compte->bloquer();

        $this->expectException(CompteBloqueException::class);

        $compte->retirer(10.0);
    }
}
