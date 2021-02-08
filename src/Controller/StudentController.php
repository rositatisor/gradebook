<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Student;
use App\Entity\Lecture;
use App\Entity\Grade;

class StudentController extends AbstractController
{
    /**
     * @Route("/student", name="student_index", methods={"GET"})
     */
    public function index(request $r): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
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
     * @Route("/student/{id}", name="student_view", methods={"POST"})
     */
    public function view(request $r, int $id): Response
    {
        $student = $this->getDoctrine()
            ->getRepository(Student::class)
            ->find($id);

        $lectures = $this->getDoctrine()
            ->getRepository(Lecture::class)
            ->findBy([],['name'=>'asc']);

        $grades = $this->getDoctrine()
            ->getRepository(Grade::class);
            if ($r->query->get('student_id') !== null && $r->query->get('student_id') != 0) 
                $grades = $grades->findBy(['student_id' => $r->query->get('student_id')]);
            elseif ($r->query->get('student_id') == 0) $grades = $grades->findAll();
            else $grades = $grades->findAll();

        return $this->render('student/view.html.twig', [
            'grades' => $grades,
            'lectures' => $lectures,
            'student' => $student
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
            'student_phone' => $student_phone[0] ?? '',
            'errors' => $r->getSession()->getFlashBag()->get('errors', [])
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

        $errors = $validator->validate($student);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $r->getSession()->getFlashBag()->add('errors', $error->getMessage());
            }
            $r->getSession()->getFlashBag()->add('student_name', $r->request->get('student_name'));
            $r->getSession()->getFlashBag()->add('student_surname', $r->request->get('student_surname'));
            $r->getSession()->getFlashBag()->add('student_email', $r->request->get('student_email'));
            $r->getSession()->getFlashBag()->add('student_phone', $r->request->get('student_phone'));
            return $this->redirectToRoute('student_create');
        }

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
            'student_phone' => $student_phone[0] ?? '',
            'errors' => $r->getSession()->getFlashBag()->get('errors', [])
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

        $errors = $validator->validate($student);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $r->getSession()->getFlashBag()->add('errors', $error->getMessage());
            }
            $r->getSession()->getFlashBag()->add('student_name', $r->request->get('student_name'));
            $r->getSession()->getFlashBag()->add('student_surname', $r->request->get('student_surname'));
            $r->getSession()->getFlashBag()->add('student_email', $r->request->get('student_email'));
            $r->getSession()->getFlashBag()->add('student_phone', $r->request->get('student_phone'));
            return $this->redirectToRoute('student_edit', ['id'=>$student->getId()]);
        }

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
