<?php

require_once 'models/model.php';

/* 
this function inserts a new yogaClass into the table 'Yogaclass'
@params $_POST[datas]
@returns bool
*/
function insertNewClass(int $courseId, int $userId, $date, $time, int $nbSeat, int $price): bool
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "INSERT INTO Yogaclass
        (Date, Time, Course_Id, Number_participants, User_Id, Price)
        VALUES (:date, :time, :courseId, :nbSeat, :userId, :price)");

    return $query->execute(compact('date', 'time', 'courseId', 'nbSeat', 'userId', 'price')); 
}


/* 
this function finds all yogaClasses in the table 'Yogaclass'
@returns array 
*/
function findAllClasses(): array
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "SELECT Yogaclass.Id, DATE_FORMAT(Yogaclass.Date, '%d/%m/%Y') as Date, DATE_FORMAT(Yogaclass.Time, '%H:%i') as Time, Yogaclass.Price, Yogaclass.Number_participants-Yogaclass.Nb_booking as Nb_seats, Course.Name as Name, User.FirstName
        FROM Yogaclass
        INNER JOIN Course ON Course.Id = Yogaclass.Course_Id
        INNER JOIN User ON User.Id = Yogaclass.User_Id
        WHERE Yogaclass.Date > NOW()
        ORDER BY Yogaclass.Date ASC");
    
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}


/* 
this function finds a yogaClass in the table 'Yogaclass' by its id
@params integer $id
@returns array | bool | null 
*/
function findClass(int $id)
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "SELECT Yogaclass.Id, Yogaclass.Date, DATE_FORMAT(Yogaclass.Time, '%H:%i') as Time, Yogaclass.Number_participants, Yogaclass.Price, Course.Name, User.FirstName, User.LastName
        FROM Yogaclass
        INNER JOIN Course ON Course.Id = Yogaclass.Course_Id
        INNER JOIN User ON User.Id = Yogaclass.User_Id
        WHERE Yogaclass.Id = :id");

    $query->execute([':id' => $id]);

    return $query->fetch(PDO::FETCH_ASSOC);
}


/* 
this function updates a yogaclass into the table 'Yogaclass' 
@params $_POST[datas] + integer $yogaclassId
@returns bool 
*/
function updateClass(int $id, int $courseId, int $userId, $date, $time, int $nbSeat, int $price)
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "UPDATE Yogaclass
        SET Date = :date, Time = :time, Course_Id = :courseId, Number_participants = :nbSeat, User_Id = :userId, Price = :price
        WHERE Id = :id"
    );
    
    return $query->execute(compact('id','date', 'time', 'courseId', 'nbSeat', 'userId', 'price'));
}


/*
User can book several yogaclasses at one time
this function finds all yogaclasses that user booked into the table 'Yogaclass' 
@params string $sql that contains the yogaclass Id's 
@returns array 
*/
function findBookingClass($sql): array
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "SELECT Yogaclass.Id, DATE_FORMAT(Yogaclass.Date, '%d/%m/%Y') as Date, DATE_FORMAT(Yogaclass.Time, '%H:%i') as Time, Yogaclass.Price, Course.Name 
        FROM Yogaclass
        INNER JOIN Course ON Course.Id = Yogaclass.Course_Id
        WHERE Yogaclass.Id IN ($sql)");

    $query->execute();

    return $query->fetchAll(PDO::FETCH_ASSOC);

}

/*
this function update column 'Nb_booking' when user books a yogaclass
@params int $yogaclassId
@returns bool
*/
function updateNbBooking(int $id, $nb): bool
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
    "UPDATE Yogaclass
    SET Nb_booking = Nb_booking  + $nb
    WHERE Yogaclass.Id = :id");
    
    return $query->execute([':id' => $id]);
}

/*
This function retrieves number of seats left for the yogaclass given in parameter
@params int $yogaclassId
@returns array | bool | null
*/
function nbSeatsLeft(int $id)
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "SELECT Yogaclass.Number_participants-Yogaclass.Nb_booking as Nb_seats
        FROM Yogaclass
        WHERE Yogaclass.Id = :id");
    
    $query->execute([':id' => $id]);
    return $query->fetch(PDO::FETCH_ASSOC);
}


/*
This function retrieves the 5 next yogaclasses for the admin dashboard
@returns array
*/
function findyogaClassesAdmin(): array
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "SELECT DATE_FORMAT(Yogaclass.Date, '%d/%m/%Y') as Date, DATE_FORMAT(Yogaclass.Time, '%H:%i') as Time, Yogaclass.Nb_booking, Course.Name as Name, User.FirstName
        FROM Yogaclass
        INNER JOIN Course ON Course.Id = Yogaclass.Course_Id
        INNER JOIN User ON User.Id = Yogaclass.User_Id
        WHERE Yogaclass.Date > NOW() 
        ORDER BY Yogaclass.Date ASC
        LIMIT 5");
    
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}