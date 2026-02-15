<?php

require_once __DIR__ . '/../config/mainConfig.php';

class accessDeniedController
{
    public function __construct()
    {
        if (!is_login()) {
            header("Location: login");
            exit;
        }

        
        
    }

    public function index()
    {
        

        

        
        require_once __DIR__ . '/../view/Admin/access-denied.php';
    }

    
}