<?php

const SECRETKEY = 'N7U29khnY';

//This function returns the encrypted password  

function encryptPassword($pass) {
    return openssl_encrypt($pass, "AES-128-ECB", SECRETKEY);
}

//This function compares the password you type when you log in with the encrypted password registered in database
function verifyPassword($pass, $pass_hash) {

    if ($pass === openssl_decrypt($pass_hash, "AES-128-ECB", SECRETKEY)) {
        return true;
    }
     
    return false;
}

// redirect to the page given in parameter
function redirect(string $url): void
{
    //require(ROOT.'controllers/'.$controller.'.php');
   header("Location: $url");
   exit();
}

//redirect to previous page
function redirectBack()
    {
         if (empty($_SERVER['HTTP_REFERER']))
         {
            redirect(ROOT); 
         }
        redirect($_SERVER['HTTP_REFERER']);
    }

// render page requested in parameter
function renderPage(string $template, array $variables = []): void
{
    extract($variables);

    ob_start();  
    require("views/$template.phtml");
    $pageContent = ob_get_clean();
    
    require('views/layout.phtml');
}

// render Admin back office
function renderPageAdmin(string $template, array $variables = []): void
{
    extract($variables);

    ob_start();
    require("views/admin/$template.phtml");
    $pageContent = ob_get_clean();    
    require('views/admin/layout_admin.phtml');
}

function renderError($code, $message)
{
    ob_start();  
    require("views/errors.phtml");
    $pageContent = ob_get_clean();
    
    require('views/layout.phtml');

}


function addFlashMsg(string $type, string $message)
    {
        if (empty($_SESSION['messages'])) {
            $_SESSION['messages'] = [
                'error' => [],
                'success' => [],
            ];
        }
        $_SESSION['messages'][$type][] = $message;
    }

function hasFlashes(string $type): bool
    {
        if (empty($_SESSION['messages'])) {
            return false;
        }

        return !empty($_SESSION['messages'][$type]);
    }

function getFlashes(string $type): array
{
    if (empty($_SESSION['messages'])) {
        return [];
    }

    $messages = $_SESSION['messages'][$type];

    $_SESSION['messages'][$type] = [];

    return $messages;
}

function isConnected(): bool
{
    return !empty($_SESSION['user']);
}

function isAdmin(): bool
    {
        if($_SESSION['user']['Role']==3) {
            return true;
        }
        
        return false;
    }
