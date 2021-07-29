<?php

namespace App\Service;

trait UserAuth
{
    protected string $user_id;

    public function getUserId(): string
    {
        return $this->user_id;
    }

    public function setUserId(string $user_id): void
    {
        $this->user_id = $user_id;
    }
}