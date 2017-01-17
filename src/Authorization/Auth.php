<?php
namespace Phacil\Core\Authorization;

use Phacil\HTTP\Session;
use Phacil\Core\Routing\Route;
use Phacil\Core\Routing\RouteBuilder;
use Phacil\Core\Routing\RouteMatcher;
use Phacil\HTTP\Request;
use Phacil\Core\Exception\PhacilException;

class Auth {
    
    private static $authRedirect = '/';
    private static $loginRoute = '/users/login';
    private static $logoutRedirect = '/users/login';
    private static $loginRedirect = '/users/login';
    private static $allow = '*';
    private static $public = null;
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
                throw new PhacilException('Objeto não reconhecido');
            }
            
            $options = [
                'salt' => app()->get('Config.Salt'),
            ];

            $hash = password_hash(current($cred[1]), PASSWORD_BCRYPT, $options);
            
            if($hash == $objUser->{key($cred[1])}){
                Session::set("Auth", $objUser);
                
                if($redirect){
                    if(Session::check('App.redirectTryPage')){
                        $route = Session::get('App.redirectTryPage');
                        Session::delete('App.redirectTryPage');
                        Route::url($route)->redirect();
                    }else{
                        Route::url(self::$authRedirect)->redirect();
                    }                    
                }               
                return true;
            }
        }
            
        flash()->message(self::$notCanLoginMessage)->id('auth');
        if($redirect){
            Route::url(self::$loginRedirect)->redirect();
        }
        return false;
        
    }
    
    public static function logout(){
        Session::delete('Auth');
        Route::url(self::$loginRedirect)->redirect();
    }
     
    public static function start(){
        if (self::isPublic(Request::info('uri'))){
            return;
        }
        
        if (  (!self::isLogged() && Request::info('uri') != rtrim(self::$loginRoute, '/')) 
              || 
              (self::isLogged() && !self::isAllowed(Request::info('uri')))
              || 
              (self::isLogged() && self::isDenied(Request::info('uri')))
              
            )
        {
            session()->set('App.redirectTryPage', Request::info('uri'));
            flash()->message(self::$notAuthorizedMessage)->id('auth');
            Route::url(self::$loginRedirect)->redirect();
        }
    }
        
    public static function allow($pages = []){
        if(!is_array($pages)){
            $pages = (array) $pages;
        }
        foreach($pages as $route){
            $collection[] = new Route($route[0], rtrim($route[1], ' /'));
            self::$allow = $collection;
        }
        return;
    }
    
    public static function publics($pages = []){
        if(!is_array($pages)){
            $pages = (array) $pages;
        }
        foreach($pages as $route){
            $collection[] = new Route($route[0], rtrim($route[1], ' /'));
            self::$public = $collection;
        }
        return;
    }
    
    public static function deny($pages = []){
        if(!is_array($pages)){
            $pages = (array) $pages;
        }
        foreach($pages as $route){
            $collection[] = new Route($route[0], rtrim($route[1], ' /'));
            self::$deny = $collection;
        }
        return;
    }
    
    public static function isAllowed($route){
        if(self::$allow == '*'){
            return true;
        }else{
            if(RouteMatcher::match(self::$allow, $route, request()->method(), false)){
                return true;
            }
        }
        return false;
    } 
    
    public static function isPublic($route){
        if(is_null(self::$public)){
            return false;
        }else{
            if(RouteMatcher::match(self::$public, $route, request()->method(), false)){
                return true;
            }
        }
        return false;
    }
    
    public static function isDenied($route){
        if(is_null(self::$deny)){
            return false;
        }else{
            if(RouteMatcher::match(self::$deny, $route, request()->method(), false)){
                return true;
            }
        }
        return false;
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
