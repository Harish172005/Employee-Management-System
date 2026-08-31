<?php

class PasswordHasher {
    
    // Bcrypt algorithm configuration
    const ALGORITHM = PASSWORD_BCRYPT;
    const COST = 10;
    
    
    public static function hash($password) {
        return password_hash($password, self::ALGORITHM, ['cost' => self::COST]);
    }
    
   
    public static function verify($password, $hash) {
        return password_verify($password, $hash);
    }
    
    
    public static function needsRehash($hash) {
        return password_needs_rehash($hash, self::ALGORITHM, ['cost' => self::COST]);
    }
    
   
}
?>
