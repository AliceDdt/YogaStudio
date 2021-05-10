<?php

require_once 'libraries/utils.php';
require_once 'models/yogaclass.php';
require_once 'models/reservation.php';

/*this function shows cart's user. 
If he/she has added yogaclass to cart, we display the added yogaclass info */
function index(){
    if(!isset($_SESSION['booking'])){
            $_SESSION['booking'] = [];      
        }

    if(!empty($_SESSION['booking'])){
        $keys = array_keys($_SESSION['booking']);
        $yogaclass = findBookingClass(implode(',', $keys));
        renderPage('cart', compact('yogaclass'));
    }
    else{
        renderPage('cart');
    }
}



//Validation booking process
function validate(){

    //if user not connected, redirect to login page
    if(!isConnected()){
        addFlashMsg('error', 'Veuillez vous connecter pour réserver');
        redirect('http://localhost/projet_mvc/user');
    }

    //before proceeding to insertion, we need to verify if the user has already booked a yogaclass
    $idUserReservation = has_user_booked($_SESSION['user']['Id']);

    //if javascript is enabled, we retrieve booking info via JSON file
    if(!empty($_POST['cart'])){
        $bookings = json_decode($_POST['cart'], true);
    
        //if user is unknown, it means we can proceed to booking
        if(!$idUserReservation){ 
            validateJSONBooking($_SESSION['user']['Id'], $bookings);
        }

        //if user is known in database
        if(in_array($_SESSION['user']['Id'], $idUserReservation)){

            //we need to verify the yogaclasses he/she already booked, so we retrieve booking info using userId
            $BookedClass = findBookedClass($_SESSION['user']['Id']);

            /*we compare the yogaclassId he/she already booked with the yogaclassId he wants to book*/        
            foreach($BookedClass as $item){  
                foreach($bookings as $booking){   
                    if($item['Yogaclass_Id'] == $booking['id']){
                        //if he/she already booked this yogaclass we display an error message
                        addFlashMsg('error', 'Vous avez déjà réservé cette/ces séance(s) ! <br> Veuillez consulter les réservations dans votre profil');
                        redirectBack();
                    }
                }  
            }  

            //if not we proceed to booking
            validateJSONBooking($_SESSION['user']['Id'], $bookings);
        }
    }

    //if javascript is deactivated, we use $_SESSION['booking'] to get booking info;
    if(!empty($_SESSION['booking'])){

        //if user is unknown, it means we can proceed to booking
        if(!$idUserReservation){ 
            validateBooking($_SESSION['user']['Id'], $_SESSION['booking']);       
        }

        //if user is known in database
        if(in_array($_SESSION['user']['Id'], $idUserReservation)){

            //we need to verify the yogaclasses he/she already booked, so we retrieve booking info using userId
            $BookedClass = findBookedClass($_SESSION['user']['Id']);
            
            /*we compare the yogaclassId he/she already booked with the yogaclassId he wants to book*/        
            foreach($BookedClass as $item){               
                if(in_array($item['Yogaclass_Id'], $_SESSION['booking'])){
                    //if he/she already booked this yogaclass we display an error message
                    addFlashMsg('error', 'Vous avez déjà réservé cette/ces séance(s) ! <br> Veuillez consulter les réservations dans votre profil');
                    $_SESSION['booking']= [];
                    redirectBack();
                }
            }

            //if not we proceed to booking
            validateBooking($_SESSION['user']['Id'], $_SESSION['booking']);
        }
    }
}  

//Remove a yogaclass from cart before cart validation
function remove($yogaclassId){

    if(isset($_SESSION['booking'])){
        foreach($_SESSION['booking'] as $keys){
            if ($keys == $yogaclassId){
                unset($_SESSION['booking'][$keys]);
            }
        }
        redirectBack();
    }
}

/* IF JAVASCRIPT DEACTIVATED => this function inserts booking info into tables 'Reservation' and 'Reservation_details'
it is only used inside controller 'cart.php'
@param: int $userId + array $sessionBooking
@returns void */
function validateBooking(int $userId, array $sessionBooking): void
{  
    //insert reservation into table Reservation
    $bookingId = insertReservation($userId);

    //insert details reservation : each yogaclass Id added to cart
    foreach($sessionBooking as $values){
        $insert = insertDetail($bookingId, intval($values));
        $update = updateNbBooking(intval($values), 1);
    }

    //if insertion is successful, display success message, else error messsage
    if($insert && $update){
        $_SESSION['booking']= [];
        renderPage('cart_end');
    }else{
        addFlashMsg('error', 'la réservation n\'a pas aboutie !');
        redirectBack();
    }
}

/*WORKS WITH JAVASCRIPT => this function inserts booking info into tables 'Reservation' and 'Reservation_details'
it is only used inside controller 'cart.php'
@param: int $userId + array $sessionBooking
@returns void */
function validateJSONBooking(int $userId, array $sessionBooking): void
{
    $bookingId = insertReservation($userId);

    //insert details reservation : each yogaclass Id added to cart
    foreach($sessionBooking as $item){
        $itemId = intval($item['id']);
        
        $insert = insertDetail($bookingId, $itemId);
        $update = updateNbBooking(intval($item['id']), 1);
    }
        
    if($insert && $update){
        renderPage('cart_end');
    }else{
        addFlashMsg('error', 'la réservation n\'a pas aboutie !');
        redirectBack();
    }
}