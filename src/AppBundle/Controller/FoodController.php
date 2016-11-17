<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Food;
use AppBundle\Form\FoodType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/settings/food")
 */
class FoodController extends Controller
{
    /**
     * @Route("/new", name="food_new")
     */
    public function newAction(Request $request)
    {
        $food = new Food();

        $form = $this->createForm(FoodType::class, $food);
        $form->handleRequest($request);
        if ($form->isValid())
        {
            $food->setUser($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($food);
            $em->flush();

            $this->addFlash('success', 'Your food has been added.');

            return $this->redirectToRoute('settings');
        }

        return $this->render('ratio/form.html.twig', [
            'form' => $form->createView(),
            'food' => $food
        ]);
    }


    /**
     * @Route("/{id}/edit", name="food_edit")
     * @Security("user == food.getUser()")
     *
     * @param Food $food
     * @return Response
     */
    public function editAction(Request $request, Food $food)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(FoodType::class, $food);
        $form->handleRequest($request);
        if ($form->isValid())
        {
            $em->persist($food);
            $em->flush();

            $this->addFlash('success', 'Your food has been updated.');

            return $this->redirectToRoute('settings');
        }

        return $this->render('ratio/form.html.twig', [
            'form' => $form->createView(),
            'food' => $food
        ]);
    }
}
