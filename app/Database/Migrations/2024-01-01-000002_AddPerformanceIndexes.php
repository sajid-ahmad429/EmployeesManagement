<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPerformanceIndexes extends Migration
{
    public function up()
    {
        // Employee table performance indexes
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_employee_status ON employee(status)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_employee_department_id ON employee(department_id)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_employee_created_at ON employee(created_at)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_employee_email ON employee(email)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_employee_status_dept ON employee(status, department_id)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_employee_search ON employee(employee_name, designation, salary)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_employee_type_status ON employee(employee_type, status)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_employee_updated_at ON employee(updated_at)");

        // Department table performance indexes
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_department_status ON department(status)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_department_name ON department(department_name)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_department_created_at ON department(created_at)");

        // Auth logins table performance indexes
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_auth_logins_employee_id ON auth_logins(employee_id)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_auth_logins_date ON auth_logins(date)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_auth_logins_successful ON auth_logins(successful)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_auth_logins_ip_address ON auth_logins(ip_address)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_auth_logins_employee_date ON auth_logins(employee_id, date)");

        // Auth tokens table performance indexes
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_auth_tokens_user_id ON auth_tokens(user_id)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_auth_tokens_selector ON auth_tokens(selector)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_auth_tokens_expires ON auth_tokens(expires)");

        // Composite indexes for common query patterns
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_employee_composite ON employee(status, department_id, employee_type)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_department_composite ON department(status, department_name)");
    }

    public function down()
    {
        // Drop all created indexes
        $indexes = [
            'idx_employee_status',
            'idx_employee_department_id',
            'idx_employee_created_at',
            'idx_employee_email',
            'idx_employee_status_dept',
            'idx_employee_search',
            'idx_employee_type_status',
            'idx_employee_updated_at',
            'idx_department_status',
            'idx_department_name',
            'idx_department_created_at',
            'idx_auth_logins_employee_id',
            'idx_auth_logins_date',
            'idx_auth_logins_successful',
            'idx_auth_logins_ip_address',
            'idx_auth_logins_employee_date',
            'idx_auth_tokens_user_id',
            'idx_auth_tokens_selector',
            'idx_auth_tokens_expires',
            'idx_employee_composite',
            'idx_department_composite'
        ];

        foreach ($indexes as $index) {
            $this->db->query("DROP INDEX IF EXISTS {$index}");
        }
    }
}