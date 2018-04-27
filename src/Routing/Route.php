<?php
/**
 * Description of Route
 *
 * @author alisson
 */

namespace Phacil\Routing;

class Route {
    
    protected $method = null;
    
    protected $route = null;
    
    protected $callback = null;
    
    protected $where = [];
    
    protected $headers = [];
    
    protected $namedArgs = [];
    
    protected $mimetypes = [
         'atom'          =>  'application/atom+xml',
         'css'           =>  'text/css',
         'javascript'    =>  'text/javascript',
         'jpg'           =>  'image/jpeg',
         'json'          =>  'application/json',
         'pdf'           =>  'application/pdf',
         'rss'           =>  'application/rss+xml; charset=ISO-8859-1',
         'plain'         =>  'text/plain',
         'xml'           =>  'text/xml'
    ];
    /**
     *
     * @var array 
     */
    protected $matchParam = [
        'i'  => '([0-9]++)',
        'a'  => '([A-Za-z]++)',
        'an' => '([0-9A-Za-z]++)', 
        'f'  => '([0-9]++(?:\.[0-9]+))',
        'h'  => '([0-9A-Fa-f]++)',
        'n'  => '([a-zA-Z ]++)',
        'e'  => '([a-z0-9]+(?:[_a-z0-9-.]+)@[a-z0-9-]+(?:\.[_a-z0-9-]{2,})?(?:\.[_a-z0-9-]{2,})?)',
        ''   => '([^/.]++)'
    ];
    
    protected $matches = [];
    
    /**
     * 
     * @param type $method
     * @param type $route
     * @param type $callback
     */
    public function __construct($method=null, $route=null, $callback=null) {
        $this->method = $method;
        $this->route = $this->__translate($route);
        $this->callback = $callback;
    }
    
    protected function __translate($route){
        $parts = explode('/', ltrim($route, '/'));
        
        $translated = [];
        foreach($parts as $part){
            
            if(strpos($part, ':') !== false){
                list($pseudo_pattern, $var) = explode(':', $part);
                $translated[] = $this->matchParam[$pseudo_pattern];
                $this->namedArgs[] = $var;
            }else if($part == '*'){
                $translated[] = '(.+)';
            }else{
                $translated[] = $part;
            }
        }
        return '/' . join('/', $translated);
    }

    public function getMethod() {
       return $this->method;
    }

    public function getRoute(){        
       return $this->route;
    }
    
    public function getRawRoute(){
       return $this->route;
    }

    public function getCallback() {
       return $this->callback;
    }

    public function getWhere() {
       return $this->where;
    }

    public function getHeaders() {
       return $this->headers;
    }

    public function getMimeTypes() {
       return $this->mimetypes;
    }
    
    public function getNamedArgs() {
        return $this->namedArgs;
    }
    
    public function getMatches() {
        return $this->matches;
    }

    public function setMethod($method) {
       $this->method = $method;
    }

    public function setRoute($route) {
       $this->route = $route;
    }

    public function setCallback($callback) {
       $this->callback = $callback;
    }

    public function setWhere($where) {
       $this->where = $where;
    }
    
    public function setMatches($matches) {
       $this->matches = $matches;
    }

    public function where($where = []) {

    }

    public function mimetype($mimetype) {
       $this->headers['mimetype'] = strtolower($mimetype);
    }

    public function insertHeaders(){
       if(isset($this->headers['mimetype'])){
          header('Content-Type: ' . $this->mimetypes[$this->headers['mimetype']]);
       }
    }
   
    public static function url($url = '/'){
        //pr($url);
        return new RouteBuilder($url);
    }
    
}
