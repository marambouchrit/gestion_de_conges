<?php

namespace App\Controller\Api;

use App\Entity\Role;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/role', name: 'api_role_')]
class RoleController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(RoleRepository $repository): JsonResponse
    {
        $roles = $repository->findAll();
        $data = [];

        foreach ($roles as $role) {
            $data[] = [
                'id' => $role->getId(),
                'nom_role' => $role->getNomRole(),
            ];
        }

        return $this->json($data);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['nom_role'])) {
            return $this->json(['message' => 'Champ nom_role requis'], 400);
        }

        $existingRole = $em->getRepository(Role::class)->findOneBy(['nom_role' => $data['nom_role']]);
        if ($existingRole) {
            return $this->json(['message' => 'Rôle avec ce nom existe déjà'], 400);
        }

        $role = new Role();
        $role->setNomRole($data['nom_role']);

        $em->persist($role);
        $em->flush();

        return $this->json(['message' => 'Rôle créé', 'id' => $role->getId()], 201);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id, RoleRepository $repo): JsonResponse
    {
        $role = $repo->find($id);

        if (!$role) {
            return $this->json(['message' => 'Non trouvé'], 404);
        }

        $data = [
            'id' => $role->getId(),
            'nom_role' => $role->getNomRole(),
        ];

        return $this->json($data);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request, RoleRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $role = $repo->find($id);

        if (!$role) {
            return $this->json(['message' => 'Non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['nom_role'])) {
            $existingRole = $em->getRepository(Role::class)->findOneBy(['nom_role' => $data['nom_role']]);
            if ($existingRole && $existingRole->getId() !== $role->getId()) {
                return $this->json(['message' => 'Rôle avec ce nom existe déjà'], 400);
            }
            $role->setNomRole($data['nom_role']);
        }

        $em->flush();

        return $this->json(['message' => 'Rôle mis à jour']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id, RoleRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $role = $repo->find($id);

        if (!$role) {
            return $this->json(['message' => 'Non trouvé'], 404);
        }

        $em->remove($role);
        $em->flush();

        return $this->json(['message' => 'Rôle supprimé']);
    }
}