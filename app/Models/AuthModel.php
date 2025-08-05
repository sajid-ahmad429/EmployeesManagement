<?php

/**
 * --------------------------------------------------------------------
 * CODEIGNITER 4 - SimpleAuth
 * --------------------------------------------------------------------
 *
 * This content is released under the MIT License (MIT)
 *
 * @package    SimpleAuth
 * @author     GeekLabs - Lee Skelding 
 * @license    https://opensource.org/licenses/MIT	MIT License
 * @link       https://github.com/GeekLabsUK/SimpleAuth
 * @since      Version 1.0
 * 
 */

namespace App\Models;

use CodeIgniter\Model;

class AuthModel extends Model {

    protected $table = 'employee';
    protected $primaryKey = 'employee_id';
    protected $allowedFields = ['employee_id','employee_name', 'department_id', 'salary', 'designation', 
        'employee_type', 'email', 'password'
    ];
    protected $beforeInsert = ['beforeInsert'];
    protected $beforeUpdate = ['beforeUpdate'];

    /**
     * Runs before inserting data
     *
     * @param  mixed $data
     * @return void
     */
    protected function beforeInsert(array $data) {

        $data = $this->passwordHash($data);
        return $data;
    }

    /**
     * Runs before Updating data
     *
     * @param  mixed $data
     * @return void
     */
    protected function beforeUpdate(array $data) {

        $data = $this->passwordHash($data);
        return $data;
    }

    /**
     * passwordHash
     *
     * @param  mixed $data
     * @return void
     */
    protected function passwordHash(array $data) {

        if (isset($data['data']['password']))
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        return $data;
    }

    public function verifyUser($email, $password, $role) {

//        $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        $data['email'] = trim($email);
        $data['employee_type'] = $role;
        $builder = $this->db->table('employee');
//        $builder->select('id', 'status', 'firstname', 'password');
        $builder->where($data);
        $result = $builder->get()->getResultArray();
        if ($result != NULL && isset($result[0]['password'])) {
            if ($result[0]['status'] != 1) {
                return 2;
            } else {
                if (password_verify($password, $result[0]['password'])) {
                    return 1;
                } else {
                    return 0;
                }
            }
        } else {
            return 0;
        }
    }

    /**
     * Saves the employee login session to DB
     *
     * @param  mixed $data
     * @return void
     */
    public function LogLogin($data) {
        $this->db->table('auth_logins')
                ->insert($data);
    }

    public function updatedAt($id) {
        $builder = $this->db->table('employee');
        $builder->where('employee_id', $id);
        $builder->update(['updated_at' => date('Y-m-d h:i:s')]);
        $result = $builder->get();
        if ($this->db->affectedRows() == 1) {
            return true;
        } else {
            return false;
        }
    }

}
