<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\In\Web;

use App\Application\UseCase\ConsulterTransactionUseCase;
use App\Application\UseCase\EffectuerVirementUseCase;
use App\Application\UseCase\ListerTransactionsUseCase;
use App\Infrastructure\DTO\TransactionResponseDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TransactionController extends AbstractController
{
    public function __construct(
        private readonly EffectuerVirementUseCase $effectuerVirementUseCase,
        private readonly ConsulterTransactionUseCase $consulterTransactionUseCase,
        private readonly ListerTransactionsUseCase $listerTransactionsUseCase,
        private readonly ValidatorInterface $validator,
    ) {
    }

    #[Route('/transactions', methods: ['GET'])]
    public function lister(): JsonResponse
    {
        $transactions = $this->listerTransactionsUseCase->execute();

        return new JsonResponse(array_map(
            fn ($t) => TransactionResponseDTO::fromEntity($t),
            $transactions
        ));
    }

    #[Route('/transactions', methods: ['POST'])]
    public function effectuerVirement(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];

        $constraint = new Assert\Collection([
            'compteSourceId' => [new Assert\NotBlank(), new Assert\Type('string')],
            'compteDestinationId' => [new Assert\NotBlank(), new Assert\Type('string')],
            'montant' => [new Assert\NotBlank(), new Assert\Type('numeric'), new Assert\Positive()],
        ]);

        $violations = $this->validator->validate($data, $constraint);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            return new JsonResponse(['error' => 'Validation failed', 'details' => $errors], 400);
        }

        $transaction = $this->effectuerVirementUseCase->execute(
            $data['compteSourceId'],
            $data['compteDestinationId'],
            (float) $data['montant']
        );

        return new JsonResponse(TransactionResponseDTO::fromEntity($transaction), 201);
    }

    #[Route('/transactions/{id}', methods: ['GET'])]
    public function consulterTransaction(string $id): JsonResponse
    {
        $transaction = $this->consulterTransactionUseCase->execute($id);

        return new JsonResponse(TransactionResponseDTO::fromEntity($transaction), 200);
    }
}
