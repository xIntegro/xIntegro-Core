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

    public function modal_item_lookups()
    {
        $this->load->model('mdl_item_lookups');

        $data = array(
            'item_lookups' => $this->mdl_item_lookups->get()->result()
        );

        $this->layout->load_view('item_lookups/modal_item_lookups', $data);
    }

    public function process_item_selections()
    {
        $this->load->model('mdl_item_lookups');

        $items = $this->mdl_item_lookups->where_in('item_lookup_id', $this->input->post('item_lookup_ids'))->get()->result();

        foreach ($items as $item) {
            $item->item_price = format_amount($item->item_price);
        }

        echo json_encode($items);
    }

}
