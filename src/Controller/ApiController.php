<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class ApiController extends AbstractController
{
    #[Route('/api/events', name: 'api_events')]
    public function events(): JsonResponse{
        return new JsonResponse(HomeController::getEvents());
    }

    #[Route('/api/events/{id}', name: 'api_event_details', requirements: ['id'=>'\d+'])]
    public function eventById(int $id): JsonResponse {

        $event = array_values(array_filter(HomeController::getEvents(), function ($item) use ($id) {
            return $item['id'] === $id;
        }))[0] ?? null;

        if (!$event) {
            throw $this->createNotFoundException("Event not found");
        }

        return new JsonResponse($event);
    }
}
