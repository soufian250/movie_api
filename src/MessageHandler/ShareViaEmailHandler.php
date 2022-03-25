<?php


namespace App\MessageHandler;

use App\Message\ShareViaEmail;
use App\Repository\MovieRepository;
use App\ShareEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Email;


class ShareViaEmailHandler implements MessageHandlerInterface
{

    private $em;
    private $shareEmailService;

    public function __construct(EntityManagerInterface $em, ShareEmailService  $shareEmailService)
    {
        $this->em = $em;
        $this->shareEmailService = $shareEmailService;
    }

    public function __invoke(ShareViaEmail $message)
    {
        $movie_link = 'https://www.imdb.com/title/tt1596345/?ref_=vp_back';


        $this->shareEmailService->share($message);

    }
}