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

class Mdl_Client_Notes extends Response_Model
{
    public $table = 'xc_client_notes';
    public $primary_key = 'xc_client_notes.client_note_id';

    public function default_order_by()
    {
        $this->db->order_by('xc_client_notes.client_note_date DESC');
    }

    public function validation_rules()
    {
        return array(
            'client_id' => array(
                'field' => 'client_id',
                'label' => lang('client'),
                'rules' => 'required'
            ),
            'client_note' => array(
                'field' => 'client_note',
                'label' => lang('note'),
                'rules' => 'required'
            )
        );
    }

    public function db_array()
    {
        $db_array = parent::db_array();

        $db_array['client_note_date'] = date('Y-m-d');

        return $db_array;
    }

}
