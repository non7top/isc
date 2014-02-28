#!/usr/bin/php
<?php

# Command line opts
array_shift($argv);
$server = array_shift($argv);
$act_type = array_shift($argv);
$act = array_shift($argv);

class isc
{
    protected $client;
    protected $session_id;
    public $client_id;
    protected $server;

    public function __construct($server, $url, $username,$password) { 
        $this->server = $server;

        $soap_location = $url.'/remote/index.php';
        $soap_uri = $url.'/remote/';

        $this->client = new SoapClient(null, array('location' => $soap_location,
                                                   'uri'      => $soap_uri,
                                                   'trace' => 1,
                                                   'exceptions' => 1));

        try {
            if($this->session_id = $this->client->login($username,$password)) {
                echo 'Logged successfull. Session ID:'.$this->session_id."\n";
            }
        } catch (SoapFault $e) {
            echo $this->client->__getLastResponse();
            die('SOAP Error: '.$e->getMessage()."\n");
        }
    }
    
    public function rand_passwd(){
        return substr(str_shuffle('abcdefghijklmnopqrs@#$%^<>&tuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') , 0 , 10 );
    }


    public function add_client($client_name) {

        try {
	
            //* Set the function parameters.
            $reseller = 0;
            $params = array(
			'company_name' => $client_name,
			'contact_name' => $client_name,
			'customer_no' => '',
			'vat_id' => '',
			'street' => '',
			'zip' => '',
			'city' => '',
			'state' => '',
			'country' => 'RU',
			'telephone' => '',
			'mobile' => '',
			'fax' => '',
			'email' => '',
			'internet' => '',
			'icq' => '',
			'notes' => '',
			'dafault_mailserver' => 1,
			'limit_maildomain' => -1,
			'limit_mailbox' => -1,
			'limit_mailalias' => -1,
			'limit_mailaliasdomain' => -1,
			'limit_mailforward' => -1,
			'limit_mailcatchall' => -1,
			'limit_mailrouting' => 0,
			'limit_mailfilter' => -1,
			'limit_fetchmail' => -1,
			'limit_mailquota' => -1,
			'limit_spamfilter_wblist' => 0,
			'limit_spamfilter_user' => 0,
			'limit_spamfilter_policy' => 1,
			'default_webserver' => 1,
			'limit_web_ip' => '',
			'limit_web_domain' => -1,
			'limit_web_quota' => -1,
			'web_php_options' => 'no,fast-cgi,cgi,mod,suphp',
			'limit_web_subdomain' => -1,
			'limit_web_aliasdomain' => -1,
			'limit_ftp_user' => -1,
			'limit_shell_user' => 0,
			'ssh_chroot' => 'no,jailkit,ssh-chroot',
			'limit_webdav_user' => 0,
			'default_dnsserver' => 1,
			'limit_dns_zone' => -1,
			'limit_dns_slave_zone' => -1,
			'limit_dns_record' => -1,
			'default_dbserver' => 1,
			'limit_database' => -1,
			'limit_cron' => 0,
			'limit_cron_type' => 'url',
			'limit_cron_frequency' => 5,
			'limit_traffic_quota' => -1,
			'limit_client' => 0,
			'parent_client_id' => 0,
			'username' => $client_name,
			'password' => $this->rand_passwd(),
			'language' => 'en',
			'usertheme' => 'default',
			'template_master' => 0,
			'template_additional' => '',
			'created_at' => 0
			);
	
            $affected_rows = $this->client->client_add($this->session_id, $reseller, $params);
	
            echo "Created client: ".$client_name."\n";
            return $affected_rows;
	
        } catch (SoapFault $e) {
            echo $this->client->__getLastResponse();
            die('SOAP Error: '.$e->getMessage()."\n");
        }
    }

    public function add_web_domain($client_name){
        try {

            $params = array(
                        'server_id' => 1,
                        'ip_address' => '*',
                        'domain' => $client_name.'.'.$this->server,
                        'type' => 'vhost',
                        'parent_domain_id' => 0,
                        'vhost_type' => 'name',
                        'hd_quota' => -1,
                        'traffic_quota' => -1,
                        'cgi' => 'n',
                        'ssi' => 'n',
                        'suexec' => 'y',
                        'errordocs' => 1,
                        'is_subdomainwww' => 1,
                        'subdomain' => 'www',
                        'php' => 'fast-cgi',
                        'ruby' => 'n',
                        'redirect_type' => '',
                        'redirect_path' => '',
                        'seo_redirect' => '*_domain_tld_to_domain_tld',
                        'ssl' => 'n',
                        'ssl_state' => '',
                        'ssl_locality' => '',
                        'ssl_organisation' => '',
                        'ssl_organisation_unit' => '',
                        'ssl_country' => '',
                        'ssl_domain' => '',
                        'ssl_request' => '',
                        'ssl_cert' => '',
                        'ssl_bundle' => '',
                        'ssl_action' => '',
                        'stats_password' => '',
                        'stats_type' => 'webalizer',
                        'allow_override' => 'All',
                        'apache_directives' => '',
                        'php_open_basedir' => '-',
                        'custom_php_ini' => '',
                        'backup_interval' => '',
                        'backup_copies' => 1,
                        'active' => 'y',
                        'traffic_quota_lock' => 'n',
                        'pm_process_idle_timeout' => '10',
                        'pm_max_requests' => '0'
                        );
        $affected_rows = $this->client->sites_web_domain_add($this->session_id, $this->client_id, $params, $readonly = false);

        echo "Created web site: ".$client_name.'.'.$this->server."\n";
        return $affected_rows;

        } catch (SoapFault $e) {
            echo $this->client->__getLastResponse();
            die('SOAP Error: '.$e->getMessage());
        }
    }

    public function add_ftp_user($domain, $client_name) {
        $password=$this->rand_passwd();
        try {

            $domain_record = $this->client->sites_web_domain_get($this->session_id, $domain);


        $params = array(
                        'server_id' => 1,
                        'sys_userid' => 1,
                        'parent_domain_id' => 1,
                        'username_prefix' => $client_name,
                        'parent_domain_id' => $domain,
                        'username' => $client_name.'_ftp',
                        'password' => $password,
                        'quota_size' => -1,
                        'active' => 'y',
                        'uid' => $domain_record['system_user'],
                        'gid' => $domain_record['system_group'],
                        'dir' => $domain_record['document_root'],
                        'quota_files' => -1,
                        'ul_ratio' => -1,
                        'dl_ratio' => -1,
                        'ul_bandwidth' => -1,
                        'dl_bandwidth' => -1
                        );

        $affected_rows = $this->client->sites_ftp_user_add($this->session_id, $this->client_id, $params);
        
        echo "Created ftp user: ".$client_name.'_ftp'.' / '.$password."\n";

        } catch (SoapFault $e) {
            echo $this->client->__getLastResponse();
            die('SOAP Error: '.$e->getMessage());
        }
    }

    public function add_database_user($client_name) {
        $password=$this->rand_passwd();
        try {
            $db_user = substr('c'.$this->client_id.'_'.$client_name, 0, 15);
            $db_user = str_replace("-", '_', $db_user);
            $params = array(
                'server_id' => 1,
                'sys_userid' => 1,
                'database_user' => $db_user,
                'database_user_prefix' => 'c'.$this->client_id,
                'database_password' => $password
            );
        $affected_rows = $this->client->sites_database_user_add($this->session_id, $this->client_id, $params);
        echo "Created DB user: ".$db_user.' / '.$password."\n";
        return $affected_rows;

        } catch (SoapFault $e) {
            echo $this->client->__getLastResponse();
            die('SOAP Error: '.$e->getMessage());
        }
    }

    public function add_database($domain, $client_name, $user) {
        try {
            $db_name = substr('c'.$this->client_id.'_'.$client_name, 0, 15);
            $db_name = str_replace("-", '_', $db_name);
            $params = array(
                'sys_userid' => 1,
                'server_id' => 1,
                'parent_domain_id' => $domain,
                'type' => 'mysql',
                'database_name' => $db_name,
                'database_name_prefix' => 'c'.$this->client_id,
                'database_user_id' => $user,
                'remote_access' => 'n',
                'active' => 'y'
            );
            $affected_rows = $this->client->sites_database_add($this->session_id, $this->client_id, $params);
            echo "Created DB: ".$db_name."\n";
            return $affected_rows;

        } catch (SoapFault $e) {
            echo $this->client->__getLastResponse();
            die('SOAP Error: '.$e->getMessage());
        }
    }

    public function __destruct() {
        //var_dump($this->client->logout($this->session_id));
        if($this->client->logout($this->session_id)) {
            echo 'Logged out.\n';
        }
    }

}

function getLineWithString($fileName, $str) {
        // http://stackoverflow.com/a/9722200
        $lines = file($fileName);
        foreach ($lines as $lineNumber => $line) {
                if (strpos($line, $str) !== false) {
                        return $line;
                }
        }
        return -1;
}


function getServerConfig($server) {
// Read config file to get url and credentials
$configline=getLineWithString('config.php', $server);
if ($configline !== -1 ) {
    #list($server_url, $username, $password ) = explode('.', $configline);
    return trim($configline);
} else {
    print ("No configration found in config.php for $server \n");
    die("Format is url|user|password\n");
}
}


if ($act_type == "site_wizard" && $act == "add") {
    $client_name = array_shift($argv); // Fourth argument is site name
    if ( $client_name == NULL or preg_match('/[^a-z0-9_\-]/', $client_name) or strlen($client_name) > 15 ) {
        echo "SITENAME should consist of a-z0-9_ and be no more then 15 characters\n";
        die("Usage: script site_wizard add SITENAME\n");
    }
    list($url, $username, $password ) = explode('|', getServerConfig($server));

    $isc=new isc($server, $url, $username, $password);
    $isc->client_id = $isc->add_client($client_name);
    $new_domain=$isc->add_web_domain($client_name);
    $new_ftpuser=$isc->add_ftp_user($new_domain, $client_name);
    $new_database_user=$isc->add_database_user($client_name);
    $new_db=$isc->add_database($new_domain, $client_name, $new_database_user);
        
}


?>
