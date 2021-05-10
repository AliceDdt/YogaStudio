<?php
//function connects to the database
function dbConnexion(){

    $host = 'mysql-alicedau.alwaysdata.net';
    $dbName = 'alicedau_yogastudio';
    $login = 'alicedau';
    $password = 'Dreamfrog35!';
    $connexion = False;

    if(!$connexion){

        try{
            $pdo = new PDO (
            'mysql:host='.$host.';dbname='.$dbName.';charset=utf8', 
            $login, 
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
            );
            return $pdo;
        }
        catch(PDOException $error){
            $code = $error->getCode();
            $message = $error->getMessage();
           // echo "Erreur de connexion: ".$error->getMessage();
           renderError($code, $message);
        }
    }
}

/*
this function deletes row identified by given id from table also given in parameters 
@params int $id + string $table = indication where to delete
@returns bool 
*/
function delete(int $id, string $table): bool
{
        $pdo = dbConnexion();
        $query = $pdo->prepare(
            "DELETE FROM $table
            WHERE Id = :id");
    
        return $query->execute(['id' => $id]);
}


