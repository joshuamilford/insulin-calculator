<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\CorrectionFactor;
use AppBundle\Form\CorrectionFactorType;

/**
 * @Route("settings/correction_factor")
 */
class CorrectionFactorController extends Controller
{
    /**
     * @Route("/new", name="correction_factor_new")
     */
    public function newAction(Request $request)
    {
        $correctionFactor = new CorrectionFactor();

        $form = $this->createForm(CorrectionFactorType::class, $correctionFactor);
        $form->handleRequest($request);
        if($form->isValid()) {
            $correctionFactor->setUser($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($correctionFactor);
            $em->flush();

            $this->addFlash('success', 'Your correction factor has been added.');

            return $this->redirectToRoute('settings');

        }

        return $this->render('correction_factor/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="correction_factor_edit")
     * @Security("user == correctionFactor.getUser()")
     */
    public function editAction(Request $request, CorrectionFactor $correctionFactor)
    {
        $form = $this->createForm(CorrectionFactorType::class, $correctionFactor);

        $form->handleRequest($request);
        if($form->isValid()) {

            $correctionFactor->setUser($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($correctionFactor);
            $em->flush();

            $this->addFlash('success', 'Your correction factor has been updated.');

            return $this->redirectToRoute('settings');

        }

        return $this->render('correction_factor/form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
