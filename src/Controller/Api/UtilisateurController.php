<?php

namespace App\Controller\Api;

use App\Entity\Utilisateur;
use App\Entity\Role;
use App\Entity\Departement;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/utilisateur', name: 'api_utilisateur_')]
class UtilisateurController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(UtilisateurRepository $utilisateurRepository): JsonResponse
    {
        $utilisateurs = $utilisateurRepository->findAll();

        $data = [];
        foreach ($utilisateurs as $utilisateur) {
            $data[] = [
                'id' => $utilisateur->getId(),
                'nom' => $utilisateur->getNom(),
                'prenom' => $utilisateur->getPrenom(),
                'email' => $utilisateur->getEmail(),
                'role' => $utilisateur->getRole() ? $utilisateur->getRole()->getId() : null,
                'departement' => $utilisateur->getDepartement() ? $utilisateur->getDepartement()->getId() : null,
            ];
        }

        return $this->json($data);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $utilisateur = new Utilisateur();
        $utilisateur->setNom($data['nom'] ?? null);
        $utilisateur->setPrenom($data['prenom'] ?? null);
        $utilisateur->setEmail($data['email']);
        $utilisateur->setMotDePasse($data['mot_de_passe'] ?? null);

        //  Gestion Role et Departement 
         $role = $entityManager->getRepository(Role::class)->find($data['role_id']);
        $departement = $entityManager->getRepository(Departement::class)->find($data['departement_id']);
         $utilisateur->setRole($role);
         $utilisateur->setDepartement($departement);


        $entityManager->persist($utilisateur);
        $entityManager->flush();

        return $this->json([
            'message' => 'Utilisateur créé avec succès',
            'id' => $utilisateur->getId()
        ], 201);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
public function update(
    int $id,
    Request $request,
    UtilisateurRepository $utilisateurRepository,
    EntityManagerInterface $entityManager
): JsonResponse {
    $utilisateur = $utilisateurRepository->find($id);

    if (!$utilisateur) {
        return $this->json(['message' => 'Utilisateur non trouvé'], 404);
    }

    $data = json_decode($request->getContent(), true);

    $utilisateur->setNom($data['nom'] ?? $utilisateur->getNom());
    $utilisateur->setPrenom($data['prenom'] ?? $utilisateur->getPrenom());
    $utilisateur->setEmail($data['email'] ?? $utilisateur->getEmail());
    $utilisateur->setMotDePasse($data['mot_de_passe'] ?? $utilisateur->getMotDePasse());

    // Exemple pour Role et Departement si besoin :
    // if (isset($data['role_id'])) {
    //     $role = $entityManager->getRepository(Role::class)->find($data['role_id']);
    //     $utilisateur->setRole($role);
    // }
    // if (isset($data['departement_id'])) {
    //     $departement = $entityManager->getRepository(Departement::class)->find($data['departement_id']);
    //     $utilisateur->setDepartement($departement);
    // }

    $entityManager->flush();

    return $this->json(['message' => 'Utilisateur mis à jour']);
}
#[Route('/{id}', name: 'delete', methods: ['DELETE'])]
public function delete(
    int $id,
    UtilisateurRepository $utilisateurRepository,
    EntityManagerInterface $entityManager
): JsonResponse {
    $utilisateur = $utilisateurRepository->find($id);

    if (!$utilisateur) {
        return $this->json(['message' => 'Utilisateur non trouvé'], 404);
    }

    $entityManager->remove($utilisateur);
    $entityManager->flush();

    return $this->json(['message' => 'Utilisateur supprimé']);
}
public function consulterDemandes(): array
{
    return $this->demandesConge->toArray();
}
public function consulterSolde(): ?SoldeConge
{
    return $this->soldeConge;
}
public function recevoirNotification(Notification $notification): void
{
    $this->notifications[] = $notification;
}
}
