<?php

require_once 'libraries/utils.php';
require_once 'models/user.php';

//Show all users who have an account
function index(){
    $users = findAllUsers();
    renderPageAdmin('user/list', compact('users'));
}

