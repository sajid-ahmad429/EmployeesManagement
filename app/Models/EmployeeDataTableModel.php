<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeDataTableModel extends Model {

    protected $table = 'employee';
    protected $allowedFields = ['employee_id', 'employee_name', 'status', 'trash', 'created_at', 'updated_at'];
    var $column_order = array('emp.employee_id', 'emp.employee_id', 'emp.employee_name', 'dp.department_name', 'emp.salary', 'emp.designation', 'emp.employee_type', 'emp.status', 'emp.created_at'); //set column field database for datatable orderable
    var $column_search = array('emp.employee_id', 'emp.employee_name', 'dp.department_name', 'emp.salary', 'emp.designation'); //set column field database for datatable searchable 
    var $order = array('emp.employee_id' => 'DESC', 'emp.employee_name' => 'ASC', 'emp.created_at' => 'DESC'); // default order     

    /**
     * Saves the employee login session to DB
     *
     * @param  mixed $data
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    private function getBaseQuery() {
        $db = \Config\Database::connect();
        $builder = $db->table('employee as emp');
        $builder->select('emp.employee_id, emp.employee_name, emp.salary, emp.designation, emp.employee_type, emp.status, emp.created_at, emp.updated_at, dp.department_name');
        $builder->join('department as dp', 'dp.department_id = emp.department_id', 'left');
        $builder->where('emp.status !=', 2);
        return $builder;
    }

    public function get_datatables($data_where) {
        $builder = $this->getBaseQuery();
        
        // Apply additional where conditions
        if (!empty($data_where)) {
            $builder->where($data_where);
        }

        // Apply search filters
        $this->applySearchFilters($builder);
        
        // Apply ordering
        $this->applyOrdering($builder);
        
        // Apply pagination
        if (isset($_POST['length']) && $_POST['length'] != -1) {
            $builder->limit($_POST['length'], $_POST['start'] ?? 0);
        }

        return $builder->get()->getResultArray();
    }

    public function count_InActiveRecordsfiltered($data_where) {
        $builder = $this->getBaseQuery();
        $data_where['emp.status'] = 0;
        
        if (!empty($data_where)) {
            $builder->where($data_where);
        }
        
        $this->applySearchFilters($builder);
        
        return $builder->countAllResults();
    }

    public function count_ActiveRecordsfiltered($data_where) {
        $builder = $this->getBaseQuery();
        $data_where['emp.status'] = 1;
        
        if (!empty($data_where)) {
            $builder->where($data_where);
        }
        
        $this->applySearchFilters($builder);
        
        return $builder->countAllResults();
    }

    public function count_filtered($data_where) {
        $builder = $this->getBaseQuery();
        
        if (!empty($data_where)) {
            $builder->where($data_where);
        }
        
        $this->applySearchFilters($builder);
        
        return $builder->countAllResults();
    }

    public function count_all($data_where) {
        $builder = $this->getBaseQuery();
        
        if (!empty($data_where)) {
            $builder->where($data_where);
        }
        
        return $builder->countAllResults();
    }

    private function applySearchFilters($builder) {
        if (isset($_POST['search']['value']) && !empty($_POST['search']['value'])) {
            $searchValue = $_POST['search']['value'];
            
            $builder->groupStart();
            foreach ($this->column_search as $i => $item) {
                if ($i === 0) {
                    $builder->like($item, $searchValue);
                } else {
                    $builder->orLike($item, $searchValue);
                }
            }
            $builder->groupEnd();
        }
    }

    private function applyOrdering($builder) {
        if (isset($_POST['order'])) {
            $columnIndex = $_POST['order']['0']['column'];
            $direction = $_POST['order']['0']['dir'];
            
            if (isset($this->column_order[$columnIndex])) {
                $builder->orderBy($this->column_order[$columnIndex], $direction);
            }
        } else {
            // Apply default ordering
            foreach ($this->order as $column => $direction) {
                $builder->orderBy($column, $direction);
                break; // Only apply the first default order
            }
        }
    }
}
