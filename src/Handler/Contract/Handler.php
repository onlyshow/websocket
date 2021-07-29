<?php

namespace App\Handler\Contract;

interface Handler
{
    public function handle(array $data);
}