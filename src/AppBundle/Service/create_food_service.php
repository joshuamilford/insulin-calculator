<?php
/**
 * Created by PhpStorm.
 * User: joshuamilford
 * Date: 11/18/16
 * Time: 7:31 PM
 */

namespace AppBundle\Service;


use Doctrine\ORM\EntityManager;

class create_food_service
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function create()
    {
        die(dump($this->em));
    }
}