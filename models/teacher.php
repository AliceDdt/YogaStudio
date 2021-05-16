<?php

require_once 'models/model.php';

/*
 this function inserts a new teacher into the table 'User' with the Role Id 2 
@params $_POST[datas] + $userId
@returns string Last Insert ID 
*/
function insertNewTeacher(string $lastName,
                            string $firstName,
                            string $address,
                            ?string $address2,
                            string $city,
                            string $zipcode,
                            string $phone,
                            string $email,
                            string $bio) {

    $pdo = dbConnexion();
    $query = $pdo->prepare(
    "INSERT INTO User
    (LastName , FirstName, Address1, Address2, City, ZipCode, Phone, Email, Description, Role_Id)
    VALUES (:lastName, :firstName, :address, :address2, :city, :zipcode, :phone, :email, :bio, 2);");

    $query->execute(compact('lastName', 'firstName', 'address', 'address2', 'city', 'zipcode', 'phone', 'email', 'bio'));

    return $pdo->lastInsertId();
}


/* 
this function finds all teachers in the table 'User'
@returns array 
*/
function findAllTeachers(): array
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "SELECT Id, LastName , FirstName, Picture, Description
        FROM User
        WHERE Role_Id = '2'
        ORDER BY Id ASC");
    
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}


/* 
this function finds a teacher into the table 'User' by his/her id
@params integer $userId
@returns array|bool|null 
*/
function findTeacher(int $userId){
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "SELECT Id, LastName, FirstName, Address1, Address2, City, ZipCode, Phone, Email, Picture, Description
        FROM User
        WHERE Id = :id");

    $query->execute([':id' => $userId]);

    return $query->fetch(PDO::FETCH_ASSOC);
}


/* 
this function updates a teacher into the table 'User' 
@params $_POST[datas] + integer $userId
@returns bool 
*/
function updateTeacher( 
                        string $lastName,
                        string $firstName,
                        string $address,
                        ?string $address2,
                        string $city,
                        string $zipcode,
                        string $phone,
                        string $email,
                        string $bio,
                        int $userId): bool
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "UPDATE User
        SET LastName = :lastName, FirstName = :firstName, Address1 = :address, Address2 = :address2, City = :city, ZipCode = :zipcode, Phone = :phone, Email = :email, Description = :bio
        WHERE Id = :userId");

    return $query->execute(compact('lastName','firstName', 'address', 'address2', 'city', 'zipcode', 'phone', 'email', 'bio', 'userId'));   
}


/* 
this function updates a teacher's picture into the table 'User' 
@params string $filename + integer $userId
@returns void 
*/
function updatePicture(int $userId, string $filename): void
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "UPDATE User
        SET Picture = :filename
        WHERE Id = :userId");

    $query->execute(compact('filename', 'userId'));   
}


/* 
this function finds the filename of teacher's picture into table User
@params int $userId
@returns array|bool|null 
*/
function findPicture(int $userId)
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "SELECT Picture FROM User
        WHERE Id = :userId");

    $query->execute(compact('userId'));

    return $query->fetch(PDO::FETCH_ASSOC);
}