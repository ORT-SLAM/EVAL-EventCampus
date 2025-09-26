<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ApiController extends AbstractController
{
    #[Route('/api/events', name: 'api_events')]
    public function events(Request $request): JsonResponse {
        $filteredEvents = [];
        $cat = $request->query->get('categorie');
        $access = $request->query->get('acces');
        $acceptedCat = ['culturel', 'festif', 'associatif', 'sportif'];

        if (in_array($cat, $acceptedCat)) {
            foreach (HomeController::getEvents() as $event) {
                if ($event['categorie'] === $cat) {
                    if ($access === "gratuit") {
                        if ($event['prix'] == 0) {
                            $filteredEvents[] = $event;
                        }
                    }
                    else if ($access === "payant") {
                        if ($event['prix'] >= 1) {
                            $filteredEvents[] = $event;
                        }
                    }
                    else {
                        $filteredEvents[] = $event;
                    }
                }
            }
        } else {
            return new JsonResponse(['error' => "categorie not found"]);
        }

        if ($filteredEvents) {
            return new JsonResponse($filteredEvents);
        }

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
