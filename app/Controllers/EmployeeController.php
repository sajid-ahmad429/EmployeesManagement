<?php

namespace App\Controllers;

use App\Models\DepartmentModel;
use App\Models\EmployeementModel;
use App\Models\EmployeeDataTableModel;
use CodeIgniter\Controller;

class EmployeeController extends Controller
{
    protected $departmentModel;
    protected $EmployeementModel;
    protected $EmployeeDataTableModel;

    public function __construct()
    {
        $this->departmentModel = new DepartmentModel();
        $this->EmployeementModel = new EmployeementModel();
        $this->EmployeeDataTableModel = new EmployeeDataTableModel();
    }

    // List all employees
    public function index()
    {
        $data = [];
        $data['activeMenu'] = 'employee';
        $data['assetsJs'] = array('app-employee-list');

        $data['departments'] = $this->departmentModel->where('status=', 1)->findAll();

        $data['totalcount'] = $this->EmployeementModel->getAllCount();
        $inactive = $this->EmployeementModel->inactiveCount();
        $data['active'] = $this->EmployeementModel->activeCount();
        $data['inactive'] = $inactive;
        echo view('admin/templates/header', $data);
        echo view('admin/employees/list');
        echo view('admin/templates/footer');
    }

    public function showEmployee()
    {
        $db = \Config\Database::connect();
        $data = [];
        $data['activeMenu'] = 'showemployee';

        // Assuming session is already loaded
        $session = session(); // Get session instance
        // Retrieve employee_id from session
        $employee_id = $session->get('employee_id');
        // Check if employee_id is set in session
        if ($employee_id) {
            // Assuming 'employee' is the table for employee data
            $builder = $db->table('employee'); 
            $builder->select('employee.*, department.department_name'); // Select necessary fields
            $builder->join('department', 'employee.department_id = department.department_id', 'left'); // Perform LEFT JOIN with department table
            $builder->where('employee.status', 1); // Only active employees (if you have a status column in the employee table)
            $builder->where('department.status', 1); // Only active departments (if you have a status column in the department table)

            // Add condition for employee_id from session
            $builder->where('employee.employee_id', $employee_id); // Filter by employee_id from session

            // Get the result
            $employees = $builder->get()->getResult();
        } else {
            // Handle case when employee_id is not set in session
            // You can redirect, show an error, or handle as needed
            $employees = []; // Empty result if session does not contain employee_id
        }


        // Send data to view
        $data['employees'] = $employees;
        $data['departments'] = $this->departmentModel->where('status', 1)->findAll(); // List of departments

        echo view('admin/templates/header', $data);
        echo view('admin/employees/employee_details');
        echo view('admin/templates/footer');
    }

    public function add()
    {
        if (isset($_SESSION['isLoggedIn']) && isset($_SESSION['employee_id'])) {
            // Load helpers
            helper(['form', 'url','header_helper']);
            
            // CSRF token setup
            $csrfName = csrf_token();
            $csrfHash = csrf_hash();

            // Get request data
            $request = service('request');
            $isUpdating = $this->request->getVar('user_id') && $this->request->getVar('user_id') != 0;

            // Define validation rules
            $validation = \Config\Services::validation();

            $isUpdating = $this->request->getVar('user_id') && $this->request->getVar('user_id') != 0;

            $validationRules = [
                'employeeName' => [
                    'rules' => 'required|string|min_length[3]|max_length[255]',
                    'errors' => [
                        'required' => 'The Employee Name field is required.',
                        'string' => 'The Employee Name must be a valid string.',
                        'min_length' => 'The Employee Name must be at least 3 characters long.',
                        'max_length' => 'The Employee Name cannot exceed 255 characters.',
                    ],
                ],
                
                'salary' => [
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => 'The Salary field is required.',
                        'numeric' => 'The Salary must be a valid number.',
                    ],
                ],
                'designation' => [
                    'rules' => 'required|string|min_length[3]|max_length[255]',
                    'errors' => [
                        'required' => 'The Designation field is required.',
                        'string' => 'The Designation must be a valid string.',
                        'min_length' => 'The Designation must be at least 3 characters long.',
                        'max_length' => 'The Designation cannot exceed 255 characters.',
                    ],
                ],

                'email' => [
                    'rules' => 'required|valid_email' . 
                            ($isUpdating ? '|is_unique[employee.email,employee_id,' . $this->request->getVar('user_id') . ']' : '|is_unique[employee.email]'),
                    'errors' => [
                        'required' => 'The Email field is required.',
                        'valid_email' => 'Please enter a valid email address.',
                        'is_unique' => 'This email is already in use.',
                    ],
                ],


                // Conditional password validation
                'password' => [
                    'rules' => ($isUpdating ? 'permit_empty' : 'required') . '|min_length[8]|max_length[255]',
                    'errors' => [
                        'required' => 'The Password field is required.',
                        'permit_empty' => '',
                        'min_length' => 'The Password must be at least 8 characters long.',
                        'max_length' => 'The Password cannot exceed 255 characters.',
                    ],
                ],
                'confirmPassword' => [
                    'rules' => ($isUpdating ? 'permit_empty' : 'required') . '|matches[password]',
                    'errors' => [
                        'required' => 'The Confirm Password field is required.',
                        'permit_empty' => '',
                        'matches' => 'The Confirm Password does not match the Password.',
                    ],
                ],
            ];

            // Validate the input
            if (!$this->validate($validationRules)) {
                // Validation failed
                return $this->response->setJSON([
                    'validation' => $this->validator->getErrors(),
                    'status' => 2,
                    'message' => 'Failed To Save Employee. Please Check All Fields Are Filled Properly.',
                    'acftkn' => [
                        'acftkname' => $csrfName,
                        'acftknhs' => $csrfHash,
                    ],
                ]);
            }

            // Prepare data array
            $data = [
                'employee_name'   => $this->request->getPost('employeeName'),
                'department_id'   => $this->request->getPost('department'),
                'salary'          => $this->request->getPost('salary'),
                'designation'     => $this->request->getPost('designation'),
                'employee_type'   => $this->request->getPost('employeeType'),
                'email'           => $this->request->getPost('email'),
                'password'        => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            ];


            try {
                if ($isUpdating) {
                    // Update existing user
                    $userId = $request->getPost('user_id');
                    $user = $this->EmployeementModel->where('employee_id',$userId)->first();
                
                    if ($user) {
                        // Save the previous data for tracking (optional)
                        $previousUpdateData = array();
                        $select_array = array_keys($data);
                        $previousUpdateData = $this->EmployeementModel->select($select_array)->where('employee_id', $userId)->findAll(); // Corrected select method
                        
                        $this->EmployeementModel->set($data)->where('employee_id', $userId)->update();
                        // Track changes (if needed, you can implement this logic)
                        track_activity($previousUpdateData, $this->EmployeementModel, $data, $userId, 'employee', 1);

                        return $this->response->setJSON([
                            'status' => 1,
                            'message' => 'Record Details Updated Successfully',
                            'acftkn' => [
                                'acftkname' => $csrfName,
                                'acftknhs' => $csrfHash,
                            ],
                        ]);
                    } else {
                        return $this->response->setJSON([
                            'status' => 2,
                            'message' => 'User not found',
                            'acftkn' => [
                                'acftkname' => $csrfName,
                                'acftknhs' => $csrfHash,
                            ],
                        ]);
                    }
                } else {
                    // Create a new user
                    $this->EmployeementModel->save($data);
                    return $this->response->setJSON([
                        'status' => 1,
                        'message' => 'Record Details Added Successfully',
                        'acftkn' => [
                            'acftkname' => $csrfName,
                            'acftknhs' => $csrfHash,
                        ],
                    ], 201); // Created
                }
            } catch (\Exception $e) {
                // Log the exception message
                log_message('error', 'Error creating or updating user: ' . $e->getMessage());

                // Return JSON response with error message
                return $this->response->setJSON([
                    'status' => 0,
                    'message' => 'An error occurred while processing the request. Please try again.',
                    'acftkn' => [
                        'acftkname' => $csrfName,
                        'acftknhs' => $csrfHash,
                    ],
                ], 500); // Internal Server Error
            }
        }
    }

    public function getRecordDetails()
    {
        // Initialize response data
        $returnData = [
            'status' => 0,
            'message' => 'Failed',
            'acftkn' => [
                'acftkname' => csrf_token(),
                'acftknhs' => csrf_hash(),
            ]
        ];

        // Check if 'id' parameter is present in the request
        $idEncoded = $this->request->getVar('id');
        if ($idEncoded) {
            try {
                // Decode the ID
                $id = base64_decode($idEncoded);
                // Fetch user data from the database
                $userData = $this->EmployeementModel->where(['status' => 1, 'employee_id' => $id])->first();
                if ($userData) {
                    // Prepare response data
                    $returnData = [
                        'status' => 1,
                        'message' => 'Success',
                        'data' => $userData,
                        'acftkn' => [
                            'acftkname' => csrf_token(),
                            'acftknhs' => csrf_hash(),
                        ]
                    ];
                } else {
                    $returnData['status'] = 2;
                    $returnData['message'] = 'Invalid Data...!';
                }
            } catch (\Exception $e) {
                // Handle exception and log the error
                log_message('error', 'Error fetching user details: ' . $e->getMessage());
                $returnData['message'] = 'An error occurred. Please try again.';
            }
        } else {
            $returnData['status'] = 2;
            $returnData['message'] = 'Invalid Request...!';
        }

        // Return JSON response
        return $this->response->setJSON($returnData);
    }



    public function getDepartmentData() {
        helper(['form', 'url']);
        $data = [];
        $returndata['acftkn']['acftkname'] = csrf_token();
        $returndata['acftkn']['acftknhs'] = csrf_hash();
        $datatable_where['emp.status !='] = 2;
        // Check if 'start' is set in $_POST
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $list = $this->EmployeeDataTableModel->get_datatables($datatable_where);
        $no = $start;
        if ($list != NULL) {
            foreach ($list as $EmployeeList) {
                $avtarData['sizeData'] = $EmployeeList;
                $div = view('admin/employees/avtarDiv', $avtarData);
                $statusDiv = view('admin/employees/statusDiv', $avtarData);
                $actionDiv = view('admin/employees/actionDiv', $avtarData);

                
                $no++;
                $row = array();
                $row[] = "<td>" . $no . "</td>";
                $row[] = "<td>" . $actionDiv . "</td>";
                $row[] = "<td>" . $div . "</td>";
                $row[] = "<td>" . $EmployeeList['department_name'] . "</td>";
                $row[] = "<td>" . '₹ ' . number_format($EmployeeList['salary'], 2) . "</td>";
                $row[] = "<td>" . $EmployeeList['designation'] . "</td>";
                $row[] = "<td>" . $EmployeeList['employee_type'] . "</td>";
                $row[] = "<td>" . $datetime = date('d M Y, h:i A', strtotime($EmployeeList['created_at'])) . "</td>";
                $row[] = "<td>" . $statusDiv . "</td>";
                $data[] = $row;
            }
        }

        $output = array(
            "draw" => isset($_POST['draw']) ? $_POST['draw'] : 1, // Set a default value if 'draw' is not set
            "recordsTotal" => $this->EmployeeDataTableModel->count_all($datatable_where),
            "recordsFiltered" => $this->EmployeeDataTableModel->count_filtered($datatable_where),
            "totalActiveRecods" => $this->EmployeeDataTableModel->count_ActiveRecordsfiltered($datatable_where),
            "totalInActiveRecods" => $this->EmployeeDataTableModel->count_InActiveRecordsfiltered($datatable_where),
            "data" => $data,
        );
        echo json_encode($output);
    }

}
