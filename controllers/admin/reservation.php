<?php

require_once 'libraries/utils.php';
require_once 'models/reservation.php';
require_once 'models/yogaclass.php';

//Show all the reservation made by users
function index(){
    $reservation = findAllReservation();
    renderPageAdmin('reservation/list', compact('reservation'));

}

//show Booking details of each reservation : you can see each yogaclass user has booked per reservation 
function details($resaId){
    
    //we retrieve last parameter of url path to have the booking Id
    $id = basename($_GET['p']);
    
    if(is_numeric($resaId)){
        //we retrieve booking info with reservation Id
        $details = findDetailsReservation(intval($resaId));

        if(empty($details)){
            addFlashMsg('error', 'aucune réservation correspondante trouvée');
            redirect('http://localhost/yogaStudio/reservation');
        }
        else{
        renderPageAdmin('reservation/details', compact('id', 'details'));}
    }   
    else {
        throw new Exception('Impossible d\'afficher la page !', 4);
    }
}

//Delete a booking
function deleteBooking(){

    //we delete the reservation and update the number of booking for the relevant yogaclass
    $result = deleteOneReservation(intval($_POST['yogaclass_id']), intval($_POST['resa_id']));
    updateNbBooking(intval($_POST['yogaclass_id']), -1);

    /*once the reservation is deleted, we need to verify if the user had booked several yogaclasses 
    in one reservation or if he has only booked one class. 
    So we verify if the reservation Id still exists or not (table 'Reservation_details') */
    if($result){
        $booking = is_reservation_exists(intval($_POST['resa_id']));

        /*if the reservation Id does not exist, it means that user had booked just one yogaclass 
        so we can proceed to deleting his/her entire reservation (table 'Reservation')*/
        if(!$booking){
            delete(intval($_POST['resa_id']), 'Reservation');
            addFlashMsg('success', 'Suppression réussie');
            redirect('http://localhost/yogaStudio/reservation');
        }
    }
    else{
        addFlashMsg('error', 'Echec de la suppression');
        redirect('index');
    }
}