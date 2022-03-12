<?php
session_start();
require_once 'libraries/utils.php';
define('ROOT', str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']));

try{
    if(isset($_GET['p']) && !empty($_GET['p'])){

        $parameter = explode("/", $_GET['p']);

        if($parameter[0] !==""){
            $controller = $parameter[0];

            $controllers = [
                            'users' => ['home', 'user', 'timetable', 'cart'],
            
                            'admin' => ['admin', 'course', 'member', 'reservation', 'teacher', 'yogaclass']
                        ];
        
            if(isset($parameter[1])){
                $task = $parameter[1];
            }
            else{
                $task = 'index';
            }
            
            if(in_array($controller, $controllers['users']) ){           
                require ROOT.'controllers/'.$controller.'.php';                        
            }
            elseif(in_array($controller, $controllers['admin']) && isConnected() && isAdmin()){
                require ROOT.'controllers/admin/'.$controller.'.php'; 
            }                    
            else{           
                throw new Exception("Page inexistante ou accès refusé", 1);
            }
        
            if(function_exists($task)){
        
                if(isset($parameter[2]) && isset($parameter[3])){
                    $task($parameter[2],$parameter[3]);
                }elseif(isset($parameter[2])){
                    $task($parameter[2]);
                }else{
                    $task();
                }
            } 
            else{
                throw new Exception("Aucune action régulière définie", 2);
            }
        }
    }else{
        redirect('http://localhost/yogaStudio/home/index');
    }
}
catch(Exception $e) { // S'il y a eu une erreur, alors...
    $code = $e->getCode();
    $message = $e->getMessage();

    if(isConnected() && isAdmin()){
        renderError($code, $message, 'admin/layout_admin');
    }
    else{
        renderError($code, $message, 'layout');
    }
    
}

 
