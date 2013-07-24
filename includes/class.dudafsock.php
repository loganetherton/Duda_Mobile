<?php
/**
 * DUDAMOBILE API CONNECTION CLASS
 * 
 * @author Dudamobile
 * @link http://www.dudamobile.com
 */
class DudaMobile_Connection {

//Vars:
//$api_url: URL of the API
//$api_login: Account login
//$api_password: Account password
//$api_port: Port to be used for API
    private $api_url = '';
    private $api_login = '';
    private $api_password = '';
    private $api_port = '';
    
//$fp: File pointer for fsockopen().
    public $fp;

//Set class variables to object variables, based on what's passed when DudaMobile_Connection() is instantiated.
//Call $this->_initFsock() - Is this call right? Shouldn't it be a class call here?
    public function __construct($url, $username, $password, $port) {
        $this->api_url = $url;
        $this->api_login = $username;
        $this->api_password = $password;
        $this->api_port = $port;

        $this->_initFsock();
    }

//If object API port is 443, open ssl connection. Otherwise, open connection based on URL.
    private function _initFsock() {
        
        if ($this->api_port == 443) {
            $url = "ssl://".$this->api_url;
        }
        else {
            $url = $this->api_url;
        }

//Open socket to url, port, error number, error message, set timeout
//If unable to open socket, die with error message
        $this->fp = fsockopen($url, $this->api_port, $errno, $errstr, 5);
        if(!$this->fp){
            die("Error: $errstr ($errno)\n");
        }
    }

//Write to file pointer to specify the following headers (I believe):
//using HTTP, $method and $resource.
//Sets the host to the object $api_url property.

    public function sendRequest($method,$resource,$data=array()) {
        
        $output = "";
        $headers= "";
        $is_header = 1;
        $buffer = NULL;
        $rtn = array();
        $protocol = '';
        $http_code = '';
        
        fwrite($this->fp, "$method $resource HTTP/1.0\r\n");
        fwrite($this->fp, "Host: {$this->api_url}\r\n");
        fwrite($this->fp, "Authorization: Basic ".base64_encode($this->api_login.":".$this->api_password)."\r\n");

        if(!empty($data)){
            fwrite($this->fp, "Content-Type: application/json\r\n");
            fwrite($this->fp, "Accept: application/json\r\n");
            if($data){
                $poststring=json_encode($data);
            }
   
            fwrite($this->fp, "Content-length: ".strlen($poststring)."\r\n");
        }

        
        fwrite($this->fp, "Connection: close\r\n");
        fwrite($this->fp,"\r\n");
        if($data){
            fwrite($this->fp, $poststring . "\r\n\r\n");
        }

        stream_set_timeout($this->fp,60);

        
        while(!feof($this->fp)) {
            $buffer = fgets($this->fp, 128);

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
        fclose($this->fp);

        $head = explode("\r\n", $headers);
        if(!empty($head)) {
            foreach($head as $h) {
                if(strpos($h, ':') !== FALSE)
                    $rtn['header'][(substr($h, 0, strpos($h,':')))] = trim(substr($h,strpos($h,':')+1));
            }
        }
        
        list($protocol,$http_code,$message)=explode(" ",$head[0]);
        
        return array('http_code'=>$http_code,'content'=>json_decode($output));

    }

}

?>
