<?php


namespace App\Message;

class ShareViaEmail
{
    private $movieId;


    public function __construct(int $movieId)
    {
        $this->movieId = $movieId;
    }


    public function getId(): int
    {
        return $this->movieId;
    }
}
