<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class OptimizePerformanceIndexes extends Migration
{
    public function up()
    {
        // Employee table optimizations
        $this->db->query('
            CREATE INDEX IF NOT EXISTS idx_employee_status_trash 
            ON employee(status, trash)
        ');
        
        $this->db->query('
            CREATE INDEX IF NOT EXISTS idx_employee_department_status 
            ON employee(department_id, status)
        ');
        
        $this->db->query('
            CREATE INDEX IF NOT EXISTS idx_employee_created_at 
            ON employee(created_at)
        ');
        
        $this->db->query('
            CREATE INDEX IF NOT EXISTS idx_employee_name_search 
            ON employee(employee_name)
        ');
        
        $this->db->query('
            CREATE INDEX IF NOT EXISTS idx_employee_type_status 
            ON employee(employee_type, status)
        ');
        
        // Department table optimizations  
        $this->db->query('
            CREATE INDEX IF NOT EXISTS idx_department_status_trash 
            ON department(status, trash)
        ');
        
        // Auth tokens table optimizations
        $this->db->query('
            CREATE INDEX IF NOT EXISTS idx_auth_tokens_expires 
            ON auth_tokens(expires)
        ');
        
        $this->db->query('
            CREATE INDEX IF NOT EXISTS idx_auth_tokens_user_expires 
            ON auth_tokens(user_id, expires)
        ');
        
        // Auth logins table optimizations
        $this->db->query('
            CREATE INDEX IF NOT EXISTS idx_auth_logins_employee_date 
            ON auth_logins(employee_id, date)
        ');
        
        $this->db->query('
            CREATE INDEX IF NOT EXISTS idx_auth_logins_successful_date 
            ON auth_logins(successful, date)
        ');
        
        // Password reset tokens table optimizations
        $this->db->query('
            CREATE INDEX IF NOT EXISTS idx_password_reset_expires 
            ON password_reset_tokens(expires_at)
        ');
        
        $this->db->query('
            CREATE INDEX IF NOT EXISTS idx_password_reset_user_expires 
            ON password_reset_tokens(user_id, expires_at)
        ');
        
        // Composite indexes for common query patterns
        $this->db->query('
            CREATE INDEX IF NOT EXISTS idx_employee_active_department 
            ON employee(status, trash, department_id, created_at)
        ');
        
        // Full-text search indexes (if MySQL supports it)
        if ($this->db->DBDriver === 'MySQLi') {
            try {
                $this->db->query('
                    ALTER TABLE employee 
                    ADD FULLTEXT(employee_name, designation)
                ');
            } catch (\Exception $e) {
                // Ignore if FULLTEXT not supported
                log_message('info', 'FULLTEXT index creation failed: ' . $e->getMessage());
            }
        }
        
        // Optimize table structure
        if ($this->db->DBDriver === 'MySQLi') {
            $this->db->query('OPTIMIZE TABLE employee');
            $this->db->query('OPTIMIZE TABLE department');
            $this->db->query('ANALYZE TABLE employee');
            $this->db->query('ANALYZE TABLE department');
        }
    }

    public function down()
    {
        // Employee table indexes
        $this->db->query('DROP INDEX IF EXISTS idx_employee_status_trash ON employee');
        $this->db->query('DROP INDEX IF EXISTS idx_employee_department_status ON employee');
        $this->db->query('DROP INDEX IF EXISTS idx_employee_created_at ON employee');
        $this->db->query('DROP INDEX IF EXISTS idx_employee_name_search ON employee');
        $this->db->query('DROP INDEX IF EXISTS idx_employee_type_status ON employee');
        $this->db->query('DROP INDEX IF EXISTS idx_employee_active_department ON employee');
        
        // Department table indexes
        $this->db->query('DROP INDEX IF EXISTS idx_department_status_trash ON department');
        
        // Auth tokens table indexes  
        $this->db->query('DROP INDEX IF EXISTS idx_auth_tokens_expires ON auth_tokens');
        $this->db->query('DROP INDEX IF EXISTS idx_auth_tokens_user_expires ON auth_tokens');
        
        // Auth logins table indexes
        $this->db->query('DROP INDEX IF EXISTS idx_auth_logins_employee_date ON auth_logins');
        $this->db->query('DROP INDEX IF EXISTS idx_auth_logins_successful_date ON auth_logins');
        
        // Password reset tokens table indexes
        $this->db->query('DROP INDEX IF EXISTS idx_password_reset_expires ON password_reset_tokens');
        $this->db->query('DROP INDEX IF EXISTS idx_password_reset_user_expires ON password_reset_tokens');
        
        // Full-text indexes
        if ($this->db->DBDriver === 'MySQLi') {
            try {
                $this->db->query('ALTER TABLE employee DROP INDEX employee_name');
            } catch (\Exception $e) {
                // Ignore if index doesn't exist
            }
        }
    }
}