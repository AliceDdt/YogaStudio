<?php

require_once 'models/model.php';

/* 
this function inserts a new course into the table 'Course'
@params $_POST[datas]
@returns bool
*/
function insertNewCourse(string $name, string $shortDescript, string $longDescript): bool
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "INSERT INTO Course
        (Name, Short_description, Long_description)
        VALUES (:name, :shortDescript, :longDescript)");

    return $query->execute(compact('name', 'shortDescript', 'longDescript'));       
}


/* 
this function finds all courses in the table 'Course'
@returns array 
*/
function findAllCourses(): array
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "SELECT Id, Name, Short_description, Long_description
        FROM Course
        ORDER BY Id ASC");
    

    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}


/* 
this function finds a course into the table 'Course' by its id
@params integer $courseId
@returns array|bool|null
*/
function findCourse(int $courseId)
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "SELECT Id, Name, Short_description, Long_Description
        FROM Course
        WHERE Id = :id");

    $query->execute([':id' => $courseId]);

    return $query->fetch(PDO::FETCH_ASSOC);
}


/* 
this function updates a course into the table 'Course' 
@params $_POST[datas] + integer $courseId
@returns bool 
*/
function updateCourse(int $courseId, string $name, string $shortDescript, string $longDescript): bool
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "UPDATE Course
        SET Name = :name, Short_description = :shortDescript, Long_Description = :longDescript
        WHERE Id = :courseId");

    return $query->execute(compact('name', 'shortDescript', 'longDescript', 'courseId'));
}



/*
this function retrieves the course that has the highest number of reservation
@returns
*/
function findMostBookedCourse(): array
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "SELECT Course.Id, Course.Name, SUM(Yogaclass.Nb_Booking) as Total
        FROM Course
        INNER JOIN Yogaclass ON Yogaclass.Course_Id = Course.Id
        WHERE Yogaclass.Nb_Booking = (
                SELECT MAX(Yogaclass.Nb_Booking)
                FROM Yogaclass)
        ");

    $query->execute();

    return $query->fetch(PDO::FETCH_ASSOC);
}
