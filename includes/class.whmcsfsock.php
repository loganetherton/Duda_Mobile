<?php
/**
 * WHMCS API CONNECTION CLASS for DUDAMOBILE
 * 
 * @author Dudamobile
 * @link http://www.dudamobile.com
 */
class whmcsapi {
    
//Vars:
//$host:
//$path:
//$username:
//$password: 
    private $host;
    private $path;
    private $username;
    private $password;

    function __construct($host,$path,$username,$password) {
        
        $this->host       = $host;
        $this->path       = $path;
        $this->username   = $username;
        $this->password   = md5($password);
        
    }
    public function addOrder($uid, $pid, $domain) {

        $params = array(
            'action' => 'addorder',
            'clientid' => $uid,
            'pid' => $pid,
            'domain' => $domain,            
            'billingcycle' => 'annually',
            'paymentmethod'=> 'paypal',
        );
        $ret = $this->get($params);
        if($ret['http_code']!='200' || $ret['content']->result!='success')
            die("WHMCS: Can't create order");
        $oid=$ret['content']->orderid;
        $hid=$ret['content']->productids;

        $params = array();
        $params["action"] = "acceptorder";
        $params["orderid"] = $oid;
        $ret = $this->get($params);
        if($ret['http_code']!='200' || $ret['content']->result!='success')
            die("WHMCS: Can't accept order");
        
        $params = array();
        $params["action"] = "modulecreate";
        $params["accountid"] = $hid;
        $ret = $this->get($params);
        if($ret['http_code']!='200' || $ret['content']->result!='success')
            die("WHMCS: Can't activate service");
        
        return true;
    }
    
    public function enable($uid,$pid,$domain){
        
        $products = $this->getClientsProducts($uid,$pid,$domain);
        if(isset($products->product[0])){
            $hid = $products->product[0]->id;
            $this->unsuspend($hid);
        }
        else{
            die("WHMCS: Service doesn't exist");
        }
        
    }
    
    public function disable($uid,$pid,$domain){

        $products = $this->getClientsProducts($uid,$pid,$domain);
        if(isset($products->product[0])){
            $hid = $products->product[0]->id;
            $this->suspend($hid);
        }
        else{
            die("WHMCS: Service doesn't exist");
        }
        
    }
    
    private function getClientsProducts($uid,$pid,$domain){
        
        $params = array();
        $params["action"] = "getclientsproducts";
        $params["clientid"] = $uid;
        $params["domain"] = $domain;
        $params["pid"] = $pid;
        $ret = $this->get($params);
        if($ret['http_code']!='200' || $ret['content']->result!='success')
            die("WHMCS: Can't check products");
        
        return $ret['content']->products;
    }
    
    private function suspend($hid) {

        $params = array();
        $params["action"] = "modulesuspend";
        $params["accountid"] = $hid;
        $ret = $this->get($params);
        if($ret['http_code']!='200' || $ret['content']->result!='success')
            die("WHMCS: Can't suspend account");
        
        return true;
    }
    
    private function unsuspend($hid) {

        $params = array();
        $params["action"] = "moduleunsuspend";
        $params["accountid"] = $hid;
        $ret = $this->get($params);
        if($ret['http_code']!='200' || $ret['content']->result!='success')
            die("WHMCS: Can't unsuspend account");
        
        return true;
    }
    
    private function get($params = array()) {
        
        if(!is_array($params))
            $params = array();
        $host = $this->host;
        $path = $this->path;
        $params['username'] =$this->username;
        $params['password'] =$this->password;
        $params['responsetype'] = 'json';
        $poststring=http_build_query($params);
        
        $fp = fsockopen($host, '80', $errno, $errstr, 30);
        if(!$fp){
            die("WHMCS: Connection Error: $errstr ($errno)\n");
        }
        fwrite($fp, "POST $path HTTP/1.1\r\n");
        fwrite($fp, "Host: $host\r\n");
        fwrite($fp, "Content-type: application/x-www-form-urlencoded\r\n");
        fwrite($fp, "Content-length: ".strlen($poststring)."\r\n");
        fwrite($fp, "Connection: close\r\n");
        fwrite($fp,"\r\n");
        fwrite($fp, $poststring . "\r\n\r\n");

        stream_set_timeout($fp,60);

        $output = "";
        $headers= "";
        $is_header = 1;
        while(!feof($fp)) {
            $buffer = fgets($fp, 128);
            
            if ($buffer == FALSE) {
                break;
            }
            if (!$is_header) {
            $output .= $buffer;
            }
            if ($buffer == "\r\n") {
                $is_header = 0;
            }
            if ($is_header) {
            $headers .= $buffer;
            }
        }
        fclose($fp);

        $head = explode("\r\n", $headers);
        if(!empty($head)) {
            foreach($head as $h) {
                if(strpos($h, ':') !== FALSE)
                    $rtn['header'][(substr($h, 0, strpos($h,':')))] = trim(substr($h,strpos($h,':')+1));
            }
        }

        list($protocol,$http_code,$message)=explode(" ",$head[0]);
     
        
        if(substr($output,0,1) != "{")
                $output=substr($output,strpos ($output,'{'));
        
        if(substr($output,-1) != "}")
                $output=substr($output,0,strrpos ($output,'}')+1);
        
        return array('http_code'=>$http_code,'content'=>json_decode($output));
        
    }

}

?>