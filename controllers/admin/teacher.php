<?php

require_once 'models/teacher.php';
require_once 'models/user.php';
require_once 'libraries/utils.php';

//Show all teachers
function index(){   
    $teachers = findAllTeachers();
    renderPageAdmin('teacher/list', compact('teachers'));

}

//Create a teacher
function create(){

    //creation teacher form processing
    if (!empty($_POST)) {
    
        //if required inputs are empty, we display error message
        if(empty($_POST['lastName'])
           || empty($_POST['firstName'])
           || empty($_POST['email'])
           || empty($_POST['address'])
           || empty($_POST['zipcode']) 
           || empty($_POST['city'])
           || empty($_POST['phone'])
           || empty($_POST['bio'])){

            addFlashMsg('error', 'Champs obligatoires non remplis !');
            redirectBack();
           }

       //test if user's email already exists in the database
        if (is_user_exists($_POST['email'])){
            addFlashMsg('error', 'Utilisateur déjà créé!');
            redirectBack();
        }

        //testing email format
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            addFlashMsg('error', 'Mauvais format d\'email');
            redirectBack();
        }

        //if input address2 is empty, its value will be null
        $address2 = strip_tags($_POST['address2']);
        if($address2 == ""){
            $address2 = NULL;
        }

        //insert Teacher in database
        $resultId = insertNewTeacher(
                            strip_tags($_POST['lastName']), 
                            strip_tags($_POST['firstName']),
                            strip_tags($_POST['address']), 
                            $address2,
                            strip_tags($_POST['city']),
                            strip_tags( $_POST['zipcode']), 
                            strip_tags($_POST['phone']),
                            strip_tags($_POST['email']), 
                            strip_tags($_POST['bio']));

        //if insertion is successful, we can proceed to upload the picture with the return Id             
        if ($resultId >0 && isset($_FILES['photo_teacher']) && !empty($_FILES['photo_teacher']['name'] ))
        {
            //upload
             // Testons si le fichier n'est pas trop gros
            if ($_FILES['photo_teacher']['size'] <= 1000000)
            {
                $filename = upload($_FILES["photo_teacher"], intval($resultId));
                if ($filename) {
                    updatePicture(intval($resultId), $filename);
                }         
        
                //redirect to teachers list
                addFlashMsg('success', 'Création réussie');
                redirect('index');
            }
            else{
                addFlashMsg('error', 'La taille du fichier doit être inférieure à 1Mo');
                redirectBack();
            }
        }
        else{
            addFlashMsg('error', 'La création a échoué');
            redirect('index');
        }     
    }
    //show template
    else{        
        renderPageAdmin('teacher/new');
    }
}

//Update a teacher
function update($userId){

    //update teacher form processing
    if (!empty($_POST)) {
    
        //if required inputs are empty, we display error message
        if(empty($_POST['email'])
           || empty($_POST['lastName'])
           || empty($_POST['firstName'])
           || empty($_POST['address'])
           || empty($_POST['zipcode']) 
           || empty($_POST['city'])
           || empty($_POST['phone'])
           || empty($_POST['bio'])){

            addFlashMsg('error', 'Champs obligatoires non remplis !');
            redirectBack();             
           }

        //verify email format
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            addFlashMsg('error', 'Mauvais format d\'email');
            redirectBack();
        }

        //if input address2 is empty, its value will be null
        $address2 = strip_tags($_POST['address2']);
        if($address2 == ""){
            $address2 = NULL;
        }
        
        //if $_POST is not empty and correct, we proceed to update teacher
        $result = updateTeacher(
                        strip_tags($_POST['lastName']),
                        strip_tags($_POST['firstName']),
                        strip_tags($_POST['address']), 
                        $address2,
                        strip_tags($_POST['city']),
                        strip_tags($_POST['zipcode']), 
                        strip_tags($_POST['phone']),
                        strip_tags($_POST['email']), 
                        strip_tags($_POST['bio']),
                        intval($userId));

        // we verify if there is a photo to update
        if(isset($_FILES['photo_teacher']) && !empty($_FILES['photo_teacher']['name'])){
            
            if ($_FILES['photo_teacher']['size'] <= 1000000)
            {
                //delete the previous picture from folder uploads
                if(!empty($_POST['picture'])) {
                    $picture = explode('.', $_POST['picture']);
                    unlink('uploads/'.$picture[0].'.'.$picture[1]);             
                }
                    
                //then proceed to upload new picture into uploads folder
                $filename = upload($_FILES["photo_teacher"], intval($userId));
                
                //if upload is successful, update picture into database
                if ($filename) {
                    updatePicture(intval($userId), $filename);
                }     
            } 
            else{
                addFlashMsg('error', 'La taille du fichier doit être inférieure à 1Mo');
                redirectBack();
            }     
        }

        //if update is successful, display success message, else error message
        if($result){
            addFlashMsg('success', 'Mise à jour effectuée');
            redirect('http://localhost/projet_mvc/teacher');
        }else{
            addFlashMsg('error', 'La mise à jour a échoué');
            redirect('http://localhost/projet_mvc/teacher');
        }          
           
    }
    //show template
    else{
        $teacher = findTeacher($userId);
    
        if(!$teacher){
            addFlashMsg('error', 'Aucun prof trouvé');
            redirectBack();
        }
        else{
            renderPageAdmin('teacher/edit', compact('teacher'));
        }
    }
}

//Delete teacher process
function deleteTeacher($id){
    $picture = findPicture($id);
    //delete the previous picture from folder uploads
    unlink('uploads/'.$picture['Picture']);

    //delete teacher from database
    $result = delete($id, 'User');
    
    //checking if deleting has worked, display success message, else error message
    if($result){  
        addFlashMsg('success', 'Prof supprimé!');
        redirectBack();
    }else{
        addFlashMsg('error', 'La suppression n\'a pas été effectuée');
        redirectBack();
    }

}


/* This function uploads a file into the folder uploads
it is only used inside controller 'teacher.php'
@params: array $file contains info about the file sent via POST, string $namefile
@returns string or false
*/
function upload(array $file, string $namefile){
        
    //allowed file extension
    $allowed_file_types = ['image/png', 'image/jpeg'];
    
    //checking upload file extension
    //checking if MIME type file ($_FILES['file1']['type'] is in array $allowed_file_types 
    if (in_array(mime_content_type($file["tmp_name"]), $allowed_file_types)) 
    {
        //filename will use userId.ext
        switch(mime_content_type($file["tmp_name"]))
        {
            case 'image/png':
                $filenameWithExt = $namefile.'.png';
                break;
                
            case 'image/jpeg':
                $filenameWithExt = $namefile.'.jpg';
                break;
                
        }
        
        //move uploaded file to "uploads" folder
        $resultMoveFile = move_uploaded_file($file['tmp_name'],"uploads/".$filenameWithExt);
        
        //checking if uploaded file is in the uploads folder
        if ($resultMoveFile) {
            return $filenameWithExt;
        }
        else{ //return false if upload did not work
                return false;
        }
    }
 
}