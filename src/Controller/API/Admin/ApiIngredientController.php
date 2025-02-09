<?php

namespace App\Controller\API\Admin;

use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/admin/ingredients')]
class ApiIngredientController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/all', name: 'api_ingredient_list', methods: ['GET'])]
    public function findAll(IngredientRepository $ingredientRepository): JsonResponse
    {
        return $this->json($ingredientRepository->findAll(), Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'api_ingredient_show', methods: ['GET'])]
    public function show(Ingredient $ingredient): JsonResponse
    {
        return $this->json($ingredient, Response::HTTP_OK);
    }

    #[Route('/create', name: 'api_ingredient_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $ingredient = new Ingredient();
        $ingredient->setNomIngredient($data['nomIngredient']);
        $ingredient->setNomImage($data['nomImage']);

        $this->entityManager->persist($ingredient);
        $this->entityManager->flush();

        return $this->json($ingredient, Response::HTTP_CREATED);
    }

    #[Route('/edit/{id}', name: 'api_ingredient_edit', methods: ['PUT'])]
    public function edit(Request $request, Ingredient $ingredient): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['nomIngredient'])) {
            $ingredient->setNomIngredient($data['nomIngredient']);
        }

        if (isset($data['nomImage'])) {
            $ingredient->setNomImage($data['nomImage']);
        }

        $this->entityManager->flush();

        return $this->json($ingredient, Response::HTTP_OK);
    }

    #[Route('/delete/{id}', name: 'api_ingredient_delete', methods: ['DELETE'])]
    public function delete(Ingredient $ingredient): JsonResponse
    {
        $this->entityManager->remove($ingredient);
        $this->entityManager->flush();

        return $this->json(['message' => 'Ingredient deleted successfully'], Response::HTTP_OK);
    }
}