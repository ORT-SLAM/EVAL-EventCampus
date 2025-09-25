<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
  #[Route('/', name: 'app_home')]
  public function index(): Response
  {
    $ongoingEvents = $this->getOngoingEvents();
    $upcomingEvents = $this->getUpcomingEvents();

    return $this->render('home/index.html.twig', [
      'pageName' => 'EC - Accueil',
      'ongoingEvents' => $ongoingEvents,
      'upcomingEvents' => $upcomingEvents,
    ]);
  }

  #[Route('/stats', name: 'app_stats')]
  public function stats(): Response
  {
    $nbEvents = count($this->getEvents());
    $nbOpenEvents = count($this->getOpenEvents());
    $nbFestifEvents = $this->getNbEventsByCategories("festif");
    $nbAssociatifEvents = $this->getNbEventsByCategories("associatif");
    $nbCultureEvents = $this->getNbEventsByCategories("culturel");
    $nbSportEvents = $this->getNbEventsByCategories("sportif");

    return $this->render('statistiques/stats.html.twig', [
      'pageName' => 'EC - Statistiques',
      'nbEvents' => $nbEvents,
      'nbOpenEvents' => $nbOpenEvents,
      'nbFestifEvents' => $nbFestifEvents,
      'nbAssociatifEvents' => $nbAssociatifEvents,
      'nbCultureEvents' => $nbCultureEvents,
      'nbSportEvents' => $nbSportEvents,
    ]);
  }

  private function getNbEventsByCategories(string $category): int
  {
    $events = array_values(array_filter(HomeController::getEvents(), function ($item) use ($category) {
      return $item['categorie'] === $category;
    }));

    return count($events);
  }

  private function getOpenEvents(): array
  {
    $openEvents = [];
    foreach ($this->getEvents() as $event) {
      if ($event['statut'] === "ouvert") {
        $openEvents[] = $event;
      }
    }
    return $openEvents;
  }

  private function getOngoingEvents(): array
  {
    $now = new \DateTimeImmutable('now');
    $events = array_values(array_filter(self::getEvents(), function ($event) use ($now) {
      $start = new \DateTimeImmutable($event['date_debut']);
      $end = new \DateTimeImmutable($event['date_fin']);
      return $start <= $now && $now <= $end;
    }));

    usort($events, function ($a, $b) {
      return strcmp($a['date_debut'], $b['date_debut']);
    });

    return $events;
  }

  private function getUpcomingEvents(): array
  {
    $now = new \DateTimeImmutable('now');
    $events = array_values(array_filter($this->getEvents(), function ($event) use ($now) {
      $start = new \DateTimeImmutable($event['date_debut']);
      return $start > $now;
    }));

    usort($events, function ($a, $b) {
      return strcmp($a['date_debut'], $b['date_debut']);
    });

    return $events;
  }

  public static function getEvents(): array {
    return [
      1 => [
        'id' => 1,
        'titre' => 'Soirée Étudiante Halloween',
        'description' => 'Grande soirée costumée pour célébrer Halloween au campus !',
        'date_debut' => '2025-10-31 20:00:00',
        'date_fin' => '2025-11-01 02:00:00',
        'lieu' => 'Amphithéâtre Central',
        'categorie' => 'festif',
        'organisateur' => 'BDE Campus',
        'prix' => 8.0,
        'places_disponibles' => 150,
        'places_totales' => 200,
        'image' => 'halloween.jpg',
        'statut' => 'ouvert'
      ],
      2 => [
        'id' => 2,
        'titre' => 'Tournoi de Football Interpromotions',
        'description' => 'Compétition amicale entre les différentes promotions du campus.',
        'date_debut' => '2025-09-25 14:00:00',
        'date_fin' => '2025-09-25 18:00:00',
        'lieu' => 'Stade Universitaire',
        'categorie' => 'sportif',
        'organisateur' => 'Association Sportive',
        'prix' => 0.0,
        'places_disponibles' => 300,
        'places_totales' => 300,
        'image' => 'football.jpg',
        'statut' => 'ouvert'
      ],
      3 => [
        'id' => 3,
        'titre' => 'Conférence IA et Éthique',
        'description' => 'Intervention d’experts sur les enjeux éthiques de l’intelligence artificielle.',
        'date_debut' => '2025-11-12 10:00:00',
        'date_fin' => '2025-11-12 12:00:00',
        'lieu' => 'Salle de Conférence B',
        'categorie' => 'culturel',
        'organisateur' => 'Département Informatique',
        'prix' => 5.0,
        'places_disponibles' => 80,
        'places_totales' => 100,
        'image' => 'conference_ia.jpg',
        'statut' => 'ouvert'
      ],
      4 => [
        'id' => 4,
        'titre' => 'Atelier Cuisine du Monde',
        'description' => 'Venez apprendre à cuisiner des plats venus d’Asie et d’Amérique Latine.',
        'date_debut' => '2025-09-25 17:00:00',
        'date_fin' => '2025-09-25 20:00:00',
        'lieu' => 'Cafétéria Campus',
        'categorie' => 'associatif',
        'organisateur' => 'Club Gastronomie',
        'prix' => 12.0,
        'places_disponibles' => 20,
        'places_totales' => 25,
        'image' => 'atelier_cuisine.jpg',
        'statut' => 'ouvert'
      ],
      5 => [
        'id' => 5,
        'titre' => 'Concert Étudiant Rock & Pop',
        'description' => 'Groupes étudiants sur scène pour une soirée musicale inoubliable.',
        'date_debut' => '2025-12-05 19:30:00',
        'date_fin' => '2025-12-05 23:30:00',
        'lieu' => 'Salle Polyvalente',
        'categorie' => 'festif',
        'organisateur' => 'Club Musique',
        'prix' => 6.0,
        'places_disponibles' => 120,
        'places_totales' => 150,
        'image' => 'concert.jpg',
        'statut' => 'ouvert'
      ],
      6 => [
        'id' => 6,
        'titre' => 'Projection Cinéma : Inception',
        'description' => 'Séance cinéma étudiante avec pop-corn offert.',
        'date_debut' => '2025-09-28 20:00:00',
        'date_fin' => '2025-09-28 23:00:00',
        'lieu' => 'Amphi 2',
        'categorie' => 'culturel',
        'organisateur' => 'Ciné-Club',
        'prix' => 3.5,
        'places_disponibles' => 60,
        'places_totales' => 80,
        'image' => 'cinema.jpg',
        'statut' => 'ouvert'
      ],
      7 => [
        'id' => 7,
        'titre' => 'Hackathon 24h',
        'description' => 'Compétition de programmation en équipe sur 24 heures.',
        'date_debut' => '2025-09-25 18:00:00',
        'date_fin' => '2025-09-26 19:00:00',
        'lieu' => 'Salle Informatique',
        'categorie' => 'culturel',
        'organisateur' => 'Club Développeurs',
        'prix' => 0.0,
        'places_disponibles' => 40,
        'places_totales' => 40,
        'image' => 'hackathon.jpg',
        'statut' => 'fermer'
      ],
      8 => [
        'id' => 8,
        'titre' => 'Marché de Noël Étudiant',
        'description' => 'Stands d’associations et d’artisans étudiants pour préparer les fêtes.',
        'date_debut' => '2025-12-15 10:00:00',
        'date_fin' => '2025-12-15 18:00:00',
        'lieu' => 'Cour du Campus',
        'categorie' => 'associatif',
        'organisateur' => 'BDE & Associations',
        'prix' => 0.0,
        'places_disponibles' => 500,
        'places_totales' => 500,
        'image' => 'marche_noel.jpg',
        'statut' => 'fermer'
      ],
    ];
  }
}
