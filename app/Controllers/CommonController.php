<?php

/**
 * --------------------------------------------------------------------
 * CI4- ACF- Unique Enterprises 
 * --------------------------------------------------------------------
 *
 * This content is released under the MIT License (MIT)
 *
 * @package    Unique Enterprises ACF
 * @author     Altctrlfix It Solutions Private Limited
 * @license     
 * @link        
 * @since      Version 1.0
 * 
 */

namespace App\Controllers;

//use App\Models\ProductImageModel;
use CodeIgniter\I18n\Time;

class CommonController extends BaseController {

    public function __construct() {
        $this->session = \Config\Services::session();
        $request = \Config\Services::request();
//        $this->ProductImageModel = new ProductImageModel();
    }

    public function chnage_status() {
        helper(['form', 'url', 'genral', 'header']);
        $db = \Config\Database::connect();
        $data = [];
    
        $id = base64_decode($this->request->getVar('id'));
        $status = $this->request->getVar('status');
        $tableName = base64_decode($this->request->getVar('name'));
    
        if (!$db->tableExists($tableName)) {
            return $this->response->setJSON(['error' => 'Table does not exist']);
        }
    
        // Determine the primary key field dynamically
        $primaryKeyField = 'id';
        if ($tableName === 'employee') {
            $primaryKeyField = 'employee_id';
        } elseif ($tableName === 'department') {
            $primaryKeyField = 'department_id';
        }
    
        $fields = $db->getFieldData($tableName);
        $fieldNames = [];
        foreach ($fields as $field) {
            $fieldNames[] = $field->name;
        }
    
        $previousUpdateData = $db->table($tableName)
            ->select($fieldNames)
            ->where($primaryKeyField, $id)
            ->get()
            ->getResultArray();
    
        if ($status == 2) {
            if ($tableName === 'employee' && $status === 2) {
                $result = $db->table($tableName)->set('status', $status)->where($primaryKeyField, $id)->delete();
                $data = ['employee_id' => $id, 'status' => $status];
            } elseif ($tableName === 'department') {
                $result = $db->table($tableName)->set('status', $status)->where($primaryKeyField, $id)->update(['status' => $status]);
                $data = ['department_id' => $id, 'status' => $status];
            } else {
                $result = $db->table($tableName)->set('status', $status)->set('trash', 1)->where($primaryKeyField, $id)->update();
                $data = [$primaryKeyField => $id, 'status' => $status];
            }
            $actionType = 4; // Delete action
            $this->session->setFlashdata('msg', 'The record has been deleted successfully.');
        } else {
            if ($tableName === 'employee' || $tableName === 'department') {
                $result = $db->table($tableName)->set('status', $status)->where($primaryKeyField, $id)->update();
            } else {
                $result = $db->table($tableName)->set('status', $status)->where($primaryKeyField, $id)->update();
            }
            $data = [$primaryKeyField => $id, 'status' => $status];
            $actionType = ($status == 0) ? 3 : 2; // Update action
            $this->session->setFlashdata('msg', 'The record has been updated successfully.');
        }
    
        if ($result) {
            track_activity($previousUpdateData, "", $data, $id, $tableName, $actionType);
            echo 1;
        } else {
            echo 0;
        }
    }
    

    /**
     * Get tables that reference the given table.
     *
     * @param \CodeIgniter\Database\BaseConnection $db
     * @param string $tableName
     * @return array
     */
//    function getReferencedTablesAndColumns($db, $tableName) {
//        $query = $db->query("
//            SELECT TABLE_NAME, COLUMN_NAME
//            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
//            WHERE REFERENCED_TABLE_NAME = ?", [$tableName]);
//
//        return $query->getResultArray();
//    }
}
