<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Fix_pessoas_cpfcnpj_unique extends CI_Migration {

    public function up()
    {
        // Verificar se a constraint UNIQUE existe e removê-la
        $constraints = $this->db->query("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'pessoas' 
            AND CONSTRAINT_TYPE = 'UNIQUE' 
            AND CONSTRAINT_NAME LIKE '%cpfcnpj%'
        ")->result();

        foreach ($constraints as $constraint) {
            $this->db->query("ALTER TABLE `pessoas` DROP INDEX `{$constraint->CONSTRAINT_NAME}`");
        }

        // Também tentar remover por nome conhecido
        $knownNames = ['uk_pessoas_cpfcnpj', 'pes_cpfcnpj', 'idx_pessoas_cpfcnpj'];
        foreach ($knownNames as $name) {
            try {
                $this->db->query("ALTER TABLE `pessoas` DROP INDEX `{$name}`");
            } catch (Exception $e) {
                // Ignorar se não existir
            }
        }

        // Criar nova constraint UNIQUE que inclui ten_id
        // Isso permite o mesmo CPF/CNPJ em tenants diferentes, mas não duplica no mesmo tenant
        $this->db->query("
            ALTER TABLE `pessoas` 
            ADD UNIQUE INDEX `uk_pessoas_cpfcnpj_tenant` (`ten_id`, `pes_cpfcnpj`)
        ");
    }

    public function down()
    {
        // Remover a constraint com ten_id
        try {
            $this->db->query("ALTER TABLE `pessoas` DROP INDEX `uk_pessoas_cpfcnpj_tenant`");
        } catch (Exception $e) {
            // Ignorar se não existir
        }

        // Recriar constraint única apenas em pes_cpfcnpj (sem ten_id)
        try {
            $this->db->query("
                ALTER TABLE `pessoas` 
                ADD UNIQUE INDEX `uk_pessoas_cpfcnpj` (`pes_cpfcnpj`)
            ");
        } catch (Exception $e) {
            // Ignorar se já existir
        }
    }
}

