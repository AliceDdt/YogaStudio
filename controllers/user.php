<?php

require_once 'libraries/utils.php';
require_once 'models/user.php';
require_once 'models/reservation.php';


function index(){
        //if user is logged, template is the profil page
        if(isconnected()){
            //retrieve user info to display into the profil page
            $userInfos = findUser($_SESSION['user']['Id']);
            
            /*if user already has made yogaclass reservation, we need to display booking info into user's profile
            first we verify if user has already booked, then we retrieve booking info*/
            $userBooking = has_user_booked($_SESSION['user']['Id']);           
            $bookingDetails = findUserReservation(intval($_SESSION['user']['Id']));

           renderPage('profil', compact('userInfos', 'userBooking', 'bookingDetails'));
           
        }
        //if user is not logged, template is login page
        else{
            renderPage('login');
        }    
}
//Login process
function login(){

    //login form processing
    if(!empty($_POST)){
    
        //if required inputs are empty, we display error message
        if(empty($_POST['email']) 
            || empty($_POST['password']))
        {
            addFlashMsg('error', 'Un des champs est vide');
            redirect('login');
        }
       
        //retrieve user info using $_POST['email'], returns true or false
        $user = is_user_exists($_POST['email']);
        
        //if user does not exist, redirect to login page
        if(!$user){
            addFlashMsg('error', 'Veuillez créer un compte');
            redirectBack();            
        }
        
        //verify if password is correct, if not : redirect to login page
        if(!verifyPassword($_POST['password'], $user['Password'])) {
            addFlashMsg('error', 'Email ou mot de passe incorrect! ');
            redirectBack();
        }

        //create a SESSION variable 
        $_SESSION['user']=[
            'Id' => intval($user['Id']),
            'LastName' => htmlspecialchars($user['LastName']),
            'FirstName' => htmlspecialchars($user['FirstName']),
            'Email' => htmlspecialchars($user['Email']),
            'Role' => htmlspecialchars($user['Role_Id'])
        ];

        //every time user connects, update last login date
        updateLastLoginUser($_SESSION['user']['Id']);
        
        //if User is admin, redirect to Admin dashboard
        if(isAdmin()){
            redirect('http://localhost/yogaStudio/admin');
        }
        //else redirect to home index
        else{
            redirectBack();;
        }
    }

}

// Create new account
function register(){

    //create account form processing
    if (!empty($_POST)) {

        //if required inputs are empty, we display error message
        if (empty($_POST['email'])
        || empty($_POST['lastName'])
        || empty($_POST['firstName'])
        || empty($_POST['password'])
        || empty($_POST['password2'])
        || empty($_POST['address'])
        || empty($_POST['zipcode']) 
        || empty($_POST['city'])
        || empty($_POST['phone'])){

            addFlashMsg('error', 'Champs obligatoires non remplis !');
            redirectBack();
        }

        //checking if user's email already exists in the database
        if(is_user_exists($_POST['email'])){
            addFlashMsg('error','l\'email existe déjà !');
            redirectBack();
        }
    
        //verify firstname, lastname and email
        identityValidation($_POST['firstName'], $_POST['lastName'], $_POST['email']);
        
        //verify password
        passwordValidation($_POST['password'], $_POST['password2']);
    
     
        //if input address2 is empty, its value will be null
        $adress2 = strip_tags($_POST['address2']);
        if($adress2 == ""){
            $adress2 = NULL;
        }

        //encrypt the pssword before the insert into database
        $pwd_encrypted = encryptPassword($_POST['password']);

        //insert User in database
        insertNewUser(strip_tags($_POST['lastName']), 
                    strip_tags($_POST['firstName']),
                    strip_tags($_POST['address']), 
                    $adress2,
                    strip_tags($_POST['city']),
                    strip_tags($_POST['zipcode']), 
                    strip_tags($_POST['phone']),
                    strip_tags($_POST['email']), 
                    $pwd_encrypted);

        //redirect to login.php
        addFlashMsg('success','Compte créé <br> Veuillez vous identifier!');
        redirect('http://localhost/yogaStudio/user');
    }
    //show template
    else {
        renderPage('register');
    }
}

//Update user profil
function update($userId){

    //if user is not connected, redirect to login page
    if(!isset($_SESSION['user'])){
        redirect('http://localhost/yogaStudio/user');
    }
    else{
        //update profil form processing
        if (!empty($_POST)) {

            //user can update his/her profil without changing his/her password, so we set value to empty
            $pwd_encrypted = '';
            $queryPwd = '';

            //if required inputs are empty, we display error message
            if (empty($_POST['email'])
            || empty($_POST['lastName'])
            || empty($_POST['firstName'])
            || empty($_POST['address'])
            || empty($_POST['zipcode']) 
            || empty($_POST['city'])
            || empty($_POST['phone'])){

                addFlashMsg('error', 'Champs obligatoires non remplis !');
                redirectBack();
            }

            //verify firstname, lastname and email
            identityValidation($_POST['firstName'], $_POST['lastName'], $_POST['email']);
        
            //if input address2 is empty, its value will be null
            $adress2 = strip_tags($_POST['address2']);
            if($adress2 == ""){
                $adress2 = NULL;
            }

            //if User modifies his/her password
            if(!empty($_POST['password']) && !empty($_POST['password2'])){

                passwordValidation($_POST['password'], $_POST['password2']);

                //encrypt the password before the insert into database
                $pwd_encrypted = encryptPassword($_POST['password']);
                $queryPwd = ', Password = ?';

            }

            //create array for the update
            $dataUpdate = [strip_tags($_POST['lastName']), 
                            strip_tags($_POST['firstName']),
                            strip_tags($_POST['address']), 
                            $adress2,
                            strip_tags($_POST['city']),
                            strip_tags($_POST['zipcode']), 
                            strip_tags($_POST['phone']),
                            strip_tags($_POST['email'])];

            //if user has modified his/her password, new password is pushed into $dataUpdate array
            if($pwd_encrypted !==''){
                array_push($dataUpdate, $pwd_encrypted);
            }
            
            //then we push $userId into $dataUpdate array
            array_push($dataUpdate, intval($_SESSION['user']['Id']));
            
            //proceed to update User into database
            $result = updateUser($queryPwd, $dataUpdate);
            
            //if success
            if($result){
                //update $_SESSION info
                $_SESSION['user']['LastName'] = htmlspecialchars($_POST['lastName']);
                $_SESSION['user']['FirstName'] = htmlspecialchars($_POST['firstName']);
                $_SESSION['user']['Email'] = htmlspecialchars($_POST['email']);
                
                addFlashMsg('success', 'Mise à jour réussie !');
                redirect('http://localhost/yogaStudio/user');

            }else{
                addFlashMsg('error', 'Un problème est survenu !');
                redirectBack();
            }
        
        }else{
            //retrieve User info to display into template edit profil
            $profil = findUser(intval($_SESSION['user']['Id']));
            renderPage('edit_profil', compact('profil'));
        }
    }
}

//Disconnect user
function logout(){
    //clear array $_SESSION
    $_SESSION =[];

    //destroy session
    session_destroy();

    //redirect to index
    redirect('http://localhost/yogaStudio/home');
}


/*
Only used inside user controller
this function verify password
@params string password and password 2
@returns void
*/
function passwordValidation(string $password, string $password2):void
{
    //checking if password contains 8 letters including at least one capital letter, one number and one special character
    if(!preg_match('/^(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])(?=.*\W+)[a-zA-Z0-9\W+]{8,}$/', $password)){
        addFlashMsg('error', 'Le mot de passe doit contenir au moins 8 caractères dont 1 chiffre, 1 majuscule et 1 caractère spécial');
        redirectBack();
    }
   
    // checking if the 2 passwords are identicals
    if($password != $password2) {
       addFlashMsg('error', 'les deux mots de passe ne correspondent pas');
    redirectBack();
    }
}


/*
Only used inside user controller
this function verify if last name and first name are not digits and if email is valid
@params string $firstname, string $lastname, string $email
@returns void
*/
function identityValidation(string $firstname, string $lastname, string $email): void
{
    //checking if firstname or lastname are not digits
    if(ctype_digit($firstname) || ctype_digit($lastname)){
        addFlashMsg('error', 'nom et/ou prénom ne peuvent pas être numériques !');
        redirectBack();
    }

    //checking email format
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        addFlashMsg('error', 'l\'email n\'est pas au bon format !');
        redirectBack();
    }           
}