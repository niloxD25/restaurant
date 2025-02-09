<?php

namespace App\Controller\API\Admin;

use App\Entity\DetailCommande;
use App\Entity\Commande;
use App\Entity\Plat;
use App\Enum\DetailCommandeStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/admin/detail-commandes')]
class ApiDetailCommandeController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/{id}', name: 'api_detail_commande_show', methods: ['GET'])]
    public function show(DetailCommande $detailCommande): JsonResponse
    {
        return $this->json($detailCommande, Response::HTTP_OK);
    }

    #[Route('/createOne', name: 'api_detail_commande_create_one', methods: ['POST'])]
    public function createOne(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $commande = $this->entityManager->getRepository(Commande::class)->find($data['idCommande']);
        $plat = $this->entityManager->getRepository(Plat::class)->find($data['idPlat']);

        if (!$commande || !$plat) {
            return $this->json(['error' => 'Commande or Plat not found'], Response::HTTP_BAD_REQUEST);
        }

        $detailCommande = new DetailCommande();
        $detailCommande->setCommande($commande);
        $detailCommande->setPlat($plat);
        $detailCommande->setStatus(DetailCommandeStatus::from($data['status']));

        $this->entityManager->persist($detailCommande);
        $this->entityManager->flush();

        return $this->json($detailCommande, Response::HTTP_OK, [], ['groups' => 'detailCommande:read']);
    }

    #[Route('/create', name: 'api_detail_commande_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json(['error' => 'Invalid data format, expected an array'], Response::HTTP_BAD_REQUEST);
        }

        $createdDetails = [];

        foreach ($data as $item) {
            if (!isset($item['idCommande'], $item['idPlat'], $item['status'])) {
                return $this->json(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
            }

            $commande = $this->entityManager->getRepository(Commande::class)->find($item['idCommande']);
            $plat = $this->entityManager->getRepository(Plat::class)->find($item['idPlat']);

            if (!$commande || !$plat) {
                return $this->json(['error' => 'Commande or Plat not found'], Response::HTTP_BAD_REQUEST);
            }

            $detailCommande = new DetailCommande();
            $detailCommande->setCommande($commande);
            $detailCommande->setPlat($plat);
            $detailCommande->setStatus(DetailCommandeStatus::from($item['status']));

            $this->entityManager->persist($detailCommande);
            $createdDetails[] = $detailCommande;
        }

        $this->entityManager->flush();

        return $this->json($createdDetails, Response::HTTP_OK, [], ['groups' => 'detailCommande:read']);

    }


    #[Route('/edit/{id}', name: 'api_detail_commande_edit', methods: ['PUT'])]
    public function edit(Request $request, DetailCommande $detailCommande, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
    
        if (isset($data['status'])) {
            $detailCommande->setStatus(DetailCommandeStatus::from($data['status']));
        }
    
        if (isset($data['dateDeFinition'])) {
            try {
                $date = new DateTime($data['dateDeFinition']);
                $detailCommande->setDateDeFinition($date);
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Invalid date format'], Response::HTTP_BAD_REQUEST);
            }
        }
        $entityManager->flush();
        return $this->json($detailCommande, Response::HTTP_OK);
    }

    
    #[Route('/delete/{id}', name: 'api_detail_commande_delete', methods: ['DELETE'])]
    public function delete(DetailCommande $detailCommande): JsonResponse
    {
        $this->entityManager->remove($detailCommande);
        $this->entityManager->flush();

        return $this->json(['message' => 'Detail Commande deleted successfully'], Response::HTTP_OK);
    }
}
