<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MovieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $movie = new Movie();
        $movie->setTitle("The Dark Knight");
        $movie->setReleaseYear(2008);
        $movie->setDescription("This is the description of the Dark Knight");
        $movie->setImagePath("https://images.unsplash.com/photo-1531259683007-016a7b628fc3?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8YmF0bWFufGVufDB8fDB8fHww");
        // Add Data To Pivot Table
        $movie->addActor($this->getReference("actor_1"));
        $movie->addActor($this->getReference("actor_2"));

        $manager->persist($movie); // add this object to the query

        $movie2 = new Movie();
        $movie2->setTitle("Avengers End Game");
        $movie2->setReleaseYear(2019);
        $movie2->setDescription("This is the description of the Avengers End Game");
        $movie2->setImagePath("https://images.unsplash.com/photo-1626278664285-f796b9ee7806?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MjB8fGF2ZW5nZXJzfGVufDB8fDB8fHww");

        // Add Data To Pivot Table
        $movie2->addActor($this->getReference("actor_3"));
        $movie2->addActor($this->getReference("actor_4"));

        $manager->persist($movie2); // add this object to the query

        $manager->flush(); // all queries will be performed at the same time


    }
}
