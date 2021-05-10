<?php

require_once 'libraries/utils.php';
require_once 'models/reservation.php';
require_once 'models/user.php';
require_once 'models/yogaclass.php';
require_once 'models/course.php';

//Show Dashboard with some statistics info
function index(){

    $booking = nbReservation();
    $user = nbUser();
    $yogaclass = findyogaClassesAdmin();
    $course = findMostBookedCourse();

    renderPageAdmin('dashboard', compact('booking', 'user', 'yogaclass', 'course'));
}

