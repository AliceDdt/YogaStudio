<?php

require_once 'models/model.php';

/* 
this function inserts a new user into the table 'User' 
@params $_POST[datas]
@returns void
*/
function insertNewUser(
                        string $lastName,
                        string $firstName,
                        string $address1,
                        ?string $address2,
                        string $city,
                        string $zipcode,
                        string $phone,
                        string $email,
                        string $password): void
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "INSERT INTO User
        (LastName , FirstName, Address1, Address2, City, ZipCode, Phone, Email, Password)
        VALUES (:lastName, :firstName, :address1, :address2, :city, :zipcode, :phone, :email, :password)");


    //execute request
    $query->execute(compact('lastName', 'firstName', 'address1', 'address2', 'city', 'zipcode', 'phone', 'email', 'password'));
}


/*
this function finds user in database using email given in parameter
@params string $email
@returns array with user's data 
*/
function is_user_exists(string $email) {
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "SELECT Id, LastName , FirstName, Email, Password, Role_Id
        FROM User
        WHERE Email = :email 
        LIMIT 0,1");

    $query->execute([':email' => $email]);

    return $query->fetch(PDO::FETCH_ASSOC);   
}


/* 
Update user's last login date in database when he/she logs in 
@params integer $id
@returns void
*/
function updateLastLoginUser(int $userId): void
{
    $pdo = dbConnexion();
    $query = $pdo->prepare("UPDATE User SET Last_Login = NOW() WHERE id = :id");
    
    $query->execute([':id' => $userId]);
}


/* 
this function finds a user into the table 'User' by his/her id
@params integer $userId
@returns array|bool|null 
*/
function findUser(int $userId){
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "SELECT Id, FirstName, LastName, Address1, Address2, City, ZipCode, Phone, Email
        FROM User
        WHERE Id = :id 
        LIMIT 0,1");
    
    $query->execute([':id' => $userId]);

    return $query->fetch(PDO::FETCH_ASSOC);
}


/* 
this function finds all users in the table 'User'
@returns array 
*/
function findAllUsers(): array
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "SELECT Id, FirstName, LastName, Address1, Address2, City, ZipCode, Phone, Email, Created_at, Last_login
        FROM User
        WHERE Role_Id = 1");

    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}


/* 
this function updates user in the table 'User'
@params string $request (if password is updated or not) + array $data (info to update)
@returns bool 
*/
function updateUser(string $request, array $data): bool
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        'UPDATE User
        SET LastName = ?, FirstName = ?, Address1 =?, Address2 = ?, City = ?, ZipCode = ?, Phone = ?, Email = ?'.$request.
        'WHERE Id = ?');

    return $query->execute($data);
}


/*
this function retrieves the number of user registered on  website since 01/01/2021
@returns array 
*/
function nbUser(): array
{
    $pdo = dbConnexion();
    $query = $pdo->prepare(
        "SELECT COUNT(Id) as nb_user
        FROM User
        WHERE Role_Id = 1 AND DATE_FORMAT(Created_at, '%Y') >= 2021");

    $query->execute();    

    return $query->fetch(PDO::FETCH_ASSOC);
}