<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS");
class EnquiryModel extends CI_model
{
    public function get_Enquiry_data()
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
            
        $this->db->where($where);
        $this->db->where("(status='' OR status ='Follow Up')");
        $q=$this->db->get('enquiry');
        $alldata=$q->result_array();
        
        if ($param['page'] > 1) {
            $start = ($param['page'] - 1) * $limit;
        } else {
            $start = 0;
        }
        $this->db->select('customer_id,time,reminder');  
        $this->db->from('(SELECT customer_id, MAX(time) AS time,reminder FROM reminder GROUP BY customer_id ORDER BY time DESC) as customer_id');  
        $this->db->where('time >=',date('Y-m-d'));  
        $this->db->order_by('time', "ASC");
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
        $sorted_data_first=[];
        if(count($reminder)>0)
        {
        $this->db->limit($limit, $start);
        $this->db->where($where);
        $this->db->where_in('customer_id',$reminder);
        $this->db->where("(status='' OR status ='Follow Up')");
        $this->db->order_by("FIELD(customer_id, ".implode(',',$reminder).")");
        $this->db->order_by("id","DESC");
        $q=$this->db->get('enquiry');
        //echo $this->db->last_query();
        $sorted_data_first=$q->result_array();
        }
        $counts = count($sorted_data_first);
        $i=$start+1;
        $sorted_data_second = array();
        $customer_id_array = array();
        foreach ($sorted_data_first as $key => $val) {
            $customer_id_array[$val['customer_id']] = $val['customer_id'];
        }
        $f_c = count($sorted_data_first);
        if ($f_c < $limit) {
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
            
            $this->db->limit($limits, $start);
            $this->db->where($where);
            if(count($reminder)>0)
            {        
                $this->db->where_not_in('customer_id',$reminder);
            }
            $this->db->where("(status='' OR status ='Follow Up')");
            $this->db->order_by("id","DESC");
            $q = $this->db->get('enquiry');
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
            $start_date=date('Y-m-d',strtotime($reminder_customer[$value['customer_id']]['time']));
            $last3_date=date('Y-m-d', strtotime('+4 days'));
            ?>
<div class="col-12 p-0 pull-left border-top b-l-r Flex"
    ondblclick="redirect(<?php echo $value['customer_id']?>, 'customer_detail')"
    style="background-color: #ebeefe;display: flex;">
    <div class="pull-left Flex-item font-blue text-ellipsis" style="width: 4%;padding-left: 8px;"><?php echo $i++?>
    </div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis position-relative" style="width: 10%;"
        onmouseover="show_tooltip(event,'<?php echo $value['enquiry_id']?>')" onmouseout="hide_tooltip(event)">
        <?php echo $value['enquiry_id']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis abc position-relative" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['name']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['name']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['corporate']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['corporate']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['number']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['number']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['email']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['email']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $user_data[$value['assigned_id']]['name']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $user_data[$value['assigned_id']]['name']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['reference']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['reference']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $value['destination']?>
    </div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $value['days']?></div>
    <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo 'Adult-'.$value['adult'].'<br>Child-'.$value['children'].'<br>Infant-'.$value['infant'].'<br>Total-'.($value['adult']+$value['children']+$value['infant']);?>')"
        onmouseout="hide_tooltip(event)">
        <?php echo 'A-'.$value['adult'].', C-'.$value['children'].', I-'.$value['infant']?></div>
    <div class="col-2 pull-left Flex-item font-blue text-ellipsis p-0 text-center" style="width: 8%;">
        <?php if ($value['start_date'] == '0000-00-00') {} else {echo date('d M Y', strtotime($value['start_date']));}?>
    </div>
    <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        <?php if (array_key_exists($value['customer_id'], $remark)) {?>
        onmouseover="show_tooltip(event,'<?php echo $remark[$value['customer_id']]['remark'] ?>')"
        onmouseout="hide_tooltip(event)" <?php }?>>
        <?php if (array_key_exists($value['customer_id'], $remark)) {echo $remark[$value['customer_id']]['remark'];}?>
    </div>
    <?php if($start_date<$today){$tool_data='No Reminder';}elseif($start_date==$today){$tool_data='Todays';}elseif($start_date>$today && $start_date<=$last3_date){$tool_data='Recent 3 days';}else{$tool_data='After 3 days';}?>
    <div class="col-2 position-relative pull-left Flex-item font-blue text-center text-ellipsis"
        onmouseover="show_tooltip(event,'<?php echo $tool_data; ?>')" onmouseout="hide_tooltip(event)"
        style="width: 5.5%;padding-top: 4px;"><svg xmlns="http://www.w3.org/2000/svg" class="ionicon"
            viewBox="0 0 512 512" fill="<?php if ($start_date < $today) {
                echo "white";
            } elseif ($start_date == $today) {
                echo "red";
            } elseif ($start_date > $today && $start_date <= $last3_date) {
                echo "orange";
            } else {
                echo "green";
            } ?>" width="20" style="<?php if ($start_date < $today) {echo "stroke: black;stroke-width: 50px;";}?>">
            <path d="M256 464c-114.69 0-208-93.31-208-208S141.31 48 256 48s208 93.31 208 208-93.31 208-208 208z" />
        </svg></div>
    <div class="col-2 d-flex justify-content-center pull-left Flex-item text-ellipsis" data-bs-toggle="modal"
        data-bs-target="#call_lead"
        onclick="$('#call_lead input[name=customer_id]').val('<?php echo $value['id'];?>'),$('#call_lead .modal-title').html('Do you want to call<br><span style=\'color:#eb1616\'><?php echo $customer_data[$value['customer_id']]['name']?></span>')"
        style="width:4%">
        <div class="colbutton"><svg xmlns="http://www.w3.org/2000/svg" width="15px" fill="#fff" viewBox="0 0 512 512">
                <path xmlns="http://www.w3.org/2000/svg"
                    d="M391 480c-19.52 0-46.94-7.06-88-30-49.93-28-88.55-53.85-138.21-103.38C116.91 298.77 93.61 267.79 61 208.45c-36.84-67-30.56-102.12-23.54-117.13C45.82 73.38 58.16 62.65 74.11 52a176.3 176.3 0 0128.64-15.2c1-.43 1.93-.84 2.76-1.21 4.95-2.23 12.45-5.6 21.95-2 6.34 2.38 12 7.25 20.86 16 18.17 17.92 43 57.83 52.16 77.43 6.15 13.21 10.22 21.93 10.23 31.71 0 11.45-5.76 20.28-12.75 29.81-1.31 1.79-2.61 3.5-3.87 5.16-7.61 10-9.28 12.89-8.18 18.05 2.23 10.37 18.86 41.24 46.19 68.51s57.31 42.85 67.72 45.07c5.38 1.15 8.33-.59 18.65-8.47 1.48-1.13 3-2.3 4.59-3.47 10.66-7.93 19.08-13.54 30.26-13.54h.06c9.73 0 18.06 4.22 31.86 11.18 18 9.08 59.11 33.59 77.14 51.78 8.77 8.84 13.66 14.48 16.05 20.81 3.6 9.53.21 17-2 22-.37.83-.78 1.74-1.21 2.75a176.49 176.49 0 01-15.29 28.58c-10.63 15.9-21.4 28.21-39.38 36.58A67.42 67.42 0 01391 480z" />
            </svg></div>
    </div>
</div>
<?php
        }
        if ($f_c < $limit) 
        {
            foreach($sorted_data_second as $key=>$value)
            {
                $start_date = $last3_date='';
                if (array_key_exists($value['customer_id'], $reminder_customer)) {
                    $start_date = date('Y-m-d', strtotime($reminder_customer[$value['customer_id']]['time']));
                    $last3_date = date('Y-m-d', strtotime('+4 days'));
                }
            ?>
<div class="col-12 p-0 pull-left border-top b-l-r Flex"
    ondblclick="redirect(<?php echo $value['customer_id']?>, 'customer_detail')"
    style="background-color: #ebeefe;display: flex;">
    <div class="pull-left Flex-item font-blue text-ellipsis" style="width: 4%;padding-left: 8px;"><?php echo $i++?>
    </div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis position-relative" style="width: 10%;"
        onmouseover="show_tooltip(event,'<?php echo $value['enquiry_id']?>')" onmouseout="hide_tooltip(event)">
        <?php echo $value['enquiry_id']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis abc position-relative" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['name']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['name']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['corporate']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['corporate']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['number']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['number']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['email']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['email']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $user_data[$value['assigned_id']]['name']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $user_data[$value['assigned_id']]['name']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['reference']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['reference']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $value['destination']?>
    </div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $value['days']?></div>
    <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo 'Adult-'.$value['adult'].'<br>Child-'.$value['children'].'<br>Infant-'.$value['infant'].'<br>Total-'.($value['adult']+$value['children']+$value['infant']);?>')"
        onmouseout="hide_tooltip(event)">
        <?php echo 'A-'.$value['adult'].', C-'.$value['children'].', I-'.$value['infant']?></div>
    <div class="col-2 pull-left Flex-item font-blue text-ellipsis p-0 text-center" style="width: 8%;">
        <?php if ($value['start_date'] == '0000-00-00') {} else {echo date('d M Y', strtotime($value['start_date']));}?>
    </div>
    <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        <?php if (array_key_exists($value['customer_id'], $remark)) {?>
        onmouseover="show_tooltip(event,'<?php echo $remark[$value['customer_id']]['remark'] ?>')"
        onmouseout="hide_tooltip(event)" <?php }?>>
        <?php if (array_key_exists($value['customer_id'], $remark)) {echo $remark[$value['customer_id']]['remark'];}?>
    </div>
    <?php if($start_date<$today){$tool_data='No Reminder';}elseif($start_date==$today){$tool_data='Todays';}elseif($start_date>$today && $start_date<=$last3_date){$tool_data='Recent 3 days';}else{$tool_data='After 3 days';}?>
    <div class="col-2 position-relative pull-left Flex-item font-blue text-center text-ellipsis"
        onmouseover="show_tooltip(event,'<?php echo $tool_data; ?>')" onmouseout="hide_tooltip(event)"
        style="width: 5.5%;padding-top: 4px;"><svg xmlns="http://www.w3.org/2000/svg" class="ionicon"
            viewBox="0 0 512 512" fill="<?php if ($start_date < $today) {
                    echo "white";
                } elseif ($start_date == $today) {
                    echo "red";
                } elseif ($start_date > $today && $start_date <= $last3_date) {
                    echo "orange";
                } else {
                    echo "green";
                } ?>" width="20" style="<?php if ($start_date < $today) {echo "stroke: black;stroke-width: 50px;";}?>">
            <path d="M256 464c-114.69 0-208-93.31-208-208S141.31 48 256 48s208 93.31 208 208-93.31 208-208 208z" />
        </svg></div>
    <div class="col-2 d-flex justify-content-center pull-left Flex-item text-ellipsis" data-bs-toggle="modal"
        data-bs-target="#call_lead"
        onclick="$('#call_lead input[name=customer_id]').val('<?php echo $value['id'];?>'),$('#call_lead .modal-title').html('Do you want to call<br><span style=\'color:#eb1616\'><?php echo $customer_data[$value['customer_id']]['name']?></span>')"
        style="width:4%">
        <div class="colbutton"><svg xmlns="http://www.w3.org/2000/svg" width="15px" fill="#fff" viewBox="0 0 512 512">
                <path xmlns="http://www.w3.org/2000/svg"
                    d="M391 480c-19.52 0-46.94-7.06-88-30-49.93-28-88.55-53.85-138.21-103.38C116.91 298.77 93.61 267.79 61 208.45c-36.84-67-30.56-102.12-23.54-117.13C45.82 73.38 58.16 62.65 74.11 52a176.3 176.3 0 0128.64-15.2c1-.43 1.93-.84 2.76-1.21 4.95-2.23 12.45-5.6 21.95-2 6.34 2.38 12 7.25 20.86 16 18.17 17.92 43 57.83 52.16 77.43 6.15 13.21 10.22 21.93 10.23 31.71 0 11.45-5.76 20.28-12.75 29.81-1.31 1.79-2.61 3.5-3.87 5.16-7.61 10-9.28 12.89-8.18 18.05 2.23 10.37 18.86 41.24 46.19 68.51s57.31 42.85 67.72 45.07c5.38 1.15 8.33-.59 18.65-8.47 1.48-1.13 3-2.3 4.59-3.47 10.66-7.93 19.08-13.54 30.26-13.54h.06c9.73 0 18.06 4.22 31.86 11.18 18 9.08 59.11 33.59 77.14 51.78 8.77 8.84 13.66 14.48 16.05 20.81 3.6 9.53.21 17-2 22-.37.83-.78 1.74-1.21 2.75a176.49 176.49 0 01-15.29 28.58c-10.63 15.9-21.4 28.21-39.38 36.58A67.42 67.42 0 01391 480z" />
            </svg></div>
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


    public function get_countries()
{
    $this->db->distinct();
    $this->db->select('country_name');
    $this->db->order_by('country_name', 'ASC');
    return $this->db->get('crcountry')->result();
}


    public function get_missing_count()
    {
        $this->load->database();
        $param=$this->input->post();
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
        
        $this->db->select('customer_id,maxtime,reminder');  
        $this->db->from('(SELECT customer_id, MAX(time) AS maxtime,reminder FROM reminder GROUP BY customer_id ORDER BY time DESC) as customer_id');  
        $this->db->where('customer_id.maxtime <',date('Y-m-d'));  
        $this->db->order_by('customer_id.maxtime', "ASC");
        $q=$this->db->get();
        $reminder_array=$q->result_array();
        $reminder=array();
        foreach ($reminder_array as $key => $val) {
            if (!array_key_exists($val['customer_id'], $reminder)) {
                $reminder[$val['customer_id']] = $val['customer_id'];
            }
            
        }
        $this->db->where($where);
        $this->db->where_in('customer_id',$reminder);
        $this->db->where("(status='' OR status ='Follow Up')");
        $q=$this->db->get('enquiry');
        $alldata=$q->result_array();
        echo count($alldata);
        
    }
    public function getMissingReminder()
    {
        $limit = 200;
        
        $this->load->database();
        $param=$this->input->post();
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
        
        if ($param['page'] > 1) {
            $start = ($param['page'] - 1) * $limit;
        } else {
            $start = 0;
        }
        $this->db->select('customer_id,maxtime,reminder');  
        $this->db->from('(SELECT customer_id, MAX(time) AS maxtime,reminder FROM reminder GROUP BY customer_id ORDER BY time DESC) as customer_id');  
        $this->db->where('customer_id.maxtime <',date('Y-m-d'));  
        $this->db->order_by('customer_id.maxtime', "ASC");
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
        $this->db->where_in('customer_id',$reminder);
        $this->db->where("(status='' OR status ='Follow Up')");
        $q=$this->db->get('enquiry');
        $alldata=$q->result_array();
        
        $this->db->limit($limit, $start);
        $this->db->where($where);
        $this->db->where_in('customer_id',$reminder);
        $this->db->where("(status='' OR status ='Follow Up')");
        $this->db->order_by("FIELD(customer_id, ".implode(',',$reminder).")");
        $q=$this->db->get('enquiry');
        
        
        //echo $this->db->last_query();
        $sorted_data_first=$q->result_array();
        $counts = count($sorted_data_first);
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
            $start_date=date('Y-m-d',strtotime($reminder_customer[$value['customer_id']]['time']));
            $last3_date=date('Y-m-d', strtotime('+4 days'));
            ?>
<div class="col-12 p-0 pull-left border-top b-l-r Flex"
    ondblclick="redirect(<?php echo $value['customer_id']?>, 'customer_detail')"
    style="background-color: #ebeefe;display: flex;">
    <div class="pull-left Flex-item font-blue text-ellipsis" style="width: 4%;padding-left: 8px;"><?php echo $i++?>
    </div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis position-relative" style="width: 10%;"
        onmouseover="show_tooltip(event,'<?php echo $value['enquiry_id']?>')" onmouseout="hide_tooltip(event)">
        <?php echo $value['enquiry_id']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis abc position-relative" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['name']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['name']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['corporate']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['corporate']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['number']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['number']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['email']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['email']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $user_data[$value['assigned_id']]['name']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $user_data[$value['assigned_id']]['name']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['reference']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['reference']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $value['destination']?>
    </div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $value['days']?></div>
    <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo 'Adult-'.$value['adult'].'<br>Child-'.$value['children'].'<br>Infant-'.$value['infant'].'<br>Total-'.($value['adult']+$value['children']+$value['infant']);?>')"
        onmouseout="hide_tooltip(event)">
        <?php echo 'A-'.$value['adult'].', C-'.$value['children'].', I-'.$value['infant']?></div>
    <div class="col-2 pull-left Flex-item font-blue text-ellipsis p-0 text-center" style="width: 8%;">
        <?php if ($value['start_date'] == '0000-00-00') {} else {echo date('d M Y', strtotime($value['start_date']));}?>
    </div>
    <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        <?php if (array_key_exists($value['customer_id'], $remark)) {?>
        onmouseover="show_tooltip(event,'<?php echo $remark[$value['customer_id']]['remark'] ?>')"
        onmouseout="hide_tooltip(event)" <?php }?>>
        <?php if (array_key_exists($value['customer_id'], $remark)) {echo $remark[$value['customer_id']]['remark'];}?>
    </div>
    <?php if($start_date<$today){$tool_data='No Reminder';}elseif($start_date==$today){$tool_data='Todays';}elseif($start_date>$today && $start_date<=$last3_date){$tool_data='Recent 3 days';}else{$tool_data='After 3 days';}?>
    <div class="col-2 position-relative pull-left Flex-item font-blue text-center text-ellipsis"
        onmouseover="show_tooltip(event,'<?php echo $tool_data; ?>')" onmouseout="hide_tooltip(event)"
        style="width: 5.5%;padding-top: 4px;"><svg xmlns="http://www.w3.org/2000/svg" class="ionicon"
            viewBox="0 0 512 512" fill="<?php if ($start_date < $today) {
                echo "white";
            } elseif ($start_date == $today) {
                echo "red";
            } elseif ($start_date > $today && $start_date <= $last3_date) {
                echo "orange";
            } else {
                echo "green";
            } ?>" width="20" style="<?php if ($start_date < $today) {echo "stroke: black;stroke-width: 50px;";}?>">
            <path d="M256 464c-114.69 0-208-93.31-208-208S141.31 48 256 48s208 93.31 208 208-93.31 208-208 208z" />
        </svg></div>
    <div class="col-2 d-flex justify-content-center pull-left Flex-item text-ellipsis" data-bs-toggle="modal"
        data-bs-target="#call_lead"
        onclick="$('#call_lead input[name=customer_id]').val('<?php echo $value['id'];?>'),$('#call_lead .modal-title').html('Do you want to call<br><span style=\'color:#eb1616\'><?php echo $customer_data[$value['customer_id']]['name']?></span>')"
        style="width:4%">
        <div class="colbutton"><svg xmlns="http://www.w3.org/2000/svg" width="15px" fill="#fff" viewBox="0 0 512 512">
                <path xmlns="http://www.w3.org/2000/svg"
                    d="M391 480c-19.52 0-46.94-7.06-88-30-49.93-28-88.55-53.85-138.21-103.38C116.91 298.77 93.61 267.79 61 208.45c-36.84-67-30.56-102.12-23.54-117.13C45.82 73.38 58.16 62.65 74.11 52a176.3 176.3 0 0128.64-15.2c1-.43 1.93-.84 2.76-1.21 4.95-2.23 12.45-5.6 21.95-2 6.34 2.38 12 7.25 20.86 16 18.17 17.92 43 57.83 52.16 77.43 6.15 13.21 10.22 21.93 10.23 31.71 0 11.45-5.76 20.28-12.75 29.81-1.31 1.79-2.61 3.5-3.87 5.16-7.61 10-9.28 12.89-8.18 18.05 2.23 10.37 18.86 41.24 46.19 68.51s57.31 42.85 67.72 45.07c5.38 1.15 8.33-.59 18.65-8.47 1.48-1.13 3-2.3 4.59-3.47 10.66-7.93 19.08-13.54 30.26-13.54h.06c9.73 0 18.06 4.22 31.86 11.18 18 9.08 59.11 33.59 77.14 51.78 8.77 8.84 13.66 14.48 16.05 20.81 3.6 9.53.21 17-2 22-.37.83-.78 1.74-1.21 2.75a176.49 176.49 0 01-15.29 28.58c-10.63 15.9-21.4 28.21-39.38 36.58A67.42 67.42 0 01391 480z" />
            </svg></div>
    </div>
</div>
<?php
        }
        echo ",,$". count($alldata);
        $this->load->model('Pageination');
        $second_count = $counts;
        echo ",,$".$this->Pageination->get_pageination(count($alldata), $limit, 2, $param['page'], $param['func'],$second_count);
    }
    

    public function presale_call()
    {
        $param=$this->input->get();
        $this->load->database();            
        $param['datetime'] = date('Y-m-d H:i');
        $this->db->insert('presale_call', $param);

        $this->db->where('id', $param['assigned_id']);
      	$q=$this->db->get('user');
        $user_data=$q->result_array();

        $rparam=[];
        $rparam['customer_id']=$param['customer_id'];
        $rparam['remark']='Called by - '.$user_data[0]['name'];
        $rparam['time']=date('Y-m-d H:i');
        $rparam['remarkby']=$param['assigned_id'];
        $rparam['remarkby_name']=$user_data[0]['name'];
        $this->db->insert('remark', $rparam);

        $this->db->where('id', $param['customer_id']);
      	$q = $this->db->get('customer');
      	$data = $q->result_array();
        
        
        $this->load->model('Notification');
        $this->Notification->send_notification($user_data[0]['fcm'],'Do you want to call '.$data[0]['number'],$data[0]['number']);
    }
    public function get_enquiry_detail()
    {
        $this->load->database();
        $param=$this->input->post();
        $this->db->where("id", $param['id']);
        $q=$this->db->get('enquiry');
        $data=$q->result_array();
        echo json_encode($data);
    }
    public function addEnquiry()
    {
        $param=$this->input->post();
        $this->load->database();            
        if ($param['id'] == '') {
            unset($param['id']);
            $q = $this->db->insert('enquiry', $param);
            if ($q) {
                echo "ok";
            }
        }
        else
        {
            $this->db->where('id', $param['id']);
            $q=$this->db->update('enquiry',$param);        
        }    
    }
    public function get_search_Enquiry_data()
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
        

        
        if ($param['page'] > 1) {
            $start = ($param['page'] - 1) * $limit;
        } else {
            $start = 0;
        }
        
        $this->db->select('customer_id,time,reminder');  
        $this->db->from('(SELECT customer_id, MAX(time) AS time,reminder FROM reminder GROUP BY customer_id ORDER BY time DESC) as customer_id');  
        $this->db->where('time >=',date('Y-m-d'));  
        $this->db->order_by('time', "ASC");
        $q=$this->db->get();
        $reminder_array=$q->result_array();
        
        $reminder_customer=$reminder=array();
        foreach ($reminder_array as $key => $val) {
            if (!array_key_exists($val['customer_id'], $reminder)) {
                $reminder[$val['customer_id']] = $val['customer_id'];
                $reminder_customer[$val['customer_id']] = $val;
            }
            
        }
        $sorted_data_first=[];
        $this->db->select('*')
            ->from('enquiry as e, customer as c')
            ->where($where);
        if(trim($param['type_customer'])!='')
        {
            $type_customer=explode(',',$param['type_customer']);
            $this->db->where_in('e.type_customer',$type_customer);
        }
          if (!empty($param['type'])) {
            $type = is_array($param['type']) ? $param['type'] : explode(',', $param['type']);
            $type = array_filter(array_map('trim', $type)); // clean it
            if (!empty($type)) {
                $this->db->where_in('e.type', $type);
            }
        }

        
        if($param['start_date']!='' && $param['end_date']=='')
        {
            $this->db->where("(DATE(e.datetime)='".date('Y-m-d',strtotime($param['start_date']))."')");
        }
        if($param['start_date']!='' && $param['end_date']!='')
        {
            $this->db->where("(DATE(e.datetime)>='".date('Y-m-d',strtotime($param['start_date']))."' AND DATE(e.datetime)<='".date('Y-m-d',strtotime($param['end_date']))."')");
        }
            
            
        $this->db->where("(e.status='' OR e.status ='Follow Up')");
        if($param['value']!='')
        {
            $this->db->where("(c.name LIKE '%" . $param['value'] . "%' escape '!' OR c.corporate LIKE '%" . $param['value'] . "%' escape '!' OR c.number LIKE '%" . $param['value'] . "%' escape '!' OR c.email LIKE '%" . $param['value'] . "%' escape '!' OR c.reference LIKE '%" . $param['value'] . "%' escape '!' OR e.start_date LIKE '%" . $param['value'] . "%' escape '!'OR e.status LIKE '%" . $param['value'] . "%' escape '!'OR e.remark LIKE '%" . $param['value'] . "%' escape '!' OR e.enquiry_id LIKE '%" . $param['value'] . "%' escape '!'  OR e.destination LIKE '%" . $param['value'] . "%' escape '!')");
        }
        // if(count($reminder)>0)
        // {
        //     $this->db->order_by("FIELD(customer_id, ".implode(',',$reminder).",customer_id)");
        // }
        $this->db->order_by('e.id','DESC');
        $this->db->where('e.customer_id = c.id');
        $q=$this->db->get();
        // echo $this->db->last_query();
        $filterdata=$q->result_array();
        

        
       

  

    // $this->db->select('e.*, c.*,  GROUP_CONCAT(co.country_name SEPARATOR ", ") as  country_name , GROUP_CONCAT(ci.country_name SEPARATOR ", ") as city_names')
    // ->from('enquiry as e')
    // ->join('customer as c', 'e.customer_id = c.id', 'inner')
    // ->join('crcountry as co', 'co.country_id = e.country_id', 'left')
    // ->join('crcity as ci', 'FIND_IN_SET(ci.city_id, e.city_id)', 'left', false) // Important: Use false to avoid escaping
    // ->where($where)
    // ->group_by('e.id');

    $this->db->select('
    e.*, 
    c.*,  
    GROUP_CONCAT(DISTINCT co.country_name SEPARATOR ", ") as country_name, 
    GROUP_CONCAT(DISTINCT ci.country_name SEPARATOR ", ") as city_names
    ')
    ->from('enquiry as e')
    ->join('customer as c', 'e.customer_id = c.id', 'inner')
    ->join('crcountry as co', 'FIND_IN_SET(co.country_id, e.country_id)', 'left', false) // ✅ FIXED
    ->join('crcity as ci', 'FIND_IN_SET(ci.city_id, e.city_id)', 'left', false)
    ->where($where)
    ->group_by('e.id');


        if(trim($param['type_customer'])!='')
        {
            $type_customer=explode(',',$param['type_customer']);
            $this->db->where_in('e.type_customer',$type_customer);
        }
         if (!empty($param['type'])) {
            $type = is_array($param['type']) ? $param['type'] : explode(',', $param['type']);
            $type = array_filter(array_map('trim', $type)); // clean it
            if (!empty($type)) {
                $this->db->where_in('e.type', $type);
            }
        }

            
        if($param['start_date']!='' && $param['end_date']=='')
        {
            $this->db->where("(DATE(e.datetime)='".date('Y-m-d',strtotime($param['start_date']))."')");
        }
        if($param['start_date']!='' && $param['end_date']!='')
        {
            $this->db->where("(DATE(e.datetime)>='".date('Y-m-d',strtotime($param['start_date']))."' AND DATE(e.datetime)<='".date('Y-m-d',strtotime($param['end_date']))."')");
        }   
        $this->db->where("(e.status='' OR e.status ='Follow Up')");
        if($param['value']!='')
        {
            $this->db->where("(c.name LIKE '%" . $param['value'] . "%' escape '!' OR c.corporate LIKE '%" . $param['value'] . "%' escape '!' OR c.number LIKE '%" . $param['value'] . "%' escape '!' OR c.email LIKE '%" . $param['value'] . "%' escape '!' OR c.reference LIKE '%" . $param['value'] . "%' escape '!' OR e.start_date LIKE '%" . $param['value'] . "%' escape '!'OR e.status LIKE '%" . $param['value'] . "%' escape '!'OR e.remark LIKE '%" . $param['value'] . "%' escape '!' OR e.enquiry_id LIKE '%" . $param['value'] . "%' escape '!'  OR e.destination LIKE '%" . $param['value'] . "%' escape '!')");
        }
        // if(count($reminder)>0)
        // {
        //     $this->db->order_by("FIELD(customer_id, ".implode(',',$reminder).",customer_id)");
        // }
        $this->db->order_by('e.id','DESC');
        $this->db->where('e.customer_id = c.id')
            ->limit($limit, $start);
        
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
<div class="col-12 p-0 pull-left border-top b-l-r Flex"
    ondblclick="redirect(<?php echo $value['customer_id']?>, 'customer_detail')"
    style="background-color: #ebeefe;display: flex;">
    <div class="pull-left Flex-item font-blue text-ellipsis" style="width: 4%;padding-left: 8px;"><?php echo $i++?>
    </div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis position-relative" style="width: 12%;"
        onmouseover="show_tooltip(event,'<?php echo $value['enquiry_id']?>')" onmouseout="hide_tooltip(event)">
        <?php echo $value['enquiry_id']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis abc position-relative" style="width: 10%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['name']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['name']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['corporate']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['corporate']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['number']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['number']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['email']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['email']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $user_data[$value['assigned_id']]['name']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $user_data[$value['assigned_id']]['name']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['reference']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['reference']?>
    </div>

    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;" onmouseover="show_tooltip(event, '<?php
         $tooltip = '';
         if (empty($value['destination'])) {
            // $parts = [];
            // if (!empty($value['country_name'])) $parts[] = $value['country_name'];
            // if (!empty($value['city_names'])) $parts[] = $value['city_names'];
            // $tooltip = implode(', ', $parts);
            $parts = [];
if (!empty($value['country_name'])) $parts[] = $value['country_name'];
if (!empty($value['city_names'])) $parts[] = $value['city_names'];
echo implode(', ', $parts);

         } else {
             $tooltip = $value['destination'];
         }
         echo htmlspecialchars($tooltip, ENT_QUOTES);
     ?>')" onmouseout="hide_tooltip(event)">
        <?php
        if (empty($value['destination'])) {
            $parts = [];
            if (!empty($value['country_name'])) $parts[] = $value['country_name'];
            if (!empty($value['city_names'])) $parts[] = $value['city_names'];
            echo implode(', ', $parts);
        } else {
            echo $value['destination'];
        }
    ?>
    </div>


    <!-- <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
     onmouseover="show_tooltip(event, '<?php
         $tooltip = '';
         if (empty($value['destination'])) {
             $parts = [];
             if (!empty($value['country_name'])) $parts[] = $value['country_name'];
             if (!empty($value['city_names'])) $parts[] = $value['city_names'];
             $tooltip = implode(', ', $parts);
         } else {
             $tooltip = $value['destination'];
         }
         echo htmlspecialchars($tooltip, ENT_QUOTES);
     ?>')"
     onmouseout="hide_tooltip(event)">
    <?php
        if (empty($value['destination'])) {
            $parts = [];
            if (!empty($value['country_name'])) $parts[] = $value['country_name'];
            if (!empty($value['city_names'])) $parts[] = $value['city_names'];
            echo implode(', ', $parts);
        } else {
            echo $value['destination'];
        }
    ?>
</div> -->


    <!-- <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
     onmouseover="show_tooltip(event, '<?php
         $tooltip = '';
         if (empty($value['destination'])) {
             $parts = [];
             if (!empty($value['country_name'])) $parts[] = $value['country_name'];
             if (!empty($value['city_name'])) $parts[] = $value['city_name'];
             $tooltip = implode(', ', $parts);
         } else {
             $tooltip = $value['destination'];
         }
         echo htmlspecialchars($tooltip, ENT_QUOTES);
     ?>')"
     onmouseout="hide_tooltip(event)">
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
</div> -->




    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $value['days']?></div>
    <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo 'Adult-'.$value['adult'].'<br>Child-'.$value['children'].'<br>Infant-'.$value['infant'].'<br>Total-'.($value['adult']+$value['children']+$value['infant']);?>')"
        onmouseout="hide_tooltip(event)">
        <?php echo 'A-'.$value['adult'].', C-'.$value['children'].', I-'.$value['infant']?></div>
    <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 8%;">
        <?php if ($value['start_date'] == '0000-00-00') {} else {echo date('d M Y', strtotime($value['start_date']));}?>
    </div>
    <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        <?php if (array_key_exists($value['customer_id'], $remark)) {?>
        onmouseover="show_tooltip(event,'<?php echo $remark[$value['customer_id']]['remark'] ?>')"
        onmouseout="hide_tooltip(event)" <?php }?>>
        <?php if (array_key_exists($value['customer_id'], $remark)) {echo $remark[$value['customer_id']]['remark'];}?>
    </div>
    <?php if($start_date<$today){$tool_data='No Reminder';}elseif($start_date==$today){$tool_data='Todays';}elseif($start_date>$today && $start_date<=$last3_date){$tool_data='Recent 3 days';}else{$tool_data='After 3 days';}?>
    <div class="col-2 position-relative pull-left Flex-item font-blue text-center text-ellipsis"
        onmouseover="show_tooltip(event,'<?php echo $tool_data; ?>')" onmouseout="hide_tooltip(event)"
        style="width: 5.5%;padding-top: 4px;"><svg xmlns="http://www.w3.org/2000/svg" class="ionicon"
            viewBox="0 0 512 512" fill="<?php if ($start_date < $today) {
                echo "white";
            } elseif ($start_date == $today) {
                echo "red";
            } elseif ($start_date > $today && $start_date <= $last3_date) {
                echo "orange";
            } else {
                echo "green";
            } ?>" width="20" style="<?php if ($start_date < $today) {echo "stroke: black;stroke-width: 50px;";}?>">
            <path d="M256 464c-114.69 0-208-93.31-208-208S141.31 48 256 48s208 93.31 208 208-93.31 208-208 208z" />
        </svg></div>
</div>
<?php
        }
        echo ",,$". count($sorted_data_first);
        $this->load->model('Pageination');
        $second_count = $counts;
        echo ",,$".$this->Pageination->get_pageination(count($filterdata), $limit, 2, $param['page'], $param['func'],$second_count);
    }
    
    

    public function get_search_Missing_Reminder()
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
            

        
        if ($param['page'] > 1) {
            $start = ($param['page'] - 1) * $limit;
        } else {
            $start = 0;
        }
        $this->db->select('customer_id.reminderby,customer_id.customer_id,customer_id.time,customer_id.reminder');  
        $this->db->from('(SELECT reminderby,customer_id, MAX(time) AS time,reminder FROM reminder GROUP BY customer_id ORDER BY time DESC) as customer_id');  
        if($param['select_staff_id']!='' && $param['select_staff_id']!="null")
        {
            $this->db->where('customer_id.reminderby',$param['select_staff_id']);
        }
        
        if($param['missed']!='yes')
        {
            $threeday= date('Y-m-d',strtotime('+4 day'));
            $this->db->where('time >=',date('Y-m-d'));  
            $this->db->where('time <',$threeday);  
            $this->db->order_by('time', "ASC");
        }
        else
        {
            $this->db->where('time <',date('Y-m-d'));  
            $this->db->order_by('time', "DESC");
        }
        
        // $this->db->where('maxtime <',date('Y-m-d'));  
        // $this->db->order_by('maxtime', "ASC");
        $q=$this->db->get();
        // echo $this->db->last_query();
        $reminder_array=$q->result_array();
        
        $reminder_customer=$reminder=array();
        foreach ($reminder_array as $key => $val) {
            if (!array_key_exists($val['customer_id'], $reminder)) {
                $reminder[$val['customer_id']] = $val['customer_id'];
                $reminder_customer[$val['customer_id']] = $val;
            }
            
        }
        if(count($reminder)>0)
        {
            $this->db->select('*,count(*) OVER() AS total_count')
                ->from('enquiry as e, customer as c')
                ->where($where);
            if(count($reminder)>0)
            {
                $this->db->where_in('customer_id',$reminder);
            }
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
                
                
            // $this->db->where("(e.status='' OR e.status ='Follow Up')");
            $this->db->where("(e.status='' OR e.status='Follow Up' OR e.status='Confirmed')");

            if($param['value']!='')
            {
                $this->db->where("(c.name LIKE '%" . $param['value'] . "%' escape '!' OR c.corporate LIKE '%" . $param['value'] . "%' escape '!' OR c.number LIKE '%" . $param['value'] . "%' escape '!' OR c.email LIKE '%" . $param['value'] . "%' escape '!' OR c.reference LIKE '%" . $param['value'] . "%' escape '!' OR e.start_date LIKE '%" . $param['value'] . "%' escape '!'OR e.status LIKE '%" . $param['value'] . "%' escape '!'OR e.remark LIKE '%" . $param['value'] . "%' escape '!' OR e.enquiry_id LIKE '%" . $param['value'] . "%' escape '!'  OR e.destination LIKE '%" . $param['value'] . "%' escape '!')");
            }
            if(count($reminder)>0)
            {
                $this->db->order_by("FIELD(customer_id, ".implode(',',$reminder).")");
            }
            $this->db->order_by('e.id','DESC');
            $this->db->where('e.customer_id = c.id')
                ->limit($limit, $start);
            
            $q=$this->db->get();
            
            // echo $this->db->last_query();
            $sorted_data_first=$q->result_array();
            $counts = count($sorted_data_first);
            
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
                $start_date=date('Y-m-d',strtotime($reminder_customer[$value['customer_id']]['time']));
                $last3_date=date('Y-m-d', strtotime('+4 days'));
                ?>
<div class="col-12 p-0 pull-left border-top b-l-r Flex"
    ondblclick="redirect(<?php echo $value['customer_id']?>, 'customer_detail')"
    style="background-color: #ebeefe;display: flex;">
    <div class="pull-left Flex-item font-blue text-ellipsis" style="width: 4%;padding-left: 8px;"><?php echo $i++?>
    </div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis position-relative" style="width: 12%;"
        onmouseover="show_tooltip(event,'<?php echo $value['enquiry_id']?>')" onmouseout="hide_tooltip(event)">
        <?php echo $value['enquiry_id']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis abc position-relative" style="width: 10%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['name']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['name']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['corporate']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['corporate']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['number']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['number']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['email']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['email']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $user_data[$value['assigned_id']]['name']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $user_data[$value['assigned_id']]['name']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['reference']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $customer_data[$value['customer_id']]['reference']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $value['destination']?>
    </div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 6%;"><?php echo $value['days']?></div>
    <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        onmouseover="show_tooltip(event,'<?php echo 'Adult-'.$value['adult'].'<br>Child-'.$value['children'].'<br>Infant-'.$value['infant'].'<br>Total-'.($value['adult']+$value['children']+$value['infant']);?>')"
        onmouseout="hide_tooltip(event)">
        <?php echo 'A-'.$value['adult'].', C-'.$value['children'].', I-'.$value['infant']?></div>
    <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 8%;">
        <?php if ($value['start_date'] == '0000-00-00') {} else {echo date('d M Y', strtotime($value['start_date']));}?>
    </div>
    <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 7%;"
        <?php if (array_key_exists($value['customer_id'], $remark)) {?>
        onmouseover="show_tooltip(event,'<?php echo $remark[$value['customer_id']]['remark'] ?>')"
        onmouseout="hide_tooltip(event)" <?php }?>>
        <?php if (array_key_exists($value['customer_id'], $remark)) {echo $remark[$value['customer_id']]['remark'];}?>
    </div>
    <?php if($start_date<$today){$tool_data='No Reminder';}elseif($start_date==$today){$tool_data='Todays';}elseif($start_date>$today && $start_date<=$last3_date){$tool_data='Recent 3 days';}else{$tool_data='After 3 days';}?>
    <div class="col-2 position-relative pull-left Flex-item font-blue text-center text-ellipsis"
        onmouseover="show_tooltip(event,'<?php echo $tool_data; ?>')" onmouseout="hide_tooltip(event)"
        style="width: 5.5%;padding-top: 4px;"><svg xmlns="http://www.w3.org/2000/svg" class="ionicon"
            viewBox="0 0 512 512" fill="
                <?php 
                if($param['missed']=='yes')
                {
                    echo "black";
                }
                if ($start_date < $today) {
                    echo "white";
                } elseif ($start_date == $today) {
                    echo "red";
                } elseif ($start_date > $today && $start_date <= $last3_date) {
                    echo "orange";
                } else {
                    echo "green";
                } ?>" width="20" style="<?php if ($start_date < $today) {echo "stroke: black;stroke-width: 50px;";}?>">
            <path d="M256 464c-114.69 0-208-93.31-208-208S141.31 48 256 48s208 93.31 208 208-93.31 208-208 208z" />
        </svg></div>
</div>
<?php
            }
            echo ",,$"; 
            if(count($sorted_data_first)>0){echo $sorted_data_first[0]['total_count'];}else{echo 0;}
            $this->load->model('Pageination');
            $second_count = $sorted_data_first[0]['total_count'];
            echo ",,$".$this->Pageination->get_pageination($sorted_data_first[0]['total_count'], $limit, 2, $param['page'], $param['func'],$second_count);
        }
        else
        {
            echo ",,$0"; 
            $this->load->model('Pageination');
            echo ",,$".$this->Pageination->get_pageination(0, $limit, 2, $param['page'], $param['func'],'');
        }
    }
    public function get_search_dob()
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
            

        
        if ($param['page'] > 1) {
            $start = ($param['page'] - 1) * $limit;
        } else {
            $start = 0;
        }
        
        $today = date('Y-m-d');
        $last7_date=date('Y-m-d', strtotime('+7 days'));
        $last3_date=date('Y-m-d', strtotime('+4 days'));
        $this->db->select('*')
            ->from('enquiry as e,customer as c')
            ->where($where);
            $this->db->where("((((month(dob)>=".date('m',strtotime($today))." AND DAY(dob)>=".date('d',strtotime($today)).") AND (month(dob)>0 AND DAY(dob)>0)) OR ((month(anniversary)>=".date('m',strtotime($today))." AND DAY(anniversary)>=".date('d',strtotime($today)).") AND  (month(anniversary)>0 AND DAY(anniversary)>0))) AND (((month(dob)<=".date('m',strtotime($last7_date))." AND DAY(dob)<=".date('d',strtotime($last7_date)).") AND  (month(dob)>0 AND DAY(dob)>0)) OR ((month(anniversary)<=".date('m',strtotime($last7_date))." AND DAY(anniversary)<=".date('d',strtotime($last7_date)).") AND  (month(anniversary)>0 AND DAY(anniversary)>0))))");
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
        $this->db->order_by("SUBSTRING(DATE_FORMAT(`c`.`dob`, '%d'), 1, 1), SUBSTRING(DATE_FORMAT(`c`.`anniversary`, '%d'), 1, 1),SUBSTRING(DATE_FORMAT(`c`.`dob`, '%M'), 1, 1), SUBSTRING(DATE_FORMAT(`c`.`anniversary`, '%M'), 1, 1) ASC");
        $this->db->where('e.customer_id = c.id');
        
        $q=$this->db->get();
        // echo $this->db->last_query();
        $alldata=$q->result_array();
        $y=date('Y');
        $this->db->select("*,c.dob as sdob,c.anniversary as sanniversary, CASE WHEN (UNIX_TIMESTAMP(REPLACE(`c`.`dob`, DATE_FORMAT(`c`.`dob`, '%Y'), '2023')) > UNIX_TIMESTAMP(REPLACE(`c`.`anniversary`, DATE_FORMAT(`c`.`anniversary`, '%Y'), '2023')) && UNIX_TIMESTAMP(REPLACE(`c`.`dob`, DATE_FORMAT(`c`.`dob`, '%Y'), '2023')) > UNIX_TIMESTAMP('2023-10-11')) or UNIX_TIMESTAMP(REPLACE(`c`.`anniversary`, DATE_FORMAT(`c`.`anniversary`, '%Y'), '2023')) > UNIX_TIMESTAMP(REPLACE(`c`.`dob`, DATE_FORMAT(`c`.`dob`, '%Y'), '2023') && UNIX_TIMESTAMP(REPLACE(`c`.`anniversary`, DATE_FORMAT(`c`.`anniversary`, '%Y'), '2023')) > UNIX_TIMESTAMP('2023-10-11')) THEN IF(UNIX_TIMESTAMP(REPLACE(`c`.`dob`, DATE_FORMAT(`c`.`dob`, '%Y'), '2023')) < UNIX_TIMESTAMP(REPLACE(`c`.`anniversary`, DATE_FORMAT(`c`.`anniversary`, '%Y'), '2023')) && UNIX_TIMESTAMP(REPLACE(`c`.`dob`, DATE_FORMAT(`c`.`dob`, '%Y'), '2023')) > UNIX_TIMESTAMP('2023-10-11'), REPLACE(`c`.`dob`, DATE_FORMAT(`c`.`dob`, '%Y'), '2023'), IF(UNIX_TIMESTAMP(REPLACE(`c`.`anniversary`, DATE_FORMAT(`c`.`anniversary`, '%Y'), '2023')) < UNIX_TIMESTAMP(REPLACE(`c`.`dob`, DATE_FORMAT(`c`.`dob`, '%Y'), '2023')) && UNIX_TIMESTAMP(REPLACE(`c`.`anniversary`, DATE_FORMAT(`c`.`anniversary`, '%Y'), '2023')) > UNIX_TIMESTAMP('2023-10-11'), REPLACE(`c`.`anniversary`, DATE_FORMAT(`c`.`anniversary`, '%Y'), '2023'), REPLACE(`c`.`dob`, DATE_FORMAT(`c`.`dob`, '%Y'), '2023'))) END as adob")
            ->from('enquiry as e,customer as c')
            ->where($where);
            $this->db->where("((((month(dob)>=".date('m',strtotime($today))." AND DAY(dob)>=".date('d',strtotime($today)).") AND (month(dob)>0 AND DAY(dob)>0)) OR ((month(anniversary)>=".date('m',strtotime($today))." AND DAY(anniversary)>=".date('d',strtotime($today)).") AND  (month(anniversary)>0 AND DAY(anniversary)>0))) AND (((month(dob)<=".date('m',strtotime($last7_date))." AND DAY(dob)<=".date('d',strtotime($last7_date)).") AND  (month(dob)>0 AND DAY(dob)>0)) OR ((month(anniversary)<=".date('m',strtotime($last7_date))." AND DAY(anniversary)<=".date('d',strtotime($last7_date)).") AND  (month(anniversary)>0 AND DAY(anniversary)>0))))");
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
        $this->db->order_by("adob ASC");
        // $this->db->order_by("SUBSTRING(DATE_FORMAT(`c`.`dob`, '%d'), 1, 2), SUBSTRING(DATE_FORMAT(`c`.`anniversary`, '%d'), 1, 2) ASC");
        $this->db->where('e.customer_id = c.id');
        $this->db->limit($limit, $start);
         
        $q=$this->db->get();
        
        // echo $this->db->last_query();
        $sorted_data_first=$q->result_array();
        $counts = count($sorted_data_first);
        
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
        $today = date('Y-m-d');
        foreach($sorted_data_first as $key=>$value)
        {
            $type='';
            
            if($value['sdob']!='0000-00-00' && date('m-d', strtotime($value['sdob']))==date('m-d', strtotime($today)))
            {
                $type="DOB";
                $dob=date('d M Y',strtotime($value['sdob']));
                $toolTip=$type. ' : '.$dob;
                $start_date=date('Y',strtotime($today)).'-'.date('m-d',strtotime($dob));
                $Reminder_data='DOB';
                if($value['sanniversary']==$today)
                {
                    if($type!='')
                    {
                        $type.="/Anniversary";
                        $dob.=' / '.date('d M Y',strtotime($value['sanniversary']));
                        $toolTip.='<br>Anniversary : '.date('d M Y',strtotime($value['sanniversary']));
                        $start_date=date('Y',strtotime($today)).'-'.date('m-d',strtotime($value['sanniversary']));
                        $Reminder_data='DOB && Anniversary';
                    }
                }
            }
            else if($value['sanniversary']!='0000-00-00' && date('m-d', strtotime($value['sanniversary']))==date('m-d', strtotime($today)))
            {
                $type="Anniversary";
                $dob=date('d M Y',strtotime($value['sanniversary']));
                $toolTip=$type. ' : '.date('d M Y',strtotime($value['sanniversary']));
                $start_date=date('Y',strtotime($today)).'-'.date('m-d',strtotime($dob));
                $Reminder_data='Anniversary';
            }            
            else if(date('m-d', strtotime($value['sdob']))<=date('m-d', strtotime($last7_date)) && date('m-d', strtotime($value['sdob']))>date('m-d', strtotime($today)))
            {
                $type="DOB";
                $dob=date('d M Y',strtotime($value['sdob']));
                $toolTip=$type. ' : '.$dob;
                $start_date=date('Y',strtotime($today)).'-'.date('m-d',strtotime($dob));
                $Reminder_data='DOB';
                if(date('m-d', strtotime($value['sanniversary']))<=date('m-d', strtotime($last7_date)) && date('m-d', strtotime($value['sanniversary']))>date('m-d', strtotime($today)))
                {
                    if($type!='')
                    {
                        $type.="/Anniversary";
                        $dob.=' / '.date('d M Y',strtotime($value['sanniversary']));
                        $toolTip.='<br>Anniversary : '.date('d M Y',strtotime($value['sanniversary']));
                        $start_date=date('Y',strtotime($today)).'-'.date('m-d',strtotime($value['sanniversary']));
                        $Reminder_data='DOB && Anniversary';
                    }
                }
            }
            else if(strtotime(date('Y-').date('m-d', strtotime($value['sanniversary'])))<=strtotime(date('Y-').date('m-d', strtotime($last7_date))) && strtotime(date('Y-').date('m-d', strtotime($value['sanniversary'])))>strtotime($today))
            {
                if($type!='')
                {
                    $type.="/Anniversary";
                    $dob.=' / '.date('d M Y',strtotime($value['sanniversary']));
                    $toolTip.='<br>Anniversary : '.date('d M Y',strtotime($value['sanniversary']));
                    $start_date=date('Y',strtotime($today)).'-'.date('m-d',strtotime($value['sanniversary']));
                    $Reminder_data='DOB && Anniversary';
                }
                else
                {
                    $type="Anniversary";
                    $dob=date('d M Y',strtotime($value['sanniversary']));
                    $toolTip=$type. ' : '.date('d M Y',strtotime($value['sdob']));
                    $start_date=date('Y',strtotime($today)).'-'.date('m-d',strtotime($dob));
                    $Reminder_data='Anniversary';
                }
            }
            
            ?>
<div class="col-12 p-0 pull-left border-top b-l-r Flex"
    ondblclick="redirect(<?php echo $value['customer_id']?>, 'customer_detail')"
    style="background-color: #ebeefe;display: flex;">
    <div class="pull-left Flex-item font-blue text-ellipsis" style="width: 4%;padding-left: 8px;"><?php echo $i++?>
    </div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis abc position-relative" style="width: 20%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['name']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $value['name']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 10%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['number']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $value['number']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 10%;"
        onmouseover="show_tooltip(event,'<?php echo $customer_data[$value['customer_id']]['email']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $value['email']?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 12%;"
        onmouseover="show_tooltip(event,'<?php echo $user_data[$value['assigned_id']]['name']?>')"
        onmouseout="hide_tooltip(event)"><?php echo $user_data[$value['assigned_id']]['name']?></div>
    <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 15%;"
        onmouseover="show_tooltip(event,'<?php echo $toolTip?>')" onmouseout="hide_tooltip(event)"><?php echo $dob?>
    </div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 12%;"
        onmouseover="show_tooltip(event,'<?php echo $type?>')" onmouseout="hide_tooltip(event)"><?php echo $type?></div>
    <?php if($start_date<$today){$tool_data='No Reminder';}elseif($start_date==$today){$tool_data='Todays<br>'.$Reminder_data;}elseif($start_date>$today && $start_date<=$last3_date){$tool_data='Recent 3 days<br>'.$Reminder_data;}else{$tool_data='After 3 days<br>'.$Reminder_data;}?>
    <div class="col-2 position-relative pull-left Flex-item font-blue text-center text-ellipsis"
        onmouseover="show_tooltip(event,'<?php echo $tool_data; ?>')" onmouseout="hide_tooltip(event)"
        style="width: 5.5%;padding-top: 4px;"><svg xmlns="http://www.w3.org/2000/svg" class="ionicon"
            viewBox="0 0 512 512" fill="
                <?php 
                if($param['missed']=='yes')
                {
                    echo "black";
                }
                if ($start_date < $today) {
                    echo "white";
                } elseif ($start_date == $today) {
                    echo "red";
                } elseif ($start_date > $today && $start_date <= $last3_date) {
                    echo "orange";
                } else {
                    echo "green";
                } ?>" width="20" style="<?php if ($start_date < $today) {echo "stroke: black;stroke-width: 50px;";}?>">
            <path d="M256 464c-114.69 0-208-93.31-208-208S141.31 48 256 48s208 93.31 208 208-93.31 208-208 208z" />
        </svg>
    </div>
</div>
<?php
        }
        echo ",,$". count($alldata);
        $this->load->model('Pageination');
        $second_count = $counts;
        echo ",,$".$this->Pageination->get_pageination(count($alldata), $limit, 2, $param['page'], $param['func'],$second_count);
    }
    public function website_lead()
    {
        $param=$this->input->get();
        $this->load->database();            
        $param['datetime'] = date('Y-m-d H:i');
        $this->db->insert('website_lead', $param);
    }

    
    public function get_countrieslistupdate($type = null) {
        $this->load->database(); 
        $this->db->distinct();
        $this->db->select('country_id, country_name');
        $this->db->order_by('country_name', 'ASC');
        $countries = $this->db->get('crcountry')->result();
    
        // Manually filter by name as a fallback (example logic)
        if ($type == 'domestic') {
            $countries = array_filter($countries, function($c) {
                return in_array($c->country_name, ['India']); // Add other domestic countries if needed
            });
        } elseif ($type == 'international') {
            $countries = array_filter($countries, function($c) {
                return !in_array($c->country_name, ['India']); // Or reverse logic
            });
        }
       
        return array_values($countries); // reindex array
    }
    
    
    // public function get_cities_by_countryupdate($country_id) {
    //     $this->load->database(); 
    //     $this->db->select('city_id, country_name');
    //     $this->db->where('country_id', $country_id);
    //     $this->db->order_by('country_name', 'ASC');
    //     return $this->db->get('crcity')->result();
    // }

    public function get_cities_by_countryupdate($country_ids = []) {
    $this->load->database(); 
    if (empty($country_ids)) return [];

    $this->db->select('city_id, country_name, country_id'); // use actual city name column
    $this->db->from('crcity');
    $this->db->where_in('country_id', $country_ids); // multiple country support
    $this->db->order_by('country_name', 'ASC');
    return $this->db->get()->result();
}
  



// public function get_cities_by_country($country_ids = [])
// {
//     $this->load->database();

//     if (!is_array($country_ids)) {
//         $country_ids = [$country_ids];
//     }

//     $this->db->select('*');
//     $this->db->from('crcity');
//     $this->db->where_in('country_id', $country_ids);
//     $this->db->order_by('country_name', 'ASC'); // Not country_name

//     return $this->db->get()->result();
// }


public function get_cities_by_country($country_ids = [])
{
    $this->load->database();

    if (!is_array($country_ids)) {
        $country_ids = [$country_ids];
    }

    $this->db->select('ci.city_id, ci.country_id, ci.country_name AS city_name, co.country_name');
    $this->db->from('crcity as ci');
    $this->db->join('crcountry as co', 'co.country_id = ci.country_id', 'left');
    $this->db->where_in('ci.country_id', $country_ids);
    $this->db->order_by('co.country_name', 'ASC');

   
    return $this->db->get()->result();
}


// public function get_cities_by_country($country_ids = [])
// {
//     $this->load->database();

//     if (!is_array($country_ids)) {
//         $country_ids = [$country_ids];
//     } 
//     print_r($country_ids);
//     $this->db->select('ci.city_id, ci.country_id, ci.country_name AS city_name, co.country_name');
//     $this->db->from('crcity as ci');
//     $this->db->join('crcountry as co', 'co.country_id = ci.country_id', 'left');
//     $this->db->where_in('ci.country_id', $country_ids);
//     $this->db->order_by('co.country_name', 'ASC');

//     // ✅ Debug SQL query
//     $this->db->get(); // run the query first
//     echo $this->db->last_query(); // print the generated SQL

//     exit; // stop further execution if needed
// }







// public function get_cities_by_country($country_ids = [])
// {
//     $this->load->database();

//     if (!is_array($country_ids)) {
//         $country_ids = array($country_ids);
//     }

//     $this->db->select('*');
//     $this->db->from('crcity');
//     $this->db->where_in('country_id', $country_ids);
//     $this->db->order_by('country_name', 'ASC');

//     return $this->db->get()->result();
// }

public function get_countries_by_type($type, $country_ids = [])
{
    $this->load->database();
    $db_type = ($type === 'domestic') ? 'Dom' : 'Int';

    $this->db->select('country_id, country_name');
    $this->db->from('crcountry');
    $this->db->where('country_type', $db_type);

    // ✅ Add where_in condition if $country_ids is provided
    if (!empty($country_ids) && is_array($country_ids)) {
        $this->db->where_in('country_id', $country_ids);
    }

    $this->db->order_by('country_name', 'ASC');

    return $this->db->get()->result();
}



// public function get_countries_by_type($type)
// {
//     $this->load->database();
//     $db_type = ($type === 'domestic') ? 'Dom' : 'Int';

//     return $this->db
       
//         ->select('country_id, country_name')
//         ->from('crcountry')
//         ->where('country_type', $db_type)
//         ->order_by('country_name', 'ASC')
//         ->get()
//         ->result();
// }

// public function get_cities_by_country($country_id)
// {
//     $this->load->database(); 
//     return $this->db
     
//         ->select('city_name')
//         ->from('crcity')
//         ->where('country_id', $country_id)
//         ->order_by('city_name', 'ASC')
//         ->get()
//         ->result();
// }

}

?>