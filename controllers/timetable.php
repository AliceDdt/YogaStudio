<?php

require_once 'models/yogaclass.php';
require_once 'models/reservation.php';
require_once 'libraries/utils.php';

//show timetable page
function index(){
    $yogaclass = findAllClasses();
    renderPage('timetable', compact('yogaclass'));
}


/* IF JAVASCRIPT DEACTIVATED => this function adds the yogaclass Id into the array $_SESSION['booking']*/
function add(){

    //if user is not connected, redirect to login page
    if(!isConnected()){
        addFlashMsg('error', 'Veuillez vous connecter pour réserver');
        redirect('http://localhost/projet_mvc/user');
    }
    /*if user has clicked on 'Reserver' button, yogaclassId is stored into array $_SESSION['booking] */
    if(!empty($_POST)){
        
        if(!isset($_SESSION['booking'])){
            $_SESSION['booking'] = [];      
        }

        if(in_array($_POST['yogaclass_id'], $_SESSION['booking'])){
            // if already added to cart, we display an error message
            addFlashMsg('error', 'Séance déjà ajoutée au panier');
            redirectBack();
        }
        
        /*if user is connected, we verify that yogaclassId is not already stored into array 
        $_SESSION['booking] because user can only booked one spot for each yogaclass*/              
        if(!in_array($_POST['yogaclass_id'], $_SESSION['booking'])){
                 // if not in array, we verify that yogaclass has a spot left
                 $seats = nbSeatsLeft($_POST['yogaclass_id']);
                if($seats['Nb_seats'] <= 0){
                     $_SESSION['booking']= [];
                     addFlashMsg('error', 'la séance est complète !');
                     redirectBack();
                 }
                // we construct SessionBooking array
                $_SESSION['booking'][$_POST['yogaclass_id']] = $_POST['yogaclass_id'];
            
                redirectBack();
                //renderPage('timetable', compact('yogaclass'));
                
        } 
              
    }
    
    // if(!isset($_SESSION['booking'])){
    //     $_SESSION['booking'] = [];      
    // }
   
    // /*if user has clicked on 'Reserver' button, yogaclassId is stored into array $_SESSION['booking] */
    // if(!empty($_POST)){
    //     //if user is not connected, redirect to login page
    //     if(!isConnected()){
    //         addFlashMsg('error', 'Veuillez vous connecter pour réserver');
    //         redirect('http://localhost/projet_mvc/user/login');
    //     }  
    //     /*if user is connected, we verify that yogaclassId is not already stored into array $_SESSION['booking] because user can only booked one spot for each yogaclass*/
    //     else{   
    //         var_dump($_POST);     
    //         // if(!in_array($_POST['yogaclass_id'], $_SESSION['booking'])){
    //         //     // if not in array, we verify that yogaclass has a spot left
    //         //     $seats = nbSeatsLeft($_POST['yogaclass_id']);
    //         //     if($seats['Nb_seats'] <= 0){
    //         //         $_SESSION['booking']= [];
    //         //         addFlashMsg('error', 'la séance est complète !');
    //         //         redirectBack();
    //         //     }
    //         //      // we construct SessionBooking array
    //         //         $_SESSION['booking'][$_POST['yogaclass_id']] = $_POST['yogaclass_id'];
                    
    //         //        // redirectBack();
                    
                
    //         // }
    //         // else{// if already added to cart, we display an error message
    //         //     addFlashMsg('error', 'Séance déjà ajoutée au panier');
    //         //     redirectBack();
    //         // }           
    //     }     
    //}
}
