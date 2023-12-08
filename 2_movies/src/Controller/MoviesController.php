<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MoviesController extends AbstractController
{
    // defaults: ["name" => null] -> default value of the parameter "name" is null. I don't like this way, but I should know it
    // HEAD == GET, but is called when you pass a huge data
    #[Route('/movies/{name}', name: 'movies', defaults: ['name' => null], methods: ['GET', 'HEAD'])]
    public function index($name) : Response
    {
        return $this->render('movies/index.html.twig', [
            'name' => $name
        ]);
    }
}