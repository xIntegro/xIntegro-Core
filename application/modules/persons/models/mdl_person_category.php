<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Mdl_person_category extends Response_Model
{
    public $table = 'xc_person_categories';
    public $primary_key = 'xc_person_categories.id';

    //Insert record Into Database
    public function Save($data)
    {
        $this->db->insert_batch('xc_person_categories',$data);
    }
    public function get_category($person_id)
    {
        $query=$this->db->select('category_id')->from('xc_person_categories')->where('person_id',$person_id)->get();

        if($query->num_rows()>0)
        {
            $row=$query->result();
            return $row;
        }
    }
    public function Update($personid,$data)
    {
        $this->db->where_in('person_id',$personid);
        $this->db->delete('xc_person_categories');

        $this->Save($data);
    
        //    $this->db->update_batch('xc_person_categories',$data,'person_id');
    }
    public function validation_rules()
    {
        return array(
            'category'=>array(
                'field'=>'category',
                'label'=>lang('category'),
                'rules'=>'required'
            )
        );
    }
}