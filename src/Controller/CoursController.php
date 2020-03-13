<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\Semestre;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
