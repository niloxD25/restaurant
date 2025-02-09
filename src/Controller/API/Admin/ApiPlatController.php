<?php

namespace App\Controller\API\Admin;


use App\Entity\Plat;
use App\Repository\PlatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/admin/plats')]
class ApiPlatController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/all-detailed', name: 'api_plat_list_detailed', methods: ['GET'])]
    public function findAllDetailed(PlatRepository $platRepository): JsonResponse
    {
        $plats = $platRepository->findAll();
        $data = [];

        foreach ($plats as $plat) {
            $ingredients = [];

            foreach ($plat->getIngredientsPlats() as $ingredientPlat) {
                $ingredient = $ingredientPlat->getIngredient();
                $ingredients[] = [
                    'id' => $ingredient->getId(),
                    'nomIngredient' => $ingredient->getNomIngredient(),
                    'nomImage' => $ingredient->getNomImage(),
                    'quantite' => $ingredientPlat->getQuantite()
                ];
            }

            $data[] = [
                'id' => $plat->getId(),
                'nomPlat' => $plat->getNomPlat(),
                'prixUnitaire' => $plat->getPrixUnitaire(),
                'tempsCuisson' => $plat->getTempsCuisson()->format('H:i:s'),
                'ingredients' => $ingredients
            ];
        }

        return $this->json($data, Response::HTTP_OK);
    }

    
    #[Route('/{id}', name: 'api_plat_show', methods: ['GET'])]
    public function show(Plat $plat): JsonResponse
    {
        return $this->json($plat, Response::HTTP_OK);
    }

    #[Route('/create', name: 'api_plat_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $plat = new Plat();
        $plat->setNomPlat($data['nomPlat']);
        $plat->setPrixUnitaire($data['prixUnitaire']);
        $plat->setTempsCuisson(new \DateTime($data['tempsCuisson']));

        $this->entityManager->persist($plat);
        $this->entityManager->flush();

        return $this->json($plat, Response::HTTP_CREATED);
    }

    #[Route('/edit/{id}', name: 'api_plat_edit', methods: ['PUT'])]
    public function edit(Request $request, Plat $plat): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['nomPlat'])) {
            $plat->setNomPlat($data['nomPlat']);
        }

        if (isset($data['prixUnitaire'])) {
            $plat->setPrixUnitaire($data['prixUnitaire']);
        }

        if (isset($data['tempsCuisson'])) {
            $plat->setTempsCuisson(new \DateTime($data['tempsCuisson']));
        }

        $this->entityManager->flush();

        return $this->json($plat, Response::HTTP_OK);
    }

    #[Route('/delete/{id}', name: 'api_plat_delete', methods: ['DELETE'])]
    public function delete(Plat $plat): JsonResponse
    {
        $this->entityManager->remove($plat);
        $this->entityManager->flush();

        return $this->json(['message' => 'Plat deleted successfully'], Response::HTTP_OK);
    }
}
