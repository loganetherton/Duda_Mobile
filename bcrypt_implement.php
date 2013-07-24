<?php
/**********************************************************************
 *  CPanel - Dudamobile Integration. Custom developed. (10.10.12)
 *
 *
 *  CREATED BY DUDAMOBILE          ->        http://dudamobile.com
 *
 *
 *
 **********************************************************************/

/*
 * Includes & settings
 */

phpinfo();
 
////Include cPanel LiveAPI class and instantiate object. 
//require_once "/usr/local/cpanel/php/cpanel.php";
//$cpanel = new CPANEL();
//
////Include configuration file
require_once('includes/configuration.php');
////Include dudafsock.php, to establish connection to Dudamobile API
//require_once('includes/class.dudafsock.php');
//// Include whmcsfsock.php, WHMCS socket connection
//require_once('includes/class.whmcsfsock.php');
//// Inlude blowfish.php, bcrypt implementation for encryption
require_once('includes/class.blowfish.php');
//
//
///**
//* FUNCTION DEFINITIONS
//*/
//
///**
//* FUNCTION addHtaccess
//* Adds specified content to .htaccess files in every directory from $dirs array
//* @param string $htaccess
//* @param string $dirs
//*/
//function addHtaccess($htaccess,$dirs){
//
//    global $cpanel,$runtimeurl;
//
//    dudaLog("dudamobile.live::addHtaccess start");
//
//    $htaccess = preg_replace('/\<mobile domain\>/', $runtimeurl, $htaccess);
//
//    dudaLog('dudamobile.live::addHtaccess htaccess='.$htaccess);
//
//    $extracontent="#duda_mobile_section_start\r\n".$htaccess."\r\n#duda_mobile_section_end\r\n";
//    
//    foreach($dirs as $dir){
//        $htaccessexists=false;
//        $apiresult = $cpanel->api2( 'Fileman', 'listfiles', array('dir'=>$dir,'showdotfiles'=>true,'types'=>'file') );
//        foreach($apiresult['cpanelresult']['data'] as $scan){
//            if($scan['file']=='.htaccess'){
//                $htaccessexists=true;
//                $apiresult = $cpanel->api2( 'Fileman', 'viewfile', array('dir'=>$dir,'file'=>'.htaccess') );
//                if($apiresult['cpanelresult']['data'][0]['fileinfo']=='empty' || substr($apiresult['cpanelresult']['data'][0]['contents'],0,7)=='<iframe')
//                        $content='';
//                else
//                        $content=$apiresult['cpanelresult']['data'][0]['contents'];
//                if(strpos($content,'duda_mobile_section')===false)
//                        $newcontent=$extracontent.$content;
//                else
//                        $newcontent=$content;
//                break;
//            }
//        }
//        if(!$htaccessexists)
//            $newcontent=$extracontent;
//        
//        $cpanel->api1( 'Fileman', 'fmsavefile', array(0=>$dir,1=>'.htaccess',2=>$newcontent) );
//
//        dudaLog("dudamobile.live::addHtaccess end");
//    }   
//}
//
///**
//* FUNCTION callApi
//* Sends Data to DudaMobile API and recieves response
//* @param string $method
//* @param string $resource
//* @param array $data
//* @return array
//*/
//function callApi($method,$resource,$data=array()){
//	
//    global $url,$username,$password,$authtoken,$port;
//
//    dudaLog("dudamobile.live::callApi start");
//
//    $credentials = sprintf("Calling API: URL=%s, PORT=%s, USERNAME=%s, PASSWORD=%s, AUTHTOKEN=%s", $url, $port, $username, $password, $authtoken);
//
//    dudaLog($credentials);
//
//    $DUDA = new DudaMobile_Connection($url,$username,$password, $port);
//    
//    dudaLog("dudamobile.live::callApi end");
//    
//    return $DUDA->sendRequest($method,"/api$resource?authToken=$authtoken",$data);
//}
//
///**
//* FUNCTION checkAccount
//* Checks if DudaMobile account exists & its status
//* @param string $accname
//* @return string
//*/
//function checkAccount($accname){
//
//    dudaLog("dudamobile.live::checkAccount start");
//
//    $result=callApi('GET','/accounts/'.$accname);
//
//    $http_code = $result['http_code'];
//
//    dudaLog('dudamobile.live::checkAccount http_code='.$http_code);
//    dudaLog("dudamobile.live::checkAccount end");
//
//    if($http_code=='200'){
//            return 'exists';
//    }
//    else if($http_code=='400' && $result['content']->error_code=='ResourceNotExist')
//            return 'notexists';
//    else if (isset($result['content']->error_code))
//            return $result['content']->error_code;
//    else if (isset($result['content']->http_code))
//            return $result['content']->http_code;
//    else
//            return "Unknown Error when Checking Account";
//}
//
///**
//* FUNCTION checkSite
//* Checks if DudaMobile site exists & it's status
//* @param string $site
//* @return string
//*/
//function checkSite($site){
//
//    dudaLog("dudamobile.live::checkSite start");
//
//    $result=callApi('GET','/sites/'.$site);
//
//    $http_code = $result['http_code'];
//
//    dudaLog('dudamobile.live::checkSite http_code='.$http_code);
//    dudaLog("dudamobile.live::checkSite end");
//
//    if($result['http_code']=='200'){
//		if(isset($result['content']->last_published_date))
//			return 'enabled';
//		else
//			return 'disabled';
//    }
//    else if($result['http_code']=='400' && $result['content']->error_code=='ResourceNotExist')
//            return 'notexists';
//    else if (isset($result['error_code']))
//            return $result['error_code'];
//    else if (isset($result['http_code']))
//            return $result['http_code'];
//    else
//            return "Unknown Error when Checking Site";
//}
//
///**
//* FUNCTION createSite
//* Creates site
//* @return array
//*/
//function createSite(){
//    
//    global $accname,$sitealias,$domain;
//    
//    dudaLog("dudamobile.live::createSite start");
//
//    $data=  array(  'site_data'    => array('site_name'          => $sitealias,
//                                            'account_name'       => $accname,
//                                            'original_site_url'  => 'http://'.$domain,
//                                            'site_domain'        => 'http://'.$domain),
//                                            'publish_now'        => 'true');
//    
//    $result = callApi('POST','/sites/create',$data);
//
//    $http_code = $result['http_code'];
//
//    dudaLog('dudamobile.live::createSite http_code='.$http_code);
//    dudaLog("dudamobile.live::createSite end");
//
//    if($http_code=='200')
//            return array('success'=>true);
//    else
//            return array('success'=>false, 'error'=>$result['content']->message);
//}
//
///**
//* FUNCTION createWithSite
//* Creates DudaMobile account & site
//* @return array
//*/
//function createWithSite(){
//    
//    global $email,$accname,$authtoken,$domain,$sitealias;
//    
//    dudaLog("dudamobile.live::createWithSite start");
//
//    $data=array( 'account'  => array( 'account_name' => $accname,
//                                      'auth_token'   => $authtoken,
//                                      'email'        => $email),
//                 'site'     => array( 'site_data'    => array( 'site_name'          => $sitealias,
//                                                               'original_site_url'  => 'http://'.$domain,
//                                                               'site_domain'        => 'http://'.$domain),
//                                      'publish_now'  => 'true'));
//    
//    $result = callApi('POST','/accounts/createwithsite',$data);
//
//    $http_code = $result['http_code'];
//
//    dudaLog('dudamobile.live::createWithSite http_code='.$http_code);
//    dudaLog("dudamobile.live::createWithSite end\n");
//
//    if($http_code=='204')
//            return array('success'=>true);
//    else
//            return array('success'=>false, 'error'=>$result['content']->message);
//}
//
///**
// * FUNCTION dudaLog
// * Writes to logfile
// * @param $text
// */
//function dudaLog($text){
//
//    global $logfile;
//    
//    file_put_contents($logfile, "$text\n", FILE_APPEND | LOCK_EX);
//}
//
///**
//* FUNCTION generateSsoLink
//* Generates DudaMobile SSO Link
//* @return string
//*/
//function generateSsoLink(){
//
//    global $secret_key,$partner_key,$accname,$authtoken,$sitealias,$url;
//
//    dudaLog("dudamobile.live::generateSsoLink start");
//
//    $combined_key=$secret_key.$authtoken;
//	$timestamp=time();
//        
//	$sso = "{$secret_key}{$authtoken}user={$accname}timestamp={$timestamp}site={$sitealias}partner_key={$partner_key}";
//	$dm_sig=hash_hmac('sha1',$sso,$combined_key);
//
//	$link = "http://$url/home/site/$sitealias?dm_sig_site=$sitealias&dm_sig_user=$accname&dm_sig_partner_key=$partner_key&dm_sig_timestamp=$timestamp&dm_sig=$dm_sig";
//
//    dudaLog('dudamobile.live::generateSsoLink link='.$link);
//    dudaLog("dudamobile.live::generateSsoLink end");
//	
//	return $link;
//}
//
///**
//* FUNCTION getDirs
//* Creates list of all subdirectories in $path
//* @param string $path
//* @return array
//*/
//function getDirs($path){
//    
//    global $cpanel;
//    $dirs=array($path);
//    $apiresult = $cpanel->api2( 'Fileman', 'listfiles', array('dir'=>$path,'types'=>'dir') );
//    foreach($apiresult['cpanelresult']['data'] as $scan){
//        $dirs=array_merge($dirs,getDirs($scan['fullpath']));
//    }
//    return $dirs;
//}
//
///**
//* FUNCTION getDocRoot
//* Recieves homedir of the site
//* @param string $domain
//* @return string
//*/
//function getDocRoot($domain){
//    
//    global $cpanel;
//    $apiresult = $cpanel->api2( 'DomainLookup', 'getdocroot', array('domain'=>$domain) );
//    return $apiresult['cpanelresult']['data'][0]['docroot'];
//}
//
///**
//* FUNCTION publishSite
//* Publishes DudaMobile site
//* @param string $site
//* @return array
//*/
//function publishSite($site){
//
//    dudaLog('dudamobile.live::publishSite site='.$site);
//
//    $result=callApi('POST','/sites/publish/'.$site);
//	
//	if($result['http_code']=='204')
//		return array('success'=>true);
//	else
//		return array('success'=>false, 'error'=>$result['content']->message);
//}
//
///**
//* FUNCTION removeHtaccess
//* Removes specified content from .htaccess files in every directory from $dirs array
//* @param string $dirs
//*/
//function removeHtaccess($dirs){
//    
//    global $cpanel;
//   
//    dudaLog("dudamobile.live::removeHtaccess start");
//
//    foreach($dirs as $dir){
//        $apiresult = $cpanel->api2( 'Fileman', 'listfiles', array('dir'=>$dir,'showdotfiles'=>true,'types'=>'file') );
//        foreach($apiresult['cpanelresult']['data'] as $scan){
//            if($scan['file']=='.htaccess'){
//                $apiresult = $cpanel->api2( 'Fileman', 'viewfile', array('dir'=>$dir,'file'=>'.htaccess') );
//                $content=$apiresult['cpanelresult']['data'][0]['contents'];
//                if(strpos($content,'duda_mobile_section')!==false){
//                    $temp=explode('#duda_mobile_section_start',$content,2);
//                    $newcontent=$temp[0];
//                    $temp=explode('#duda_mobile_section_end',$temp[1],2);
//                    $newcontent.=$temp[1];
//                    $cpanel->api1( 'Fileman', 'fmsavefile', array(0=>$dir,1=>'.htaccess',2=>trim($newcontent)) );
//                }
//            }
//        }
//    }
//    dudaLog("dudamobile.live::removeHtaccess end");
//}
//
///**
//* FUNCTION unpublishSite
//* Unpublishes DudaMobile site
//* @param string $site
//* @return array
//*/
//function unpublishSite($site){
//
//    dudaLog('dudamobile.live::unpublishSite site='.$site);
//
//    $result=callApi('POST','/sites/unpublish/'.$site);
//
//    $http_code = $result['http_code'];
//
//    dudaLog('dudamobile.live::unpublishSite http_code='.$http_code);
//
//    if($result['http_code']=='204')
//		return array('success'=>true);
//	else
//		return array('success'=>false, 'error'=>$result['content']->message);
//		
//}

/**
 * Set global variables
 */

//$domain     =   $cpanel->cpanelprint('$CPDATA{\'DNS\'}');
//$email      =   $cpanel->cpanelprint('$CPDATA{\'CONTACTEMAIL\'}');

//REMOVE THIS $domain SETTING
$domain = "example.com";

/**
 * FUNCTION 
 * Check if bcrypt Blowfish is installed, and use it if available. Generate a unique id using uniqid().
 * Create a unique id and use that and Blowfish to create strong hashes for
 * @param str $accname Hashed account name
 * @param str $sitealias Hashed site alias
 * @param str $authtoken Hash authorization token
 */

if(CRYPT_BLOWFISH){
    //Create new instance of Bcrypt, with 12 cycle of workload
    $bcrypt = new bcrypt(12);
    
    // Create a unique id using uniqid().
    $uniqid = uniqid();
    
    //Create a hash using bcrypt
    echo 'Bcrypt hash: ' . $bcrypt->generateHash($domain.$uniqid) . '<br>';
    //
    //Verify the hash
    $hash = $bcrypt->generateHash($domain.$uniqid);
    
    $accname = $bcrypt->generateHash($domain.$uniqid);
    echo "Verify account name hash: " . $bcrypt->verify($domain.$uniqid, $hash) . "<br>";
    
    $sitealias = $bcrypt->generateHash($domain.$uniqid);
    echo "Verify site alias hash: " . $bcrypt->verify($domain.$uniqid, $hash) . "<br>";
    
    $authtoken = $bcrypt->generateHash($domain.$uniqid);
    echo "Verify auth token hash: " . $bcrypt->verify($domain.$uniqid, $hash) . "<br>";
}
else{
    $accname    =   md5($domain."_account");
    $sitealias  =   md5($domain."_site");
    $authtoken    =   md5($domain."_password");
}


$htaccess   =   file_get_contents($htaccess_content);
//$docroot    =   getDocRoot($domain);
//$dirs       =   getDirs($docroot);

$logfile    =   '/tmp/duda.log';

/*
 * Action handler
 */



if($dir_handle = opendir('includes')){
    $files = array();
    while(false !== ($entry = readdir($dir_handle))){
        $files[] = $entry;
    }
}
 
if(isset($_POST['ajax'])){

    $date = date('d/m/Y H:i:s', time());
    dudaLog($date.':: Starting Dudamobile Action handler');
    
    // For now, it looks like I'm going to have to make it '/home/alexna/.cpanel/ndata' for the nvdatastore
    // This will be changed when implementing the plugin
    if($dir_handle = opendir('/home/alexna/.cpanel/nvdata')){
        
    }


    /**
     * // Do not use the md5 to generate accname, sitealias and authtoken, they should be generated once and then stored in a file
     * // They can be stored in one file or 3 different files, whatever suits
     * // Each value key will be the domain_key (key = accname / sitealias / authtoken)
     *
     * if ($cpanel->api2( 'NVData', 'get', $domain.'_accname') is null) {     // The accname was not set yet (and also sitealias, authtoken)
     *
     *      // Set global parameters and save them to file
     *
     *      $accname = uniqid($domain);
     *      $sitealias = uniqid($domain);
     *      $authtoken = uniqid($domain);
     *
     *      $cpanel->api2( 'NVData', 'set', array($domain.'_accname'=>$accname, $domain.'_sitealias'=>$sitealias, $domain.'_authtoken'=>$authtoken) );
     * }
     * else {
     *
     *      $accname =  $cpanel->api2( 'NVData', 'get', $domain.'_accname');
     *      $sitealias =  $cpanel->api2( 'NVData', 'get', $domain.'_sitealias');
     *      $authtoken =  $cpanel->api2( 'NVData', 'get', $domain.'_authtoken');
     *
     * }
     */

    dudaLog(
        sprintf(
            "action=%s, account_name=%s, site_alias=%s domain=%s",
            $_POST['action'],
            $accname,
            $sitealias,
            $domain
        )
    );

    switch($_POST['action']){
        /*
         * Check DudaMobile site's status
         */
        case 'checksite':
            
            die(checkSite($sitealias));
            break;
        /*
         * Check account's status
         */
        case 'checkaccount':
            
            die(checkAccount($accname));
            break;
        /*
         * Create site & account
         */
        case 'makesite':
            
            // Call DudaMobile api to create site
            if($_POST['onlysite']=='true'){
                $result=createSite();
                if(!$result['success']){
                        die('result**failure||during**creating site||error**'.$result['error']);
                }
            }
            else{
                $result=createWithSite();
                if(!$result['success']){
                        die('result**failure||during**creating site||error**'.$result['error']);
                }
            }
            
            // Call CPanel api to add zone record
            $result = $cpanel->api2( 'ZoneEdit', 'add_zone_record', array('domain'=>$domain,'name'=>'m','cname'=>'mobile.dudamobile.com','type'=>'CNAME') );
            if($result['cpanelresult']['data'][0]['result']['status']==0){
                    die('result**failure||during**adding zone record||error**'.$result['cpanelresult']['data'][0]['result']['statusmsg']);
            }
            
            // Add specified content to .htaccess files
            addHtaccess($htaccess,$dirs);

            if(!$disable_whmcs_api){
                // Call WHMCS api to add new order
                $WHMCS = new whmcsapi($whmcs_host,$whmcs_path,$whmcs_username,$whmcs_password);
                $WHMCS->addOrder($whmcs_clientid,$whmcs_productid,$domain);
            }

            die('result**success');
            break;
        /*
         * Edit site via SSO link
         */
        case 'editsite':
            
            global $url;

            // Mobile preview link
            $previewlink= sprintf("http://%s/site/%s?preview=true&dm_try_mode=true", $url, $sitealias);

            die('result**success||previewlink**'.$previewlink);
            break;
        /*
         * Disable site
         */
        case 'disablesite':
            
            // Call DudaMobile api to unpublish site
            $result=unpublishSite($sitealias);
            if(!$result['success']){
                    die('result**failure||during**disabling site||error**'.$result['error']);
            }
            
            // Remove specified content from .htaccess files
            removeHtaccess($dirs);

            if(!$disable_whmcs_api){
                // Call WHMCS api to suspend the service
                $WHMCS = new whmcsapi($whmcs_host,$whmcs_path,$whmcs_username,$whmcs_password);
                $WHMCS->disable($whmcs_clientid,$whmcs_productid,$domain);
            }
            
            die('result**success');
            break;
        /*
        * Enable site
        */
        case 'enablesite':
            
            // Call DudaMobile api to publish site
            $result=publishSite($sitealias);
            if(!$result['success']){
                    die('result**failure||during**enabling site||error**'.$result['error']);
            }
            
            // Add specified content to .htaccess files
            addHtaccess($htaccess,$dirs);

            if(!$disable_whmcs_api){
                // Call WHMCS api to unsuspend the service
                $WHMCS = new whmcsapi($whmcs_host,$whmcs_path,$whmcs_username,$whmcs_password);
                $WHMCS->enable($whmcs_clientid,$whmcs_productid,$domain);
            }
            
            die('result**success');
            break;

        case 'redirecttoeditor':

            $ssolink = generateSsoLink();
            die($ssolink);
            break;
            
    }
}