<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Lecture;

class LectureController extends AbstractController
{
    /**
     * @Route("/lecture", name="lecture_index", methods={"GET"})
     */
    public function index(request $r): Response
    {
        $lectures = $this->getDoctrine()
            ->getRepository(Lecture::class)
            ->findAll();

        return $this->render('lecture/index.html.twig', [
            'controller_name' => 'LectureController',
            'lectures' => $lectures
        ]);
    }

    /**
     * @Route("/lecture/create", name="lecture_create", methods={"GET"})
     */
    public function create(request $r): Response
    {
        $lecture_name = $r->getSession()->getFlashBag()->get('lecture_name', []);
        $lecture_description = $r->getSession()->getFlashBag()->get('lecture_description', []);

        return $this->render('lecture/create.html.twig', [
            'lecture_name' => $lecture_name[0] ?? '',
            'lecture_description' => $lecture_description[0] ?? ''
        ]);
    }

    /**
     * @Route("/lecture/store", name="lecture_store", methods={"POST"})
     */
    public function store(request $r, ValidatorInterface $validator): Response
    {
        $lecture = new Lecture;
        $lecture
            ->setName($r->request->get('lecture_name'))
            ->setDescription($r->request->get('lecture_description'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($lecture);
        $entityManager->flush();

        return $this->redirectToRoute('lecture_index');
    }

    /**
     * @Route("/lecture/edit/{id}", name="lecture_edit", methods={"GET"})
     */
    public function edit(int $id, request $r): Response
    {
        $lecture = $this->getDoctrine()
            ->getRepository(Lecture::class)
            ->find($id);

        $lecture_name = $r->getSession()->getFlashBag()->get('lecture_name', []);
        $lecture_description = $r->getSession()->getFlashBag()->get('lecture_description', []);

        return $this->render('lecture/edit.html.twig', [
            'lecture' => $lecture,
            'lecture_name' => $lecture_name[0] ?? '',
            'lecture_description' => $lecture_description[0] ?? ''
        ]);
    }

    /**
     * @Route("/lecture/update/{id}", name="lecture_update", methods={"POST"})
     */
    public function update(request $r, $id, ValidatorInterface $validator): Response
    {
        $lecture = $this->getDoctrine()
            ->getRepository(Lecture::class)
            ->find($id);

        $lecture
            ->setName($r->request->get('lecture_name'))
            ->setDescription($r->request->get('lecture_description'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($lecture);
        $entityManager->flush();

        return $this->redirectToRoute('lecture_index');
    }

    /**
     * @Route("/lecture/delete/{id}", name="lecture_delete", methods={"POST"})
     */
    public function delete($id): Response
    {
        $lecture = $this->getDoctrine()
            ->getRepository(Lecture::class)
            ->find($id);
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($lecture);
        $entityManager->flush();

        return $this->redirectToRoute('lecture_index');
    }

}
