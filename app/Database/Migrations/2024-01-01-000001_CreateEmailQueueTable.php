<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmailQueueTable extends Migration
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
            'to_email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'to_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'subject' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => false,
            ],
            'message' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'template' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'priority' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 3,
                'comment' => '1=highest, 5=lowest',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'sent', 'failed'],
                'default' => 'pending',
            ],
            'attempts' => [
                'type' => 'TINYINT',
                'constraint' => 3,
                'default' => 0,
            ],
            'max_attempts' => [
                'type' => 'TINYINT',
                'constraint' => 3,
                'default' => 3,
            ],
            'error_message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'scheduled_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('status');
        $this->forge->addKey('scheduled_at');
        $this->forge->addKey('priority');
        $this->forge->addKey(['status', 'scheduled_at']);

        $this->forge->createTable('email_queue');
    }

    public function down()
    {
        $this->forge->dropTable('email_queue');
    }
}