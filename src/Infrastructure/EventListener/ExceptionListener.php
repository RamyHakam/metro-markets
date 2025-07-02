<?php

namespace App\Infrastructure\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

#[AsEventListener]
class ExceptionListener
{

    public function __invoke(ExceptionEvent $event): void
    {
        $e = $event->getThrowable();

        if ($e instanceof  HttpException &&  $e->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
            $response = new JsonResponse(
                ['error' => 'Unauthorized', 'message' => $e->getMessage()],
                Response::HTTP_UNAUTHORIZED
            );
            $event->setResponse($response);
        }
    }
}
