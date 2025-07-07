<?php

namespace App\Controller\Api;

use App\Entity\Departement;
use App\Repository\DepartementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/departements', name: 'api_departement_')]
class DepartementController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(DepartementRepository $repository): JsonResponse
    {
        $departements = $repository->findAll();
        $data = [];

        foreach ($departements as $departement) {
            $data[] = [
                'id' => $departement->getId(),
                'nom_departement' => $departement->getNomDepartement(),
            ];
        }

        return $this->json($data);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['nom_departement'])) {
            return $this->json(['message' => 'Champ nom_departement requis'], 400);
        }

        $existingDepartement = $em->getRepository(Departement::class)->findOneBy(['nom_departement' => $data['nom_departement']]);
        if ($existingDepartement) {
            return $this->json(['message' => 'Département avec ce nom existe déjà'], 400);
        }

        $departement = new Departement();
        $departement->setNomDepartement($data['nom_departement']);

        $em->persist($departement);
        $em->flush();

        return $this->json(['message' => 'Département créé', 'id' => $departement->getId()], 201);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id, DepartementRepository $repo): JsonResponse
    {
        $departement = $repo->find($id);

        if (!$departement) {
            return $this->json(['message' => 'Non trouvé'], 404);
        }

        $data = [
            'id' => $departement->getId(),
            'nom_departement' => $departement->getNomDepartement(),
        ];

        return $this->json($data);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request, DepartementRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $departement = $repo->find($id);

        if (!$departement) {
            return $this->json(['message' => 'Non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['nom_departement'])) {
            $existingDepartement = $em->getRepository(Departement::class)->findOneBy(['nom_departement' => $data['nom_departement']]);
            if ($existingDepartement && $existingDepartement->getId() !== $departement->getId()) {
                return $this->json(['message' => 'Département avec ce nom existe déjà'], 400);
            }
            $departement->setNomDepartement($data['nom_departement']);
        }

        $em->flush();

        return $this->json(['message' => 'Département mis à jour']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id, DepartementRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $departement = $repo->find($id);

        if (!$departement) {
            return $this->json(['message' => 'Non trouvé'], 404);
        }

        $em->remove($departement);
        $em->flush();

        return $this->json(['message' => 'Département supprimé']);
    }
}