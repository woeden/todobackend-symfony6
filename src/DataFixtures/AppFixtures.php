<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use App\Entity\Todo;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $todo = new Todo();
        $todo->setTitle('Lorem ipsum');

        $tag = new Tag();
        $tag->setTitle("dolor");

        $todo->addTag($tag);

        $manager->persist($tag);
        $manager->persist($todo);

        $manager->flush();
    }
}
