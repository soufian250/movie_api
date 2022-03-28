<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\ShareMovieFormType;
use App\Form\ShareWithFriendFormType;
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

//      La récupération des 20 derniers films actuellement en cours de diffusion au cinéma
//      Pour chaque page on a 20 films

        $url = 'https://api.themoviedb.org/3/movie/now_playing?api_key=c89646cb9c2f9f7a6144c074fff0e9c7&language=en-US&page=1';

        $response = $this->client->request(
            'GET',
            $url
        );

        $content = $response->toArray();
        $movies = $content['results'];

//        GET POSTER: https://image.tmdb.org/t/p/original/<poster_path>

        $em = $this->entityManager;

        $moviesExist = $em->getRepository(Movie::class)->findAll();

        if ($moviesExist == null){
            foreach ($movies as $film){

                $movie = new Movie();
                $movie->setName($film['title']);
                $date =  new \DateTime($film['release_date']);
                $movie->setOverview($film['overview']);
                $movie->setReleaseDate($date);
                $movie->setThemoviedbId($film['id']);

                $em->persist($movie);
                $em->flush();

            }
        }


        $response = new Response(json_encode(['content'=>1]),200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/getmovies", name="getmovie")
     */
    public function getMovies(): Response
    {

//       Une route api permettant à un applicatif front de lister les films préalablement stockés.

        $em = $this->entityManager;
        $movies = $em->getRepository(Movie::class)->findAll();

        $response = new Response(json_encode(['content'=>$movies]),200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/movie/{id}", name="movie_detail", methods={"GET"})
     */
    public function movieDetail($id): Response
    {

//        Une route api permettant d'avoir le détail d'un film.
       
        $id = $request->attributes->get('id');
        $url =  "https://api.themoviedb.org/3/movie/".$id."?api_key=c89646cb9c2f9f7a6144c074fff0e9c7&language=en-US";


        $response = $this->client->request(
            'GET',
            $url
        );


        $movie = $response->toArray();


        $response = new Response(json_encode(['content'=>$movie]),200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;


    }


    /**
     * @Route("/share", name="share")
     */
    public function shareMovie(Request $request, MessageBusInterface $messageBus): Response
    {

//       Un webservice permettant le partage un film à un ami par mail

        $form = $this->createForm(ShareMovieFormType::class,[]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $data = $form->getData();
            $messageBus->dispatch(new ShareViaEmail($data['email'],$data['movieLink']));

            $response = new Response(json_encode(['content'=>'E-mail envoyé avec succès']));
            $response->headers->set('Content-Type', 'application/json');
            return $response;

        }

        return $this->renderForm('movie/new.html.twig', [
            'form' => $form,
        ]);

    }

    /**
     * @Route("/", name="index")
     */
    public function index(Request $request): Response
    {

        $em = $this->entityManager;

        $movies = $em->getRepository(Movie::class)->createQueryBuilder('m')
            ->where('m.shareCount is not NULL')
            ->orderBy('m.shareCount','DESC')
            ->getQuery()->setMaxResults(3)->getResult();


        return $this->render('movie/index.html.twig',[
            'movies'=>$movies
        ]);

    }


    /**
     * @Route("/moviedetail/{idMovie}", name="moviedetail")
     */
    public function showMovie(Request $request,MessageBusInterface $messageBus)
    {

        $id = intval($request->attributes->get('idMovie'));
        $credit = 'https://api.themoviedb.org/3/movie/'.$id.'/credits?api_key=c89646cb9c2f9f7a6144c074fff0e9c7&language=en-US';


        $url =  'https://api.themoviedb.org/3/movie/'.$id.'?api_key=c89646cb9c2f9f7a6144c074fff0e9c7&language=en-US';
        $response = $this->client->request(
            'GET',
            $url
        );


        $movie = $response->toArray();

        $responseCredit = $this->client->request(
            'GET',
            $credit
        );

        $actorsList = $responseCredit->toArray();

        $form = $this->createForm(ShareWithFriendFormType::class,[]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $final = preg_replace('#[ -]+#', '-', $movie['original_title']);

           $movie_link = 'https://www.themoviedb.org/movie/'.$id.'-'.$final;


            $data = $form->getData();

            $messageBus->dispatch(new ShareViaEmail($data['email'],$movie_link));

            return $this->redirectToRoute('moviedetail',["idMovie"=>$id]);

        }


        return $this->renderForm('movie/show.html.twig',[
            'form'=>$form,
            'actorsList'=>$actorsList['cast'],
            'movie'=>$movie,
        ]);

    }


}
