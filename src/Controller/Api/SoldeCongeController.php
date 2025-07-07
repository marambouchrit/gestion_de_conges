<?php

namespace App\Controller\Api;

use App\Entity\SoldeConge;
use App\Entity\Utilisateur;
use App\Repository\SoldeCongeRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/solde_conge', name: 'api_solde_conge_')]
class SoldeCongeController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(SoldeCongeRepository $repository): JsonResponse
    {
        $soldes = $repository->findAll();
        $data = [];

        foreach ($soldes as $solde) {
            $data[] = [
                'id' => $solde->getId(),
                'nb_jours_total' => $solde->getNbJoursTotal(),
                'nb_jours_pris' => $solde->getNbJoursPris(),
                'utilisateur_id' => $solde->getUtilisateur()?->getId(),
            ];
        }

        return $this->json($data);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        UtilisateurRepository $utilisateurRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $solde = new SoldeConge();
        $solde->setNbJoursTotal($data['nb_jours_total']);
        $solde->setNbJoursPris($data['nb_jours_pris']);

        if (isset($data['utilisateur_id'])) {
            $utilisateur = $utilisateurRepository->find($data['utilisateur_id']);
            if ($utilisateur) {
                $solde->setUtilisateur($utilisateur);
            }
        }

        $em->persist($solde);
        $em->flush();

        return $this->json(['message' => 'Solde de congé créé', 'id' => $solde->getId()], 201);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id, SoldeCongeRepository $repo): JsonResponse
    {
        $solde = $repo->find($id);

        if (!$solde) {
            return $this->json(['message' => 'Non trouvé'], 404);
        }

        $data = [
            'id' => $solde->getId(),
            'nb_jours_total' => $solde->getNbJoursTotal(),
            'nb_jours_pris' => $solde->getNbJoursPris(),
            'utilisateur_id' => $solde->getUtilisateur()?->getId(),
        ];

        return $this->json($data);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(
        int $id,
        Request $request,
        SoldeCongeRepository $repo,
        EntityManagerInterface $em,
        UtilisateurRepository $utilisateurRepository
    ): JsonResponse {
        $solde = $repo->find($id);

        if (!$solde) {
            return $this->json(['message' => 'Non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $solde->setNbJoursTotal($data['nb_jours_total'] ?? $solde->getNbJoursTotal());
        $solde->setNbJoursPris($data['nb_jours_pris'] ?? $solde->getNbJoursPris());

        if (isset($data['utilisateur_id'])) {
            $utilisateur = $utilisateurRepository->find($data['utilisateur_id']);
            if ($utilisateur) {
                $solde->setUtilisateur($utilisateur);
            }
        }

        $em->flush();

        return $this->json(['message' => 'Solde de congé mis à jour']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id, SoldeCongeRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $solde = $repo->find($id);

        if (!$solde) {
            return $this->json(['message' => 'Non trouvé'], 404);
        }

        $em->remove($solde);
        $em->flush();

        return $this->json(['message' => 'Solde de congé supprimé']);
    }
}
