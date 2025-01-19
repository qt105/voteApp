<?php

namespace App\VoteApp;

class ClassB
{
    public const C = 100;

    /**
     * @var float $nb Un nombre dony j'ai besoindans mon app
     */
    private float $nb;

    /**
     * Une fonction qui calcule la racine carrée d'un nombre
     *
     * @param int $x Un nombre
     *
     * @return float
     */
    public function f(int $x): float
    {
        $this->nb = $x ** 0.5;
    
        return $this->nb;
    }
}
