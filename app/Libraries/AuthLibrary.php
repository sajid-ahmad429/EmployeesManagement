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

            // GENERATE AND SEND ACTIVATION EMAIL
            $result = $this->ActivateEmail($user, $encodedtoken);

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

        // GENERATE A NEW TOKEN
        // SET THE TOKEN TYPE AS SECOND PARAMETER. Reset password token = 'reset_token'
        // Assuming 'auth/reset_password/{token}' route

        // Base64 encode the employee_id
        $encodedtoken = base64_encode($user['employee_id']);

        // Make the encoded token URL-safe
        $encodedtoken_url_safe = rtrim(strtr($encodedtoken, '+/', '-_'), '=');

        // Now, create the reset link
        $resetLink = site_url('resetpassword/' . $encodedtoken_url_safe);
        // You can return the reset link or store it for further use
        return $resetLink;  // Return the generated link instead of sending an email
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

        // set_Cookie
        setcookie(
                "remember",
                $token,
                $expires,
                $this->AppConfig->cookiePath,
                $this->AppConfig->cookieDomain,
                $this->AppConfig->cookieSecure,
                $this->AppConfig->cookieHTTPOnly
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

        // SET COOKIE        
        setcookie(
                "remember",
                $token,
                $expires,
                $this->AppConfig->cookiePath,
                $this->AppConfig->cookieDomain,
                $this->AppConfig->cookieSecure,
                $this->AppConfig->cookieHTTPOnly
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

}
