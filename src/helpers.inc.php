<?php

use Phacil\Kernel\App;
use Phacil\Architecture\Flash;
use Phacil\Architecture\View;
use Phacil\Architecture\Theme;
use Phacil\Exception\PhacilException;
use Phacil\Routing\RouteBuilder;
use Phacil\Routing\Router;

/**
 * 
 * @return Phacil\Kernel\App
 */
function app()
{
    if(!is_null(App::getInstance())){
        return App::getInstance();
    }
    return new App();
}

/**
 * 
 * @param type $message
 * @param type $code
 * @param Exception $previous
 * @return Phacil\Exception\PhacilException
 */
function exception($message, $code = 0, Exception $previous = null)
{
    return new PhacilException($message, $code, $previous);
}

/**
 * 
 * @return Phacil\Architecture\Flash
 */
function flash()
{
    return new Flash();
}

/**
 * 
 * @param type $url
 * @return RouteBuilder
 */
function route($url = '/')
{
    return new RouteBuilder($url);
}

/**
 * 
 * @return Router
 */
function router()
{
    if(!is_null(Router::getInstance())){
        return Router::getInstance();
    }
    return new Router();
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

