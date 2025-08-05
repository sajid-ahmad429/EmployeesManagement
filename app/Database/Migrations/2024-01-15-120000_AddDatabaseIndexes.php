<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDatabaseIndexes extends Migration
{
    public function up()
    {
        // Add indexes for frequently queried columns in employee table
        $this->db->query('CREATE INDEX IF NOT EXISTS idx_employee_status ON employee(status)');
        $this->db->query('CREATE INDEX IF NOT EXISTS idx_employee_department_id ON employee(department_id)');
        $this->db->query('CREATE INDEX IF NOT EXISTS idx_employee_created_at ON employee(created_at)');
        $this->db->query('CREATE INDEX IF NOT EXISTS idx_employee_email ON employee(email)');
        $this->db->query('CREATE INDEX IF NOT EXISTS idx_employee_status_dept ON employee(status, department_id)');
        
        // Add indexes for department table
        $this->db->query('CREATE INDEX IF NOT EXISTS idx_department_status ON department(status)');
        
        // Add composite index for common queries
        $this->db->query('CREATE INDEX IF NOT EXISTS idx_employee_search ON employee(employee_name, designation, salary)');
    }

    public function down()
    {
        // Drop the indexes if needed
        $this->db->query('DROP INDEX IF EXISTS idx_employee_status ON employee');
        $this->db->query('DROP INDEX IF EXISTS idx_employee_department_id ON employee');
        $this->db->query('DROP INDEX IF EXISTS idx_employee_created_at ON employee');
        $this->db->query('DROP INDEX IF EXISTS idx_employee_email ON employee');
        $this->db->query('DROP INDEX IF EXISTS idx_employee_status_dept ON employee');
        $this->db->query('DROP INDEX IF EXISTS idx_department_status ON department');
        $this->db->query('DROP INDEX IF EXISTS idx_employee_search ON employee');
    }
}