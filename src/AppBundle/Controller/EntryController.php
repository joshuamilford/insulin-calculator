<?php

namespace AppBundle\Controller;

use AppBundle\Entity\EntryFood;
use AppBundle\Entity\Food;
use AppBundle\Entity\Tag;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
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
        $entries = $this->getDoctrine()->getRepository('AppBundle:Entry')
            ->findBy(
                ['user' => $this->getUser()],
                ['createdAt' => 'DESC']
            );

        return $this->render('entry/index.html.twig', [
            'entries' => $entries
        ]);
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
        $em = $this->getDoctrine()->getManager();

        $originalEntryFoods = new ArrayCollection();
        foreach ($entry->getEntryFoods() as $entryFood) {
            $originalEntryFoods->add($entryFood);
        }

        $form = $this->createForm(EntryType::class, $entry, ['em' => $em, 'user' => $this->getUser()]);

        $form->handleRequest($request);
        if($form->isValid()) {

            $ratio = $form->get('ratio')->getData();
            if ($ratio) {
                $entry->setRatio($ratio->getCarbs());
                $entry->setName($ratio->getName());
            }
            $entry->setUnits();

            if ($form->get('save')->isClicked()) {

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

    /**
     * @Route("/tag/{id}", name="entry_tag")
     * @param Tag $tag
     * @return Response
     */
    public function entryTagAction(Tag $tag)
    {
        return $this->render('entry/index.html.twig', [
            'entries' => $tag->getEntries()
        ]);
    }
}
