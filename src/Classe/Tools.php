<?php 

namespace App\Classe;

use DateTime;

Class Tools {
    
    public function tripDuration(DateTime $start, DateTime $end) 
    {
        $start = $start->format('d-m-Y');
        $end = $end->format('d-m-Y');
        return round((strtotime($end) - strtotime($start))/(60*60*24)+1); 
    }
}