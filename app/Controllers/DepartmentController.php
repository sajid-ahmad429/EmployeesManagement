<?php

namespace App\Controllers;

use App\Models\DepartmentModel;
use App\Models\DepartmentDataTableModel;
use CodeIgniter\Controller;

class DepartmentController extends Controller
{
    protected $departmentModel;

    public function __construct()
    {
        $this->departmentModel = new DepartmentModel();
        $this->DepartmentDataTableModel = new DepartmentDataTableModel();
    }

    // List all departments
    public function index()
    {
        $data = [];
        $data['activeMenu'] = 'department';
        $data['assetsJs'] = array('app-department-list');
        $data['totalcount'] = $this->departmentModel->getAllCount();
        $inactive = $this->departmentModel->inactiveCount();
        $data['active'] = $this->departmentModel->activeCount();
        $data['inactive'] = $inactive;
        echo view('admin/templates/header', $data);
        echo view('admin/departments/list');
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

            $validationRules = [
                'departmentName' => [
                    'rules' => 'required|string|min_length[5]|max_length[255]' . 
                              ($isUpdating ? '' : '|is_unique[department.department_name]'),
                    'errors' => [
                        'required' => 'The Department Name field is required.',
                        'string' => 'The Department Name must be a valid string.',
                        'min_length' => 'The Department Name must be at least 5 characters long.',
                        'max_length' => 'The Department Name cannot exceed 255 characters.',
                        'is_unique' => 'This Department Name already exists.',
                    ],
                ],
                'departmentStatus' => [
                    'rules' => 'required|in_list[0,1]',
                    'errors' => [
                        'required' => 'The Department Status field is required.',
                        'in_list' => 'The Department Status must be either 0 or 1.',
                    ],
                ],
            ];
            

            // Validate the input
            if (!$this->validate($validationRules)) {
                // Validation failed
                return $this->response->setJSON([
                    'validation' => $this->validator->getErrors(),
                    'status' => 2,
                    'message' => 'Failed To Save Department. Please Check All Fields Are Filled Properly.',
                    'acftkn' => [
                        'acftkname' => $csrfName,
                        'acftknhs' => $csrfHash,
                    ],
                ]);
            }

            // Prepare data array
            $data = [
                'department_name' => $this->request->getPost('departmentName'),
                'status' => $this->request->getPost('departmentStatus'),
            ];

            try {
                if ($isUpdating) {
                    // Update existing user
                    $userId = $request->getPost('user_id');
                    $user = $this->departmentModel->where('department_id',$userId)->first();
                
                    if ($user) {
                        // Save the previous data for tracking (optional)
                        $previousUpdateData = array();
                        $select_array = array_keys($data);
                        $previousUpdateData = $this->departmentModel->select($select_array)->where('department_id', $userId)->findAll(); // Corrected select method
                        
                        $this->departmentModel->set($data)->where('department_id', $userId)->update();
                        // Track changes (if needed, you can implement this logic)
                        track_activity($previousUpdateData, $this->departmentModel, $data, $userId, 'department', 1);

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
                    $this->departmentModel->save($data);
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
                $userData = $this->departmentModel->where(['status' => 1, 'department_id' => $id])->first();
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
        $datatable_where['dp.status !='] = 2;
        // Check if 'start' is set in $_POST
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $list = $this->DepartmentDataTableModel->get_datatables($datatable_where);
        $no = $start;
        if ($list != NULL) {
            foreach ($list as $DepartmentList) {
                $avtarData['sizeData'] = $DepartmentList;
                $div = view('admin/departments/avtarDiv', $avtarData);
                $statusDiv = view('admin/departments/statusDiv', $avtarData);
                $actionDiv = view('admin/departments/actionDiv', $avtarData);

                $no++;
                $row = array();
                $row[] = "<td>" . $no . "</td>";
                $row[] = "<td>" . $actionDiv . "</td>";
                $row[] = "<td>" . $div . "</td>";
                $row[] = "<td>" . $datetime = date('d M Y, h:i A', strtotime($DepartmentList['created_at'])) . "</td>";
                $row[] = "<td>" . $statusDiv . "</td>";
                $data[] = $row;
            }
        }

        $output = array(
            "draw" => isset($_POST['draw']) ? $_POST['draw'] : 1, // Set a default value if 'draw' is not set
            "recordsTotal" => $this->DepartmentDataTableModel->count_all($datatable_where),
            "recordsFiltered" => $this->DepartmentDataTableModel->count_filtered($datatable_where),
            "totalActiveRecods" => $this->DepartmentDataTableModel->count_ActiveRecordsfiltered($datatable_where),
            "totalInActiveRecods" => $this->DepartmentDataTableModel->count_InActiveRecordsfiltered($datatable_where),
            "data" => $data,
        );
        echo json_encode($output);
    }

}
