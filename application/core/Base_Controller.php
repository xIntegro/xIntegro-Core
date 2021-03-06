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

class Base_Controller extends MX_Controller
{

    public $ajax_controller = false;

    public function __construct()
    {
        parent::__construct();

        $this->config->load('xintegro');

        // Don't allow non-ajax requests to ajax controllers
        if ($this->ajax_controller and !$this->input->is_ajax_request()) {
            exit;
        }

        $this->load->library('session');
        $this->load->helper('url');
        $this->load->database();

        // Check if database has been configured
        if (empty($this->db->hostname)) {

            $this->load->helper('redirect');
            redirect('/welcome');

        } else {

            $this->load->library('form_validation');
            $this->load->helper('number');
            $this->load->helper('pager');
            $this->load->helper('invoice');
            $this->load->helper('date');
            $this->load->helper('redirect');

            // Load setting model and load settings
            $this->load->model('settings/mdl_settings');
            $this->mdl_settings->load_settings();
            
            // Debug Mode
            if ($this->mdl_settings->setting('enable_debug')) {
                $this->config->set_item('log_threshold', 2);
                define('IP_DEBUG', true);
            } else {
                define('IP_DEBUG', false);
            }

            $this->lang->load('ip', $this->mdl_settings->setting('default_language'));
            $this->lang->load('form_validation', $this->mdl_settings->setting('default_language'));
            $this->lang->load('custom', $this->mdl_settings->setting('default_language'));

            $this->load->helper('language');

            $this->load->module('layout');

        }
    }

}

?>