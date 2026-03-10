<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/messages')]
class MessageController extends AbstractController
{
    #[Route('/new', name: 'app_message_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($currentUser->isBlocked()) {
            $this->addFlash('error', 'Je account is geblokkeerd. Je kunt geen berichten versturen.');

            return $this->redirectToRoute('app_home');
        }

        $message = new Message();
        $message->setSender($currentUser);

        $form = $this->createForm(MessageType::class, $message, [
            'recipient_query_builder' => $userRepository->createQueryBuilder('u')
                ->andWhere('u != :currentUser')
                ->setParameter('currentUser', $currentUser)
                ->orderBy('u.email', 'ASC'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($message);
            $entityManager->flush();

            $this->addFlash('success', 'Bericht is verzonden.');

            return $this->redirectToRoute('app_message_inbox');
        }

        return $this->render('message/new.html.twig', [
            'messageForm' => $form,
        ]);
    }

    #[Route('/inbox', name: 'app_message_inbox')]
    public function inbox(MessageRepository $messageRepository): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        return $this->render('message/inbox.html.twig', [
            'messages' => $messageRepository->findInboxForUser($currentUser),
        ]);
    }
}
