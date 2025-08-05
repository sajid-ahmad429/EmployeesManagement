<?php

namespace App\Models;

use CodeIgniter\Model;

class DepartmentDataTableModel extends Model {

    protected $table = 'department';
    protected $allowedFields = ['department_id', 'department_name', 'status', 'trash', 'created_at', 'updated_at'];
    var $column_order = array('dp.department_id', 'dp.department_id', 'dp.department_name', 'dp.status', 'dp.created_at',); //set column field database for datatable orderable
    var $column_search = array('dp.department_id', 'dp.department_id', 'dp.department_name',); //set column field database for datatable searchable 
    var $order = array('dp.department_id' => 'DESC', 'dp.department_name' => 'ASC', 'dp.created_at' => 'DESC'); // default order     

    /**
     * Saves the department login session to DB
     *
     * @param  mixed $data
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    private function _get_datatables_query($data_where) {
        $db = \Config\Database::connect();
        $builder = $db->table('department as dp');
        $builder->select('dp.*');
        $builder->where('dp.status !=', 2);
        $i = 0;
        foreach ($this->column_search as $item) { // loop column 
            if ($_POST['search']['value']) { // if datatable send POST for search
                if ($i === 0) { // first loop
                    $builder->where($data_where);
                    $builder->like($item, $_POST['search']['value']);
                } else {
                    $builder->orLike($item, $_POST['search']['value']);
                    $builder->where($data_where);
                }
            }
            $i++;
        }
        if (isset($_POST['order'])) { // here order processing
            $builder->where($data_where);
            $builder->orderBy($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $builder->where($data_where);
            $order = $this->order;
            $builder->orderBy(key($order), $order[key($order)]);
        }

        die;
    }

    public function get_datatables($data_where) {
        $db = \Config\Database::connect();

        $builder = $db->table('department as dp');
        $builder->select('dp.*');
        $builder->where('dp.status !=', 2);

        $i = 0;
        if (isset($_POST['search']['value']) && $_POST['search']['value'] != '' && $_POST['search']['value'] != null) {
            foreach ($this->column_search as $item) {
                if ($_POST['search']['value']) {
                    if ($i === 0) {
                        $builder->where($data_where);
                        $builder->like($item, $_POST['search']['value']);
                    } else {
                        $builder->where($data_where);
                        $builder->orLike($item, $_POST['search']['value']);
                    }
                }
                $i++;
            }
        }

        if (isset($_POST['order'])) {
            $builder->where($data_where);
            $builder->orderBy($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $builder->where($data_where);
            $order = $this->order;
            $builder->orderBy(key($order), $order[key($order)]);
        }
        if ($_POST['length'] != -1) {
            $builder->limit($_POST['length'], $_POST['start']);
        }
        $query = $builder->get()->getResultArray();
        // echo $this->db->getLastQuery();
        return $query;
    }

    public function count_InActiveRecordsfiltered($data_where) {
        $db = \Config\Database::connect();
        $builder = $db->table('department as dp');
        $builder->select('dp.*');
        $i = 0;

        $this->applySearchFilters($builder, $data_where);
        $builder->where('dp.status', 0);

        $query = $builder->get()->getResultArray();
        return count($query);
    }

    public function count_ActiveRecordsfiltered($data_where) {
        $db = \Config\Database::connect();
        $builder = $db->table('department as dp');
        $builder->select('dp.*');
        $i = 0;

        $this->applySearchFilters($builder, $data_where);
        $builder->where('dp.status', 1);
        $query = $builder->get()->getResultArray();
        return count($query);
    }

    public function count_filtered($data_where) {
        $db = \Config\Database::connect();
        $builder = $db->table('department as dp');
        $builder->select('dp.*');

        $this->applySearchFilters($builder, $data_where);
        $builder->where('dp.status', 1);
        $query = $builder->get()->getResultArray();
        return count($query);
    }

    public function count_all($data_where) {
        $db = \Config\Database::connect();
        $builder = $db->table('department as dp');
        $builder->select('dp.*');
        $builder->where('dp.status !=', 2);
        $this->applySearchFilters($builder, $data_where);
        return $builder->countAllResults();
    }

    private function applySearchFilters($builder, $data_where) {
        $i = 0;

        if (isset($_POST['search']['value']) && $_POST['search']['value'] != '' && $_POST['search']['value'] != null) {
            foreach ($this->column_search as $item) {
                if ($_POST['search']['value']) {
                    if ($i === 0) {
                        $builder->where($data_where);
                        $builder->like($item, $_POST['search']['value']);
                    } else {
                        $builder->orLike($item, $_POST['search']['value']);
                        $builder->where($data_where);
                    }
                }
                $i++;
            }
        }

        if (isset($_POST['order'])) {
            $builder->where($data_where);
            $builder->orderBy($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $builder->where($data_where);
            $order = $this->order;
            $builder->orderBy(key($order), $order[key($order)]);
        }
    }
}
