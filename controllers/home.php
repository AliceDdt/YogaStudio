<?php

require_once 'models/course.php';
require_once 'models/teacher.php';
require_once 'models/yogaclass.php';
require_once 'models/reservation.php';
require_once 'libraries/utils.php';

//show index page
function index(){
    $courses = findAllCourses();
    $teachers = findAllTeachers();
    renderPage('index', compact('courses', 'teachers'));
}

//show course page
function course(){
    $courses = findAllCourses();
    renderPage('yogatype', compact('courses'));
}

//show teacher page
function teacher(){
    $teachers = findAllTeachers();
    renderPage('teacher', compact('teachers'));
}
