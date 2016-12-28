<?php
namespace Phacil\Core\Authorization;

use Phacil\HTTP\Session;
use Phacil\Core\Routing\Route;
use Phacil\Core\Routing\RouteBuilder;
use Phacil\HTTP\Request;
use Phacil\Core\Exception\PhacilException;

class Auth {
    
    private static $authRedirect = '/';
    private static $loginRoute = '/users/login';
    private static $logoutRedirect = '/users/login';
    private static $loginRedirect = '/users/login';
    private static $allow = null;
    private static $deny = null;
    private static $notAuthorizedMessage = 'You not has authorization for this page';
    private static $notCanLoginMessage = 'You cannot login with this credentials';
    
    public static function login($query, $credentials = [], $redirect = true){
        $cred = [];
        foreach($credentials as $field => $value){
            $cred[] = [$field=> $value];
        }
        if($user = $query->where($cred[0])->get(1)){
            if(get_class($user) == 'Phacil\\Integration\\Database\\Row'){
                $objUser = current($user);
            }elseif(get_class($user) == 'Phacil\\Integration\\ORM\\ORMRow'){
                $objUser = $user;
            }else{
                throw  new PhacilException('Objeto não reconhecido');
            }
            
            if(current($cred[1]) === $objUser->{key($cred[1])}){
                Session::set("Auth", $objUser);
                
                if($redirect){
                    Route::url(self::$authRedirect)->redirect();
                }               
                return true;
            }
        }else{
            
            Session::setMessage(self::$notCanLoginMessage, 'auth');
            if($redirect){
                Route::url(self::$loginRedirect)->redirect();
            }
            return false;
        }
    }
    
    public static function logout(){
        Session::delete('Auth');
        Route::url(self::$loginRedirect)->redirect();
    }
     
    public static function start(){
        if (self::isAllowed(Request::info('uri'))){
            return;
        }
        
        if (  (!self::isLogged() && Request::info('uri') != rtrim(self::$loginRoute, '/')) 
              || 
              (self::isDenied(Request::info('uri'))) 
              
            )
        {
            Session::setMessage(self::$notAuthorizedMessage, 'auth');
            Route::url(self::$loginRedirect)->redirect();
        }
    }
        
    public static function allow($pages = []){        
        if(!$pages){
            self::$allow = [];
            return;
        }
        
        if(!is_array($pages)){
            $pages = (array) $pages;
        }
        foreach($pages as $route){
            self::$allow[] = self::__parseRoute($route);
        }
        return;
    }
    
    public static function deny($pages = []){
        if(!$pages){
            self::$deny = [];
            return;
        }
        
        if(!is_array($pages)){
            $pages = (array) $pages;
        }
        foreach($pages as $route){
            self::$deny[] = self::__parseRoute($route);
        }
        return;
    }
    
    public static function isAllowed($route){
        if(is_null(self::$allow)){
            return false;
        }else if(self::$allow === []){
            return true;
        }else{
            return in_array($route, self::$allow)?true:false;
        }
    } 
    
    public static function isDenied($route){
        if(is_null(self::$deny)){
            return false;
        }else if(self::$deny === []){
            return true;
        }else{
            return in_array($route, self::$deny)?true:false;
        }
    } 
    
    public static function authRedirect($route){
        self::$authRedirect = $route;
    }
    
    public static function logoutRedirect($route){
        self::$logoutRedirect = $route;
    }
    
    public static function loginRedirect($route){
        self::$loginRedirect = $route;
    }
    
    public static function getLogged(){        
        return Session::check("Auth")?Session::get("Auth"):false;
    }
    
    public static function isLogged(){
        return Session::check("Auth")?true:false;
    }
    
    private static function __parseRoute($route){
        if($route instanceof RouteBuilder){
            return $route->output();
        }
        return $route;
    }
}
