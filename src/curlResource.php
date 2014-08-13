<?php

class curlResource extends classes\Interfaces\resource{

   public function multiCurl($nodes){ 
        $curl_array = array(); 
        foreach($nodes as $url){ 
            $curl_array[$url] = curl_init($url); 
            curl_setopt($curl_array[$url], CURLOPT_RETURNTRANSFER, true); 
        } 
        return $this->executeMultiCurl($curl_array);
    } 
    
    private function executeMultiCurl($arr){
        $mh = curl_multi_init(); 
        foreach($arr as $var){
            curl_multi_add_handle($mh, $var); 
        }
        $running = NULL; 
        do { 
            usleep(10000); 
            curl_multi_exec($mh,$running); 
        } while($running > 0); 

        $res = array(); 
        foreach($arr as $url => $var) { 
            $res[$url] = curl_multi_getcontent($var); 
        } 

        foreach($arr as $var){ 
            curl_multi_remove_handle($mh, $var); 
        } 
        curl_multi_close($mh);    
        return $res; 
    }
    
    public function multiSimpleCurl($nodes,$post=array(),$get=array(),$http=array(), $buildQuery = true, $timeout = 0){ 
        $curl_array = array(); 
        foreach($nodes as $url){
            if(strstr($url, URL) !== false) {
                $post['Crypty_base64key'] = Crypty_base64key;
            }
            $url = explode('?',$url,2);
            if(count($url)===2){
                $temp_get = array();
                parse_str($url[1],$temp_get);
                $get = array_merge($get,$temp_get);
            }
            
            $curl_array[$url] = curl_init($url[0]."?".http_build_query($get)); 
            if($timeout > 0) {curl_setopt($curl_array[$url], CURLOPT_TIMEOUT, $timeout);}
            if(!empty($post)){
                $post = ($buildQuery)?http_build_query($post):$post;
                curl_setopt($curl_array[$url], CURLOPT_POST, 1);
                curl_setopt($curl_array[$url], CURLOPT_POSTFIELDS, $post);
            }
            if(!empty($http)) {curl_setopt($curl_array[$url], CURLOPT_HTTPHEADER, $http);}
            curl_setopt($curl_array[$url], CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl_array[$url], CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl_array[$url], CURLOPT_RETURNTRANSFER, true);
        }
        return $this->executeMultiCurl($curl_array);
    } 
}