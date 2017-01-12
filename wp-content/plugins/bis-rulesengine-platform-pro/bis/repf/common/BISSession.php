<?php


/* ######################################################################################

  Copyright (C) 2016 by Ritu.  All rights reserved.  This software
  is an unpublished work and trade secret of Ritu, and distributed only
  under restriction.  This software (or any part of it) may not be used,
  modified, reproduced, stored on a retrieval system, distributed, or
  transmitted without the express written consent of Ritu.  Violation of
  the provisions contained herein may result in severe civil and criminal
  penalties, and any violators will be prosecuted to the maximum extent
  possible under the law.  Further, by using this software you acknowledge and
  agree that if this software is modified by anyone such as you, a third party
  or Ritu, then Ritu will have no obligation to provide support or
  maintenance for this software.

  ##################################################################################### */

namespace bis\repf\common;

/*
  Use the static method getInstance to get the object.
 */

class BISSession {

    const SESSION_STARTED = TRUE;
    const SESSION_NOT_STARTED = FALSE;

    // The state of the session
    private $sessionState = self::SESSION_NOT_STARTED;
    // THE only instance of the class
    private static $instance;

    private function __construct() {
        
    }

    /**
     *    Returns THE instance of 'Session'.
     *    The session is automatically initialized if it wasn't.
     *   
     *    @return    object
     * */
    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }

        self::$instance->startSession();

        return self::$instance;
    }

    /**
     *    (Re)starts the session.
     *   
     *    @return    bool    TRUE if the session has been initialized, else FALSE.
     * */
    public function startSession() {
        if ($this->sessionState == self::SESSION_NOT_STARTED) {
            $this->sessionState = session_start();
        }
        return $this->sessionState;
    }

    /**
     * Set the value to the session.
     * 
     * @param type $key
     * @param type $value
     */
    public function setAttribute($key, $value) {
        $_SESSION[$key] = $value;
    }

   /**
     * Gets the value from the session.
     * 
     * @param type $key
     * @return type
     */
    public function getAttribute($key) {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return null;
    }
   
    /**
     * This method check if the value is set.
     * 
     * @param type $key
     * @return type
     */
    public function isSessionAttributeSet($key) {
        return isset($_SESSION[$key]);
    }
    
    public function removeAttribute($key) {
        $_SESSION[$key] = null;
        unset($_SESSION[$key]);
    }
    
    /**
     *    Destroys the current session.
     *   
     *    @return    bool    TRUE is session has been deleted, else FALSE.
     * */
    public function destroy() {
        
        if ($this->sessionState == self::SESSION_STARTED) {
            if ($this->is_session_started()) {
                $this->sessionState = !session_destroy();
                unset($_SESSION);
            }
            return !$this->sessionState;
        }

        return FALSE;
    }
    
    function is_session_started() {
        if (php_sapi_name() !== 'cli') {
            if (version_compare(phpversion(), '5.4.0', '>=')) {
                return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
            } else {
                return session_id() === '' ? FALSE : TRUE;
            }
        }
        return FALSE;
    }

}
