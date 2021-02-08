<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Student;

class StudentController extends AbstractController
{
    /**
     * @Route("/student", name="student_index", methods={"GET"})
     */
    public function index(request $r): Response
    {
        $students = $this->getDoctrine()
            ->getRepository(Student::class);
            if ($r->query->get('sort') == 'name_az') $students = $students->findBy([], ['name' => 'asc', 'surname' => 'asc']);
            elseif ($r->query->get('sort') == 'name_za') $students = $students->findBy([], ['name' => 'desc']);
            elseif ($r->query->get('sort') == 'surname_az') $students = $students->findBy([], ['surname' => 'asc']);
            elseif ($r->query->get('sort') == 'surname_za') $students = $students->findBy([], ['surname' => 'desc']);
            else $students = $students->findAll();

        return $this->render('student/index.html.twig', [
            'controller_name' => 'StudentController',
            'students' => $students,
            'sortBy' => $r->query->get('sort') ?? 'default',
        ]);
    }

    /**
     * @Route("/student/create", name="student_create", methods={"GET"})
     */
    public function create(request $r): Response
    {
        $student_name = $r->getSession()->getFlashBag()->get('student_name', []);
        $student_surname = $r->getSession()->getFlashBag()->get('student_surname', []);
        $student_email = $r->getSession()->getFlashBag()->get('student_email', []);
        $student_phone = $r->getSession()->getFlashBag()->get('student_phone', []);

        return $this->render('student/create.html.twig', [
            'student_name' => $student_name[0] ?? '',
            'student_surname' => $student_surname[0] ?? '',
            'student_email' => $student_email[0] ?? '',
            'student_phone' => $student_phone[0] ?? ''
        ]);
    }

    /**
     * @Route("/student/store", name="student_store", methods={"POST"})
     */
    public function store(request $r, ValidatorInterface $validator): Response
    {
        $student = new Student;
        $student
            ->setName($r->request->get('student_name'))
            ->setSurname($r->request->get('student_surname'))
            ->setEmail($r->request->get('student_email'))
            ->setPhone($r->request->get('student_phone'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($student);
        $entityManager->flush();

        return $this->redirectToRoute('student_index');
    }

    /**
     * @Route("/student/edit/{id}", name="student_edit", methods={"GET"})
     */
    public function edit(int $id, request $r): Response
    {
        $student = $this->getDoctrine()
            ->getRepository(Student::class)
            ->find($id);

        $student_name = $r->getSession()->getFlashBag()->get('student_name', []);
        $student_surname = $r->getSession()->getFlashBag()->get('student_surname', []);
        $student_email = $r->getSession()->getFlashBag()->get('student_email', []);
        $student_phone = $r->getSession()->getFlashBag()->get('student_phone', []);

        return $this->render('student/edit.html.twig', [
            'student' => $student,
            'student_name' => $student_name[0] ?? '',
            'student_surname' => $student_surname[0] ?? '',
            'student_email' => $student_email[0] ?? '',
            'student_phone' => $student_phone[0] ?? ''
        ]);
    }

    /**
     * @Route("/student/update/{id}", name="student_update", methods={"POST"})
     */
    public function update(request $r, $id, ValidatorInterface $validator): Response
    {
        $student = $this->getDoctrine()
            ->getRepository(Student::class)
            ->find($id);

        $student
            ->setName($r->request->get('student_name'))
            ->setSurname($r->request->get('student_surname'))
            ->setEmail($r->request->get('student_email'))
            ->setPhone($r->request->get('student_phone'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($student);
        $entityManager->flush();

        return $this->redirectToRoute('student_index');
    }

    /**
     * @Route("/student/delete/{id}", name="student_delete", methods={"POST"})
     */
    public function delete($id): Response
    {
        $student = $this->getDoctrine()
            ->getRepository(Student::class)
            ->find($id);
        
        if ($student->getGrades()->count() > 0) {
            return new Response('Selected Student (#id: '.$student->getId().') can not be deleted, because '.$student->getName().' has '.$student->getGrades()->count().' grades.');
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($student);
        $entityManager->flush();

        return $this->redirectToRoute('student_index');
    }
}
