<?php

namespace AppBundle\Controller;

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

        $form = $this->createForm(EntryType::class, $entry);

        $form->handleRequest($request);
        if($form->isValid()) {

            $ratio = $form->get('ratio')->getData();

            $entry->setRatio($ratio->getCarbs());
            $entry->setName($ratio->getName());
            $entry->setUnits();

            if ($request->request->get('save')) {

                $em = $this->getDoctrine()->getManager();
                $em->persist($entry);
                $em->flush();

                $this->addFlash('success', 'Your entry has been saved.');

                return $this->redirectToRoute('entry_index');
            }
        }

        return $this->render('entry/new.html.twig', [
            'form' => $form->createView(),
            'entry' => $entry,
        ]);
    }
}
