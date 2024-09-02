<?php


class Timeago {
    
    public function get($time){
        
        $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
        $lengths = array("60","60","24","7","4.35","12","10");

        $now = time();

        $difference  = $now - $time;
        $tense       = "ago";
        $oneDay      = 86400; 

        for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
            $difference /= $lengths[$j];
        }

        $difference = round($difference);

        if($difference != 1) {
            $periods[$j].= "s";
        }
        
        if($difference > ($oneDay*2)){
        
            return date("d M Y, H:i", $time); 
            
        } else {

            return "$difference $periods[$j] 'ago' ";
       }// if($difference = ($oneDay*2)){
        
    }//  public function get($time){ 
    
}// class Timeago {
