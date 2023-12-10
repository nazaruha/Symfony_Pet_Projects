<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieFormType;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MoviesController extends AbstractController
{
    private MovieRepository $movieRepository; // already repository, but only for movies
    private EntityManagerInterface $em; // due to this object you can relate to any Repository

    public function __construct(MovieRepository $movieRepository, EntityManagerInterface $em)
    {
        $this->movieRepository = $movieRepository;
        $this->em = $em;
    }

    // defaults: ["name" => null] -> default value of the parameter "name" is null. I don't like this way, but I should know it
    // HEAD == GET, but is called when you pass a huge data
    // <\d+> -> regex expression for the prop 'id'
    #[Route('/movies', name: 'movies', methods: ['GET', 'HEAD'])]
    public function index(int $id = null) : Response
    {
        // Get All Movies
        $movies = $this->movieRepository->findAll(); // SELECT * FROM movies

        // Count Movies In The List
        $moviesCount = $this->movieRepository->count([]); // SELECT COUNT() FROM movies

        // Count Movies By The Specific Prop
        $searchId = 10;
        $moviesCountByProp = $this->movieRepository->count(['id' => $searchId]); // SELECT COUNT() FROM movies WHERE id = 10

        // Get The Entity Class Name
        $movieClass = $this->movieRepository->getClassName();

        return $this->render('movies/index.html.twig', [
            'movies' => $movies,
            'moviesCount' => $moviesCount,
            'searchId' => $searchId,
            'moviesCountByProp' => $moviesCountByProp,
            'movieClass' => $movieClass
        ]);
    }

    #[Route("movies/{id<\d+>}", name: 'movie-show', methods: ['GET'])]
    public function show(int $id) : Response
    {
        $movie = $this->movieRepository->find($id); // SELECT * FROM movies WHERE id = 1;
        if (!isset($movie))
        {
            return $this->json([
                'message' => "Movie by id $id not found",
                'success' => false
            ]);
        }
        return $this->render('movies/show.html.twig', [
            'movie' => $movie
        ]);
    }

    #[Route("/movies/create", name: 'movie-create')]
    public function create(Request $request) : Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieFormType::class, $movie);

        $form->handleRequest($request);
        if ($form->isSubmitted() /*POST or PUT req*/ && $form->isValid() /*fields validation*/)
        {
            $newMovie = $form->getData();

            $imagePath = $form->get('imagePath'); // get the specific field
            if ($imagePath)
            {
                $newFileName = uniqid() . '.' . $imagePath->guessExtension();

                try {
                    $imagePath->move(
                        $this->getParameter('kernel,project_dir') . '/public/uploads', // path to save
                        $newFileName // name of image
                    );
                }
                catch(FileException $e)
                {
                    return new Response($e->getMessage());
                }

                $newMovie->setImagePath('/uploads/' . $newFileName);
            }

            $this->em->persist($newMovie);
            $this->em->flush(); // post to db

            return $this->redirectToRoute('movies'); // name of route
        }

        return $this->render('movies/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // This Route is just for Example of another way to use Repositories. This is the Better one
    #[Route('/movies2', name: 'second', methods: ['GET', 'HEAD'])]
    public function second() : Response
    {
        $repository = $this->em->getRepository(Movie::class);
        $movies = $repository->findAll();

        return $this->render('movies/index.html.twig', [
            'movies' => $movies
        ]);
    }

    #[Route("/movies/{prop}", name: "moviesSortByProp", methods: ['GET', 'HEAD'])]
    public function moviesSortByProperty(string $prop = null) : Response
    {
        $repository = $this->em->getRepository(Movie::class);
        $movies = $repository->findBy([], [$prop => "ASC"]); // DESC or ASC sorting (SELECT * FROM movies ORDER BY id DESC)

        dd($movies);

        return $this->json([
            "movies" => $movies
        ]);
    }

    #[Route("/movies3", name: "movies2", methods: ['GET', 'HEAD'])]
    public function moviesFindOnBy() : Response
    {
        $repository = $this->em->getRepository(Movie::class);
        // SELECT * FROM movies WHERE id = 10 AND title = 'Avengers End Game' ORDER BY id DESC
        $movies = $repository->findOneBy(['id' => 10, 'title' => "Avengers End Game"], ['id' => 'DESC'] /*optional parameter for kind of sorting*/);

        dd($movies);

        return $this->json([
            "movies" => $movies
        ]);
    }

}