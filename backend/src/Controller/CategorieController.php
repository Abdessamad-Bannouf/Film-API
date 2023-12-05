<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends AbstractController
{
    private $categoryRepository;
    private $em;
    private $serializer;

    public function __construct(CategorieRepository $categorieRepository, EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $this->categoryRepository = $categorieRepository;
        $this->em = $em;
        $this->serializer = $serializer;
    }


    /** 
     * @OA\Response(
     *     response=200,
     *     description="Returns the list of categoeries",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Categorie::class, groups={"categorie:list"}))
     *     )
     * )
     */
    #[Route('/api/categorie', name: 'app_api_film_categorie', methods: 'get')]
    public function list(PaginationService $paginationService): Response
    {
        $categories = $this->categoryRepository->findAll();
        
        $response = $paginationService->getPagination($categories, 10, 'categorie:list');
        
        return $response;
    }

    /** 
     * @OA\Response(
     *     response=200,
     *     description="Return an user by id",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Categorie::class, groups={"categorie:single"}))
     *     )
     * )
     */
    #[Route('/api/categorie/{id}', name: 'app_api_categorie_single', methods: 'get')]
    public function single(Categorie $categorie): Response
    {
        $categories = $this->categoryRepository->findOneBy(['id' => $categorie]);

        $jsonContent = $this->serializer->serialize($categories, 'json', SerializationContext::create()->setGroups(array('categorie:single')));

        return new Response($jsonContent, '200', [
            "Content-Type' => 'application/json"
        ]);
    }
}
