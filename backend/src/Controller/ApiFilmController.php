<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Film;
use App\Repository\CategorieRepository;
use App\Repository\FilmRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiFilmController extends AbstractController
{
    private $filmRepository;
    private $categoryRepository;
    private $em;
    private $serializer;

    public function __construct(FilmRepository $filmRepository, CategorieRepository $categorieRepository, EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $this->filmRepository = $filmRepository;
        $this->categoryRepository = $categorieRepository;
        $this->em = $em;
        $this->serializer = $serializer;
    }


    /** 
     * @OA\Response(
     *     response=200,
     *     description="Returns the list of films",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Film::class, groups={"film:list"}))
     *     )
     * )
     */
    #[Route('/api/film', name: 'app_api_film_list', methods: 'get')]
    public function list(PaginationService $paginationService): Response
    {
        $films = $this->filmRepository->findAll();

        //$jsonContent = $this->serializer->serialize($films, 'json', SerializationContext::create()->setGroups(array('film:list')));
        
        $response = $paginationService->getPagination($films, 10, 'film:list');
        
        return $response;
    }

    /** 
     * @OA\Response(
     *     response=200,
     *     description="Return an user by id",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Film::class, groups={"film:single"}))
     *     )
     * )
     */
    #[Route('/api/film/{id}', name: 'app_api_film_single', methods: 'get')]
    public function single(Film $film): Response
    {
        $films = $this->filmRepository->findOneBy(['id' => $film]);

        $jsonContent = $this->serializer->serialize($films, 'json', SerializationContext::create()->setGroups(array('film:single')));

        return new Response($jsonContent, '200', [
            "Content-Type' => 'application/json"
        ]);
    }

    /** 
     * @OA\Response(
     *     response=201,
     *     description="Add an film",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Film::class))
     *     )
     * )
     */
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

    /**
     * @OA\Response(
     *     response=200,
     *     description="Delete an film by id",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=Film::class, groups={"film:delete"}))
     *    )
     * )
     */
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

    /**
     * @OA\Response(
     *     response=200,
     *     description="Patch an film",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=Film::class, groups={"film:patch"}))
     *    )
     * )
     */
    #[Route('/api/film/{id}', name: 'app_api_film_patch', methods: 'patch')]
    public function patch(Film $film = null, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $film = $this->filmRepository->findOneBy(['id' => $film]);

        if($film) {

            foreach($data as $key => $value) {
                
                if($key === 'category') {

                    if($value[0]["action"] === "add" AND isset($value[0]["id"]) AND !empty($value[0]["id"])) {
                        
                        $category = $this->categoryRepository->findOneBy(['id' => $value[0]["id"]]);

                        if(!$category)
                            return new Response('La catégorie n\'a pas été trouvé', '404', [
                                "Content-Type' => 'application/json"
                            ]);

                        $film->addCategory($category);
                    }
                        else {
                            $category = $this->categoryRepository->findOneBy(['id' => $value[0]["id"]]);
                            if($category)
                                $film->removeCategory($category);
                        }

                    $this->em->persist($film);

                    continue;
                }

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

    /**
     * @OA\Response(
     *     response=200,
     *     description="Put an film",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=Film::class, groups={"film:put"}))
     *    )
     * )
     */
    #[Route('/api/film/{id}', name: 'app_api_film_put', methods: 'put')]
    public function put(Film $film = null, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $film = $this->filmRepository->findOneBy(['id' => $film]);

        if($film) {

            foreach($data as $key => $value) {

                if($key === 'category') {

                    if($value[0]["action"] === "add" AND isset($value[0]["id"]) AND !empty($value[0]["id"])) {
                        
                        $category = $this->categoryRepository->findOneBy(['id' => $value[0]["id"]]);

                        if(!$category)
                            return new Response('La catégorie n\'a pas été trouvé', '404', [
                                "Content-Type' => 'application/json"
                            ]);

                        $film->addCategory($category);
                    }
                        else {
                            $category = $this->categoryRepository->findOneBy(['id' => $value[0]["id"]]);
                            if($category)
                                $film->removeCategory($category);
                        }

                    $this->em->persist($film);

                    continue;
                }

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
