<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_tenants_and_fk extends CI_Migration {

    public function up()
    {
        // 1. Create 'tenants' table
        if (!$this->db->table_exists('tenants')) {
            $this->dbforge->add_field(array(
                'ten_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ),
                'ten_nome' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '255',
                    'null' => FALSE,
                ),
                'ten_cnpj' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '20',
                    'null' => TRUE,
                ),
                'ten_email' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => TRUE,
                ),
                'ten_telefone' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '20',
                    'null' => TRUE,
                ),
                'ten_data_cadastro' => array(
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ),
            ));
            $this->dbforge->add_key('ten_id', TRUE);
            $this->dbforge->create_table('tenants');

            // 2. Insert default tenant (so existing data belongs to it)
            $data = array(
                'ten_nome' => 'Matriz',
                'ten_data_cadastro' => date('Y-m-d H:i:s')
            );
            $this->db->insert('tenants', $data);
            $default_tenant_id = $this->db->insert_id();
        } else {
            // If table exists, get the first ID or create one
            $query = $this->db->get('tenants', 1);
            if ($query->num_rows() > 0) {
                $default_tenant_id = $query->row()->ten_id;
            } else {
                $data = array(
                    'ten_nome' => 'Matriz',
                    'ten_data_cadastro' => date('Y-m-d H:i:s')
                );
                $this->db->insert('tenants', $data);
                $default_tenant_id = $this->db->insert_id();
            }
        }

        // 3. Add 'ten_id' to all other tables
        $tables = $this->db->list_tables();
        $excluded_tables = array('tenants', 'migrations', 'ci_sessions');

        foreach ($tables as $table) {
            if (in_array($table, $excluded_tables)) {
                continue;
            }

            // Check if column already exists
            if (!$this->db->field_exists('ten_id', $table)) {
                $fields = array(
                    'ten_id' => array(
                        'type' => 'INT',
                        'constraint' => 11,
                        'unsigned' => TRUE,
                        'default' => $default_tenant_id, // Default ensures existing rows get this ID
                        'null' => FALSE
                    )
                );
                
                $this->dbforge->add_column($table, $fields);

                // Add Foreign Key using raw SQL
                // Note: Ensure the table engine supports foreign keys (InnoDB)
                // We also add an index for performance
                $this->db->query("ALTER TABLE `$table` ADD KEY `idx_{$table}_ten_id` (`ten_id`)");
                
                // Add Constraint
                // Using a unique name for the constraint
                $constraint_name = "fk_{$table}_ten_id";
                
                // Check if constraint exists (MySQL specific check usually, but safe to try/catch or just run)
                // We'll just try to add it.
                try {
                    $this->db->query("ALTER TABLE `$table` ADD CONSTRAINT `$constraint_name` FOREIGN KEY (`ten_id`) REFERENCES `tenants` (`ten_id`) ON DELETE NO ACTION ON UPDATE NO ACTION");
                } catch (Exception $e) {
                    log_message('error', "Could not add FK to $table: " . $e->getMessage());
                }
            }
        }
    }

    public function down()
    {
        // Drop 'ten_id' from all tables
        $tables = $this->db->list_tables();
        $excluded_tables = array('tenants', 'migrations', 'ci_sessions');

        foreach ($tables as $table) {
            if (in_array($table, $excluded_tables)) {
                continue;
            }

            if ($this->db->field_exists('ten_id', $table)) {
                $constraint_name = "fk_{$table}_ten_id";
                // Drop FK
                try {
                    $this->db->query("ALTER TABLE `$table` DROP FOREIGN KEY `$constraint_name`");
                } catch (Exception $e) {
                    // Ignore if not found
                }
                
                // Drop Index
                try {
                    $this->db->query("ALTER TABLE `$table` DROP INDEX `idx_{$table}_ten_id`");
                } catch (Exception $e) {
                    // Ignore
                }

                $this->dbforge->drop_column($table, 'ten_id');
            }
        }

        // Drop 'tenants' table
        $this->dbforge->drop_table('tenants');
    }
}
