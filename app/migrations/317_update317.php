<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update317 extends CI_Migration {

    public function up() {

        // Widen sma_sales.return_id so it can hold a JSON array of all linked
        // return sale ids when a sale has multiple partial returns. Before this
        // change the column was INT(11) and could only store a single return id.
        $fields = array(
            'return_id' => array('type' => 'TEXT', 'null' => TRUE),
        );
        if ($this->db->field_exists('return_id', 'sales')) {
            $this->dbforge->modify_column('sales', $fields);
        }

    }

    public function down() {
        $fields = array(
            'return_id' => array('type' => 'INT', 'constraint' => '11', 'null' => TRUE),
        );
        if ($this->db->field_exists('return_id', 'sales')) {
            $this->dbforge->modify_column('sales', $fields);
        }
    }

}
