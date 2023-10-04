<?php
namespace Simcify;

class Session {
    
    /**
     * Create a new session instance
     * 
     * @return  void
     */
    public function __construct() {
        session_name(Config::get('session.name'));
        if(session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }
    
    /**
     * Destroy the session object
     * 
     * @return  void
     */
    public function __destruct() {
        
    }
    
    /**
     * Add a value to the session
     * 
     * @param   string $key
     * @param   mixed $value
     * @return  mixed
     */
    protected function add($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    /**
     * Destroy all data in the session
     * 
     * @return  void
     */
    public function flush() {
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            cookie(session_name(), '', -7);
        }
        session_destroy();
    }
    
    /**
     * Delete a key from the session
     * 
     * @return  void
     */
    public function forget($key) {
        unset($_SESSION[$key]);
    }
    
    /**
     * Get a value from the session
     * 
     * @param   string  $key
     * @return  mixed
     */
    public function get($key) {
        return $this->has($key) ? $_SESSION[$key] : null;
    }
    
    /**
     * Check for a key in the session
     * 
     * @param   string $key
     * @return  bool
     */
    public function has($key) {
        return isset($_SESSION[$key]);
    }
    
    /**
     * Delete and return a key from the session
     * 
     * @param   string $key
     * @return  void
     */
    public function pull($key) {
        $value = $this->get('key');
        $this->forget($key);
        return $value;
    }
    
    /**
     * Add a value to the session
     * 
     * @param   mixed   $mixed
     * @param   mixed   $value
     * @return  void
     */
    public function put($mixed, $value = null) {
        if(is_array($mixed)) {
            foreach($mixed as $key => $value) {
                $this->add($key, $value);
            }
        }else {
            $this->add($mixed, $value);
        }
    }
}
