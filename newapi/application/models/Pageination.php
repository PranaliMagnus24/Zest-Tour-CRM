<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Pageination extends CI_model
{
    public function get_pageination($total_pages,$limit,$adjacents,$page,$fun,$param='')
    {
        if ($page == 0 || $page=='') 
        {
        $page = 1;					
        }
        $prev = $page - 1;							
        $next = $page + 1;							
        $lastpage = ceil($total_pages/$limit);		
        $lpm1 = $lastpage - 1;						
        $pagination = "";
        if($lastpage > 1)
        {	
            $pagination .= "<div class=\"pagination  text-center float-start bg-white m-auto\">";
            if ($page > 1) 
                $pagination.= "<a href=\"#\" onclick=\"$fun('$prev',$param)\"><button type='button' class='btn  btn-outline-primary btn-default waves-effect  m-l-5 m-r-5' style='border-radius: 10px 0px 0px 10px;'>Previous</button></a>";
            else
                $pagination.= "<span class=\"disabled\"><button type='button' class='btn  btn-outline-primary btn-default disabled waves-effect  m-l-5 m-r-5' style='border-radius: 10px 0px 0px 10px;'>Previous</button></span>";	
            if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
            {	
                for ($counter = 1; $counter <= $lastpage; $counter++)
                {
                    if ($counter == $page)
                        $pagination.= "<span data-onclick=\"$fun('$counter',$param)\" class=\"current\"><button type='button' class='btn border-radius-0 btn-outline-primary btn-primary waves-effect  m-l-5 m-r-5'>$counter</button></span>";
                    else
                        $pagination.= "<a href=\"#\" onclick=\"$fun('$counter',$param)\"><button type='button' class='btn border-radius-0 btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>$counter</button></a>";					
                }
            }
            else if($lastpage > 5 + ($adjacents))	
            {
                if($page < 1 + ($adjacents * 2))		
                {
                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
                    {
                        if ($counter == $page)
                            $pagination.= "<span data-onclick=\"$fun('$counter',$param)\" class=\"current\"><button type='button' class='btn border-radius-0 btn-outline-primary btn-primary  waves-effect  m-l-5 m-r-5'>$counter</button></span>";
                        else
                            $pagination.= "<a href=\"#\" onclick=\"$fun('$counter',$param)\"><button type='button' class='btn  border-radius-0 btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>$counter</button></a>";					
                    }
                    $pagination.= "<button type='button' class='btn btn-outline-primary border-radius-0 border-radius-0 btn-default waves-effect  m-l-5 m-r-5'>...</button>";
                    $pagination.= "<a href=\"#\" onclick=\"$fun('$lpm1',$param)\"><button type='button' class='btn border-radius-0 btn-outline-primary border-radius-0 btn-default waves-effect  m-l-5 m-r-5'>$lpm1</button></a>";
                    $pagination.= "<a href=\"#\" onclick=\"$fun('$lastpage',$param)\"><button type='button' class='btn border-radius-0 btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>$lastpage</button></a>";		
                }
                else if($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
                {
                    $pagination.= "<a href=\"#\" onclick=\"$fun('1',$param)\"><button type='button' class='btn border-radius-0 btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>1</button></a>";
                    $pagination.= "<a href=\"#\" onclick=\"$fun('2',$param)\"><button type='button' class='btn border-radius-0 btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>2</button></a>";
                    $pagination.= "<button type='button' class='btn border-radius-0 btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>...</button>";
                    for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
                    {
                        if ($counter == $page)
                            $pagination.= "<span data-onclick=\"$fun('$counter',$param)\" class=\"current\"><button type='button' class='btn border-radius-0 btn-outline-primary btn-primary waves-effect  m-l-5 m-r-5'>$counter</button></span>";
                        else
                            $pagination.= "<a href=\"#\" onclick=\"$fun('$counter',$param)\"><button type='button' class='btn border-radius-0 btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>$counter</button></a>";					
                    }
                    $pagination.= "<button type='button' class='btn border-radius-0 btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>...</button>";
                    $pagination.= "<a href=\"#\" onclick=\"$fun('$lpm1',$param)\"><button type='button' class='btn border-radius-0 btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>$lpm1</button></a>";
                    $pagination.= "<a href=\"#\" onclick=\"$fun('$lastpage',$param)\"><button type='button' class='btn border-radius-0 btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>$lastpage</button></a>";		
                }
                else
                {
                    $pagination.= "<a href=\"#\" onclick=\"$fun('1',$param)\"><button type='button' class='btn border-radius-0 btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>1</button></a>";
                    $pagination.= "<a href=\"#\" onclick=\"$fun('2',$param)\"><button type='button' class='btn border-radius-0 btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>2</button></a>";
                    $pagination.= "<button type='button' class='btn border-radius-0 btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>...</button>";
                    for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
                    {
                        if ($counter == $page)
                            $pagination.= "<span class=\"current\"><button type='button' class='btn border-radius-0 btn-outline-primary btn-primary waves-effect m-l-5 m-r-5'>$counter</button></span>";
                        else
                            $pagination.= "<a href=\"#\" onclick=\"$fun('$counter',$param)\"><button type='button' class='btn border-radius-0 btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>$counter</button></a>";					
                    }
                }
            }
            
            if ($page < $counter - 1) 
                $pagination.= "<a href=\"#\" onclick=\"$fun('$next',$param)\"><button type='button' class='btn btn-outline-primary btn-default waves-effect  m-l-5 m-r-5' style='border-radius: 0px 10px 10px 0px;'>Next</button></a>";
            else
                $pagination.= "<span class=\"disabled\"><button type='button' class='btn btn-outline-primary btn-primary disabled waves-effect  m-l-5 m-r-5' style='border-radius: 0px 10px 10px 0px;'>Next</button></span>";
            $pagination.= "</div>\n";		
        }

        return $pagination;
    }

}