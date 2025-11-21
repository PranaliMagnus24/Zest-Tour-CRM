<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS");
class TravelledModel extends CI_model
{
    public function get_Travelled_data()
    {
        $limit = 200;
        $this->load->database();
        $param=$this->input->get();
        $this->db->where("end_date!=","0000-00-00");
        $this->db->where("end_date<='".date('Y-m-d', strtotime('-7 days'))."'");
        $this->db->where("status='Confirmed'");  
        $q=$this->db->get('enquiry');
        $alldata=$q->result_array();        
        $new_param=[];
        foreach($alldata as $key=>$value)
        {
            $new_param['status']='Travelled';
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
        
        $this->db->select('*,i.no_of_pax as nof,i.remark as i_remark,e.enquiry_id as enquiry_number,c.id as customer_id,c.name as customer_name');  
        $this->db->from('(SELECT *, MIN(checkin) AS mcheckin, MAX(checkout) AS mcheckout,SUM(product_amount) as amount FROM itinerary GROUP BY enquiry_id) as i,enquiry as e,customer as c');  
        $this->db->where('e.id=i.enquiry_id');  
        $this->db->where('e.customer_id=c.id');  
        $this->db->where($where);  
        $q=$this->db->get();
        //echo $this->db->last_query();
        $alldata=$q->result_array();
        
        if ($param['page'] > 1) {
            $start = ($param['page'] - 1) * $limit;
        } else {
            $start = 0;
        }
        $cancellation_date=date('Y-m-d',strtotime('+1 days',strtotime(date('Y-m-d'))));
        $this->db->select('*,i.no_of_pax as nof,i.remark as i_remark,e.enquiry_id as enquiry_number,c.id as customer_id,c.name as customer_name');  
        $this->db->from('(SELECT *, MIN(checkin) AS mcheckin, MAX(checkout) AS mcheckout,SUM(product_amount) as amount FROM itinerary GROUP BY enquiry_id) as i,enquiry as e,customer as c');  
        $this->db->where('e.id=i.enquiry_id');  
        $this->db->where('e.customer_id=c.id');  
        $this->db->where($where); 
        $this->db->order_by($param['column'],$param['sort']); 
        $this->db->limit($limit, $start);
        $q=$this->db->get();
        // echo $this->db->last_query();
        $sorted_data_first=$q->result_array();
        $i=$start+1;
        $sorted_data_second = array();
        $customer_id_array = array();
        foreach ($sorted_data_first as $key => $val) {
            $customer_id_array[$val['customer_id']] = $val['customer_id'];
        }
        $cr=$this->db->get('user');
        $user=$cr->result_array();
        $user_data = array();
        foreach($user as $key => $value)
        {
            $user_data[$value['id']] = $value;
        }

        $customer_data = array();
        if(count($sorted_data_first)<=0)
        {
            $customer_id_array=[0];
        }
        $this->db->where_in('id', $customer_id_array);
        $cr=$this->db->get('customer');
        //echo $this->db->last_query();
        $customer=$cr->result_array();
        foreach($customer as $key => $value)
        {
            $customer_data[$value['id']] = $value;
        }
        $today = date('Y-m-d');
        foreach($sorted_data_first as $key=>$value)
        {
            $start_date=date('Y-m-d',strtotime($value['cancellation']));
            $last7_date=date('Y-m-d', strtotime('+7 days'));
            ?>
<div class="col-12 p-0 pull-left border-top b-l-r Flex " data-checkin="<?php echo $value['mcheckin'] ?>"
    data-checkout="<?php echo $value['mcheckout'] ?>" data-booking_date="<?php echo $value['booking_date'] ?>"
    ondblclick="redirect(<?php echo $value['customer_id']?>, 'customer_detail')" style="background-color: #ebeefe;">
    <div class="pull-left Flex-item font-blue" style="width: 4%;padding-left: 8px;"><?php echo $i++ ?></div>
    <div class="col-1 text-ellipsis pull-left Flex-item font-blue position-relative" style="width: 11%;"
        onmouseover="show_tooltip(event,'<?php echo $value['enquiry_number']?>')" onmouseout="hide_tooltip(event)">
        <?php echo $value['enquiry_number']?></div>
    <div class="col-2 text-ellipsis pull-left Flex-item font-blue position-relative" style="width: 10%;"
        onmouseover="show_tooltip(event,'<?php echo date('d M Y',strtotime($value['booking_date']))?>')"
        onmouseout="hide_tooltip(event)"><?php echo date('d M Y',strtotime($value['booking_date']))?></div>
    <div class="col-1 text-ellipsis pull-left Flex-item font-blue" style="width: 10%;"
        onmouseover="show_tooltip(event,'<?php echo date('d M Y',strtotime($value['mcheckin']))?>')"
        onmouseout="hide_tooltip(event)"><?php echo date('d M Y',strtotime($value['mcheckin']))?></div>
    <div class="col-1 text-ellipsis pull-left Flex-item font-blue" style="width: 10%;"
        onmouseover="show_tooltip(event,'<?php echo date('d M Y',strtotime($value['mcheckout']))?>')"
        onmouseout="hide_tooltip(event)"><?php echo date('d M Y',strtotime($value['mcheckout']))?></div>
    <div class="col-1 text-ellipsis pull-left Flex-item font-blue" style="width: 10%;"><?php echo $value['product']?>
    </div>
    <div class="col-1 text-ellipsis pull-left Flex-item font-blue" style="width: 10%;"><?php echo $value['amount']?>
    </div>
    <div class="col-1 text-ellipsis pull-left Flex-item font-blue" style="width: 10%;"
        onmouseover="show_tooltip(event,'<?php echo $user_data[$value['assigned_id']]['name']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $user_data[$value['assigned_id']]['name']?></div>
    <div class="col-2 text-ellipsis pull-left Flex-item font-blue" style="width: 15%;"
        onmouseover="show_tooltip(event,'<?php echo $value['pax_name']?>')" onmouseout="hide_tooltip(event)">
        <?php echo $value['pax_name']?></div>
    <div class="col-2 text-ellipsis pull-left Flex-item font-blue" style="width: 10%;"
        onmouseover="show_tooltip(event,'<?php echo $value['nof']?>')" onmouseout="hide_tooltip(event)">
        <?php echo $value['nof']?></div>
</div>
<?php
        }
        echo ",,$". count($alldata);
        $this->load->model('Pageination');
        echo ",,$".$this->Pageination->get_pageination(count($alldata), $limit, 2, $param['page'], $param['func']);
    }
    
    public function get_search_Travelled_data()
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
        if($param['select_staff_id']!='')
        {
            $where['e.assigned_id'] = $param['select_staff_id'];            
        }
        if(trim($param['status'])!='')
        {
            $where['e.status'] = $param['status'];            
        }
        
        
        $this->db->select('*,i.no_of_pax as nof,i.remark as i_remark,e.enquiry_id as enquiry_number,c.id as customer_id,c.name as customer_name');  
        $this->db->from('(SELECT *, MIN(checkin) AS mcheckin, MAX(checkout) AS mcheckout,SUM(product_amount) as amount FROM itinerary WHERE is_deleted = 0 GROUP BY enquiry_id) as i,enquiry as e,customer as c');  
        $this->db->where('e.id=i.enquiry_id');  
        $this->db->where('e.customer_id=c.id');  
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
        if($param['value']!='')
        {
            $this->db->where("(i.product LIKE '%" . $param['value'] . "%' escape '!' OR i.supplier_name LIKE '%" . $param['value'] . "%' escape '!' OR i.booking_reference LIKE '%" . $param['value'] . "%' escape '!' OR i.pax_name LIKE '%" . $param['value'] . "%' escape '!' OR e.id LIKE '%" . $param['value'] . "%' escape '!')");
        }
        if($param['start_date']!='' && $param['end_date']=='')
        {
            $this->db->where("(i.booking_date>='".date('Y-m-d',strtotime($param['start_date']))."')");
        }
        if($param['start_date']!='' && $param['end_date']!='')
        {
            $this->db->where("(i.booking_date>='".date('Y-m-d',strtotime($param['start_date']))."' AND i.booking_date<='".date('Y-m-d',strtotime($param['end_date']))."')");
        }
         
            
        $q=$this->db->get();
        // echo $this->db->last_query();
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
        if($param['value']!='')
        {
            $this->db->where("(i.product LIKE '%" . $param['value'] . "%' escape '!' OR i.supplier_name LIKE '%" . $param['value'] . "%' escape '!' OR i.booking_reference LIKE '%" . $param['value'] . "%' escape '!' OR i.pax_name LIKE '%" . $param['value'] . "%' escape '!' OR e.id LIKE '%" . $param['value'] . "%' escape '!')");

        }
        
        if($param['start_date']!='' && $param['end_date']=='')
        {
            $this->db->where("(i.booking_date>='".date('Y-m-d',strtotime($param['start_date']))."')");
        }
        if($param['start_date']!='' && $param['end_date']!='')
        {
            $this->db->where("(i.booking_date>='".date('Y-m-d',strtotime($param['start_date']))."' AND i.booking_date<='".date('Y-m-d',strtotime($param['end_date']))."')");
        }


      
      
                  
        $this->db->order_by($param['column'],$param['sort']); 
        $this->db->limit($limit, $start);
        $q=$this->db->get();
       // echo $this->db->last_query();
        
        $sorted_data_first=$q->result_array();
        $i=$start+1;
        $total_nof = 0;

        $cr = $this->db->get('user');
        $user = $cr->result_array();
        $user_data = array();
        foreach ($user as $key => $value) {
            $user_data[$value['id']] = $value;
         
        }

        $today = date('Y-m-d');
        foreach($sorted_data_first as $key=>$value)
        {
            $total_amount += floatval($value['amount']);
            $total_nof += floatval($value['nof']);
            $start_date=date('Y-m-d',strtotime($value['cancellation']));
            $last7_date=date('Y-m-d', strtotime('+7 days'));



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
<div class="col-12 p-0 pull-left border-top b-l-r Flex " data-checkin="<?php echo $value['mcheckin'] ?>"
    data-checkout="<?php echo $value['mcheckout'] ?>" data-booking_date="<?php echo $value['booking_date'] ?>"
    ondblclick="redirect(<?php echo $value['customer_id']?>, 'customer_detail')" style="background-color: #ebeefe;">
    <div class="pull-left Flex-item font-blue" style="width: 4%;padding-left: 8px;"><?php echo $i++ ?></div>
    <div class="col-1 text-ellipsis pull-left Flex-item font-blue position-relative" style="width: 11%;"
        onmouseover="show_tooltip(event,'<?php echo $value['enquiry_number']?>')" onmouseout="hide_tooltip(event)">
        <?php echo $value['enquiry_number']?></div>
    <div class="col-2 text-ellipsis pull-left Flex-item font-blue position-relative" style="width: 8%;"
        onmouseover="show_tooltip(event,'<?php echo date('d M Y',strtotime($value['booking_date']))?>')"
        onmouseout="hide_tooltip(event)"><?php echo date('d M Y',strtotime($value['booking_date']))?></div>
    <div class="col-1 text-ellipsis pull-left Flex-item font-blue" style="width: 8%;"
        onmouseover="show_tooltip(event,'<?php echo date('d M Y',strtotime($value['mcheckin']))?>')"
        onmouseout="hide_tooltip(event)"><?php echo date('d M Y',strtotime($value['mcheckin']))?></div>
    <div class="col-1 text-ellipsis pull-left Flex-item font-blue" style="width: 8%;"
        onmouseover="show_tooltip(event,'<?php echo date('d M Y',strtotime($value['mcheckout']))?>')"
        onmouseout="hide_tooltip(event)"><?php echo date('d M Y',strtotime($value['mcheckout']))?></div>
    <div class="col-1 text-ellipsis pull-left Flex-item font-blue" style="width: 8%;"><?php echo $value['product']?>
    </div>
    <div class="col-1 text-ellipsis pull-left Flex-item font-blue" style="width: 8%;"><?php echo $value['amount']?>
    </div>
    <!-- <div class="col-1 text-ellipsis pull-left Flex-item font-blue" style="width: 7%;"><?php echo $value['destination']?></div> -->
    <!-- <div class="col-2 text-ellipsis pull-left Flex-item font-blue" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $value['destination']?>')" onmouseout="hide_tooltip(event)"><?php echo $value['destination']?></div> -->

    <div class="col-2 text-ellipsis pull-left Flex-item font-blue" style="width: 7%;" onmouseover="show_tooltip(event, '<?php
         if (empty($value['destination'])) {
             $parts = [];
             if (!empty($value['country_name'])) $parts[] = $value['country_name'];
             if (!empty($value['city_name'])) $parts[] = $value['city_name'];
             echo htmlspecialchars(implode(', ', $parts), ENT_QUOTES);
         } else {
             echo htmlspecialchars($value['destination'], ENT_QUOTES);
         }
     ?>')" onmouseout="hide_tooltip(event)">
        <?php
        if (empty($value['destination'])) {
            $parts = [];
            if (!empty($value['country_name'])) $parts[] = $value['country_name'];
            if (!empty($value['city_name'])) $parts[] = $value['city_name'];
            echo implode(', ', $parts);
        } else {
            echo $value['destination'];
        }
    ?>
    </div>


    <div class="col-1 text-ellipsis pull-left Flex-item font-blue" style="width: 10%;"
        onmouseover="show_tooltip(event,'<?php echo $user_data[$value['assigned_id']]['name']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $user_data[$value['assigned_id']]['name']?></div>
    <div class="col-2 text-ellipsis pull-left Flex-item font-blue" style="width: 15%;"
        onmouseover="show_tooltip(event,'<?php echo $value['pax_name']?>')" onmouseout="hide_tooltip(event)">
        <?php echo $value['pax_name']?></div>
    <div class="col-2 text-ellipsis pull-left Flex-item font-blue" style="width: 10%;"
        onmouseover="show_tooltip(event,'<?php echo $value['nof']?>')" onmouseout="hide_tooltip(event)">
        <?php echo $value['nof']?></div>
</div>
<?php
        }
        ?>

<!-- <div class="col-12 p-0 pull-left border-top b-l-r Flex" style="background-color: #dfe6f1; font-weight: bold;"> -->
    <!-- <div class="pull-left Flex-item text-left" style="width:50%; padding: 10px 10px; border-right: 1px solid #000;">
       
        Total Amount (All Pages): <?php echo number_format($grand_total_amount, 2); ?>
    </div>
    <div class="pull-left Flex-item text-left" style="width:50%; padding: 10px 10px;">
       
        Total No Pax (All Pages): <?php echo number_format($grand_total_nof, 2); ?>
    </div>
</div> -->
<div class="col-12 p-0 pull-left border-top b-l-r Flex" style="background-color: #dfe6f1; font-weight: bold;">
    <div class="pull-left Flex-item text-left"  style="width:50%; padding: 5px 10px; border-right: 1px solid #000;">
         Total Amount : <?php echo number_format($total_amount, 2); ?>
    </div>
     <div class="pull-left Flex-item text-left" style="width:50%; padding: 5px 10px;">
        Total No Pax : <?php echo number_format($total_nof, 2); ?>
    </div>

  
</div>
<?php 

        // echo ",,$". count($alldata);
        echo ",,$". count($alldata);
        $this->load->model('Pageination');
        echo ",,$".$this->Pageination->get_pageination(count($alldata), $limit, 2, $param['page'], $param['func']);
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
        //echo $this->db->last_query();
        $alldata=$q->result_array();
        
        if ($param['page'] > 1) {
            $start = ($param['page'] - 1) * $limit;
        } else {
            $start = 0;
        }
        
        
        $this->db->select('*,i.no_of_pax as nof,i.remark as i_remark,e.enquiry_id as enquiry_number,c.id as customer_id,c.name as customer_name,i.amount');  
        $this->db->from('(SELECT *, MIN(checkin) AS mcheckin, MAX(checkout) AS mcheckout,SUM(product_amount) as amount FROM itinerary GROUP BY enquiry_id) as i,enquiry as e,customer as c');  
        $this->db->where('e.id=i.enquiry_id');  
        $this->db->where('e.customer_id=c.id');  
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