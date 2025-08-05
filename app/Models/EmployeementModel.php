<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeementModel extends Model
{
    // Specify the table name
    protected $table      = 'employee';
    
    // Specify the primary key of the table
    protected $primaryKey = 'employee_id';
    
    // Define the allowed fields for insert/update operations
    protected $allowedFields = ['employee_name','department_id','salary','designation','employee_type','email','password','status','trash','created_at','updated_at'];

    /**
     * Saves the users login session to DB
     *
     * @param  mixed $data
     * @return void
     */
    public function insertData($data) {
        $data = $this->db->table('employee')
                ->insert($data);
        return $data;
    }

    public function getListAll() {
        $data = $this->table('employee')->orderBy('employee_id', 'desc')->findAll();
        return $data;
    }

    public function getAllAdminRecords() {
        $data = $this->table('employee')->where('status !=', 2)->where('trash', 0)->orderBy('employee_id', 'desc')->findAll();
        return $data;
    }

    public function getAllActiveRecords() {
        $data = $this->table('employee')->where('status', 1)->where('trash', 0)->orderBy('employee_id', 'desc')->findAll();
        return $data;
    }

    public function getListAllJoinWhere($select, $joinArray, $whereArray, $orderByArray, $tableName) {
        $this->select($select);
        if ($joinArray != NULL) {
            foreach ($joinArray as $joinArrayData) {
                $this->join($joinArrayData['tablename'], $joinArrayData['joinCondition'], $joinArrayData['joinType']);
            }
        }
        if ($whereArray != NULL) {
            $this->where($whereArray);
        }
        if ($orderByArray != NULL) {
            foreach ($orderByArray as $orderByArrayData) {
                $this->orderBy($orderByArrayData['orderKey'], $orderByArrayData['orderBy']);
            }
        }
        $data = $this->table($tableName)->findAll();
        return $data;
    }

    public function getListAllJoinWhereLimitOffset($joinArray, $whereArray, $orderByArray, $tableName, $limit, $offset) {
        $this->select($select);
        if ($joinArray != NULL) {
            foreach ($joinArray as $joinArrayData) {
                $this->join($joinArrayData['tablename'], $joinArrayData['joinCondition'], $joinArrayData['joinType']);
            }
        }
        if ($whereArray != NULL) {
            $this->where($whereArray);
        }
        if ($orderByArray != NULL) {
            foreach ($orderByArray as $orderByArrayData) {
                $this->orderBy($orderByArrayData['orderKey'], $orderByArrayData['orderBy']);
            }
        }
        if ($limit != NULL) {
            if ($offset != NULL) {
                $this->limit($limit);
            } else {
                $this->limit($limit, $offset);
            }
            $data = $this->table($tableName)->find();
        } else {
            $data = $this->table($tableName)->findAll();
        }
        return $data;
    }

    public function getDataBySessionId() {
        $result = array();
        $subscriptionWhere['employee_id'] = $_SESSION['employee_id'];
        $subscriptionWhere['status'] = 1;
        $result = $this->table('employee')->where($subscriptionWhere)->findAll();
        return $result;
    }

    public function getDataById($id) {
        $result = array();
        $subscriptionWhere['employee_id'] = $id;
        $subscriptionWhere['status'] = 1;
        $result = $this->table('employee')->where($subscriptionWhere)->findAll();

        return $result;
    }

    public function getDataWhere($whereArray) {
        $result = array();
        $result = $this->table('employee')->where($whereArray)->findAll();
        return $result;
    }

    public function updateData($data, $userId) {

        return $this->db->table('employee')
                        ->where('employee_id', $userId)->update($data);
    }

    public function updateDataWhere($updateData, $whereArray) {
        $result = array();

        $result = $this->table('employee')->where($whereArray)->set($updateData)->update();
        return $result;
    }

    function getAllCount() {
        $query = $this->db->table('employee')->where('status!=', 2)->where('trash', 0)->countAllResults();
        if ($query > 0) {
            $test = $query;
            return $test;
        } else {
            return 0;
        }
    }

    function InactiveCount() {
        $query = $this->db->table('employee')->select('*')->where('status', 0)->countAllResults();
        if ($query > 0) {
            $test = $query;
            return $test;
        } else {
            return 0;
        }
    }

    public function activeCount() {
        $query = $this->db->table('employee')->select('*')->where('trash', 0)->where('status', 1)->countAllResults();
        if ($query > 0) {
            $test = $query;
            return $test;
        } else {
            return 0;
        }
    }
}
