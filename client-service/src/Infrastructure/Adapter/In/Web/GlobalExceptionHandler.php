<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\In\Web;

use App\Domain\Exception\ClientInexistantException;
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

        if ($exception instanceof ClientInexistantException) {
            $statusCode = 404;
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
