<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS");
class WebLeadModel extends CI_model
{
    public function get_WebLead()
    {
        $limit = 200;       
        $this->load->database();
        $param=$this->input->post();
        $where = array();
        if ($param['page'] > 1) {
            $start = ($param['page'] - 1) * $limit;
        } else {
            $start = 0;
        }
        
        
        $this->db->select('*,count(*) OVER() AS total_count');  
        $this->db->from('website_lead');  
        if($param['value']!='')
        {
            $this->db->where("(name LIKE '%" . $param['value'] . "%' escape '!' OR email LIKE '%" . $param['value'] . "%' escape '!' OR phone LIKE '%" . $param['value'] . "%' escape '!' OR destination LIKE '%" . $param['value'] . "%' escape '!')");
        }
        
        $this->db->order_by('datetime','DESC'); 
        $this->db->limit($limit, $start);
        $q=$this->db->get();
        $this->db->last_query();
        $sorted_data_first=$q->result_array();
        $counts = count($sorted_data_first);
        $i=$start+1;
        
        foreach($sorted_data_first as $key=>$value)
        {
            ?>
            <div class="col-12 p-0 pull-left border-top b-l-r Flex" style="background-color: #ebeefe;display: flex;" >
                <div class="col-1 pull-left Flex-item font-blue" style="padding-left: 8px;"><?php echo $i++?></div>
                <div class="col-1 pull-left Flex-item font-blue position-relative"><?php echo date('d M Y',strtotime($value['datetime']))?></div>
                <div class="col-2 pull-left Flex-item font-blue position-relative"><?php echo $value['name']?></div>
                <div class="col-2 pull-left Flex-item font-blue"><?php echo $value['phone']?></div>
                <div class="col-2 pull-left Flex-item font-blue"><?php echo $value['email']?></div>
                <div class="col-2 pull-left Flex-item font-blue"><?php echo $value['destination']?></div>
                <div class="col-2 pull-left Flex-item font-blue"><?php echo 'Website Form'?></div>
            </div>  
        <?php
        }
        echo ",,$". $sorted_data_first[0]['total_count'];
        $this->load->model('Pageination');
        echo ",,$".$this->Pageination->get_pageination($sorted_data_first[0]['total_count'], $limit, 2, $param['page'], $param['func'],"");
    }
    public function add_Agent()
    {
        $this->load->database();
        $param=$this->input->get();
        $data=array();
        if($param['id']=='')
        {
            $param['datetime']=date('Y-m-d');
            $q=$this->db->insert('agent',$param);    
            $data["success"]=true;
            $data["message"]="Agent added to list";
            echo json_encode($data); 
        }
        else
        {
            $this->db->where('id',$param['id']);
            unset($param['id']);
            $q=$this->db->update('agent',$param);     
            $data["success"]=true;
            $data["message"]="Agent updated in list";
            echo json_encode($data); 
        }
    }
    public function get_suppliers()
    {
        $this->load->database();
        $param=$this->input->get();
        $this->db->distinct();
        $this->db->select('supplier_name');
        $this->db->where(array('supplier_name NOT LIKE'=>""));
        $this->db->order_by('supplier_name','ASC');
        $query = $this->db->get('itinerary');      
        $q=$query->result_array();
        echo json_encode($q);
        
    }
    
}

?>