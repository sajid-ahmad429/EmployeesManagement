<?php

/**
 * --------------------------------------------------------------------
 * CODEIGNITER 4 - SimpleAuth
 * --------------------------------------------------------------------
 *
 * This content is released under the MIT License (MIT)
 *
 * @package    SimpleAuth
 * @author     
 * @license    https://opensource.org/licenses/MIT	MIT License
 * @link       
 * @since      Version 1.0
 * 
 */

namespace App\libraries;

use CodeIgniter\I18n\Time;
use App\Models\AuthModel;
use Config\Auth;
use Config\Email;
use Config\App;
use \Config\Services;
use App\Models\DepartmentModel;
use App\Libraries\EmailQueueLibrary;

/**
 * AuthLibrary
 */
class AuthLibrary {

    public function __construct() {
        $this->AuthModel = new AuthModel();
        $this->DepartmentModel = new DepartmentModel();
        $this->config = new Auth;
        $this->emailconfig = new Email;
        $this->AppConfig = new App;
        $this->Session = session();
        $this->request = Services::request();
        $this->emailQueue = new EmailQueueLibrary();
    }

    /**
     * --------------------------------------------------------------------------
     * LOGIN USER
     * --------------------------------------------------------------------------
     *
     * Form validation done in controller
     * Gets the user from DB
     * Checks if their account is activated
     * Sets the user session and logs them in
     * 
     * @param  string $email
     * @return true
     */
    public function LoginUser($email, $rememberMe) {

        // GET USER DETAILS FROM DB
        $user = $this->AuthModel->where('email', $email)->first();

        // SET USER ID AS A VARIABLE
        $userID = $user['employee_id'];
        
        // IF REMEMBER ME FUNCTION IS SET TO TRUE IN CONFIG 
        if ($this->config->rememberMe && $rememberMe == '1') {

            $this->rememberMe($userID);
            $this->Session->set('rememberme', $rememberMe);
        }

        $this->Session->set('lockscreen', false);

        // ✅ FIXED: Regenerate session ID to prevent session fixation
        $this->Session->regenerate();

        //SET USER SESSION 
        $this->setUserSession($user);
    }

    /**
     * --------------------------------------------------------------------------
     * REGISTER USER
     * --------------------------------------------------------------------------
     *
     * Form validation done in controller
     * Save user details to DB
     * Send activation email if config is set to true
     * If config is false manually activate account
     * 
     * @param  array $userData
     * @return true
     */
    public function RegisterUser($userData) {
        // ADD USER TO DEFAULT ROLE
        $userData['role'] = $this->config->defaultRole;

        // SAVE USER DETAILS TO DB
        $this->AuthModel->save($userData);

        // FIND OUR NEW USER BY EMAIL SO WE CAN GRAB NEW DETAILS
        $user = $this->AuthModel->where('email', $userData['email'])
                ->first();

        // SHOULD WE SEND AN ACTIVATION EMAIL?
        if ($this->config->sendActivationEmail) {

            // GENERATE A NEW TOKEN
            // SET THE TOKEN TYPE AS SECOND PARAMETER. Reset password token = 'reset_token'
            $encodedtoken = $this->GenerateToken($user, 'activate_token');

            // QUEUE ACTIVATION EMAIL FOR ASYNCHRONOUS PROCESSING
            $emailData = [
                'to' => $user['email'],
                'to_name' => $user['employee_name'],
                'subject' => 'Account Activation',
                'message' => $this->generateActivationEmailContent($user, $encodedtoken),
                'template' => 'activation',
                'priority' => 2, // High priority for activation emails
            ];

            $result = $this->emailQueue->queueEmail($emailData);

            if ($result) {
                $this->Session->setFlashData('success', lang('Auth.accountCreated'));
                return true;
            } else {
                $this->Session->setFlashData('danger', lang('Auth.errorOccured'));
                return false;
            }
        }

        // IF WERE NOT SENDING AN ACTIVATION EMAIL LETS SET THE USER TO ACTIVATED NOW
        else {

            $data = [
                'id' => $user['id'],
                'activated' => '1',
            ];

            // UPDATE DB
            $this->AuthModel->save($data);

            $this->Session->setFlashData('success', lang('Auth.accountCreatedNoAuth'));
            return true;
        }
    }

    /**
     * --------------------------------------------------------------------------
     * FORGOT PASSWORD
     * --------------------------------------------------------------------------
     *
     * @param  int $email
     * @return string $resetLink
     */
    public function Forgotpassword($email) {
        // FIND USER BY EMAIL
        $user = $this->AuthModel->where('email', $email)->first();
        // Check if the user exists
        if (!$user) {
            // User not found, handle as needed
            return 'User not found';
        }

        // ✅ FIXED: Generate secure token with expiration
        $tokenData = [
            'user_id' => $user['employee_id'],
            'token_type' => 'reset_password',
            'expires_at' => date('Y-m-d H:i:s', time() + ($this->config->resetTokenExpire * 3600))
        ];
        
        // Generate a cryptographically secure token
        $secureToken = bin2hex(random_bytes(32));
        $tokenData['token'] = hash('sha256', $secureToken);
        
        // Store token in database (you'll need to create this table)
        $this->AuthModel->insertPasswordResetToken($tokenData);
        
        // Make the token URL-safe
        $encodedtoken_url_safe = rtrim(strtr(base64_encode($secureToken), '+/', '-_'), '=');

        // QUEUE PASSWORD RESET EMAIL FOR ASYNCHRONOUS PROCESSING
        $emailData = [
            'to' => $user['email'],
            'to_name' => $user['employee_name'],
            'subject' => 'Password Reset Request',
            'message' => $this->generatePasswordResetEmailContent($user, $resetLink),
            'template' => 'password_reset',
            'priority' => 1, // Highest priority for password reset emails
        ];

        $this->emailQueue->queueEmail($emailData);

        // Return success message instead of link
        return 'Password reset email has been queued for delivery.';
    }


    /**
     * --------------------------------------------------------------------------
     * UPDATE PASSWORD
     * --------------------------------------------------------------------------
     *
     * @param  array $user
     * @return void
    */
    public function updatePassword($user) {
        // UPDATE DB
        $this->AuthModel->save($user);

        // SET SOME FLASH DATA WITH HARD-CODED SUCCESS MESSAGE
        $this->Session->setFlashdata('success', 'Your password has been updated successfully.');
    }


    /**
     * --------------------------------------------------------------------------
     * SET USER SESSION
     * --------------------------------------------------------------------------
     *
     * Saves user details to session
     * 
     * @param  array $user
     * @return void
     */
    public function setUserSession($user) {
        $department = $this->DepartmentModel->where('department_id', $user['department_id'])->first();
        $data = [
            'employee_id' => $user['employee_id'],
            'employee_name' => $user['employee_name'],
            'department' => $department['department_name'],
            'email' => $user['email'],
            'employee_type' => $user['employee_type'],
            'isLoggedIn' => true,
            'ipaddress' => $this->request->getIPAddress(),
        ];
        $this->Session->set($data);
        $this->loginlog();

        return true;
    }

    /**
     * --------------------------------------------------------------------------
     * lOG LOGIN 
     * --------------------------------------------------------------------------
     *
     * Logs users login session to DB
     * 
     * @return void
     */
    public function loginlog() {

        // LOG THE LOGIN IN DB
        if ($this->Session->get('isLoggedIn')) {

            // BUILD DATA TO ADD TO auth_logins TABLE
            $logdata = [
                'employee_id' => $this->Session->get('employee_id'),
                'employee_name' => $this->Session->get('employee_name'),
                'employee_type' => $this->Session->get('employee_type'),
                'ip_address' => $this->request->getIPAddress(),
                'date' => new Time('now'),
                'successful' => '1',
            ];
            

            // SAVE LOG DATA TO DB
            $this->AuthModel->LogLogin($logdata);
        }
    }

    /**
     * --------------------------------------------------------------------------
     * lOG LOGIN FAILURE
     * --------------------------------------------------------------------------
     *
     * If user login / verification failed log an unsuccesfull login attempt
     * 
     * @param  mixed $email
     * @return void
     */
    public function loginlogFail($email) {
        // FIND USER BY EMAIL
        $user = $this->AuthModel->where('email', $email)->first();
        if (!empty($user)) {

            // BUILD DATA TO ADD TO auth_logins TABLE
            $logdata = [
                'employee_id' => $user['employee_id'],
                'employee_name' => $user['employee_name'],
                'employee_type' => $user['employee_type'],
                'ip_address' => $this->request->getIPAddress(),
                'date' => new Time('now'),
                'successful' => '0',
            ];
            // SAVE LOG DATA TO DB
            $this->AuthModel->LogLogin($logdata);
        }
    }

    /**
     * --------------------------------------------------------------------------
     * REMEMBER ME
     * --------------------------------------------------------------------------
     *
     * if the remember me function is set to true in the config file
     * we set up a cookie using a secure selector|validator
     * 
     * @param  int $userID
     * @return void
     */
    public function rememberMe($userID) {


        // SET UP OUR SELECTOR, VALIDATOR AND EXPIRY 
        //
        // The selector acts as unique id so we dont have to save a user id in our cookie
        // the validator is saved in plain text in the cookie but hashed in the db
        // if a selector (id) is found in the auth_tokens table we then match the validators
        //
        $selector = random_string('crypto', 12);
        $validator = random_string('crypto', 20);
        $expires = time() + 60 * 60 * 24 * $this->config->rememberMeExpire;


        // SET OUR TOKEN
        $token = $selector . ':' . $validator;

        // SET DATA ARRAY
        $data = [
            'user_id' => $userID,
            'selector' => $selector,
            'hashedvalidator' => hash('sha256', $validator),
            'expires' => date('Y-m-d H:i:s', $expires),
        ];

        // CHECK IF A USER ID ALREADY HAS A TOKEN SET
        //
        // We dont really want to have multiple tokens and selectors for the
        // same user id. there is no need as the validator gets updated on each login
        // so check if there is a token already and overwrite if there is.
        // should keep DB maintenance down a bit and remove the need to do sporadic purges.
        //

        $result = $this->AuthModel->GetAuthTokenByUserId($userID);

        // IF NOT INSERT
        if (empty($result)) {
            $this->AuthModel->insertToken($data);
        }
        // IF HAS UPDATE
        else {
            $this->AuthModel->updateToken($data);
        }

        // ✅ FIXED: Secure cookie settings
        setcookie(
                "remember",
                $token,
                $expires,
                $this->AppConfig->cookiePath,
                $this->AppConfig->cookieDomain,
                true, // Force secure flag for HTTPS
                true  // Force HTTPOnly flag
        );
    }

    public function IsLoggedIn() {
        if (session()->get('isLoggedIn')) {
            return true;
        }
    }

    /**
     * --------------------------------------------------------------------------
     * CHECK REMEMBER ME COOKIE
     * --------------------------------------------------------------------------
     *
     * checks to see if a remember me cookie has ever been set
     * if we find one w echeck it against our auth_tokens table and see
     * if we find a match and its still valid.
     * 
     * @return void
     */
    public function checkCookie() {
        if ($this->Session->get('lockscreen') == true) {

            return;
        }
        // IS THERE A COOKIE SET?
        $remember = get_cookie('remember');

        // NO COOKIE FOUND
        if (empty($remember)) {
            return;
        }

        // GET OUR SELECTOR|VALIDATOR VALUE
        [$selector, $validator] = explode(':', $remember);
        $validator = hash('sha256', $validator);

        $token = $this->AuthModel->GetAuthTokenBySelector($selector);

        // NO ENTRY FOUND
        if (empty($token)) {

            return false;
        }

        // HASH DOESNT MATCH
        if (!hash_equals($token->hashedvalidator, $validator)) {

            return false;
        }

        // WE FOUND A MATCH SO GET USER ID
        $user = $this->AuthModel->find($token->user_id);

        // NO USER FOUND
        if (empty($user)) {

            return false;
        }

        // JUST BEFORE WE SET SESSION DATA AND LOG USER IN
        // LETS CHECK IF THEY NEED A FORCED LOGIN

        if ($this->config->forceLogin > 1) {

            // GENERATES A RANDOM NUMBER FROM 1 - 100
            // IF THIS NUMBER IS LESS THAN THE NUMBER IN THE FORCE LOGIN SETTING
            // DELETE THE TOKEN FROM THE DB

            if (rand(1, 100) < $this->config->forceLogin) {

                $this->AuthModel->DeleteTokenByUserId($token->user_id);

                return;
            }
        }

        // SET USER SESSION
        $this->setUserSession($user, '1');

        $userID = $token->user_id;

        $this->rememberMeReset($userID, $selector);

        return;
    }

    /**
     * --------------------------------------------------------------------------
     * REMEMBER ME RESET
     * --------------------------------------------------------------------------
     *
     * each time a user is logged in using their remember me cookie
     * reset the validator and update the db
     * 
     * @param  int $userID
     * @param  int $selector
     * @return void
     */
    public function rememberMeReset($userID, $selector) {
        // DB QUERY        
        $existingToken = $this->AuthModel->GetAuthTokenBySelector($selector);

        if (empty($existingToken)) {

            return $this->rememberMe($userID);
        }

        $validator = random_string('crypto', 20);
        $expires = time() + 60 * 60 * 24 * $this->config->rememberMeExpire;

        // SET OUR TOKEN
        $token = $selector . ':' . $validator;

        if ($this->config->rememberMeRenew) {

            // SET DATA ARRAY
            $data = [
                'hashedvalidator' => hash('sha256', $validator),
                'expires' => date('Y-m-d H:i:s', $expires),
            ];
        } else {
            // SET DATA ARRAY
            $data = [
                'hashedvalidator' => hash('sha256', $validator),
            ];
        }

        $this->AuthModel->UpdateSelector($data, $selector);

        // ✅ FIXED: SET SECURE COOKIE        
        setcookie(
                "remember",
                $token,
                $expires,
                $this->AppConfig->cookiePath,
                $this->AppConfig->cookieDomain,
                true, // Force secure flag for HTTPS
                true  // Force HTTPOnly flag
        );
    }

    public function lockScreen() {
        if ($this->config->lockScreen) {

            $this->Session->set('isLoggedIn', false);
            $this->Session->set('lockscreen', true);

            return true;
        }

        return false;
    }

    /**
     * --------------------------------------------------------------------------
     * LOGOUT
     * --------------------------------------------------------------------------
     *
     * @return void
     */
    public function logout() {
        // REMOVE REMEMBER ME TOKEN FROM DB 
        //DESTROY SESSION
        $this->Session->destroy();

        return;
    }

    public function autoredirect() {

        // AUTO REDIRECTS BASED ON ROLE 
        $redirect = $this->config->assignRedirect;
//        print_r($_SESSION);
//        print_r($redirect);exit;
        return $redirect[$this->Session->get('employee_type')];
    }

    /**
     * Generate activation email content
     * 
     * @param array $user
     * @param string $token
     * @return string
     */
    private function generateActivationEmailContent($user, $token)
    {
        $activationLink = site_url('activate/' . $token);
        
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: #333;'>Welcome to Employee Management System</h2>
            <p>Hello {$user['employee_name']},</p>
            <p>Thank you for registering with our Employee Management System. To complete your registration, please click the activation link below:</p>
            <p style='text-align: center; margin: 30px 0;'>
                <a href='{$activationLink}' style='background-color: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; display: inline-block;'>
                    Activate Account
                </a>
            </p>
            <p>If the button doesn't work, you can copy and paste this link into your browser:</p>
            <p style='word-break: break-all; color: #666;'>{$activationLink}</p>
            <p>This link will expire in 24 hours.</p>
            <p>Best regards,<br>Employee Management System Team</p>
        </div>";
    }

    /**
     * Generate password reset email content
     * 
     * @param array $user
     * @param string $resetLink
     * @return string
     */
    private function generatePasswordResetEmailContent($user, $resetLink)
    {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: #333;'>Password Reset Request</h2>
            <p>Hello {$user['employee_name']},</p>
            <p>We received a request to reset your password. If you didn't make this request, you can safely ignore this email.</p>
            <p>To reset your password, please click the button below:</p>
            <p style='text-align: center; margin: 30px 0;'>
                <a href='{$resetLink}' style='background-color: #dc3545; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; display: inline-block;'>
                    Reset Password
                </a>
            </p>
            <p>If the button doesn't work, you can copy and paste this link into your browser:</p>
            <p style='word-break: break-all; color: #666;'>{$resetLink}</p>
            <p>This link will expire in 1 hour for security reasons.</p>
            <p>Best regards,<br>Employee Management System Team</p>
        </div>";
    }

}
