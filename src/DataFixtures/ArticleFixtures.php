<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();
        //créer 3 catégories
        for ($i = 1; $i <= 3; $i++) {
            $category = new Category();
            $category->setTitle($faker->sentence())
                     ->setDescription($faker->paragraph());
            
            $manager->persist($category);
            //créer 4 à 6 articles par catégorie
            for ($j = 1; $j <= mt_rand(4, 6); $j++){
                $article = new Article();
                // paragraphs return array so change this into string for content
                $content = '<p>' . join($faker->paragraphs(5), '</p><p>') . '</p>';

                $article->setTitle($faker->sentence())
                        ->setContent($content) 
                        ->setImage($faker->imageUrl())
                        ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                        ->setCategory($category);
    
                $manager->persist($article);

                //création de 4 à 10 commentaires par article
                for($k = 1; $k <= mt_rand(4, 10); $k++) {
                    $comment = new Comment();

                    $content = '<p>' . join($faker->paragraphs(2), '</p><p>') . '</p>';

                    // algo pour eviter d'avoir des comments apparus avant la parution de l'article
                    $now = new \DateTime();
                    $interval = $now->diff($article->getCreatedAt())->days;
                    $min = '-' . $interval . ' days'; // pour injecter dans le dateTimeBetween

                    $comment->setAuthor($faker->name)
                            ->setContent($content)
                            ->setCreatedAt($faker->dateTimeBetween($min))
                            ->setArticle($article);
                    
                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
    }
}
