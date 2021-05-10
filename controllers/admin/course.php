<?php

require_once 'libraries/utils.php';
require_once 'models/course.php';

//Show all types of courses
function index(){
    $courses = findAllCourses();   
    renderPageAdmin('course/list', compact('courses'));
}

//Create a course
function create(){
    
    //creation course form processing
    if(!empty($_POST)){

        //if required inputs are empty, we display error message
        if(empty($_POST['name'])
            || empty($_POST['shortDescription'])
            || empty($_POST['description'])){
                addFlashMsg('error', 'Champs obligatoires non remplis !');
                redirectBack();
        };
        
        //if $_POST is not empty, we insert the new course into database
        $result = insertNewCourse($_POST['name'], $_POST['shortDescription'], $_POST['description']);

        //if insertion is successful, display success message, else error message
        if($result){
            addFlashMsg('success', 'Création réussie');
            redirect('index');
        }else{
            addFlashMsg('error', 'La création a échoué');
            redirect('index');
        }     
    }

    //show template
    else{
        renderPageAdmin('course/new');
    }
}

//Update a course
function update($courseId){

    //update course form processing
    if (!empty($_POST)) {
    
        //if required inputs are empty, we display error message
        if (empty($_POST['name'])
           || empty($_POST['shortDescription'])
           || empty($_POST['description'])){

            addFlashMsg('error', 'Champs obligatoires non remplis !');
            redirectBack();              
        }

        //if $_POST is not empty and correct, we proceed to update the course into the database
        else{
            $result = updateCourse(intval($courseId), $_POST['name'],$_POST['shortDescription'], $_POST['description']);
            
            //if update is successful, display success message, else error message
            if($result){
                addFlashMsg('success', 'Mise à jour effectuée');
                redirect('http://localhost/projet_mvc/course');
            }else{
                addFlashMsg('error', 'La mise à jour a échoué');
                redirect('http://localhost/projet_mvc/course');
            }          
        }     
    }
    else{
        //show template whith course info
        $course = findCourse($courseId);

        if(!$course){
            addFlashMsg('error', 'Aucun cours trouvé !');
            redirectBack();
        }else{
            renderPageAdmin('course/edit', compact('course'));
        }
    }
}

//Delete course process
function deleteCourse($id){
    // proceed to delete the course from database
    $result = delete($id, 'Course');

    //if delete is successful, display success message, else error message
    if($result){
        addFlashMsg('success', 'Cours supprimé!');
        redirectBack();

    }else{
        addFlashMsg('error', 'Un problème est survenu, le cours n\'a pas été supprimé');
        redirectBack();       
    }
}


