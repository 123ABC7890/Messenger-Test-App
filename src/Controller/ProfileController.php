<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Profiel bijgewerkt.');

            return $this->redirectToRoute('app_profile_show', ['id' => $user->getId()]);
        }

        return $this->render('profile/edit.html.twig', [
            'profileForm' => $form,
        ]);
    }

    #[Route('/users/{id}', name: 'app_profile_show', requirements: ['id' => '\\d+'])]
    public function show(User $user): Response
    {
        return $this->render('profile/show.html.twig', [
            'profileUser' => $user,
        ]);
    }
}
