<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $query = trim((string) $request->query->get('q', ''));

        if ($query !== '') {
            $users = $userRepository->searchBySkillsOrName($query);
        } else {
            $users = $userRepository->findBy([], ['id' => 'DESC']);
        }

        return $this->render('index/index.html.twig', [
            'users' => $users,
            'query' => $query,
        ]);
    }
}
