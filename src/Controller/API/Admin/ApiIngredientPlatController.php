<?php

namespace App\Controller\API\Admin;

use App\Entity\IngredientPlat;
use App\Entity\Plat;
use App\Entity\Ingredient;
use App\Repository\IngredientPlatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/admin/ingredient-plats')]
class ApiIngredientPlatController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 📌 Récupérer toutes les associations plat-ingredient
     */
    #[Route('/all', name: 'api_ingredient_plat_list', methods: ['GET'])]
    public function findAll(IngredientPlatRepository $ingredientPlatRepository): JsonResponse
    {
        $ingredientPlats = $ingredientPlatRepository->findAll();

        $data = array_map(function (IngredientPlat $ingredientPlat) {
            return [
                'id' => $ingredientPlat->getId(),
                'ingredient' => [
                    'id' => $ingredientPlat->getIngredient()->getId(),
                    'nomIngredient' => $ingredientPlat->getIngredient()->getNomIngredient(),
                    'nomImage' => $ingredientPlat->getIngredient()->getNomImage(),
                ],
                'quantite' => $ingredientPlat->getQuantite(),
            ];
        }, $ingredientPlats);

        return $this->json($data, Response::HTTP_OK);
    }

        /**
     * 📌 Ajouter un ingrédient à un plat
     */
    #[Route('/create', name: 'api_ingredient_plat_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['idPlat'], $data['idIngredient'])) {
                return $this->json(['error' => 'Missing required fields: idPlat, idIngredient'], Response::HTTP_BAD_REQUEST);
            }

            $plat = $this->entityManager->getRepository(Plat::class)->find($data['idPlat']);
            if (!$plat) {
                return $this->json(['error' => 'Plat not found'], Response::HTTP_NOT_FOUND);
            }

            $ingredient = $this->entityManager->getRepository(Ingredient::class)->find($data['idIngredient']);
            if (!$ingredient) {
                return $this->json(['error' => 'Ingredient not found'], Response::HTTP_NOT_FOUND);
            }

            // Vérifier si l'association existe déjà
            $existingIngredientPlat = $this->entityManager->getRepository(IngredientPlat::class)->findOneBy([
                'plat' => $plat,
                'ingredient' => $ingredient,
            ]);

            if ($existingIngredientPlat) {
                return $this->json(['error' => 'This ingredient is already associated with this plat'], Response::HTTP_CONFLICT);
            }

            $ingredientPlat = new IngredientPlat();
            $ingredientPlat->setPlat($plat);
            $ingredientPlat->setIngredient($ingredient);
            $ingredientPlat->setQuantite(1); // ✅ Quantité mise par défaut à 0

            $this->entityManager->persist($ingredientPlat);
            $this->entityManager->flush();

            return $this->json([
                'id' => $ingredientPlat->getId(),
                'ingredient' => [
                    'id' => $ingredientPlat->getIngredient()->getId(),
                    'nomIngredient' => $ingredientPlat->getIngredient()->getNomIngredient(),
                    'nomImage' => $ingredientPlat->getIngredient()->getNomImage(),
                ],
                'quantite' => $ingredientPlat->getQuantite(),
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json(['error' => 'An unexpected error occurred', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * 📌 Supprimer un ingrédient d'un plat
     */
    #[Route('/delete/{id}', name: 'api_ingredient_plat_delete', methods: ['DELETE'])]
    public function delete(IngredientPlat $ingredientPlat): JsonResponse
    {
        $this->entityManager->remove($ingredientPlat);
        $this->entityManager->flush();

        return $this->json(['message' => 'IngredientPlat deleted successfully'], Response::HTTP_OK);
    }
}
