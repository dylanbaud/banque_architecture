<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\In\Web;

use App\Application\UseCase\ConsulterClientUseCase;
use App\Application\UseCase\CreerClientUseCase;
use App\Infrastructure\DTO\ClientResponseDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ClientController extends AbstractController
{
    private CreerClientUseCase $creerClientUseCase;
    private ConsulterClientUseCase $consulterClientUseCase;
    private ValidatorInterface $validator;

    public function __construct(
        CreerClientUseCase $creerClientUseCase,
        ConsulterClientUseCase $consulterClientUseCase,
        ValidatorInterface $validator,
    ) {
        $this->creerClientUseCase = $creerClientUseCase;
        $this->consulterClientUseCase = $consulterClientUseCase;
        $this->validator = $validator;
    }

    #[Route('/clients', name: 'creer_client', methods: ['POST'])]
    public function creer(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];

        $constraints = new Assert\Collection([
            'nom' => new Assert\NotBlank(),
            'prenom' => new Assert\NotBlank(),
            'email' => [new Assert\NotBlank(), new Assert\Email()],
        ]);

        $violations = $this->validator->validate($data, $constraints);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            return $this->json(['errors' => $errors], 400);
        }

        $client = $this->creerClientUseCase->execute(
            (string) $data['nom'],
            (string) $data['prenom'],
            (string) $data['email']
        );

        return $this->json(ClientResponseDTO::fromEntity($client), 201);
    }

    #[Route('/clients/{id}', name: 'consulter_client', methods: ['GET'])]
    public function consulter(string $id): JsonResponse
    {
        $client = $this->consulterClientUseCase->execute($id);

        return $this->json(ClientResponseDTO::fromEntity($client));
    }
}
