<?php

namespace App\Controller;

use App\Entity\Film;
use App\Repository\FilmRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiFilmController extends AbstractController
{
    private $filmRepository;
    private $em;
    private $serializer;

    public function __construct(FilmRepository $filmRepository, EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $this->filmRepository = $filmRepository;
        $this->em = $em;
        $this->serializer = $serializer;
    }


    #[Route('/api/film', name: 'app_api_film_list', methods: 'get')]
    public function list(): Response
    {
        $films = $this->filmRepository->findAll();

        $jsonContent = $this->serializer->serialize($films, 'json', SerializationContext::create()->setGroups(array('film:list')));

        return new Response($jsonContent, '200', [
            "Content-Type' => 'application/json"
        ]);
    }

    #[Route('/api/film/{id}', name: 'app_api_film_single', methods: 'get')]
    public function single(Film $film): Response
    {
        $films = $this->filmRepository->findOneBy(['id' => $film]);

        $jsonContent = $this->serializer->serialize($films, 'json', SerializationContext::create()->setGroups(array('film:single')));

        return new Response($jsonContent, '200', [
            "Content-Type' => 'application/json"
        ]);
    }

    #[Route('/api/film', name: 'app_api_film_add', methods: 'post')]
    public function add(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $film = new Film();

        $film->setNom($data['nom']);
        $film->setDescription($data['description']);
        $film->setDate(new \DateTime($data['date']));
        $film->setNote($data['note']);

        $this->em->persist($film);
        $this->em->flush();

        $jsonContent = $this->serializer->serialize($film, 'json', SerializationContext::create()->setGroups(array('film:add')));

        return new Response($jsonContent, '201', [
            "Content-Type' => 'application/json"
        ]);
    }

    #[Route('/api/film/{id}', name: 'app_api_film_delete', methods: 'delete')]
    public function delete(Film $film = null, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $film = $this->filmRepository->findOneBy(['id' => $film]);

        if($film) {
            $this->serializer->serialize($film, 'json', SerializationContext::create()->setGroups(array('film:delete')));

            $this->em->remove($film);
            $this->em->flush();

            return new Response('Le film a été supprimé', '200', [
                "Content-Type' => 'application/json"
            ]);
        }

        return new Response('Le film n\'a pas été trouvé', '404', [
            "Content-Type' => 'application/json"
        ]);
    }

    #[Route('/api/film/{id}', name: 'app_api_film_patch', methods: 'patch')]
    public function patch(Film $film = null, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $film = $this->filmRepository->findOneBy(['id' => $film]);

        if($film) {

            foreach($data as $key => $value) {
                $film->{'set'.ucfirst($key)}($value);
                $this->em->persist($film);
            }

            $this->serializer->serialize($film, 'json', SerializationContext::create()->setGroups(array('film:patch')));

            $this->em->flush();

            return new Response('Le film a été modifié', '200', [
                "Content-Type' => 'application/json"
            ]);
        }

        return new Response('Le film n\'a pas été trouvé', '404', [
            "Content-Type' => 'application/json"
        ]);
    }

    #[Route('/api/film/{id}', name: 'app_api_film_put', methods: 'put')]
    public function put(Film $film = null, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $film = $this->filmRepository->findOneBy(['id' => $film]);

        if($film) {

            foreach($data as $key => $value) {

                if($key === 'date') {
                    $film->{'set'.ucfirst($key)}(new \DateTime($value));
                    $this->em->persist($film);
                    continue;
                }
                
                $film->{'set'.ucfirst($key)}($value);
                $this->em->persist($film);
            }

            $this->serializer->serialize($film, 'json', SerializationContext::create()->setGroups(array('film:put')));

            $this->em->flush();
            
            return new Response('Le film a été modifié', '200', [
                "Content-Type' => 'application/json"
            ]);
        }

        return new Response('Le film n\'a pas été trouvé', '404', [
            "Content-Type' => 'application/json"
        ]);
    }
}
