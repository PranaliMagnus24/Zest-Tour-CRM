<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS");
class ConfirmModel extends CI_model
{
    public function get_Confirm_data()
    {
        $limit = 200;
        $this->load->database();
        $param=$this->input->get();
        $this->db->select('*,i.no_of_pax as nof,i.remark as i_remark,e.enquiry_id as enquiry_number');  
        $this->db->from('(SELECT *, MIN(checkin) AS mcheckin, MAX(checkout) AS mcheckout,SUM(product_amount) as amount FROM itinerary GROUP BY enquiry_id) as i,enquiry as e');  
        $this->db->where("`i`.`mcheckout`<'".date('Y-m-d', strtotime('-7 days'))."'");
        $this->db->where("status='Confirmed'");  
        $this->db->where('e.customer_id=i.customer_id');  
        $q=$this->db->get();
        
        $alldata=$q->result_array();        
        $new_param=[];
        foreach($alldata as $key=>$value)
        {
            $new_param['status']='Confirmed';
            $this->db->where("id='".$value['id']."'");  
           $q=$this->db->update('enquiry',$new_param);
        }
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
        if(trim($param['status'])!='')
        {
            $where['e.status'] = $param['status'];            
        }
        
        $this->db->select('*,i.no_of_pax as nof,i.remark as i_remark,e.enquiry_id as enquiry_number');  
        $this->db->from('(SELECT *, MIN(checkin) AS mcheckin, MAX(checkout) AS mcheckout,SUM(product_amount) as amount FROM itinerary GROUP BY enquiry_id) as i,enquiry as e');  
        $this->db->where("e.end_date>='".date('Y-m-d', strtotime('-7 days'))."'");
        $this->db->where('e.customer_id=i.customer_id');  
        $this->db->where($where);  
        $q=$this->db->get();
        //echo $this->db->last_query();
        $alldata=$q->result_array();
        
        if ($param['page'] > 1) {
            $start = ($param['page'] - 1) * $limit;
        } else {
            $start = 0;
        }
        $cancellation_date=date('Y-m-d');
        $this->db->select('*,i.no_of_pax as nof,i.remark as i_remark,e.enquiry_id as enquiry_number');  
        $this->db->from('(SELECT *, MIN(checkin) AS mcheckin, MAX(checkout) AS mcheckout,SUM(product_amount) as amount FROM itinerary GROUP BY enquiry_id) as i,enquiry as e');  
        $this->db->where("e.end_date>='".date('Y-m-d', strtotime('-7 days'))."'");
        $this->db->where('e.customer_id=i.customer_id');  
        $this->db->where($where); 
        $this->db->where('i.cancellation>=',$cancellation_date); 
        $this->db->order_by($param['column'],$param['sort']); 
        $this->db->limit($limit, $start);
        $q=$this->db->get();
        //echo $this->db->last_query();
        $sorted_data_first=$q->result_array();
        $counts = count($sorted_data_first);
        $i=$start+1;
        $sorted_data_second = array();
        $customer_id_array = array();
        foreach ($sorted_data_first as $key => $val) {
            $customer_id_array[$val['customer_id']] = $val['customer_id'];
        }
        $f_c = count($sorted_data_first);
        if ($f_c < $limit) 
        {
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
            $this->db->from('(SELECT *, MIN(checkin) AS mcheckin, MAX(checkout) AS mcheckout,SUM(product_amount) as amount FROM itinerary GROUP BY enquiry_id) as i,enquiry as e');  
            $this->db->where('e.customer_id=i.customer_id');  
            $this->db->where("e.end_date>='".date('Y-m-d', strtotime('-7 days'))."'");
            $this->db->where($where); 
            $this->db->where('i.cancellation<',$cancellation_date); 
            $this->db->order_by($param['column'],$param['sort']); 
            $this->db->limit($limits, $start);
            $q=$this->db->get();
            //echo $this->db->last_query();            
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
        if(count($customer_id_array)>0)
        {
            $customer_data = array();
            $this->db->where_in('id', $customer_id_array);
            $cr=$this->db->get('customer');
            $customer=$cr->result_array();
            foreach($customer as $key => $value)
            {
                $customer_data[$value['id']] = $value;
            }
        }
        $today = date('Y-m-d');
        foreach($sorted_data_first as $key=>$value)
        {
            $color="";
            $start_date=date('Y-m-d',strtotime($value['cancellation']));
            $last2_date=date('Y-m-d', strtotime('+2 days'));
            $last7_date=date('Y-m-d', strtotime('+7 days'));
            $last15_date=date('Y-m-d', strtotime('+15 days'));
            if ($start_date >= $today && $start_date <= $last2_date){
                $color="#FF396F";
                $text="Recent 2 days";
            }
            else if ($start_date > $last2_date && $start_date <= $last7_date){
                $color="#fd7e14";
                $text="After 2 days and before 7 days";
            }
            else if ($start_date > $last7_date && $start_date <= $last15_date){
                $color="#ffc107";
                $text="After 7 days and before 15 days";
            }
            else
            {
                $color="#fff";
                $text="After 15 days";
            }
            ?>
<div class="col-12 p-0 pull-left border-top b-l-r " data-checkin="<?php echo $value['mcheckin'] ?>"
    data-checkout="<?php echo $value['mcheckout'] ?>" data-booking_date="<?php echo $value['booking_date'] ?>"
    ondblclick="redirect(<?php echo $value['customer_id']?>, 'customer_detail')">
    <div class="pull-left Flex-item" style="width: 4%;padding-left: 8px;"><?php echo $i++ ?></div>
    <!-- <div class="col-1 text-ellipsis pull-left Flex-item" style="width: 5%;" onmouseover="show_tooltip(event,'<?php echo $value['enquiry_number']?>')" onmouseout="hide_tooltip(event)"><?php echo $value['enquiry_number']?></div> -->

    <div class="col-1 text-ellipsis pull-left Flex-item" style="width: 5%;"
        onmouseover="show_tooltip(event,'<?php echo substr(explode('-', $value['enquiry_number'])[1], 0, 10); ?>')"
        onmouseout="hide_tooltip(event)">
        <?php echo substr(explode('-', $value['enquiry_number'])[1], 0, 10); ?>
    </div>
    
    <div class="col-1 text-ellipsis pull-left Flex-item" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo date('d M Y',strtotime($value['booking_date']))?>')"
        onmouseout="hide_tooltip(event)"><?php echo date('d M Y',strtotime($value['booking_date']))?></div>
    <div class="col-1 text-ellipsis pull-left Flex-item" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo date('d M Y',strtotime($value['mcheckin']))?>')"
        onmouseout="hide_tooltip(event)"><?php echo date('d M Y',strtotime($value['mcheckin']))?></div>
    <div class="col-1 text-ellipsis pull-left Flex-item" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo date('d M Y',strtotime($value['mcheckout']))?>')"
        onmouseout="hide_tooltip(event)"><?php echo date('d M Y',strtotime($value['mcheckout']))?></div>
    <div class="col-1 text-ellipsis pull-left Flex-item" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo date('d M Y',strtotime($value['cancellation']))?>')"
        onmouseout="hide_tooltip(event)"><?php echo date('d M Y',strtotime($value['cancellation']))?></div>
    <div class="col-1 text-ellipsis pull-left Flex-item" style="width: 8%;"><?php echo $value['product']?></div>
    <div class="col-1 text-ellipsis pull-left Flex-item" style="width: 6%;"><?php echo $value['amount']?></div>
    <div class="col-1 text-ellipsis pull-left Flex-item" style="width: 8%;"
        onmouseover="show_tooltip(event,'<?php echo $user_data[$value['assigned_id']]['name']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $user_data[$value['assigned_id']]['name']?></div>
    <div class="col-1 text-ellipsis pull-left Flex-item" style="width: 8%;"
        onmouseover="show_tooltip(event,'<?php echo $value['supplier_name']?>')" onmouseout="hide_tooltip(event)">
        <?php echo $value['supplier_name']?></div>
    <div class="col-1 text-ellipsis pull-left Flex-item" style="width: 8%;"
        onmouseover="show_tooltip(event,'<?php echo $value['booking_reference']?>')" onmouseout="hide_tooltip(event)">
        <?php echo $value['booking_reference']?></div>
    <div class="col-2 text-ellipsis pull-left Flex-item" style="width: 10%;"
        onmouseover="show_tooltip(event,'<?php echo $value['pax_name']?>')" onmouseout="hide_tooltip(event)">
        <?php echo $value['pax_name']?></div>
    <div class="col-2 text-ellipsis pull-left Flex-item" style="width: 8%;"
        onmouseover="show_tooltip(event,'<?php echo $value['nof']?>')" onmouseout="hide_tooltip(event)">
        <?php echo $value['nof']?></div>
    <div class="col-2 text-ellipsis pull-left Flex-item text-center" style="width: 7%;padding-top: 2px;"
        onmouseover="show_tooltip(event,'<?php echo $text?>')" onmouseout="hide_tooltip(event)">
        <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512" fill="<?php echo $color;?>"
            width="20" style="stroke: black;stroke-width: 50px;">
            <path d="M256 464c-114.69 0-208-93.31-208-208S141.31 48 256 48s208 93.31 208 208-93.31 208-208 208z"></path>
        </svg>
    </div>
</div>
<?php
        }
        if ($f_c < $limit) 
        {
            foreach($sorted_data_second as $key=>$value)
            {
                $start_date=date('Y-m-d',strtotime($value['cancellation']));
                $last7_date=date('Y-m-d', strtotime('+7 days'));
                ?>
<div class="col-12 p-0 pull-left border-top b-l-r Flex" data-checkin="<?php echo $value['mcheckin'] ?>"
    data-checkout="<?php echo $value['mcheckout'] ?>" data-booking_date="<?php echo $value['booking_date'] ?>"
    ondblclick="redirect(<?php echo $value['customer_id']?>, 'customer_detail')">
    <div class="pull-left Flex-item " style="width: 4%;padding-left: 8px;"><?php echo $i++?></div>
    <!-- <div class="col-1 text-ellipsis pull-left Flex-item " style="width: 5%;"
        onmouseover="show_tooltip(event,'<?php echo $value['enquiry_number']?>')" onmouseout="hide_tooltip(event)">
        <?php echo $value['enquiry_number']?></div> -->

    <div class="col-1 text-ellipsis pull-left Flex-item" style="width: 5%;"
        onmouseover="show_tooltip(event,'<?php echo substr(explode('-', $value['enquiry_number'])[1], 0, 10); ?>')"
        onmouseout="hide_tooltip(event)">
        <?php echo substr(explode('-', $value['enquiry_number'])[1], 0, 10); ?>
    </div>

    <div class="col-1 text-ellipsis pull-left Flex-item " style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo date('d M Y',strtotime($value['booking_date']))?>')"
        onmouseout="hide_tooltip(event)"><?php echo date('d M Y',strtotime($value['booking_date']))?></div>
    <div class="col-1 text-ellipsis pull-left Flex-item " style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo date('d M Y',strtotime($value['mcheckin']))?>')"
        onmouseout="hide_tooltip(event)"><?php echo date('d M Y',strtotime($value['mcheckin']))?></div>
    <div class="col-1 text-ellipsis pull-left Flex-item " style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo date('d M Y',strtotime($value['mcheckout']))?>')"
        onmouseout="hide_tooltip(event)"><?php echo date('d M Y',strtotime($value['mcheckout']))?></div>
    <div class="col-1 text-ellipsis pull-left Flex-item " style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo date('d M Y',strtotime($value['cancellation'])) ?>')"
        onmouseout="hide_tooltip(event)"><?php echo date('d M Y', strtotime($value['cancellation']));?></div>
    <div class="col-1 text-ellipsis pull-left Flex-item " style="width: 8%;"><?php echo $value['product']?></div>
    <div class="col-1 text-ellipsis pull-left Flex-item " style="width: 6%;"><?php echo $value['amount']?></div>
    <div class="col-1 text-ellipsis pull-left Flex-item " style="width: 8%;"
        onmouseover="show_tooltip(event,'<?php echo $user_data[$value['assigned_id']]['name']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $user_data[$value['assigned_id']]['name']?></div>
    <div class="col-1 text-ellipsis pull-left Flex-item " style="width: 8%;"
        onmouseover="show_tooltip(event,'<?php echo $value['supplier_name']?>')" onmouseout="hide_tooltip(event)">
        <?php echo $value['supplier_name']?></div>
    <div class="col-1 text-ellipsis pull-left Flex-item " style="width: 8%;"
        onmouseover="show_tooltip(event,'<?php echo $value['booking_reference']?>')" onmouseout="hide_tooltip(event)">
        <?php echo $value['booking_reference']?></div>
    <div class="col-2 text-ellipsis pull-left Flex-item " style="width: 10%;"
        onmouseover="show_tooltip(event,'<?php echo $value['pax_name']?>')" onmouseout="hide_tooltip(event)">
        <?php echo $value['pax_name']?></div>
    <div class="col-2 text-ellipsis pull-left Flex-item " style="width: 8%;"
        onmouseover="show_tooltip(event,'<?php echo $value['nof']?>')" onmouseout="hide_tooltip(event)">
        <?php echo $value['nof']?></div>
    <div class="col-2 text-ellipsis pull-left Flex-item  text-center" style="width: 7%;padding-top: 2px;"
        onmouseover="show_tooltip(event,'<?php echo 'Last 7 days'?>')" onmouseout="hide_tooltip(event)">
        <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512" fill="#bdc5f1" width="20"
            style="stroke: black;stroke-width: 50px;">
            <path d="M256 464c-114.69 0-208-93.31-208-208S141.31 48 256 48s208 93.31 208 208-93.31 208-208 208z"></path>
        </svg>
    </div>
</div>
<?php
            }
        }
        echo ",,$". count($alldata);
        $this->load->model('Pageination');
        $second_count = $counts;
        echo ",,$".$this->Pageination->get_pageination(count($alldata), $limit, 2, $param['page'], $param['func'],$second_count);
    }
    
    public function get_search_Confirm_data()
    {
        $limit = 200;
        
        $this->load->database();
        $param=$this->input->post();
        $this->db->select('*,i.no_of_pax as nof,i.remark as i_remark,e.enquiry_id as enquiry_number');  
        $this->db->from('(SELECT *, MIN(checkin) AS mcheckin, MAX(checkout) AS mcheckout, SUM(product_amount) as amount FROM itinerary GROUP BY enquiry_id) as i,enquiry as e');  
        $this->db->where("`i`.`mcheckout`<'".date('Y-m-d', strtotime('-7 days'))."'");
        $this->db->where("status='Confirmed'");  
        $this->db->where('e.customer_id=i.customer_id');  
        
        $q=$this->db->get();
        
        $alldata=$q->result_array();        
        $new_param=[];
        foreach($alldata as $key=>$value)
        {
            $new_param['status']='Confirmed';
            $this->db->where("id='".$value['id']."'");  
            $q=$this->db->update('enquiry',$new_param);
        }
        
        $where = array();
        if(trim($param['user_type'])=='domestic' || trim($param['user_type'])=='international')
        {
            $where['e.type'] = $param['user_type'];
            if(trim($param['user_designation'])!='tl')
            {
                $where['e.assigned_id'] = $param['assigned_id'];
            }
        }
        if($param['select_staff_id']!='' && $param['select_staff_id']!="null")
        {
            $where['e.assigned_id'] = $param['select_staff_id'];            
        }
        if(trim($param['status'])!='')
        {
            $where['e.status'] = $param['status'];            
        }
        
        
        $cancellation_date=date('Y-m-d');
        $this->db->select('*,i.no_of_pax as nof,i.remark as i_remark,e.enquiry_id as enquiry_number');  
        $this->db->from('(SELECT *, MIN(checkin) AS mcheckin, MAX(checkout) AS mcheckout,SUM(product_amount) as amount FROM itinerary GROUP BY enquiry_id) as i,enquiry as e');  
        $this->db->where("i.mcheckout>='".date('Y-m-d', strtotime('-7 days'))."'");
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
            else if($cancledays[1]=='future')
            {
                $this->db->where('i.cancellation>=',$cancellation_date); 
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
            // $this->db->where('i.cancellation>=',$cancellation_date); 
        }
        
        if($param['value']!='')
        {
            $this->db->where("(i.product LIKE '%" . $param['value'] . "%' escape '!' OR i.supplier_name LIKE '%" . $param['value'] . "%' escape '!' OR i.booking_reference LIKE '%" . $param['value'] . "%' escape '!' OR i.pax_name LIKE '%" . $param['value'] . "%' escape '!' OR e.id LIKE '%" . $param['value'] . "%' escape '!')");
        }
        $this->db->order_by($param['column'],$param['sort']); 
        $q=$this->db->get();
        //echo $this->db->last_query();
        $alldata=$q->result_array();
        
        if ($param['page'] > 1) {
            $start = ($param['page'] - 1) * $limit;
        } else {
            $start = 0;
        }
        
        
        

        $this->db->select('
                    *,
                    i.no_of_pax as nof,
                    i.remark as i_remark,
                    e.enquiry_id as enquiry_number,
                    c.id as customer_id,
                    c.name as customer_name,
                    crcountry.country_name as country_name,
                    crcity.country_name as city_name
                ');

        $this->db->from('(SELECT *, MIN(checkin) AS mcheckin, MAX(checkout) AS mcheckout, SUM(product_amount) as amount, SUM(no_of_pax) as nof FROM itinerary WHERE is_deleted = 0 GROUP BY enquiry_id) as i');

        $this->db->join('enquiry as e', 'e.id = i.enquiry_id');
        $this->db->join('customer as c', 'e.customer_id = c.id');
        $this->db->join('crcountry', 'e.country_id = crcountry.country_id', 'left');
        $this->db->join('crcity', 'e.city_id = crcity.city_id', 'left');
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
            else if($cancledays[1]=='future')
            {
                $this->db->where('i.cancellation>=',$cancellation_date); 
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
           
        }
        
        if($param['start_date']!='' && $param['end_date']=='')
        {
            $this->db->where("(i.booking_date>='".date('Y-m-d',strtotime($param['start_date']))."')");
        }
        if($param['start_date']!='' && $param['end_date']!='')
        {
            $this->db->where("(i.booking_date>='".date('Y-m-d',strtotime($param['start_date']))."' AND i.booking_date<='".date('Y-m-d',strtotime($param['end_date']))."')");
        }
         
        if($param['value']!='')
        {
            $this->db->where("(i.product LIKE '%" . $param['value'] . "%' escape '!' OR i.supplier_name LIKE '%" . $param['value'] . "%' escape '!' OR i.booking_reference LIKE '%" . $param['value'] . "%' escape '!' OR i.pax_name LIKE '%" . $param['value'] . "%' escape '!' OR e.id LIKE '%" . $param['value'] . "%' escape '!')");
        }


        
        $this->db->order_by($param['column'],$param['sort']); 
        $this->db->limit($limit, $start);
        $q=$this->db->get();
        //echo $this->db->last_query();
        $sorted_data_first=$q->result_array();
        $customer_id_array = array();
        $i = $start + 1;
        $today = date('Y-m-d');
        $total_amount = 0;
        $total_nof = 0;
       
        foreach ($sorted_data_first as $key => $val) {
        $total_amount += floatval($val['amount']);
        $total_nof += floatval($val['nof']);
        $customer_id_array[$val['customer_id']] = $val['customer_id'];
        }
        
        $cr=$this->db->get('user');
        $user=$cr->result_array();
        $user_data = array();
        foreach($user as $key => $value)
        {
            $user_data[$value['id']] = $value;
            
        }
        
        $i=$start+1;
        
        $today = date('Y-m-d');
        foreach($sorted_data_first as $key=>$value)
        {
            $color="";
            $start_date=date('Y-m-d',strtotime($value['cancellation']));
            $last2_date=date('Y-m-d', strtotime('+2 days'));
            $last7_date=date('Y-m-d', strtotime('+7 days'));
            $last15_date=date('Y-m-d', strtotime('+15 days'));
            if ($start_date >= $today && $start_date <= $last2_date){
                $color="#FF396F";
                $text="Recent 2 days";
            }
            else if ($start_date > $last2_date && $start_date <= $last7_date){
                $color="#fd7e14";
                $text="After 2 days and before 7 days";
            }
            else if ($start_date > $last7_date && $start_date <= $last15_date){
                $color="#ffc107";
                $text="After 7 days and before 15 days";
            }
            else
            {
                $color="#fff";
                $text="After 15 days";
            }




                $filter_conditions = "i.is_deleted = 0 AND e.status = 'Confirmed' AND i.booking_date >= '2025-01-01'";

                if (!empty($param['type_customer'])) {
                    $type_customer = explode(',', $param['type_customer']);
                    $in = implode("','", $type_customer);
                    $filter_conditions .= " AND e.type_customer IN ('$in')";
                }

                if (!empty($param['type'])) {
                    $type = explode(',', $param['type']);
                    $in = implode("','", $type);
                    $filter_conditions .= " AND e.type IN ('$in')";
                }

                if (!empty($param['value'])) {
                    $val = $this->db->escape_like_str($param['value']);
                    $filter_conditions .= " AND (
                        i.product LIKE '%$val%' ESCAPE '!' 
                        OR i.supplier_name LIKE '%$val%' ESCAPE '!' 
                        OR i.booking_reference LIKE '%$val%' ESCAPE '!' 
                        OR i.pax_name LIKE '%$val%' ESCAPE '!' 
                        OR e.id LIKE '%$val%' ESCAPE '!'
                    )";
                }

                if (!empty($param['start_date']) && empty($param['end_date'])) {
                    $filter_conditions .= " AND i.booking_date >= '" . date('Y-m-d', strtotime($param['start_date'])) . "'";
                }
                if (!empty($param['start_date']) && !empty($param['end_date'])) {
                    $filter_conditions .= " AND i.booking_date >= '" . date('Y-m-d', strtotime($param['start_date'])) . "' 
                        AND i.booking_date <= '" . date('Y-m-d', strtotime($param['end_date'])) . "'";
                }

                // Now build full query
                $grand_total_sql = "
                    SELECT 
                        SUM(main.amount) AS total_amount,
                        SUM(main.nof) AS total_nof
                    FROM (
                        SELECT 
                            i.no_of_pax AS nof,
                            i.remark AS i_remark,
                            e.enquiry_id AS enquiry_number,
                            c.id AS customer_id,
                            c.name AS customer_name,
                            crcountry.country_name AS country_name,
                            crcity.country_name AS city_name,
                            MIN(i.checkin) AS mcheckin,
                            MAX(i.checkout) AS mcheckout,
                            SUM(i.product_amount) AS amount
                        FROM itinerary i
                        JOIN enquiry e ON e.id = i.enquiry_id
                        JOIN customer c ON e.customer_id = c.id
                        LEFT JOIN crcountry ON e.country_id = crcountry.country_id
                        LEFT JOIN crcity ON e.city_id = crcity.country_id
                        WHERE $filter_conditions
                        GROUP BY i.enquiry_id
                    ) AS main
                ";



                            ?>



                            <!-- onmouseover="show_tooltip(event,'<?php echo substr(explode('-', $value['enquiry_number'])[1], 0, 10); ?>')"
                        onmouseout="hide_tooltip(event)">
                        <?php echo substr(explode('-', $value['enquiry_number'])[1], 0, 10); ?> -->
                <div class="col-12 p-0 pull-left border-top b-l-r Flex" data-checkin="<?php echo $value['mcheckin'] ?>"
                    data-checkout="<?php echo $value['mcheckout'] ?>" data-booking_date="<?php echo $value['booking_date'] ?>"
                    ondblclick="redirect(<?php echo $value['customer_id']?>, 'customer_detail')" style="background-color: #ebeefe;">
                    
                <div class="pull-left Flex-item" style="width: 4%;padding-left: 8px;"><?php echo $i++ ?></div>
                    

                    <div class="col-1 text-ellipsis pull-left Flex-item" style="width: 5%;"
                    
                        onmouseover="show_tooltip(event,'<?php echo $value['enquiry_number']?>')" onmouseout="hide_tooltip(event)">
                        <?php echo $value['enquiry_number']?>
                    </div>

                    <div class="col-1 text-ellipsis pull-left Flex-item" style="width: 7%;"
                        onmouseover="show_tooltip(event,'<?php echo date('d M Y',strtotime($value['booking_date']))?>')"
                        onmouseout="hide_tooltip(event)"><?php echo date('d M Y',strtotime($value['booking_date']))?>
                    </div>
                    

                    <div class="col-1 text-ellipsis pull-left Flex-item" style="width: 7%;"
                        onmouseover="show_tooltip(event,'<?php echo date('d M Y',strtotime($value['mcheckin']))?>')"
                        onmouseout="hide_tooltip(event)"><?php echo date('d M Y',strtotime($value['mcheckin']))?></div>
                    <div class="col-1 text-ellipsis pull-left Flex-item" style="width: 7%;"
                        onmouseover="show_tooltip(event,'<?php echo date('d M Y',strtotime($value['mcheckout']))?>')"
                        onmouseout="hide_tooltip(event)"><?php echo date('d M Y',strtotime($value['mcheckout']))?></div>
                    <div class="col-1 text-ellipsis pull-left Flex-item" style="width: 7%;"
                        onmouseover="show_tooltip(event,'<?php echo date('d M Y',strtotime($value['cancellation']))?>')"
                        onmouseout="hide_tooltip(event)"><?php echo date('d M Y',strtotime($value['cancellation']))?></div>
                    <div class="col-1 text-ellipsis pull-left Flex-item" style="width: 6%;"><?php echo $value['product']?></div>
                    <div class="col-1 text-ellipsis pull-left Flex-item" style="width: 6%;"><?php echo $value['amount']?></div>
                <div class="col-2 text-ellipsis pull-left Flex-item font-blue" style="width: 7%;"
                    onmouseover="show_tooltip(event, '<?php
                        if (empty($value['destination'])) {
                            echo htmlspecialchars(($value['country_name'] ?? '') . ', ' . ($value['city_name'] ?? ''), ENT_QUOTES);
                        } else {
                            echo htmlspecialchars($value['destination'], ENT_QUOTES);
                        }
                    ?>')"
                    onmouseout="hide_tooltip(event)">
                    <?php
                        if (empty($value['destination'])) {
                            echo ($value['country_name'] ?? '') . ', ' . ($value['city_name'] ?? '');
                        } else {
                            echo $value['destination'];
                        }
                    ?>
                </div>
                    <div class="col-1 text-ellipsis pull-left Flex-item" style="width: 7%;"
                        onmouseover="show_tooltip(event,'<?php echo $user_data[$value['assigned_id']]['name']?>')"
                        onmouseout="hide_tooltip(event)"><?php echo $user_data[$value['assigned_id']]['name']?></div>
                    <div class="col-1 text-ellipsis pull-left Flex-item" style="width: 8%;"
                        onmouseover="show_tooltip(event,'<?php echo $value['supplier_name']?>')" onmouseout="hide_tooltip(event)">
                        <?php echo $value['supplier_name']?></div>
                    <div class="col-1 text-ellipsis pull-left Flex-item" style="width: 6%;"
                        onmouseover="show_tooltip(event,'<?php echo $value['booking_reference']?>')" onmouseout="hide_tooltip(event)">
                        <?php echo $value['booking_reference']?></div>
                    <div class="col-2 text-ellipsis pull-left Flex-item" style="width: 10%;"
                        onmouseover="show_tooltip(event,'<?php echo $value['pax_name']?>')" onmouseout="hide_tooltip(event)">
                        <?php echo $value['pax_name']?></div>
                    <div class="col-2 text-ellipsis pull-left Flex-item" style="width: 6%;"
                        onmouseover="show_tooltip(event,'<?php echo $value['nof']?>')" onmouseout="hide_tooltip(event)">
                        <?php echo $value['nof']?></div>
                    <div class="col-2 text-ellipsis pull-left Flex-item text-center" style="width: 7%;padding-top: 2px;"
                        onmouseover="show_tooltip(event,'<?php echo $text?>')" onmouseout="hide_tooltip(event)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512" fill="<?php echo $color;?>"
                            width="20" style="stroke: black;stroke-width: 50px;">
                            <path d="M256 464c-114.69 0-208-93.31-208-208S141.31 48 256 48s208 93.31 208 208-93.31 208-208 208z"></path>
                        </svg>
                    </div>

                </div>

                </div>




                <?php
                        } ?>
                        
                        <div class="col-12 p-0 pull-left border-top b-l-r Flex" style="background-color: #dfe6f1; font-weight: bold;">
                            <div class="pull-left Flex-item text-left" style="width: 50%; padding: 10px 10px; border-right: 1px solid #000;">
                                Total Amount : <?php echo number_format($total_amount, 2); ?></div>
                                <div class="pull-left Flex-item text-left" style="width: 50%; padding: 10px 10px;">
                                Total No Pax : <?php echo number_format($total_nof, 2); ?>
                            </div>
                        </div>

                <!-- <div class="col-12 p-0 pull-left border-top b-l-r Flex" style="background-color: #dfe6f1; font-weight: bold;">
                    <div class="pull-left Flex-item text-left" style="width:50%; padding: 10px 10px; border-right: 1px solid #000;">
                        Total Amount (All Pages): <?php echo number_format($grand_total_amount, 2); ?>
                    </div>
                    <div class="pull-left Flex-item text-left" style="width:50%; padding: 10px 10px;">
                        Total No Pax (All Pages): <?php echo number_format($grand_total_nof, 2); ?>
                    </div>
                </div> -->

                    
                
                        <?php
                        echo ",,$". count($sorted_data_first);
                        $this->load->model('Pageination');
                        $second_count = $counts;
                        echo ",,$".$this->Pageination->get_pageination(count($alldata), $limit, 2, $param['page'], $param['func'],$second_count);
                    }



     public function download_data()
    {
        $limit = 200;
        
        $this->load->database();
        $param=$this->input->get();
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
            // $where['e.type_customer'] = $param['type_customer'];            
        }
        if($param['select_staff_id']!='')
        {
            $where['e.assigned_id'] = $param['select_staff_id'];            
        }
        if(trim($param['status'])!='')
        {
            $where['e.status'] = $param['status'];            
        }
        
        
        $this->db->select('*,i.no_of_pax as nof,i.remark as i_remark,e.enquiry_id as enquiry_number');  
        $this->db->from('(SELECT *, MIN(checkin) AS mcheckin, MAX(checkout) AS mcheckout,SUM(product_amount) as amount FROM itinerary GROUP BY enquiry_id) as i,enquiry as e');  
        $this->db->where('e.id=i.enquiry_id');  
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
    
            
        $q=$this->db->get();
       // echo $this->db->last_query();
        $alldata=$q->result_array();
        
        if ($param['page'] > 1) {
            $start = ($param['page'] - 1) * $limit;
        } else {
            $start = 0;
        }
        
        


// $this->db->select('
//     e.*, 
//     i.no_of_pax AS nof, 
//     i.remark AS i_remark, 
//     i.mcheckin, 
//     i.mcheckout, 
//     i.product,
//     i.amount, 
//     i.cancellation, 
//     i.booking_date,
//     i.supplier_name, 
//     i.booking_reference, 
//     i.pax_name, 
//     e.enquiry_id AS enquiry_number, 
//     e.assigned_id, 
//     co.country_name, 
//     ci.country_name AS city_name
// ');


// $this->db->from('(SELECT enquiry_id, customer_id,MAX(booking_date) AS booking_date,MIN(checkin) AS mcheckin, MAX(checkout) AS mcheckout, SUM(product_amount) as amount, MAX(product) as product, MAX(no_of_pax) as no_of_pax, MAX(remark) as remark, MAX(cancellation) as cancellation, MAX(supplier_name) as supplier_name, MAX(booking_reference) as booking_reference, MAX(pax_name) as pax_name FROM itinerary WHERE is_deleted = 0 GROUP BY enquiry_id) as i, enquiry as e');

// // Conditions
// $this->db->where('i.mcheckout >=', date('Y-m-d', strtotime('-7 days')));
// $this->db->where('e.customer_id = i.customer_id');


// $this->db->join('crcountry as co', 'co.country_id = e.country_id', 'left');
// $this->db->join('crcity as ci', 'ci.city_id = e.city_id', 'left');


// $this->db->where($where);

$this->db->select('
            *,
            i.no_of_pax as nof,
            i.remark as i_remark,
            e.enquiry_id as enquiry_number,
            c.id as customer_id,
            c.name as customer_name,
            crcountry.country_name as country_name,
            crcity.country_name as city_name
        ');

        $this->db->from('(SELECT *, MIN(checkin) AS mcheckin, MAX(checkout) AS mcheckout, SUM(product_amount) as amount FROM itinerary WHERE is_deleted = 0 GROUP BY enquiry_id) as i');

        $this->db->join('enquiry as e', 'e.id = i.enquiry_id');
        $this->db->join('customer as c', 'e.customer_id = c.id');
        $this->db->join('crcountry', 'e.country_id = crcountry.country_id', 'left');
        $this->db->join('crcity', 'e.city_id = crcity.city_id', 'left');
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
        if($param['start_date']!='' && $param['end_date']=='')
        {
            $this->db->where("(i.booking_date='".date('Y-m-d',strtotime($param['start_date']))."')");
        }
        if($param['start_date']!='' && $param['end_date']!='')
        {
            $this->db->where("(i.booking_date>='".date('Y-m-d',strtotime($param['start_date']))."' AND i.booking_date<='".date('Y-m-d',strtotime($param['end_date']))."')");
        }
          
    
        $this->db->order_by($param['column'],$param['sort']); 
        $q=$this->db->get();
        //echo $this->db->last_query();
        
        $sorted_data_first=$q->result_array();
        $i=$start+1;
        
        
        $cr=$this->db->get('user');
        $user=$cr->result_array();
        $user_data = array();
        foreach($user as $key => $value)
        {
            $user_data[$value['id']] = $value;
        }
        foreach($sorted_data_first as $key=>$value)
        {
            $sorted_data_first[$key]['assigned_to']=$user_data[$value['assigned_id']]['name'];
            $sorted_data_first[$key]['added_by']=$user_data[$value['added_by_id']]['name'];
        }
        echo json_encode($sorted_data_first);
        
    }
    
}

?>