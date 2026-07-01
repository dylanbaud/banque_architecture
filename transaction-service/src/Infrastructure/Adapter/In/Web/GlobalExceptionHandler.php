<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\In\Web;

use App\Domain\Exception\CompteServiceException;
use App\Domain\Exception\MontantInvalideException;
use App\Domain\Exception\TransactionInexistanteException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class GlobalExceptionHandler implements EventSubscriberInterface
{
    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $statusCode = 500;
        $message = 'Erreur interne du serveur';

        if ($exception instanceof TransactionInexistanteException) {
            $statusCode = 404;
            $message = $exception->getMessage();
        } elseif ($exception instanceof MontantInvalideException) {
            $statusCode = 400;
            $message = $exception->getMessage();
        } elseif ($exception instanceof CompteServiceException) {
            $statusCode = 400; // Ou 409 selon le cas, on reste sur 400 pour simplifier
            $message = $exception->getMessage();
        }

        if (500 !== $statusCode) {
            $response = new JsonResponse([
                'error' => $message,
                'code' => $statusCode,
            ], $statusCode);

            $event->setResponse($response);
        }
    }
}
