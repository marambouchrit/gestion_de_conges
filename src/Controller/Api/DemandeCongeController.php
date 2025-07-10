<?php

namespace App\Controller\Api;

use App\Entity\DemandeConge;
use App\Entity\Utilisateur;
use App\Repository\DemandeCongeRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/demande_conge', name: 'api_demande_conge_')]
class DemandeCongeController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(DemandeCongeRepository $repository): JsonResponse
    {
        $conges = $repository->findAll();
        $data = [];

        foreach ($conges as $conge) {
            $data[] = [
                'id' => $conge->getId(),
                'date_debut' => $conge->getDateDebut()?->format('Y-m-d'),
                'date_fin' => $conge->getDateFin()?->format('Y-m-d'),
                'statut' => $conge->getStatut(),
                'commentaire_refus' => $conge->getCommentaireRefus(),
                'type_conge' => $conge->getTypeConge(),
                'utilisateur_id' => $conge->getUtilisateur()?->getId(),
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

        $conge = new DemandeConge();
        $conge->setDateDebut(new \DateTime($data['date_debut']));
        $conge->setDateFin(new \DateTime($data['date_fin']));
        $conge->setStatut($data['statut']);
        $conge->setCommentaireRefus($data['commentaire_refus']);
        $conge->setTypeConge($data['type_conge']); 

        if (isset($data['utilisateur_id'])) {
            $utilisateur = $utilisateurRepository->find($data['utilisateur_id']);
            if ($utilisateur) {
                $conge->setUtilisateur($utilisateur);
            }
        }

        $em->persist($conge);
        $em->flush();

        return $this->json(['message' => 'Demande de congé créée', 'id' => $conge->getId()], 201);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id, DemandeCongeRepository $repo): JsonResponse
    {
        $conge = $repo->find($id);

        if (!$conge) {
            return $this->json(['message' => 'Non trouvé'], 404);
        }

        $data = [
            'id' => $conge->getId(),
            'date_debut' => $conge->getDateDebut()?->format('Y-m-d'),
            'date_fin' => $conge->getDateFin()?->format('Y-m-d'),
            'statut' => $conge->getStatut(),
            'commentaire_refus' => $conge->getCommentaireRefus(),
            'type_conge' => $conge->getTypeConge(), 
            'utilisateur_id' => $conge->getUtilisateur()?->getId(),
        ];

        return $this->json($data);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(
        int $id,
        Request $request,
        DemandeCongeRepository $repo,
        EntityManagerInterface $em,
        UtilisateurRepository $utilisateurRepository
    ): JsonResponse {
        $conge = $repo->find($id);

        if (!$conge) {
            return $this->json(['message' => 'Non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['date_debut'])) {
            $conge->setDateDebut(new \DateTime($data['date_debut']));
        }
        if (isset($data['date_fin'])) {
            $conge->setDateFin(new \DateTime($data['date_fin']));
        }

        $conge->setStatut($data['statut'] ?? $conge->getStatut());
        $conge->setCommentaireRefus($data['commentaire_refus'] ?? $conge->getCommentaireRefus());
        $conge->setTypeConge($data['type_conge'] ?? $conge->getTypeConge()); 

        if (isset($data['utilisateur_id'])) {
            $utilisateur = $utilisateurRepository->find($data['utilisateur_id']);
            if ($utilisateur) {
                $conge->setUtilisateur($utilisateur);
            }
        }

        $em->flush();

        return $this->json(['message' => 'Demande de congé mise à jour']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id, DemandeCongeRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $conge = $repo->find($id);

        if (!$conge) {
            return $this->json(['message' => 'Non trouvé'], 404);
        }

        $em->remove($conge);
        $em->flush();

        return $this->json(['message' => 'Demande de congé supprimée']);
    }
    #[Route('/{id}/valider', name: 'valider', methods: ['POST'])]
    public function validerDemande(
    int $id,
    DemandeCongeRepository $repo,
    EntityManagerInterface $em
): JsonResponse {
   
    if (!$this->isGranted('ROLE_SUPERVISEUR') && !$this->isGranted('ROLE_ADMIN')) {
        throw $this->createAccessDeniedException('Vous n\'avez pas le droit de valider une demande.');
    }

    $demande = $repo->find($id);
    if (!$demande) {
        return $this->json(['message' => 'Demande non trouvée'], 404);
    }

    $demande->setStatut('Validée');
    $em->flush();

    return $this->json(['message' => 'Demande validée']);
}

#[Route('/{id}/refuser', name: 'refuser', methods: ['POST'])]
public function refuserDemande(
    int $id,
    Request $request,
    DemandeCongeRepository $repo,
    EntityManagerInterface $em
): JsonResponse {
    if (!$this->isGranted('ROLE_SUPERVISEUR') && !$this->isGranted('ROLE_ADMIN')) {
        throw $this->createAccessDeniedException('Vous n\'avez pas le droit de refuser une demande.');
    }

    $demande = $repo->find($id);
    if (!$demande) {
        return $this->json(['message' => 'Demande non trouvée'], 404);
    }

    $data = json_decode($request->getContent(), true);
    $demande->setStatut('Refusée');
    $demande->setCommentaireRefus($data['justification'] ?? '');

    $em->flush();

    return $this->json(['message' => 'Demande refusée']);
}

}
