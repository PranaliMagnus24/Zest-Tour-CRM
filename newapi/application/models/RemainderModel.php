<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS");
class RemainderModel extends CI_model
{
    public function get_Remainder_data()
    {
        
    }
    
    public function get_Reminder_detail()
    {
        $this->load->database();
        $param=$this->input->post();
        $this->db->where("id", $param['id']);
        $q=$this->db->get('contact_reminder');
        $data=$q->result_array();
        echo json_encode($data);
    }
    public function addReminder()
    {
        $param=$this->input->post();
        $this->load->database();            
        if ($param['remind_time'] == '') 
        {
            $param['remark'] = $param['reminder'];
            unset($param['reminder']);
            $param['remarkby'] = $param['reminderby'];
            unset($param['reminderby']);
            unset($param['remind_time']);
            $param['time'] = date('Y-m-d H:i');
            $q = $this->db->insert('contact_remark', $param);
            if ($q) {
                echo "ok";
            }
        } 
        else 
        {
            $this->db->where("id", $param['contact_id']);
            $q=$this->db->get('contact');
            $data=$q->result_array();
            
            $param['time'] = date('Y-m-d H:i',strtotime($param['remind_time']));
            
            unset($param['remind_time']);
            unset($param['remindme']);
            $param['datetime'] = date('Y-m-d H:i');
            $q = $this->db->insert('contact_reminder', $param);
            if ($q) {
                echo "ok";
            }
        }            
    }
}

?>