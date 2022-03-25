<?php

namespace App\Controller;

use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
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
    public function shareMovie(MailerInterface $mailer): Response
    {
        $movie_link = 'https://www.imdb.com/title/tt1596345/?ref_=vp_back';

        $email = (new Email())
            ->from('learndevsho@gmail.com')
            ->to('soufianjill@gmail.com')
            ->subject('Check out this link on Themoviedb!')
            ->text('Sending emails is fun again!')
            ->html('<p>Check This movie</p> <a href="'.$movie_link.'">'.$movie_link.'</a>');


        $mailer->send($email);


        $response = new Response(json_encode(['content'=>1]));
        $response->headers->set('Content-Type', 'application/json');

        return $response;

    }

}
