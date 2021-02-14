![Homework](https://img.shields.io/badge/PHP-application-blue)
[![HitCount](http://hits.dwyl.com/rositatisor/gradebook.svg)](http://hits.dwyl.com/rositatisor/gradebook)

# Library
BIT task: to create the Gradebook application with PHP Symfony framework.
The teacher can write grades for students (only teacher is able to insert and review information, students do not have access to the system). The teacher must register the students first (insert their names and surnames) and enter lectures (insert the title and description of the lecture), then he must be able to select the student and assign a grade for him.

**This repository is for educational porpuses only.**

# Detailed task
1. Create the structure in the MySQL database as in the example below. 

grades | lectures | students | users
------------ | ------------- | ------------- | -------------
id: int(11) <br> lecture_id: int(11) <br> student_id : int(11) <br> grade : int(11) | id: int(11) <br> name: varchar(64) <br> description: varchar(64) | id: int(11) <br> name: varchar(64) <br> surname: varchar(64) <br> email: varchar(64) <br> phone: varchar(32) | id: int(11) <br> name: varchar(64) <br> email: email(64) <br> pass: password(128) 

If necessary, there is possibility to add more fields, as well as adjust existing fields.

2. There must be an opportunity to add, edit and delete students and lectures. All students and lectures must be showed as following: 
- students sorted by surname,
- lectures sorted by name.

3. Create the grade assessment system. After selecting assessment, the system must display two selection lists: student and lecture, and one input field - grade. After selecting student, lecture and entering the grade, it must appear in the database.

4. Create a student grade review page. In the list of students, add the review button next to each student, which when clicked would display all the grades of that student listed in the table: lecture name, grade.

5. Create registration, login, logout and so only logged users are able to work with application.

6. Use WYSIWYG type editor for book about section.

Notes:

* Application must have responsive design.
* Input fields must be validated to prevent SQL injections and forms must use CSRF protection.

## Preview
<img width="550" alt="Capture" src="https://raw.githubusercontent.com/rositatisor/gradebook/master/assets/img/screenshot.PNG">

### Authors 
[Rosita](https://github.com/rositatisor)
