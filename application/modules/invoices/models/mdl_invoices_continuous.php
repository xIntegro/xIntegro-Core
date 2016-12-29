<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
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
class Mdl_Invoices_Continuous extends Response_Model
{
    public $table = 'xc_invoices_continuous';
    public $primary_key = 'xc_invoices_continuous.invoice_continuous_id';
    public $recur_frequencies = array(
        '7D' => 'calendar_week',
        '1M' => 'calendar_month',
        '1Y' => 'year',
        '3M' => 'quarter',
        '6M' => 'six_months'
    );

    public function default_select()
    {
        $this->db->select("SQL_CALC_FOUND_ROWS xc_invoices.*,
            xc_clients.client_name,
            xc_invoices_continuous.*,
            IF(recur_end_date > date(NOW()) OR recur_end_date = '0000-00-00', 'active', 'inactive') AS recur_status", FALSE);
    }

    public function default_join()
    {
        $this->db->join('xc_invoices', 'xc_invoices.invoice_id = xc_invoices_continuous.invoice_id');
        $this->db->join('xc_clients', 'xc_clients.client_id = xc_invoices.client_id');
    }

    public function validation_rules()
    {
        return array(
            'invoice_id' => array(
                'field' => 'invoice_id',
                'rules' => 'required'
            ),
            'recur_start_date' => array(
                'field' => 'recur_start_date',
                'label' => lang('start_date'),
                'rules' => 'required'
            ),
            'recur_end_date' => array(
                'field' => 'recur_end_date',
                'label' => lang('end_date')
            ),
            'recur_frequency' => array(
                'field' => 'recur_frequency',
                'label' => lang('every'),
                'rules' => 'required'
            ),
            'notes_one' => array(
                'field' => 'notes_one',
                'label' => lang('notes')
            ),
            'notes_two' => array(
                'field' => 'notes_two',
                'label' => lang('notes')
            ),
        );
    }

    public function db_array()
    {
        $db_array = parent::db_array();

        $db_array['recur_start_date'] = date_to_mysql($db_array['recur_start_date']);
        $db_array['recur_next_date'] = $db_array['recur_start_date'];

        if ($db_array['recur_end_date']) {
            $db_array['recur_end_date'] = date_to_mysql($db_array['recur_end_date']);
        } else {
            $db_array['recur_end_date'] = '0000-00-00';
        }

        return $db_array;
    }

    public function stop($invoice_continuous_id)
    {
        $db_array = array(
            'recur_end_date' => date('Y-m-d'),
            'recur_next_date' => '0000-00-00'
        );

        $this->db->where('invoice_continuous_id', $invoice_continuous_id);
        $this->db->update('xc_invoices_continuous', $db_array);
    }

    /**
     * Sets filter to only continuous invoices which should be generated now
     * @return \Mdl_Invoices_continuous
     */
    public function active()
    {
        $this->filter_where("recur_next_date <= date(NOW()) AND (recur_end_date > date(NOW()) OR recur_end_date = '0000-00-00')");
        return $this;
    }

    public function set_next_recur_date($invoice_continuous_id)
    {
        $invoice_continuous = $this->where('invoice_continuous_id', $invoice_continuous_id)->get()->row();

        $recur_next_date = increment_date($invoice_continuous->recur_next_date, $invoice_continuous->recur_frequency);

        $db_array = array(
            'recur_next_date' => $recur_next_date
        );

        $this->db->where('invoice_continuous_id', $invoice_continuous_id);
        $this->db->update('xc_invoices_continuous', $db_array);
    }

}