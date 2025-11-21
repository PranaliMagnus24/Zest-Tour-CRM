<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS");
class AgentModel extends CI_model
{
    public function get_Agent_data()
    {
        $this->load->database();
        $q=$this->db->get('agent');
        echo json_encode($q->result_array());
    }
    
    public function get_search_Agent_data()
    {
        $limit = 200;
        
        $this->load->database();
        $param=$this->input->post();
        $where = array();
        if(trim($param['user_type'])=='domestic' || trim($param['user_type'])=='international')
        {
            $where['e.type'] = $param['user_type'];
            if(trim($param['user_designation'])!='tl')
            {
                $where['e.assigned_id'] = $param['assigned_id'];
            }
        }
        if(trim($param['type_customer'])!='')
        {
            $where['e.type_customer'] = $param['type_customer'];            
        }
        if($param['select_staff_id']!='')
        {
            $where['e.assigned_id'] = $param['select_staff_id'];            
        }
        if(trim($param['status'])!='')
        {
            $where['e.status'] = $param['status'];            
        }
        
        
        $cancellation_date=date('Y-m-d');
        $this->db->select('*,i.no_of_pax as nof,i.remark as i_remark,e.enquiry_id as enquiry_number');  
        $this->db->from('itinerary as i,enquiry as e');  
        $this->db->where('e.customer_id=i.customer_id');  
        $this->db->where($where); 
        if(trim($param['type_customer'])!='')
        {
            $type_customer=explode(',',$param['type_customer']);
            $this->db->where_in('e.type_customer',$type_customer);
        }
        if($param['type']!='')
        {
            $type=explode(',',$param['type']);
            $this->db->where_in('e.type',$type);
        }
        if($param['cancledays']!='')
        {
            $cancledays=explode(',',$param['cancledays']);
            if($cancledays[1]=='passed')
            {
                $this->db->where('i.cancellation<',$cancellation_date); 
            }
            else
            {
                $from_date=date('Y-m-d', strtotime('+'.$cancledays[0].' days'));
                $to_date=date('Y-m-d', strtotime('+'.$cancledays[1].' days'));
                $this->db->where('i.cancellation>=',$from_date); 
                $this->db->where('i.cancellation<=',$to_date); 
            }
        }
        else
        {
            $this->db->where('i.cancellation>=',$cancellation_date); 
        }
        
            
        $q=$this->db->get();
        //echo $this->db->last_query();
        $alldata=$q->result_array();
        
        if ($param['page'] > 1) {
            $start = ($param['page'] - 1) * $limit;
        } else {
            $start = 0;
        }
        
        
        $this->db->select('*,i.no_of_pax as nof,i.remark as i_remark,e.enquiry_id as enquiry_number');  
        $this->db->from('(SELECT *, MIN(checkin) AS mcheckin, MAX(checkout) AS mcheckout FROM itinerary GROUP BY enquiry_id) as i,enquiry as e');  
        $this->db->where('e.customer_id=i.customer_id');  
        $this->db->where($where); 
        if(trim($param['type_customer'])!='')
        {
            $type_customer=explode(',',$param['type_customer']);
            $this->db->where_in('e.type_customer',$type_customer);
        }
        if($param['type']!='')
        {
            $type=explode(',',$param['type']);
            $this->db->where_in('e.type',$type);
        }
        if($param['cancledays']!='')
        {
            $cancledays=explode(',',$param['cancledays']);
            if($cancledays[1]=='passed')
            {
                $this->db->where('i.cancellation<',$cancellation_date); 
            }
            else
            {
                $from_date=date('Y-m-d', strtotime('+'.$cancledays[0].' days'));
                $to_date=date('Y-m-d', strtotime('+'.$cancledays[1].' days'));
                $this->db->where('i.cancellation>=',$from_date); 
                $this->db->where('i.cancellation<=',$to_date); 
            }
        }
        else
        {
            $this->db->where('i.cancellation>=',$cancellation_date); 
        }
        $this->db->order_by($param['column'],$param['sort']); 
        $this->db->limit($limit, $start);
        $q=$this->db->get();
        $this->db->last_query();
        $sorted_data_first=$q->result_array();
        $counts = count($sorted_data_first);
        $i=$start+1;
        
        $sorted_data_second = array();
        $customer_id_array = array();
        
        foreach ($sorted_data_first as $key => $val) {
            $customer_id_array[$val['customer_id']] = $val['customer_id'];
        }
        $f_c = count($sorted_data_first);
        if ($f_c < $limit) {
            $limits = $limit - $f_c;
            if($counts==0)
            {
                $counts = $param['second_count'];
            }
            if($param['page']>1 && $param['second_count']!=''&& $param['second_count']>0)
            {
                $start = (($param['page']-1)*$limit)-$param['second_count'];                
                $limits = $limit;
            } else {
                $limits = $limit - $f_c;
            }
            
            $this->db->select('*,i.no_of_pax as nof,i.remark as i_remark,e.enquiry_id as enquiry_number');  
            $this->db->from('(SELECT *, MIN(checkin) AS mcheckin, MAX(checkout) AS mcheckout FROM itinerary GROUP BY enquiry_id) as i,enquiry as e');  
            $this->db->where('e.customer_id=i.customer_id');  
            $this->db->where($where); 
            if(trim($param['type_customer'])!='')
            {
                $type_customer=explode(',',$param['type_customer']);
                $this->db->where_in('e.type_customer',$type_customer);
            }
            if($param['type']!='')
            {
                $type=explode(',',$param['type']);
                $this->db->where_in('e.type',$type);
            }
            $this->db->where('i.cancellation<',$cancellation_date); 
            $this->db->order_by($param['column'],$param['sort']); 
            $this->db->limit($limits, $start);
            $q=$this->db->get();
            $sorted_data_second = $q->result_array();
            foreach ($sorted_data_second as $key => $val) {
                $customer_id_array[$val['customer_id']] = $val['customer_id'];
            }
        }
        
        $cr=$this->db->get('user');
        $user=$cr->result_array();
        $user_data = array();
        foreach($user as $key => $value)
        {
            $user_data[$value['id']] = $value;
        }

        $today = date('Y-m-d');
        foreach($sorted_data_first as $key=>$value)
        {
            $color="";
            $text_color="font-blue";
            $start_date=date('Y-m-d',strtotime($value['cancellation']));
            $last2_date=date('Y-m-d', strtotime('+2 days'));
            $last7_date=date('Y-m-d', strtotime('+7 days'));
            $last15_date=date('Y-m-d', strtotime('+15 days'));
            if ($start_date >= $today && $start_date <= $last2_date){
                $color="bg-danger";
                $text_color="text-white";
            }
            else if ($start_date > $last2_date && $start_date <= $last7_date){
                $color="bg-orange";
                $text_color="text-white";
            }
            else if ($start_date > $last7_date && $start_date <= $last15_date){
                $color="bg-yellow";
                $text_color="text-black";
            }
            ?>
            <div class="col-12 p-0 pull-left border-top b-l-r Flex <?php echo $color;?>" data-checkin="<?php echo $value['mcheckin'] ?>" data-checkout="<?php echo $value['mcheckout'] ?>" data-booking_date="<?php echo $value['booking_date'] ?>" ondblclick="redirect(<?php echo $value['customer_id']?>, 'customer_detail')" style="background-color: #ebeefe;display: flex;" >
                <div class="pull-left Flex-item <?php echo $text_color?>" style="width: 4%;padding-left: 8px;"><?php echo $i++ ?></div>
                <div class="col-1 text-ellipsis pull-left Flex-item <?php echo $text_color?> position-relative" onmouseover="show_tooltip(event,'<?php echo $value['enquiry_number']?>')" onmouseout="hide_tooltip(event)" style="width: 10%;"><?php echo $value['enquiry_number']?></div>
                <div class="col-1 text-ellipsis pull-left Flex-item <?php echo $text_color?> position-relative" onmouseover="show_tooltip(event,'<?php echo date('d M Y',strtotime($value['booking_date']))?>')" onmouseout="hide_tooltip(event)" style="width: 7%;"><?php echo date('d M Y',strtotime($value['booking_date']))?></div>
                <div class="col-1 text-ellipsis pull-left Flex-item <?php echo $text_color?>" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo date('d M Y',strtotime($value['mcheckin']))?>')" onmouseout="hide_tooltip(event)"><?php echo date('d M Y',strtotime($value['mcheckin']))?></div>
                <div class="col-1 text-ellipsis pull-left Flex-item <?php echo $text_color?>" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo date('d M Y',strtotime($value['mcheckout']))?>')" onmouseout="hide_tooltip(event)"><?php echo date('d M Y',strtotime($value['mcheckout']))?></div>
                <div class="col-1 text-ellipsis pull-left Flex-item <?php echo $text_color?>" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo date('d M Y',strtotime($value['cancellation']))?>')" onmouseout="hide_tooltip(event)"><?php echo date('d M Y',strtotime($value['cancellation']))?></div>
                <div class="col-1 text-ellipsis pull-left Flex-item <?php echo $text_color?>" style="width: 8%;"><?php echo $value['product']?></div>
                <div class="col-1 text-ellipsis pull-left Flex-item <?php echo $text_color?>" style="width: 8%;" onmouseover="show_tooltip(event,'<?php echo $user_data[$value['assigned_id']]['name']?>')" onmouseout="hide_tooltip(event)"><?php echo $user_data[$value['assigned_id']]['name']?></div>
                <div class="col-1 text-ellipsis pull-left Flex-item <?php echo $text_color?>" style="width: 12%;" onmouseover="show_tooltip(event,'<?php echo $value['supplier_name']?>')" onmouseout="hide_tooltip(event)"><?php echo $value['supplier_name']?></div>
                <div class="col-1 text-ellipsis pull-left Flex-item <?php echo $text_color?>" style="width: 8%;" onmouseover="show_tooltip(event,'<?php echo $value['booking_reference']?>')" onmouseout="hide_tooltip(event)"><?php echo $value['booking_reference']?></div>
                <div class="col-2 text-ellipsis pull-left Flex-item <?php echo $text_color?>" style="width: 12%;" onmouseover="show_tooltip(event,'<?php echo $value['pax_name']?>')" onmouseout="hide_tooltip(event)"><?php echo $value['pax_name']?></div>
                <div class="col-2 text-ellipsis pull-left Flex-item <?php echo $text_color?>" style="width: 10%;" onmouseover="show_tooltip(event,'<?php echo $value['nof']?>')" onmouseout="hide_tooltip(event)"><?php echo $value['nof']?></div>
            </div>  
        <?php
        }
        if ($f_c < $limit && $param['cancledays']=='') 
        {
            foreach($sorted_data_second as $key=>$value)
            {
                $start_date=date('Y-m-d',strtotime($value['cancellation']));
                $last7_date=date('Y-m-d', strtotime('+7 days'));
                ?>
                <div class="col-12 p-0 pull-left border-top b-l-r Flex  bg-blue" data-checkin="<?php echo $value['mcheckin'] ?>" data-checkout="<?php echo $value['mcheckout'] ?>" data-booking_date="<?php echo $value['booking_date'] ?>" ondblclick="redirect(<?php echo $value['customer_id']?>, 'customer_detail')" style="background-color: #ebeefe;display: flex;" >
                    <div class="pull-left Flex-item text-blue" style="width: 4%;padding-left: 8px;"><?php echo $i++?></div>
                    <div class="col-1 text-ellipsis pull-left Flex-item text-blue position-relative" onmouseover="show_tooltip(event,'<?php echo $value['enquiry_number']?>')" onmouseout="hide_tooltip(event)" style="width: 10%;"><?php echo $value['enquiry_number']?></div>
                    <div class="col-1 text-ellipsis pull-left Flex-item text-blue position-relative" onmouseover="show_tooltip(event,'<?php echo date('d M Y',strtotime($value['booking_date']))?>')" onmouseout="hide_tooltip(event)" style="width: 7%;"><?php echo date('d M Y',strtotime($value['booking_date']))?></div>
                    <div class="col-1 text-ellipsis pull-left Flex-item text-blue" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo date('d M Y',strtotime($value['mcheckin']))?>')" onmouseout="hide_tooltip(event)"><?php echo date('d M Y',strtotime($value['mcheckin']))?></div>
                    <div class="col-1 text-ellipsis pull-left Flex-item text-blue" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo date('d M Y',strtotime($value['mcheckout']))?>')" onmouseout="hide_tooltip(event)"><?php echo date('d M Y',strtotime($value['mcheckout']))?></div>
                    <div class="col-1 text-ellipsis pull-left Flex-item text-blue" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo date('d M Y',strtotime($value['cancellation'])) ?>')" onmouseout="hide_tooltip(event)"><?php echo date('d M Y', strtotime($value['cancellation']));?></div>
                    <div class="col-1 text-ellipsis pull-left Flex-item text-blue" style="width: 8%;"><?php echo $value['product']?></div>
                    <div class="col-1 text-ellipsis pull-left Flex-item text-blue" style="width: 8%;" onmouseover="show_tooltip(event,'<?php echo $user_data[$value['assigned_id']]['name']?>')" onmouseout="hide_tooltip(event)"><?php echo $user_data[$value['assigned_id']]['name']?></div>
                    <div class="col-1 text-ellipsis pull-left Flex-item text-blue" style="width: 12%;" onmouseover="show_tooltip(event,'<?php echo $value['supplier_name']?>')" onmouseout="hide_tooltip(event)"><?php echo $value['supplier_name']?></div>
                    <div class="col-1 text-ellipsis pull-left Flex-item text-blue" style="width: 8%;" onmouseover="show_tooltip(event,'<?php echo $value['booking_reference']?>')" onmouseout="hide_tooltip(event)"><?php echo $value['booking_reference']?></div>
                    <div class="col-2 text-ellipsis pull-left Flex-item text-blue" style="width: 12%;" onmouseover="show_tooltip(event,'<?php echo $value['pax_name']?>')" onmouseout="hide_tooltip(event)"><?php echo $value['pax_name']?></div>
                    <div class="col-2 text-ellipsis pull-left Flex-item text-blue" style="width: 10%;" onmouseover="show_tooltip(event,'<?php echo $value['nof']?>')" onmouseout="hide_tooltip(event)"><?php echo $value['nof']?></div>
                </div>  
            <?php
            }
        }
        
        echo ",,$". count($alldata);
        $this->load->model('Pageination');
        $second_count = $counts;
        echo ",,$".$this->Pageination->get_pageination(count($alldata), $limit, 2, $param['page'], $param['func'],$second_count);
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