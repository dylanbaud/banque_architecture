<?php

namespace App\Infrastructure\Adapter\In\Web;

use App\Domain\Exception\CompteBloqueException;
use App\Domain\Exception\CompteInexistantException;
use App\Domain\Exception\MontantInvalideException;
use App\Domain\Exception\SoldeInsuffisantException;
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

        if ($exception instanceof SoldeInsuffisantException) {
            $statusCode = 400;
            $message = $exception->getMessage();
        } elseif ($exception instanceof MontantInvalideException) {
            $statusCode = 400;
            $message = $exception->getMessage();
        } elseif ($exception instanceof CompteInexistantException) {
            $statusCode = 404;
            $message = $exception->getMessage();
        } elseif ($exception instanceof CompteBloqueException) {
            $statusCode = 409;
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
