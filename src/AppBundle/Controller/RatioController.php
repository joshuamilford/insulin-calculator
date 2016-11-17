<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\RatioType;
use AppBundle\Entity\Ratio;

/**
 * @Route("/settings/ratio")
 */
class RatioController extends Controller
{
    /**
     * @Route("/new", name="ratio_new")
     */
    public function newAction(Request $request)
    {
        $ratio = new Ratio();

        $form = $this->createForm(RatioType::class, $ratio);
        $form->handleRequest($request);
        if ($form->isValid())
        {
            $ratio->setUser($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($ratio);
            $em->flush();

            $this->addFlash('success', 'Your ratio has been added.');

            return $this->redirectToRoute('settings');
        }

        return $this->render('ratio/form.html.twig', [
            'form' => $form->createView(),
            'ratio' => $ratio
        ]);
    }


    /**
     * @Route("/{id}/edit", name="ratio_edit")
     * @Security("user == ratio.getUser()")
     *
     * @param Ratio $ratio
     * @return Response
     */
    public function editAction(Request $request, Ratio $ratio)
    {

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('Gedmo\Loggable\Entity\LogEntry'); // we use default log entry class
        $logs = $repo->getLogEntries($ratio);

        $form = $this->createForm(RatioType::class, $ratio);
        $form->handleRequest($request);
        if ($form->isValid())
        {
            $ratio->setUser($this->getUser());

            $em->persist($ratio);
            $em->flush();

            $this->addFlash('success', 'Your ratio has been updated.');

            return $this->redirectToRoute('settings');
        }

        return $this->render('ratio/form.html.twig', [
            'form' => $form->createView(),
            'ratio' => $ratio,
            'logs' => $logs
        ]);
    }
}
