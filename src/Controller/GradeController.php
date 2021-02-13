<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Lecture;
use App\Entity\Student;
use App\Entity\Grade;

class GradeController extends AbstractController
{
    /**
     * @Route("/grade", name="grade_index")
     */
    public function index(request $r): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $lectures = $this->getDoctrine()
            ->getRepository(Lecture::class)
            ->findBy([],['name'=>'asc']);

        $students = $this->getDoctrine()
            ->getRepository(Student::class)
            ->findBy([],['surname'=>'asc']);

        $grades = $this->getDoctrine()
            ->getRepository(Grade::class);
            if ($r->query->get('student_id') !== null && $r->query->get('student_id') != 0) 
                $grades = $grades->findBy(['student_id' => $r->query->get('student_id')]);
            elseif ($r->query->get('student_id') == 0) $grades = $grades->findAll();
            else $grades = $grades->findAll();

        return $this->render('grade/index.html.twig', [
            'controller_name' => 'GradeController',
            'studentId' => $r->query->get('student_id') ?? 0,
            'grades' => $grades,
            'lectures' => $lectures,
            'students' => $students,
            'success' => $r->getSession()->getFlashBag()->get('success', [])
        ]);
    }

    /**
     * @Route("/grade/create", name="grade_create", methods={"GET"})
     */
    public function create(request $r): Response
    {
        $lectures = $this->getDoctrine()
            ->getRepository(Lecture::class)
            ->findBy([],['name'=>'asc']);

        $students = $this->getDoctrine()
            ->getRepository(Student::class)
            ->findBy([],['surname'=>'asc']);

        $grade_grade = $r->getSession()->getFlashBag()->get('grade_grade', []);

        return $this->render('grade/create.html.twig', [
            'lectures' => $lectures,
            'students' => $students,
            'grade_grade' => $grade_grade[0] ?? '',
            'grade_student_id' => $grade_student_id[0] ?? '',
            'grade_lecture_id' => $grade_grade_id[0] ?? '',
            'errors' => $r->getSession()->getFlashBag()->get('errors', [])
        ]);
    }

    /**
     * @Route("/grade/store", name="grade_store", methods={"POST"})
     */
    public function store(request $r, ValidatorInterface $validator): Response
    {
        $student = $this->getDoctrine()
            ->getRepository(Student::class)
            ->find($r->request->get('grades_student'));

        $lecture = $this->getDoctrine()
            ->getRepository(Lecture::class)
            ->find($r->request->get('grades_lecture'));

        if($student == null) $r->getSession()->getFlashBag()->add('errors', 'Student must be selected.');
        if($lecture == null) $r->getSession()->getFlashBag()->add('errors', 'Lecture must be selected.');

        $grade = new Grade;
        $grade
            ->setGrade((int)$r->request->get('grade_grade'))
            ->setStudent($student)
            ->setLecture($lecture);

        $errors = $validator->validate($grade);
        if (count($errors) > 0 or $lecture == null or $student == null) {
            foreach ($errors as $error) {
                $r->getSession()->getFlashBag()->add('errors', $error->getMessage());
            }
            $r->getSession()->getFlashBag()->add('grade_grade', $r->request->get('grade_grade'));
            $r->getSession()->getFlashBag()->add('grade_student_id', $r->request->get('grade_student_id'));
            $r->getSession()->getFlashBag()->add('grade_lecture_id', $r->request->get('grade_lecture_id'));

            return $this->redirectToRoute('grade_create');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($grade);
        $entityManager->flush();

        $r->getSession()->getFlashBag()->add('success', 'Grade '.$grade->getGrade().' from '.$grade->getLecture()->getName().' for '.$grade->getStudent()->getName().' '.$grade->getStudent()->getSurname().' was created.');

        return $this->redirectToRoute('grade_index');
    }

    /**
     * @Route("/grade/edit/{id}", name="grade_edit", methods={"GET"})
     */
    public function edit(int $id, request $r): Response
    {
        $grade = $this->getDoctrine()
            ->getRepository(Grade::class)
            ->find($id);

        $lectures = $this->getDoctrine()
            ->getRepository(Lecture::class)
            ->findBy([],['name'=>'asc']);

        $students = $this->getDoctrine()
            ->getRepository(Student::class)
            ->findBy([],['surname'=>'asc']);

        return $this->render('grade/edit.html.twig', [
            'grade' => $grade,
            'lectures' => $lectures,
            'students' => $students,
            'errors' => $r->getSession()->getFlashBag()->get('errors', []),
            'success' => $r->getSession()->getFlashBag()->get('success', [])
        ]);
    }

    /**
     * @Route("/grade/update/{id}", name="grade_update", methods={"POST"})
     */
    public function update(request $r, $id, ValidatorInterface $validator): Response
    {
        $student = $this->getDoctrine()
            ->getRepository(Student::class)
            ->find($r->request->get('grades_student'));

        $lecture = $this->getDoctrine()
            ->getRepository(Lecture::class)
            ->find($r->request->get('grades_lecture'));

        $grade = $this->getDoctrine()
            ->getRepository(Grade::class)
            ->find($id);
        
        $grade
            ->setGrade($r->request->get('grade_grade'))
            ->setStudent($student)
            ->setLecture($lecture);

        $errors = $validator->validate($grade);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $r->getSession()->getFlashBag()->add('errors', $error->getMessage());
            }
            return $this->redirectToRoute('grade_edit', ['id'=>$grade->getId()]);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($grade);
        $entityManager->flush();

        $r->getSession()->getFlashBag()->add('success', 'Grade '.$grade->getGrade().' for '.$grade->getStudent()->getName().' '.$grade->getStudent()->getSurname().' was updated.');

        return $this->redirectToRoute('grade_index');
    }

    /**
     * @Route("/grade/delete/{id}", name="grade_delete", methods={"POST"})
     */
    public function delete(request $r, int $id): Response
    {
        $grade = $this->getDoctrine()
            ->getRepository(Grade::class)
            ->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($grade);
        $entityManager->flush();

        $r->getSession()->getFlashBag()->add('success', 'Grade '.$grade->getGrade().' from '.$grade->getLecture()->getName().' for '.$grade->getStudent()->getName().' '.$grade->getStudent()->getSurname().' was deleted.');

        return $this->redirectToRoute('grade_index');
    }

}
