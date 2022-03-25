<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\ShareMovieFormType;
use App\Message\ShareViaEmail;
use App\ShareEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MovieController extends AbstractController
{

    private $client;
    private $entityManager;


    public function __construct(HttpClientInterface $client,EntityManagerInterface $entityManager)
    {
        $this->client = $client;
        $this->entityManager = $entityManager;

    }

    /**
     * @Route("/movie", name="movie_list")
     */
    public function moviesList(): Response
    {
        $url = 'https://api.themoviedb.org/3/movie/now_playing?api_key=c89646cb9c2f9f7a6144c074fff0e9c7&language=en-US&page=1';


        $response = $this->client->request(
            'GET',
            $url
        );

//        $statusCode = $response->getStatusCode();
//        $contentType = $response->getHeaders()['content-type'][0];
//        $content = $response->getContent();
//        $content = $response->toArray();


        $em = $this->entityManager;


        $response = new Response(json_encode(['content'=>1]));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/getmovies", name="getmovie")
     */
    public function getMovies(): Response
    {

        $em = $this->entityManager;

        $movies = $em->getRepository(Movie::class)->findAll();

        $response = new Response(json_encode(['content'=>1]));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/movie/{id}", name="movies", methods={"GET"})
     */
    public function movieDetail($id): Response
    {

        $url = 'https://api.themoviedb.org/3/movie/'.$id.'?api_key=c89646cb9c2f9f7a6144c074fff0e9c7&language=en-US';

        $response = $this->client->request(
            'GET',
            $url
        );
        $content = $response->toArray();

        $response = new Response(json_encode(['content'=>$content]));
        $response->headers->set('Content-Type', 'application/json');

        return $response;

    }


    /**
     * @Route("/share", name="share")
     */
    public function shareMovie(Request $request, MessageBusInterface $messageBus): Response
    {

        $form = $this->createForm(ShareMovieFormType::class,[]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $data = $form->getData();


            $messageBus->dispatch(new ShareViaEmail(intval($data['name'])));

            $response = new Response(json_encode(['content'=>'Email sent successfully']));
            $response->headers->set('Content-Type', 'application/json');
            return $response;

        }

        return $this->renderForm('movie/new.html.twig', [
            'form' => $form,
        ]);

    }

}
