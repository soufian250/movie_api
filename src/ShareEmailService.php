<?php

namespace App;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ShareEmailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function share(){

        $movie_link = 'https://www.imdb.com/title/tt1596345/?ref_=vp_back';

        $email = (new Email())
            ->from('learndevsho@gmail.com')
            ->to('soufianjill@gmail.com')
            ->subject('Check out this link on Themoviedb!')
            ->text('Text body')
            ->html('<p>Check This movie</p> <a href="'.$movie_link.'">'.$movie_link.'</a>');


        $this->mailer->send($email);
    }

}