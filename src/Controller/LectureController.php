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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $lectures = $this->getDoctrine()
            ->getRepository(Lecture::class);
            if ($r->query->get('sort') == 'name_az') $lectures = $lectures->findBy([], ['name' => 'asc']);
            elseif ($r->query->get('sort') == 'name_za') $lectures = $lectures->findBy([], ['name' => 'desc']);
            else $lectures = $lectures->findAll();

        return $this->render('lecture/index.html.twig', [
            'controller_name' => 'LectureController',
            'lectures' => $lectures,
            'sortBy' => $r->query->get('sort') ?? 'default',
            'success' => $r->getSession()->getFlashBag()->get('success', []),
            'errors' => $r->getSession()->getFlashBag()->get('errors', [])
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
            'lecture_description' => $lecture_description[0] ?? '',
            'errors' => $r->getSession()->getFlashBag()->get('errors', [])
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

        $errors = $validator->validate($lecture);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $r->getSession()->getFlashBag()->add('errors', $error->getMessage());
            }
            $r->getSession()->getFlashBag()->add('lecture_name', $r->request->get('lecture_name'));
            $r->getSession()->getFlashBag()->add('lecture_description', $r->request->get('lecture_description'));
            return $this->redirectToRoute('lecture_create');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($lecture);
        $entityManager->flush();

        $r->getSession()->getFlashBag()->add('success', $lecture->getName().' was created.');

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
            'lecture_description' => $lecture_description[0] ?? '',
            'errors' => $r->getSession()->getFlashBag()->get('errors', []),
            'success' => $r->getSession()->getFlashBag()->get('success', [])
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

        $errors = $validator->validate($lecture);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $r->getSession()->getFlashBag()->add('errors', $error->getMessage());
            }
            $r->getSession()->getFlashBag()->add('lecture_name', $r->request->get('lecture_name'));
            $r->getSession()->getFlashBag()->add('lecture_description', $r->request->get('lecture_description'));
            return $this->redirectToRoute('lecture_edit', ['id'=>$lecture->getId()]);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($lecture);
        $entityManager->flush();

        $r->getSession()->getFlashBag()->add('success', $lecture->getName().' was updated.');

        return $this->redirectToRoute('lecture_index');
    }

    /**
     * @Route("/lecture/delete/{id}", name="lecture_delete", methods={"POST"})
     */
    public function delete(request $r, int $id): Response
    {
        $lecture = $this->getDoctrine()
            ->getRepository(Lecture::class)
            ->find($id);

        if ($lecture->getGrades()->count() > 0) {
            $r->getSession()->getFlashBag()->add('errors', 'Selected lecture '.$lecture->getName().' cannot be deleted ('.$lecture->getGrades()->count().' grade/-s assigned).');
            return $this->redirectToRoute('lecture_index');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($lecture);
        $entityManager->flush();

        $r->getSession()->getFlashBag()->add('success', $lecture->getName().' was successfully deleted.');

        return $this->redirectToRoute('lecture_index');
    }

}
