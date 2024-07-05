<?php

namespace App\Controller;

use App\Service\Greetings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route('/hello/{name}', name: 'app_hello')]
    public function hello(Greetings $greetingsService, string $name): Response
    {
        return $this->render('default/hello.html.twig', [
            'message' => $greetingsService->greet($name),
        ]);
    }

    #[Route('/goodbye/{name}', name: 'app_goodbye')]
    public function goodbye(Greetings $greetingsService, string $name): Response
    {
        return $this->render('default/goodbye.html.twig', [
            'message' => $greetingsService->bye($name),
        ]);
    }
}
