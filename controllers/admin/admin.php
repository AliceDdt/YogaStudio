<?php

require_once 'libraries/utils.php';
require_once 'models/reservation.php';
require_once 'models/user.php';
require_once 'models/course.php';

//Show Dashboard with some statistics info
function index(){

    $booking = nbReservation();
    $user = nbUser();
    $course = findMostBookedCourse();

    renderPageAdmin('dashboard', compact('booking', 'user', 'course'));
}

