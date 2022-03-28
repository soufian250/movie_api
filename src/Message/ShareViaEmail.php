<?php


namespace App\Message;

class ShareViaEmail
{
    private $email;
    private $movieLink;



    public function __construct(string $emil,string $movieLink)
    {
        $this->email = $emil;
        $this->movieLink = $movieLink;
    }


    public function getEmail(): string
    {
        return $this->email;
    }

    public function getMovieLink(): string
    {
        return $this->movieLink;
    }
}
