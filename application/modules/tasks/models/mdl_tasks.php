<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * xintegro
 * 
 * A free and open source web based invoicing system
 *
 * @package		xintegro
 * @author		xintegro (xintegro.de)
 * @copyright	Copyright (c) 2012 - 2015 xintegro.de
 * @license		http://xintegro.de/license.txt
 * @link		http://xintegro.de/
 * 
 */

class Mdl_Tasks extends Response_Model
{

    public $table = 'xc_tasks';
    public $primary_key = 'xc_tasks.task_id';

    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *,
          (CASE WHEN DATEDIFF(NOW(), task_finish_date) > 0 THEN 1 ELSE 0 END) is_overdue
        ', FALSE);
    }

    public function default_order_by()
    {
        $this->db->order_by('xc_projects.project_name, xc_tasks.task_name');
    }

    public function default_join()
    {
        $this->db->join('xc_projects', 'xc_projects.project_id = xc_tasks.project_id', 'left');
    }

    public function by_task($match)
    {
        $this->db->like('task_name', $match);
        $this->db->or_like('task_description', $match);
    }

    public function validation_rules()
    {
        return array(
            'task_name' => array(
                'field' => 'task_name',
                'label' => lang('task_name'),
                'rules' => 'required'
            ),
            'task_description' => array(
                'field' => 'task_description',
                'label' => lang('task_description'),
                'rules' => ''
            ),
            'task_price' => array(
                'field' => 'task_price',
                'label' => lang('task_price'),
                'rules' => 'required'
            ),
            'task_finish_date' => array(
                'field' => 'task_finish_date',
                'label' => lang('task_finish_date'),
                'rules' => 'required'
            ),
            'project_id' => array(
                'field' => 'project_id',
                'label' => lang('project'),
                'rules' => ''
            ),
            'task_status' => array(
                'field' => 'task_status',
                'label' => lang('status')
            )
        );
    }


    public function db_array()
    {
        $db_array = parent::db_array();

        $db_array['task_finish_date'] = date_to_mysql($db_array['task_finish_date']);
        $db_array['task_price'] = standardize_amount($db_array['task_price']);

        return $db_array;
    }

    public function prep_form($id = NULL)
    {
        if (!parent::prep_form($id)) {
            return FALSE;
        }

        if (!$id) {
            parent::set_form_value('task_finish_date', date('Y-m-d'));
        }

        return TRUE;
    }

    public function statuses()
    {
        return array(
            '1' => array(
                'label' => lang('not_started'),
                'class' => 'draft'
            ),
            '2' => array(
                'label' => lang('in_progress'),
                'class' => 'viewed'
            ),
            '3' => array(
                'label' => lang('complete'),
                'class' => 'sent'
            ),
            '4' => array(
                'label' => lang('invoiced'),
                'class' => 'paid'
            )
        );
    }

}

?>