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
        // ✅ FIXED: Use parameterized queries to prevent SQL injection
        $builder = $this->db->table('employee');
        $builder->select('employee_id, status, password');
        $builder->where('email', trim($email));
        $builder->where('employee_type', $role);
        $result = $builder->get()->getResultArray();
        
        if ($result != NULL && isset($result[0]['password'])) {
            if ($result[0]['status'] != 1) {
                return 2; // Account disabled
            } else {
                if (password_verify($password, $result[0]['password'])) {
                    return 1; // Success
                } else {
                    return 0; // Wrong password
                }
            }
        } else {
            return 0; // User not found
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

    /**
     * Get authentication token by user ID
     *
     * @param  int $userID
     * @return object|null
     */
    public function GetAuthTokenByUserId($userID) {
        return $this->db->table('auth_tokens')
                       ->where('user_id', $userID)
                       ->get()
                       ->getRow();
    }

    /**
     * Get authentication token by selector
     *
     * @param  string $selector
     * @return object|null
     */
    public function GetAuthTokenBySelector($selector) {
        return $this->db->table('auth_tokens')
                       ->where('selector', $selector)
                       ->get()
                       ->getRow();
    }

    /**
     * Insert new authentication token
     *
     * @param  array $data
     * @return bool
     */
    public function insertToken($data) {
        return $this->db->table('auth_tokens')
                       ->insert($data);
    }

    /**
     * Update authentication token
     *
     * @param  array $data
     * @return bool
     */
    public function updateToken($data) {
        return $this->db->table('auth_tokens')
                       ->where('user_id', $data['user_id'])
                       ->update($data);
    }

    /**
     * Update selector
     *
     * @param  array $data
     * @param  string $selector
     * @return bool
     */
    public function UpdateSelector($data, $selector) {
        return $this->db->table('auth_tokens')
                       ->where('selector', $selector)
                       ->update($data);
    }

    /**
     * Delete token by user ID
     *
     * @param  int $userID
     * @return bool
     */
    public function DeleteTokenByUserId($userID) {
        return $this->db->table('auth_tokens')
                       ->where('user_id', $userID)
                       ->delete();
    }

    /**
     * Insert password reset token
     *
     * @param  array $data
     * @return bool
     */
    public function insertPasswordResetToken($data) {
        return $this->db->table('password_reset_tokens')
                       ->insert($data);
    }

    /**
     * Get password reset token
     *
     * @param  string $token
     * @return object|null
     */
    public function getPasswordResetToken($token) {
        return $this->db->table('password_reset_tokens')
                       ->where('token', $token)
                       ->where('expires_at >', date('Y-m-d H:i:s'))
                       ->get()
                       ->getRow();
    }

    /**
     * Delete password reset token
     *
     * @param  string $token
     * @return bool
     */
    public function deletePasswordResetToken($token) {
        return $this->db->table('password_reset_tokens')
                       ->where('token', $token)
                       ->delete();
    }

}
