<?php

use Phacil\Core\Architecture\Flash;
use Phacil\Core\Architecture\View;
use Phacil\Core\Architecture\Theme;

/**
 * 
 * @return Phacil\Core\Architecture\Flash
 */
function flash()
{
    return new Flash();
}

/**
 * 
 * @return View
 */
function view()
{
    if(!is_null(View::getInstance())){
        return View::getInstance();
    }
    return new View();
}

/**
 * 
 * @return Theme
 */
function theme()
{
    if(!is_null(Theme::getInstance())){
        return Theme::getInstance();
    }
    return new Theme();
}


