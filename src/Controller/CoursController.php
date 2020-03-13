<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\Semestre;
use App\Form\CoursForm;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CoursController extends AbstractController {

    private $_em;

    public function __construct(ManagerRegistry $em) {
        $this->_em = $em;
    }

    /**
     * @Route("/", name="index")
     */
    public function index() {
        return $this->redirectToRoute("cours");
    }

    /**
     * @Route("/cours/list", name="cours")
     */
    public function cours() {

        $cours_repository = $this->_em->getRepository(Cours::class);
        $cours = $cours_repository->findAll();

        return $this->render('cours/index.html.twig',
            [
                'cours' => $cours
            ]);
    }

    /**
     * @Route("/cours/cours_by_semestre", name="cours_by_semestre")
     */
    public function cours_by_semestre() {

        $semestre_repository = $this->_em->getRepository(Semestre::class);
        $cours_repository = $this->_em->getRepository(Cours::class);

        $semestres = $semestre_repository->findAll();
        $cours = array();

        foreach ($semestres as $semestre) {
            $cours[$semestre->getName()] = $cours_repository->findBy(['semestre' => $semestre->getId()]);
        }

        $empty = $cours_repository->findBy(['semestre' => null]);
        if (!empty($empty)) {
            $cours["Aucun semestre"] = $cours_repository->findBy(['semestre' => null]);
        }

        return $this->render('cours/by_semestre.html.twig',
            [
                'cours' => $cours
            ]);
    }

    /**
     * @Route("/cours/{id}", name="cours_details", requirements={"id"="\d+"})
     */
    public function cours_details(Cours $cours) {

        //Symfony automatically get the corresponding Cours
//        $cours = $this->_em->getRepository(Cours::class)->find($id);

        return $this->render('cours/cours_details.html.twig',
            [
                'cours' => $cours
            ]);
    }

    /**
     * @Route("/cours/new", name="cours_new", methods={"GET","POST"})
     */
    public function cours_new(Request $request) : Response
    {
        $cours = new Cours();
        $form = $this->createForm(CoursForm::class, $cours);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cours);
            $entityManager->flush();

            return $this->render('cours/cours_details.html.twig',
                [
                    'cours' => $cours
                ]
            );
        }

        return $this->render('cours/cours_new.html.twig',
            [
                'cours' => $cours,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/cours/edit/{id}", name="cours_edit", methods={"GET","POST"})
     */
    public function cours_edit(Request $request, Cours $cours): Response
    {
        $form = $this->createForm(CoursForm::class, $cours);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->render('cours/cours_details.html.twig',
                [
                    'cours' => $cours
                ]);
        }

        return $this->render('cours/cours_edit.html.twig', [
            'cour' => $cours,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/cours/delete/{id}", name="cours_delete", methods={"DELETE"})
     */
    public function cours_delete(Request $request, Cours $cours): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cours->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($cours);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cours');
    }
}
