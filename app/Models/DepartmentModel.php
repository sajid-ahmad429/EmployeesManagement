<?php

namespace App\Models;

use CodeIgniter\Model;

class DepartmentModel extends Model
{
    // Specify the table name
    protected $table      = 'department';
    
    // Specify the primary key of the table
    protected $primaryKey = 'department_id';
    
    // Define the allowed fields for insert/update operations
    protected $allowedFields = ['department_name','status','trash','created_at','updated_at'];

    /**
     * Saves the users login session to DB
     *
     * @param  mixed $data
     * @return void
     */
    public function insertData($data) {
        $data = $this->db->table('department')
                ->insert($data);
        return $data;
    }

    public function getListAll() {
        $data = $this->table('department')->orderBy('department_id', 'desc')->findAll();
        return $data;
    }

    public function getAllAdminRecords() {
        $data = $this->table('department')->where('status !=', 2)->where('trash', 0)->orderBy('department_id', 'desc')->findAll();
        return $data;
    }

    public function getAllActiveRecords() {
        $data = $this->table('department')->where('status', 1)->where('trash', 0)->orderBy('department_id', 'desc')->findAll();
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
        $subscriptionWhere['department_id'] = $_SESSION['department_id'];
        $subscriptionWhere['status'] = 1;
        $result = $this->table('department')->where($subscriptionWhere)->findAll();
        return $result;
    }

    public function getDataById($id) {
        $result = array();
        $subscriptionWhere['department_id'] = $id;
        $subscriptionWhere['status'] = 1;
        $result = $this->table('department')->where($subscriptionWhere)->findAll();

        return $result;
    }

    public function getDataWhere($whereArray) {
        $result = array();
        $result = $this->table('department')->where($whereArray)->findAll();
        return $result;
    }

    public function updateData($data, $userId) {

        return $this->db->table('department')
                        ->where('department_id', $userId)->update($data);
    }

    public function updateDataWhere($updateData, $whereArray) {
        $result = array();

        $result = $this->table('department')->where($whereArray)->set($updateData)->update();
        return $result;
    }

    function getAllCount() {
        $query = $this->db->table('department')->where('status!=', 2)->where('trash', 0)->countAllResults();
        if ($query > 0) {
            $test = $query;
            return $test;
        } else {
            return 0;
        }
    }

    function InactiveCount() {
        $query = $this->db->table('department')->select('*')->where('status', 0)->countAllResults();
        if ($query > 0) {
            $test = $query;
            return $test;
        } else {
            return 0;
        }
    }

    public function activeCount() {
        $query = $this->db->table('department')->select('*')->where('trash', 0)->where('status', 1)->countAllResults();
        if ($query > 0) {
            $test = $query;
            return $test;
        } else {
            return 0;
        }
    }
}
