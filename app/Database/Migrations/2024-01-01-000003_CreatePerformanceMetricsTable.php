<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePerformanceMetricsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'operation' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'execution_time' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'comment' => 'Execution time in milliseconds',
            ],
            'memory_usage' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'comment' => 'Memory usage in MB',
            ],
            'peak_memory' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'comment' => 'Peak memory usage in MB',
            ],
            'url' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
            ],
            'timestamp' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('operation');
        $this->forge->addKey('timestamp');
        $this->forge->addKey('execution_time');
        $this->forge->addKey(['operation', 'timestamp']);

        $this->forge->createTable('performance_metrics');
    }

    public function down()
    {
        $this->forge->dropTable('performance_metrics');
    }
}