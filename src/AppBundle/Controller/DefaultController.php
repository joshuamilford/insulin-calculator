<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Entry;
use AppBundle\Entity\Tag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {

//        $em = $this->getDoctrine()->getManager();
//        $entry = $this->getDoctrine()->getRepository(Entry::class)
//            ->find(1395);
//        $tag = $this->getDoctrine()->getRepository(Tag::class)
//            ->find(1);
//
//        $entry->addTag($tag);
//        $em->persist($entry);
//        $em->flush();
//
//        dump($tag);
//        die(dump($entry));

        return $this->render('default/index.html.twig');
    }
}