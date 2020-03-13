<?php

namespace App\Controller;

use App\Entity\Semestre;
use App\Form\SemestreForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SemestreController extends AbstractController
{
    /**
     * @Route("/semestre/new", name="semestre_new", methods={"GET","POST"})
     */
    public function semestre_new(Request $request) : Response
    {
        $semestre = new Semestre();
        $form = $this->createForm(SemestreForm::class, $semestre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($semestre);
            $entityManager->flush();

            return $this->redirectToRoute('cours_by_semestre');
        }

        return $this->render('semestre/semestre_new.html.twig',
            [
                'semestre' => $semestre,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/semestre/edit/{id}", name="semestre_edit", methods={"GET","POST"})
     */
    public function semestre_edit(Request $request, Semestre $semestre): Response {

        $clean_cours = $this->getDoctrine()->getRepository(Semestre::class)->find($semestre->getId());
        foreach ($clean_cours as $cours) {
            $semestre->removeCour($cours);
        }

        $form = $this->createForm(SemestreForm::class, $semestre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cours_by_semestre');
        }

        return $this->render('semestre/semestre_edit.html.twig', [
            'semestre' => $semestre,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/semestre/delete/{id}", name="semestre_delete", methods={"DELETE"})
     */
    public function cours_delete(Request $request, Semestre $semestre): Response
    {
        if ($this->isCsrfTokenValid('delete'.$semestre->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($semestre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cours_by_semestre');
    }
}
