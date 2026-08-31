 <?php
 class PasswordValidator {
 public static function validateStrength($password) {
        $validation = [
            'valid' => true,
            'message' => []
        ];
        
        if (strlen($password) < 6) {
            $validation['valid'] = false;
            $validation['message'][] = 'Password must be at least 6 characters long';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $validation['message'][] = 'Password should contain at least one uppercase letter';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $validation['message'][] = 'Password should contain at least one lowercase letter';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $validation['message'][] = 'Password should contain at least one number';
        }
        
        return $validation;
    }
}
?>