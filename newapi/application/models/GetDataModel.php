<?php
header('Access-Control-Allow-Origin: *');
class GetDataModel extends CI_model
{
    public function get_list()
    {
        $param=$this->input->get();
        // echo "<pre>";
        // print_r($param);
      	$this->load->database();
        $datas=[];
        if($param['sort']=='')
        {
          $this->db->order_by('name','ASC');  
        }
        else
        {
          $this->db->order_by($param['sort'],'ASC');  
        }
        $table=$param['table'];
        unset($param['table']);
        unset($param['sort']);
        $groups=[];
      	if(array_key_exists('group',$param))
        {
          $q = $this->db->get($param['group']);
          $data = $q->result_array();
          foreach($data as $key=>$value)
          {
            $groups[$value['id']]=$value;
          }
        }
        
        foreach($param as $key=>$value)
        {
          if($key!='count' && $key!='group' && $key!='group_by' && $key!='json_extractor' && $key!='data_merge')
          {
            if(str_contains($value,','))
            {
              $values=explode(',',$value);
              $this->db->where_in($key,$values);
            }
            else
            {
              $this->db->where($key,$value);
            }
          }
          if($key=='json_extractor')
          {
            $json_extractor=json_decode($value,true);
            $json_extractor_string='';
            foreach($json_extractor as $jsonKey=>$jsonvalue)
            {
              if($json_extractor_string!='')
              {
                $json_extractor_string.=' AND ';
              }
              $i=0;
              $json_extractor_string.='(';
              foreach($jsonvalue as $jsoninnerKey=>$jsoninnervalue)
              {
                if($i>0)
                {
                  $json_extractor_string.=' OR ';
                }
                $json_extractor_string.="JSON_EXTRACT(".$jsoninnervalue['field'].",'$.\"".$jsonKey."\".\"".$jsoninnervalue['param']."\"')";
                $i++;
              }
              $json_extractor_string.=')';
            }
            // echo $json_extractor_string;
            $this->db->where($json_extractor_string." IS NOT NULL");
          }
        }
      	$q = $this->db->get($table);
        // echo $this->db->last_query();
      	$data = $q->result_array();
        foreach($data as $key=>$value)
      	{
          if(array_key_exists('group',$param))
          {
            $datas[$groups[$value['filter_id']]['name']][$value['id']]=$value;
          }
          else {
            $datas[$value['id']]=$value;
          }
        }
        
        if(array_key_exists('count',$param))
        {
          foreach(json_decode($param['count'],true) as $keys=>$values)
          {            
            foreach($values['param'] as $pkey=>$pvalue)
            {
              if($pkey!='count' && $pkey!='group' && $pkey!='group_by' && $pkey!='json_extractor' && $pkey!='data_merge')
              {
                if(str_contains($pvalue,','))
                {
                  $psvalues=explode(',',$pvalue);
                  $this->db->where_in($pkey,$psvalues);
                }
                else
                {
                  $this->db->where($pkey,$pvalue);
                }
              }
            }    
            $q = $this->db->get($values['table']);
            // echo $this->db->last_query();
            $data = $q->result_array();
            $newData=array();
            foreach($data as $key=>$value)
            {
              $newData[$value['id']]=$value;
            }
            // echo "<pre>";
            // print_r($datas);
            foreach($data as $key=>$value)
            {
              if(array_key_exists($value[$values['connect']],$datas))
              {        
                $datas[$value[$values['connect']]][$values['table']][$key]=$value;
              }
            }  
          }
        }
      	
        if(array_key_exists('count',$param))
        {
          foreach(json_decode($param['count'],true) as $keys=>$values)
          { 
            if(array_key_exists('count',$values))
            {
              foreach($values['count'] as $ckeys=>$cvalues)
              {                   
                foreach($cvalues['param'] as $pkey=>$pvalue)
                {
                  if($pkey!='count' && $pkey!='group' && $pkey!='group_by' && $pkey!='json_extractor' && $pkey!='data_merge')
                  {
                    if(str_contains($pvalue,','))
                    {
                      $psvalues=explode(',',$pvalue);
                      $this->db->where_in($pkey,$psvalues);
                    }
                    else
                    {
                      $this->db->where($pkey,$pvalue);
                    }
                  }
                }    
                $q = $this->db->get($cvalues['table']);
                // echo $this->db->last_query();
                $newdatas = $q->result_array();
                foreach($newdatas as $key=>$value)
                {
                  $newdata[$value['id']]=$value;
                }
                // echo "<pre>";
                // print_r($newdata);
                foreach($data as $key=>$value)
                {
                  foreach($newdata as $nkey=>$nvalue)
                  {
                    if(array_key_exists($value[$values['connect']],$datas))
                    {        
              
                      // if(array_key_exists($value[$cvalues['connect']],$datas))
                      //  {                      
                        $datas[$value[$values['connect']]][$values['table']][$key][$cvalues['table']]=$newdata[$value[$cvalues['connect']]];
                      // }
                    }
                  }
                }
              }
            }
          }
        }
      	
        
        
        echo json_encode($datas);
    }

    public function get_person_list()
    {
        $param=$this->input->get();
        // echo "<pre>";
        // print_r($param);
      	$this->load->database();
        $datas=[];
        if($param['sort']=='')
        {
          $this->db->order_by('name','ASC');  
        }
        else
        {
          $this->db->order_by($param['sort'],'ASC');  
        }
        $table=$param['table'];
        unset($param['table']);
        unset($param['sort']);
        $groups=[];
      	if(array_key_exists('group',$param))
        {
          $q = $this->db->get($param['group']);
          $data = $q->result_array();
          foreach($data as $key=>$value)
          {
            $groups[$value['id']]=$value;
          }
        }
        
        foreach($param as $key=>$value)
        {
          if($key!='count' && $key!='group' && $key!='group_by' && $key!='json_extractor' && $key!='data_merge')
          {
            if(str_contains($value,','))
            {
              $values=explode(',',$value);
              $this->db->where_in($key,$values);
            }
            else
            {
              $this->db->where($key,$value);
            }
          }
          if($key=='json_extractor')
          {
            $json_extractor=json_decode($value,true);
            $json_extractor_string='';
            foreach($json_extractor as $jsonKey=>$jsonvalue)
            {
              if($json_extractor_string!='')
              {
                $json_extractor_string.=' AND ';
              }
              $i=0;
              $json_extractor_string.='(';
              foreach($jsonvalue as $jsoninnerKey=>$jsoninnervalue)
              {
                if($i>0)
                {
                  $json_extractor_string.=' OR ';
                }
                $json_extractor_string.="JSON_EXTRACT(".$jsoninnervalue['field'].",'$.\"".$jsonKey."\".\"".$jsoninnervalue['param']."\"')";
                $i++;
              }
              $json_extractor_string.=')';
            }
            // echo $json_extractor_string;
            $this->db->where($json_extractor_string." IS NOT NULL");
          }
        }
      	$q = $this->db->get($table);
        // echo $this->db->last_query();
      	$data = $q->result_array();
        foreach($data as $key=>$value)
      	{
          if(array_key_exists('group',$param))
          {
            $datas[$groups[$value['filter_id']]['name']][$value['id']]=$value;
          }
          else {
            $datas[$value['id']]=$value;
          }
        }
        
        if(array_key_exists('count',$param))
        {
          foreach(json_decode($param['count'],true) as $keys=>$values)
          {            
            foreach($values['param'] as $pkey=>$pvalue)
            {
              if($pkey!='count' && $pkey!='group' && $pkey!='group_by' && $pkey!='json_extractor' && $pkey!='data_merge')
              {
                if(str_contains($pvalue,','))
                {
                  $psvalues=explode(',',$pvalue);
                  $this->db->where_in($pkey,$psvalues);
                }
                else
                {
                  $this->db->where($pkey,$pvalue);
                }
              }
            }    
            $q = $this->db->get($values['table']);
            // echo $this->db->last_query();
            $data = $q->result_array();
            $newData=array();
            foreach($data as $key=>$value)
            {
              $newData[$value['id']]=$value;
            }
            // echo "<pre>";
            // print_r($datas);
            foreach($data as $key=>$value)
            {
              if(array_key_exists($value[$values['connect']],$datas))
              {        
                $datas[$value[$values['connect']]][$values['table']][$key]=$value;
              }
            }  
          }
        }
      	
        if(array_key_exists('count',$param))
        {
          foreach(json_decode($param['count'],true) as $keys=>$values)
          { 
            if(array_key_exists('count',$values))
            {
              foreach($values['count'] as $ckeys=>$cvalues)
              {                   
                foreach($cvalues['param'] as $pkey=>$pvalue)
                {
                  if($pkey!='count' && $pkey!='group' && $pkey!='group_by' && $pkey!='json_extractor' && $pkey!='data_merge')
                  {
                    if(str_contains($pvalue,','))
                    {
                      $psvalues=explode(',',$pvalue);
                      $this->db->where_in($pkey,$psvalues);
                    }
                    else
                    {
                      $this->db->where($pkey,$pvalue);
                    }
                  }
                }    
                $q = $this->db->get($cvalues['table']);
                // echo $this->db->last_query();
                $newdatas = $q->result_array();
                foreach($newdatas as $key=>$value)
                {
                  $newdata[$value['id']]=$value;
                }
                // echo "<pre>";
                // print_r($newdata);
                foreach($data as $key=>$value)
                {
                  foreach($newdata as $nkey=>$nvalue)
                  {
                    if(array_key_exists($value[$values['connect']],$datas))
                    {        
              
                      // if(array_key_exists($value[$cvalues['connect']],$datas))
                      //  {                      
                        $datas[$value[$values['connect']]][$values['table']][$key][$cvalues['table']]=$newdata[$value[$cvalues['connect']]];
                      // }
                    }
                  }
                }
              }
            }
          }
        }
      	
        
        
        echo json_encode($datas);
    }

    public function insert_data()
    {
      $param=$this->input->post();
      
      //print_r($param);die;
      // $this->db->where('name', $param['name']);
      // $query = $this->db->get('supplier');
      // if ($query->num_rows() > 0) {
      //     echo json_encode([["success" => false, "message" => "Supplier name already exists."]]);
      //     return;
      // }
      //print_r($param['name']);
      $table_param=[];
      $this->load->database();
      $table=$param['table'];
      $table_list= $this->db->list_fields($table);
    //   if ($table == 'supplier') {
    //     if (!isset($param['name']) || empty($param['name'])) {
    //         echo json_encode([["success" => false, "message" => "Supplier name is required."]]);
    //         return;
    //     }
    
    //     $this->db->where('name', $param['name']);
    //     $query = $this->db->get('supplier');
    //     if ($query->num_rows() > 0) {
    //         echo json_encode([["success" => false, "message" => "Supplier name already exists."]]);
    //         return;
    //     }
    // }
      foreach($table_list as $key=>$value)
      {
        if(array_key_exists($value,$param))
        {
          if(preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $param[$value]) || preg_match('/^(\d{1})\/(\d{1})\/(\d{4})$/', $param[$value]) || preg_match('/^(\d{1})\/(\d{2})\/(\d{4})$/', $param[$value]) || preg_match('/^(\d{2})\/(\d{1})\/(\d{4})$/', $param[$value]) || preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $param[$value]) || preg_match('/^(\d{1})-(\d{1})-(\d{4})$/', $param[$value]) || preg_match('/^(\d{1})-(\d{2})-(\d{4})$/', $param[$value]) || preg_match('/^(\d{2})-(\d{1})-(\d{4})$/', $param[$value]))
          {
            $table_param[$value]=date('Y/m/d',strtotime(str_replace('/','-',$param[$value])));
          }
          else
          {
            if(strpos($param[$value], ";base64,") >0) 
            {
              if (is_object(json_decode($param[$value]))) 
              {             
                  $base=json_decode($param[$value],true);
                  if(array_key_exists('image',$base))
                  {
                    foreach($base['image'] as $imagekey=>$imagevalue)
                    {
                      if(strpos($imagevalue, ";base64,") >0) 
                      {
                        $this->load->model('Upload');
                        $image = str_replace('plusssss', '+', $imagevalue);
                        $folder='';
                        $image_name=$imagekey;
                        if($param['create_folder']=='yes')
                        {
                          $folder=$param['name'];
                          $param['folder']=$folder;
                        }
                        else if($param['create_folder']!='')
                        {
                          $folder=$param['create_folder'];
                          $param['folder']=$folder;
                        }
                        
                        if($value!='image')
                        {
                          $image_name=$value.$imagekey;
                        }
                        
                        $table_param[$value]['image'][$imagekey] = $this->Upload->upload_image($image, $image_name,$folder);
                        
                      }
                      else
                      {
                        $table_param[$value]['image'][$imagekey] = $imagevalue;
                      }
                    }
                  }
                  else
                  {
                    foreach($base['pdf'] as $imagekey=>$imagevalue)
                    {
                      if(strpos($imagevalue, ";base64,") >0) 
                      {
                        $this->load->model('Upload');
                        $image = str_replace('plusssss', '+', $imagevalue);
                        $table_param[$value]['pdf'][$imagekey] = $this->Upload->upload_image($image, rand(0, 1000000) . '_'.$value,'pdf');
                      }
                      else
                      {
                        $table_param[$value]['pdf'][$imagekey] = $imagevalue;
                      }
                    }
                    
                  }
                  $table_param[$value]=json_encode($table_param[$value],JSON_FORCE_OBJECT);
                      
              }
              else if(strpos($param[$value], ";base64,") >0) 
              {
                $this->load->model('Upload');
                $image = str_replace('plusssss', '+', $param[$value]);
                $folder='';
                if($param['create_folder']=='yes')
                {
                  $folder=$param['name'];
                  $param['folder']=$folder;
                }
                else if($param['create_folder']!='')
                {
                  $folder=$param['create_folder'];
                  $param['folder']=$folder;
                }
                
                $table_param[$value] = $this->Upload->upload_image($image, rand(0, 1000000) . '_'.$value,$folder);
                
              }
              
            }
            else
            { 
              if($table=='barcode' && $value=='name')
              {
                $this->load->library('zend');
                $this->zend->load('Zend/Barcode');
                $param['code']=str_replace(' ','',$param['name']);
                $imageResource = Zend_Barcode::factory('code128', 'image', array('text'=>$param['code']), array())->draw();
                ob_start();
                imagepng($imageResource);
                $data = ob_get_clean();
                $this->load->model('Upload');
                $image = str_replace('plusssss', '+', $imagevalue);
                $folder='';
                if($param['create_folder']!='')
                {
                  $folder=$param['create_folder'];
                }
                $table_param['image']['image'][0] = $this->Upload->upload_image('data:image/png;base64,'.base64_encode($data), $param['name'],$folder);                    
                $table_param['image']=json_encode($table_param['image'],JSON_FORCE_OBJECT);
                $table_param[$value]=$param[$value];
              }
              else
                $table_param[$value]=$param[$value];
            }
          }
        }
      }
      //  print_r($table_param);
      if($param['id']!='')
      {
        $enquiry_id=$param['id'];
        if($table=='supplier')
        {
          $this->db->where('id',$enquiry_id);
          $q = $this->db->get('supplier');
          $newdatas = $q->result_array();
          $ratings=$newdatas[0]['rating'];
          if($ratings!=$param['rating'])
          {
            $supplier_param=array();
            $supplier_param['time']=date('Y-m-d H:s');
            $supplier_param['type']='supplier';
            $supplier_param['remark']="Rating Updated: ".$param['rating']."<br>".$param['rating_remark'];
            $supplier_param['remarkby']=$param['reminderassigned_id'];
            $supplier_param['customer_id']=$enquiry_id;
            $q=$this->db->insert("remark",$supplier_param);
            // echo $this->db->last_query();
          }
        }
        $this->db->where('id',$param['id']);
        unset($table_param['id']);
        $q=$this->db->update($table,$table_param);
        // echo $this->db->last_query();
        $data = array(array("success" => true, "message" => "Data updated successfuly","id"=>$param['id']));      
        
      }
      else 
      {

        $table_param['datetime']=date('Y-m-d H:s');
        $q=$this->db->insert($table,$table_param);
        $id=$this->db->insert_id();
        $data = array(array("success" => true, "message" => "Data added successfuly","id"=>$id));      
        if($table=='supplier')
        {
          $supplier_param=array();
          $supplier_param['time']=date('Y-m-d H:s');
          $supplier_param['type']='supplier';
          $supplier_param['remark']="Rating : ".$param['rating']."<br>".$param['rating_remark'];
          $supplier_param['remarkby']=$param['assigned_id'];
          $supplier_param['customer_id']=$id;
          $q=$this->db->insert("remark",$supplier_param);
            
        }
      }
      echo json_encode($data); 
    }


    public function delete_data()
    {
        $param=$this->input->get();
        $this->load->database();
        $table=$param['table'];
        unset($param['table']);

        $this->db->where('id',$param['id']);
        $q=$this->db->get($table);
        $data = $q->result_array();
        foreach($data as $key=>$value)
        {
          foreach($value as $vkey=>$vvalue)
          {
            if($vkey=='folder')
            {
              $dir=BASEPATH.'uploads/'.$vvalue;
              if (is_dir($dir))
              {
                $dir = strtr($dir, '/', '\\');
                $files = array_diff(scandir($dir), array('.', '..')); 
                foreach ($files as $file) { 
                    if(is_dir("$dir/$file"))
                    {
                      rmdir("$dir/$file"); 
                    }
                    else {
                      unlink("$dir/$file"); 
                    }
                }
                rmdir($dir); 
              }
           }
          }
        }

        $this->db->where('id',$param['id']);
        $this->db->delete($table,$param);
        $data = array(array("success" => true, "message" => "Data deleted successfuly"));      
        echo json_encode($data); 
    }
    function create_Image_Json()
    {
      $param=$this->input->get();
      $this->load->database();
      $image=array();
      $this->load->helper('url');
      $dir=BASEPATH.'uploads/'.$param['folder'];              
      if (is_dir($dir))
      {
        $dir = strtr($dir, '/', '\\');
        $files = array_diff(scandir($dir), array('.', '..')); 
        foreach ($files as $file) { 
            if(!is_dir("$dir/$file"))
            {
              $name=str_replace('.png','',$file);
              if(strpos($file,"purchase_image") === false)
              {
                $image['image'][$name]=base_url().'system/uploads/'.$param['folder'].'/'.$file;
              }
            }
        }
      }
      
      ksort( $image['image']);
      $table_param['front_image']=0;
      $table_param['image']=json_encode($image,JSON_FORCE_OBJECT);
      $this->db->where('id',$param['id']);
      $q=$this->db->update('product',$table_param);
            
    }
}
?>


