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
        $lectures = $this->getDoctrine()
            ->getRepository(Lecture::class)
            ->findBy([],['name'=>'asc']);

        $students = $this->getDoctrine()
            ->getRepository(Student::class)
            ->findBy([],['surname'=>'asc']);

        $grades = $this->getDoctrine()
            ->getRepository(Grade::class)
            ->findAll();

        return $this->render('grade/index.html.twig', [
            'controller_name' => 'GradeController',
            'grades' => $grades,
            'lectures' => $lectures,
            'students' => $students
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
            'grade_lecture_id' => $grade_grade_id[0] ?? ''
        ]);
    }

    /**
     * @Route("/grade/store", name="grade_store", methods={"POST"})
     */
    public function store(request $r, ValidatorInterface $validator): Response
    {
        $grade = new Grade;
        $grade
            ->setGrade($r->request->get('grade_grade'))
            ->setStudentId($r->request->get('grades_student'))
            ->setLectureId($r->request->get('grades_lecture'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($grade);
        $entityManager->flush();

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
        ]);
    }

    /**
     * @Route("/grade/update/{id}", name="grade_update", methods={"POST"})
     */
    public function update(request $r, $id, ValidatorInterface $validator): Response
    {
        $grade = $this->getDoctrine()
            ->getRepository(Grade::class)
            ->find($id);
        
        $grade
            ->setGrade($r->request->get('grade_grade'))
            ->setStudentId($r->request->get('grades_student'))
            ->setLectureId($r->request->get('grades_lecture'));
            
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($grade);
        $entityManager->flush();

        return $this->redirectToRoute('grade_index');
    }

    /**
     * @Route("/grade/delete/{id}", name="grade_delete", methods={"POST"})
     */
    public function delete($id): Response
    {
        $grade = $this->getDoctrine()
            ->getRepository(Grade::class)
            ->find($id);
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($grade);
        $entityManager->flush();

        return $this->redirectToRoute('grade_index');
    }

}
