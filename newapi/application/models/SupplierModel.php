<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS");
class SupplierModel extends CI_model
{
    public function get_Supplier_data()
    {
        $param=$this->input->get();
        $this->load->database();
        $this->db->where("id",$param['id']);
        $q=$this->db->get('supplier');
        echo json_encode($q->result_array());
    }
    
   public function get_search_Supplier_data() {
    $limit = 200;

    $this->load->database();
    $param = $this->input->get();
    $where = array();

    // Pagination
    if ($param['page'] > 1) {
        $start = ($param['page'] - 1) * $limit;
    } else {
        $start = 0;
    }
    $i = $start + 1;

    // Apply limits
    $this->db->limit($limit, $start);

    // Name search filter
    if (!empty($param['value'])) {
        $this->db->like('name', $param['value'], 'both');
    }

    // Country filter
    if (!empty($param['selected_country'])) {
        $this->db->like('country_name', $param['selected_country'], 'both');
    }

    // Fetch data
    $cr = $this->db->get('supplier');
    //echo $this->db->last_query();
    $supplier = $cr->result_array();

    // Render data
    foreach ($supplier as $key => $value) {
        $person_names = '';
        $person_name = json_decode($value['person_name'], true);
        $country_name = explode(",", $value['country_name']);
        $j = 0;

        foreach ($person_name as $pkey => $pvalue) {
            $product = '';
            if (array_key_exists('product', $pvalue)) {
                $product = implode(',', $pvalue['product']) . ' - ';
            }

            if ($person_names == '') {
                $person_names .= $country_name[$j] . ' - ' . $product . $pvalue['name'] . ' - ' . $pvalue['number'];
            } else {
                $person_names .= "<br>" . $country_name[$j] . ' - ' . $product . $pvalue['name'] . ' - ' . $pvalue['number'];
            }
            $j++;
        }

        // Render rows
        ?>
<div class="col-12 p-0 pull-left border-top b-l-r Flex"
    ondblclick="redirect(<?php echo $value['id'] ?>, 'supplier_detail')"
    style="background-color: #ebeefe; display: flex;">
    <div class="pull-left Flex-item font-blue" style="width: 8%; padding-left: 8px;"><?php echo $i; ?></div>
    <div class="col-2 pull-left Flex-item font-blue text-ellipsis" style="width: 15%;"
        onmouseover="show_tooltip(event,$(this).html())" onmouseout="hide_tooltip(event)">
        <?php echo $value['name']; ?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 15%;"
        onmouseover="show_tooltip(event,$(this).html())" onmouseout="hide_tooltip(event)"><?php echo $value['name_poc']; ?>
    </div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 10%;"
        onmouseover="show_tooltip(event,$(this).html())" onmouseout="hide_tooltip(event)">
        <?php echo $value['contact_no']; ?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 12%;"
        onmouseover="show_tooltip(event,$(this).html())" onmouseout="hide_tooltip(event)"><?php echo $value['email']; ?>
    </div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 8%;"
        onmouseover="show_tooltip(event,$(this).html())" onmouseout="hide_tooltip(event)">
        <?php echo $value['country_name']; ?></div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 8%;"
        onmouseover="show_tooltip(event,$(this).html())" onmouseout="hide_tooltip(event)"><?php echo $value['categories']; ?>
    </div>
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 8%;"
        onmouseover="show_tooltip(event,$(this).html())" onmouseout="hide_tooltip(event)"><?php echo $value['remark']; ?>
    </div>

    <!-- <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 15%;" onmouseover="show_tooltip(event,$(this).html())" onmouseout="hide_tooltip(event)">
                <div class="custom-radio" style="width: 15px;margin-right:5px;margin-top:6px; height: 15px; border-radius: 50%; background-color: <?php echo ($value['status'] == 'Active') ? 'green' : ($value['status'] == 'In-Active' ? 'red' : ''); ?>; display: inline-block;"></div>
                <?php echo $value['status']; ?>
            </div> -->

            
    <div class="col-1 pull-left Flex-item font-blue text-ellipsis" style="width: 15%;"
        onmouseover="show_tooltip(event,$(this).html())" onmouseout="hide_tooltip(event)">

        <?php
        // Mapping status values
        $display_status = ($value['status'] == 'Preferable') ? 'Active' : (($value['status'] == 'Non-Preferable') ? 'In-Active' : $value['status']);
        $bg_color = ($display_status == 'Active') ? 'green' : (($display_status == 'In-Active') ? 'red' : '');
    ?>

        <div class="custom-radio" style="width: 15px; margin-right:5px; margin-top:6px; height: 15px; 
         border-radius: 50%; background-color: <?php echo $bg_color; ?>; display: inline-block;">
        </div>

        <?php echo $display_status; ?>
    </div>
</div>
</div>
<?php
        $i++;
    }

    // Pagination
    echo ",,$" . count($supplier);
    $this->load->model('Pageination');
    echo ",,$" . $this->Pageination->get_pageination(count($supplier), $limit, 2, $param['page'], $param['func'], "");
}


    public function add_Supplier()
    {
        $this->load->database();
        $param=$this->input->get();
        $data=array();
        if($param['id']=='')
        {
            $param['datetime']=date('Y-m-d');
            $q=$this->db->insert('supplier',$param);    
            $data["success"]=true;
            $data["message"]="Supplier added to list";
            echo json_encode($data); 
        }
        else
        {
            $this->db->where('id',$param['id']);
            unset($param['id']);
            $q=$this->db->update('supplier',$param);     
            $data["success"]=true;
            $data["message"]="Supplier updated in list";
            echo json_encode($data); 
        }
    }
    
    public function get_categories() {
        $this->db->select('name');
        $query = $this->db->get('categories'); // Get categories
        return $query->result_array(); // Return as an associative array
    }

}

?>