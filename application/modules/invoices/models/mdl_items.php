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

class Mdl_Items extends Response_Model
{
    public $table = 'xc_invoice_items';
    public $primary_key = 'xc_invoice_items.item_id';
    public $date_created_field = 'item_date_added';

    public function default_select()
    {
        $this->db->select('xc_invoice_item_amounts.*, xc_invoice_items.*, item_tax_rates.tax_rate_percent AS item_tax_rate_percent');
    }

    public function default_order_by()
    {
        $this->db->order_by('xc_invoice_items.item_order');
    }

    public function default_join()
    {
        $this->db->join('xc_invoice_item_amounts', 'xc_invoice_item_amounts.item_id = xc_invoice_items.item_id', 'left');
        $this->db->join('xc_tax_rates AS item_tax_rates', 'item_tax_rates.tax_rate_id = xc_invoice_items.item_tax_rate_id', 'left');
    }

    public function validation_rules()
    {
        return array(
            'invoice_id' => array(
                'field' => 'invoice_id',
                'label' => lang('invoice'),
                'rules' => 'required'
            ),
            'item_name' => array(
                'field' => 'item_name',
                'label' => lang('item_name'),
                'rules' => 'required'
            ),
            'item_description' => array(
                'field' => 'item_description',
                'label' => lang('description')
            ),
            'item_quantity' => array(
                'field' => 'item_quantity',
                'label' => lang('quantity'),
                'rules' => 'required'
            ),
            'item_price' => array(
                'field' => 'item_price',
                'label' => lang('price'),
                'rules' => 'required'
            ),
            'item_tax_rate_id' => array(
                'field' => 'item_tax_rate_id',
                'label' => lang('item_tax_rate')
            )
        );
    }

    public function save($id = NULL, $db_array = NULL)
    {
        $id = parent::save($id, $db_array);

        $this->load->model('invoices/mdl_item_amounts');
        $this->mdl_item_amounts->calculate($id);

        $this->load->model('invoices/mdl_invoice_amounts');
        
        if (is_object($db_array) && isset($db_array->invoice_id)){
            $this->mdl_invoice_amounts->calculate($db_array->invoice_id);
        } elseif (is_array($db_array) && isset($db_array['invoice_id'])){
            $this->mdl_invoice_amounts->calculate($db_array['invoice_id']);
        }

        return $id;
    }

    public function delete($item_id)
    {
        // Get the invoice id so we can recalculate invoice amounts
        $this->db->select('invoice_id');
        $this->db->where('item_id', $item_id);
        $invoice_id = $this->db->get('xc_invoice_items')->row()->invoice_id;

        // Delete the item
        parent::delete($item_id);

        // Delete the item amounts
        $this->db->where('item_id', $item_id);
        $this->db->delete('xc_invoice_item_amounts');

        // Recalculate invoice amounts
        $this->load->model('invoices/mdl_invoice_amounts');
        $this->mdl_invoice_amounts->calculate($invoice_id);
    }

}
