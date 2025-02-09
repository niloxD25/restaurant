<?php

namespace App\Controller\API\Admin;

use App\Entity\Commande;
use App\Entity\User;
use App\Entity\DetailCommande;
use App\Entity\Plat;
use App\Enum\CommandeStatus;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/admin/commandes')]
class ApiCommandeController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/all', name: 'api_commande_list', methods: ['GET'])]
    public function findAll(CommandeRepository $commandeRepository): JsonResponse
    {
        $commandes = $commandeRepository->findAll();
        $data = [];

        foreach ($commandes as $commande) {
            $details = [];
            $montant = 0;

            foreach ($commande->getDetailsCommande() as $detail) {
                $plat = $detail->getPlat();
                $montant += $plat->getPrixUnitaire(); 

                $detailArray = [
                    'id' => $detail->getId(),
                    'plat' => [
                        'id' => $plat->getId(),
                        'nomPlat' => $plat->getNomPlat(),
                        'prixUnitaire' => $plat->getPrixUnitaire(),
                        'tempsCuisson' => $plat->getTempsCuisson()->format('H:i:s'),
                        'ingredients' => array_map(function ($ingredientPlat) {
                            return [
                                'id' => $ingredientPlat->getIngredient()->getId(),
                                'nomIngredient' => $ingredientPlat->getIngredient()->getNomIngredient(),
                                'nomImage' => $ingredientPlat->getIngredient()->getNomImage(),
                                'quantite' => $ingredientPlat->getQuantite(),
                            ];
                        }, $plat->getIngredientsPlats()->toArray()),
                    ],
                    'status' => $detail->getStatus()->value,
                ];

                if ($detail->getDateDeFinition() !== null) {
                    $detailArray['dateDeFinition'] = $detail->getDateDeFinition()->format('Y-m-d H:i:s');
                }

                $details[] = $detailArray;
            }

            $data[] = [
                'id' => $commande->getId(),
                'client' => [
                    'id' => $commande->getClient()->getId(),
                    'email' => $commande->getClient()->getEmail(),
                    'role' => $commande->getClient()->getRole()->value,
                ],
                'dateCommande' => $commande->getDateCommande()->format('Y-m-d'),
                'montantTotal' => $montant,
                'status' => $commande->getStatus()->value,
                'details' => $details,
            ];
        }

        return $this->json($data, Response::HTTP_OK);
    }

    // #[Route('/all-en-cours', name: 'api_commande_list_en_cours', methods: ['GET'])]
    // public function findAlld(CommandeRepository $commandeRepository): JsonResponse
    // {
    //     $commandes = $commandeRepository->findAll();
    //     $data = [];

    //     foreach ($commandes as $commande) {
    //         $details = [];
    //         $montant = 0;

    //         foreach ($commande->getDetailsCommande() as $detail) {
    //             $plat = $detail->getPlat();
    //             $montant += $plat->getPrixUnitaire(); 

    //             $detailArray = [
    //                 'id' => $detail->getId(),
    //                 'plat' => [
    //                     'id' => $plat->getId(),
    //                     'nomPlat' => $plat->getNomPlat(),
    //                     'prixUnitaire' => $plat->getPrixUnitaire(),
    //                     'tempsCuisson' => $plat->getTempsCuisson()->format('H:i:s'),
    //                     'ingredients' => array_map(function ($ingredientPlat) {
    //                         return [
    //                             'id' => $ingredientPlat->getIngredient()->getId(),
    //                             'nomIngredient' => $ingredientPlat->getIngredient()->getNomIngredient(),
    //                             'nomImage' => $ingredientPlat->getIngredient()->getNomImage(),
    //                             'quantite' => $ingredientPlat->getQuantite(),
    //                         ];
    //                     }, $plat->getIngredientsPlats()->toArray()),
    //                 ],
    //                 'status' => $detail->getStatus()->value,
    //             ];

    //             if ($detail->getDateDeFinition() !== null) {
    //                 $detailArray['dateDeFinition'] = $detail->getDateDeFinition()->format('Y-m-d H:i:s');
    //             }

    //             $details[] = $detailArray;
    //         }

    //         $data[] = [
    //             'id' => $commande->getId(),
    //             'client' => [
    //                 'id' => $commande->getClient()->getId(),
    //                 'email' => $commande->getClient()->getEmail(),
    //                 'role' => $commande->getClient()->getRole()->value,
    //             ],
    //             'dateCommande' => $commande->getDateCommande()->format('Y-m-d'),
    //             'montantTotal' => $montant,
    //             'status' => $commande->getStatus()->value,
    //             'details' => $details,
    //         ];
    //     }

    //     return $this->json($data, Response::HTTP_OK);
    // }

    #[Route('/{id}', name: 'api_commande_show', methods: ['GET'])]
    public function show(Commande $commande): JsonResponse
    {
        return $this->json($commande, Response::HTTP_OK);
    }

    #[Route('/create', name: 'api_commande_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $commande = new Commande();
        $commande->setDateCommande(isset($data['dateCommande']) ? new \DateTime($data['dateCommande']) : new \DateTime());
        $commande->setMontantTotal($data['montantTotal']);
        $commande->setStatus(CommandeStatus::from($data['status']));
        $commande->setClient($this->entityManager->getRepository(User::class)->find($data['idClient']));

        $this->entityManager->persist($commande);
        $this->entityManager->flush();

        return $this->json($commande, Response::HTTP_CREATED);
    }


    #[Route('/edit/{id}', name: 'api_commande_edit', methods: ['PUT'])]
    public function edit(Request $request, Commande $commande): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['montantTotal'])) {
            $commande->setMontantTotal($data['montantTotal']);
        }

        if (isset($data['status'])) {
            $commande->setStatus(CommandeStatus::from($data['status']));
        }

        $this->entityManager->flush();

        return $this->json($commande, Response::HTTP_OK);
    }

    #[Route('/delete/{id}', name: 'api_commande_delete', methods: ['DELETE'])]
    public function delete(Commande $commande): JsonResponse
    {
        $this->entityManager->remove($commande);
        $this->entityManager->flush();

        return $this->json(['message' => 'Commande deleted successfully'], Response::HTTP_OK);
    }
}
