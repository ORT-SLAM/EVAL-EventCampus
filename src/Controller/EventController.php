<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class EventController extends AbstractController
{

  #[Route('/events', name: 'app_events')]
  public function index(Request $request): Response
  {
    $allEvents = HomeController::getEvents();

    $categoryParam = strtolower(trim((string) $request->query->get('category', 'all')));

    if ($categoryParam === 'all' || $categoryParam === '') {
      $events = $allEvents;
    } else {
      $events = array_values(array_filter($allEvents, function ($item) use ($categoryParam) {
        return strtolower($item['categorie']) === $categoryParam;
      }));
    }

    return $this->render('event/index.html.twig', [
      'pageName' => 'EC - Liste des Evenements',
      'events' => $events,
      'category' => $categoryParam,
    ]);
  }

  #[Route('/event/{id}', name: 'app_event_detail', requirements: ['id' => '\d+'])]
  public function eventById(int $id): Response
  {
    $event = array_values(array_filter(HomeController::getEvents(), function ($item) use ($id) {
      return $item['id'] === $id;
    }))[0] ?? null;

    if (!$event) {
      throw $this->createNotFoundException("Event not found");
    }
    return $this->render('event/show.html.twig', [
      'event' => HomeController::getEvents()[$id],
    ]);
  }

  #[Route('/events/categorie/{category}', name: 'app_events_by_categorie', defaults: ['category' => 'all'])]
  public function eventsByCategorie(string $category = 'all'): Response
  {
    return $this->redirectToRoute('app_events', ['category' => $category]);
  }
}
