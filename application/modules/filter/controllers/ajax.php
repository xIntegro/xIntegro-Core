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

class Ajax extends Admin_Controller
{
    public $ajax_controller = TRUE;

    public function filter_invoices()
    {
        $this->load->model('invoices/mdl_invoices');

        $query = $this->input->post('filter_query');

        $keywords = explode(' ', $query);
        $params = array();

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = strtolower($keyword);
                $this->mdl_invoices->like("CONCAT_WS('^',LOWER(invoice_number),invoice_date_created,invoice_date_due,LOWER(client_name),invoice_total,invoice_balance)", $keyword);
            }
        }

        $data = array(
            'invoices' => $this->mdl_invoices->get()->result(),
            'invoice_statuses' => $this->mdl_invoices->statuses()
        );

        $this->layout->load_view('invoices/partial_invoice_table', $data);
    }

    public function filter_quotes()
    {
        $this->load->model('quotes/mdl_quotes');

        $query = $this->input->post('filter_query');

        $keywords = explode(' ', $query);
        $params = array();

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = strtolower($keyword);
                $this->mdl_quotes->like("CONCAT_WS('^',LOWER(quote_number),quote_date_created,quote_date_expires,LOWER(client_name),quote_total)", $keyword);
            }
        }

        $data = array(
            'quotes' => $this->mdl_quotes->get()->result(),
            'quote_statuses' => $this->mdl_quotes->statuses()
        );

        $this->layout->load_view('quotes/partial_quote_table', $data);
    }

    public function filter_clients()
    {
        $this->load->model('clients/mdl_clients');

        $query = $this->input->post('filter_query');

        $keywords = explode(' ', $query);
        $params = array();

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = strtolower($keyword);
                $this->mdl_clients->like("CONCAT_WS('^',LOWER(client_name),LOWER(client_email),client_phone,client_active)", $keyword);
            }
        }

        $data = array(
            'records' => $this->mdl_clients->with_total_balance()->get()->result()
        );

        $this->layout->load_view('clients/partial_client_table', $data);
    }

    public function filter_payments()
    {
        $this->load->model('payments/mdl_payments');

        $query = $this->input->post('filter_query');

        $keywords = explode(' ', $query);
        $params = array();

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = strtolower($keyword);
                $this->mdl_payments->like("CONCAT_WS('^',payment_date,LOWER(invoice_number),LOWER(client_name),payment_amount,LOWER(payment_method_name),LOWER(payment_note))", $keyword);
            }
        }

        $data = array(
            'payments' => $this->mdl_payments->get()->result()
        );

        $this->layout->load_view('payments/partial_payment_table', $data);
    }
    public function filter_persons()
    {
        $this->load->model('persons/person_model');

        //get the post input
        $name = $this->input->post('filter_query');
        $data=array(
            'records'=>$this->load->person_model->SearchResult($name)
        );
        


        $this->layout->load_view('persons/partial_person_table', $data);

    }
    public function filter_categories()
    {
            $this->load->model('categories/category_model');
            //get the post input
            $name=$this->input->post('filter_query');
            $data=array(
                'records'=>$this->load->category_model->SearchResult($name)
            );

        $this->layout->load_view('categories/partial_category_table',$data);
    }

}
