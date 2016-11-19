<?php

namespace AppBundle\Controller;

use AppBundle\Entity\EntryFood;
use AppBundle\Entity\Food;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Entry;
use AppBundle\Form\EntryType;

/**
 * @Route("entry")
 * @package AppBundle\Controller
 */
class EntryController extends Controller
{
    /**
     * @Route("/", name="entry_index"))
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('entry/index.html.twig');
    }

    /**
     * @Route("/new", name="entry_new")
     */
    public function newAction(Request $request)
    {

        $entry = new Entry();
        $entry->setUser($this->getUser());

        return $this->save($request, $entry);
    }

    /**
     * @Route("/{id}/edit", name="entry_edit")
     * @Security("user == entry.getUser()")
     */
    public function editAction(Request $request, Entry $entry)
    {
        return $this->save($request, $entry);
    }

    private function save(Request $request, Entry $entry)
    {

        $originalEntryFoods = new ArrayCollection();
        foreach ($entry->getEntryFoods() as $entryFood) {
            $originalEntryFoods->add($entryFood);
        }

        $form = $this->createForm(EntryType::class, $entry);

        $form->handleRequest($request);
        if($form->isValid()) {

            $ratio = $form->get('ratio')->getData();

            $entry->setRatio($ratio->getCarbs());
            $entry->setName($ratio->getName());
            $entry->setUnits();

            if ($request->request->get('save')) {

                $em = $this->getDoctrine()->getManager();

                $data = $form->getData();
                foreach ($data->getEntryFoods() as $food) {
                    if (null === $food->getFood() && !empty($food->getNewFood())) {
                        $newFood = new Food();
                        $newFood->setCarbs($food->getCarbs());
                        $newFood->setName($food->getNewFood());
                        $newFood->setUser($this->getUser());
                        $food->setFood($newFood);
                        $em->persist($newFood);
                    }
                }

                foreach ($originalEntryFoods as $entryFood) {
                    if (false === $entry->getEntryFoods()->contains($entryFood)) {
                        $em->remove($entryFood);
                    }
                }

                $em->persist($entry);
                $em->flush();

                $this->addFlash('success', 'Your entry has been saved.');

                return $this->redirectToRoute('entry_index');
            }
        }

        return $this->render('entry/new.html.twig', [
            'form' => $form->createView(),
            'entry' => $entry,
            'foodList' => json_encode($this->getDoctrine()->getRepository('AppBundle:Food')->findAll())
        ]);
    }
}
