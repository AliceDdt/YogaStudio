<?php

require_once 'models/model.php';

/* 
this function inserts a new reservation into the table 'Reservation'
@params $userId
@returns string Last Insert ID 
*/
function insertReservation(int $userId): int
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "INSERT INTO Reservation (User_Id) VALUES (:userId)");

    $query->execute([':userId' => $userId]);
    return $pdo->lastInsertId();
}

/*
this function inserts the reservations details using lastInsertId from function insertReservation
@params int idBooking, int $idYogaclass
@returns bool 
*/ 
function insertDetail(int $idBooking, int $yogaclassId): bool
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "INSERT INTO Reservation_details (Reservation_Id, Yogaclass_Id) VALUES (:idBooking, :yogaclassId)");

    return $query->execute(compact('idBooking','yogaclassId'));
}

/* 
this function retrieves user's booking information using userId
@params int userId
@returns array
*/
function findUserReservation(int $userId): array
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "SELECT DATE_FORMAT(Created_at, '%d/%m/%Y %H:%i') as Date_Creation, Reservation_details.Reservation_Id,DATE_FORMAT(Yogaclass.Date, '%d/%m/%Y') as Date, DATE_FORMAT(Yogaclass.Time, '%H:%i') as Time, Yogaclass.Price, Course.Name
        FROM Reservation 
        INNER JOIN Reservation_details ON Reservation.Id = Reservation_details.Reservation_Id 
        INNER JOIN Yogaclass ON Reservation_details.Yogaclass_Id = Yogaclass.Id 
        INNER JOIN Course ON Course.Id = Yogaclass.Course_Id 
        WHERE Reservation.User_Id = :userId AND Yogaclass.Date > NOW()
        ORDER BY Yogaclass.Date ASC");

    $query->execute([':userId' => $userId]);    

    return $query->fetchAll(PDO::FETCH_ASSOC);
}

/* 

this function finds all the reservations made by users
@returns array 
*/
function findAllReservation(): array
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "SELECT Reservation.Id, Reservation.User_Id, DATE_FORMAT(Reservation.Created_at, '%d/%m/%Y %H:%i') as Date, User.FirstName, User.LastName
        FROM Reservation
        INNER JOIN User ON User.Id = Reservation.User_Id
        ORDER BY Reservation.Created_at DESC");

    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

/* 
this function finds the yogaclass Id's booked by the user
@params int $userId
@returns array 
*/
function findBookedClass(int $userId): array
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "SELECT Yogaclass_Id
        FROM Reservation_details
        INNER JOIN Reservation ON Reservation.Id =  Reservation_details.Reservation_Id
        WHERE User_Id = :id");
    
    $query->execute([':id' => $userId]);
    return $query->fetchAll(PDO::FETCH_ASSOC);
}


/*
this function finds user in table 'Reservation'
@params int $userId
@returns array | bool | null
*/
function has_user_booked(int $userId)
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "SELECT User_Id
        FROM Reservation
        WHERE User_Id = :id");
    
    $query->execute([':id' => $userId]);
    return $query->fetch(PDO::FETCH_ASSOC);
}


/*
this function finds Details info about a reservation in table 'Reservation'
@params int $reservationId
@returns array
*/
function findDetailsReservation(int $id): array
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "SELECT Reservation.Id, Reservation.User_Id, DATE_FORMAT(Yogaclass.Date, '%d/%m/%Y') as Date, DATE_FORMAT(Yogaclass.Time, '%H:%i') as Time, Yogaclass.Price, Course.Name, Reservation_details.Yogaclass_Id
        FROM Reservation
        INNER JOIN Reservation_details ON Reservation.Id = Reservation_details.Reservation_Id 
        INNER JOIN Yogaclass ON Reservation_details.Yogaclass_Id = Yogaclass.Id 
        INNER JOIN Course ON Course.Id = Yogaclass.Course_Id 
        WHERE Reservation.Id = :id");

    $query->execute([':id' => $id]);    

    return $query->fetchAll(PDO::FETCH_ASSOC);
}

/* 
this function deletes one reservation from table Reservation_details
@params int $yogaclassId + $reservationId
@returns bool
*/
function deleteOneReservation(int $yogaclassId, int $resId): bool
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "DELETE FROM Reservation_details
        WHERE Yogaclass_Id = :yogaclassId AND Reservation_Id = :resId ");

    return $query->execute(compact('yogaclassId', 'resId'));
}

/*
this function searches if the reservation Id given in parameter still exists in the table Reservation_details
@params int $reservationId
@returns  array | bool | null
*/
function is_reservation_exists(int $id)
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "SELECT Reservation_Id
        FROM Reservation_details
        WHERE Reservation_Id = :id");

    $query->execute([':id' => $id]);    

    return $query->fetch(PDO::FETCH_ASSOC);
}


/*
this function retrieves the number of reservation
@returns array
*/
function nbReservation(): array
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "SELECT COUNT(Reservation_Id) as Booking
        FROM Reservation_details");

    $query->execute();    

    return $query->fetch(PDO::FETCH_ASSOC);
}
