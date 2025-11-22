<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS");
class DashboardModel extends CI_model
{
    public function get_Dashboard_data()
    {
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
        if($param['select_staff_id']!='' && $param['select_staff_id']!="null")
        {
            $where['e.assigned_id'] = $param['select_staff_id'];            
        }
        $jsondata=array();
        $this->db->where('type NOT LIKE ','admin');
        $this->db->where('type NOT LIKE ','sales');
        $this->db->where('type NOT LIKE ','account');
        if($param['select_staff_id']!='' && $param['select_staff_id']!="null")
        {
            $this->db->where('id',$param['select_staff_id']);
        }
        
        $cr=$this->db->get('user');
        // echo $this->db->last_query();

        $user=$cr->result_array();
        $user_data = array();
        
        foreach($user as $key => $value)
        {
            $user_data[$value['id']] = $value;
            if(!array_key_exists('assigned',$jsondata))
            {
                $jsondata['assigned']=array();
            }
            if(!array_key_exists($value['name'],$jsondata['assigned']))
            {
                $jsondata['assigned'][$value['name']]=0;
            }
                
            if(array_key_exists('assigned',$jsondata))
            {
                $jsondata['contribiution'][$value['name']]['followup']=0;
                $jsondata['contribiution'][$value['name']]['sale']=0;
                $jsondata['contribiution'][$value['name']]['Confirmed']=0;
                $jsondata['contribiution'][$value['name']]['Travelled']=0;
                $jsondata['contribiution'][$value['name']]['Missed']=0;

                $jsondata['contribiution'][$value['name']]['total']['followup']=0;
                $jsondata['contribiution'][$value['name']]['total']['Confirmed']=0;
                $jsondata['contribiution'][$value['name']]['total']['Travelled']=0;
            }
        
        }
        $this->db->select('customer_id.reminderby,customer_id.customer_id,customer_id.time,customer_id.reminder');
        $this->db->from('(SELECT r.reminderby, r.customer_id, r.time, r.reminder FROM reminder r INNER JOIN (SELECT customer_id, MAX(time) AS max_time FROM reminder GROUP BY customer_id) m ON r.customer_id = m.customer_id AND r.time = m.max_time) as customer_id,enquiry as e,customer as c');
        $this->db->where('customer_id.time <',date('Y-m-d'));
        if($param['select_staff_id']!='' && $param['select_staff_id']!="null")
        {
            $this->db->where('e.assigned_id',$param['select_staff_id']);
        }
        $this->db->where('c.id=e.customer_id');
        $this->db->where('customer_id.customer_id=e.customer_id');
        $this->db->where('customer_id.reminderby=e.assigned_id');
        $this->db->where("(e.status='' OR e.status ='Follow Up')");
        $this->db->order_by('customer_id.time', "DESC");
        $q=$this->db->get();
        //echo $this->db->last_query();
        $missed=$q->result_array();
        
        foreach($missed as $key =>$value)
        {
            $jsondata['contribiution'][$user_data[$value['reminderby']]['name']]['Missed']=$jsondata['contribiution'][$user_data[$value['reminderby']]['name']]['Missed']+1;
        }

        $this->db->select('*');  
        $this->db->from('enquiry as e,customer as c');  
        $this->db->where("(e.status='' OR e.status ='Follow Up') AND `e`.`customer_id` = `c`.`id`");
        $this->db->where($where);  
        if($param['type']!='')
        {
            $type=explode(',',$param['type']);
            $this->db->where_in('e.type',$type);
        }
        if(trim($param['type_customer'])!='')
        {
            $type_customer=explode(',',$param['type_customer']);
            $this->db->where_in('e.type_customer',$type_customer);
        }
        $q=$this->db->get(); 
        //echo $this->db->last_query();
        $followup=$q->result_array();
        foreach($followup as $key =>$value)
        {
            if(!array_key_exists('assigned',$jsondata))
            {
                $jsondata['assigned']=array();
            }
            if(array_key_exists($user_data[$value['assigned_id']]['name'],$jsondata['assigned']))
            {
                // $jsondata['assigned'][$user_data[$value['assigned_id']]['name']]=$jsondata['assigned'][$user_data[$value['assigned_id']]['name']]+1;
                $jsondata['contribiution'][$user_data[$value['assigned_id']]['name']]['followup']=$jsondata['contribiution'][$user_data[$value['assigned_id']]['name']]['followup']+1;
            }
            else
            {
                // $jsondata['assigned'][$user_data[$value['assigned_id']]['name']]=1;
                $jsondata['contribiution'][$user_data[$value['assigned_id']]['name']]['followup']=1;
            }
            
        }

        // $start_date=$param['start_date'];
        // if ($start_date != '') 
        // {
        //     if($start_date == 'blank')
        //     {
        //         $where['e.datetime'] = $start_date;                     
        //     }
        //     else
        //     {
                
        //         $start_date=explode('-',$start_date);
        //         if($start_date[0]!='' &&  $start_date[0]!='All')
        //         {
        //             $where['MONTH(e.datetime)'] = $start_date[0];                
        //         }
        //         if($start_date[1]!='' && $start_date[1]!='All')
        //         {
        //             $where['YEAR(e.datetime)'] = $start_date[1];                
        //         }
        //     }
        // }
        
        $this->db->select('e.adult,e.children,e.infant,e.datetime,e.id,e.enquiry_id,e.status,e.assigned_id,i.amount,i.booking_date,i.no_of_pax');
        $this->db->from('(SELECT enquiry_id, MIN(checkin) AS mcheckin, MAX(checkout) AS mcheckout, SUM(product_amount) as amount, MAX(booking_date) as booking_date, SUM(no_of_pax) as no_of_pax FROM itinerary WHERE is_deleted = 0 GROUP BY enquiry_id) as i,enquiry as e,customer as c');
        $this->db->where('e.id=i.enquiry_id');  
        $this->db->where('e.customer_id=c.id');  
        $this->db->where("(e.status!='DropOut' and e.status!='')");
        $this->db->where($where);  
        if($param['start_date']!='' && $param['end_date']=='')
        {
            $this->db->where("DATE(i.booking_date) >= ", date('Y-m-d', strtotime($param['start_date'])));
        }
        if($param['start_date']!='' && $param['end_date']!='')
        {
            $this->db->where("(i.booking_date>='".date('Y-m-d',strtotime($param['start_date']))."' AND i.booking_date<='".date('Y-m-d',strtotime($param['end_date']))."')");
        }
        if($param['year']!='')
        {
            $this->db->where("(YEAR(date(i.booking_date)) ='".$param['year']."')");
        }
        $this->db->group_by('e.id');
        if($param['type']!='')
        {
            $type=explode(',',$param['type']);
            $this->db->where_in('e.type',$type);
        }
        if(trim($param['type_customer'])!='')
        {
            $type_customer=explode(',',$param['type_customer']);
            $this->db->where_in('e.type_customer',$type_customer);
        }
        
        $q=$this->db->get();
        //echo $this->db->last_query();
        $data=$q->result_array();
        $travelled=$total_sale=$book_date_count=$total=$totalTravelledSale=$totalConfirmedSale=0;
        foreach($data as $key =>$value)
        {
            if($value['status']=='Travelled')
            {
                $travelled++;
            }
            if($value['status']=='Travelled' || $value['status']=='Confirmed' )
            {
                $total++;
            }
            
            $total_sale+=$value['amount'];
            if(!array_key_exists('assigned',$jsondata))
            {
                $jsondata['assigned']=array();
            }
            if($value['status']=='' || $value['status']=="Follow Up")
            {
                $value['status']='FollowUp';
            }
            
         

            if(array_key_exists($user_data[$value['assigned_id']]['name'],$jsondata['assigned']))
            {
                //print_r($value);
                 $jsondata['assigned'][$user_data[$value['assigned_id']]['name']]=$jsondata['assigned'][$user_data[$value['assigned_id']]['name']]+1;
                 $jsondata['contribiution'][$user_data[$value['assigned_id']]['name']]['sale']=$jsondata['contribiution'][$user_data[$value['assigned_id']]['name']]['sale']+$value['amount'];
                 $jsondata['contribiution'][$user_data[$value['assigned_id']]['name']][$value['status']]=$jsondata['contribiution'][$user_data[$value['assigned_id']]['name']][$value['status']]+1;
                 $jsondata['contribiution'][$user_data[$value['assigned_id']]['name']]['total'][$value['status']]=$jsondata['contribiution'][$user_data[$value['assigned_id']]['name']]['total'][$value['status']]+$value['amount'];
                // $jsondata['contribiution'][$user_data[$value['assigned_id']]['name']]['totalPAX'][$value['status']]=$jsondata['contribiution'][$user_data[$value['assigned_id']]['name']]['totalPAX'][$value['status']]+($value['no_of_pax']);
                $jsondata['contribiution'][$user_data[$value['assigned_id']]['name']]['totalPAX'][$value['status']] =
    (int)$jsondata['contribiution'][$user_data[$value['assigned_id']]['name']]['totalPAX'][$value['status']] + (int)$value['no_of_pax'];
                $month=date('m',strtotime($value['booking_date']));
                $year=date('Y',strtotime($value['booking_date']));
                 $jsondata['contribiution'][$user_data[$value['assigned_id']]['name']]['monthly'][$year][$month][$value['status']]['total']=$jsondata['contribiution'][$user_data[$value['assigned_id']]['name']]['monthly'][$year][$month][$value['status']]['total']+1;
                 $jsondata['contribiution'][$user_data[$value['assigned_id']]['name']]['monthly'][$year][$month][$value['status']]['sale']=$jsondata['contribiution'][$user_data[$value['assigned_id']]['name']]['monthly'][$year][$month][$value['status']]['sale']+$value['amount'];
                 $jsondata['contribiution'][$user_data[$value['assigned_id']]['name']]['monthly'][$year][$month][$value['status']]['pax']=$jsondata['contribiution'][$user_data[$value['assigned_id']]['name']]['monthly'][$year][$month][$value['status']]['pax']+($value['adult']+$value['children']); 
            }
            else
            {
                $jsondata['assigned'][$user_data[$value['assigned_id']]['name']]=1;
                $jsondata['contribiution'][$user_data[$value['assigned_id']]['name']]['sale']=$value['amount'];
                $jsondata['contribiution'][$user_data[$value['assigned_id']]['name']][$value['status']]=$jsondata['contribiution'][$user_data[$value['assigned_id']]['name']][$value['status']]+1;
                $jsondata['contribiution'][$user_data[$value['assigned_id']]['name']]['total'][$value['status']]=$jsondata['contribiution'][$user_data[$value['assigned_id']]['name']]['total'][$value['status']]+$value['amount'];
                $jsondata['contribiution'][$user_data[$value['assigned_id']]['name']]['totalPAX'][$value['status']]=$jsondata['contribiution'][$user_data[$value['assigned_id']]['name']]['totalPAX'][$value['status']]+($value['no_of_pax']);
                $month=date('m',strtotime($value['booking_date']));
                $year=date('Y',strtotime($value['booking_date']));
                $jsondata['contribiution'][$user_data[$value['assigned_id']]['name']]['monthly'][$year][$month][$value['status']]['total']=$jsondata['contribiution'][$user_data[$value['assigned_id']]['name']]['monthly'][$year][$month][$value['status']]['total']+1;
                $jsondata['contribiution'][$user_data[$value['assigned_id']]['name']]['monthly'][$year][$month][$value['status']]['sale']=$jsondata['contribiution'][$user_data[$value['assigned_id']]['name']]['monthly'][$year][$month][$value['status']]['sale']+$value['amount'];
                $jsondata['contribiution'][$user_data[$value['assigned_id']]['name']]['monthly'][$year][$month][$value['status']]['pax']=$jsondata['contribiution'][$user_data[$value['assigned_id']]['name']]['monthly'][$year][$month][$value['status']]['pax']+($value['adult']+$value['children']); 
            }
        }

        // print_r($book_date_count);die;
        
        $start_date = $param['start_date'];
        $end_date = $param['end_date'];
        
        // Convert the date to YYYY-MM-DD format
        $start_date = date('Y-m-d', strtotime($start_date));
        $end_date = date('Y-m-d', strtotime($end_date));
        
       
        $this->db->select('COUNT(i.booking_date) AS total_bookings');
        $this->db->from('itinerary i');
        $this->db->join('enquiry e', 'e.id = i.enquiry_id', 'left');
        $this->db->where('i.booking_date IS NOT NULL');

        // if($param['start_date']!='' && $param['end_date']=='')
        // {
        //     $this->db->where('DATE(datetime)', $start_date);
        // }
        // if($param['start_date']!='' && $param['end_date']!='')
        // {
        //     $this->db->where('DATE(e.datetime) >=', $start_date);
        //     $this->db->where('DATE(e.datetime) <=', $end_date);
        // }
        if($param['start_date']!='' && $param['end_date']=='')
        {
            $this->db->where("DATE(i.booking_date) >= ", date('Y-m-d', strtotime($param['start_date'])));
        }
        if($param['start_date']!='' && $param['end_date']!='')
        {
            $this->db->where("(i.booking_date>='".date('Y-m-d',strtotime($param['start_date']))."' AND i.booking_date<='".date('Y-m-d',strtotime($param['end_date']))."')");
        }
        
        
        // Execute the query
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        // Get the result
        if ($query->num_rows() > 0) {
            $result = $query->row(); // Fetch first row of result
            $bookcount = $result->total_bookings; // Get total bookings count
        } else {
            $bookcount = 0; 
        }
        $jsondata['total']=$total;
        $jsondata['book_date_count']=$bookcount;
        $jsondata['travelled']=$travelled;
        $jsondata['total_sale']=$total_sale;
        //print_r($jsondata);die;
        echo json_encode($jsondata);
         
    }
      
    public function get_total_count($param) {
        $this->db->select('COUNT(e.id) AS total_count');  
        $this->db->from('(SELECT enquiry_id, MIN(checkin) AS mcheckin, MAX(checkout) AS mcheckout, SUM(product_amount) AS amount FROM itinerary GROUP BY enquiry_id) AS i');  
        $this->db->join('enquiry AS e', 'e.id = i.enquiry_id', 'inner');  
        $this->db->join('customer AS c', 'e.customer_id = c.id', 'inner');  
        $this->db->where("(e.status != 'DropOut')");
    
       
        $this->db->where('DATE(e.datetime) >=', '2025-01-01');  
        $this->db->where('DATE(e.datetime) <=', date('Y-m-d'));
        $this->db->where($where);  
        if($param['start_date']!='' && $param['end_date']=='')
        {
            $this->db->where("DATE(e.datetime) >= ", date('Y-m-d', strtotime($param['start_date'])));
        }  
    
      
        $query = $this->db->get();
        echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $result = $query->row(); 
            return $result->total_count;
        } else {
            return 0; 
        }
    }


    
    
}

?>
