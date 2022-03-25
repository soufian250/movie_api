<?php

namespace App;

use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ShareEmailService
{
    private $mailer;
    private $entityRepository;

    public function __construct(MailerInterface $mailer,EntityManagerInterface $entityRepository)
    {
        $this->mailer = $mailer;
        $this->entityRepository = $entityRepository;
    }

    public function share($id){

        $movie_link = 'https://www.imdb.com/title/tt1596345/?ref_=vp_back';

        $email = (new Email())
            ->from('learndevsho@gmail.com')
            ->to('soufianjill@gmail.com')
            ->subject('Check out this link on Themoviedb!')
            ->text('Text body')
            ->html('<p>Check This movie</p> <a href="'.$movie_link.'">'.$id.'</a>');

        if ($id){

            $movie = $this->entityRepository->getRepository(Movie::class)->find($id);

            if ($movie){
                $shareCount = $movie->getShareCount() + 1;
                $movie->setShareCount($shareCount);

                $this->entityRepository->flush();
            }
        }

        $this->mailer->send($email);
    }

}