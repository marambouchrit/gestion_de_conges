<?php

namespace App\Controller\Api;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/notification', name: 'api_notification_')]
class NotificationController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(NotificationRepository $notificationRepository): JsonResponse
    {
        $notifications = $notificationRepository->findAll();
        $data = [];

        foreach ($notifications as $notification) {
            $data[] = [
                'id' => $notification->getId(),
                'message' => $notification->getMessage(),
                'date_envoi' => $notification->getDateEnvoi()?->format('Y-m-d H:i:s'),
                'utilisateur' => $notification->getUtilisateur()?->getId(),
            ];
        }

        return $this->json($data);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, UtilisateurRepository $utilisateurRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $notification = new Notification();
        $notification->setMessage($data['message'] ?? null);
        $notification->setDateEnvoi(new \DateTime($data['date_envoi']));

        if (isset($data['utilisateur_id'])) {
            $utilisateur = $utilisateurRepository->find($data['utilisateur_id']);
            $notification->setUtilisateur($utilisateur);
        }

        $entityManager->persist($notification);
        $entityManager->flush();

        return $this->json([
            'message' => 'Notification créée avec succès',
            'id' => $notification->getId()
        ], 201);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(
        int $id,
        Request $request,
        NotificationRepository $notificationRepository,
        EntityManagerInterface $entityManager,
        UtilisateurRepository $utilisateurRepository
    ): JsonResponse {
        $notification = $notificationRepository->find($id);

        if (!$notification) {
            return $this->json(['message' => 'Notification non trouvée'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $notification->setMessage($data['message'] ?? $notification->getMessage());
        if (isset($data['date_envoi'])) {
            $notification->setDateEnvoi(new \DateTime($data['date_envoi']));
        }

        if (isset($data['utilisateur_id'])) {
            $utilisateur = $utilisateurRepository->find($data['utilisateur_id']);
            $notification->setUtilisateur($utilisateur);
        }

        $entityManager->flush();

        return $this->json(['message' => 'Notification mise à jour']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(
        int $id,
        NotificationRepository $notificationRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $notification = $notificationRepository->find($id);

        if (!$notification) {
            return $this->json(['message' => 'Notification non trouvée'], 404);
        }

        $entityManager->remove($notification);
        $entityManager->flush();

        return $this->json(['message' => 'Notification supprimée']);
    }
    public function afficherNotification(): string
{
    return $this->message ?? '';
}
}
