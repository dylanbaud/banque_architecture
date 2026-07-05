<?php

namespace App\Infrastructure\Adapter\In\Web;

use App\Application\UseCase\ConsulterCompteUseCase;
use App\Application\UseCase\CreerCompteUseCase;
use App\Application\UseCase\DeposerArgentUseCase;
use App\Application\UseCase\ListerComptesUseCase;
use App\Application\UseCase\RetirerArgentUseCase;
use App\Domain\Exception\CompteBloqueException;
use App\Domain\Exception\CompteInexistantException;
use App\Domain\Exception\MontantInvalideException;
use App\Domain\Exception\SoldeInsuffisantException;
use App\Infrastructure\DTO\CompteResponseDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/accounts')]
class CompteController extends AbstractController
{
    public function __construct(
        private readonly ConsulterCompteUseCase $consulterCompteUseCase,
        private readonly CreerCompteUseCase $creerCompteUseCase,
        private readonly DeposerArgentUseCase $deposerArgentUseCase,
        private readonly ListerComptesUseCase $listerComptesUseCase,
        private readonly RetirerArgentUseCase $retirerArgentUseCase,
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function lister(): JsonResponse
    {
        $comptes = $this->listerComptesUseCase->execute();

        return $this->json(array_map(
            fn ($c) => CompteResponseDTO::fromEntity($c),
            $comptes
        ));
    }

    /**
     * @throws MontantInvalideException
     */
    #[Route('', methods: ['POST'])]
    public function creer(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $clientId = $data['clientId'] ?? '';
        $soldeInitial = $data['soldeInitial'] ?? 0.0;

        if (empty($clientId)) {
            return $this->json(['error' => 'clientId est requis'], 400);
        }

        $compte = $this->creerCompteUseCase->execute($clientId, (float) $soldeInitial);

        return $this->json(CompteResponseDTO::fromEntity($compte), 201);
    }

    /**
     * @throws CompteInexistantException
     */
    #[Route('/{id}', methods: ['GET'])]
    public function consulter(string $id): JsonResponse
    {
        $compte = $this->consulterCompteUseCase->execute($id);

        return $this->json(CompteResponseDTO::fromEntity($compte));
    }

    /**
     * @throws MontantInvalideException
     * @throws CompteInexistantException
     * @throws CompteBloqueException
     */
    #[Route('/{id}/deposit', methods: ['POST'])]
    public function deposer(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $montant = $data['montant'] ?? 0.0;

        $this->deposerArgentUseCase->execute($id, (float) $montant);

        $compte = $this->consulterCompteUseCase->execute($id);

        return $this->json(CompteResponseDTO::fromEntity($compte));
    }

    /**
     * @throws CompteInexistantException
     * @throws SoldeInsuffisantException
     * @throws MontantInvalideException
     * @throws CompteBloqueException
     */
    #[Route('/{id}/withdraw', methods: ['POST'])]
    public function retirer(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $montant = $data['montant'] ?? 0.0;

        $this->retirerArgentUseCase->execute($id, (float) $montant);

        $compte = $this->consulterCompteUseCase->execute($id);

        return $this->json(CompteResponseDTO::fromEntity($compte));
    }
}
