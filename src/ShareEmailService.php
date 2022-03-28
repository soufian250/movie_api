<?php

namespace App;

use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ShareEmailService
{
    private $client;
    private $mailer;
    private $entityRepository;

    public function __construct(MailerInterface $mailer,EntityManagerInterface $entityRepository,HttpClientInterface $client)
    {
        $this->client = $client;
        $this->mailer = $mailer;
        $this->entityRepository = $entityRepository;
    }

    public function share($email,$movieLink){


        $email = (new Email())
            ->from('learndevsho@gmail.com')
            ->to($email)
            ->subject('Check out this movie on Themoviedb!')
            ->html('<p>Check This movie</p> <a href="'.$movieLink.'">'.$movieLink.'</a>');


        // Extraire l'ID du lien du film
        $last_part = substr(strrchr($movieLink, "/"), 1);
        $id = strtok($last_part, '-');


        $movie = $this->entityRepository->getRepository(Movie::class)->findByThemoviedbId($id);


        if ($movie){

            if ($movie[0]->getShareCount() == null){

                $movie[0]->setShareCount(1);

            }else{

                $shareCount = $movie[0]->getShareCount() + 1;
                $movie[0]->setShareCount($shareCount);
            }

        }else{

            $url =  "https://api.themoviedb.org/3/movie/".$id."?api_key=c89646cb9c2f9f7a6144c074fff0e9c7&language=en-US";

            $response = $this->client->request(
                'GET',
                $url
            );


            $film = $response->toArray();

            $movie = new Movie();
            $movie->setName($film['title']);
            $date =  new \DateTime($film['release_date']);
            $movie->setOverview($film['overview']);
            $movie->setReleaseDate($date);
            $movie->setThemoviedbId($film['id']);

            $movie->setShareCount(1);

            $this->entityRepository->persist($movie);

        }

        $this->entityRepository->flush();


        $this->mailer->send($email);
    }

}