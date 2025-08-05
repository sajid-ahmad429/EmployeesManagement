<?php

namespace App\Controllers;

use App\Models\AuthModel;
use App\Libraries\AuthLibrary;
use App\Models\DepartmentModel;

class Auth extends BaseController
{
    public function __construct() {
        $this->request = \Config\Services::request();
        $this->AuthModel = new AuthModel();  // Correctly reference AuthModel
        $this->DepartmentModel = new DepartmentModel();  // Correctly reference AuthModel
        $this->config = config('Auth');
        $this->session = session();  // Ensure session is properly initialized
        $validation = \Config\Services::validation();
        $this->Auth = new AuthLibrary;
    }

    public function index()
    {    
        return redirect()->to('sysCtrlLogin');
    }

    protected function validateUserCredentials($email, $password)
    {
        // Fetch user data by email
        $userData = $this->AuthModel->where('email', $email)->first();

        // Check if the user exists and the password matches
        if ($userData && password_verify($password, $userData['password'])) {
            return $userData;
        }

        return false;
    }

    public function login()
    {
        helper(['form', 'url']);
  
        $viewData['config'] = $this->config;
        $viewData['errorMessage'] = '';

        // Check if the user is already logged in
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to($this->Auth->autoRedirect());
        }
        
        if ($this->request->getMethod() == 'POST') {
            // ✅ FIXED: Add CSRF protection
            if (!$this->validate(['csrf_token' => 'required'])) {
                $viewData['errorMessage'] = 'Security token mismatch. Please try again.';
                return view('admin/auth/login', $viewData);
            }

            // ✅ FIXED: Add basic rate limiting
            $ipAddress = $this->request->getIPAddress();
            $rateLimitKey = 'login_attempts_' . $ipAddress;
            $attempts = $this->session->get($rateLimitKey) ?? 0;
            
            if ($attempts >= 5) {
                $viewData['errorMessage'] = 'Too many login attempts. Please try again later.';
                return view('admin/auth/login', $viewData);
            }
            
            // Set validation rules
            $rules = [
                'email' => 'required|valid_email|validateExists[email]',
                'password' => 'required|min_length[6]|max_length[255]|validateUser[email,password]',
            ];

            // Custom error messages
            $errors = [
                'email' => [
                    'validateExists' => 'The email address does not exist in our records.',
                ],
                'password' => [
                    'validateUser' => 'Email or Password do not match',
                ]
            ];


            if (!$this->validate($rules, $errors)) {
                // ✅ FIXED: Increment failed attempts counter
                $this->session->set($rateLimitKey, $attempts + 1);
                
                // Failed validation
                $data['validation'] = $this->validator;
                $this->Auth->loginlogFail($this->request->getVar('email'));
            } else {
                // Get email and remember me from POST
                $email = $this->request->getVar('email');
                $rememberMe = $this->request->getVar('rememberme');
                $userData = $this->AuthModel->where('email', $email)->first();
                

                if ($userData === false) {
                    // ✅ FIXED: Increment failed attempts and generic error message
                    $this->session->set($rateLimitKey, $attempts + 1);
                    $viewData['errorMessage'] = 'Invalid credentials. Please try again.';
                    
                } else {
                    // Check user status
                    if ($userData['status'] != 1) {
                        $viewData['errorMessage'] = 'Account access restricted. Please contact support.';
                    } else {
                        // ✅ FIXED: Reset failed attempts on successful login
                        $this->session->remove($rateLimitKey);
                        
                        // Check password match with stored hash in database
                        // Log the user in
                        $this->Auth->Loginuser($email, $rememberMe);
                            
                        // Redirect based on role
                        if ($this->session->get('employee_type')) {
                            return redirect()->to($this->Auth->autoRedirect());
                        }
                    }
                }
            }
        }
        // Set up view
        return view('admin/auth/login', $viewData);
    }

      /*
      |--------------------------------------------------------------------------
      | REGISTER USER
      |--------------------------------------------------------------------------
      |
      | Get post data from register.php view
      | Set and Validate rules
      | pass over to library RegisterUser
      | If successfull save user details to DB
      | check if we should send activation email
      | return true / false
      |
     */

     public function register()
     {
        $data['departments'] = $this->DepartmentModel->where('status', 1)->findAll();
         // Check if the form is submitted
         if ($this->request->getMethod() === 'POST') {
             // Define validation rules
             $rules = [
                'email' => 'required|valid_email|is_unique[employee.email]',
                'employeename' => 'required|min_length[6]|max_length[50]', // Employee name validation
                'password' => [
                    'required',
                    'min_length[8]',
                    'regex_match[/[a-z]/]',       // At least one lowercase letter
                    'regex_match[/[A-Z]/]',       // At least one uppercase letter
                    'regex_match[/[0-9]/]',       // At least one number
                    'regex_match[/[@$!%*?&]/]',   // At least one special character
                ],
                'confirmPassword' => [
                    'required',
                    'min_length[8]',
                    'matches[password]',         // Ensure confirm password matches password
                    'regex_match[/[a-z]/]',      // At least one lowercase letter
                    'regex_match[/[A-Z]/]',      // At least one uppercase letter
                    'regex_match[/[0-9]/]',      // At least one number
                    'regex_match[/[@$!%*?&]/]',  // At least one special character
                ],
                'department' => 'required', // Department must be selected and exist
                'employeeType' => 'required|in_list[admin,employee]', // Employee type should be either 'admin' or 'employee'
                'salary' => 'required|numeric|min_length[1]', // Salary must be a number and required
                'designation' => 'required|min_length[3]|max_length[50]', // Designation should be between 3 to 50 characters
            ];
            
           
 
            // Run validation
            if (!$this->validate($rules)) {
                // Return with validation errors
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            } 
             // ✅ FIXED: Hash password before storing
             $userData = [
                'employee_name'   => $this->request->getPost('employeename'),
                'department_id'   => $this->request->getPost('department'),
                'salary'          => $this->request->getPost('salary'),
                'designation'     => $this->request->getPost('designation'),
                'employee_type'   => $this->request->getPost('employeeType'),
                'email'           => $this->request->getPost('email'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'status'          => 1, // Set active status
             ];
 
             // Save user data
             if ($this->AuthModel->insert($userData)) {
                 return redirect()->to('/sysCtrlLogin')->with('success', 'Registration successful. You can now login.');
             } else {
                 return redirect()->back()->with('error', 'Failed to register. Please try again.');
             }
         }
 
        return view('admin/auth/register',$data);
     }

     /*
      |--------------------------------------------------------------------------
      | REGISTER USER
      |--------------------------------------------------------------------------
      |
      | Get post data from forgotpassword.php view
      | Set and Validate rules
      | Save to DB
      | Set session data
      |
     */

     public function forgotPassword() {
        if ($this->request->getMethod() == 'POST') {
            $rules = [
                'email' => 'required|valid_email|validateExists[email]',
            ];

            $errors = [
                'email' => [
                    'validateExists' => 'The email address does not exist in our records.',
                ],
            ];

            if (!$this->validate($rules, $errors)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            } else {
                // Validation passed, call the ForgotPassword method
                $resetLink = $this->Auth->ForgotPassword($this->request->getVar('email'));
                // Check if resetLink is generated
                if ($resetLink) {
                    // Set success flash message with the reset link
                      // Store reset link as a separate flash data
                      session()->setFlashdata([
                        'success' => 'A password reset link has been generated.',
                        'resetLink' => $resetLink
                    ]);
                    
                } else {
                    // Handle if user is not found or any other issue
                    session()->setFlashdata('error', 'The email address provided is not registered.');
                }
            }
        }
    
        // Return the forgot password view with any data
        return view('admin/auth/forgotpassword');
    }

     /*
      |--------------------------------------------------------------------------
      | RESET PASSWORD
      |--------------------------------------------------------------------------
      |
      | Takes the response from a a rest link from users reset email
      | Pass the user id and token to Library resetPassword();
      |
     */

     public function resetPassword($id) {
        // PASS TO LIBRARY
        // Here if i using email configuration then we can use
        // $id = $this->Auth->resetPassword($id);

        // REDIRECT PASSING USER ID TO UPDATE PASSWORD FORM
        $this->updatepassword($id);
    }

     /*
      |--------------------------------------------------------------------------
      | UPDATE PASSWORD
      |--------------------------------------------------------------------------
      |
      | Get post data from resetpassword.php view
      | Save new password to DB
      |
     */

     public function updatepassword($encoded_token) {
        // Decode the base64 encoded id
        $encoded_token = strtr($encoded_token, '-_', '+/');
        $decoded_token = base64_decode($encoded_token);
        
        // IF ITS A POST REQUEST DO YOUR STUFF ELSE SHOW VIEW
        if ($this->request->getMethod() == 'POST') {
            // SET RULES
            $rules = [
                'password' => [
                    'required',
                    'min_length[8]',
                    'regex_match[/[a-z]/]',       // At least one lowercase letter
                    'regex_match[/[A-Z]/]',       // At least one uppercase letter
                    'regex_match[/[0-9]/]',       // At least one number
                    'regex_match[/[@$!%*?&]/]',   // At least one special character
                ],
                'confirmPassword' => [
                    'required',
                    'min_length[8]',
                    'matches[password]',         // Ensure confirm password matches password
                    'regex_match[/[a-z]/]',      // At least one lowercase letter
                    'regex_match[/[A-Z]/]',      // At least one uppercase letter
                    'regex_match[/[0-9]/]',      // At least one number
                    'regex_match[/[@$!%*?&]/]',  // At least one special character
                ],
            ];
            
            // VALIDATE RULES
            if (!$this->validate($rules)) {
                // Validation failed, pass errors to view
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            } else {
                // RULES PASSED
    
                // Retrieve the form data
                $password = $this->request->getVar('password');
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password
    
                // Create user data to update
                $user = [
                    'employee_id' => $decoded_token,
                    'password' => $hashedPassword, // ✅ FIXED: Use hashed password
                ];

                // Pass to the Auth model to update password
                $this->Auth->updatepassword($user);
    
                // Set success flash message
                session()->setFlashdata('success', 'Your password has been updated successfully.');
    
                // Redirect to login page
                return redirect()->route('sysCtrlLogin');
            }
        }
    
        // Show view with user id
        $data = [
            'id' => $encoded_token,  // Pass the base64 encoded ID to the view
        ];
    
        echo view('admin/auth/resetpassword', $data);
    }
    
    
    
    /*
      |--------------------------------------------------------------------------
      | LOG USER OUT
      |--------------------------------------------------------------------------
      |
      | Destroy session
      |
     */

     public function logout() {
        $this->Auth->logout();

        return redirect()->to('/');
    }

    public function countList() {
        helper(['form', 'url', 'genral']);
        $data['activeMenu'] ='dashboard';
        
        // $data['viewFileName'] = 'admin/auth/superadmin';
        // echo view('admin/templates/header', $data);
        echo view('admin/templates/header', $data);
        echo view('admin/auth/superadmin', $data);
        echo view('admin/templates/footer', $data);
    }
}
