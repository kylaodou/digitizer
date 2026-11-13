<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * La racine redirige le visiteur vers la page d'accueil.
     */
    public function test_the_root_redirects_guests_to_the_home_page(): void
    {
        $this->get('/')->assertRedirect(route('home'));
    }
}
