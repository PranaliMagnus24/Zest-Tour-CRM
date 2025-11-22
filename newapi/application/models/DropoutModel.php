<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS");
class DropoutModel extends CI_model
{
    public function get_Dropout_data()
    {
        $limit = 200;
        
        $this->load->database();
        $param=$this->input->get();
        $where = array();
        if(trim($param['user_type'])=='domestic' || trim($param['user_type'])=='international')
        {
            $where['type'] = $param['user_type'];
            if(trim($param['user_designation'])!='tl')
            {
                $where['assigned_id'] = $param['assigned_id'];
            }
        }
        if(trim($param['type_customer'])!='')
        {
            $where['type_customer'] = $param['type_customer'];            
        }
        if(trim($param['status'])!='')
        {
            $where['status'] = $param['status'];            
        }
            
        
        if ($param['page'] > 1) {
            $start = ($param['page'] - 1) * $limit;
        } else {
            $start = 0;
        }
        
        $this->db->select('r.customer_id, r.time, r.reminder');
        $this->db->from('reminder r');
        $this->db->join('(SELECT customer_id, MAX(time) AS max_time FROM reminder GROUP BY customer_id) m', 'r.customer_id = m.customer_id AND r.time = m.max_time', 'inner');
        $this->db->where('r.time >=', date('Y-m-d'));
        $this->db->order_by('r.time', 'ASC');
        $q=$this->db->get();
        //echo $this->db->last_query();
        $reminder_array=$q->result_array();
        $reminder_customer=$reminder=array();
        foreach ($reminder_array as $key => $val) {
            if (!array_key_exists($val['customer_id'], $reminder)) {
                $reminder[$val['customer_id']] = $val['customer_id'];
                $reminder_customer[$val['customer_id']] = $val;
            }
            
        }
        
        $this->db->where($where);
        $q=$this->db->get('enquiry');
        // echo $this->db->last_query();
        $filter_data=$q->result_array();
        
        $this->db->limit($limit, $start);
        $this->db->where($where);        
        if(count($reminder)>0)
        {
            $this->db->order_by("FIELD(customer_id, ".implode(',',$reminder).",customer_id)");
        }
        $q=$this->db->get('enquiry');
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
        if(count($sorted_data_first)<=0)
        {
            $customer_id_array=[0];
        }
        
        $this->db->where_in('customer_id', $customer_id_array);
        $this->db->order_by('time', "ASC");
        $cr=$this->db->get('remark');
        //echo $this->db->last_query();
        $remark_data=$cr->result_array();
        $remark = array();
        foreach($remark_data as $key => $value)
        {
            $remark[$value['customer_id']] = $value;
        }
        $customer_data = array();
        $this->db->where_in('id', $customer_id_array);
        $cr=$this->db->get('customer');
        $customer=$cr->result_array();
        foreach($customer as $key => $value)
        {
            $customer_data[$value['id']] = $value;
        }
        $today = date('Y-m-d');
        foreach($sorted_data_first as $key=>$value)
        {
            $start_date = $last3_date = '';
            if (array_key_exists($value['customer_id'], $reminder_customer)) {
                $start_date = date('Y-m-d', strtotime($reminder_customer[$value['customer_id']]['time']));
                $last3_date = date('Y-m-d', strtotime('+4 days'));
            }
        ?>
            <div class="col-12 p-0 pull-left border-top b-l-r Flex" ondblclick="redirect(<?php echo $value['customer_id']?>, 'customer_detail')" style="background-color: #ebeefe;display: flex;">
            <div class="pull-left Flex-item font-blue text-ellipsis" style="width: 4%;padding-left: 8px;"><?php echo $i++?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis position-relative" style="width: 10%;" onmouseover="show_tooltip(event,'<?php echo $value['enquiry_id']?>')" onmouseout="hide_tooltip(event)"><?php echo $value['enquiry_id']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis abc position-relative" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['name']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['name']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['corporate']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['corporate']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['number']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['number']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['email']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['email']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $user_data[$value['assigned_id']]['name']?>')" onmouseout="hide_tooltip(event)"><?php echo $user_data[$value['assigned_id']]['name']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['reference']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['reference']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $value['destination']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $value['days']?></div>
            <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo 'Adult-'.$value['adult'].'<br>Child-'.$value['children'].'<br>Infant-'.$value['infant'].'<br>Total-'.($value['adult']+$value['children']+$value['infant']);?>')" onmouseout="hide_tooltip(event)"><?php echo 'A-'.$value['adult'].', C-'.$value['children'].', I-'.$value['infant']?></div>
            <div class="col-2 pull-left Flex-item font-blue text-ellipsis p-0 text-center" style="width: 6%;"><?php echo date('d M Y',strtotime($value['start_date']))?></div>
            <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 13%;" <?php if (array_key_exists($value['customer_id'], $remark)) {?> onmouseover="show_tooltip(event,'<?php echo $remark[$value['customer_id']]['remark'] ?>')" onmouseout="hide_tooltip(event)"<?php }?>><?php if (array_key_exists($value['customer_id'], $remark)) {echo $remark[$value['customer_id']]['remark'];}?></div>
            <?php if($start_date<$today){$tool_data='No Reminder';}elseif($start_date==$today){$tool_data='Todays';}elseif($start_date>$today && $start_date<=$last3_date){$tool_data='Recent 3 days';}else{$tool_data='After 3 days';}?>
            <div class="col-2 position-relative pull-left Flex-item font-blue text-center text-ellipsis" onmouseover="show_tooltip(event,'<?php echo $tool_data; ?>')" onmouseout="hide_tooltip(event)" style="width: 5.5%;padding-top: 4px;"><svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512" fill="<?php if ($start_date < $today) {
                echo "white";
            } elseif ($start_date == $today) {
                echo "red";
            } elseif ($start_date > $today && $start_date <= $last3_date) {
                echo "orange";
            } else {
                echo "green";
            } ?>" width="20" style="<?php if ($start_date < $today) {echo "stroke: black;stroke-width: 50px;";}?>"><path d="M256 464c-114.69 0-208-93.31-208-208S141.31 48 256 48s208 93.31 208 208-93.31 208-208 208z"/></svg></div>
        </div>  
        <?php
        }
        echo ",,$". count($filter_data);
        $this->load->model('Pageination');
        $second_count = count($filter_data);
        echo ",,$".$this->Pageination->get_pageination(count($filter_data), $limit, 2, $param['page'], $param['func'],$second_count);
    }
    public function get_search_Dropout_data()
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
        if($param['select_staff_id']!='' && $param['select_staff_id']!="null")
        {
            $where['e.assigned_id'] = $param['select_staff_id'];            
        }
        if(trim($param['status'])!='')
        {
            $where['e.status'] = $param['status'];            
        }
        if ($param['page'] > 1) {
            $start = ($param['page'] - 1) * $limit;
        } else {
            $start = 0;
        }
        
        $this->db->select('r.customer_id, r.time, r.reminder');
        $this->db->from('reminder r');
        $this->db->join('(SELECT customer_id, MAX(time) AS max_time FROM reminder GROUP BY customer_id) m', 'r.customer_id = m.customer_id AND r.time = m.max_time', 'inner');
        $this->db->where('r.time >=', date('Y-m-d'));
        $this->db->order_by('r.time', 'ASC');
        $q=$this->db->get();
        $reminder_array=$q->result_array();
        
        $reminder_customer=$reminder=array();
        foreach ($reminder_array as $key => $val) {
            if (!array_key_exists($val['customer_id'], $reminder)) {
                $reminder[$val['customer_id']] = $val['customer_id'];
                $reminder_customer[$val['customer_id']] = $val;
            }
            
        }
        $this->db->select('*')
            ->from('enquiry as e, customer as c')
            ->where($where);
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
            $this->db->where("(c.name LIKE '%" . $param['value'] . "%' escape '!' OR c.corporate LIKE '%" . $param['value'] . "%' escape '!' OR c.number LIKE '%" . $param['value'] . "%' escape '!' OR c.email LIKE '%" . $param['value'] . "%' escape '!' OR c.reference LIKE '%" . $param['value'] . "%' escape '!' OR e.start_date LIKE '%" . $param['value'] . "%' escape '!'OR e.status LIKE '%" . $param['value'] . "%' escape '!'OR e.remark LIKE '%" . $param['value'] . "%' escape '!' OR e.enquiry_id LIKE '%" . $param['value'] . "%' escape '!'  OR e.destination LIKE '%" . $param['value'] . "%' escape '!')");
        }
        
        if($param['start_date']!='' && $param['end_date']=='')
        {
            $this->db->where("(e.datetime='".date('Y-m-d',strtotime($param['start_date']))."')");
        }
        if($param['start_date']!='' && $param['end_date']!='')
        {
            $this->db->where("(e.datetime>='".date('Y-m-d',strtotime($param['start_date']))."' AND e.datetime<='".date('Y-m-d',strtotime($param['end_date']))."')");
        }
        if(count($reminder)>0)
        {
            $this->db->order_by("FIELD(customer_id, ".implode(',',$reminder).",customer_id)");
        }
        $this->db->where('e.customer_id = c.id');
         
        $q=$this->db->get();
        //echo $this->db->last_query();
        $filter_data=$q->result_array();
        
        $this->db->select('*')
            ->from('enquiry as e, customer as c')
            ->where($where);
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
                $this->db->where("(e.datetime='".date('Y-m-d',strtotime($param['start_date']))."')");
            }
            if($param['start_date']!='' && $param['end_date']!='')
            {
                $this->db->where("(e.datetime>='".date('Y-m-d',strtotime($param['start_date']))."' AND e.datetime<='".date('Y-m-d',strtotime($param['end_date']))."')");
            }
        
            if($param['value']!='')
            {
                $this->db->where("(c.name LIKE '%" . $param['value'] . "%' escape '!' OR c.corporate LIKE '%" . $param['value'] . "%' escape '!' OR c.number LIKE '%" . $param['value'] . "%' escape '!' OR c.email LIKE '%" . $param['value'] . "%' escape '!' OR c.reference LIKE '%" . $param['value'] . "%' escape '!' OR e.start_date LIKE '%" . $param['value'] . "%' escape '!'OR e.status LIKE '%" . $param['value'] . "%' escape '!'OR e.remark LIKE '%" . $param['value'] . "%' escape '!' OR e.enquiry_id LIKE '%" . $param['value'] . "%' escape '!'  OR e.destination LIKE '%" . $param['value'] . "%' escape '!')");
            }
            if(count($reminder)>0)
            {
                $this->db->order_by("FIELD(customer_id, ".implode(',',$reminder).",customer_id)");
            }
            $this->db->where('e.customer_id = c.id')
            ->limit($limit, $start);
         
        $q=$this->db->get();
        //echo $this->db->last_query();
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
        $remark = array();
        $customer_data = array();
            
        if (count($customer_id_array) > 0) {
            $this->db->where_in('customer_id', $customer_id_array);
            $this->db->order_by('time', "ASC");
            $cr = $this->db->get('remark');
            //echo $this->db->last_query();
            $remark_data = $cr->result_array();
            foreach ($remark_data as $key => $value) {
                $remark[$value['customer_id']] = $value;
            }
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
            $start_date = $last3_date='';
            if (array_key_exists($value['customer_id'], $reminder_customer)) {
                $start_date = date('Y-m-d', strtotime($reminder_customer[$value['customer_id']]['time']));
                $last3_date = date('Y-m-d', strtotime('+4 days'));
            }
        ?>
            <div class="col-12 p-0 pull-left border-top b-l-r Flex" ondblclick="redirect(<?php echo $value['customer_id']?>, 'customer_detail')" style="background-color: #ebeefe;display: flex;">
            <div class="pull-left Flex-item font-blue text-ellipsis" style="width: 4%;padding-left: 8px;"><?php echo $i++?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis position-relative" style="width: 10%;" onmouseover="show_tooltip(event,'<?php echo $value['enquiry_id']?>')" onmouseout="hide_tooltip(event)"><?php echo $value['enquiry_id']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis abc position-relative" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['name']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['name']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['corporate']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['corporate']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['number']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['number']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['email']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['email']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo $user_data[$value['assigned_id']]['name']?>')" onmouseout="hide_tooltip(event)"><?php echo $user_data[$value['assigned_id']]['name']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;" onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['reference']?>')" onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['reference']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"><?php echo $value['destination']?></div>
            <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $value['days']?></div>
            <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event,'<?php echo 'Adult-'.$value['adult'].'<br>Child-'.$value['children'].'<br>Infant-'.$value['infant'].'<br>Total-'.($value['adult']+$value['children']+$value['infant']);?>')" onmouseout="hide_tooltip(event)"><?php echo 'A-'.$value['adult'].', C-'.$value['children'].', I-'.$value['infant']?></div>
            <div class="col-2 pull-left Flex-item font-blue text-ellipsis p-0 text-center" style="width: 6%;"><?php echo date('d M Y',strtotime($value['start_date']))?></div>
            <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 13%;" <?php if (array_key_exists($value['customer_id'], $remark)) {?> onmouseover="show_tooltip(event,'<?php echo $remark[$value['customer_id']]['remark'] ?>')" onmouseout="hide_tooltip(event)"<?php }?>><?php if (array_key_exists($value['customer_id'], $remark)) {echo $remark[$value['customer_id']]['remark'];}?></div>
            <?php if($start_date<$today){$tool_data='No Reminder';}elseif($start_date==$today){$tool_data='Todays';}elseif($start_date>$today && $start_date<=$last3_date){$tool_data='Recent 3 days';}else{$tool_data='After 3 days';}?>
            <div class="col-2 position-relative pull-left Flex-item font-blue text-center text-ellipsis" onmouseover="show_tooltip(event,'<?php echo $tool_data; ?>')" onmouseout="hide_tooltip(event)" style="width: 5.5%;padding-top: 4px;"><svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512" fill="<?php if ($start_date < $today) {
                echo "white";
            } elseif ($start_date == $today) {
                echo "red";
            } elseif ($start_date > $today && $start_date <= $last3_date) {
                echo "orange";
            } else {
                echo "green";
            } ?>" width="20" style="<?php if ($start_date < $today) {echo "stroke: black;stroke-width: 50px;";}?>"><path d="M256 464c-114.69 0-208-93.31-208-208S141.31 48 256 48s208 93.31 208 208-93.31 208-208 208z"/></svg></div>
        </div>  
        <?php
        }
        echo ",,$". count($filter_data);
        $this->load->model('Pageination');
        $second_count = count($filter_data);
        echo ",,$".$this->Pageination->get_pageination(count($filter_data), $limit, 2, $param['page'], $param['func'],$second_count);
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
        if($param['select_staff_id']!='')
        {
            $where['e.assigned_id'] = $param['select_staff_id'];            
        }
        if(trim($param['status'])!='')
        {
            $where['e.status'] = $param['status'];            
        }
            

        if ($param['page'] > 1) {
            $start = ($param['page'] - 1) * $limit;
        } else {
            $start = 0;
        }
        
        $this->db->select('r.customer_id, r.time, r.reminder');
        $this->db->from('reminder r');
        $this->db->join('(SELECT customer_id, MAX(time) AS max_time FROM reminder GROUP BY customer_id) m', 'r.customer_id = m.customer_id AND r.time = m.max_time', 'inner');
        $this->db->where('r.time >=', date('Y-m-d'));
        $this->db->order_by('r.time', 'ASC');
        $q=$this->db->get();
        $reminder_array=$q->result_array();
        
        $reminder_customer=$reminder=array();
        foreach ($reminder_array as $key => $val) {
            if (!array_key_exists($val['customer_id'], $reminder)) {
                $reminder[$val['customer_id']] = $val['customer_id'];
                $reminder_customer[$val['customer_id']] = $val;
            }
            
        }
        
        
        $this->db->select('*')
            ->from('enquiry as e, customer as c')
            ->where($where);
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
                $this->db->where("(c.name LIKE '%" . $param['value'] . "%' escape '!' OR c.corporate LIKE '%" . $param['value'] . "%' escape '!' OR c.number LIKE '%" . $param['value'] . "%' escape '!' OR c.email LIKE '%" . $param['value'] . "%' escape '!' OR c.reference LIKE '%" . $param['value'] . "%' escape '!' OR e.start_date LIKE '%" . $param['value'] . "%' escape '!'OR e.status LIKE '%" . $param['value'] . "%' escape '!'OR e.remark LIKE '%" . $param['value'] . "%' escape '!' OR e.enquiry_id LIKE '%" . $param['value'] . "%' escape '!'  OR e.destination LIKE '%" . $param['value'] . "%' escape '!')");
            }
            
            if($param['start_date']!='' && $param['end_date']=='')
            {
                $this->db->where("(e.datetime='".date('Y-m-d',strtotime($param['start_date']))."')");
            }
            if($param['start_date']!='' && $param['end_date']!='')
            {
                $this->db->where("(e.datetime>='".date('Y-m-d',strtotime($param['start_date']))."' AND e.datetime<='".date('Y-m-d',strtotime($param['end_date']))."')");
            }
            if(count($reminder)>0)
            {
                $this->db->order_by("FIELD(customer_id, ".implode(',',$reminder).",customer_id)");
            }
            $this->db->where('e.customer_id = c.id');
         
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
        foreach($sorted_data_first as $key=>$value)
        {
            $sorted_data_first[$key]['assigned_to']=$user_data[$value['assigned_id']]['name'];
            $sorted_data_first[$key]['added_by']=$user_data[$value['added_by_id']]['name'];
        }
        echo json_encode($sorted_data_first);
        
    }  
}

?>
