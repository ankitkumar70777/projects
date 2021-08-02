<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Site extends CI_Controller {

    private $data = array();

    public function __construct() {
        parent::__construct();
        $this->is_logged_in();
        $this->getConfig();
        $this->data['userButtonSetting'] = $this->user_model->getAgentButtonSetting();
    }

    function getMenuData() {
        $currentMenu = $this->menu_model->getCurrentMenu();
//    print_r($currentMenu);
        if ($currentMenu->output == "TRUE") {
            return $currentMenu;
        } else {
            $data['errorMessage'] = $currentMenu->message;
            $data['errorDiscription'] = "Please contact to Admin";
            $data['errorType'] = "404";
            $data['page'] = "pageNotFound";
            $this->load->view('error', $data);
            $this->output->_display();
            exit();
            $data['redirect'] = "site/pageNotFound/" . $currentMenu->message;
            $this->pageNotFound($currentMenu->message);
        }
    }

    function reloadPage() {
        $data['menuDetail'] = $this->getMenuData();
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $this->userStateChangeSet("reload");
        $data['page'] = "reload";
        $data['title'] = "Reloda page";
        // print_r($this->data['userButtonSetting']);
        $this->load->view('template', $data);
    }

    function getConfig() {
        $opt = $this->config_model->userPageSetting();
    }

    function is_logged_in() {
        $is_logged_in = $this->session->userdata('logged_in');
        $user = $this->session->userdata('id');
        $msg = $this->user_model->isexpired();
        if ($this->session->userdata('accesslevel') == 1) {

            if ($msg == -1)
                $data['alertexpiryerror'] = "Please change server expiry date";
        }
        if ($msg == -1 && ($this->session->userdata('accesslevel') == 3 || $this->session->userdata('accesslevel') == 4)) {
            $this->user_model->changeloginlog($this->session->userdata("id"));
            $this->session->sess_destroy();
            redirect(base_url() . 'index.php/login?alerterror=Login Expired', 'refresh');
        } else if ($user != "" || !empty($user)) {

            if ($this->user_model->isloggedin($user) == 0 && ($this->session->userdata('accesslevel') == 3 || $this->session->userdata('accesslevel') == 4)) {
                $data['alerterror'] = 'User can login from one system and cant login again';
                $data['redirect'] = "login/logout";
                $this->load->view('redirect', $data);
            }
        } else if ($is_logged_in !== 'true' || !isset($is_logged_in)) {
            redirect(base_url() . 'index.php/login', 'refresh');
        } //$is_logged_in !== 'true' || !isset( $is_logged_in )
    }

    public function index() {

        $menuDetail = $this->getMenuData();

        // $data['menuDetail'] = $this->getMenuData();

        if ($menuDetail->first_url != "") {
            $data['redirect'] = $menuDetail->first_url;
            $this->load->view("redirect", $data);
        } else {
            $data['errorMessage'] = "Please contact to Admin";
            $data['errorDiscription'] = "Sorry we are unable to find your default page";
            $data['errorType'] = "404";
            $data['page'] = "pageNotFound";
            $this->load->view('error', $data);
        }
        // if ( $this->session->userdata('accesslevel')  && $this->session->userdata('accesslevel') == 4 ) {
        //   $data['redirect']="site/agentdashboard";
        //   $this->load->view("redirect",$data);
        // }
        // else if ( $this->session->userdata('userMixDashboardSetting')  && $this->session->userdata('userMixDashboardSetting') == 1 ) {
        //   $data['redirect']="site/mixDashboard";
        //   $this->load->view("redirect",$data);
        // }
        // else {
        //   $data['userButtonSetting'] = $this->data['userButtonSetting'];
        //   $this->userStateChangeSet("dashboard");
        //   $data['editmode']="0";
        //   $data[ 'page' ] = 'dashboard';
        //   $data[ 'title' ] = 'Welcome';
        //   $data[ 'leadset' ] =$this->lead_model->getleadset();
        //   $data['table']=$this->campaign_model->getdashboarddata();
        //   $data['agentdashboarddata']=$this->user_model->superAgentdashboarddata();
        //   // print_r($data);
        //   $data['dashboarddata']=$this->reporting_model->getdashboarddata();
        //   $data['serverdata']=$this->reporting_model->getserverdata();
        //   $this->load->view( 'template', $data ); 
        // }
    }

    public function dashboard() {
        $data['menuDetail'] = $this->getMenuData();
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $this->userStateChangeSet("dashboard");
        $data['luaOpt'] = $this->userStateChangeSet("dashboard");
        $data['editmode'] = "0";
        // $data[ 'leadset' ] =$this->lead_model->getleadset();
        $data['table'] = $this->campaign_model->getdashboarddata();
        $data['agentdashboarddata'] = $this->user_model->superAgentdashboarddata();
        $data['dashboarddata'] = $this->reporting_model->getdashboarddata();
        $data['serverdata'] = $this->reporting_model->getserverdata();

        $data['page'] = 'dashboard';
        $data['title'] = 'Welcome';
        $this->load->view('template', $data);
    }

    public function mixDashboard() {
        $data['menuDetail'] = $this->getMenuData();

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['editmode'] = "0";
        $data['luaOpt'] = $this->userStateChangeSet("mixDashboard");

        $data['table'] = $this->campaign_model->getdashboarddata();
        $data['agentdashboarddata'] = $this->user_model->superAgentdashboarddata();
        $data['dashboarddata'] = $this->reporting_model->getdashboarddata();
        $data['serverdata'] = $this->reporting_model->getserverdata();
        $data['page'] = 'mixDashboard';
        $data['title'] = 'Welcome';
        $data['campaign'] = $this->reporting_model->getcampaign();
        $data['process'] = $this->reporting_model->getprocess();
        $data['agent'] = $this->reporting_model->getagent();
        $data['TotalGraphData'] = $this->reporting_model->TotalGraphData();
        // $data['table']    = $this->reporting_model->dashboardRedisData();
        $this->load->view('template', $data);
    }

    public function removeServiceLogs() {

        $dir = '/home/voitekk/productionCodes/logs/out';
        $files = array_diff(scandir($dir), array('..', '.'));
        foreach ($files as $key => $value) {
            $command = " > $dir/$value";
            exec($command, $output, $arr);
        }

        $dir = '/home/voitekk/productionCodes/logs/err';
        $files = array_diff(scandir($dir), array('..', '.'));
        foreach ($files as $key => $value) {
            $command = " > $dir/$value";
            exec($command, $output, $arr);
        }
        if (!$arr) {
            echo "log deleted successfully";
        } else {
            echo "log can not be deleted ";
        }
    }

    public function systemOperation() {
        $data['menuDetail'] = $this->getMenuData();
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['clientid'] = $this->input->get('client');
        $data['serverdata'] = $this->reporting_model->getserverdata();
        $data['table'] = $this->db->query("SELECT * FROM `services` order by `services` ")->result();
        $data['page'] = 'viewSystem';
        $data['title'] = 'System Operation';
        $this->load->view('template', $data);
    }

    public function fileTransfer() {

        $this->load->library('sftp');

        $sftp_config['hostname'] = $this->input->get_post('host');
        $sftp_config['username'] = $this->input->get_post('uname');
        $sftp_config['password'] = $this->input->get_post('password');
        $sftp_config['debug'] = TRUE;

//		if ($this->debug == TRUE)
        // Actually try and connect to the remote server...
        if ($this->sftp->connect($sftp_config) == TRUE) {
            echo "yes";
        } else {
            echo "no";
        }
    }

    // server operation
    function serverOperation() {
        $server_action = $this->input->get_post('server_action');
        $username = $this->session->userdata('username');
        $date = date('Y-m-d');
        $time = date('h-i-sa');

        if ($server_action == "start") {
            $comment = "start";
            exec("reboot", $output, $retval);
            print_r($output);
            if (!$retval) {
                print_r($output);
                echo " server started successfully";
            } else {
                echo " server failed to start";
            }
        } else if ($server_action == "stop") {
            $comment = "shutdown";
            exec("stopserver", $output, $retval);
//            print_r($output);
            if (!$retval) {
                echo " server started successfully";
            } else {
                echo " server failed to stop";
            }
        }
    }

    // service operation
    public function serviceOperation() {

        $id = $this->input->get_post('id');
        $service_operation = $this->input->get_post('service_operation');
        $service_name = $this->input->get_post('service_name');
        $username = $this->session->userdata('username');
        $clientid = $this->input->get_post('clientid');
        $date = date('Y-m-d');
        $time = date('h-i-sa');
        $log_history = array(
            'feature_name' => $service_name,
            'service' => $service_operation,
            'edit_by' => $username,
            'date' => $date,
            'time' => $time,
            'client_id' => $clientid,
        );

        if ($service_operation == "start") {
            $comment = "start";
           /* $this->db->insert("logging_history ", $log_history);
            if ($service_name == 'all services') {
                $this->db->query("UPDATE `client_details` SET  `all` = 1  WHERE `id`='16'");
            } else {
                $this->db->query("UPDATE `client_details` SET  `$service_name` = 1  WHERE `id`='16'");
            }*/
            if ($service_name == 'all services') {
                exec("111StartVoitekkServices", $output, $retval);
                if (!$retval) {
                    //print_r($output);
                    echo $service_name . " services started successfully";
                } else {
                    echo $service_name . " services failed to start";
                }
            } else {
                $cmd = "serviceStart $service_name start";
                exec($cmd, $output, $retval);
                if (!$retval) {
                    echo $service_name . " services started successfully";
                } else {
                    echo $service_name . " services failed to start";
                }
            }
        } else if ($service_operation == "stop") {
            $comment = "stop";
/*            $this->db->insert("logging_history ", $log_history);
$this->db->query("UPDATE `client_details` SET  `$service_name` = 0  WHERE `id`='16'"); */
if ($service_name == 'all services') {
    exec("StopVoitekkServices", $output, $retval);
                //print_r($output);
    if (!$retval) {
        echo $service_name . " services stopped successfully";
    } else {
        echo $service_name . " services failed to stop";
    }
} else {
    $cmd = "serviceStart $service_name stop";
    exec($cmd, $output, $retval);
    if (!$retval) {
        echo "Service stop successfully";
    } else {
        echo "Service not stop successfully";
    }
}
}
}

public function getQualityParameters() {
    $allowQualityManage = $this->input->post('allowQualityManage');
    $processId = $this->input->post('processId');
    $getQualityParameters = $this->process_model->getQualityParameters($processId, $allowQualityManage);
    $json_result = json_encode($getQualityParameters);
    echo $json_result;
}

public function editQualityParameterTable() {
    if ($this->input->post('action') == 'edit') {
        $arr = [];
        $id = $this->input->post('id');
        $prmtxt = $this->input->post('prmtxt');
        $desc = $this->input->post('desc');
        $weightage = $this->input->post('weightage');
        $critical = $this->input->post('critical');
        $active = $this->input->post('active');

        if (isset($prmtxt) && !empty($prmtxt) && !is_null($prmtxt)) {
            $arr['parameterText'] = $prmtxt;
        }
        if (isset($desc) && !empty($desc) && !is_null($desc)) {
            $arr['desc'] = $this->input->post('desc');
        }
        if (isset($weightage) && !empty($weightage) && !is_null($weightage)) {
            $arr['weightage'] = $weightage;
        }
        if (isset($critical) && !empty($critical) && !is_null($critical)) {
            $arr['critical'] = $critical;
        }
        if (isset($active) && !empty($active) && !is_null($active)) {
            $arr['active'] = $active;
        }

        $this->process_model->editQualityParameterTable($arr, $id);
    }
}

    //process extra column
function copyProcessExtraColumns() {
    $proceessIdTo = $this->input->post('proceessId');
    $campaignIdTo = $this->input->post('campaignId');
    $proceessIdFrom = $this->input->post('copyFrom');
    $proceessLeadJson = $this->db->query("SELECT `process_lead_json`  FROM `process` WHERE `id` = $proceessIdFrom ")->row();

    $processUpdate = $this->process_model->processLeadJsonUpdate($proceessIdTo, $proceessLeadJson->process_lead_json);
    if (isset($processUpdate) && !empty($processUpdate)) {
            //$data['alertsuccess'] = "Process Script json updated Successfully";
        $this->session->set_flashdata('success', "Process Extra Column json updated Successfully");
    } else {
        $this->session->set_flashdata('error', "Process Extra Column updated unsuccessfully");
            //$data['alerterror'] = "Process Script json updated unsuccessfully";
    }
    redirect("site/viewcampaign");
}

    // latest dashboard function changes  
public function latestDashboard() {
    $data['menuDetail'] = $this->getMenuData();
    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['luaOpt'] = $this->userStateChangeSet("latestDashboard");
    $data['editmode'] = "0";
        // $data[ 'page' ]   = 'dashboard';
    $data['page'] = 'latestDashboard';
    $data['title'] = 'Welcome';
    $data['campaign'] = $this->reporting_model->getcampaign();
    $data['process'] = $this->reporting_model->getprocess();
    $data['agent'] = $this->reporting_model->getagent();
    $data['TotalGraphData'] = $this->reporting_model->TotalGraphData();
        // $data['table']    = $this->reporting_model->dashboardRedisData();
    $this->load->view('template', $data);
}

public function callTest() {
    $data['menuDetail'] = $this->getMenuData();
    $data['luaOpt'] = $this->userStateChangeSet("callTest");
    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['editmode'] = "0";
    $data['page'] = 'callTest';
        // $data[ 'page' ]   = 'latestDashboard';
    $data['title'] = 'Welcome';
        // $data['table']    = $this->reporting_model->dashboardRedisData();
    $this->load->view('callTest', $data);
}

public function callingscreen() {

    $data['menuDetail'] = $this->getMenuData();
    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['luaOpt'] = $this->userStateChangeSet("callingscreen");

        // this code added for welcome cure click me error
        // if agent filter data in logger and click on calling screen
        // then sip not registed remove after check
        // $this->resetloggerForCallingScreen();

    $agentId = $this->session->userdata('id');
    $data['editmode'] = "0";
    $data['page'] = 'callingscreen';
    $data['title'] = 'Calling Screen';
    $data['callback'] = $this->callback_model->agentCallBackData($agentId);
    $data['callSetting'] = $this->config_model->checkCallSetting();
    $data['callFeature'] = $this->config_model->getCallingFeature();
    $data['leadset'] = $this->lead_model->getleadset();
    $data['Sms'] = $this->message_model->viewsms();
    $data['SystemApiJson'] = "";
    $data['SystemIfameJson'] = "";
    $data['processApiJsonArray'] = array();
    $data['processIframeJsonArray'] = array();
    $data['processSettingJson'] = array();



    $data['crmIdName'] = "CRM Id";
    $data['crmIdAllow'] = 0;
    $configArray = array(
        120 => (object) array('variableName' => 'crmIdAllow', 'defaultValue' => '0'),
        121 => (object) array('variableName' => 'crmIdName', 'defaultValue' => 'CRM Id'));
    $configData = $this->config_model->getMultipleConfig($configArray);
    if (isset($configData->crmIdAllow)) {

        $data['crmIdAllow'] = $configData->crmIdAllow;
    }

    if (isset($configData->crmIdName)) {

        $data['crmIdName'] = $configData->crmIdName;
    }

    $processData = $this->reporting_model->getprocess();
    $processCsv = "";
    foreach ($processData as $key => $value) {
        $processCsv .= ( $processCsv == "" ) ? $key : "," . $key;
    }
    $data['processCsv'] = $processCsv;

    $MenuApiData = $this->menu_model->getApiAndIFrameData();

    /*------------------------- enable disable api start-------------------------------- */
    if ($processCsv != "") {

        $data['processApiFlag'] = $this->process_model->getApiEnableFlag($processData);
        $data['processSystemApiFlag'] = $this->process_model->getSystemProcessApiEnableFlag($processData);
    }
    /*------------------------- enable disable api end-------------------------------- */

    /* ------------------------ crm login api creation ------------------------------- */

    $data['crmloginurl'] = "undefined";
    $campDetails = $this->process_model->getUserMappedCampaigns($this->session->userdata('id'));



    $data['generateCrmLoginConfAlert'] = "false";
    if (isset($campDetails) && count($campDetails) > 0) {

        if (count($campDetails) > 1) {
            foreach ($campDetails as $key => $value) {
                if (isset($value->crm_login_conf) && !empty($value->crm_login_conf) && $value->crm_login_conf !== 0) {
                    $data['generateCrmLoginConfAlert'] = "true";
                }
            }
        } else {
            $crmloginurl = "";
            $crmloginurlparams = "";

            if (isset($campDetails[0]->crm_login_conf) && !empty($campDetails[0]->crm_login_conf) && $campDetails[0]->crm_login_conf !== 0) {


                $confdetails = json_decode($campDetails[0]->crm_login_conf, true);

                if (count($confdetails) > 0 && isset($confdetails['loginurl'])) {

                    $crmloginurl .= $confdetails['loginurl'] . "?";

                    if (isset($confdetails['campaign_id']) && !empty($confdetails['campaign_id'])) {

                        if (isset($confdetails['params']) && count($confdetails['params']) > 0) {
                            foreach ($confdetails['params'] as $key => $value) {

                                $paramseparator = "";
                                if ($crmloginurlparams !== "") {
                                    $paramseparator = "&";
                                }

                                if ($value['param_type'] == 'custom') {
                                    $crmloginurlparams .= $paramseparator . $value['param_name'] . "=" . $value['param_value'];
                                }
                                if ($value['param_type'] == 'system') {
                                    if ($value['param_value'] == "agentusername") {
                                        $crmloginurlparams .= $paramseparator . $value['param_name'] . "=" . $this->session->userdata('username');
                                    }
                                    if ($value['param_value'] == "agentid") {
                                        $crmloginurlparams .= $paramseparator . $value['param_name'] . "=" . $this->session->userdata('id');
                                    }
                                    if ($value['param_value'] == "usertype") {
                                        $crmloginurlparams .= $paramseparator . $value['param_name'] . "=" . $this->session->userdata('accesslvlTxt');
                                    }
                                    if ($value['param_value'] == "ip") {
                                        $crmloginurlparams .= $paramseparator . $value['param_name'] . "=" . $this->session->userdata('ip_address');
                                    }
                                    if ($value['param_value'] == "port") {
                                        $crmloginurlparams .= $paramseparator . $value['param_name'] . "=" . $this->session->userdata('port');
                                    }
                                    if ($value['param_value'] == "sessionid") {
                                        $crmloginurlparams .= $paramseparator . $value['param_name'] . "=" . $this->session->userdata('session_id');
                                    }
                                    if ($value['param_value'] == "campaignid") {
                                        $crmloginurlparams .= $paramseparator . $value['param_name'] . "=" . $confdetails['campaign_id'];
                                    }
                                }
                            }
                        }

                        $crmloginurl = $crmloginurl . $crmloginurlparams;
                            //echo '<pre>';print_r($crmloginurl);exit;
                        $data['crmloginurl'] = $crmloginurl;
                    }
                }
//                echo '<pre>';
//                print_r($confdetails);
//                exit;
            }
        }
    }


    /**         * ******** crm login api creation end ************** */
    foreach ($MenuApiData as $key => $menuData) {

        if (isset($menuData->menu_location) && $menuData->menu_location == "site/systemApiIntegrationView") {

            $data['SystemApiJsonFlag'] = "TRUE";
            $data['SystemApiJson'] = $this->api_model->getTptSystemSetUpApi();

        } else if (isset($menuData->menu_location) && $menuData->menu_location == "form/viewSystemMenuTab") {

            $data['SystemIfameJsonFlag'] = "TRUE";
            $data['SystemIfameJson'] = $this->api_model->getTptSystemSetUpIframe();
        } else if (isset($menuData->menu_location) && $menuData->menu_location == "form/viewApiProcess") {

            if ($processCsv != "") {

                $apiData = $this->api_model->getTptCallSetUpApi($processCsv);

                if (isset($apiData->output) && $apiData->output == "TRUE" && isset($apiData->message) && isset($apiData->body) && $apiData->message != "NO_API_SET") {

                    $data['processApiJsonArrayFlag'] = "TRUE";
                    $data['processApiJsonArray'] = $apiData->body;
                }
            }
        } else if (isset($menuData->menu_location) && $menuData->menu_location == "form/viewSystemApiProcess") {
            if ($processCsv != "") {

                $apiData = $this->api_model->getTptCallSetUpIframe($processCsv);

                if (isset($apiData->output) && $apiData->output == "TRUE" && isset($apiData->message) && isset($apiData->body) && $apiData->message != "NO_API_SET") {
                    $data['processIframeJsonArrayFlag'] = "TRUE";
                    $data['processIframeJsonArray'] = $apiData->body;
                }
            }
        }
    }

    if ($processCsv != "") {
        $apiData = $this->api_model->getProcessSettingJson($processCsv);
        if (isset($apiData->output) && $apiData->output == "TRUE" && isset($apiData->message) && isset($apiData->body) && $apiData->message != "NO_API_SET") {
            $data['processSettingJson'] = $apiData->body;
        }
    }

    $data['processData'] = $processData;
    $accesslevel = $this->session->userdata("accesslevel");
    $userName = $this->session->userdata("username");
    if ($accesslevel == 4) {
        $data['buttonSetting'] = $this->user_model->getAgentSettings($agentId, $userName);
    } else {
        $data['buttonSetting'] = $this->user_model->getSuperAgentDefaultSettings($agentId, $userName);
    }

        // data for redial
    $callingFlag = $this->input->get_post("callingFlag");

    if ($callingFlag == "TRUE" && $this->session->userdata("sessionUUid") != $this->input->get_post('sessionUUid')) {

        $data['rd_callingFlag'] = $callingFlag;
        $data['rd_timestamp'] = $this->input->get_post('timestamp');
        $data['rd_leadId'] = $this->input->get_post('leadId');
        $data['rd_phone'] = $this->input->get_post('phone');
        $data['rd_customerName'] = $this->input->get_post('customerName');
        $data['rd_campaignId'] = $this->input->get_post('campaignId');
        $data['rd_campaignName'] = $this->input->get_post('campaignName');
        $data['rd_processId'] = $this->input->get_post('processId');
        $data['rd_processName'] = $this->input->get_post('processName');
        $data['rd_leadsetId'] = $this->input->get_post('leadsetId');
        $data['rd_leadsetName'] = $this->input->get_post('leadsetName');
        $data['rd_dialFrom'] = $this->input->get_post('dialFrom');
        $data['sessionUUid'] = $this->input->get_post('sessionUUid');
        $newdata = array(
            'sessionUUid' => $data['sessionUUid']
        );
        $this->session->set_userdata($newdata);
    } else {
        $data['rd_callingFlag'] = "FALSE";
    }
    $this->menu_model->resertPageSession();
    $data['pausecode'] = $this->process_model->getpausecode();
    $data['pausetime'] = json_encode($this->process_model->getpauseTime());
        // echo "<pre>";print_r($data);exit;
    $this->load->view('template', $data);
}

public function headsetTesting() {
    $data['menuDetail'] = $this->getMenuData();
    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $this->userStateChangeSet("headsetTesting");
    $data['editmode'] = "0";
    $data['page'] = 'headsetTesting';
    $data['title'] = 'Headset Test';
    $this->load->view('template', $data);
}

public function updateEmailPassword() {
    $password = $this->input->post('enterPassword');
    $confirmpassword = $this->input->post('reEnterPassword');
    $messageSmtp = $this->message_model->updateEmailPassword($password, $confirmpassword);
    $data['other'] = "passwordOutput=" . $messageSmtp->output . "&passwordMessage=" . $messageSmtp->message;
    $data['redirect'] = "site/profile";
    $this->load->view("redirect", $data);
}

public function profile() {
    $data['menuDetail'] = $this->getMenuData();
    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $this->userStateChangeSet("profile");
    $data['editmode'] = "0";
    $data['page'] = 'profile';
    $data['title'] = 'View Profile';
    $data['table'] = $this->user_model->viewprofile();
    $user = $this->session->userdata('id');
    $data['userTable'] = $this->standard_model->fetch_user_email_detail($user);
    $this->load->view('template', $data);
}

public function createuser() {
    $data['menuDetail'] = $this->getMenuData();
    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $this->userStateChangeSet("createuser");
    $data['editmode'] = "0";
    $data['accesslevel'] = $this->user_model->getaccesslevels();
    $data['userSetting'] = $this->config_model->getUserSetting();
    $data['userNameArray'] = $this->standard_model->getUserNameArray();
    $data['extensionList'] = $this->extension_model->getRemainingExtn();
    $data['page'] = 'createuser';
    $data['title'] = 'Create User';
    $this->load->view('template', $data);
}

function createusersubmit() {
    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();
    $data['userNameArray'] = $this->standard_model->getUserNameArray();
    $this->form_validation->set_rules('fname', 'First Name', 'trim|alpha_numeric|required|max_length[30]');
    $this->form_validation->set_rules('lname', 'Last Name', 'trim|alpha_numeric|max_length[30]');
    $this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[1]|max_length[30]|alpha_numeric');
    $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]|max_length[30]');
    $this->form_validation->set_rules('confirmpassword', 'Confirm Password', 'trim|required|matches[password]');
    $this->form_validation->set_rules('accesslevel', 'Access Level', 'trim|required|required');
    $this->form_validation->set_rules('email', 'Email', 'trim|valid_email');
    $this->form_validation->set_rules('mobile', 'Mobile Number', 'trim');
    $this->form_validation->set_rules('address', 'Address', 'trim');
    $this->form_validation->set_rules('city', 'City', 'trim');
    $this->form_validation->set_rules('pincode', 'Pin code', 'trim');
    $this->form_validation->set_rules('nationality', 'Nationality', 'trim');
    $this->form_validation->set_rules('bloodgroup', 'Blood Group', 'trim');
    $this->form_validation->set_rules('qualification', 'Qualification', 'trim');
    $this->form_validation->set_rules('pan', 'PAN Card', 'trim');
    $this->form_validation->set_rules('dob', 'DOB', 'trim');
    if ($this->form_validation->run() == FALSE) {
        $data['alerterror'] = validation_errors();

        $data['accesslevel'] = $this->user_model->getaccesslevels();
        $data['status'] = $this->user_model->getstatusdropdown();
        $this->userStateChangeSet("Edit extension");
        $data['userSetting'] = $this->config_model->getUserSetting();
        $data['page'] = 'createuser';
        $data['title'] = 'Create New User';
        $this->load->view('template', $data);
    } else {
        $accesslevel = $this->input->post('accesslevel');
        if ($accesslevel == 2 || $accesslevel == 3) {
            if ($this->user_model->usercount($accesslevel) == 0) {
                $data['redirect'] = "site/viewusers";
                    //$data['other']="template=$template";
                $this->load->view("redirect", $data);
                $data['alerterror'] = 'Limit Exceeded.Please Contact Admin';
            }
        }
        $fname = $this->input->post('fname');
        $lname = $this->input->post('lname');
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $email = $this->input->post('email');
        $mobile = $this->input->post('mobile');
        $address = $this->input->post('address');
        $city = $this->input->post('city');
        $pincode = $this->input->post('pincode');
        $nationality = $this->input->post('nationality');
        $bloodgroup = $this->input->post('bloodgroup');
        $qualification = $this->input->post('qualification');
        $pan = $this->input->post('pan');
        $superAdminFlag = $this->input->post('superAdminFlag');
        $dob = $this->input->post('dob');
            // $extenstionType = $this->input->post('extenstionType');
        $extenstionType = "";
            // $extension      = $this->input->post('extension');
        $extension = "";
        $dob = date("Y-m-d", strtotime($dob));
        $gender = $this->input->post('gender');

        if ($this->user_model->create($fname, $lname, $username, $password, $accesslevel, $email, $mobile, $address, $city, $pincode, $nationality, $bloodgroup, $qualification, $pan, $dob, $gender, $extension, $extenstionType, $superAdminFlag) == 0)
            $data['alerterror'] = "New user could not be created.";
        else
            $data['alertsuccess'] = "User created Successfully.";
        $data['table'] = $this->user_model->viewusers();
        $data['redirect'] = "site/viewusers";
            //$data['other']="template=$template";
        $this->load->view("redirect", $data);
            /* $data['page']='viewusers';
              $data['title']='View Users';
              $this->load->view('template',$data); */
          }
      }

      function viewusers() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['externalAllow'] = $this->standard_model->checkFeature('3');
        $data['createUserApiAllow'] = $this->standard_model->createUserApiAllow();

        $this->userStateChangeSet("viewusers");
        $data['editmode'] = "0";

        $data['accesslevelArray'] = $this->user_model->getAllAccesslevels();
        $data['userSetting'] = $this->config_model->getUserSetting();
        $data['processDetail'] = $this->reporting_model->getProcessTotalDetail();
        $data['table'] = $this->user_model->viewusers();
        $data['page'] = 'viewusers';
        $data['title'] = 'View Users';
        $this->load->view('template', $data);
    }

    function edituser() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("edituser");
        $data['editmode'] = "1";
        $data['accesslevel'] = $this->user_model->getaccesslevels();
        $data['before'] = $this->user_model->beforeedit($this->input->get('id'));
        $data['userSetting'] = $this->config_model->getUserSetting();
        $data['page'] = 'edituser';
        $data['title'] = 'Edit User';
        $this->load->view('template', $data);
    }

    function editusersubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->form_validation->set_rules('fname', 'First Name', 'trim|required|max_length[30]');
        //$this->form_validation->set_rules('username','Username','trim|required|min_length[6]|max_length[30]|is_unique[user.username]');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]|max_length[30]');
        $this->form_validation->set_rules('confirmpassword', 'Confirm Password', 'trim|required|matches[password]');
        $this->form_validation->set_rules('accesslevel', 'Access Level', 'trim');
        $this->form_validation->set_rules('email', 'Email', 'trim|valid_email');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'trim');
        $this->form_validation->set_rules('address', 'Address', 'trim');
        $this->form_validation->set_rules('city', 'City', 'trim');
        $this->form_validation->set_rules('pincode', 'Pin code', 'trim');
        $this->form_validation->set_rules('nationality', 'Nationality', 'trim');
        $this->form_validation->set_rules('bloodgroup', 'Blood Group', 'trim');
        $this->form_validation->set_rules('qualification', 'Qualification', 'trim');
        $this->form_validation->set_rules('pan', 'PAN Card', 'trim');
        $this->form_validation->set_rules('dob', 'DOB', 'trim');

        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $data['accesslevel'] = $this->user_model->getaccesslevels();
            $data['userSetting'] = $this->config_model->getUserSetting();
            $data['before'] = $this->user_model->beforeedit($this->input->post('id'));
            $data['page'] = 'edituser';
            $data['title'] = 'Edit User';
            $this->load->view('template', $data);
        } else {
            $id = $this->input->post('id');
            $fname = $this->input->post('fname');
            $lname = $this->input->post('lname');
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            $accesslevel = $this->input->post('accesslevel');
            $email = $this->input->post('email');
            $mobile = $this->input->post('mobile');
            $address = $this->input->post('address');
            $city = $this->input->post('city');
            $pincode = $this->input->post('pincode');
            $nationality = $this->input->post('nationality');
            $bloodgroup = $this->input->post('bloodgroup');
            $qualification = $this->input->post('qualification');
            $pan = $this->input->post('pan');
            $gender = $this->input->post('gender');
            $dob = $this->input->post('dob');
            $oldAccessLevel = $this->input->post('oldAccessLevel');
            $extension = '';
            // $extension=$this->input->post('extension');
            $extenstionType = '';
            // $extenstionType = $this->input->post('extenstionType');
            $dob = date("Y-m-d", strtotime($dob));
            if ($this->user_model->edit($id, $fname, $lname, $password, $accesslevel, $email, $mobile, $address, $city, $pincode, $nationality, $bloodgroup, $qualification, $pan, $dob, $gender, $extension, $extenstionType, $oldAccessLevel) == 0)
                $data['alerterror'] = "User Editing was unsuccesful";
            else
                $data['alertsuccess'] = "User edited Successfully.";
            $data['table'] = $this->user_model->viewusers();
            $data['redirect'] = "site/viewusers";
            //$data['other']="template=$template";
            $this->load->view("redirect", $data);
            /* $data['page']='viewusers';
              $data['title']='View Users';
              $this->load->view('template',$data); */
          }
      }

      function deleteuser() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("deleteuser");
        $data['editmode'] = "0";
        $this->user_model->deleteuser($this->input->get('id'));
        // $data['table']=$this->user_model->viewusers();
        $data['alertsuccess'] = "User Deleted Successfully";

        $data['redirect'] = "site/viewusers";
        $this->load->view("redirect", $data);
    }

    function changeuserstatus() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $userId = $this->input->get('id');
        $status = $this->input->get('status');
        $this->user_model->changestatus($userId, $status);
        $data['alertsuccess'] = "Status Changed Successfully";
        $data['redirect'] = "site/viewusers";
        $this->load->view("redirect", $data);
    }

    public function uploaduser() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $data['page'] = 'uploaduser';
        $data['title'] = 'Upload User';
        $this->load->view('template', $data);
    }

    public function uploadusersubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        //uploading files
        $config['upload_path'] = './uploads/csv/';
        $config['allowed_types'] = '*';
        $config['max_size'] = 1024 * 8;
        $config['encrypt_name'] = TRUE;

        $this->load->library('upload', $config);
        $file_element_name = 'csv';
        $csvfile = "";
        if ($this->upload->do_upload($file_element_name)) {
            $uploaddata = $this->upload->data();
            $csvfile = $uploaddata['file_name'];
        }
        $this->load->library('csvreader');
        $filePath = 'uploads/csv/' . $csvfile;
//        $csvData = $this->csvreader->parse_file($filePath);
        
//        -----------------csv changes---------------
        $file = fopen($filePath, 'r');
        
        if(true) {
            $this->fields = fgetcsv($file, 4096, ',', '"');        
        }

        $csvData = false;
        $p_NamedFields = true;
        while( ($row = fgetcsv($file, 4096, ',', '"')) != false ) {            
            if( !$csvData ) {
                $csvData = array();
            }
            if( $p_NamedFields ) {
                $items = array();

                    // I prefer to fill the array with values of defined fields
                foreach( $this->fields as $id => $field ) {
                    if( isset($row[$id]) ) {
                        $items[$field] = $row[$id];    
                    }
                }
                $csvData[] = $items;
            } else {
                $csvData[] = $row;
            }
        }
        fclose($file);

        $result = $this->user_model->uploaduser($csvData);

//        if ($this->user_model->uploaduser($csvData) == 0)
//            $data['alerterror'] = "New User List could not be created.";
//        else
//            $data['alertsuccess'] = "User List created Successfully.";
        if(!empty($result->successmsg) ){
            $data['alertsuccess'] = $result->successmsg;
        }

        if(!empty($result->notPresentKeys)  || !empty($result->errormsg)){
            $data['alerterror'] = $result->notPresentKeys .  "   " .( $result->errormsg);           
        }
        $data['table'] = $this->user_model->viewusers();
        $data['redirect'] = "site/viewusers";
        //$data['other']="template=$template";
        $this->load->view("redirect", $data);
    }

    function userGroup() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("userGroup");
        $data['editmode'] = "0";
        $data['table'] = $this->user_model->viewusers();
        $data['page'] = 'viewUserGoup';
        $data['title'] = 'View Users';
        $this->load->view('template', $data);
    }

    public function changepasswordsubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $this->form_validation->set_rules('currentpassword', 'Current Password', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]|max_length[30]');
        $this->form_validation->set_rules('confirmpassword', 'Confirm Password', 'trim|required|matches[password]');
        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $data['table'] = $this->user_model->viewprofile();
            $data['page'] = "profile";
            $this->load->view('template', $data);
        } else {
            $currentpassword = $this->input->post('currentpassword');
            $password = $this->input->post('password');
            if ($this->user_model->validatecurrentpassword($currentpassword) == 1) {
                $data['alertsuccess'] = "Password changed successfully";
                $this->user_model->changepassword($password);
            } else {
                $data['alerterror'] = "Please Enter valid password";
            }
            $data['page'] = "profile";
            $data['table'] = $this->user_model->viewprofile();
            $data['redirect'] = "site/profile";
            //$data['other']="template=$template";
            $this->load->view("redirect", $data);
        }
    }

    //Process
    public function createprocess() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("createprocess");
        $data['editmode'] = "0";
        $data['status'] = $this->process_model->getstatus();
        $data['campaign'] = 0;
        $data['campaigns'] = $this->process_model->getcampaign();
        $data['calling_mode'] = $this->process_model->getcallingmode();
        $data['agent'] = $this->user_model->getagents();
        $data['page'] = 'createprocess';
        $data['title'] = 'Create Process';
        $this->load->view('template', $data);
    }

    function createprocesssubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->form_validation->set_rules('name', 'Name', 'trim|required|alpha_numeric');
        $this->form_validation->set_rules('description', 'description', 'trim');
        $this->form_validation->set_rules('sdate', 'Start Date', 'trim|required');
        $this->form_validation->set_rules('edate', 'End Date', 'trim|required');
        $this->form_validation->set_rules('starttime', 'Start Time', 'trim|required');
        $this->form_validation->set_rules('endtime', 'End Time', 'trim|required');
        $this->form_validation->set_rules('calling_mode', 'Calling Mode', 'trim');
        $this->form_validation->set_rules('campaign', 'Campaign', 'trim');
        $this->form_validation->set_rules('ratio', 'Ratio', 'trim');
        $this->form_validation->set_rules('countDown', 'countDown', 'trim');
        $this->form_validation->set_rules('leadSearch', 'leadSearch', 'trim');
        $this->form_validation->set_rules('leadSearch', 'leadSearch', 'trim');

        // process api copy flag
        $processApiCopyFlag = $this->input->post('processApiCopyFlag');
        $callingMode = $this->input->post('calling_mode');

        if ($processApiCopyFlag == "2" && $callingMode != 7 && $callingMode != 8) {

            $this->form_validation->set_rules('apiCopyProcess', 'Api Copy Process', 'required');
        }

        // process Ifram copy flag
        $processIframeCopyFlag = $this->input->post('processIframeCopyFlag');
        if ($processIframeCopyFlag == "2" && $callingMode != 7 && $callingMode != 8) {

            $this->form_validation->set_rules('iframeCopyProcess', 'Tab Copy Process', 'required');
        }

        $uniqueProcessName = $this->process_model->uniqueProcessName($this->input->post('campaign'), $this->input->post('name'));

        if ($this->form_validation->run() == FALSE || $uniqueProcessName == 1) {
            if ($uniqueProcessName == 1)
                $data['alerterror'] = "Process name must be unique in same campaign";
            else
                $data['alerterror'] = validation_errors();

            $data['campaigns'] = $this->process_model->getcampaign();
            $data['calling_mode'] = $this->process_model->getcallingmode();
            $data['other'] = "campaign=" . $this->input->post('campaign');
            $data['redirect'] = "site/viewcampaignprocess";
            $this->load->view("redirect2", $data);
        }
        else {
            $name = $this->input->post('name');
            $description = $this->input->post('description');
            $calling_mode = $this->input->post('calling_mode');
            $campaign = $this->input->post('campaign');
            $campaignName = $this->input->post('campaignName');
            $ratio = $this->input->post('ratio');
            $sdate = $this->input->post('sdate');
            $edate = $this->input->post('edate');
            $sdate = date("Y-m-d", strtotime($sdate));
            $edate = date("Y-m-d", strtotime($edate));
            $starttime = date("H:i", strtotime($this->input->post('starttime')));
            $starttime = $starttime . ":00";
            $starttime = date("H:i:s", strtotime($starttime));
            $endtime = date("H:i", strtotime($this->input->post('endtime')));
            $endtime = $endtime . ":00";
            $endtime = date("H:i:s", strtotime($endtime));
            $numberofcomments = $this->input->post('numberofcomments');
            $dispose = $this->input->post('dispose');
            $countDown = $this->input->post('countDown');
            $leadSearch = $this->input->post('leadSearch');
            $processPrefix = $this->input->post('processPrefix');
            $disposeProcess = $this->input->post('disposeProcess');

            if ($callingMode != 7 && $callingMode != 8) {

                $processApiCopyFlag = $this->input->post('processApiCopyFlag');
                $apiCopyProcess = $this->input->post('apiCopyProcess');
                $processIframeCopyFlag = $this->input->post('processIframeCopyFlag');
                $iframeCopyProcess = $this->input->post('iframeCopyProcess');
            } else {

                $processApiCopyFlag = "";
                $apiCopyProcess = "";
                $processIframeCopyFlag = "";
                $iframeCopyProcess = "";
            }

            $processCreateData = $this->process_model->create($name, $description, $calling_mode, $campaign, $sdate, $edate, $numberofcomments, $dispose, $ratio, $starttime, $endtime, $countDown, $leadSearch, $campaignName, $processPrefix, $disposeProcess, $processApiCopyFlag, $apiCopyProcess, $processIframeCopyFlag, $iframeCopyProcess);
            if ($processCreateData == 0) {

                $data['alerterror'] = "New Process could not be created.";
            } else {

                $data['alertsuccess'] = "process created Successfully.";
            }


            $data['table'] = $this->process_model->viewprocess();
            $data['other'] = "campaign=" . $this->input->post('campaign');
            $data['redirect'] = "site/viewcampaignprocess";
            $this->load->view("redirect2", $data);
        }
    }

    function viewprocess() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("viewprocess");
        $data['editmode'] = "0";
        $data['table'] = $this->process_model->viewprocess();
        $data['page'] = 'viewprocess';
        $data['title'] = 'View Categories';
        $this->load->view('template', $data);
    }

    function editprocess() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("editprocess");
        $data['editmode'] = "1";
        $data['status'] = $this->process_model->getstatus();
        $data['campaign'] = $this->process_model->getcampaign();
        $data['calling_mode'] = $this->process_model->getcallingmode();
        $data['before'] = $this->process_model->beforeedit($this->input->get('id'));
        $data['page'] = 'editprocess';
        $data['title'] = 'Edit process';
        $this->load->view('template', $data);
    }

    function editprocesssubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('description', 'description', 'trim');
        $this->form_validation->set_rules('sdate', 'Start Date', 'trim|required');
        $this->form_validation->set_rules('edate', 'End Date', 'trim|required');
        $this->form_validation->set_rules('starttime', 'Start Time', 'trim|required');
        $this->form_validation->set_rules('endtime', 'End Time', 'trim|required');
        $this->form_validation->set_rules('status', 'Status', 'trim');
        $this->form_validation->set_rules('campaign', 'Campaign', 'trim');
        $this->form_validation->set_rules('countDown', 'countDown', 'trim');
        $this->form_validation->set_rules('leadSearch', 'leadSearch', 'trim');
        $callingMode = $this->input->post('calling_mode');

        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $data['status'] = $this->process_model->getstatus();
            $data['campaign'] = $this->process_model->getcampaign();
            $data['calling_mode'] = $this->process_model->getcallingmode();
            $data['agent'] = $this->campaign_model->getagents();
            $data['before'] = $this->process_model->beforeedit($this->input->post('id'));
            $data['other'] = "campaign=" . $this->input->post('campaign') . "&id=" . $this->input->post('id');
            $data['redirect'] = "site/editcampaignprocess";
            $this->load->view("redirect2", $data);
        } else {
            $id = $this->input->post('id');
            $name = $this->input->post('name');
            $description = $this->input->post('description');
            $status = $this->input->post('status');
            $sdate = $this->input->post('sdate');
            $edate = $this->input->post('edate');
            $sdate = date("Y-m-d", strtotime($sdate));
            $edate = date("Y-m-d", strtotime($edate));
            $starttime = date("H:i", strtotime($this->input->post('starttime')));
            $starttime = $starttime . ":00";
            $starttime = date("H:i:s", strtotime($starttime));
            $endtime = date("H:i", strtotime($this->input->post('endtime')));
            $endtime = $endtime . ":00";
            $endtime = date("H:i:s", strtotime($endtime));
            $agents = $this->input->post('agent');
            $numberofcomments = $this->input->post('numberofcomments');
            $dispose = $this->input->post('dispose');
            $ratio = $this->input->post('ratio');
            $countDown = $this->input->post('countDown');
            $leadSearch = $this->input->post('leadSearch');
            $processPrefix = $this->input->post('processPrefix');
            $disposeProcess = $this->input->post('disposeProcess');

            if ($callingMode != 7 && $callingMode != 8) {

                $processApiCopyFlag = $this->input->post('processApiCopyFlag');
                $apiCopyProcess = $this->input->post('apiCopyProcess');
                $processIframeCopyFlag = $this->input->post('processIframeCopyFlag');
                $iframeCopyProcess = $this->input->post('iframeCopyProcess');
            } else {
                $processApiCopyFlag = 0;
                $apiCopyProcess = "";
                $processIframeCopyFlag = 0;
                $iframeCopyProcess = "";
            }
            $editProcessData = $this->process_model->edit($id, $name, $description, $sdate, $edate, $agents, $numberofcomments, $dispose, $ratio, $starttime, $endtime, $countDown, $leadSearch, $processPrefix, $disposeProcess, $processApiCopyFlag, $apiCopyProcess, $processIframeCopyFlag, $iframeCopyProcess);
            if ($editProcessData == 0) {

                $data['alerterror'] = "process Editing was unsuccesful";
            } else {

                $data['alertsuccess'] = "process edited Successfully.";
            }
            $data['table'] = $this->process_model->viewprocess();
            $data['other'] = "campaign=" . $this->input->post('campaign');
            $data['redirect'] = "site/viewcampaignprocess";
            $this->load->view("redirect2", $data);
        }
    }

    function deleteprocess() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("deleteprocess");
        $data['editmode'] = "0";
        $process = $this->input->get('id');
        $query = $this->db->query("SELECT `status`, `calling_mode` FROM `process` WHERE `id`='$process'")->row();
        if ($query->status > 0) {
            $data['alertsuccess'] = "Process can not be deleted ";
        } else {
            $this->process_model->deleteprocess($process, $query->calling_mode);
            $data['alertsuccess'] = "process Deleted Successfully";
        }
        $data['other'] = "campaign=" . $this->input->get('campaign');
        $data['redirect'] = "site/viewcampaignprocess";
        //$data['other']="template=$template";
        $this->load->view("redirect2", $data);
    }

    function changeprocessstatus() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        // $this->userStateChangeSet("changeprocessstatus");
        $data['editmode'] = "0";
        // echo $this->input->get('campaign');
        $this->process_model->changestatus($this->input->get('id'), $this->input->get('processStatus'));
        //$data['table']=$this->process_model->viewprocess();
        $data['alertsuccess'] = "Status Changed Successfully";
        $data['other'] = "campaign=" . $this->input->get('campaign');
        $data['redirect'] = "site/viewcampaignprocess";
        // print_r($data);
        //$data['other']="template=$template";
        $this->load->view("redirect2", $data);
    }

    //lead
    public function createlead() {
        $this->userStateChangeSet("createlead");
        $leadset = $this->input->get_post('leadset');
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $data['leadset'] = $leadset;

        $crmIdSaveAllow = 0;
        $configArray = array(120 => (object) array('variableName' => 'crmIdSaveAllow', 'defaultValue' => '0'));
        $configData = $this->config_model->getMultipleConfig($configArray);
        if (isset($configData->crmIdSaveAllow)) {

            $crmIdSaveAllow = $configData->crmIdSaveAllow;
        }

        $data['jsonlimit'] = $this->lead_model->getjsonlimit();
        $data['leadJson'] = $this->lead_model->getLeadsetJson($leadset);
        $data['leadsets'] = $this->lead_model->getleadset();
        $data['crmIdSaveAllow'] = $crmIdSaveAllow;
        $data['page'] = 'createlead';
        $data['title'] = 'Create lead';
        $this->load->view('template', $data);
    }

    function createleadsubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";

        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|valid_email');
        $this->form_validation->set_rules('phone', 'Phone', 'trim');
        $this->form_validation->set_rules('leadset', 'Leadset', 'trim');
        $this->form_validation->set_rules('crmId', 'crmId', 'trim');

        if ($this->form_validation->run() == FALSE) {

            $data['alerterror'] = validation_errors();
            $crmIdSaveAllow = 0;
            $configArray = array(120 => (object) array('variableName' => 'crmIdSaveAllow', 'defaultValue' => '0'));
            $configData = $this->config_model->getMultipleConfig($configArray);
            if (isset($configData->crmIdSaveAllow)) {

                $crmIdSaveAllow = $configData->crmIdSaveAllow;
            }
            $leadset = $this->input->post('leadset');
            $data['leadsets'] = $this->lead_model->getleadset();
            $data['jsonlimit'] = $this->lead_model->getjsonlimit();
            $data['leadJson'] = $this->lead_model->getLeadsetJson($leadset);
            $data['crmIdSaveAllow'] = $crmIdSaveAllow;
            $data['page'] = 'createlead';
            $data['title'] = 'Create lead';
            $this->load->view('template', $data);
        } else {

            $name = $this->input->post('name');
            $email = $this->input->post('email');
            $phone = $this->input->post('phone');
            $leadset = $this->input->post('leadset');
            $jsoncolumn = $this->input->post('jsoncolumn');
            $jsonvalue = $this->input->post('jsonvalue');
            $crmId = $this->input->post('crmId');
            $result = $this->lead_model->create($name, $email, $phone, $leadset, $jsoncolumn, $jsonvalue, $crmId);
            if ($result == 0) {

                $data['alerterror'] = "New lead could not be created.";
            } else {

                $data['alertsuccess'] = "lead created Successfully.";
            }

            $data['redirect'] = "site/viewleadbyleadset";
            $data['other'] = "leadset=" . $leadset;
            $this->load->view("redirect", $data);
        }
    }

    function viewlead() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("viewlead");
        $data['editmode'] = "0";
        $data['table'] = $this->lead_model->viewlead();
        $data['page'] = 'viewlead';
        $data['title'] = 'View lead';
        $this->load->view('template', $data);
    }

    function editlead() {
        $this->userStateChangeSet("editlead");
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "1";

        $crmIdSaveAllow = 0;
        $configArray = array(120 => (object) array('variableName' => 'crmIdSaveAllow', 'defaultValue' => '0'));
        $configData = $this->config_model->getMultipleConfig($configArray);

        if (isset($configData->crmIdSaveAllow)) {

            $crmIdSaveAllow = $configData->crmIdSaveAllow;
        }

        $data['before'] = $this->lead_model->beforeedit($this->input->get('id'));
        $data['jsonlimit'] = $this->lead_model->getjsonlimit();
        $data['leadset'] = $this->lead_model->getleadset();
        $data['crmIdSaveAllow'] = $crmIdSaveAllow;
        $data['page'] = 'editlead';
        $data['title'] = 'Edit lead';
        $this->load->view('template', $data);
    }

    function editleadsubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|valid_email');
        $this->form_validation->set_rules('phone', 'Phone', 'trim');
        $this->form_validation->set_rules('leadset', 'Leadset', 'trim');
        $this->form_validation->set_rules('crmId', 'crmId', 'trim');
        if ($this->form_validation->run() == FALSE) {

            $crmIdSaveAllow = 0;
            $configArray = array(120 => (object) array('variableName' => 'crmIdSaveAllow', 'defaultValue' => '0'));
            $configData = $this->config_model->getMultipleConfig($configArray);

            if (isset($configData->crmIdSaveAllow)) {

                $crmIdSaveAllow = $configData->crmIdSaveAllow;
            }
            $data['alerterror'] = validation_errors();
            $data['leadset'] = $this->lead_model->getleadset();
            $data['jsonlimit'] = $this->lead_model->getjsonlimit();
            $data['before'] = $this->lead_model->beforeedit($this->input->post('id'));
            $data['crmIdSaveAllow'] = $crmIdSaveAllow;
            $data['page'] = 'editlead';
            $data['title'] = 'Edit lead';

            $this->load->view('template', $data);
        } else {
            $id = $this->input->post('id');
            $name = $this->input->post('name');
            $email = $this->input->post('email');
            $phone = $this->input->post('phone');
            $leadset = $this->input->post('leadset');
            $leadsetName = $this->input->post('leadsetName');
            $jsoncolumn = $this->input->post('jsoncolumn');
            $jsonvalue = $this->input->post('jsonvalue');
            $crmId = $this->input->post('crmId');
            if ($this->lead_model->edit($id, $name, $email, $phone, $leadset, $leadsetName, $jsoncolumn, $jsonvalue, $crmId) == 0)
                $data['alerterror'] = "lead Editing was unsuccesful";
            else
                $data['alertsuccess'] = "lead edited Successfully.";
            //$data['table']=$this->lead_model->viewlead();
            $data['redirect'] = "site/viewleadbyleadset";
            $data['other'] = "leadset=" . $leadset;
            $this->load->view("redirect2", $data);
            /* $data['page']='viewusers';
              $data['title']='View Users';
              $this->load->view('template',$data); */
          }
      }

      function deletelead() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("deletelead");
        $data['editmode'] = "0";
        $leadId = $this->input->get('id');
        $leadsetId = $this->input->get('leadsetId');
        $this->lead_model->deletelead($leadId, $leadsetId);
        $data['alertsuccess'] = "lead Deleted Successfully";
        $data['redirect'] = "site/viewleadbyleadset";
        $data['other'] = "leadset=" . $this->input->get('leadset');
        $this->load->view("redirect2", $data);
    }

    public function createleadset() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['leadsetFlag'] = $this->input->get('leadsetFlag');
        $this->userStateChangeSet("createleadset");
        $data['editmode'] = "0";
        $data['page'] = 'createleadset';
        $data['title'] = 'Create leadset';
        $this->load->view('template', $data);
    }

    function createleadsetsubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['leadsetFlag'] = $this->input->get('leadsetFlag');

        $this->form_validation->set_rules('name', 'Name', 'trim|required|alpha_numeric|is_unique[leadset.name]');
        $this->form_validation->set_rules('description', 'description', 'trim');
        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $data['page'] = 'createleadset';
            $data['title'] = 'Create leadset';
            $this->load->view('template', $data);
        } else {
            $name = $this->input->post('name');
            $description = $this->input->post('description');
            if ($this->lead_model->createleadset($name, $description) == 0)
                $data['alerterror'] = "New leadset could not be created.";
            else
                $data['alertsuccess'] = "leadset created Successfully.";
            // $data['table']=$this->lead_model->viewleadset();
            if (isset($data['leadsetFlag']) && $data['leadsetFlag'] == "true") {
                $data['redirect'] = "site/viewLeadsetByAccessLevel";
            } else {

                $data['redirect'] = "site/viewleadset";
            }
            //$data['other']="template=$template";
            $this->load->view("redirect", $data);
        }
    }

    function viewDnc() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $accesslevel = $this->session->userdata('accesslevel');

        // if ( $this->session->userdata('dndViewConfig') == 1){
        //   if( ( ($accesslevel == 3 || $accesslevel == 10 ) && $this->session->userdata('dncAccess') == 1 )
        //     || $this->session->userdata('accesslevel') < 3) {
        $this->userStateChangeSet("viewDnc");
        $perPage = 50;
        $data['editmode'] = "0";

        $campaign = "";
        $process = "";
        $phoneno = "";
        $agent = "";
        $modeOfCalling = "";
        $dncType = "";
        $date = "";
        $dateto = "";

        $campaign = $this->input->post('campaign');
        $process = $this->input->post('process');
        $phoneno = $this->input->post('phoneno');
        $modeOfCalling = $this->input->post('modeOfCalling');
        $dncType = $this->input->post('dncType');
        $mydate = $this->input->post('date');
        $mydateto = $this->input->post('dateto');
        $agent = $this->input->get_post('agent');


        if ($mydate == "") {

        } else {
            $a = explode('-', $mydate);
            $result = $a[2] . '-' . $a[0] . '-' . $a[1];
            $mydate = $result;
        }

        if ($mydateto != "") {

            $a = explode('-', $mydateto);
            $result = $a[2] . '-' . $a[0] . '-' . $a[1];
            $mydateto = $result;
        }

        $data['campaign'] = $this->reporting_model->getcampaign();
        $campaignProcessData = $this->process_model->getLiveProcess();
        $data['process'] = $campaignProcessData->process;
        $data['agent'] = $this->reporting_model->getagent();
        $data['selectedagent'] = $agent;
        $data['selectedprocess'] = $process;
        $data['selectedcampaign'] = $campaign;
        $data['selectedphoneno'] = $phoneno;
        $data['selectedModeOfCalling'] = $modeOfCalling;
        $data['selectedDncType'] = $dncType;

        if ($mydate != "")
            $data['selecteddate'] = date("m-d-Y", strtotime($mydate));
        else
            $data['selecteddate'] = "";
        if ($mydateto != "")
            $data['selecteddateto'] = date("m-d-Y", strtotime($mydateto));
        else
            $data['selecteddateto'] = "";
        //$data['table']=$this->cdr_model->standardrecordinglogdetail($agent,$campaign,$process,$mydate,$rating,$talktime,$phoneno);

        $this->load->library("pagination");
        $config = array();
        $config['page_query_string'] = FALSE;
        $config['base_url'] = site_url("site/viewdnc");
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $data["table"] = $this->dnc_model->getDncDetail($perPage, $page, $campaign, $process, $phoneno, $modeOfCalling, $dncType, $mydate, $mydateto, $agent);
        if (!empty($data["table"])) {
            $config["total_rows"] = ($this->uri->segment(3)) ? $this->uri->segment(3) + 51 : 51;
        } else {
            $config["total_rows"] = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        }
        $config["per_page"] = $perPage;
        $config["uri_segment"] = 3;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_tag_open'] = '<li class="nextPage">';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li class="previousPage">';
        $config['prev_tag_close'] = '</li>';
        $config['display_pages'] = FALSE;
        $config['first_link'] = FALSE;
        $config['last_link'] = FALSE;
        $this->pagination->initialize($config);

        $data["links"] = $this->pagination->create_links();
        $limit = $page;
        if ($page == 0) {
            $limit = $perPage;
            $start = 0;
        } else {
            $start = $limit;
            $limit = $perPage;
        }
        $data['limit'] = $limit;
        $data['start'] = $start;

        $data['page'] = "viewDnc";
        $data['title'] = "Dnc Detail";
        $this->load->view('template', $data);
        //   }
        //   else {
        //       $data[ 'alertwarning' ] = "You don't have access to view Dnc";
        //       $data[ 'page' ] = 'dashboard';
        //       $data[ 'title' ] = 'Welcome';
        //       $data['redirect']="site";
        //       $this->load->view( 'redirect' , $data );    
        //   }
        // }
        // else {
        //       $data[ 'alertwarning' ] = "You don't have access to view Dnc";
        //       $data[ 'page' ] = 'dashboard';
        //       $data[ 'title' ] = 'Welcome';
        //       $data['redirect']="site";
        //       $this->load->view( 'redirect' , $data ); 
        // }
    }

    function reserDncFilter() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->session->unset_userdata('dnc_campaign');
        $this->session->unset_userdata('dnc_process');
        $this->session->unset_userdata('dnc_phoneno');
        $this->session->unset_userdata('dnc_modeOfCalling');
        $this->session->unset_userdata('dnc_dncType');
        $this->session->unset_userdata('dnc_date');
        $this->session->unset_userdata('dnc_dateto');
        $this->session->unset_userdata('dnc_agent');
        // $data['message']="1";
        // $this->load->view('json',$data);
        $data['redirect'] = "site/viewDnc";
        $this->load->view('redirect', $data);
    }

    // function editleadset()
    // {
    //   $this->userStateChangeSet("editleadset");
    //   $data['editmode']="1";
    //   $data['before']=$this->lead_model->beforeeditleadset($this->input->get('id'));
    //   $data['page']='editleadset';
    //   $data['title']='Edit leadset';
    //   $this->load->view('template',$data);
    // }
    // function editleadsetsubmit()
    // {
    //   $id=$this->input->post('id');
    //   $query=$this->db->query("SELECT `name` FROM `leadset` WHERE `id`='$id'")->row();
    //   if($query->name == $this->input->post('name')) {}
    //   else
    //     $this->form_validation->set_rules('name','Name','trim|required|is_unique[leadset.name]');
    //   $this->form_validation->set_rules('description','description','trim');
    //   if($this->form_validation->run() == FALSE)  
    //   {
    //     $data['alerterror'] = validation_errors();
    //     $data['before']=$this->lead_model->beforeeditleadset($this->input->post('id'));
    //     $data['page']='editleadset';
    //     $data['title']='Edit leadset';
    //     $this->load->view('template',$data);
    //   }
    //   else
    //   {
    //     $id=$this->input->post('id');
    //     $name=$this->input->post('name');
    //     $description=$this->input->post('description');
    //     if($this->lead_model->editleadset($id,$name,$description)==0)
    //     $data['alerterror']="lead Editing was unsuccesful";
    //     else
    //     $data['alertsuccess']="lead edited Successfully.";
    //     $data['table']=$this->lead_model->viewleadset();
    //     // $data['redirect']="site/viewleadset";
    //     // //$data['other']="template=$template";
    //     // $this->load->view("redirect",$data);
    //     $data['page']='viewleadset';
    //     $data['title']='View leadset';
    //     $this->load->view('template',$data);
    //   }
    // }
    // function deleteleadset()
    // {
    //   $this->userStateChangeSet("deleteleadset");
    //   $this->lead_model->deleteleadset($this->input->get('id'));
    //   $data['table']=$this->lead_model->viewleadset();
    //   $data['alertsuccess']="lead Deleted Successfully";
    //   $data['page']='viewleadset';
    //   $data['title']='View Leadset';
    //   $this->load->view('template',$data);
    // }

    function viewleadset() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("viewleadset");
        $data['editmode'] = "0";
        $data['table'] = $this->lead_model->viewleadset();
        $data['page'] = 'viewleadset';
        $data['title'] = 'View Leadset';
        $this->load->view('template', $data);
    }

    function viewLeadsetByAccesslevel() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("viewleadset");
        $data['editmode'] = "0";
        $data['table'] = $this->lead_model->viewLeadsetByAccessLevel();
        $data['page'] = 'viewLeadsetByAccesslevel';
        $data['title'] = 'View Leadset';
        $this->load->view('template', $data);
    }

    function editleadset() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("editleadset");
        $data['editmode'] = "1";
        $data['before'] = $this->lead_model->beforeeditleadset($this->input->get('id'));
        $data['page'] = 'editleadset';
        $data['title'] = 'Edit leadset';
        $this->load->view('template', $data);
    }

    function editleadsetsubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $id = $this->input->post('id');
        $query = $this->db->query("SELECT `name` FROM `leadset` WHERE `id`='$id'")->row();
        if ($query->name == $this->input->post('name')) {

        } else
        $this->form_validation->set_rules('name', 'Name', 'trim|required|is_unique[leadset.name]');

        $this->form_validation->set_rules('description', 'description', 'trim');
        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $data['before'] = $this->lead_model->beforeeditleadset($this->input->post('id'));
            $data['page'] = 'editleadset';
            $data['title'] = 'Edit leadset';
            $this->load->view('template', $data);
        } else {
            $id = $this->input->post('id');
            $name = $this->input->post('name');
            $description = $this->input->post('description');
            if ($this->lead_model->editleadset($id, $name, $description) == 0)
                $data['alerterror'] = "lead Editing was unsuccesful";
            else
                $data['alertsuccess'] = "lead edited Successfully.";
            $data['table'] = $this->lead_model->viewleadset();
            // $data['redirect']="site/viewleadset";
            // //$data['other']="template=$template";
            // $this->load->view("redirect",$data);
            $data['page'] = 'viewleadset';
            $data['title'] = 'View leadset';
            $this->load->view('template', $data);
        }
    }

    function deleteleadset() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $leadsetFlag = $this->input->get('leadsetFlag');
        $this->userStateChangeSet("deleteleadset");
        $this->lead_model->deleteleadset($this->input->get('id'));
        $data['table'] = $this->lead_model->viewleadset();
        $data['alertsuccess'] = "lead Deleted Successfully";
        //$data['other']="template=$template";
        if (isset($leadsetFlag) && $leadsetFlag == "true") {

            $data['redirect'] = 'site/viewLeadsetByAccessLevel';
        } else {
            $data['redirect'] = 'site/viewleadset';
        }
        $this->load->view("redirect", $data);
    }

    //campaign
    public function createcampaign() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("createcampaign");
        $data['editmode'] = "0";
        $data['status'] = $this->campaign_model->getstatus();
        $data['supervisor'] = $this->campaign_model->getsupervisor();
        $data['dispose'] = $this->campaign_model->getdispose();
        $data['page'] = 'createcampaign';
        $data['title'] = 'Create Campaign';
        $this->load->view('template', $data);
    }

    function createcampaignsubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $this->form_validation->set_rules('name', 'Name', 'trim|required|alpha_numeric|is_unique[campaign.name]');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('client', 'Client', 'trim|required');
        $this->form_validation->set_rules('supervisor', 'Supervisor', 'required');
        $this->form_validation->set_rules('leadSearch', 'leadSearch', 'required');
//    $this->form_validation->set_rules('dispose','dispose','trim');
        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $data['status'] = $this->campaign_model->getstatus();
            $data['supervisor'] = $this->campaign_model->getsupervisor();
            $data['dispose'] = $this->campaign_model->getdispose();
            $data['page'] = 'createcampaign';
            $data['title'] = 'Create New Campaign';
            $this->load->view('template', $data);
        } else {
            $name = $this->input->post('name');
            $description = $this->input->post('description');
            $client = $this->input->post('client');
            $supervisor = $this->input->post('supervisor');
            $leadSearch = $this->input->post('leadSearch');
            $processPrefix = $this->input->post('processPrefix');
            //$dispose=$this->input->post('dispose');
            $campaign = $this->campaign_model->create($name, $description, $client, $supervisor, $leadSearch, $processPrefix);
            if ($campaign == 0)
                $data['alerterror'] = "Campaign could not be created";
            else
                $data['alertsuccess'] = "Campaign created Successfully";
            $data['table'] = $this->campaign_model->viewcampaign();
            $data['redirect'] = "site/viewcampaignprocess";
            $data['other'] = "campaign=" . $campaign;
            $this->load->view("redirect", $data);
        }
    }

    function viewcampaign() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("viewcampaign");
        $data['editmode'] = "0";
        $data['callerIdSetting'] = $this->config_model->callerIDSetting();
        $data['table'] = $this->campaign_model->viewcampaign();
        $data['page'] = 'viewcampaign';
        $data['title'] = 'View Campaign';
        $this->load->view('template', $data);
    }

    function editcampaign() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("editcampaign");
        $data['editmode'] = "1";
        $data['status'] = $this->campaign_model->getstatus();
        $data['supervisor'] = $this->campaign_model->getsupervisor();
        $data['dispose'] = $this->campaign_model->getdispose();
        $data['before'] = $this->campaign_model->beforeedit($this->input->get('id'));
        $data['selectedSupervisor'] = $this->campaign_model->selectedSupervisor($this->input->get('id'));
        $data['page'] = 'editcampaign';
        $data['title'] = 'Edit Campaign';
        $this->load->view('template', $data);
    }

    function editcampaignsubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $id = $this->input->post('id');
        $query = $this->db->query("SELECT `name` FROM `campaign` WHERE `id`='$id'")->row();
        if ($query->name == $this->input->post('name')) {

        } else
        $this->form_validation->set_rules('name', 'Name', 'trim|required|is_unique[campaign.name]');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('client', 'Client', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            // $data['status']=$this->campaign_model->getstatus();
            $data['supervisor'] = $this->campaign_model->getsupervisor();
            $data['dispose'] = $this->campaign_model->getdispose();
            $data['before'] = $this->campaign_model->beforeedit($this->input->post('id'));

            $data['redirect'] = "site/viewcampaignprocess";
            $data['other'] = "campaign=" . $this->input->post('id');
            $this->load->view("redirect2", $data);
        } else {
            $id = $this->input->post('id');
            $name = $this->input->post('name');
            $description = $this->input->post('description');
            $client = $this->input->post('client');
            // $status=$this->input->post('status');
            $supervisor = $this->input->post('supervisor');
            $leadSearch = $this->input->post('leadSearch');
            $processPrefix = $this->input->post('processPrefix');

            //$dispose=$this->input->post('dispose');
            if ($this->campaign_model->edit($id, $name, $description, $client, $supervisor, $leadSearch, $processPrefix) == 0)
                $data['alerterror'] = "Campaign Editing was unsuccesful";
            else
                $data['alertsuccess'] = "Campaign edited Successfully.";
            $data['table'] = $this->campaign_model->viewcampaign();
            $data['redirect'] = "site/viewcampaignprocess";
            $data['other'] = "campaign=" . $id;
            $this->load->view("redirect2", $data);
            /* $data['page']='viewusers';
              $data['title']='View Users';
              $this->load->view('template',$data); */
          }
      }

      function deletecampaign() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("deletecampaign");
        $campaign = $this->input->get('id');
        $query = $this->db->query("SELECT COUNT(*) as `cnt` FROM `process` WHERE `campaign`='$campaign'")->row();
        $this->campaign_model->deletecampaign($this->input->get('id'));
        $data['alertsuccess'] = "Campaign Deleted Successfully";

        $data['table'] = $this->campaign_model->viewcampaign();
        $data['page'] = 'viewcampaign';
        $data['title'] = 'View Campaign';
        $this->load->view('template', $data);
    }

    public function createdispose() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("createdispose");
        $data['editmode'] = "0";
        $data['page'] = 'createdispose';
        $data['title'] = 'Create dispose';
        $this->load->view('template', $data);
    }

    function createdisposesubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();

        $this->form_validation->set_rules('name', 'Name', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();

            $data['page'] = 'createdispose';
            $data['title'] = 'Create New Dispose';
            $this->load->view('template', $data);
        } else {
            $name = $this->input->post('name');

            if ($this->dispose_model->createdispose($name) == 0)
                $data['alerterror'] = "New Dispose could not be created.";
            else
                $data['alertsuccess'] = "Dispose created Successfully.";
            $data['table'] = $this->dispose_model->viewdispose();
            $data['redirect'] = "site/viewdispose";
            $this->load->view("redirect", $data);
        }
    }

    function viewdispose() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("viewdispose");
        $data['editmode'] = "0";
        $data['table'] = $this->dispose_model->viewdispose();
        $data['page'] = 'viewdispose';
        $data['title'] = 'View dispose';
        $this->load->view('template', $data);
    }

    // function editdispose()
    // { 
    //   $this->userStateChangeSet("editdispose");
    //   $data['editmode']="1";
    //   $data['before']=$this->dispose_model->beforeedit($this->input->get('id'));
    //   $data['page']='editdispose';
    //   $data['title']='Edit Dispose';
    //   $this->load->view('template',$data);
    // }
    // function editdisposesubmit()
    // {
    //   $this->form_validation->set_rules('name','Name','trim|required|min_length[6]|max_length[30]');
    //   if($this->form_validation->run() == FALSE)  
    //   {
    //     $data['alerterror'] = validation_errors();
    //     $data['before']=$this->dispose_model->beforeedit($this->input->post('id'));
    //     $data['page']='createdispose';
    //     $data['title']='Create New Dispose';
    //     $this->load->view('template',$data);
    //   }
    //   else
    //   {
    //     $name=$this->input->post('name');
    //     $id=$this->input->post('id');
    //     if($this->dispose_model->edit($id,$name)==0)
    //     $data['alerterror']="Dispose Editing was unsuccesful";
    //     else
    //     $data['alertsuccess']="Dispose edited Successfully.";
    //     $data['table']=$this->dispose_model->viewdispose();
    //     $data['redirect']="site/viewdispose";
    //     //$this->load->view("redirect2",$data);
    //     $data['page']='viewdispose';
    //     $data['title']='View Dispose';
    //     $this->load->view('template',$data);
    //   }
    // }
    function deletedispose() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("view dispose");
        $this->dispose_model->deletedispose($this->input->get('id'));
        $data['table'] = $this->dispose_model->viewdispose();
        $data['alertsuccess'] = "dispose Deleted Successfully";
        $data['page'] = 'viewdispose';
        $data['title'] = 'View dispose';
        $this->load->view('template', $data);
    }

    function viewSms() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("viewSms");
        $data['editmode'] = "0";
        $data['table'] = $this->message_model->viewsms();
        if ($data['table'] == "FALSE") {
            $data['alerterror'] = $data['table']->message;
        }
        $data['page'] = 'viewSms';
        $data['title'] = 'View sms';
        $this->load->view('template', $data);
    }

    function createSms() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("createSms");
        $data['editmode'] = "0";
        $data['page'] = 'createSms';
        $data['title'] = 'Create SMS';
        $this->load->view('template', $data);
    }

    function createSmsSubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->form_validation->set_rules('heading', 'Heading', 'trim|required');
        $this->form_validation->set_rules('text', 'Text', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();

            $data['page'] = 'createSms';
            $data['title'] = 'Create New Sms';
            $this->load->view('template', $data);
        } else {
            $heading = $this->input->post('heading');
            $text = $this->input->post('text');
            $result = $this->message_model->createSmsSubmit($heading, $text);
            if ($result->output == "FALSE") {
                $data['alerterror'] = "New Sms could not be created.";
            } else
            $data['alertsuccess'] = "Sms created Successfully.";
            $data['redirect'] = "site/viewSms";
            $this->load->view("redirect", $data);
        }
    }

    function editSms() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("editSms");
        $data['editmode'] = "1";
        $data['before'] = $this->message_model->beforeeditSms($this->input->get('id'));
        $data['page'] = 'editSms';
        $data['title'] = 'Edit Sms';
        $this->load->view('template', $data);
    }

    function editSmsSubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->form_validation->set_rules('heading', 'Heading', 'trim|required');
        $this->form_validation->set_rules('text', 'Text', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $data['before'] = $this->message_model->beforeeditSms($this->input->post('id'));
            $data['page'] = 'editSms';
            $data['title'] = 'Editing Sms';
            $this->load->view('template', $data);
        } else {
            $heading = $this->input->post('heading');
            $text = $this->input->post('text');
            $id = $this->input->post('id');
            $result = $this->message_model->editSms($id, $heading, $text);
            if ($result->output == "FALSE") {
                $data['alerterror'] = "Sms Editing was unsuccesful";
            } else {
                $data['alertsuccess'] = "Sms edited Successfully.";
            }
            $data['table'] = $this->message_model->viewSms();
            $data['redirect'] = "site/viewSms";
            $this->load->view("redirect2", $data);
        }
    }

    function deleteSms() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("view dispose");
        $this->message_model->deleteSms($this->input->get('id'));
        ;
        $data['table'] = $this->message_model->viewSms();
        $data['redirect'] = "site/viewSms";
        $this->load->view("redirect2", $data);
    }

    function changecampaignstatus() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $campaignId = $this->input->get('campaign');
        $campaignStatus = $this->input->get('campaignStatus');
        $this->userStateChangeSet("View Campagin");
        $data['editmode'] = "0";
        $this->campaign_model->changestatus($campaignId, $campaignStatus);
        $data['callerIdSetting'] = $this->config_model->callerIDSetting();
        $data['table'] = $this->campaign_model->viewcampaign();
        $data['alertsuccess'] = "Status Changed Successfully";
        $data['page'] = 'viewcampaign';
        $data['title'] = 'View Campagin';
        $this->load->view('template', $data);
    }

    //extension
    public function createextension() {
        $data['menuDetail'] = $this->getMenuData();
        $extensionLevel = $this->input->get_post('extensionlevel');
        $data['extensionLevel'] = $extensionLevel;
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        // if($this->extension_model->extensioncount()==0)
        // {
        //   $this->userStateChangeSet("dashboard");
        //   $data[ 'page' ] = 'dashboard';
        //   $data[ 'title' ] = 'Welcome';
        //   $data['alerterror']='Limit Exceeded.Please Contact Admin';
        //   $this->load->view( 'template', $data ); 
        // }
        // else
        // {
        $this->userStateChangeSet("Create extension");
        $data['remainingExtn'] = $this->extension_model->getRemainingExtnDetail($extensionLevel);
        $data['accesslevel'] = $this->user_model->getExtensionLevel();
        $data['page'] = 'createextension';
        $data['title'] = 'Create extension';
        $this->load->view('template', $data);
        // }
    }

    function createextensionsubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->form_validation->set_rules('value', 'Value', 'required|greater_than[0]');
        $this->form_validation->set_rules('numExtn', 'Number Of extension', 'required|greater_than[0]');
        $this->form_validation->set_rules('type', 'Type', 'trim');
        $accesslevel = $this->input->post('accesslevel');

        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $data['extensionLevel'] = $accesslevel;
            $data['remainingExtn'] = $this->extension_model->getRemainingExtnDetail($accesslevel);
            $data['accesslevel'] = $this->user_model->getExtensionLevel();
            $data['page'] = 'createextension';
            $data['title'] = 'Create extension';
            $this->load->view('template', $data);
        } else {
            $value = $this->input->post('value');
            $type = $this->input->post('type');
            $numExtn = $this->input->post('numExtn');

            $user = $this->session->userdata('id');
            if ($this->extension_model->create($value, $type, $numExtn, $accesslevel, $user) == 0)
                $data['alerterror'] = "New extension could not be created.";
            else
                $data['alertsuccess'] = "extension created Successfully.";

            $data['other'] = "extensionlevel=$accesslevel";
            $data['redirect'] = "site/viewextension";
            $this->load->view("redirect", $data);
        }
    }

    function extensionlevel() {
        $data['menuDetail'] = $this->getMenuData();
        $data['table'] = $this->extension_model->getextensionlevel();
        $data['page'] = 'extensionLevel';
        $data['title'] = '';
        $this->load->view("template", $data);
    }

    function viewextension() {
        $extensionlevel = $this->input->get_post('extensionlevel');
        $data['remainingExtn'] = $this->extension_model->getRemainingExtnDetail($extensionlevel);
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $data['extensionlevel'] = $extensionlevel;
        $this->userStateChangeSet("View extension");
        $data['table'] = $this->extension_model->viewextension($extensionlevel);
        $data['page'] = 'viewextension';
        $data['title'] = 'View extension';
        $this->load->view('template', $data);
    }

    function editextension() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();

        $data['editmode'] = "1";
        $data['before'] = $this->extension_model->beforeedit($this->input->get('id'));
        $this->userStateChangeSet("Edit extension");
        $data['page'] = 'editextension';
        $data['title'] = 'Edit extension';
        $this->load->view('template', $data);
    }

    function editextensionsubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->form_validation->set_rules('value', 'Value', 'trim');
        $this->form_validation->set_rules('type', 'type', 'trim');

        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $data['before'] = $this->extension_model->beforeedit($this->input->post('id'));
            $this->userStateChangeSet("Edit extension");
            $data['page'] = 'editextension';
            $data['title'] = 'Edit extension';
            $this->load->view('template', $data);
        } else {
            $id = $this->input->post('id');
            $value = $this->input->post('value');
            $type = $this->input->post('type');
            $user = $this->session->userdata('id');
            if ($this->extension_model->edit($id, $value, $type, $user) == 0)
                $data['alerterror'] = "Extension Editing was unsuccesful";
            else
                $data['alertsuccess'] = "Extension edited Successfully.";
            $data['table'] = $this->extension_model->viewextension();
            $data['redirect'] = "site/viewextension";
            //$data['other']="template=$template";
            $this->load->view("redirect", $data);
            /* $data['page']='viewusers';
              $data['title']='View Users';
              $this->load->view('template',$data); */
          }
      }

      function deleteextension() {
        // $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $value = $this->input->get('value');
        $extensionlevel = $this->input->get('extensionlevel');
        $this->extension_model->delete($value, $extensionlevel);
        $data['alertsuccess'] = "Extension Deleted Successfully";
        $data['other'] = "extensionlevel=$extensionlevel";
        $data['redirect'] = "site/viewextension";
        $this->load->view("redirect", $data);
        // $this->userStateChangeSet("Edit extension");
        // $data['page']='viewextension';
        // $data['title']='View extension';
        // $this->load->view('template',$data);
    }

    function getleadbynumber() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();

        $callnumber = $this->input->get_post('callnumber');

        $data['message'] = $this->lead_model->getleadbynumber($callnumber);

        $this->load->view('json', $data);
    }

    public function createleadbyleadset() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['table'] = $this->lead_model->viewleadbyleadset($this->input->get('leadset'));
        $data['page'] = 'createleadbyleadset';
        $data['title'] = 'Create lead';
        $this->load->view('template', $data);
    }

    public function createleadbyleadsetsubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|valid_email');
        $this->form_validation->set_rules('phone', 'Phone', 'trim');
        $this->form_validation->set_rules('leadset', 'Leadset', 'trim');
        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $data['leadset'] = $this->lead_model->getleadset();
            $data['redirect'] = "site/createleadbyleadset";
            $data['other'] = "leadset=" . $this->input->post('leadset');
            $this->load->view("redirect2", $data);
            //$this->load->view( 'template', $data ); 
        } else {
            $name = $this->input->post('name');
            $email = $this->input->post('email');
            $phone = $this->input->post('phone');
            $leadset = $this->input->post('leadset');
            $jsoncolumn = $this->input->post('jsoncolumn');
            $jsonvalue = $this->input->post('jsonvalue');
            if ($this->lead_model->create($name, $email, $phone, $leadset, $jsoncolumn, $jsonvalue) == 0)
                $data['alerterror'] = "New lead could not be created.";
            else
                $data['alertsuccess'] = "lead created Successfully.";
            $data['table'] = $this->lead_model->viewleadbyleadset($leadset);
            $data['redirect'] = "site/createleadbyleadset";
            $data['other'] = "leadset=" . $leadset;
            $this->load->view("redirect2", $data);
        }
    }

    public function viewcampaignprocess() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $data['checkFeatureAllow'] = $this->standard_model->checkFeatureAllow('1', 'multilevel_dispose_allow');
        
        $data['copyFromQualityCampaign'] = $this->campaign_model->copyFromQualityCampaign();
        // process detail set
        $data['leadset'] = $this->process_model->getleadset();
        $data['processstatus'] = $this->process_model->getstatus();
        $data['calling_mode'] = $this->process_model->getcallingmode();
        $data['processConfig'] = $this->config_model->getProcessConfig();
        $data['campaignid'] = $this->input->get('campaign');
        $data['callerIdSetting'] = $this->config_model->callerIDSetting();
        $data['selectedSupervisor'] = $this->campaign_model->selectedSupervisor($this->input->get('campaign'));

        //edit campaign
        $data['status'] = $this->campaign_model->getstatus();
        $data['supervisor'] = $this->campaign_model->getsupervisor();
        $data['dispose'] = $this->dispose_model->getdispose();
        $data['alternetNumberDispose'] = $this->dispose_model->getAlternetNumberDispose();
        $data['previewSkipDispose'] = $this->dispose_model->getpreviewSkipDispose();
        $data['table'] = $this->campaign_model->viewcampaignprocess($this->input->get('campaign'));
        $data['before'] = $this->campaign_model->beforeedit($this->input->get('campaign'));
        $data['page'] = 'viewcampaignprocess';
        $data['title'] = 'View campaign process';

        $configSetting = $this->config_model->getProcessApiSetting();

        // process setting for api and iframe
        $data['processApiSetting'] = $configSetting->apiSetting;
        $data['processIframeSetting'] = $configSetting->iframeSetting;
        $data['processApiArray'] = $this->process_model->getApiProcessArray();
        $data['processIframeArray'] = $this->process_model->getIframeProcessArray();
        $this->load->view('template', $data);
    }

    public function editcampaignprocess() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "1";
        $data['checkFeatureAllow'] = $this->standard_model->checkFeatureAllow('1', 'multilevel_dispose_allow');
        //process
        $data['leadset'] = $this->process_model->getleadset();
        $data['processstatus'] = $this->process_model->getstatus();
        $data['calling_mode'] = $this->process_model->getcallingmode();
        //edit campaign
        $data['status'] = $this->campaign_model->getstatus();
        $data['processConfig'] = $this->config_model->getProcessConfig();
        $data['dispose'] = $this->dispose_model->getdispose();
        // $data['supervisor']         = $this->campaign_model->getsupervisor();
        // $data['table']              = $this->campaign_model->viewcampaignprocess($this->input->get('campaign'));
        // $data['before']             = $this->campaign_model->beforeedit($this->input->get('campaign'));
        // $data['selectedSupervisor'] = $this->campaign_model->selectedSupervisor($this->input->get('campaign'));
        //Edit process
        $data['campaignId'] = $this->input->get('campaign');
        $data['beforeprocess'] = $this->process_model->beforeedit($this->input->get('id'));
        $data['page'] = 'editcampaignprocess';
        $data['title'] = 'Edit campaign process';
        $configSetting = $this->config_model->getProcessApiSetting();
        // process setting for api and iframe
        $data['processApiSetting'] = $configSetting->apiSetting;
        $data['processIframeSetting'] = $configSetting->iframeSetting;
        $data['processApiArray'] = $this->process_model->getApiProcessArray();
        $data['processIframeArray'] = $this->process_model->getIframeProcessArray();
        // view set for controler
        $this->load->view('template', $data);
    }

    public function downloadLeadset() {
        $leadsetId = $this->input->get_post('leadset');
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->lead_model->downloadLeadset($leadsetId);
    }

    public function downloadLeadsetSample() {
        $leadsetId = $this->input->get_post('leadset');
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->lead_model->downloadLeadsetSample($leadsetId);
    }

    public function downloadUserSample() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->user_model->downloadUserSample();
    }

    public function uploadlead() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $data['leadsetid'] = $this->input->get_post('leadset');
        $data['leadsetDetail'] = $this->lead_model->getLeadsetDetail($data['leadsetid']);
        $data['page'] = 'uploadlead';
        $data['title'] = 'Upload Lead';
        $this->load->view('template', $data);
    }

    public function uploadleadsubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $config['upload_path'] = './uploads/csv/';
        $config['allowed_types'] = '*';
        $config['max_size'] = 1024 * 100;
        $config['encrypt_name'] = TRUE;
        $leadset = $this->input->get_post("leadsetid");
        $file_element_name = 'csv';
        $csvfile = "";

        $this->load->library('upload', $config);

        if ($this->upload->do_upload($file_element_name)) {

            $this->load->library('csvreader');
            $uploaddata = $this->upload->data();
            $csvfile = $uploaddata['full_path'];
            $filePath = 'uploads/csv/' . $csvfile;
            $removeduplicate = $this->input->post('removeduplicate');
            $csvData = $this->csvreader->parse_file($csvfile);
            $uploadLeadData = $this->lead_model->uploadlead($csvData, $removeduplicate, $leadset);

            if ($uploadLeadData->output == "FALSE") {

                $data['alerterror'] = "Lead data Not Uploaded because <br/>" . $uploadLeadData->error;
            } else {

                $data['alertsuccess'] = $uploadLeadData->message;
            }

            if (isset($uploadLeadData->notInsertedDataFlag) && $uploadLeadData->notInsertedDataFlag == "TRUE") {
                // header("Location: ".site_url()."/site/viewleadbyleadset?leadset=$leadset");
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename=Leadset.csv');
                $output = fopen('php://output', 'w');
                foreach ($uploadLeadData->notInsertedData as $key => $value) {
                    fputcsv($output, $value);
                }
            } else {

                $webAppPath = $this->config->item('webAppPath');

                if ($leadset != "") {

                    if ($uploadLeadData->output == "TRUE") {

                        $data['other'] = "leadset=" . $leadset;
                        $data['redirect'] = "site/viewleadbyleadset";
                        $this->load->view("redirect2", $data);
                    } else {
                        $data['other'] = "leadset=" . $leadset;
                        $data['redirect'] = "site/uploadlead";
                        $this->load->view("redirect2", $data);
                    }
                    // $data['redirect']="site/viewleadset";
                    // $this->load->view("redirect",$data);
                } else {

                    $data['redirect'] = "site/viewleadset";
                    $this->load->view("redirect2", $data);
                }
            }
        } else {

            $data['other'] = "leadset=" . $leadset . "&alerterror=" . $this->upload->display_errors();
            $data['redirect'] = "site/uploadlead";
            $this->load->view("redirect2", $data);
        }
    }

    function viewreporting() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $agent = "";
        $campaign = "";
        $process = "";
        $data['agent'] = $this->reporting_model->getagent();
        $data['campaign'] = $this->reporting_model->getcampaign();
        $data['process'] = $this->reporting_model->getprocess();
        $data['table'] = $this->reporting_model->viewreporting($agent, $campaign, $process);
        $data['page'] = 'viewreporting';
        $data['title'] = 'View Reporting';
        $this->load->view('template', $data);
    }

    function viewreportingsubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $agent = $this->input->post('agent');
        $process = $this->input->post('process');
        $campaign = $this->input->post('campaign');
        $data['message'] = $this->reporting_model->viewreporting($agent, $campaign, $process);
        $this->load->view('json', $data);
    }

    /* function viewlivemonitoring()
      {
      $data['userButtonSetting'] = $this->data['userButtonSetting'];
      $data['editmode']="0";
      $agent="";
      $campaign="";
      $process="";
      $user=$this->session->userdata('id');
      $q2 = $this->db->query("SELECT `value` FROM `config` WHERE `name` LIKE '%livemonitoringrefresh%'")->row();

      $data['refreshtime']=$q2->value;
      $q3 = $this->db->query("SELECT `extension` FROM `user` WHERE `id` = '$user'")->row();
      $data['extension']=$q3->extension;
      //$agent=$this->input->get_post('agent');
      //$process=$this->input->get_post('process');
      //$campaign=$this->input->get_post('campaign');
      //$data[ 'agent' ] =$this->reporting_model->getagent1();
      //$data['campaign']=$this->reporting_model->getcampaign1();
      //$data['process']=$this->reporting_model->getprocess1();
      //$data['table']=$this->reporting_model->viewlivemonitoring($agent,$campaign,$process);
      $data['table2']=$this->reporting_model->livemonitoringdata();
      $this->userStateChangeSet("View Live Monitoring");
      $data['page']='viewlivemonitoring';
      $data['title']='View Live Monitoring';
      $this->load->view('template',$data);
      }
      function viewlivemonitoringsubmit()
      {
      $data['editmode']="0";
      $agent=$this->input->post('agent');
      $process=$this->input->post('process');
      $campaign=$this->input->post('campaign');
      $data['message']=$this->reporting_model->viewlivemonitoring($agent,$campaign,$process);
      $this->load->view('json',$data);
  } */

  function uploadextensions() {
    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();
    $data['editmode'] = "0";
    $data['page'] = 'uploadextensions';
    $data['title'] = 'Upload Extensions';
    $this->load->view('template', $data);
}

public function uploadextensionssubmit() {
    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();
    $data['editmode'] = "0";
        //uploading files
    $config['upload_path'] = './uploads/csv/';
    $config['allowed_types'] = '*';
    $config['max_size'] = 1024 * 8;
    $config['encrypt_name'] = TRUE;

    $this->load->library('upload', $config);
    $file_element_name = 'csv';
    $csvfile = "";
    if ($this->upload->do_upload($file_element_name)) {
        $uploaddata = $this->upload->data();
        $csvfile = $uploaddata['file_name'];
    }
    $this->load->library('csvreader');
    $filePath = 'uploads/csv/' . $csvfile;
    $csvData = $this->csvreader->parse_file($filePath);
    if ($this->extension_model->uploadextensions($csvData) == 0)
        $data['alerterror'] = "New Extension List could not be created.";
    else
        $data['alertsuccess'] = "Extension List created Successfully.";
    $data['table'] = $this->extension_model->viewextension();
    $data['redirect'] = "site/viewextension";
        //$data['other']="template=$template";
    $this->load->view("redirect", $data);
}

public function edituserprocess() {
    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();

    $data['before'] = $this->process_model->edituserprocess($this->session->userdata('id'));
    $this->userStateChangeSet("Edit User process");
    $data['page'] = 'edituserprocess';
    $data['title'] = 'Edit User process';
    $this->load->view('template', $data);
}

public function edituserprocesssubmit() {
    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();
    $user = $this->session->userdata('id');
    $process = $this->input->post('process');
    $this->process_model->edituserprocesssubmit($user, $process);
        // $data['alerterror']="Process Editing was unsuccesfull";
        // else
        // $data['alertsuccess']="Process edited Successfully.";
    $data['redirect'] = "site/callingscreen";
        //$data['other']="template=$template";
    $this->load->view("redirect", $data);
}

public function callingscreen_agentdashboard() {

    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $agent = $this->session->userdata('id');
    $startDate = date('Y-m-d');
    $endDate = date('Y-m-d');
    $data['selectedagent'] = $agent;
    $data['startDate'] = $startDate;
    $data['endDate'] = $endDate;
    $data['agentdashboarddata'] = $this->user_model->agentDispose($agent, $startDate, $endDate);
    $data['agentperformance'] = $this->reporting_model->agentperformance($agent, $startDate, $endDate);

    $data['editmode'] = "0";
    $data['page'] = "callingscreenAgentDashboard";
    $data['agentDashboardData'] = $this->reporting_model->agentdashboarddata();
    return $this->load->view("template_pauseState", $data);
}

public function agentdashboard() {
    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();
    $agent = $this->session->userdata('id');
    $startDate = date('Y-m-d');
    $endDate = date('Y-m-d');
    $data['title'] = "Agent Dashboard";
    $data['selectedagent'] = $agent;
    $data['startDate'] = $startDate;
    $data['endDate'] = $endDate;
    $data['agentdashboarddata'] = $this->user_model->agentDispose($agent, $startDate, $endDate);
    $data['agentperformance'] = $this->reporting_model->agentperformance($agent, $startDate, $endDate);

    $data['editmode'] = "0";
    $this->userStateChangeSet("Agent Dashboard");
    $data['page'] = "agentDashboardNew";
    $data['agentDashboardData'] = $this->reporting_model->agentdashboarddata();
    $this->load->view("template", $data);
}

public function agentdashboarddata() {
    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();
    $data['message'] = $this->user_model->agentdashboarddata();
    $this->load->view("json", $data);
}

    //this data can be get in json function if not used again then delete
    // public function cdrreportingdetail()
    // {
    //     $reference=$this->input->get_post("reference_uuid");
    //     $qry="SELECT * FROM `cdr_detail` WHERE `cdr_detail`.`reference_uuid` = '$reference'";
    //     $data["table"]=$this->db->query($qry)->result_array();
    //     $data['page']='cdrreportingdetail';
    //     $data['title']='View  CDR Reporting Detail';
    //     $this->load->view('template',$data);
    // }

public function viewleadbyleadset() {

    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();
    $data['accesslevel'] = $this->session->userdata('accesslevel');
    $this->load->library("pagination");
    $config = array();

    if ($this->input->get_post('leadset')) {
        $newdata = array(
            'leadsetid' => $this->input->get_post('leadset')
        );
        $this->session->set_userdata($newdata);
        $data['leadsetid'] = $this->input->get_post('leadset');
        $config['page_query_string'] = FALSE;
        $config['base_url'] = site_url("site/viewleadbyleadset");
    } else {

        $config['page_query_string'] = FALSE;
        $config['base_url'] = site_url("site/viewleadbyleadset");
        $data['leadsetid'] = $this->session->userdata('leadsetid');
    }
    $config["total_rows"] = $this->lead_model->lead_count();
    $config["per_page"] = 50;
    $config["uri_segment"] = 3;
    $config['full_tag_open'] = '<ul class="pagination">';
    $config['full_tag_close'] = '</ul>';
    $config['num_tag_open'] = '<li>';
    $config['num_tag_close'] = '</li>';
    $config['first_tag_open'] = '<li>';
    $config['first_tag_close'] = '</li>';
    $config['last_tag_open'] = '<li>';
    $config['last_tag_close'] = '</li>';
    $config['cur_tag_open'] = '<li class="active"><a href="#">';
    $config['cur_tag_close'] = '</a></li>';
    $config['next_tag_open'] = '<li>';
    $config['next_tag_close'] = '</li>';
    $config['prev_tag_open'] = '<li>';
    $config['prev_tag_close'] = '</li>';
    $this->pagination->initialize($config);
    $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
    $data["table"] = $this->lead_model->leadInLeadset($config["per_page"], $page);
    $data["links"] = $this->pagination->create_links();
    $data['start'] = $page;
    $this->userStateChangeSet("View leadset");
    $data['page'] = 'viewleadbyleadset';
    $data['title'] = 'View leadset';
    $this->load->view('template', $data);
}

function viewInvoundCdr() {

    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();
    $accesslevel = $this->session->userdata('accesslevel');
    $perPage = 50;

    $data['agent'] = $this->reporting_model->getagent("TRUE");
    $data['campaign'] = $this->reporting_model->getcampaign();
    $campaignProcessData = $this->process_model->getLiveProcess();
    $data['process'] = $campaignProcessData->process;

    $process = $this->input->get_post('process');
    $campaign = $this->input->get_post('campaign');
    $date = $this->input->get_post('date');
    $dateto = $this->input->get_post('dateto');
    $phoneno = $this->input->get_post('phoneno');
    $reason = $this->input->get_post('reason');
    $missCdrDidNumber = $this->input->get_post('missCdrDidNumber');
    $missInQueueTime = $this->input->get_post('missInQueueTime');
    $agent = $this->input->get_post('agent');

    $data['selectedProcess'] = $process;
    $data['selectedCampaign'] = $campaign;
    $data['selectedAgent'] = $agent;
    $data['selectedDate'] = $date;
    $data['selectedDateTo'] = $dateto;
    $data['selectedPhoneNo'] = $phoneno;
    $data['selectedReason'] = $reason;
    $data['selectedMissCdrDidNumber'] = $missCdrDidNumber;
    $data['selectedMissInQueueTime'] = $missInQueueTime;


    $this->load->library("pagination");
    $config = array();
    $config['page_query_string'] = FALSE;
    $config['base_url'] = site_url("site/viewInvoundCdr");

    $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

    $data["table"] = $this->cdr_model->inboudCdr($perPage, $page, "FALSE", $process, $campaign, $date, $dateto, $phoneno, $reason, $missCdrDidNumber, $missInQueueTime);

    if (!empty($data["table"])) {

        $config["total_rows"] = ($this->uri->segment(3)) ? $this->uri->segment(3) + 51 : 51;
    } else {

        $config["total_rows"] = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
    }

    $config['page_query_string'] = FALSE;
    $config['base_url'] = site_url("site/viewInvoundCdr");
    $config["per_page"] = $perPage;
    $config["uri_segment"] = 3;
    $config['full_tag_open'] = '<ul class="pagination">';
    $config['full_tag_close'] = '</ul>';
    $config['num_tag_open'] = '<li>';
    $config['num_tag_close'] = '</li>';
    $config['first_tag_open'] = '<li>';
    $config['first_tag_close'] = '</li>';
    $config['last_tag_open'] = '<li>';
    $config['last_tag_close'] = '</li>';
    $config['cur_tag_open'] = '<li class="active"><a href="#">';
    $config['cur_tag_close'] = '</a></li>';
    $config['next_tag_open'] = '<li class="nextPage">';
    $config['next_tag_close'] = '</li>';
    $config['prev_tag_open'] = '<li class="previousPage">';
    $config['prev_tag_close'] = '</li>';
    $config['display_pages'] = FALSE;
    $config['first_link'] = FALSE;
    $config['last_link'] = FALSE;
    $limit = $page;

    $this->pagination->initialize($config);

    $data["links"] = $this->pagination->create_links();
    if ($page == 0) {
        $limit = $perPage;
        $start = 0;
    } else {
        $start = $limit;
        $limit = $perPage;
    }
    $data['limit'] = $limit;
    $data['start'] = $start;
    $this->userStateChangeSet("inboundCdr");
    $data['page'] = 'viewInboundCdr';
    $data['title'] = 'inboundCdr';
    $this->load->view('template', $data);
}

public function standardcdrreporting() {
    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();

    $perPage = 50;
    $data['editmode'] = "0";

    $agent = $this->input->post('agent');
    $campaign = $this->input->post('campaign');
    $process = $this->input->post('process');
    $talktime = $this->input->post('talktime');
    $timeInterval = $this->input->post('timeInterval');
    $phoneno = $this->input->post('phoneno');
    $mydate = $this->input->post('date');
    $mydateto = $this->input->post('dateto');
    if ($mydate == "") {

    } else {
        $a = explode('-', $mydate);
        $result = $a[2] . '-' . $a[0] . '-' . $a[1];
        $mydate = $result;
    }
    if ($mydateto == "") {

    } else {
        $a = explode('-', $mydateto);
        $result = $a[2] . '-' . $a[0] . '-' . $a[1];
        $mydateto = $result;
    }
    $data['agent'] = $this->reporting_model->getagent();
    $data['campaign'] = $this->reporting_model->getcampaign();
        // $data['process']         = $this->reporting_model->getprocess();
    $campaignProcessData = $this->process_model->getLiveProcess();
    $data['process'] = $campaignProcessData->process;
    $data['dispose'] = $this->reporting_model->getdispose();


    $data['selectedAgent'] = $agent;
    $data['selectedProcess'] = $process;
    $data['selectedCampaign'] = $campaign;
    $data['selectedTalktime'] = $talktime;
    $data['selectedPhoneno'] = $phoneno;
    $data['selectedTimeInterval'] = $timeInterval;


    if ($mydate != "")
        $data['selecteddate'] = date("m-d-Y", strtotime($mydate));
    else
        $data['selecteddate'] = "";
    if ($mydateto != "")
        $data['selecteddateto'] = date("m-d-Y", strtotime($mydateto));
    else
        $data['selecteddateto'] = "";


        //$data['table']=$this->cdr_model->standardrecordinglogdetail($agent,$campaign,$process,$mydate,$rating,$talktime,$phoneno);

    $this->load->library("pagination");
    $config = array();

    $config['page_query_string'] = FALSE;
    $config['base_url'] = site_url("site/standardcdrreporting");
    $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

    $data["table"] = $this->cdr_model->fetch_cdrReport($perPage, $page, $agent, $campaign, $process, $talktime, $phoneno, $timeInterval, $mydate, $mydateto);
    if (!empty($data["table"])) {
        $config["total_rows"] = ($this->uri->segment(3)) ? $this->uri->segment(3) + 51 : 51;
    } else {
        $config["total_rows"] = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
    }

    $config["per_page"] = $perPage;
    $config["uri_segment"] = 3;
    $config['full_tag_open'] = '<ul class="pagination">';
    $config['full_tag_close'] = '</ul>';
    $config['num_tag_open'] = '<li>';
    $config['num_tag_close'] = '</li>';
    $config['first_tag_open'] = '<li>';
    $config['first_tag_close'] = '</li>';
    $config['last_tag_open'] = '<li>';
    $config['last_tag_close'] = '</li>';
    $config['cur_tag_open'] = '<li class="active"><a href="#">';
    $config['cur_tag_close'] = '</a></li>';
    $config['next_tag_open'] = '<li class="nextPage">';
    $config['next_tag_close'] = '</li>';
    $config['prev_tag_open'] = '<li class="previousPage">';
    $config['prev_tag_close'] = '</li>';
    $config['display_pages'] = FALSE;
    $config['first_link'] = FALSE;
    $config['last_link'] = FALSE;

    $this->pagination->initialize($config);
    $data["links"] = $this->pagination->create_links();
    $limit = $page;
    if ($page == 0) {
        $limit = $perPage;
        $start = 0;
    } else {
        $start = $limit;
        $limit = $perPage;
    }
    $data['limit'] = $limit;
    $data['start'] = $start;

    $this->userStateChangeSet("View Standard CDR Reporting");
    $data['page'] = 'standardcdrreporting';
    $data['title'] = 'View Standard CDR Reporting';
    $this->load->view('template', $data);
}

    /* function standardcdrreportingsubmit()
      {
      $data['editmode']="0";
      $agent=$this->input->post('agent');
      $process=$this->input->post('process');
      $campaign=$this->input->post('campaign');
      $campaign=$this->input->post('calldir');
      $campaign=$this->input->post('contact');
      $campaign=$this->input->post('campaign');
      $date=$this->input->post('date');

      $data['message']=$this->reporting_model->viewreporting($agent,$campaign,$process);
      $this->load->view('json',$data);
  } */

  public function adnvancedcdrreporting() {
    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();
    $data['editmode'] = "0";
    $userwhere = "";
    $innerjoin = "";
    $user = $this->session->userdata('id');
    $accesslevel = $this->session->userdata('accesslevel');
    if ($accesslevel == 3) {
        $innerjoin = " where`campaign`.`supervisor`=$user ";
    }
    $request = $this->input->get_post("request");
    if ($request == "json") {
        $query = $this->input->get_post("query");
        $fromtable = $this->input->get_post("table");
        $data["fromtable"] = $fromtable;
        $page = $this->input->get_post("page");
        $keyword = $this->input->get_post("keyword");
        $filters = $this->input->get_post("filters");
        $filterscompare = $this->input->get_post("filterscompare");
        $orderby = $this->input->get_post("orderby");
        $maxrow = $this->input->get_post("maxrow");
        $forone = $this->db->query("$query LIMIT 0,1")->result_array();
        $keys = array_keys($forone[0]);
        $where = " WHERE ( ";
        $filterwhere = " ( ";
        foreach ($keys as $num => $key) {
            $where .= "`$key` LIKE '%$keyword%' OR ";
            if ($filters[$num] != "SELECTALLGODISGREAT")
                $filterwhere .= " `$key` " . $filterscompare[$num] . " '" . $filters[$num] . "' AND ";
        }
        $where .= " 0 ) ";
        $filterwhere .= " 1 ) ";
        $startrow = (($page - 1) * $maxrow);
            //echo $where;
        $data["message"]["content"] = $this->db->query("$query $where AND $filterwhere $orderby LIMIT $startrow,$maxrow")->result_array();
        $data["message"]["totalrow"] = $this->db->query("SELECT count(*)  as `total` FROM $fromtable $where AND $filterwhere")->row();
        $data["message"]["totalrow"] = $data["message"]["totalrow"]->total;
        $data["message"]["maxrow"] = $maxrow;
        $data["message"]["filterwhere"] = $filterwhere;
            //echo "$query $where $filterwhere";
        $this->load->view('json', $data);
    }
    else {

        $data["query"] = "SELECT `user`.`username` AS `agent_username`,
        `campaign`.`name` AS `campaign_name`,
        `process`.`name` as `process_name`,`cdr_out`.`call_direction`,`cdr_out`.`customer_phone_number`,`cdr_out`.`agent_talktime_sec`,`cdr_out`.`total_duration_sec`,`cdr_out`.`disconnected_by`,`cdr_out`.`customer_uuid`,`cdr_out`.`customer_id`,`cdr_out`.`agent_extension`,`cdr_out`.`ringing_sec`,`cdr_out`.`answer_stamp`,`cdr_out`.`progress_sec`,`cdr_out`.`ringing_stamp`,`cdr_out`.`progress_stamp`,`cdr_out`.`hangup_cause`,`cdr_out`.`hangup_cause_q850`,`cdr_out`.`codec_outbound_name`,`cdr_out`.`sip_gateway_name`,`cdr_out`.`fs_ipv4` FROM `cdr_out`
        LEFT OUTER JOIN `campaign` ON`cdr_out`.`campaign_id`=`campaign`.`id` 
        LEFT OUTER JOIN `process` ON `cdr_out`.`process_id`=`process`.`id` 
        LEFT OUTER JOIN `user` ON `cdr_out`.`agent_id`=`user`.`id`
        $innerjoin 
        ";
        $data["fromtable"] = "`cdr_out`";
        $fromtable = $data["fromtable"];
        $query = $data["query"];
        $data["maxrow"] = 25;
        $data["table"] = $this->db->query("$query LIMIT 0," . $data["maxrow"])->result_array();
        $forone = $this->db->query("$query LIMIT 0,1")->result_array();
        $keys = array_keys($forone[0]);
        $data["filters"] = array();
        $i = 0;
        foreach ($keys as $key) {
            $data["filters"][$i++] = $this->db->query("SELECT DISTINCT `$key` FROM $fromtable $innerjoin ORDER BY `$key` ASC ")->result_array();
        }
        $data["totalrow"] = $this->db->query("SELECT count(*) as `total` FROM $fromtable $innerjoin")->row();
        $data["totalrow"] = $data["totalrow"]->total;
        $this->userStateChangeSet("View CDR Reporting");
        $data['page'] = 'adnvancedcdrreporting';
        $data['title'] = 'View Advance CDR Reporting';
        $this->load->view('template', $data);
    }
        /*


          $agent="";
          $campaign="";
          $process="";
          $date="";
          $voipgateway="";
          $calldirection="";
          $agent=$this->input->post('agent');
          $process=$this->input->post('process');
          $campaign=$this->input->post('campaign');
          $mydate=$this->input->post('date');
          if($mydate == "") { }
          else
          {
          $a = explode('-',$mydate);
          $result = $a[2].'-'.$a[0].'-'.$a[1];
          $mydate = $result;
          }
          $voipgateway=$this->input->post('voipgateway');
          $calldirection=$this->input->post('calldirection');
          $data['selectedagent']=$agent;
          $data['selectedprocess']=$process;
          $data['selectedcampaign']=$campaign;
          if($mydate != "")
          $data['selecteddate']=date("m-d-Y",strtotime($mydate));
          else
          $data['selecteddate']="";
          $data['selectedvoipgateway']=$voipgateway;
          $data['selectedcalldirection']=$calldirection;
          $data[ 'agent' ] =$this->reporting_model->getagent();
          $data['campaign']=$this->reporting_model->getcampaign();
          $data['process']=$this->reporting_model->getprocess();
          $data['calldirection']=$this->reporting_model->getcalldirection();
          $data['voipgateway']=$this->reporting_model->getvoipgateway();
          $data['table']=$this->reporting_model->viewadnvancedcdrreporting($agent,$campaign,$process,$mydate,$voipgateway,$calldirection);
          $data['page']='adnvancedcdrreporting';
          $data['title']='View Advance CDR Reporting';
          $this->load->view('template',$data); */
      }

      function adnvancedcdrreportingsubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();

        $data['editmode'] = "0";
        $agent = $this->input->post('agent');
        $process = $this->input->post('process');
        $campaign = $this->input->post('campaign');
        $data['message'] = $this->reporting_model->viewreporting($agent, $campaign, $process);
        $this->load->view('json', $data);
    }

    function resetrecordingsession() {

        $data['redirect'] = "site/recordinglogdetail";
        $this->load->view('redirect', $data);
    }

    function resetCdrReport() {

        $data['redirect'] = "site/standardcdrreporting";
        $this->load->view('redirect', $data);
    }

    function recordinglogdetail() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['checkFeatureAllow'] = $this->standard_model->checkFeatureAllow('1', 'multilevel_dispose_allow');

        $perPage = 50;
        $data['editmode'] = "0";
        $agent = "";
        $process = "";
        $campaign = "";
        $leadset = "";
        $dissconnector = "";
        $agent = $this->input->post('agent');
        $process = $this->input->post('process');
        $campaign = $this->input->post('campaign');
        $mydate = $this->input->post('date');
        $mydateto = $this->input->post('dateto');
        $talktime = $this->input->post('talktime');
        $phoneno = $this->input->post('phoneno');
        $dispose = $this->input->post('dispose');
        $dispose2 = $this->input->post('dispose2');
        $dispose3 = $this->input->post('dispose3');
        $leadset = $this->input->post('leadset');
        $dissconnector = $this->input->post('dissconnector');
        $recodingDidNumber = $this->input->post('recodingDidNumber');

        // if($mydate != "") {
        //   $a = explode('-',$mydate);
        //   $result = $a[2].'-'.$a[0].'-'.$a[1];       
        //   $mydate = $result;
        // }
        // if($mydateto != "") {
        //   $a = explode('-',$mydateto);
        //   $result = $a[2].'-'.$a[0].'-'.$a[1];       
        //   $mydateto = $result;
        // }

        $data['dispose'] = $this->dispose_model->getdispose();
        $data['leadset'] = $this->process_model->getleadset();
        $data['recordDownloadFlag'] = $this->config_model->getRecordDownloadSetting();
        $data['recordDownloadJson'] = $this->user_model->getCustDownloadJson();
        $data['callbackDisposeArray'] = $this->callback_model->getCallbackDisposeArray();
        $data['selectedagent'] = $agent;
        $data['selectedprocess'] = $process;
        $data['selectedcampaign'] = $campaign;
        $data['selectedtalktime'] = $talktime;
        $data['selectedphoneno'] = $phoneno;
        $data['selectedleadset'] = $leadset;
        $data['selecteddissconnector'] = $dissconnector;
        $data['selecteddidNumber'] = $recodingDidNumber;
        $data['selectedRecodingDidNumber'] = $recodingDidNumber;
        $data['selectedDispose'] = $dispose;
        $data['selectedDispose2'] = $dispose2;
        $data['selectedDispose3'] = $dispose3;
        $data['selectedLeadset'] = $leadset;
        $data['selectedDissconnector'] = $dissconnector;
        $data['selecteddate'] = $mydate;
        $data['selecteddateto'] = $mydateto;

        $this->load->library("pagination");
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $config = array();
        $config['page_query_string'] = FALSE;
        $config['base_url'] = site_url("site/recordinglogdetail");

        $accesslevel = $this->session->userdata('accesslevel');
        if ($accesslevel == 4) {

            $userId = $this->session->userdata('id');
            $userName = $this->session->userdata('username');
            $data['userSetting'] = $this->user_model->getAgentSettings($userId, $userName);
            $otherProcessFlag = 0;
            $sameProcessFlag = 0;
            $otherProcessArray = array();
            $data["agentNameShow"] = 0;
            if (isset($data['userSetting']->extraProcessReportAllows) &&
                $data['userSetting']->extraProcessReportAllows == "1") {

                $otherProcessFlag = 1;
        }
        if (isset($data['userSetting']->sameProcessReportAllows) &&
            $data['userSetting']->sameProcessReportAllows == "1") {

            $sameProcessFlag = 1;
    }

    if (isset($data['userSetting']->otherProcessSetting)) {

        $otherProcessArray = $data['userSetting']->otherProcessSetting;
    }

    if ($otherProcessFlag == 1 || $sameProcessFlag == 1) {

        if ($sameProcessFlag != 1) {

            $processData = $this->process_model->getUserMappedProcessALL($sameProcessFlag, $otherProcessFlag, $otherProcessArray);
            if ($processData->allProcess != "") {


                $data['agent'] = $this->process_model->getAgentFromProcess($processData->extraProcess);
                $data['campaign'] = $this->process_model->getCampaignFromProcess($processData->allProcess);
                $campaignProcessData = $this->process_model->getSelectedProcessFromProcess($processData->allProcess);
                $data['process'] = $campaignProcessData->process;
            } else {

                $data['agent'] = $this->reporting_model->getagent();
                $data['campaign'] = $this->reporting_model->getcampaign();
                $campaignProcessData = $this->process_model->getLiveProcess();
                $data['process'] = $campaignProcessData->process;
            }
        } else {

            $processData = $this->process_model->getUserMappedProcess($sameProcessFlag, $otherProcessFlag, $otherProcessArray);
            if ($processData != "") {

                $data['agent'] = $this->process_model->getAgentFromProcess($processData);
                $data['campaign'] = $this->process_model->getCampaignFromProcess($processData);
                        // $data['process']             =  $this->process_model->getProcessFromProcess( $processData );
                $campaignProcessData = $this->process_model->getSelectedProcessFromProcess($processData);
                $data['process'] = $campaignProcessData->process;
            } else {

                $data['agent'] = $this->reporting_model->getagent();
                $data['campaign'] = $this->reporting_model->getcampaign();
                        // $data['process']             =  $this->reporting_model->getprocess();
                $campaignProcessData = $this->process_model->getLiveProcess();
                $data['process'] = $campaignProcessData->process;
            }
        }

        $data["agentNameShow"] = 1;
    } else {
        $data["agentNameShow"] = 0;
        $data['campaign'] = $this->reporting_model->getcampaign();
                // $data['process']             =  $this->reporting_model->getprocess();
        $campaignProcessData = $this->process_model->getLiveProcess();
        $data['process'] = $campaignProcessData->process;
    }
} else {
    $data['userSetting'] = "FALSE";
    if ($accesslevel == 7 || $accesslevel == 8 || $accesslevel == 9) {

        $data["agentNameShow"] = 0;
    } else {

        $data["agentNameShow"] = 1;
    }
    $data['agent'] = $this->reporting_model->getagent();
    $data['campaign'] = $this->reporting_model->getcampaign();
            // $data['process']             =  $this->reporting_model->getprocess();
    $campaignProcessData = $this->process_model->getLiveProcess();
    $data['process'] = $campaignProcessData->process;
}
$configArray = array(
    99 => (object) array('variableName' => 'recodingDetailFromMongo', 'defaultValue' => '0'),
    100 => (object) array('variableName' => 'recodingDownload', 'defaultValue' => '0'),
    126 => (object) array('variableName' => 'recordingSecondaryPath', 'defaultValue' => '')
);
$configData = $this->config_model->getMultipleConfig($configArray);
$data['configData'] = $configData;

if ($accesslevel == 4) {

    if (isset($configData->recodingDownload) && $configData->recodingDownload == 1) {

        $data['recodingDownloadFlag'] = 1;
    } else {

        $data['recodingDownloadFlag'] = 0;
    }
} else {

    $data['recodingDownloadFlag'] = 1;
}

$data['accesslevel'] = $accesslevel;

if (isset($configData->recodingDetailFromMongo) && $configData->recodingDetailFromMongo == 1) {

    $data["recodingDetailFromMongo"] = 1;
    $data["table"] = $this->mongoreport_model->fetchRecordDetail($perPage, $page, $data['userSetting'], $agent, $process, $campaign, $talktime, $phoneno, $dissconnector, $recodingDidNumber, $dispose, $dispose2, $dispose3, $leadset, $mydate, $mydateto);

    if ($data["table"] == "FALSE") {

        $data["recodingDetailFromMongo"] = 0;
        $data["table"] = $this->cdr_model->fetch_recording_log($perPage, $page, "FALSE", $data['userSetting'], $agent, $process, $campaign, $talktime, $phoneno, $dissconnector, $recodingDidNumber, $dispose, $dispose2, $dispose3, $leadset, $mydate, $mydateto);
    }
} else {

    $data["recodingDetailFromMongo"] = 0;
    $data["table"] = $this->cdr_model->fetch_recording_log($perPage, $page, "FALSE", $data['userSetting'], $agent, $process, $campaign, $talktime, $phoneno, $dissconnector, $recodingDidNumber, $dispose, $dispose2, $dispose3, $leadset, $mydate, $mydateto);
}
if (!empty($data["table"])) {
    $config["total_rows"] = ($this->uri->segment(3)) ? $this->uri->segment(3) + 51 : 51;
} else {
    $config["total_rows"] = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
}

$config["per_page"] = $perPage;
$config["uri_segment"] = 3;
$config['full_tag_open'] = '<ul class="pagination">';
$config['full_tag_close'] = '</ul>';
$config['num_tag_open'] = '<li>';
$config['num_tag_close'] = '</li>';
$config['first_tag_open'] = '<li>';
$config['first_tag_close'] = '</li>';
$config['last_tag_open'] = '<li>';
$config['last_tag_close'] = '</li>';
$config['cur_tag_open'] = '<li class="active"><a href="#">';
$config['cur_tag_close'] = '</a></li>';
$config['next_tag_open'] = '<li class="nextPage">';
$config['next_tag_close'] = '</li>';
$config['prev_tag_open'] = '<li class="previousPage">';
$config['prev_tag_close'] = '</li>';
$config['display_pages'] = FALSE;
$config['first_link'] = FALSE;
$config['last_link'] = FALSE;
$this->pagination->initialize($config);
$data["links"] = $this->pagination->create_links();
$limit = $page;
if ($page == 0) {

    $limit = $perPage;
    $start = 0;
} else {

    $start = $limit;
    $limit = $perPage;
}

$data['limit'] = $limit;
$data['start'] = $start;

$this->userStateChangeSet("Recording Log Detail");
$data['page'] = "recordinglogdetail";
$data['title'] = "Log Detail";
$this->load->view('template', $data);
}

function _push_file($path, $name) {
        // make sure it's a file before doing anything!
    if (is_file($path)) {
            // required for IE
        if (ini_get('zlib.output_compression')) {
            ini_set('zlib.output_compression', 'Off');
        }

            // get the file mime type using the file extension
        $this->load->helper('file');

        $mime = get_mime_by_extension($path);

            // Build the headers to push out the file properly.
            header('Pragma: public');     // required
            header('Expires: 0');         // no cache
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
            header('Cache-Control: private', false);
            header('Content-Type: ' . $mime);  // Add the mime type from Code igniter.
            header('Content-Disposition: attachment; filename="' . basename($name) . '"');  // Add the file name
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . filesize($path)); // provide file size
            //header('Connection: close');
            readfile($path); // push it out
            exit();
        }
    }

    function downloadaudio() {
        $this->load->helper('download');
        $uuid = $this->input->get_post("uuid");
        $id = $this->input->get_post("id");
        $recordUrl = $this->config->item('record_url');
        $cdrq = $this->db->query("SELECT `cdr_out`.`customer_phone_number`,`cdr_out`.`agent_username`,`cdr_out`.`start_stamp`,`lead`.`name` as `leadname` FROM `cdr_out` 
            LEFT JOIN `lead` ON `lead`.`id`=`cdr_out`.`customer_id` 
            WHERE `cdr_out`.`id`='$id'")->row();
        //$name=$uuid.".wav";
        $name = $cdrq->agent_username . "_" . $cdrq->customer_phone_number . "_" . date("d-m-Y", strtotime($cdrq->start_stamp)) . "_" . date("H:i:s", strtotime($cdrq->start_stamp)) . ".mp3";
        $path = "$recordUrl/MP3/" . $uuid . ".mp3";
        //if(is_file($path))
        {
            // required for IE
            if (ini_get('zlib.output_compression')) {
                ini_set('zlib.output_compression', 'Off');
            }

            // get the file mime type using the file extension
            //$this->load->helper('file');
            //echo $mime = get_mime_by_extension($path);
            // Build the headers to push out the file properly.
            header('Pragma: public');     // required
            //header('Expires: 0');         // no cache
            //header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
            //header('Cache-Control: private',false);
            //header('Content-Type: audio/x-wav, audio/wav');  // Add the mime type from Code igniter.
            header('Content-Disposition: attachment; filename="' . basename($name) . '"');  // Add the file name
            header('Content-Transfer-Encoding: binary');
            //header('Content-Length: '.filesize($path)); // provide file size
            //header('Connection: close');
            readfile($path); // push it out
            exit();
        }
    }

    function downloadaudio1() {
        $this->load->helper('download');
        $uuid = $this->input->get_post("uuid");
        $id = $this->input->get_post("id");
        $recordUrl = $this->config->item('record_url');
        $cdrq = $this->db->query("SELECT `cdr_out`.`customer_phone_number`,`cdr_out`.`agent_username`,`cdr_out`.`start_stamp`,`lead`.`name` as `leadname` FROM `cdr_out` 
            LEFT JOIN `lead` ON `lead`.`id`=`cdr_out`.`customer_id` 
            WHERE `cdr_out`.`id`='$id'")->row();
        //$name=$uuid.".wav";
        $name = $cdrq->agent_username . "_" . $cdrq->customer_phone_number . "_" . date("d-m-Y", strtotime($cdrq->start_stamp)) . "_" . date("H:i:s", strtotime($cdrq->start_stamp)) . ".wav";
        $path = "$recordUrl/MP3/" . $uuid . ".mp3";
        //if(is_file($path))
        {
            // required for IE
            if (ini_get('zlib.output_compression')) {
                ini_set('zlib.output_compression', 'Off');
            }

            // get the file mime type using the file extension
            //$this->load->helper('file');
            //echo $mime = get_mime_by_extension($path);
            // Build the headers to push out the file properly.
            header('Pragma: public');     // required
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            //header('Expires: 0');         // no cache
            //header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            //header('Content-Type: audio/'.$mime);  // Add the mime type from Code igniter.
            header('Content-Type:application/octet-stream');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
            //header('Cache-Control: private',false);

            header('Content-Disposition: attachment; filename="' . basename($name) . '"');  // Add the file name
            header('Connection: Keep-Alive');
            header('Expires: 0');
            header('Content-Transfer-Encoding: binary');
            //header('Content-Length: '.filesize($path)); // provide file size
            //header('Connection: close');
            readfile($path); // push it out
            exit();
        }
    }

    function limitfields() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "1";
        $query = $this->db->query("SELECT `value` FROM `config` WHERE `name`='leadjson'")->row();
        $data['limit'] = $query->value;
        $data['title'] = "Limit config";
        $this->userStateChangeSet("editlimitfield");
        $data['page'] = 'editlimitfield';
        $this->load->view("template", $data);
    }

    function editlimitfieldsubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();

        $limit = $this->input->post("limit");
        $this->db->query("UPDATE `config` SET `value`='$limit' WHERE `name`='leadjson'");
        $data['alertsuccess'] = "Limit edited successfully";
        $data['title'] = "Dashboard";
        $this->userStateChangeSet("dashboard");
        $data['page'] = 'dashboard';
        $this->load->view("template", $data);
    }

    public function createconfig() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['page'] = 'createconfig';
        $data['title'] = 'Create config';
        $this->load->view('template', $data);
    }

    function createconfigsubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('value', 'Value', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $this->userStateChangeSet("Create New config");
            $data['page'] = 'createconfig';
            $data['title'] = 'Create New config';
            $this->load->view('template', $data);
        } else {
            $name = $this->input->post('name');
            $value = $this->input->post('value');
            if ($this->config_model->create($name, $value) == 0)
                $data['alerterror'] = "New config could not be created.";
            else
                $data['alertsuccess'] = "config created Successfully.";
            $data['table'] = $this->config_model->viewconfig();
            $data['redirect'] = "site/viewconfig";
            //$data['other']="template=$template";
            $this->load->view("redirect", $data);
        }
    }

    function viewconfig() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("View config");
        $data['configData'] = $this->config_model->viewconfig();
        if ($data['configData']->output == "FALSE") {
            $data['alerterror'] = $data['configData']->message;
        }

        $data['page'] = 'viewconfig';
        $data['title'] = 'View config';
        $this->load->view('template', $data);
    }

    function editconfig() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "1";
        $data['configData'] = $this->config_model->beforeedit($this->input->get('id'));
        if ($data['configData']->output == "FALSE") {
            $data['alerterror'] = $data['configData']->message;
        }

        $data['page'] = 'editconfig';
        $data['title'] = 'Edit config';
        $this->load->view('template', $data);
    }

    function editconfigsubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('value', 'Value', 'trim|required');
        $this->form_validation->set_rules('description', 'description', 'trim');
        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $data['before'] = $this->config_model->beforeedit($this->input->post('id'));
            $this->userStateChangeSet("Edit config");
            $data['page'] = 'editconfig';
            $data['title'] = 'Edit config';
            $this->load->view('template', $data);
        } else {
            $id = $this->input->post('id');
            $name = $this->input->post('name');
            $value = $this->input->post('value');
            $description = $this->input->post('description');
            if ($this->config_model->edit($id, $name, $value, $description) == 0)
                $data['alerterror'] = "config Editing was unsuccesful";
            else
                $data['alertsuccess'] = "config edited Successfully.";
            $data['table'] = $this->config_model->viewconfig();
            $data['redirect'] = "site/viewconfig";
            //$data['other']="template=$template";
            $this->load->view("redirect", $data);
        }
    }

    // function deleteconfig()
    // {
    //   $this->config_model->deleteconfig($this->input->get('id'));
    //   $data['table']=$this->config_model->viewconfig();
    //   $data['alertsuccess']="config Deleted Successfully";
    //   $this->userStateChangeSet("View config");
    //   $data['page']='viewconfig';
    //   $data['title']='View config';
    //   $this->load->view('template',$data);
    // }

    function viewMsgConfig() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['table'] = $this->config_model->viewMsgConfig();
        $this->userStateChangeSet("View Message config");
        $data['page'] = 'viewMsgConfig';
        $data['title'] = 'View Message config';
        $this->load->view('template', $data);
    }

    function viewMessageVeriable() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $smsProviderid = $this->input->get('smsProviderid');
        $data['smsProviderid'] = $smsProviderid;
        $data['table'] = $this->config_model->viewMessageVeriable($smsProviderid);
        $this->userStateChangeSet("View Message config");
        $data['page'] = 'viewMessageVeriable';
        $data['title'] = 'View Message config';
        $this->load->view('template', $data);
    }

    public function createMessageConfig() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['smsProviderId'] = $this->input->get('smsProviderId');
        ;
        $data['page'] = 'createMessageConfig';
        $data['title'] = 'create Message Config';
        $this->load->view('template', $data);
    }

    function createMessageConfigSubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('value', 'Value', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $this->userStateChangeSet("create Message Config");
            $data['page'] = 'createMessageConfig';
            $data['title'] = 'create Message Config';
            $this->load->view('template', $data);
        } else {
            $name = $this->input->post('name');
            $value = $this->input->post('value');
            $smsProviderId = $this->input->post('smsProviderId');
            if ($this->config_model->messageConfigCreate($name, $value, $smsProviderId) == 0)
                $data['alerterror'] = "New Message config could not be created.";
            else
                $data['alertsuccess'] = "Message config created Successfully.";

            $data['smsProviderid'] = $smsProviderId;
            $data['table'] = $this->config_model->viewMessageVeriable($smsProviderId);
            $this->userStateChangeSet("View Message config");
            $data['page'] = 'viewMessageVeriable';
            $data['title'] = 'View Message config';
            $this->load->view('template', $data);
        }
    }

    function editMessageConfig() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "1";
        $data['smsProviderid'] = $this->input->get('smsProviderid');
        $data['before'] = $this->config_model->beforeMessageConfigeEdit($this->input->get('id'));
        $this->userStateChangeSet("Edit Message config");
        $data['page'] = 'editMessageConfig';
        $data['title'] = 'Edit Message config';
        $this->load->view('template', $data);
    }

    function editMessageConfigsubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('value', 'Value', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $data['before'] = $this->config_model->beforeMessageConfigeEdit($this->input->post('id'));
            $this->userStateChangeSet("Edit message config");
            $data['page'] = 'editMessageConfig';
            $data['title'] = 'Edit message config';
            $this->load->view('template', $data);
        } else {
            $id = $this->input->post('id');
            $name = $this->input->post('name');
            $value = $this->input->post('value');
            $smsProviderId = $this->input->post('smsProviderId');
            if ($this->config_model->msgConfigedit($id, $name, $value) == 0)
                $data['alerterror'] = "Message config Editing was unsuccesful";
            else
                $data['alertsuccess'] = "Message config edited Successfully.";
            $data['smsProviderid'] = $smsProviderId;
            $data['table'] = $this->config_model->viewMessageVeriable($smsProviderId);
            $this->userStateChangeSet("View Message config");
            $data['page'] = 'viewMessageVeriable';
            $data['title'] = 'View Message config';
            $this->load->view('template', $data);
        }
    }

    function resetPauseTimeFlag() {
        $user = $this->input->get('id', TRUE);
        $status = $this->user_model->resetPauseTimeExceeded($user);
        if ($status) {
            $data['alertsuccess'] = "Pause time exceeded flag reset successfully.";
        } else {
            $data['alerterror'] = "Pause time exceeded flag reset failed.";
        }
        $data['redirect'] = "site/viewusers";
        $this->load->view('redirect', $data);
    }

    function forcelogout() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $user = $this->input->get_post('id');
        // $query= $this->db->query("UPDATE `user` SET `isloggedin` =0 WHERE `id`='$user'");
        $this->user_model->changeloginlog($user);

        $data['redirect'] = "site/viewusers";
        $this->load->view('redirect', $data);
    }

    function viewpauselog() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['table'] = $this->process_model->viewpauselog();
        $this->userStateChangeSet("View Pause log");
        $data['page'] = 'viewpauselog';
        $data['title'] = 'View Pause log';
        $this->load->view('template', $data);
    }

    function getprocessdisposition() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['message'] = $this->process_model->getprocessdisposition();
        $this->load->view('json', $data);
    }

    function admindashboard() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $this->userStateChangeSet("Admin Dashboard");
        $data['page'] = "admindashboard";
        $data['title'] = "Dashboard";
        $this->load->view('template', $data);
    }

    //pausecode
    public function createpausecode() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['page'] = 'createpausecode';
        $data['title'] = 'Create pausecode';
        $this->load->view('template', $data);
    }

    /* function createpausecodesubmit() {
      $data['userButtonSetting'] = $this->data['userButtonSetting'];
      $data['menuDetail'] = $this->getMenuData();
      $this->form_validation->set_rules('name', 'Name', 'trim|required');
      if ($this->form_validation->run() == FALSE) {
      $data['alerterror'] = validation_errors();
      $this->userStateChangeSet("Create New pausecode");
      $data['page'] = 'createpausecode';
      $data['title'] = 'Create New pausecode';
      $this->load->view('template', $data);
      } else {
      $name = $this->input->post('name');
      if ($this->config_model->createpausecode($name) == 0)
      $data['alerterror'] = "New pausecode could not be created.";
      else
      $data['alertsuccess'] = "Pausecode created Successfully.";
      $data['table'] = $this->config_model->viewpausecode();
      $data['redirect'] = "site/viewpausecode";
      //$data['other']="template=$template";
      $this->load->view("redirect", $data);
      }
  } */

  function createpausecodesubmit() {
    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();
    $this->form_validation->set_rules('name', 'Name', 'trim|required');
    if ($this->form_validation->run() == FALSE) {
        $data['alerterror'] = validation_errors();
        $this->userStateChangeSet("Create New pausecode");
        $data['page'] = 'createpausecode';
        $data['title'] = 'Create New pausecode';
        $this->load->view('template', $data);
    } else {
        $data = $this->input->post();
        if ($this->config_model->createpausecode($data) == 0)
            $data['alerterror'] = "New pausecode could not be created.";
        else
            $data['alertsuccess'] = "Pausecode created Successfully.";
        $data['table'] = $this->config_model->viewpausecode();
        $this->load->view("redirect", $data);
    }
}

function viewpausecode() {

    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();
    $data['table'] = $this->config_model->viewpausecode();
    $this->userStateChangeSet("View pausecode");
    $data['page'] = 'viewpausecode';
    $data['title'] = 'View pausecode';
    $this->load->view('template', $data);
}

function editpausecode() {

    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();
    $data['editmode'] = "0";
    $data['before'] = $this->config_model->beforeeditpausecode($this->input->get('id'));
        //echo "<pre>";print_r($data['before']);exit;
    $data['hrs'] = 0;
    $data['minutes'] = 0;

    if (isset($data['before']->allowed_pause_time) && ($data['before']->allowed_pause_time > 0)) {
        $data['hrs'] = (int) ($data['before']->allowed_pause_time / 60);
        $data['minutes'] = (int) ($data['before']->allowed_pause_time % 60);
    }

    $this->userStateChangeSet("Edit pausecode");
    $data['page'] = 'editpausecode';
    $data['title'] = 'Edit pausecode';
    $this->load->view('template', $data);
}

function editpausecodesubmit() {

    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();
    $this->form_validation->set_rules('name', 'Name', 'trim|required');
    if ($this->form_validation->run() == FALSE) {
        $data['alerterror'] = validation_errors();
        $data['before'] = $this->config_model->beforeeditpausecode($this->input->post('id'));
        $this->userStateChangeSet("Edit pausecode");
        $data['page'] = 'editpausecode';
        $data['title'] = 'Edit pausecode';
        $this->load->view('template', $data);
    } else {
        $id = $this->input->post('id');
            //$name = $this->input->post('name');
        $pauseTimeHr = $this->input->post('pauseTimeHr');
        $pauseTimeMin = $this->input->post('pauseTimeMin');

        $allowed_pause_time = ($pauseTimeHr * 60) + $pauseTimeMin;

        if ($this->config_model->editpausecode($id, $allowed_pause_time))
            $data['alertsuccess'] = "Pausecode edited successfully.";
        else
            $data['alerterror'] = "Pausecode edit unsuccesful";
        $data['table'] = $this->config_model->viewpausecode();
        $data['redirect'] = "site/viewpausecode";
            //$data['other']="template=$template";
        $this->load->view("redirect", $data);
    }
}

function deletepausecode() {

    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();
    $this->config_model->deletepausecode($this->input->get('id'));
    $data['table'] = $this->config_model->viewpausecode();
    $data['alertsuccess'] = "pausecode Deleted Successfully";
    $data['page'] = 'viewpausecode';
    $data['title'] = 'View pausecode';
    $this->load->view('template', $data);
}

function home() {

    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();
    $this->userStateChangeSet("home");
    $data['page'] = 'home';
    $data['title'] = 'Home';
    $this->load->view('template', $data);
}

function qualityuserlog() {
    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();
    $data['editmode'] = "0";
    $request = $this->input->get_post("request");
    if ($request == "json") {
        $query = $this->input->get_post("query");
        $fromtable = $this->input->get_post("table");
        $data["fromtable"] = $fromtable;
        $page = $this->input->get_post("page");
        $keyword = $this->input->get_post("keyword");
        $filters = $this->input->get_post("filters");
        $filterscompare = $this->input->get_post("filterscompare");
        $orderby = $this->input->get_post("orderby");
        $maxrow = $this->input->get_post("maxrow");
        $forone = $this->db->query("$query LIMIT 0,1")->result_array();
        $keys = array_keys($forone[0]);
        $where = " WHERE ( ";
        $filterwhere = " ( ";
        foreach ($keys as $num => $key) {
            if ($key == 'firstname' || $key == 'lastname') {
                $key = "user";
                $where .= "`user` LIKE '%$keyword%' OR ";
            } else if ($key == 'userstatus') {
                $key = "state";
                $where .= "`state` LIKE '%$keyword%' OR ";
            } else
            $where .= "`$key` LIKE '%$keyword%' OR ";
            if ($filters[$num] != "SELECTALLGODISGREAT")
                $filterwhere .= " `$key` " . $filterscompare[$num] . " '" . $filters[$num] . "' AND ";
        }
        $where .= " 0 ) ";
        $filterwhere .= " 1 ) ";
        $startrow = (($page - 1) * $maxrow);
            //echo $where;
        $data["message"]["content"] = $this->db->query("$query $where AND $filterwhere $orderby LIMIT $startrow,$maxrow")->result_array();
        $data["message"]["totalrow"] = $this->db->query("SELECT count(*)  as `total` FROM $fromtable $where AND $filterwhere")->row();
        $data["message"]["totalrow"] = $data["message"]["totalrow"]->total;
        $data["message"]["maxrow"] = $maxrow;
        $data["message"]["filterwhere"] = $filterwhere;
            //echo "$query $where $filterwhere";
        $this->load->view('json', $data);
    }
    else {
        $data["query"] = "SELECT `userstatelog`.`date` as `date`,`user`.`firstname` as `firstname`,`calling_status`.`name` as `userstatus`,`userstatelog`.`date` ,`userstatelog`.`ip_address` FROM `userstatelog` INNER JOIN `user` ON `user`.`id`=`userstatelog`.`user` AND `user`.`accesslevel`=5 INNER JOIN `calling_status` ON  `userstatelog`.`state`=`calling_status`.`id`";
        $data["fromtable"] = "`userstatelog`";
        $fromtable = $data["fromtable"];
        $query = $data["query"];
        $data["maxrow"] = 25;
        $data["table"] = $this->db->query("$query LIMIT 0," . $data["maxrow"])->result_array();
        $forone = $this->db->query("$query LIMIT 0,1")->result_array();

        $keys = array_keys($forone[0]);
        $data["filters"] = array();
        $i = 0;
        foreach ($keys as $key) {

            if ($key == 'firstname' || $key == 'lastname') {
                $key = "user";
                $data["filters"][$i++] = $this->db->query("SELECT `firstname`,`id` FROM `user` WHERE  `user`.`accesslevel` =5 AND  `user`.`status` < 2  ORDER BY `firstname` ASC ")->result_array();
            } else if ($key == 'userstatus') {
                $key = "state";
                $data["filters"][$i++] = $this->db->query("SELECT `id`,`name` as `userstatus` FROM `calling_status`  ORDER BY `name` ASC ")->result_array();
            } else
            $data["filters"][$i++] = $this->db->query("SELECT DISTINCT `$key` FROM $fromtable INNER JOIN `user` ON `user`.`id`=`userstatelog`.`user` AND `user`.`accesslevel`=5 ORDER BY `$key` ASC ")->result_array();
        }
        $data["totalrow"] = $this->db->query("SELECT count(*) as `total` FROM  $fromtable 
          INNER JOIN `user` ON `user`.`id`=`userstatelog`.`user` AND `user`.`accesslevel`=5")->row();
        $data["totalrow"] = $data["totalrow"]->total;
        $this->userStateChangeSet("qualityuserlog");
        $data['page'] = 'qualityuserlog';
        $data['title'] = 'qualityuserlog';
        $this->load->view('template', $data);
    }
        /*
          $data['page']='qualityuserlog';
          $data['title']='Quality User Log';
          $data['table']=$this->reporting_model->qualityuserlog();
          $this->load->view('template',$data); */
      }

      function getpiechartdata1() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['message'] = $this->reporting_model->getpiechartdata1();
        $this->load->view('json', $data);
    }

    function getmonthlybilling() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['message'] = $this->reporting_model->getmonthlybilling();
        $this->load->view('json', $data);
    }

    function getmonthdetail() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['message'] = $this->reporting_model->getmonthdetail();
        $this->load->view('json', $data);
    }

    function qualityuserlog1() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        /* $this->db->query("SELECT *
          INTO OUTFILE 'myfile.csv'
          FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '"'
          LINES TERMINATED BY '\n'
          FROM `userstatelog`")->row(); */
          $this->db->query("SELECT *
            INTO OUTFILE 'myfile.csv'
            FIELDS TERMINATED BY ',' 
            LINES TERMINATED BY '\n'
            FROM `userstatelog`")->row();
          header('Content-type: text/csv');
          header('Content-disposition: attachment; filename=myfile21.csv');
          readfile('myfile21.csv');
        //unlink('/tmp/myfile.csv');
          exit();
      }

      function viewuserlog() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $today = date("Y-m-d");
        $user = $this->input->get_post('user');
        $request = $this->input->get_post("request");
        $request = $this->input->get_post("request");
        if ($request == "json") {
            $user = $this->input->get_post('user');
            $query = $this->input->get_post("query");
            $fromtable = $this->input->get_post("table");
            $data["fromtable"] = $fromtable;
            $page = $this->input->get_post("page");
            $keyword = $this->input->get_post("keyword");
            $filters = $this->input->get_post("filters");
            $filterscompare = $this->input->get_post("filterscompare");
            $orderby = $this->input->get_post("orderby");
            $maxrow = $this->input->get_post("maxrow");
            $forone = $this->db->query("$query LIMIT 0,1")->result_array();
            $keys = array_keys($forone[0]);
            $mywhere = " WHERE (  `user`='$user' AND ";
            $where = " WHERE   ( ";
            $filterwhere = " ( ";
            foreach ($keys as $num => $key) {
                if ($key == 'firstname' || $key == 'lastname') {
                    $key = "user";
                    $where .= "`user` = '$keyword' OR ";
                } else if ($key == 'userstatus') {
                    $key = "state";
                    $where .= "`state` LIKE '%$keyword%' OR ";
                    $mywhere .= "`state` LIKE '%$keyword%' OR ";
                } else {
                    $where .= "`$key` LIKE '%$keyword%' OR ";
                    $mywhere .= "`$key` LIKE '%$keyword%' OR ";
                }
                if ($filters[$num] != "SELECTALLGODISGREAT")
                    $filterwhere .= " `$key` " . $filterscompare[$num] . " '" . $filters[$num] . "' AND ";
            }
            $where .= " 0 ) ";
            $mywhere .= " 0 ) ";
            $filterwhere .= " 1 AND `user`='$user' ) ";
            $startrow = (($page - 1) * $maxrow);
            //echo $where;
            $data["message"]["content"] = $this->db->query("$query $where AND $filterwhere $orderby LIMIT $startrow,$maxrow")->result_array();
            $data["message"]["totalrow"] = $this->db->query("SELECT count(*)  as `total` FROM $fromtable  $where AND $filterwhere ")->row();
            $data["message"]["totalrow"] = $data["message"]["totalrow"]->total;
            $data["message"]["maxrow"] = $maxrow;
            $data["message"]["filterwhere"] = $filterwhere;
            //echo "$query $where $filterwhere";
            $this->load->view('json', $data);
        }
        else {
            $data["query"] = "SELECT `user`.`firstname` as `firstname`,`calling_status`.`name` as `userstatus`,`userstatelog`.`date` as `date`,`userstatelog`.`date2` ,`userstatelog`.`ip_address` FROM `userstatelog` INNER JOIN `user` ON `user`.`id`=`userstatelog`.`user` AND `user`.`id`='$user' INNER JOIN `calling_status` ON  `userstatelog`.`state`=`calling_status`.`id`";
            $data["fromtable"] = "`userstatelog`";
            $fromtable = $data["fromtable"];
            $query = $data["query"];
            $data["maxrow"] = 25;
            $data["table"] = $this->db->query("$query LIMIT 0," . $data["maxrow"])->result_array();
            $forone = $this->db->query("$query LIMIT 0,1")->result_array();

            $keys = array_keys($forone[0]);
            $data["filters"] = array();
            $i = 0;
            foreach ($keys as $key) {

                if ($key == 'firstname' || $key == 'lastname') {
                    $key = "user";
                    $data["filters"][$i++] = $this->db->query("SELECT `firstname`,`id` FROM `user` WHERE  `user`.`id`='$user' ORDER BY `firstname` ASC ")->result_array();
                } else if ($key == 'userstatus') {
                    $key = "state";
                    $data["filters"][$i++] = $this->db->query("SELECT `id`,`name` as `userstatus` FROM `calling_status`  ORDER BY `name` ASC ")->result_array();
                } else
                $data["filters"][$i++] = $this->db->query("SELECT DISTINCT `$key` FROM $fromtable INNER JOIN `user` ON `user`.`id`=`userstatelog`.`user` AND `user`.`id`='$user' ORDER BY `$key` ASC ")->result_array();
            }
            $data["totalrow"] = $this->db->query("SELECT count(*) as `total` FROM  $fromtable 
              INNER JOIN `user` ON `user`.`id`=`userstatelog`.`user` AND `user`.`id`='$user'")->row();
            $data["totalrow"] = $data["totalrow"]->total;
            $data['user'] = $user;
            $this->userStateChangeSet("viewuserlog");
            $data['page'] = 'viewuserlog';
            $data['title'] = 'viewuserlog';
            $this->load->view('template', $data);
        }

        /*
          $logdate=$this->input->get_post('logdate');
          $data['selecteduser']=$user;
          $data['selecteddate']=$logdate;
          $data['table']=$this->reporting_model->viewuserlog($user,$logdate);
          $data['page']='viewuserlog';
          $data['title']='View User Log';
          $this->load->view('template',$data); */
      }

      function qualitydashboard() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['table'] = $this->reporting_model->qualitydashboarddata();
        $this->userStateChangeSet("Quality Dashboard");
        $data['page'] = 'qualitydashboard';
        $data['title'] = 'Quality Dashboard';
        $this->load->view('template', $data);
    }

    /* function alluserlogs()
      {
      $data['userButtonSetting'] = $this->data['userButtonSetting'];
      $data['editmode']="0";
      $userwhere ="";
      $innerjoin = "";
      $user = $this->session->userdata('id');
      $accesslevel = $this->session->userdata('accesslevel');
      if($accesslevel == 3)
      {

      $innerjoin = " INNER JOIN `process_agent` ON  `process_agent`.`agent`=`user`.`id` INNER JOIN `process` ON `process`.`id`=`process_agent`.`process` INNER JOIN `campaign` ON  `process`.`campaign` = `campaign`.`id` ";
      $userwhere = " AND  `campaign`.`supervisor`=$user ";

      }
      $request=$this->input->get_post("request");
      if($request=="json")
      {
      $query=$this->input->get_post("query");
      $fromtable=$this->input->get_post("table");
      $data["fromtable"]=$fromtable;
      $page=$this->input->get_post("page");
      $keyword=$this->input->get_post("keyword");
      $filters=$this->input->get_post("filters");
      $filterscompare=$this->input->get_post("filterscompare");
      $orderby=$this->input->get_post("orderby");
      $maxrow=$this->input->get_post("maxrow");
      $forone=$this->db->query("$query LIMIT 0,1")->result_array();
      $keys=array_keys($forone[0]);
      $where=" WHERE ( ";
      $filterwhere=" ( ";
      foreach($keys as $num => $key)
      {
      if($key=='firstname' || $key=='lastname')
      {
      $key="user";
      $where.="`user` = '$keyword' OR ";
      }
      else if($key=='userstatus' )
      {
      $key="state";
      $where.="`state` = '$keyword' OR ";
      }
      else
      $where.="`$key` LIKE '%$keyword%' OR ";
      if($filters[$num]!="SELECTALLGODISGREAT")
      $filterwhere.=" `$key` ".$filterscompare[$num]." '". $filters[$num]."' AND ";
      }
      $where.=" 0 ) ";
      $filterwhere.=" 1 ) ";
      $startrow=(($page-1)*$maxrow);
      //echo $where;
      $data["message"]["content"]=$this->db->query("$query $where AND $filterwhere $orderby LIMIT $startrow,$maxrow")->result_array();
      $data["message"]["totalrow"]=$this->db->query("SELECT count(*)  as `total` FROM $fromtable $where AND $filterwhere")->row();
      $data["message"]["totalrow"]=$data["message"]["totalrow"]->total;
      $data["message"]["maxrow"]=$maxrow;
      $data["message"]["filterwhere"]=$filterwhere;
      //echo "$query $where $filterwhere";
      $this->load->view('json',$data);
      }
      else
      {
      $data["query"]="SELECT DISTINCT `user`.`firstname` as `firstname`,`calling_status`.`name` as `userstatus`,`userstatelog`.`date` ,`userstatelog`.`date2` as `date2`,`userstatelog`.`ip_address` FROM `userstatelog` INNER JOIN `user` ON `user`.`id`=`userstatelog`.`user`  INNER JOIN `calling_status` ON  `userstatelog`.`state`=`calling_status`.`id` $innerjoin $userwhere";
      $data["fromtable"]="`userstatelog`";
      $fromtable=$data["fromtable"];
      $query=$data["query"];
      $data["maxrow"]=25;
      $data["table"]=$this->db->query("$query LIMIT 0,".$data["maxrow"])->result_array();
      $forone=$this->db->query("$query LIMIT 0,1")->result_array();

      $keys=array_keys($forone[0]);
      $data["filters"]=array();
      $i=0;

      foreach($keys as $key) {

      if($key=='firstname' || $key=='lastname')
      {
      $key="user";


      $data["filters"][$i++]=$this->db->query("SELECT DISTINCT `firstname`,`user`.`id` FROM `user` $innerjoin $userwhere  ORDER BY `firstname` ASC ")->result_array();

      }
      else if($key=='userstatus' )
      {
      $key="state";
      $data["filters"][$i++]=$this->db->query("SELECT `id`,`name` as `userstatus` FROM `calling_status`  ORDER BY `name` ASC ")->result_array();
      }

      else
      $data["filters"][$i++]=$this->db->query("SELECT DISTINCT `$key` FROM $fromtable INNER JOIN `user` ON `user`.`id`=`userstatelog`.`user`  ORDER BY `$key` ASC ")->result_array();
      }
      $data["totalrow"]=$this->db->query("SELECT  DISTINCT `user`.`firstname` as `firstname`,`calling_status`.`name` as `userstatus`,`userstatelog`.`date` ,`userstatelog`.`date2` as `date2`,`userstatelog`.`ip_address` FROM  $fromtable
      INNER JOIN `user` ON `user`.`id`=`userstatelog`.`user` INNER JOIN `calling_status` ON  `userstatelog`.`state`=`calling_status`.`id` $innerjoin $userwhere ");
      $data["totalrow"]=$data["totalrow"]->num_rows();
      $this->userStateChangeSet("alluserlog");
      $data['page']='alluserlog';
      $data['title']='alluserlog';
      $this->load->view('template',$data);
      }

      $user=$this->input->post('users');
      $logdate=$this->input->post('logdate');
      $logtodate=$this->input->post('logtodate');
      $data['selecteduser']=$user;
      $data['selecteddate']=$logdate;
      $data['selectedtodate']=$logtodate;
      $data['users']=$this->reporting_model->getallusers();
      $data['table']=$this->reporting_model->alluserlog($user,$logdate,$logtodate);
      $data['page']='alluserlog';
      $data['title']='View User Logs';
      $this->load->view('template',$data);
  } */

  function downloadalluserlog() {
    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();
    $cdrPath = $this->config->item('cdrPath');
    $user = $this->input->get_post('user');
    $logdate = $this->input->get_post('logdate');
    $logtodate = $this->input->get_post('logtodate');
    $where = "";
    if ($user != "")
        $where .= " AND `userstatelog`.`user`='$user' ";
    if ($logdate != "" & $logtodate != "") {
        $res = explode("-", $logdate);
        $logdate = $res[2] . "-" . $res[0] . "-" . $res[1];
            //$logdate = date("Y-m-d",strtotime($logdate));
        $res = explode("-", $logtodate);
        $logtodate = $res[2] . "-" . $res[0] . "-" . $res[1];
        $where .= "AND `userstatelog`.`date` BETWEEN '$logdate 00:00:00' AND '$logtodate 23:59:59'";
    }

    $date = date("Y-m-d_H-i-s");
    $filename = "userstate" . $date;
    $filepath = "$cdrPath/$filename.csv";
        //$filepath = "$filename.csv";
    $query = $this->db->query("(SELECT  'User','Userstatus','Start Time' ,'End Time','IP Address')
        UNION 
        (SELECT CONCAT(`user`.`firstname`,' ',`user`.`lastname`) as `user`,`calling_status`.`name` as `userstatus`,`userstatelog`.`date` ,`userstatelog`.`date2`,`userstatelog`.`ip_address` INTO OUTFILE '$filepath'
        FIELDS TERMINATED BY ',' 
        LINES TERMINATED BY '\n'
        FROM `userstatelog` 
        INNER JOIN `user` ON `user`.`id`=`userstatelog`.`user`
        INNER JOIN `calling_status` ON `userstatelog`.`state`=`calling_status`.`id`
        WHERE 1 $where)")->row();
    $this->load->helper('download');
    $path = $filepath;
        //if(is_file($path))
    {
            // required for IE
        if (ini_get('zlib.output_compression')) {
            ini_set('zlib.output_compression', 'Off');
        }

            // get the file mime type using the file extension
        $this->load->helper('file');

        echo $mime = get_mime_by_extension($path);

            // Build the headers to push out the file properly.
            header('Pragma: public');     // required
            //header('Expires: 0');         // no cache
            //header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
            //header('Cache-Control: private',false);
            header('Content-Type: ' . $mime);  // Add the mime type from Code igniter.
            header('Content-Disposition: attachment; filename="' . basename($filename . ".csv") . '"');  // Add the file name
            header('Content-Transfer-Encoding: binary');
            //header('Content-Length: '.filesize($path)); // provide file size
            //header('Connection: close');
            readfile($path); // push it out
            exit();
        }

        $data['selecteduser'] = $user;
        $data['selecteddate'] = $logdate;
        $data['users'] = $this->reporting_model->getallusers();
        $data['table'] = $this->reporting_model->alluserlog($user, $logdate);
        $this->userStateChangeSet("View User Logs");
        $data['page'] = 'alluserlog';
        $data['title'] = 'View User Logs';
        $this->load->view('template', $data);
    }

    function leadpenetrationdashboard() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['campaign'] = $this->process_model->getcampaign();
        $this->userStateChangeSet("Lead Penetration");
        $data['page'] = 'leadpenetrationdashboard';
        $data['title'] = 'Lead Penetration';
        $this->load->view('template', $data);
    }

    function downloadleadpenetration() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $cdrPath = $this->config->item('cdrPath');
        $campaign = $this->input->get_post('campaign');
        $process = $this->input->get_post('process');
        $date = date("Y-m-d_H-i-s");
        $filename = "leadpenetration" . $date;
        $filepath = "$cdrPath/$filename.csv";
        //$filepath = "$filename.csv";
        /* $query=$this->db->query("(SELECT  'Lead','Leadset','Phone' ,'Agent','Call count','Dispose')
          UNION ALL
          (SELECT `lead`.`name` as `lead`,`leadset`.`name` as `leadset`,`lead`.`phone`,CONCAT(`user`.`firstname`,' ',`user`.`lastname`) as `agent`,`disposecall`.`callcount` ,`dispose`.`name` as `dispose`
          INTO OUTFILE '$filepath'
          FIELDS TERMINATED BY ','
          LINES TERMINATED BY '\n'
          FROM `disposecall`
          INNER JOIN `process` ON `process`.`id`=`disposecall`.`process` AND `disposecall`.`process`='$process'
          INNER JOIN `lead` ON `lead`.`id`=`disposecall`.`lead`
          INNER JOIN `leadset` ON `lead`.`leadset`=`leadset`.`id`
          INNER JOIN `user` ON `user`.`id`=`disposecall`.`agent`
          INNER JOIN `dispose` ON `dispose`.`id` = `disposecall`.`dispose`
          ORDER BY `disposecall`.`date`,`lead`.`id` DESC)")->row(); */
          $query = $this->db->query("(SELECT  'Lead','Leadset','Phone' ,'Agent','Call count','Dispose')
            UNION ALL

            ( SELECT `tab2`.`lead`,`tab2`.`leadset`,`tab2`.`phone`,`tab2`.`agent`,`tab2`.`callcount`,`tab2`.`dispose`

            INTO OUTFILE '$filepath'
            FIELDS TERMINATED BY ',' 
            LINES TERMINATED BY '\n'

            FROM (SELECT `lead`.`id` as `leadid`,`lead`.`name` as `lead`,`leadset`.`name` as `leadset`,`lead`.`phone` as `phone`,0 as `callcount`,IFNULL(NULL,'') as `agent`,IFNULL(NULL,'') as `dispose`,IFNULL(NULL,'') as `disposedate` FROM `lead`
            INNER JOIN `leadset` ON `leadset`.`id`=`lead`.`leadset` 
            INNER JOIN `process_leadset` ON `leadset`.`id`=`process_leadset`.`leadset` AND `process_leadset`.`process`='$process'
            UNION
            SELECT `lead`.`id` as `leadid`,`lead`.`name` as `lead`,`leadset`.`name` as `leadset`,`lead`.`phone`,`disposecall`.`callcount`,CONCAT(`user`.`firstname`,' ',`user`.`lastname`) as `agent`,`dispose`.`name` as `dispose`,`disposecall`.`date` as `disposedate` FROM `disposecall` 
            INNER JOIN `process` ON `process`.`id`=`disposecall`.`process` AND `disposecall`.`process`='$process'
            INNER JOIN `lead` ON `lead`.`id`=`disposecall`.`lead`
            INNER JOIN `leadset` ON `lead`.`leadset`=`leadset`.`id`
            INNER JOIN `user` ON `user`.`id`=`disposecall`.`agent`
            INNER JOIN `dispose` ON `dispose`.`id` = `disposecall`.`dispose`
            ORDER BY `disposedate`,`leadid` DESC) as `tab2` )")->row();
          $this->load->helper('download');
          $path = $filepath;
        //if(is_file($path))
          {
            // required for IE
            if (ini_get('zlib.output_compression')) {
                ini_set('zlib.output_compression', 'Off');
            }

            // get the file mime type using the file extension
            $this->load->helper('file');

            echo $mime = get_mime_by_extension($path);

            // Build the headers to push out the file properly.
            header('Pragma: public');     // required
            //header('Expires: 0');         // no cache
            //header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
            //header('Cache-Control: private',false);
            header('Content-Type: ' . $mime);  // Add the mime type from Code igniter.
            header('Content-Disposition: attachment; filename="' . basename($filename . ".csv") . '"');  // Add the file name
            header('Content-Transfer-Encoding: binary');
            //header('Content-Length: '.filesize($path)); // provide file size
            //header('Connection: close');
            readfile($path); // push it out
            exit();
        }
        $data['campaign'] = $this->process_model->getcampaign();
        $this->userStateChangeSet("Lead Penetration");
        $data['page'] = 'leadpenetrationdashboard';
        $data['title'] = 'Lead Penetration';
        $this->load->view('template', $data);
    }

    function agentperformance() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("Agent Performance");
        $agent = $this->input->get_post('agent');
        $startDate = $this->input->get_post('startDate');
        $endDate = $this->input->get_post('endDate');
        $data['page'] = 'viewagentperformance';
        $data['title'] = 'Agent Performance';
        $data['selectedagent'] = $agent;
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;
        $data['agents'] = $this->reporting_model->getagent();
        $data['agentdashboarddata'] = $this->user_model->agentDispose($agent, $startDate, $endDate);
        $data['agentperformance'] = $this->reporting_model->agentperformance($agent, $startDate, $endDate);

        // print_r($data['agentperformance']);
        $this->load->view('template', $data);
    }

    // function  agentPerformanceReport start
    function agentPerformanceReport() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $agentPerformanceReport = $this->reporting_model->agentPerformanceReport();
        if ($agentPerformanceReport->output == "TRUE") {
            $data['agentperformance'] = $agentPerformanceReport->data;
            $data['agentArray'] = $agentPerformanceReport->agentArray;
        } else {
            $data['alerterror'] = $agentPerformanceReport->message;
        }

        $data['page'] = 'agentPerformanceReport';
        $data['title'] = 'Agent Performance Report';

        $this->load->view('template', $data);
    }

    //function agentPerformanceReport  end
    // function  agentPerformanceReport start
    function agentSummaryReport() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $agentPerformanceReport = $this->reporting_model->agentSummaryReport();
        $accesslevel = $this->session->userdata('accesslevel');
        if ($agentPerformanceReport->output == "TRUE") {

            $data['agentperformance'] = $agentPerformanceReport->data;
            $data['userCsv'] = $agentPerformanceReport->userCsv;
            $data['agentArray'] = $agentPerformanceReport->agentArray;

            $data['agentPauseDataArray'] = $agentPerformanceReport->agentPauseDataArray;
            $data['uniquePauseNames'] = $agentPerformanceReport->uniquePauseNames;
        } else {
            $data['alerterror'] = $agentPerformanceReport->message;
        }

        $configArray = array(
            106 => (object) array('variableName' => 'agentSummaryRefresh', 'defaultValue' => '0'),
            107 => (object) array('variableName' => 'agentSummaryRefreshAccesslevel', 'defaultValue' => ''),
        );
        if (isset($configData->agentSummaryRefresh) && $configData->agentSummaryRefresh == 1) {

            if ($accesslevel == 1) {

                $data['summaryResendButton'] = 1;
            } else {

                if (isset($configData->agentSummaryRefreshAccesslevel) && $configData->agentSummaryRefreshAccesslevel != "") {

                    if (strpos($configData->agentSummaryRefreshAccesslevel, $accesslevel) !== FALSE) {

                        $data['summaryResendButton'] = 1;
                    } else {

                        $data['summaryResendButton'] = 0;
                    }
                } else {

                    $data['summaryResendButton'] = 0;
                }
            }
        } else {

            $data['summaryResendButton'] = 0;
        }

        $data['page'] = 'agentSummaryReport';
        $data['title'] = 'agentSummaryReport';

        $this->load->view('template', $data);
    }

    //function agentPerformanceReport  monthdownload
    function agentSummaryDownload() {
        $startDate = $this->input->get_post('startDate');
        $endDate = $this->input->get_post('endDate');
        $userCsv = $this->input->get_post('userCsv');

        $reportOpt = $this->download_model->agentSummaryDownload($startDate, $endDate, $userCsv);
        redirect($reportOpt);
    }

    function agentSummaryReportDownload() {
        $startDate = $this->input->get_post('startDate');
        $endDate = $this->input->get_post('endDate');
        $userCsv = $this->input->get_post('userCsv');

        $reportOpt = $this->download_model->agentSummaryReportDownload($startDate, $endDate, $userCsv);
        redirect($reportOpt);
    }

    //function agentPerformanceReport  daily
    function agentDailySummaryReportDownload() {
        $startDate = $this->input->get_post('startDate');
        $endDate = $this->input->get_post('endDate');
        $userCsv = $this->input->get_post('userCsv');

        $reportOpt = $this->download_model->agentDailySummaryReportDownload($startDate, $endDate, $userCsv);
        redirect($reportOpt);
    }

    // function pauseBreakDownReport start
    function pauseBreakDownReport() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $date = date('Y-m-d');
        $pauseBreakDownReport = $this->reporting_model->pauseBreakDownReport($date);
        if ($pauseBreakDownReport->output == "TRUE") {
            $data['pauseBreakDownReport'] = $pauseBreakDownReport;
        } else {
            $data['alerterror'] = $pauseBreakDownReport->message;
        }

        $data['page'] = 'pauseBreakDownReport';
        $data['title'] = 'Agent Performance Report';
        $this->load->view('template', $data);
    }

    function pauseBreakDownAllReports() {
        // $date = date('Y-m-d') ;      
        $agentid = $this->input->get_post('agentid');
        $date = $this->input->get_post('date');
        $result = $this->reporting_model->pauseBreakDownAllReports($agentid, $date);
        $data['pausecode'] = $this->process_model->getpausecode();
        $data['result'] = $result;
        $this->load->view('backend/includes/pauseBreakDownAllReports', $data);
    }

    // function pauseBreakDownReport end

    function pauseBreakDownAllEXcel() {
        $data = array();
        $agentname = array();
        $pauseDetail = array();
        $total_output = array();
        $date = "";


        $date = $this->input->get_post('date');
        if ($date == "") {
            $date = date('Y-m-d');
        } else {
            $date = $date;
        }
        $pauseBreakDownReport = $this->reporting_model->pauseBreakDownAllEXcel($date);

        foreach ($pauseBreakDownReport as $key => $value) {

            $username = $value['agent_username'];
            $pause_name = $value['pause_name'];

            if (isset($total_output[$username])) {

                if (isset($total_output[$username][$pause_name])) {

                    $tmp_array = array();
                    $tmp_array['startTime'] = $value['startTime'];
                    $tmp_array['endTime'] = $value['endTime'];
                    $tmp_array['timeDiffSec'] = $value['timeDiffSec'];

                    array_push($total_output[$username][$pause_name], $tmp_array);
                } else {
                    $total_output[$username][$pause_name] = array();
                    $tmp_array = array();
                    $tmp_array['startTime'] = $value['startTime'];
                    $tmp_array['endTime'] = $value['endTime'];
                    $tmp_array['timeDiffSec'] = $value['timeDiffSec'];

                    array_push($total_output[$username][$pause_name], $tmp_array);
                }
            } else {
                $total_output[$username] = array();
                $total_output[$username][$pause_name] = array();
                $tmp_array = array();
                $tmp_array['startTime'] = $value['startTime'];
                $tmp_array['endTime'] = $value['endTime'];
                $tmp_array['timeDiffSec'] = $value['timeDiffSec'];

                array_push($total_output[$username][$pause_name], $tmp_array);
            }
        }

        $path = $this->config->item('cdrPath');
        $base_url = $this->config->item('base_url');
        $filename = "pauselogger" . $date . ".csv";
        $fp = fopen($path . "/" . $filename, 'w+');

        fputcsv($fp, array('Name', 'Pause Type', ' Start Time', 'End Time'));
        foreach ($total_output as $username => $pausedetail) {

            $temp = array();
            foreach ($pausedetail as $pause_name => $pauseTimeDetail) {
                $temp['agentName'] = $username;
                $temp['pause_name'] = $pause_name;
                foreach ($pauseTimeDetail as $key => $val) {
                    $temp['startTime'] = $val['startTime'];
                    $temp['endTime'] = $val['endTime'];
                    $temp['timeDiffSec'] = $val['timeDiffSec'];
                    echo "<pre>";
                    print_r($temp);
                    fputcsv($fp, $temp);
                }
            }
        }

        fclose($fp);
        redirect(base_url() . "CDRs/" . $filename);
    }

    //callback
    function viewcallback() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $data['agents'] = $this->reporting_model->getagent();
        $data['selectedagent'] = $this->input->get_post('agent');
        if ($data['selectedagent'] != "") {
            $data['table'] = $this->callback_model->viewcallback($this->input->get_post('agent'));
        }

        $this->userStateChangeSet("View callback");

        $data['page'] = 'viewcallback';
        $data['title'] = 'View callback';
        $this->load->view('template', $data);
    }

    function editcallback() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "1";
        $data["processName"] = $this->input->get_post('processName');
        $data["phoneNumber"] = $this->input->get_post('phoneNumber');
        $data["dateTime"] = $this->input->get_post('dateTime');
        $data["agentName"] = $this->input->get_post('agentName');
        $data["agentId"] = $this->input->get_post('agentId');
        $data["leadId"] = $this->input->get_post('id');

        // $data['before']=$this->callback_model->beforeedit($this->input->get('id'));
        $data['agents'] = $this->user_model->getagents();
        $this->userStateChangeSet("Edit callback");
        // print_r($data);
        $data['page'] = 'editcallback';
        $data['title'] = 'Edit callback';
        $this->load->view('template', $data);
    }

    function editcallbacksubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();


        $id = $this->input->post('id');
        $agentId = $this->input->post('agentId');
        $callbackuser = $this->input->post('callbackuser');
        // $callbackdate  = date("Y-m-d",strtotime($callbackdate));
        // $callbacktime  = date("H:i",strtotime($this->input->post('callbacktime')));
        // $callbacktime  = $callbacktime.":00";
        // $callbacktime  = date("H:i:s",strtotime($callbacktime));
        // echo "id".$id;
        // echo "agentId".$agentId;
        // echo "callbackuser".$callbackuser;
        $result = $this->lua_model->reassignCallback($agentId, $callbackuser, $id);
        // print_r($result);
        if ($result->OUTPUT == "FALSE")
            $data['alerterror'] = "callback Editing was unsuccesful";
        else
            $data['alertsuccess'] = "callback edited Successfully.";
        $data['selectedagent'] = $this->input->get_post('agent');
        $data['agents'] = $this->user_model->getagents();
        $data['redirect'] = "site/viewcallback";
        $this->userStateChangeSet("View callback");
        $data['page'] = 'viewcallback';
        $data['other'] = 'agent=' . $this->input->get_post('callbackuser');
        $data['title'] = 'View callback';
        $this->load->view('redirect', $data);
    }

    function viewagentCallback() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $data['selectedagent'] = $this->input->get_post('agent');
        $data['table'] = $this->callback_model->viewcallback($this->session->userdata('id'));
        $this->userStateChangeSet("agentCallback");
        $data['page'] = 'agentCallback';
        $data['title'] = 'View callback';
        $this->load->view('template', $data);
    }

    function viewfollowup() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $data['selectedagent'] = $this->input->get_post('agent');
        $data['agents'] = $this->user_model->getagents();
        if ($data['selectedagent'] != "") {
            $data['table'] = $this->followup_model->viewfollowup($this->input->get_post('agent'));
        }
        $this->userStateChangeSet("viewfollowup");

        $data['page'] = 'viewfollowup';
        $data['title'] = 'View followup';
        $this->load->view('template', $data);
    }

    function editfollowup() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "1";
        $data["processName"] = $this->input->get_post('processName');
        $data["phoneNumber"] = $this->input->get_post('phoneNumber');
        $data["dateTime"] = $this->input->get_post('dateTime');
        $data["agentName"] = $this->input->get_post('agentName');
        $data["agentId"] = $this->input->get_post('agentId');
        $data["leadId"] = $this->input->get_post('id');
        $data['agents'] = $this->user_model->getagents();
        $this->userStateChangeSet("editfollowup");
        $data['page'] = 'editfollowup';
        $data['title'] = 'Edit followup';
        $this->load->view('template', $data);
    }

    function editfollowupsubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();

        $id = $this->input->post('id');
        $agentId = $this->input->post('agentId');
        $followupuser = $this->input->post('followupuser');
        // $callbackdate  = date("Y-m-d",strtotime($callbackdate));
        // $callbacktime  = date("H:i",strtotime($this->input->post('callbacktime')));
        // $callbacktime  = $callbacktime.":00";
        // $callbacktime  = date("H:i:s",strtotime($callbacktime));
        // echo "id".$id;
        // echo "agentId".$agentId;
        // echo "callbackuser".$callbackuser;
        $result = $this->lua_model->reassignFollowUp($agentId, $followupuser, $id);
        // print_r($result);
        if ($result->OUTPUT == "FALSE")
            $data['alerterror'] = "follow Editing was unsuccesful";
        else
            $data['alertsuccess'] = "follow edited Successfully.";
        $data['selectedagent'] = $this->input->get_post('agent');
        $data['agents'] = $this->user_model->getagents();
        $data['redirect'] = "site/viewfollowup";
        $this->userStateChangeSet("viewfollowup");
        $data['page'] = 'viewfollowup';
        $data['other'] = 'agent=' . $this->input->get_post('followupuser');
        $data['title'] = 'viewfollowup';
        $this->load->view('redirect', $data);
    }

    function userpriority() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['campaign'] = $this->process_model->getcampaign();
        $this->userStateChangeSet("User Priority");
        $data['page'] = 'userpriority';
        $data['title'] = 'User Priority';
        $this->load->view('template', $data);
    }

    function userprioritysubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $process = $this->input->post('process');
        $campaign = $this->input->post('campaign');
        $agent = $this->input->post('agent');
        $priority = $this->input->post('priority');
        if ($this->user_model->userprioritysubmit($process, $campaign, $agent, $priority) == 0)
            $data['alerterror'] = "User priority Editing was unsuccesful";
        else
            $data['alertsuccess'] = "User priority edited Successfully.";
        $data['redirect'] = "site/userpriority";
        //$this->load->view("redirect2",$data);
        $this->load->view('redirect', $data);
    }

    public function uploadratesheet($gatewayId, $gatewayName) {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $data['gatewayId'] = $gatewayId;
        $data['gatewayName'] = $gatewayName;
        $data['page'] = 'uploadratesheet';
        $data['title'] = 'Upload ratesheet';
        $this->load->view('template', $data);
    }

    public function uploadratesheetsubmit($gatewayId, $gatewayName) {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        //uploading files
        $config['upload_path'] = './uploads/csv/';
        $config['allowed_types'] = '*';
        $config['max_size'] = 1024 * 8;
        $config['encrypt_name'] = TRUE;

        $this->load->library('upload', $config);
        $file_element_name = 'csv';
        $csvfile = "";
        if ($this->upload->do_upload($file_element_name)) {
            $uploaddata = $this->upload->data();
            $csvfile = $uploaddata['file_name'];
        }

        $filepath = base_url() . "uploads/csv/" . $csvfile;
        $csvData = $this->csvreader
        ->parse_file($filepath);
        $this->gateway_model->uploadRatesheet($csvData, $gatewayId, $gatewayName);
        $data['redirect'] = "site/viewRateSheetDetail";
        $data['other'] = "gatewayId=$gatewayId&gatewayName=$gatewayName";
        $this->load->view("redirect", $data);
    }

    public function viewratesheet() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";

        $request = $this->input->get_post("request");
        if ($request == "json") {
            $query = $this->input->get_post("query");
            $fromtable = $this->input->get_post("table");
            $data["fromtable"] = $fromtable;
            $page = $this->input->get_post("page");
            $keyword = $this->input->get_post("keyword");
            $filters = $this->input->get_post("filters");
            $filterscompare = $this->input->get_post("filterscompare");
            $orderby = $this->input->get_post("orderby");
            $maxrow = $this->input->get_post("maxrow");
            $forone = $this->db->query("$query LIMIT 0,1")->result_array();
            $keys = array_keys($forone[0]);
            $where = " WHERE ( ";
            $filterwhere = " ( ";
            foreach ($keys as $num => $key) {
                $where .= "`$key` LIKE '%$keyword%' OR ";
                if ($filters[$num] != "SELECTALLGODISGREAT")
                    $filterwhere .= " `$key` " . $filterscompare[$num] . " '" . $filters[$num] . "' AND ";
            }
            $where .= " 0 ) ";
            $filterwhere .= " 1 ) ";
            $startrow = (($page - 1) * $maxrow);
            //echo $where;
            $data["message"]["content"] = $this->db->query("$query $where AND $filterwhere $orderby LIMIT $startrow,$maxrow")->result_array();
            $data["message"]["totalrow"] = $this->db->query("SELECT count(*)  as `total` FROM $fromtable $where AND $filterwhere")->row();
            $data["message"]["totalrow"] = $data["message"]["totalrow"]->total;
            $data["message"]["maxrow"] = $maxrow;
            $data["message"]["filterwhere"] = $filterwhere;
            //echo "$query $where $filterwhere";
            $this->load->view('json', $data);
        }
        else {
            $data["query"] = "SELECT `country`,`countrydialcode`,`usd`,`effectivedate`,`status` FROM `ratesheet` ";
            $data["fromtable"] = "`ratesheet`";
            $fromtable = $data["fromtable"];
            $query = $data["query"];
            $data["maxrow"] = 25;
            $data["table"] = $this->db->query("$query LIMIT 0," . $data["maxrow"])->result_array();
            $forone = $this->db->query("$query LIMIT 0,1")->result_array();
            $keys = array_keys($forone[0]);
            $data["filters"] = array();
            $i = 0;
            foreach ($keys as $key) {
                $data["filters"][$i++] = $this->db->query("SELECT DISTINCT `$key` FROM $fromtable  ORDER BY `$key` ASC ")->result_array();
            }
            $data["totalrow"] = $this->db->query("SELECT count(*) as `total` FROM $fromtable ")->row();
            $data["totalrow"] = $data["totalrow"]->total;
            $this->userStateChangeSet("View Ratesheet");
            $data['page'] = 'viewratesheet';
            $data['title'] = 'View Ratesheet';
            $this->load->view('template', $data);
        }
    }

    function viewCallCount() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['output'] = $this->gateway_model->viewCallCount(0, 0, 0, 0);
        // viewCallCount 0,0 Gives last seven day all data
        // viewCallCount($campaignId ,$processId,$dateStart. $dateEnd)
        $data['page'] = 'viewCallCount';
        $data['title'] = 'View Call Count';
        $this->load->view('template', $data);
    }

    function getCallCountData() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $campaignCsv = $this->input->get_post('campaignCsv');
        $processCsv = $this->input->get_post('processCsv');
        $startDate = $this->input->get_post('startDate');
        $endDate = $this->input->get_post('endDate');

        $data['output'] = $this->gateway_model->viewCallCount($campaignCsv, $processCsv, $startDate, $endDate);
        // viewCallCount 0,0 Gives last seven day all data
        // viewCallCount($campaignId ,$processId,$dateStart. $dateEnd)
        $data['page'] = 'viewCallCount';
        $data['title'] = 'View Call Count';
        $this->load->view('template', $data);
    }

    function viewGateWayDetail() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['table'] = $this->gateway_model->getGatewayDetail();
        $data['page'] = 'viewGateWayDetail';
        $data['title'] = 'View Gateway Detail';
        $this->load->view('template', $data);
    }

    function viewRateSheetDetail() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->load->library("pagination");
        $config = array();

        if ($this->input->get_post('gatewayId')) {
            $newdata = array(
                'gatewayId' => $this->input->get_post('gatewayId'),
                'gatewayName' => $this->input->get_post('gatewayName'),
            );
            $this->session->set_userdata($newdata);
            $data['gatewayId'] = $this->input->get_post('gatewayId');
            $data['gatewayName'] = $this->input->get_post('gatewayName');
            $config['page_query_string'] = FALSE;
            $config['base_url'] = site_url("site/viewRateSheetDetail");
        } else {
            $config['page_query_string'] = FALSE;
            $config['base_url'] = site_url("site/viewRateSheetDetail");
            $data['gatewayId'] = $this->session->userdata('gatewayId');
            $data['gatewayName'] = $this->session->userdata('gatewayName');
        }
        $config["total_rows"] = $this->gateway_model->getRatesheetDetailCount();
        $config["per_page"] = 50;
        $config["uri_segment"] = 3;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $data['currentPage'] = $page;
        $data["table"] = $this->gateway_model->getRatesheetDetail($config["per_page"], $page);
        $data["links"] = $this->pagination->create_links();
        $data['start'] = $page;
        $data['page'] = 'viewRatesheet';
        $data['title'] = 'View Ratesheet';
        $this->load->view('template', $data);
    }

    function viewCampaignGatewayMapping() {
        if ($this->session->userdata('supervisorGatewayView') &&
            $this->session->userdata('supervisorGatewayView') == 0 && $accesslevel == 3) {
            $data['alerterror'] = "Sorry You don't Have access.";
        $data['redirect'] = "site/agentdashboard";
        $this->load->view("redirect", $data);
    } else {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['table'] = $this->campaign_model->getCampaignDetail();
        $data['page'] = 'viewCampaignGatewayMapping';
        $data['title'] = 'View Gateway Detail';
        $this->load->view('template', $data);
    }
}

function viewProcessGatewayMapping($campaignId, $campaignName) {
    if ($this->session->userdata('supervisorGatewayView') &&
        $this->session->userdata('supervisorGatewayView') == 0 && $accesslevel == 3) {
        $data['alerterror'] = "Sorry You don't Have access.";
    $data['redirect'] = "site/agentdashboard";
    $this->load->view("redirect", $data);
} else {
    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();
    $data['campaignId'] = $campaignId;
    $data['campaignName'] = $campaignName;
    $data['table'] = $this->process_model->getProcessDetail($campaignId);
    $data['processLcrStatus'] = $this->gateway_model->getProcessLcrStatus($campaignId);
    $data['page'] = 'viewProcessGatewayMapping';
    $data['title'] = 'View Gateway Detail';
    $this->load->view('template', $data);
}
}

function viewPaymentDetail() {
    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();
    $data['table'] = $this->payment_model->getPaymentDetail();
    $data['page'] = 'viewPaymentDetail';
    $data['title'] = 'View Payment Detail';
    $this->load->view('template', $data);
}

function createbilling() {
    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();
    $data['page'] = 'createbilling';
    $data['title'] = 'Create billing';
    $this->load->view('template', $data);
}

function createbillingsubmit() {
    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();
    $this->form_validation->set_rules('amount', 'Amount', 'trim');
    $this->form_validation->set_rules('comment', 'Comment', 'trim');
    if ($this->form_validation->run() == FALSE) {
        $data['alerterror'] = validation_errors();
        $data['page'] = 'createbilling';
        $data['title'] = 'Create New billing';
        $this->load->view('template', $data);
    } else {
        $amount = $this->input->post('amount');
        $comment = $this->input->post('comment');
        if ($this->payment_model->create($amount, $comment) == 0)
            $data['alerterror'] = "New billing could not be created.";
        else
            $data['alertsuccess'] = "billing created Successfully.";
        $data['table'] = $this->payment_model->getPaymentDetail();
        ;
        $data['redirect'] = "site/viewPaymentDetail";
            //$data['other']="template=$template";
        $this->load->view("redirect", $data);
            /* $data['page']='viewbillings';
              $data['title']='View billings';
              $this->load->view('template',$data); */
          }
      }

      function editBilling() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $id = $this->input->get_post('id');
        $data['page'] = 'editBilling';
        $data['before'] = $this->payment_model->paymentDetailByid($id);
        $data['title'] = 'Create billing';
        $this->load->view('template', $data);
    }

    function autoProcessDetail() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("Agent Performance");
        $data['page'] = 'viewAutoProcessDetails';
        $data['title'] = 'Agent Performance';
        $campaignData = $this->campaign_model->getAutoProcessCampaign();
        if ($campaignData->output == "FALSE") {
            $data['alerterror'] = $campaignData->message;
        }
        $data['campaign'] = $campaignData->campaign;

        $processData = $this->process_model->getAutoProcess();
        if ($processData->output == "FALSE") {
            $data['alerterror'] = $processData->message;
        } else {
            $data['transferAgent'] = $this->standard_model->getTransferAgentData($processData->processCsv);
        }
        $data['process'] = $processData->process;
        $data['accesslevel'] = $this->standard_model->getAllAccessLevel();
        // $data['table']=$this->reporting_model->agentperformance($agent,$logdate,$logtodate);
        $this->load->view('template', $data);
    }

    function editBillingSubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();

        $this->form_validation->set_rules('amount', 'Amount', 'trim');
        $this->form_validation->set_rules('comment', 'Comment', 'trim');
        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $data['page'] = 'createbilling';
            $data['title'] = 'Create New billing';
            $this->load->view('template', $data);
        } else {
            $id = $this->input->post('id');
            $amount = $this->input->post('amount');
            $comment = $this->input->post('comment');
            $editOpt = $this->payment_model->edit($id, $amount, $comment);
            if ($editOpt == 0) {
                $data['alerterror'] = "billing detail Not Updated.";
            } else if ($editOpt == 1) {
                $data['alertsuccess'] = "billing updated Successfully.";
            } elseif ($editOpt == 2) {
                $data['alerterror'] = "billing Status Already Changed";
            }
            $data['table'] = $this->payment_model->getPaymentDetail();
            ;
            $data['redirect'] = "site/viewPaymentDetail";

            $this->load->view("redirect", $data);
        }
    }

    function changePaymentStatus() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        if ($this->session->userdata('accesslevel') == 1 && $this->session->userdata('accesslevel') != "") {

            $id = $this->input->get_post('id');
            $approved = $this->input->get_post('approved');
            $previousApproval = $this->input->get_post('previousApproval');
            $changePaymentStatusOpt = $this->payment_model->changePaymentStatus($id, $approved, $previousApproval);
            if ($changePaymentStatusOpt == 0) {
                $data['alerterror'] = "billing Status Not Updated.";
            } else if ($changePaymentStatusOpt == 1) {
                $data['alertsuccess'] = "billing Status updated Successfully.";
            } elseif ($changePaymentStatusOpt == 2) {
                $data['alerterror'] = "billing Status Already Changed";
            }
        }
        $data['table'] = $this->payment_model->getPaymentDetail();
        ;
        $data['redirect'] = "site/viewPaymentDetail";

        $this->load->view("redirect", $data);
    }

    function logger() {

        $accesslevel = $this->session->userdata('accesslevel');


        //  page default function
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['checkFeatureAllow'] = $this->standard_model->checkFeatureAllow('1', 'multilevel_dispose_allow');
        $data['dispose'] = $this->dispose_model->getdispose();
        $data['leadset'] = $this->lead_model->getleadset();
        $data['recordDownloadFlag'] = $this->config_model->getRecordDownloadSetting();
        $data['recordDownloadJson'] = $this->user_model->getCustDownloadJson();
        $data['callbackDisposeArray'] = $this->callback_model->getCallbackDisposeArray();
        $this->userStateChangeSet("logger");
        $data['qualityEvalutionRefList'] = $this->process_model->getqualityref();
        if ($data['qualityEvalutionRefList']->status == "true") {
            $data['qualityEvaRefList'] = $data['qualityEvalutionRefList']->result;
        }
        //voitekk documentation variables
        $configArray = array(
            99 => (object) array('variableName' => 'recodingDetailFromMongo', 'defaultValue' => '0'),
            93 => (object) array('variableName' => 'recordingPublicFolderUrl', 'defaultValue' => ''),
            94 => (object) array('variableName' => 'recordingLocalFolderUrl', 'defaultValue' => ''),
            100 => (object) array('variableName' => 'recodingDownload', 'defaultValue' => '0'),
            104 => (object) array('variableName' => 'apiResend', 'defaultValue' => '0'),
            105 => (object) array('variableName' => 'apiResendAccessLevel', 'defaultValue' => ''),
            120 => (object) array('variableName' => 'crmUniqueId', 'defaultValue' => '0'),
            121 => (object) array('variableName' => 'crmUniqueIdName', 'defaultValue' => 'CRM Id'),
            126 => (object) array('variableName' => 'recordingSecondaryPath', 'defaultValue' => ''),
        );

        $configData = $this->config_model->getMultipleConfig($configArray);
        $selected_agents = '';
        $data['configData'] = $configData;

        $data['editmode'] = "0";
        $perPage = 50;
        $custName = $this->input->get_post('custName');
        $process = $this->input->get_post('process');
        $campaign = $this->input->get_post('campaign');
        $mydate = $this->input->get_post('date');
        $mydateto = $this->input->get_post('dateto');
        $phoneno = $this->input->get_post('phoneno');
        $disposeArray = $this->input->get_post('dispose');
        $evaluation = $this->input->get_post('evaluation');
        $direction = $this->input->get_post('direction');
        $dispose = '';
        if (is_array($disposeArray) && count($disposeArray) > 0) {
            foreach ($disposeArray as $key => $fistLevelDispose) {
                if ($dispose == "") {
                    $dispose = '"' . $fistLevelDispose . '"';
                } else {
                    $dispose .= ',"' . $fistLevelDispose . '"';
                }
            }
        }else{
            $disposeArray = [];
        }

        $dispose2Array = $this->input->get_post('dispose2');
        $dispose2 = '';
        if (is_array($dispose2Array) && count($dispose2Array) > 0) {
            foreach ($dispose2Array as $key => $secondLevelDispose) {
                if ($dispose2 == "") {
                    $dispose2 = '"' . $secondLevelDispose . '"';
                } else {
                    $dispose2 .= ',"' . $secondLevelDispose . '"';
                }
            }
        }else{
            $dispose2Array =[];
        }

        $dispose3Array = $this->input->get_post('dispose3');
        $dispose3 = '';
        if (is_array($dispose3Array) && count($dispose3Array) > 0) {
            foreach ($dispose3Array as $key => $thirdLevelDispose) {
                if ($dispose3 == "") {
                    $dispose3 = '"' . $thirdLevelDispose . '"';
                } else {
                    $dispose3 .= ',"' . $thirdLevelDispose . '"';
                }
            }
        }else{
            $dispose3Array = [];
        }

        $leadset = $this->input->get_post('leadset');
        $dissconnector = $this->input->get_post('dissconnector');
        $loggerdidNumber = $this->input->get_post('loggerdidNumber');

        if ($configData->crmUniqueId == 1) {

            $crmId = $this->input->get_post('crmId');
        } else {

            $crmId = "";
        }
        
        $selected_agents =[];

        if (( $accesslevel <= 3 || $accesslevel == 10 || $accesslevel == 5 || $accesslevel == 6 ) && $this->session->userdata('accesslevel') != "") {

            $agentArray = $this->input->post('agent');

            //echo "<pre>";
            //print_r($agentArray);
            if (is_array($agentArray) && count($agentArray) > 0) {
                $selected_agents = $agentArray;
                foreach ($agentArray as $key => $value) {
                    if ($agent == "") {
                        $agent = $value;
                    } else {
                        $agent .= "," . $value;
                    }
                }
            } else {
                $selected_agents =[];
                $agent = "";
            }
        } else {
            $agent = "";
        }


        if ($accesslevel == 4) {

            $userId = $this->session->userdata('id');
            $userName = $this->session->userdata('username');
            $data['userSetting'] = $this->user_model->getAgentSettings($userId, $userName);
            $otherProcessFlag = 0;
            $sameProcessFlag = 0;
            $otherProcessArray = array();
            $data["agentNameShow"] = 0;
            if (isset($data['userSetting']->extraProcessReportAllows) &&
                $data['userSetting']->extraProcessReportAllows == "1") {

                $otherProcessFlag = 1;
        }
        if (isset($data['userSetting']->sameProcessReportAllows) &&
            $data['userSetting']->sameProcessReportAllows == "1") {

            $sameProcessFlag = 1;
    }

    if (isset($data['userSetting']->otherProcessSetting)) {

        $otherProcessArray = $data['userSetting']->otherProcessSetting;
    }

    if ($otherProcessFlag == 1 || $sameProcessFlag == 1) {

        if ($sameProcessFlag != 1) {

            $processData = $this->process_model->getUserMappedProcessALL($sameProcessFlag, $otherProcessFlag, $otherProcessArray);
            if ($processData->allProcess != "") {

                $data['agent'] = $this->process_model->getAgentFromProcess($processData->extraProcess);
                $data['campaign'] = $this->process_model->getCampaignFromProcess($processData->allProcess);
                        // $data['process']             =  $this->process_model->getProcessFromProcess( $processData->allProcess );
                $campaignProcessData = $this->process_model->getSelectedProcessFromProcess($processData->allProcess);
                $data['process'] = $campaignProcessData->process;
            } else {

                $data['agent'] = $this->reporting_model->getagent();
                $data['campaign'] = $this->reporting_model->getcampaign();
                        // $data['process']             =  $this->reporting_model->getprocess();
                $campaignProcessData = $this->process_model->getLiveProcess();
                $data['process'] = $campaignProcessData->process;
            }
        } else {

            $processData = $this->process_model->getUserMappedProcess($sameProcessFlag, $otherProcessFlag, $otherProcessArray);

            if ($processData != "") {

                $data['agent'] = $this->process_model->getAgentFromProcess($processData);
                $data['campaign'] = $this->process_model->getCampaignFromProcess($processData);
                $campaignProcessData = $this->process_model->getSelectedProcessFromProcess($processData);
                $data['process'] = $campaignProcessData->process;
            } else {

                $data['agent'] = $this->reporting_model->getagent();
                $data['campaign'] = $this->reporting_model->getcampaign();
                $campaignProcessData = $this->process_model->getLiveProcess();
                $data['process'] = $campaignProcessData->process;
            }
        }

        $data["agentNameShow"] = 1;
    } else {

        $data["agentNameShow"] = 0;
        $data['campaign'] = $this->reporting_model->getcampaign();
        $campaignProcessData = $this->process_model->getLiveProcess();
        $data['process'] = $campaignProcessData->process;
    }
} else {

    $data['userSetting'] = "FALSE";
    if ($accesslevel == 7 || $accesslevel == 8 || $accesslevel == 9) {

        $data["agentNameShow"] = 0;
    } else {

        $data["agentNameShow"] = 1;
    }

    $data['agent'] = $this->reporting_model->getagent();
    $data['campaign'] = $this->reporting_model->getcampaign();
    $campaignProcessData = $this->process_model->getLiveProcess();
    $data['process'] = $campaignProcessData->process;
}


$data['selectedAgent'] = $selected_agents;
$data['selectedCustName'] = $custName;
$data['selectedProcess'] = $process;
$data['selectedCampaign'] = $campaign;
$data['selectedPhoneNo'] = $phoneno;
$data['selectedLeadset'] = $leadset;
$data['selectedDispose'] = $disposeArray;
$data['selectedDispose2'] = $dispose2Array;
$data['selectedDispose3'] = $dispose3Array;
$data['selectedDissconnector'] = $dissconnector;
$data['selectedLoggerDidNumber'] = $loggerdidNumber;
$data['selectedDate'] = $mydate;
$data['selectedDateto'] = $mydateto;
$data['selectedCrmId'] = $crmId;
$data['selectedEvaluation'] = $evaluation;
$data['selecteddirection'] = $direction;

$this->load->library("pagination");

$config = array();
$config['page_query_string'] = FALSE;
$config['base_url'] = site_url("site/logger");

$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

$data['pageCount'] = $page;

if ($accesslevel == 4) {

    if (isset($configData->recodingDownload) && $configData->recodingDownload == 1) {

        $data['recodingDownloadFlag'] = 1;
    } else {

        $data['recodingDownloadFlag'] = 0;
    }
} else {

    $data['recodingDownloadFlag'] = 1;
}

if (isset($configData->apiResend) && $configData->apiResend == 1) {

    if ($accesslevel == 1) {

        $data['apiResendButton'] = 1;
    } else {

        if (isset($configData->apiResendAccessLevel) && $configData->apiResendAccessLevel != "") {

            if (strpos($configData->apiResendAccessLevel, $accesslevel) !== FALSE) {

                $data['apiResendButton'] = 1;
            } else {

                $data['apiResendButton'] = 0;
            }
        } else {

            $data['apiResendButton'] = 0;
        }
    }
} else {

    $data['apiResendButton'] = 0;
}

if (isset($configData->recodingDetailFromMongo) && $configData->recodingDetailFromMongo == 1) {

    $data["mongoLogger"] = 1;
    $data["table"] = $this->mongoreport_model->fetchLoggerDetail($perPage, $page, $data['userSetting'], $process, $custName, $campaign, $mydate, $mydateto, $phoneno, $dispose, $dispose2, $dispose3, $agent, $leadset, $dissconnector, $loggerdidNumber, $crmId, $evaluation);
    if ($data["table"] == "FALSE") {

        $data["mongoLogger"] = 0;
        $data["table"] = $this->cdr_model->fetch_logger_log($perPage, $page, "FALSE", $data['userSetting'], $process, $custName, $campaign, $mydate, $mydateto, $phoneno, $dispose, $dispose2, $dispose3, $agent, $leadset, $dissconnector, $loggerdidNumber, $crmId, $evaluation);
    }
} else {

    $data["mongoLogger"] = 0;
    $data["table"] = $this->cdr_model->fetch_logger_log($perPage, $page, "FALSE", $data['userSetting'], $process, $custName, $campaign, $mydate, 
        $mydateto, $phoneno, $dispose, $dispose2, $dispose3, $agent, $leadset, $dissconnector, $loggerdidNumber, $crmId, $evaluation, $direction);
}


if (!empty($data["table"])) {
    $config["total_rows"] = ($this->uri->segment(3)) ? $this->uri->segment(3) + 51 : 51;
} else {
    $config["total_rows"] = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
}

$config["per_page"] = $perPage;
$config["uri_segment"] = 3;
$config['full_tag_open'] = '<ul class="pagination">';
$config['full_tag_close'] = '</ul>';
$config['num_tag_open'] = '<li>';
$config['num_tag_close'] = '</li>';
$config['first_tag_open'] = '<li>';
$config['first_tag_close'] = '</li>';
$config['last_tag_open'] = '<li>';
$config['last_tag_close'] = '</li>';
$config['cur_tag_open'] = '<li class="active"><a href="#">';
$config['cur_tag_close'] = '</a></li>';
$config['next_tag_open'] = '<li class="nextPage">';
$config['next_tag_close'] = '</li>';
$config['prev_tag_open'] = '<li class="previousPage">';
$config['prev_tag_close'] = '</li>';
$config['display_pages'] = FALSE;
$config['first_link'] = FALSE;
$config['last_link'] = FALSE;
$limit = $page;
$this->pagination->initialize($config);

$data["links"] = $this->pagination->create_links();

if ($page == 0) {

    $limit = $perPage;
    $start = 0;
} else {

    $start = $limit;
    $limit = $perPage;
}
$data['limit'] = $limit;
$data['start'] = $start;
$phoneSettingArray = array();

if ($accesslevel == 4) {

    $phoneSetting = $this->process_model->getProcessPhoneSetting();

    if (isset($phoneSetting->result)) {

        foreach ($phoneSetting->result as $key => $value) {

            $phoneSettingArray[$key] = $value;
        }
    }
}
$data['phoneSetting'] = $phoneSettingArray;
$data['page'] = "logger";
$data['title'] = "Log Detail";

$this->load->view('template', $data);
}

function resetloggerForCallingScreen() {

    $this->session->unset_userdata('process');
    $this->session->unset_userdata('custName');
    $this->session->unset_userdata('campaign');
    $this->session->unset_userdata('date');
    $this->session->unset_userdata('dateto');
    $this->session->unset_userdata('talktime');
    $this->session->unset_userdata('phoneno');
    $this->session->unset_userdata('ratingfilter');
    $this->session->unset_userdata('dispose');
    $this->session->unset_userdata('agent');
    $this->session->unset_userdata('leadset');
    $this->session->unset_userdata('dissconnector');
}

function resetloggersession() {

    $data['redirect'] = "site/logger";
    $this->load->view('redirect', $data);
}

function resetleadMgmtsession() {

    $data['redirect'] = "site/leadManagement";
    $this->load->view('redirect', $data);
}

function errorSipRegistrationFailure($cause, $method, $reason_phrase, $status_code) {

    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();
    $data['heading'] = "SIP Registration Failure";
    $data['cause'] = str_replace("%20", " ", $cause);
    $data['method'] = $method;
    $data['reason_phrase'] = $reason_phrase;
    $data['status_code'] = $status_code;
    $data['message'] = "Please Contact Admin";
    $this->userStateChangeSet("errorSipRegistrationFailure");
    $data['page'] = "errorSipRegistrationFailure";
    $data['title'] = "Log Detail";
    $this->load->view('errorTemplate', $data);
}

function errorWebSocketConnectionError($type, $code, $reason, $scheme) {

    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();
    $data['heading'] = " WebSocket Registration Failure";
    $data['cause'] = str_replace("%20", " ", $type);
    $data['method'] = $code;
    $data['reason_phrase'] = $reason;
    $data['status_code'] = $scheme;
    $data['message'] = "Please Contact Admin";
    $this->userStateChangeSet("errorSipRegistrationFailure");
    $data['page'] = "errorSipRegistrationFailure";
    $data['title'] = "Log Detail";
    $this->load->view('errorTemplate', $data);
}

    // agent dispose dash board 
function agentDisposeDashboard() {
    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['multiDisposeAllow'] = $this->standard_model->checkFeatureAllow('1', 'multilevel_dispose_allow');
    $data['menuDetail'] = $this->getMenuData();
    $data['agentDisposeDashboard'] = $this->agentdispose_model->disposeCount();
    $this->userStateChangeSet("agent Dispose Dashboard");

    if ($data['agentDisposeDashboard']->output == "FALSE") {
        $data['alerterror'] = $data['agentDisposeDashboard']->message;
    }

    $data['page'] = "agentDisposeDashboard";
    $data['title'] = "agent Dispose Dashboard";
    $this->load->view('template', $data);
}

// end of agentDisposeDashboard
    // agent dispose dash board 
function liveMonitoringAgent() {
    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();
    $user = $this->session->userdata('id');
    $q2 = $this->db->query("SELECT `value` FROM `config` WHERE `name` LIKE '%livemonitoringrefresh%'")->row();
    $data['refreshtime'] = $q2->value;
    $q3 = $this->db->query("SELECT `extension` FROM `user` WHERE `id` = '$user'")->row();
    $data['extension'] = $q3->extension;
    $this->lua_model->changeExtenstion($user, $q3->extension, "dynamic");
    $data['CampaignProcess'] = $this->campaign_model->getCampaignWithProcess();
        //print_r($data['CampaignProcess']);
    $this->userStateChangeSet("live Monitoring Agent");
    $data['page'] = "liveMonitoringAgent";
    $data['title'] = "live Monitoring Agent";
    $this->load->view('template', $data);
}

// end of agentDisposeDashboard
    // agent dispose dash board 
function liveMonitoringLatest() {

    $userId = $this->session->userdata('id');
    $accesslevel = $this->session->userdata('accesslevel');
    $userName = $this->session->userdata("username");
    $agentKey = $this->session->userdata('agentKey');
    $fullName = $this->session->userdata('name');

    $campaignData = $this->campaign_model->getLiveProcessCampaign();
    if ($campaignData->output == "FALSE") {
        $data['alerterror'] = $campaignData->message;
    }
    $data['campaign'] = $campaignData->campaign;

    $processData = $this->process_model->getLiveProcess();
    if ($processData->output == "FALSE") {
        $data['alerterror'] = $processData->message;
    } else {
        $data['transferAgent'] = $this->standard_model->getTransferAgentData($processData->processCsv);
    }
    $data['process'] = $processData->process;
    $data['accesslevel'] = $this->standard_model->getAllAccessLevel();

    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();

    $livemonitorFilterConfig = $this->config_model->getlivemonitorFilterConfig();
    $data['refreshtime'] = $livemonitorFilterConfig->refreshTime;
    $data['dropRefreshtime'] = $livemonitorFilterConfig->dropCallRefreshTime;

    $extensionOutput = $this->extension_model->getAdvanceExtension($userId, $userName, $fullName, $accesslevel, $agentKey);
        // $liveMoniterSetting = $this->standard_model->getAdvMoniterAgentExtension( $userId );
    $data['extension'] = $extensionOutput;
        // $this->lua_model->changeExtenstion( $userId, $liveMoniterSetting->extension,"dynamic");
    $data['CampaignProcess'] = $this->campaign_model->getCampaignWithProcess();
        //print_r($data['CampaignProcess']);
    $this->userStateChangeSet("live Monitoring Agent");
    $data['page'] = "liveMonitorNew";
    $data['title'] = "live Monitoring Agent";
    $this->load->view('template', $data);
}

// end of agentDisposeDashboard

function excelDemo() {
    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();
    $data['page'] = "excelList";
    $data['title'] = "excel List";
    $this->load->view('template', $data);
}

function excelUpload() {
    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();
    $data['editmode'] = "0";
    $data['listId'] = $this->input->get_post('leadset');
    $data['page'] = 'uploadlead';
    $data['title'] = 'Upload Lead';
    $this->load->view('template', $data);
}

function listViewRedirect() {
    $data['menuDetail'] = $this->getMenuData();
    $userId = $this->session->userdata('id');
    $listViewData = $this->process_model->listViewRedirect($userId);
    if ($listViewData->output == "TRUE") {
        $data['redirect'] = "site/viewListProcess";
        $data['other'] = "processId=" . $listViewData->processId;
        $this->load->view('redirect', $data);
    } else {
        $data['alertwarning'] = 'Sorry No List Process Found';
        $data['redirect'] = "site/viewListProcess";
        $this->load->view('redirect', $data);
    }
}

function viewListProcess() {
    $processId = $this->input->get_post('processId');
    $data['processId'] = $processId;
    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();
    $this->userStateChangeSet("viewListProcess");
    $data['editmode'] = "0";
    if ($processId == "" || $processId == 0) {
        $data['result'] = array();
    } else {
        $data['result'] = $this->process_model->getListProcessLeadset($processId);
    }
    $data['page'] = 'viewListLeadset';
    $data['title'] = 'View Leadset';
    $this->load->view('template', $data);
}

function listCallingScreen() {
    $data['customerId'] = $this->input->get_post('customerId');
    $data['processId'] = $this->input->get_post('processId');
    $data['leadsetId'] = $this->input->get_post('leadsetId');
    $data['customerNumber'] = $this->input->get_post('customerNumber');
    $data['pageNumber'] = $this->input->get_post('pageNumber');
    $data['listId'] = $this->input->get_post('listId');
    $data['campaignId'] = $this->input->get_post('campaignId');
    $data['campaignName'] = $this->input->get_post('campaignName');
    $data['processName'] = $this->input->get_post('processName');

    $firstLead = $this->input->get_post('firstLead');
    $page = $this->input->get_post('page');
    $leadsetId = $this->input->get_post('leadsetId');
    $processId = $this->input->get_post('processId');

    $data['crmIdName'] = "CRM Id";
    $data['crmIdAllow'] = 0;
    $configArray = array(
        120 => (object) array('variableName' => 'crmIdAllow', 'defaultValue' => '0'),
        121 => (object) array('variableName' => 'crmIdName', 'defaultValue' => 'CRM Id'));
    $configData = $this->config_model->getMultipleConfig($configArray);
    if (isset($configData->crmIdAllow)) {

        $data['crmIdAllow'] = $configData->crmIdAllow;
    }

    if (isset($configData->crmIdName)) {

        $data['crmIdName'] = $configData->crmIdName;
    }

    $data['base_url'] = site_url("site/viewListView/$leadsetId/$processId/$firstLead/$page");
    $data['userButtonSetting'] = $this->data['userButtonSetting'];
    $data['menuDetail'] = $this->getMenuData();
    $data['pausecode'] = $this->process_model->getpausecode();
        $leadId = // $data['luaOpt']     = $this->userStateChangeSet("callingscreen");
        $data['editmode'] = "0";
        $data['page'] = 'listCallingScreen';
        $data['title'] = 'list Calling Screen';
        $agentId = $this->session->userdata('id');
        $data['callback'] = $this->callback_model->agentCallBackData($agentId);
        $data['callSetting'] = $this->config_model->checkCallSetting();
        $data['leadset'] = $this->lead_model->getleadset();
        $data['Sms'] = $this->message_model->viewsms();
        $accesslevel = $this->session->userdata("accesslevel");
        $userName = $this->session->userdata("username");
        $data['luaOpt'] = $this->userStateChangeSet("callingscreen");
        $data['processPhoneSetting'] = $this->process_model->singleProccessPhoneSetting($processId);
        if ($accesslevel == 4) {
            $data['buttonSetting'] = $this->user_model->getAgentSettings($agentId, $userName);
        } else {
            $data['buttonSetting'] = $this->user_model->getSuperAgentDefaultSettings($agentId, $userName);
        }
        $data['SystemApiJson'] = "";
        $data['SystemIfameJson'] = "";
        $data['processApiJsonArray'] = array();
        $data['processIframeJsonArray'] = array();
        $data['processSettingJson'] = array();
        $processData = $this->reporting_model->getprocess();
        $processCsv = "";
        foreach ($processData as $key => $value) {
            $processCsv .= ( $processCsv == "" ) ? $key : "," . $key;
        }
        $data['processCsv'] = $processCsv;

        $MenuApiData = $this->menu_model->getApiAndIFrameData();

        $data['SystemApiJsonFlag'] = "FALSE";
        $data['SystemIfameJsonFlag'] = "FALSE";
        $data['processApiJsonArrayFlag'] = "FALSE";
        $data['processIframeJsonArrayFlag'] = "FALSE";

        foreach ($MenuApiData as $key => $menuData) {
            if (isset($menuData->menu_location) && $menuData->menu_location == "site/systemApiIntegrationView") {
                $data['SystemApiJson'] = $this->api_model->getTptSystemSetUpApi();
                $data['SystemApiJsonFlag'] = "TRUE";
            } else if (isset($menuData->menu_location) && $menuData->menu_location == "form/viewSystemMenuTab") {
                $data['SystemIfameJson'] = $this->api_model->getTptSystemSetUpIframe();
                $data['SystemIfameJsonFlag'] = "TRUE";
            } else if (isset($menuData->menu_location) && $menuData->menu_location == "form/viewApiProcess") {
                if ($processCsv != "") {
                    $apiData = $this->api_model->getTptCallSetUpApi($processCsv);
                    if (isset($apiData->output) && $apiData->output == "TRUE" && isset($apiData->message) && isset($apiData->body) && $apiData->message != "NO_API_SET") {
                        $data['processApiJsonArray'] = $apiData->body;
                        $data['processApiJsonArrayFlag'] = "TRUE";
                    }
                }
            } else if (isset($menuData->menu_location) && $menuData->menu_location == "form/viewSystemApiProcess") {
                if ($processCsv != "") {
                    $apiData = $this->api_model->getTptCallSetUpIframe($processCsv);
                    if (isset($apiData->output) && $apiData->output == "TRUE" && isset($apiData->message) && isset($apiData->body) && $apiData->message != "NO_API_SET") {
                        $data['processIframeJsonArray'] = $apiData->body;
                        $data['processIframeJsonArrayFlag'] = "TRUE";
                    }
                }
            }
        }

        if ($processCsv != "") {
            $apiData = $this->api_model->getProcessSettingJson($processCsv);
            if (isset($apiData->output) && $apiData->output == "TRUE" && isset($apiData->message) && isset($apiData->body) && $apiData->message != "NO_API_SET") {
                $data['processSettingJson'] = $apiData->body;
            }
        }

        $data['processData'] = $processData;
        // $data['pausecode']   = $this->process_model->getpausecode();
        $this->load->view('template', $data);
    }

    function viewListView($leadsetId, $processId, $leadNumber = 0, $pageNumber = 0) {
        // $this->load->library("pagination");
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("viewleadset");

        $listProcessKey = 'listProcessPagination:' . $processId . ":" . $leadsetId;
        // key => listProcessPagination:processId:leadsetId
        // key used in session

        if ($pageNumber < 0) {
            $pageNumber = 0;
            $leadNumber = 0;
        }
        if ($this->session->userdata($listProcessKey) !== FALSE) {
            $listProcessArray = $this->session->userdata($listProcessKey);

            $newArray = array();
            foreach ($listProcessArray as $key => $value) {
                if ($key < intval($pageNumber))
                    $newArray[$key] = $value;
                else {
                    break;
                }
            }

            $listProcessArray = $newArray;
            $listProcessArray[$pageNumber] = $leadNumber;
        } else {
            $listProcessArray = array($pageNumber => $leadNumber);
        }
        $userdata = array($listProcessKey => $listProcessArray);
        $this->session->set_userdata($userdata);

        $config = array();
        $data['processId'] = $processId;
        $data['listId'] = $leadsetId;
        $processData = $this->process_model->getProcessData($processId);
        $data['campaignId'] = $processData->campaignId;
        $data['campaignName'] = $processData->campaignName;
        $data['processName'] = $processData->processName;
        $data['base_url'] = site_url("site/viewListView/$leadsetId/$processId");

        $data["listJson"] = $this->lead_model->getListJson($leadsetId);
        $data["table"] = $this->lead_model->leadInList($processId, $leadsetId, $leadNumber, 50);
        $this->userStateChangeSet("View leadset");
        $data['processPhoneSetting'] = $this->process_model->singleProccessPhoneSetting($processId);
        $data['start'] = $leadNumber;
        $data['pageNumber'] = $pageNumber;
        $data['page'] = 'viewlistById';
        $data['title'] = 'View leadset';
        $this->load->view('template', $data);
    }

    function viewList() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("viewleadset");
        $data['editmode'] = "0";
        $data['table'] = $this->lead_model->viewList();
        $data['page'] = 'viewList';
        $data['title'] = 'View Leadset';
        $this->load->view('template', $data);
    }

    function userStateChangeSet($pageName) {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $userId = $this->session->userdata('id');
        $userName = $this->session->userdata('username');
        $fullName = $this->session->userdata('name');
        $agentphonenumber = $this->session->userdata('agentphonenumber');
        $accesslevel = $this->session->userdata('accesslevel');
        $pageName = $pageName;
        $ipAddress = $this->input->ip_address();
        $userState = "stop";
        $campaignId = "";
        $campaignName = "";
        $processId = "";
        $processName = "";
        $customerId = "";
        $customerPhoneNumber = "";
        $customerName = "";
        $referenceUuid = "";
        $customerUuid = "";
        $LeadsetId = "";
        $LeadsetName = "";
        $UserLastState = "";
        $modeOfCalling = "";
        $userStateArg = "";
        $this->lua_model->removehashlua($userId);
        $luaOpt = $this->lua_model->userStateChange($userId, $userName, $fullName, $pageName, $ipAddress, $userState, $campaignId, $campaignName, $processId, $processName, $customerId, $customerPhoneNumber, $customerName, $referenceUuid, $customerUuid, $LeadsetId, $LeadsetName, $UserLastState, $modeOfCalling, $userStateArg, $accesslevel);
        return $luaOpt;
    }

    //  // function for button setting 
    function viewCampaignSetting() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $data['table'] = $this->campaign_model->viewcampaign();
        $data['page'] = 'viewCampaignSetting';
        $this->load->view('template', $data);
    }

    // // function for button setting 
    function viewProcessSetting() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $campaignId = $this->input->get('campaign');
        $data['table'] = $this->campaign_model->viewcampaignprocess($campaignId);
        $data['page'] = 'viewProcessSetting';
        $this->load->view('template', $data);
    }

    // // function for button setting 
    function viewUserSetting() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $data['table'] = $this->user_model->viewusers();
        $data['page'] = 'viewUserSetting';
        $data['title'] = 'View Users';
        $this->load->view('template', $data);
    }

    function viewUserProcessSetting() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $processId = $this->input->get('processId');
        $data['table'] = $this->user_model->viewProcessUsers($processId);
        $data['page'] = 'viewUserSetting';
        $data['title'] = 'View Users';
        $this->load->view('template', $data);
    }

    // this function for super user only
    // super user can add selected leadset lead in redis 
    function addLeadsetInRedis() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        if ($this->session->userdata('accesslevel') == 1 && $this->session->userdata('accesslevel') != "") {
            $leadSetArray = $this->lead_model->getleadset();
            $this->lead_model->addLeadsetInRedis($leadSetArray);
            // foreach ( $leadSetArray as $key => $value) {
            // }
            print_r($leadSetArray);
        }
    }

    function addSingleLeadsetInRedis($leadsetId) {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        if ($this->session->userdata('accesslevel') == 1 && $this->session->userdata('accesslevel') != "") {
            $leadSetArray = $this->lead_model->getleadset();
            $this->lead_model->addSingleLeadsetInRedis($leadsetId, $leadSetArray);

            // print_r($leadSetArray);
        }
    }

    public function viewautoamdcdr() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $agent = "";
        $process = "";
        $campaign = "";
        $process = $this->input->post('process');
        $campaign = $this->input->post('campaign');
        $mydate = $this->input->post('date');
        $mydateto = $this->input->post('dateto');
        $phoneno = $this->input->post('phoneno');

        if ($mydate != "") {

            $a = explode('-', $mydate);
            $result = $a[2] . '-' . $a[0] . '-' . $a[1];
            $mydate = $result;
        }

        if ($mydateto != "") {

            $a = explode('-', $mydateto);
            $result = $a[2] . '-' . $a[0] . '-' . $a[1];
            $mydateto = $result;
        }
        $data['campaign'] = $this->reporting_model->getcampaign();
        $campaignProcessData = $this->process_model->getLiveProcess();
        $data['process'] = $campaignProcessData->process;

        $data['selectedcampaign'] = $campaign;
        $data['selectedprocess'] = $process;
        $data['selectedphoneno'] = $phoneno;
        if ($mydate != "")
            $data['selecteddate'] = date("m-d-Y", strtotime($mydate));
        else
            $data['selecteddate'] = "";
        if ($mydateto != "")
            $data['selecteddateto'] = date("m-d-Y", strtotime($mydateto));
        else
            $data['selecteddateto'] = "";

        $this->load->library("pagination");
        $config = array();
        $config['page_query_string'] = FALSE;
        $config['base_url'] = site_url("site/viewautoamdcdr");

        $config["total_rows"] = $this->cdr_model->autoAmdCdr_count($process, $campaign, $mydate, $mydateto, $phoneno);
        $config["per_page"] = 50;
        $config["uri_segment"] = 3;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_tag_open'] = '<li class="nextPage">';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li class="previousPage">';
        $config['prev_tag_close'] = '</li>';
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $data["table"] = $this->cdr_model->autoAmdCdr($config["per_page"], $page, $process, $campaign, $mydate, $mydateto, $phoneno);
        $data["query"] = $this->cdr_model->autoAmdCdrQuery($process, $campaign, $mydate, $mydateto, $phoneno);
        $data["links"] = $this->pagination->create_links();
        $limit = $page;
        if ($page == 0) {
            $limit = 50;
            $start = 0;
        } else {
            $start = $limit;
            $limit = 50;
        }
        $data['limit'] = $limit;
        $data['start'] = $start;
        $data['page'] = 'viewAutoAmdCdr';
        $data['title'] = 'autoAmdCdr';
        $this->load->view('template', $data);
    }

    function resetAmdCdrReport() {

        $this->session->unset_userdata('amd_process');
        $this->session->unset_userdata('amd_campaign');
        $this->session->unset_userdata('amd_date');
        $this->session->unset_userdata('amd_dateto');
        $this->session->unset_userdata('amd_phoneno');
        // $data['message']="1";
        // $this->load->view('json',$data);
        $data['redirect'] = "site/viewautoamdcdr";
        $this->load->view('redirect', $data);
    }

    public function smsCdrReporting() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $perPage = 50;
        $data['editmode'] = "0";

        $configArray = array(
            120 => (object) array('variableName' => 'crmUniqueId', 'defaultValue' => '0'),
            121 => (object) array('variableName' => 'crmUniqueIdName', 'defaultValue' => 'CRM Id'),
        );

        $configData = $this->config_model->getMultipleConfig($configArray);
        $data['configData'] = $configData;

        $agent = $this->input->post('agent');
        $process = $this->input->post('process');
        $campaign = $this->input->post('campaign');
        $mydate = $this->input->post('date');
        $mydateto = $this->input->post('dateto');
        $phoneno = $this->input->post('phoneno');

        if ($configData->crmUniqueId == 1) {

            $crmId = $this->input->post('crmId');
        } else {

            $crmId = "";
        }

        if ($mydate != "") {

            $a = explode('-', $mydate);
            $result = $a[2] . '-' . $a[0] . '-' . $a[1];
            $mydate = $result;
        }
        if ($mydateto != "") {

            $a = explode('-', $mydateto);
            $result = $a[2] . '-' . $a[0] . '-' . $a[1];
            $mydateto = $result;
        }
        $data['agent'] = $this->reporting_model->getagent();
        $data['campaign'] = $this->reporting_model->getcampaign();
        $campaignProcessData = $this->process_model->getLiveProcess();
        $data['dispose'] = $this->reporting_model->getdispose();
        $data['process'] = $campaignProcessData->process;
        $data['selectedAgent'] = $agent;
        $data['selectedProcess'] = $process;
        $data['selectedCampaign'] = $campaign;
        $data['selectedPhoneno'] = $phoneno;
        $data['selectedCrmId'] = $crmId;

        if ($mydate != "")
            $data['selecteddate'] = date("m-d-Y", strtotime($mydate));
        else
            $data['selecteddate'] = "";

        if ($mydateto != "") {
            $data['selecteddateto'] = date("m-d-Y", strtotime($mydateto));
        } else {
            $data['selecteddateto'] = "";
        }

        $this->load->library("pagination");
        $config = array();
        $config['page_query_string'] = FALSE;
        $config['base_url'] = site_url("site/smsCdrReporting");

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

        $data["table"] = $this->message_model->fetch_smsCdrReport($perPage, $page, $agent, $process, $campaign, $mydate, $mydateto, $phoneno, $crmId);

        if (!empty($data["table"])) {
            $config["total_rows"] = ($this->uri->segment(3)) ? $this->uri->segment(3) + 51 : 51;
        } else {
            $config["total_rows"] = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        }

        $config["per_page"] = $perPage;
        $config["uri_segment"] = 3;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_tag_open'] = '<li class="nextPage">';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li class="previousPage">';
        $config['prev_tag_close'] = '</li>';
        $config['display_pages'] = FALSE;
        $config['first_link'] = FALSE;
        $config['last_link'] = FALSE;

        $this->pagination->initialize($config);
        $data["links"] = $this->pagination->create_links();
        $limit = $page;
        if ($page == 0) {

            $limit = $perPage;
            $start = 0;
        } else {

            $start = $limit;
            $limit = $perPage;
        }

        $data['limit'] = $limit;
        $data['start'] = $start;
        // $this->userStateChangeSet("View SMS CDR Reporting");
        $data['page'] = 'smsCdrReporting';
        $data['title'] = 'View SMS CDR Reporting';
        $this->load->view('template', $data);
    }

    function resetSmsCdrReport() {

        $data['redirect'] = "site/smsCdrReporting";
        $this->load->view('redirect', $data);
    }

    public function emailCdrReporting() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $perPage = 50;
        $data['editmode'] = "0";

        $configArray = array(
            120 => (object) array('variableName' => 'crmUniqueId', 'defaultValue' => '0'),
            121 => (object) array('variableName' => 'crmUniqueIdName', 'defaultValue' => 'CRM Id'),
        );

        $configData = $this->config_model->getMultipleConfig($configArray);
        $data['configData'] = $configData;

        $agent = $this->input->get_post('agent');
        $campaign = $this->input->get_post('campaign');
        $process = $this->input->get_post('process');
        $mydate = $this->input->get_post('date');
        $mydateto = $this->input->get_post('dateto');

        if ($configData->crmUniqueId == 1) {

            $crmId = $this->input->post('crmId');
        } else {

            $crmId = "";
        }

        if ($mydate != "") {

            $a = explode('-', $mydate);
            $result = $a[2] . '-' . $a[0] . '-' . $a[1];
            $mydate = $result;
        }

        if ($mydateto != "") {

            $a = explode('-', $mydateto);
            $result = $a[2] . '-' . $a[0] . '-' . $a[1];
            $mydateto = $result;
        }

        $data['agent'] = $this->reporting_model->getagent();
        $data['campaign'] = $this->reporting_model->getcampaign();
        $campaignProcessData = $this->process_model->getLiveProcess();
        $data['process'] = $campaignProcessData->process;
        $data['selectedagent'] = $agent;
        $data['selectedProcess'] = $process;
        $data['selectedCampaign'] = $campaign;
        $data['selectedCrmId'] = $crmId;

        if ($mydate != "")
            $data['selecteddate'] = date("m-d-Y", strtotime($mydate));
        else
            $data['selecteddate'] = "";
        if ($mydateto != "")
            $data['selecteddateto'] = date("m-d-Y", strtotime($mydateto));
        else
            $data['selecteddateto'] = "";



        $this->load->library("pagination");
        $config = array();
        $config['page_query_string'] = FALSE;
        $config['base_url'] = site_url("site/emailCdrReporting");
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

        $data["table"] = $this->reporting_model->fetch_emailCdrReport($perPage, $page, $agent, $campaign, $process, $mydate, $mydateto, $crmId);
        if (!empty($data["table"])) {
            $config["total_rows"] = ($this->uri->segment(3)) ? $this->uri->segment(3) + 51 : 51;
        } else {
            $config["total_rows"] = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        }

        $config["per_page"] = $perPage;
        $config["uri_segment"] = 3;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_tag_open'] = '<li class="nextPage">';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li class="previousPage">';
        $config['prev_tag_close'] = '</li>';
        $config['display_pages'] = FALSE;
        $config['first_link'] = FALSE;
        $config['last_link'] = FALSE;

        $this->pagination->initialize($config);
        $data["links"] = $this->pagination->create_links();
        $limit = $page;
        if ($page == 0) {

            $limit = $perPage;
            $start = 0;
        } else {

            $start = $limit;
            $limit = $perPage;
        }
        $data['limit'] = $limit;
        $data['start'] = $start;

        // $this->userStateChangeSet("View SMS CDR Reporting");
        $data['page'] = 'emailCdrReporting';
        $data['title'] = 'View SMS CDR Reporting';
        $this->load->view('template', $data);
    }

    function paindingCallbackReport() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['userSetting'] = $this->config_model->getUserSetting();
        $this->userStateChangeSet("paindingCallbackReport");
        $data['editmode'] = "0";
        $data['agents'] = $this->reporting_model->getagent();
        $data['table'] = $this->reporting_model->paindingCallbackReport();
        $data['page'] = 'paindingCallbackReport';
        $data['title'] = 'View Painding cdr report';
        $this->load->view('template', $data);
    }

    function resetEmailCdrReport() {

        $this->session->unset_userdata('email_cdr_agent');
        $this->session->unset_userdata('email_cdr_campaign');
        $this->session->unset_userdata('email_cdr_process');
        $this->session->unset_userdata('email_cdr_date');
        $this->session->unset_userdata('email_cdr_dateto');
        // $data['message']="1";
        // $this->load->view('json',$data);
        $data['redirect'] = "site/emailCdrReporting";
        $this->load->view('redirect', $data);
    }

// mail template start 
    function viewMailTemplate() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("View extension");

        $this->load->library("pagination");
        $config = array();
        $config["base_url"] = site_url("site/viewMailTemplate");
        $config["total_rows"] = $this->message_model->emailMailTemplateCount();
        $config["per_page"] = 20;
        $config["uri_segment"] = 3;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $this->pagination->initialize($config);

        $data['menuDetail'] = $this->getMenuData();
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $data["table"] = $this->message_model->
        getTemplate($config["per_page"], $page);
        $data['pageCount'] = $page;
        $data["links"] = $this->pagination->create_links();
        $data['page'] = 'viewMailTemplate';
        $data['title'] = 'View extension';
        $this->load->view('template', $data);
    }

    function changesEmailTemplateStatus() {
        $data['menuDetail'] = $this->getMenuData();
        $data['menuDetail'] = $this->getMenuData();
        $templateId = $this->input->get_post('templateId');
        $status = $this->input->get_post('status');
        $emailStatus = $this->message_model->changesEmailTemplateStatus($templateId, $status);
        if ($emailStatus->output == "TRUE") {
            $data['alertsuccess'] = 'Email Template Status successfully Updated';
        } else {
            $data['alertwarning'] = 'Email Template Status not successfully Updated';
        }
        $data['redirect'] = "site/viewMailTemplate";
        $this->load->view('redirect', $data);
    }

    function viewMailBody() {
        $this->load->library("pagination");
        $config = array();
        $config["base_url"] = site_url("site/viewMailBody");
        $config["total_rows"] = $this->message_model->emailBodyRowCount();
        $config["per_page"] = 20;
        $config["uri_segment"] = 3;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $this->pagination->initialize($config);

        $data['menuDetail'] = $this->getMenuData();
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $data["table"] = $this->message_model->
        fetch_email_body($config["per_page"], $page);
        $data['pageCount'] = $page;
        $data["links"] = $this->pagination->create_links();
        $data['page'] = 'viewMailBody';
        $data['title'] = 'viewMailBody';
        $this->load->view('template', $data);
    }

    function createEmailBody() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("createcampaign");
        $data['page'] = 'createEmailBody';
        $data['title'] = 'Email Body';
        $this->load->view('template', $data);
    }

    function createEmailBodySubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $this->form_validation->set_rules('emailBodytag', 'Tag Name', 'trim|required');
        $this->form_validation->set_rules('emailBodyMessage', 'Body', 'trim|required');
//    $this->form_validation->set_rules('dispose','dispose','trim');
        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $data['page'] = 'createEmailBody';
            $data['title'] = 'Email Body';
            $this->load->view('template', $data);
        } else {
            $emailBodytag = $this->input->post('emailBodytag');
            $emailBodyMessage = $this->input->post('emailBodyMessage');


            if ($emailBodyMessage == "<br>") {
                $data['alerterror'] = "The Email Body field is required.";
                $data['page'] = 'createEmailBody';
                $data['title'] = 'Email Body';
                $this->load->view('template', $data);
            } else {
                $uniqueBodyTag = new stdClass();
                $uniqueBodyTag = $this->common_model->createFieldDuplicateCheck('email_body_template', 'body_tag', $emailBodytag, 'status < 2');
                if ($uniqueBodyTag->output == "TRUE") {
                    $messageBody = $this->message_model->createEmailBody($emailBodytag, $emailBodyMessage);
                    if ($messageBody == 1) {
                        $data['alertsuccess'] = "New Email Body Created Successfully";
                    } else {
                        $data['alertwarning'] = "New Email Body Creation Error";
                    }
                    $data['redirect'] = "site/viewMailBody";
                    $this->load->view("redirect", $data);
                } else {
                    $data['alerterror'] = "The Tag Name field must contain a unique value.";
                    $data['page'] = 'createEmailBody';
                    $data['title'] = 'Email Body';
                    $this->load->view('template', $data);
                }
            }
        }
    }

    function editEmailBody($id) {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("editEmailBody");
        $data['before'] = $this->message_model->beforeeditEmailBody($id);
        $data['page'] = 'editEmailBody';
        $data['title'] = 'Email Body';
        $this->load->view('template', $data);
    }

    function editEmailBodySubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $this->form_validation->set_rules('emailBodytag', 'Tag Name', 'trim|required');
        $this->form_validation->set_rules('emailBodyMessage', 'Email Body', 'trim|required');
//    $this->form_validation->set_rules('dispose','dispose','trim');
        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $emailBodyid = $this->input->post('emailBodyid');
            $data['before'] = $this->message_model->beforeeditEmailBody($emailBodyid);
            $data['page'] = 'editEmailBody';
            $data['title'] = 'Email Body';
            $this->load->view('template', $data);
        } else {
            $emailBodytag = $this->input->post('emailBodytag');
            $emailBodyMessage = $this->input->post('emailBodyMessage');
            $emailBodyid = $this->input->post('emailBodyid');
            $duplicateCheckOutput = $this->common_model->editFieldDuplicateCheck($emailBodyid, "id", $emailBodytag, "body_tag", "email_body_template");
            if ($duplicateCheckOutput->output == "TRUE") {
                //$dispose=$this->input->post('dispose');
                if ($emailBodyMessage == "<br>") {
                    $data['alerterror'] = "The Email Body field is required.";
                    $emailBodyid = $this->input->post('emailBodyid');
                    $data['before'] = $this->message_model->beforeeditEmailBody($emailBodyid);
                    $data['page'] = 'editEmailBody';
                    $data['title'] = 'Email Body';
                    $this->load->view('template', $data);
                } else {
                    $messageBody = $this->message_model->editEmailBody($emailBodyid, $emailBodytag, $emailBodyMessage);
                    if ($messageBody == 1) {
                        $data['alertsuccess'] = "Body Edited Successfully";
                    } else {
                        $data['alerterror'] = "Body Edit Error";
                    }
                    $data['redirect'] = "site/viewMailBody";
                    $this->load->view("redirect", $data);
                }
            } else {
                $data['alerterror'] = "Please enter Unique Body Tag";
                $data['redirect'] = "site/editEmailBody/" . $emailBodyid;
                $this->load->view("redirect", $data);
            }
        }
    }

    function deleteEmailBody() {

        $deleteOpt = $this->message_model->deleteBody($this->input->get('id'));
        if ($deleteOpt->output == "TRUE") {
            $data['alertsuccess'] = "Email Body Template Deleted Successfully";
        } else {
            $data['alerterror'] = "We Can't Delete this Body <br/>
            because That are mapped in following Template<br/>"
            . $deleteOpt->result
            . "<br/> Please remove and then delete";
        }
        $data['redirect'] = 'site/viewMailBody';
        $data['title'] = 'View Mail Body';
        $this->load->view('redirect', $data);
    }

    function viewemailsubjecttemplate() {
        $data['menuDetail'] = $this->getMenuData();
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("viewEmailSubject");
        $data['userSetting'] = $this->config_model->getUserSetting();
        $data['table'] = $this->message_model->email_subject_template();
        $data['page'] = 'viewemailsubjecttemplate';
        $data['title'] = 'View Users';
        $this->load->view('template', $data);
    }

    function createemailsubject() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['page'] = 'createemailsubjecttemplate';
        $this->load->view('template', $data);
    }

    function createemailsubjectsubmit() {
        $data['menuDetail'] = $this->getMenuData();
        $this->form_validation->set_rules('subjectTag', 'Subject Tag', 'required|is_unique[email_subject_template.subject_tag]');
        $this->form_validation->set_rules('subjectText', 'Subject Text', 'required');
        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $data['page'] = 'createemailsubjecttemplate';
            $data['title'] = 'Create Email Subject Template';
            $this->load->view('template', $data);
        } else {
            $value = $this->input->post('subjectTag');
            $type = $this->input->post('subjectText');
            if ($this->message_model->createemailsubjectonsubmit($value, $type) == 0)    //,$user
            $data['alerterror'] = "New Email Subject Template could not be created.";
            else
                $data['alertsuccess'] = "Email Subject Template created Successfully.";
            $data['redirect'] = "site/viewemailsubjecttemplate";
            $this->load->view("redirect", $data);
        }
    }

    //editing usergroup
    public function editemailsubjecttemplate() {
        $data['menuDetail'] = $this->getMenuData();
        $data['before'] = $this->message_model->beforeeditsubjecttemplate($this->input->get('id'));
        $data['page'] = 'editemailsubjecttemplate';
        $data['title'] = 'Edit Email Subject Template';
        $this->load->view('template', $data);
    }

    function editemailsubjecttemplatesubmit() {
        // $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->form_validation->set_rules('type', 'Subject Text', 'required');
        $value = $this->input->post('value');
        $id = $this->input->post('id');
        $duplicateCheckOutput = $this->common_model->editFieldDuplicateCheck($id, "id", $value, "subject_tag", "email_subject_template");
        if ($duplicateCheckOutput->output == "TRUE") {
            $this->form_validation->set_rules('value', 'Subject Tag', 'required');
            if ($this->form_validation->run() == FALSE) {
                $data['alerterror'] = validation_errors();
                $data['before'] = $this->message_model->beforeeditsubjecttemplate($id);
                $data['page'] = 'editemailsubjecttemplate';
                $data['title'] = 'Edit Email Subject Template';
                $this->load->view('template', $data);
            } else {
                $type = $this->input->post('type');
                if ($this->message_model->editsubjecttemplateonsubmit($value, $type, $id) == 0)    //,$user
                $data['alerterror'] = "New Template could not be created.";
                else
                    $data['alertsuccess'] = "Template Edited Successfully.";
                $data['redirect'] = "site/viewemailsubjecttemplate";
                $this->load->view("redirect", $data);
            }
        }
        else {
            $data['alerterror'] = "Please enter Unique Tag";
            $data['redirect'] = "site/editemailsubjecttemplate";
            $data['other'] = "id=" . $id;
            $this->load->view("redirect", $data);
        }
    }

    //deleting email suject
    function deleteemailsubjecttemplate() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("view dispose");
        $deleteOpt = $this->message_model->deletesubjecttemplate($this->input->get('id'));
        if ($deleteOpt->output == "TRUE") {
            $data['alertsuccess'] = "Template Deleted Successfully";
        } else {
            $data['alerterror'] = "We Can't Delete this Subject <br/>
            because That are mapped in following Template<br/>"
            . $deleteOpt->result
            . "<br/> Please remove and then delete";
        }
        $data['redirect'] = 'site/viewemailsubjecttemplate';
        $data['title'] = 'View Subject Template';
        $this->load->view('redirect', $data);
    }

    //email mail footer template start
    function viewemailmailfooter() {
        $this->load->library("pagination");
        $config = array();
        $config["base_url"] = site_url("site/viewEmailMailFooter");
        $config["total_rows"] = $this->message_model->emailMailFooterRowCount();
        $config["per_page"] = 20;
        $config["uri_segment"] = 3;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $this->pagination->initialize($config);

        $data['menuDetail'] = $this->getMenuData();
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $data["table"] = $this->message_model->fetch_email_mail_footer($config["per_page"], $page);
        $data['pageCount'] = $page;
        $data["links"] = $this->pagination->create_links();
        $data['page'] = 'viewEmailMailFooter';
        $data['title'] = 'view Email Mail Footer';
        $this->load->view('template', $data);
    }

    function createEmailMailFooter() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("createEmailMailFooter");
        $data['page'] = 'createEmailMailFooter';
        $data['title'] = 'Email Footer';
        $this->load->view('template', $data);
    }

    function createEmailMailFooterSubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $this->form_validation->set_rules('emailFooterTag', 'Tag Name', 'trim|required|is_unique[email_mail_footer_template.tag]');
        $this->form_validation->set_rules('emailFooterMessage', 'Text', 'trim|required');
//    $this->form_validation->set_rules('dispose','dispose','trim');
        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $data['page'] = 'createEmailMailFooter';
            $data['title'] = 'Email Footer';
            $this->load->view('template', $data);
        } else {
            $emailFooterTag = $this->input->post('emailFooterTag');
            $emailFooterMessage = $this->input->post('emailFooterMessage');

            if ($emailFooterMessage == "<br>") {
                $data['alerterror'] = "The Text field is required.";
                $data['page'] = 'createEmailMailFooter';
                $data['title'] = 'Email Footer';
                $this->load->view('template', $data);
            } else {
                $messageFooter = $this->message_model->createMailFooter($emailFooterTag, $emailFooterMessage);
                if ($messageFooter == 1) {
                    $data['alertsuccess'] = "New Footer Created Successfully";
                } else {
                    $data['alerterror'] = "New Footer Creation Error";
                }
                $data['redirect'] = "site/viewEmailMailFooter";
                $this->load->view("redirect", $data);
            }
        }
    }

    function editEmailFooter($id) {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("editEmailBody");
        $data['before'] = $this->message_model->beforeeditEmailFooter($id);
        $data['page'] = 'editEmailMailFooter';
        $data['title'] = 'Email Footer';
        $this->load->view('template', $data);
    }

    function editEmailFooterSubmit() {
        $data['menuDetail'] = $this->getMenuData();
        $this->form_validation->set_rules('emailFootertag', 'Tag Name', 'trim|required');
        $this->form_validation->set_rules('emailFooterMessage', 'Footer Message', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $emailFooterid = $this->input->post('emailFooterid');
            $data['before'] = $this->message_model->beforeeditEmailFooter($emailFooterid);
            $data['page'] = 'editEmailMailFooter';
            $data['title'] = 'Email footer';
            $this->load->view('template', $data);
        } else {
            $emailFootertag = $this->input->post('emailFootertag');
            $emailFooterMessage = $this->input->post('emailFooterMessage');
            $emailFooterid = $this->input->post('emailFooterid');

            $duplicateCheckOutput = $this->common_model->editFieldDuplicateCheck($emailFooterid, "id", $emailFootertag, "tag", "email_mail_footer_template");
            if ($duplicateCheckOutput->output == "TRUE") {
                if ($emailFooterMessage == "<br>") {
                    $data['alerterror'] = "The Text field is required.";
                    $emailFooterid = $this->input->post('emailFooterid');
                    $data['before'] = $this->message_model->beforeeditEmailFooter($emailFooterid);
                    $data['page'] = 'editEmailMailFooter';
                    $data['title'] = 'Email footer';
                    $this->load->view('template', $data);
                } else {
                    $messageFooter = $this->message_model->editEmailMailFooter($emailFooterid, $emailFootertag, $emailFooterMessage);
                    if ($messageFooter == 1) {
                        $data['alertsuccess'] = "Footer Edited Successfully";
                    } else {
                        $data['alerterror'] = "Footer Edit Error";
                    }
                    $data['redirect'] = "site/viewEmailMailFooter";
                    $this->load->view("redirect", $data);
                }
            } else {
                $data['alerterror'] = "Please enter Unique Tag";
                $data['redirect'] = "site/editEmailFooter/$emailFooterid";
                $this->load->view("redirect", $data);
            }
        }
    }

    function deleteEmailFooter() {
        // $data['userButtonSetting'] = $this->data['userButtonSetting'];
        // $this->userStateChangeSet("view dispose");
        $data['menuDetail'] = $this->getMenuData();
        $deleteOpt = $this->message_model->deleteFooter($this->input->get('id'));

        if ($deleteOpt->output == "TRUE") {
            $data['alertsuccess'] = "Template Deleted Successfully";
        } else {
            $data['alerterror'] = "We Can't Delete this footer<br/>
            because That are mapped in following Template<br/>"
            . $deleteOpt->result
            . "<br/> Please remove and then delete";
        }

        $data['redirect'] = 'site/viewEmailMailFooter';
        $data['title'] = 'View Footer Template';
        $this->load->view('redirect', $data);
    }

    function viewemailsignature() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->load->library("pagination");
        $config = array();
        $config["base_url"] = site_url("site/viewEmailSignature");
        $config["total_rows"] = $this->message_model->emailSignatureRowCount();
        $config["per_page"] = 20;
        $config["uri_segment"] = 3;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $this->pagination->initialize($config);

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $data["table"] = $this->message_model->fetch_email_signature($config["per_page"], $page);
        $data['pageCount'] = $page;
        $data["links"] = $this->pagination->create_links();
        $data['page'] = 'viewEmailSignature';
        $data['title'] = 'view Email Mail Footer';
        $this->load->view('template', $data);
    }

    function createEmailSignature() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("createEmailSignature");
        $data['page'] = 'createEmailSignature';
        $data['title'] = 'Email Footer';
        $this->load->view('template', $data);
    }

    function createEmailSignatureSubmit() {

        $uploadPath = $this->config->item('uploadPath');
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $config = array(
            'upload_path' => $uploadPath . "/signature/",
            'file_name' => "SignatureImg_" . time(),
            'allowed_types' => "gif|jpg|png|jpeg",
            'overwrite' => TRUE,
            'max_size' => "100000", // Can be set to particular file size , here it is 100kb(100kb Kb)
            'max_height' => "200",
            'max_width' => "200"
        );
        $this->load->library('upload', $config);
        $this->form_validation->set_rules('emailSignatureName', 'Signature Name', 'trim|required|is_unique[email_signature_template.signature_name]');
        $this->form_validation->set_rules('emailSignatureText', 'Signature Text', 'trim|required');
//    $this->form_validation->set_rules('dispose','dispose','trim');
        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $data['page'] = 'createEmailSignature';
            $data['title'] = 'Email Signature';
            $this->load->view('template', $data);
        } else {
            $emailSignatureName = $this->input->post('emailSignatureName');
            $emailSignatureText = $this->input->post('emailSignatureText');
            if ($this->upload->do_upload()) {
                $data = $this->upload->data();
                $uploadPath = $this->config->item('base_url');
                $fileName = $uploadPath . "/uploads/signature/" . $data['file_name'];

                if ($emailSignatureText == "<br>") {
                    $data['alerterror'] = "The Signature Text field is required.";
                    $data['page'] = 'createEmailSignature';
                    $data['title'] = 'Email Signature';
                    $this->load->view('template', $data);
                } else {
                    $messageSignature = $this->message_model->createSignature($emailSignatureName, $emailSignatureText, $fileName);
                    if ($messageSignature == 1) {
                        $data['alertsuccess'] = "Signature Created Successfully";
                    } else {
                        $data['alerterror'] = "Signature Creation Error";
                    }
                    $data['redirect'] = "site/viewEmailSignature";
                    $this->load->view("redirect", $data);
                }
            } else {

                $error = $this->upload->display_errors();
                if ($_FILES['userfile']['size'] == 0) {
                    $fileName = "";
                    if ($emailSignatureText == "<br>") {
                        $data['alerterror'] = "The Signature Text field is required.";
                        $data['page'] = 'createEmailSignature';
                        $data['title'] = 'Email Signature';
                        $this->load->view('template', $data);
                    } else {
                        $messageSignature = $this->message_model->createSignature($emailSignatureName, $emailSignatureText, $fileName);
                        if ($messageSignature == 1) {
                            $data['alertsuccess'] = "Signature Created Successfully";
                        } else {
                            $data['alerterror'] = "Signature Creation Error";
                        }
                        $data['redirect'] = "site/viewEmailSignature";
                        $this->load->view("redirect", $data);
                    }
                } else {
                    $data['alerterror'] = $error;
                    $data['redirect'] = "site/createEmailSignature";
                    $this->load->view("redirect", $data);
                }
            }
        }
    }

    function editEmailSignature($id) {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("editEmailSignature");
        $data['menuDetail'] = $this->getMenuData();
        $data['before'] = $this->message_model->beforeeditEmailSignature($id);
        $data['page'] = 'editEmailSignature';
        $data['title'] = 'Email Signature';
        $this->load->view('template', $data);
    }

    function editEmailSignatureSubmit() {
        // $data['userButtonSetting'] = $this->data['userButtonSetting'];
        // $data['editmode']="0";
        $config = array(
            'upload_path' => "./uploads/signature/",
            'file_name' => "SignatureImg_" . time(),
            'allowed_types' => "gif|jpg|png|jpeg",
            'overwrite' => TRUE,
            'max_size' => "100000", // Can be set to particular file size , here it is 100kb(100kb Kb)
            'max_height' => "200",
            'max_width' => "200"
        );
        $this->load->library('upload', $config);
        $this->form_validation->set_rules('emailSignatureName', 'Signature Tag', 'trim|required');
        $this->form_validation->set_rules('emailSignatureText', 'Signature Text', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $emailSignatureid = $this->input->post('emailSignatureid');
            $data['before'] = $this->message_model->beforeeditEmailSignature($emailSignatureid);
            $data['page'] = 'editEmailSignature';
            $data['title'] = 'Email Signature';
            $this->load->view('template', $data);
        } else {
            $emailSignatureid = $this->input->post('emailSignatureid');
            $emailSignatureName = $this->input->post('emailSignatureName');
            $emailSignatureText = $this->input->post('emailSignatureText');
            $duplicateCheckOutput = $this->common_model->editFieldDuplicateCheck($emailSignatureid, "id", $emailSignatureName, "signature_name", "email_signature_template");
            if ($duplicateCheckOutput->output == "TRUE") {
                if ($this->upload->do_upload()) {
                    $data = $this->upload->data();
                    $uploadPath = $this->config->item('base_url');
                    $fileName = $uploadPath . "/uploads/signature/" . $data['file_name'];
                    $messageSignature = $this->message_model->editEmailMailSignature($emailSignatureid, $emailSignatureName, $emailSignatureText, $fileName);
                    if ($messageSignature == 1) {
                        $data['alertsuccess'] = "Signature Edited Successfully";
                    } else {
                        $data['alerterror'] = "Signature Edit Error";
                    }
                    $data['redirect'] = "site/viewEmailSignature";
                    $this->load->view("redirect", $data);
                } else {
                    $error = $this->upload->display_errors();
                    if ($_FILES['userfile']['size'] == 0) {
                        $fileName = "";
                        $messageSignature = $this->message_model->editEmailMailSignature($emailSignatureid, $emailSignatureName, $emailSignatureText, $fileName);
                        if ($messageSignature == 1) {
                            $data['alertsuccess'] = "Signature edited Successfully";
                        } else {
                            $data['alerterror'] = "Signature edited Error";
                        }
                        $data['redirect'] = "site/viewEmailSignature";
                        $this->load->view("redirect", $data);
                    } else {
                        $data['alerterror'] = $error;
                        $data['redirect'] = "site/editEmailSignature/$emailSignatureid";
                        $this->load->view("redirect", $data);
                    }
                }
            } else {
                $data['alerterror'] = "Please enter Unique Signature Tag";
                $data['redirect'] = "site/editEmailSignature/" . $emailSignatureid;
                $this->load->view("redirect", $data);
            }
        }
    }

    function deleteEmailSignature() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("view dispose");
        $deleteOpt = $this->message_model->deleteSignature($this->input->get('id'));
        if ($deleteOpt->output == "TRUE") {
            $data['alertsuccess'] = "Template Deleted Successfully";
        } else {
            $data['alerterror'] = "We Can't Delete this Subject <br/>
            because That are mapped in following Template<br/>"
            . $deleteOpt->result
            . "<br/> Please remove and then delete";
        }
        $data['redirect'] = 'site/viewEmailSignature';
        $data['title'] = 'View Signature Template';
        $this->load->view('redirect', $data);
    }

    //create email template start
    function createEmailTemplate() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("createEmailTemplate");
        $data['menuDetail'] = $this->getMenuData();
        $data['emailSubject'] = $this->message_model->getemailsubject();
        $data['emailBody'] = $this->message_model->getemailbody();
        $data['emailSignature'] = $this->message_model->getemailsignature();
        $data['emailFooter'] = $this->message_model->getemailfooter();
        $data['page'] = 'createEmailTemplate';
        $data['title'] = 'Email Teamplate';
        $this->load->view('template', $data);
    }

    function createEmailTemplateSubmit() {
        // $data['userButtonSetting'] = $this->data['userButtonSetting'];
        // $data['editmode']="0";
        $this->form_validation->set_rules('emailTemplateTag', 'Email Template Tag', 'trim|required|is_unique[email_template.email_template_name]');
        $this->form_validation->set_rules('emailSubject', 'Email Subject', 'trim|required');
        $this->form_validation->set_rules('emailBody', 'Email Body', 'trim|required');
        $this->form_validation->set_rules('emailSignature', 'Signature', 'trim');
        $this->form_validation->set_rules('emailFooter', 'Footer', 'trim');
//    $this->form_validation->set_rules('dispose','dispose','trim');
        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $data['emailSubject'] = $this->message_model->getemailsubject();
            $data['emailBody'] = $this->message_model->getemailbody();
            $data['emailSignature'] = $this->message_model->getemailsignature();
            $data['emailFooter'] = $this->message_model->getemailfooter();
            $data['page'] = 'createEmailTemplate';
            $data['title'] = 'Email Template';
            $this->load->view('template', $data);
        } else {
            $emailTemplateTag = $this->input->post('emailTemplateTag');
            $emailSubject = $this->input->post('emailSubject');
            $emailBody = $this->input->post('emailBody');
            $emailSignature = $this->input->post('emailSignature');
            $emailFooter = $this->input->post('emailFooter');
            $saveTemplate = $this->message_model->createtemplate($emailTemplateTag, $emailSubject, $emailBody, $emailSignature, $emailFooter);
            if ($saveTemplate == 1) {
                $data['alertsuccess'] = "Email Template Created Successfully";
            } else {
                $data['alerterror'] = "Email Template Creation Error";
            }
            $data['redirect'] = "site/viewmailtemplate";
            $this->load->view("redirect", $data);
        }
    }

    function editEmailTemplate($id) {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $this->userStateChangeSet("editEmailTemplate");
        $data['menuDetail'] = $this->getMenuData();
        $data['emailSubject'] = $this->message_model->getemailsubject();
        $data['emailBody'] = $this->message_model->getemailbody();
        $data['emailSignature'] = $this->message_model->getemailsignature();
        $data['emailFooter'] = $this->message_model->getemailfooter();
        $data['before'] = $this->message_model->beforeedittemplate($id);
        $data['page'] = 'editEmailTemplate';
        $data['title'] = 'Email Footer';
        $this->load->view('template', $data);
    }

    function editEmailTemplateSubmit() {
        $data['menuDetail'] = $this->getMenuData();
        $this->form_validation->set_rules('emailTemplateTag', 'Tag Name', 'trim|required');
        $this->form_validation->set_rules('emailSubject', 'Subject', 'trim|required');
        $this->form_validation->set_rules('emailBody', 'Body', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $data['before'] = $this->message_model->beforeedittemplate($this->input->post('emailTemplateid'));
            $data['emailSubject'] = $this->message_model->getemailsubject();
            $data['emailBody'] = $this->message_model->getemailbody();
            $data['emailSignature'] = $this->message_model->getemailsignature();
            $data['emailFooter'] = $this->message_model->getemailfooter();
            $data['alerterror'] = validation_errors();
            $emailTemplateid = $this->input->post('emailTemplateid');
            // $data['before']        = $this->beforeedittemplate();
            $data['page'] = 'editEmailTemplate';
            $data['title'] = 'Email Template';
            $this->load->view('template', $data);
        } else {
            $emailTemplateid = $this->input->post('emailTemplateid');
            $emailTemplateTag = $this->input->post('emailTemplateTag');
            $emailSubject = $this->input->post('emailSubject');
            $emailBody = $this->input->post('emailBody');
            $emailSignature = $this->input->post('emailSignature');
            $emailFooter = $this->input->post('emailFooter');      //$dispose=$this->input->post('dispose');
            $duplicateCheckOutput = $this->common_model->editFieldDuplicateCheck($emailTemplateid, "id", $emailTemplateTag, "email_template_name", "email_template");
            if ($duplicateCheckOutput->output == "TRUE") {
                $messageTemplate = $this->message_model->editMailTemplate($emailTemplateid, $emailTemplateTag, $emailSubject, $emailBody, $emailSignature, $emailFooter);
                if ($messageTemplate == 1) {
                    $data['alertsuccess'] = "Template Edited Successfully";
                } else {
                    $data['alerterror'] = "Template Edit Error";
                }
                $data['redirect'] = "site/viewMailTemplate";
                $this->load->view("redirect", $data);
            } else {
                $data['alerterror'] = "Please Enter Unique Tag";
                $data['redirect'] = "site/editEmailTemplate/$emailTemplateid";
                $this->load->view("redirect", $data);
            }
        }
    }

    // view for add user smtp setting
    function viewUserSmtpSetting() {
        $data['menuDetail'] = $this->getMenuData();
        $data['table'] = $this->message_model->userSmtpSettingData();
        $data['smtpArray'] = $this->message_model->getSmtpData();
        $data['accesslevelArray'] = $this->user_model->getaccesslevels();
        $data['page'] = 'viewUserSmtpSetting';
        $data['title'] = 'User Smtp Setting';
        $this->load->view('template', $data);
    }

    function editUserSmtpSetting($id) {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $this->userStateChangeSet("editUserSmtpSetting");
        $data['menuDetail'] = $this->getMenuData();
        $data['before'] = $this->message_model->beforeeditUserData($id);
        $data['SmtpSeletedData'] = $this->message_model->SmtpSeleted($id);
        $data['smtpArray'] = $this->message_model->getSmtpData();
        $data['page'] = 'editUserSmtpSetting';
        $data['title'] = 'Email Body';
        $this->load->view('template', $data);
    }

    function editUserSmtpSettingSubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $this->form_validation->set_rules('userId', '', 'trim');
        $this->form_validation->set_rules('userName', '', 'trim');
        $Email = $this->input->post('Email');
        $oldEmail = $this->input->post('oldEmail');
        if ($oldEmail != $Email) {
            $this->form_validation->set_rules('Email', 'Email', 'trim|required|valid_email');
        }

        $this->form_validation->set_rules('selectedSmtp', 'Smtp server', 'trim|required');
//    $this->form_validation->set_rules('dispose','dispose','trim');
        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $id = $this->input->post('userId');
            $data['before'] = $this->message_model->beforeeditUserData($id);
            $data['smtpArray'] = $this->message_model->getSmtpData();
            $data['page'] = 'editUserSmtpSetting';
            $data['title'] = 'Email Body';
            $this->load->view('template', $data);
        } else {
            $userId = $this->input->post('userId');
            $userName = $this->input->post('userName');
            $selectedSmtp = $this->input->post('selectedSmtp');
            $messageBody = $this->message_model->editUserSmtpSetting($userId, $userName, $Email, $selectedSmtp);
            if ($messageBody == 1) {
                $data['alertsuccess'] = "User SMTP Edited Successfully";
            } else {
                $data['alerterror'] = "User SMTP Edit Error";
            }
            $data['redirect'] = "site/viewUserSmtpSetting";
            $this->load->view("redirect", $data);
        }
    }

    function deleteTemplate($id) {
        $this->message_model->deletetemplate($id);
        $data['alertsuccess'] = "Template Deleted Successfully";
        $data['redirect'] = 'site/viewMailTemplate';
        $data['title'] = 'View Subject Template';
        $this->load->view('redirect', $data);
    }

    function viewSmtpSetting() {
        $this->load->library("pagination");
        $config = array();
        $config["base_url"] = site_url("site/viewSmtpSetting");
        $config["total_rows"] = $this->message_model->smtpRowCount();
        $config["per_page"] = 20;
        $config["uri_segment"] = 3;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $this->pagination->initialize($config);

        $data['menuDetail'] = $this->getMenuData();
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $data["table"] = $this->message_model->fetchSmtpSetting($config["per_page"], $page);
        $data['pageCount'] = $page;
        $data["links"] = $this->pagination->create_links();
        $data['page'] = 'viewSmtpSetting';
        $data['title'] = 'view Smtp setting';
        $this->load->view('template', $data);
    }

    function createSmtpSetting() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("createSmtp");
        $data['page'] = 'createSmtpSetting';
        $data['title'] = 'Create SMTP';
        $this->load->view('template', $data);
    }

    function createSmtpSettingSubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $this->form_validation->set_rules('smtpTag', 'Tag Name', 'trim|required|is_unique[email_smtp_detail.tag]');
        // $this->form_validation->set_rules('smtpAuth','Authentication','trim|required');
        // $this->form_validation->set_rules('smtpSecure','SMTP Secure','trim|required');
        $this->form_validation->set_rules('smtpHost', 'SMTP Host', 'trim|required');
        $this->form_validation->set_rules('smtpPort', 'SMTP Port', 'trim|required');
//    $this->form_validation->set_rules('dispose','dispose','trim');
        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $data['page'] = 'createSmtpSetting';
            $data['title'] = 'Create SMTP Setting';
            $this->load->view('template', $data);
        } else {
            $smtpTag = $this->input->post('smtpTag');
            $smtpAuth = $this->input->post('smtpAuth');
            $smtpSecure = $this->input->post('smtpSecure');
            $smtpHost = $this->input->post('smtpHost');
            $smtpPort = $this->input->post('smtpPort');
            //$dispose=$this->input->post('dispose');
            $messageSmtp = $this->message_model->createSmtp($smtpTag, $smtpAuth, $smtpSecure, $smtpHost, $smtpPort);
            if ($messageSmtp == 1) {
                $data['alertsuccess'] = "New SMTP Created Successfully";
            } else {
                $data['alerterror'] = "New SMTP Creation Error";
            }
            $data['redirect'] = "site/viewSmtpSetting";
            $this->load->view("redirect", $data);
        }
    }

    function editSmtpSetting($id) {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("editSmtpSetting");
        $data['before'] = $this->message_model->beforeeditSmtp($id);
        $data['page'] = 'editSmtpSetting';
        $data['title'] = 'Edit SMTP Setting';
        $this->load->view('template', $data);
    }

    function deleteSmtpSetting() {
        $data['menuDetail'] = $this->getMenuData();
        $this->message_model->deleteSmtp($this->input->get('id'));
        $data['alertsuccess'] = "SMTP Deleted Successfully";
        $data['redirect'] = 'site/viewSmtpSetting';
        $data['title'] = 'View SMTP Setting';
        $this->load->view('redirect', $data);
    }

    function editSmtpSettingSubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        // $data['editmode']="0";
        $this->form_validation->set_rules('smtpTag', 'Tag Name', 'trim|required');
        // $this->form_validation->set_rules('smtpAuth','SMTP Authentication','trim|required');
        // $this->form_validation->set_rules('smtpSecure','SMTP Secure','trim|required');
        $this->form_validation->set_rules('smtpHost', 'SMTP Host', 'trim|required');
        $this->form_validation->set_rules('smtpPort', 'SMTP Port', 'trim|required');
//    $this->form_validation->set_rules('dispose','dispose','trim');
        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $smtpId = $this->input->post('smtpId');
            $data['before'] = $this->message_model->beforeeditSmtp($smtpId);
            $data['page'] = 'editSmtpSetting';
            $data['title'] = 'Edit SMTP Setting';
            $this->load->view('template', $data);
        } else {
            $smtpId = $this->input->post('smtpId');
            $smtpTag = $this->input->post('smtpTag');
            $smtpAuth = $this->input->post('smtpAuth');
            $smtpSecure = $this->input->post('smtpSecure');
            $smtpHost = $this->input->post('smtpHost');
            $smtpPort = $this->input->post('smtpPort');
            $duplicateCheckOutput = $this->common_model->editFieldDuplicateCheck($smtpId, "id", $smtpTag, "tag", "email_smtp_detail");
            //$dispose=$this->input->post('dispose');
            $messageSmtp = $this->message_model->editSmtp($smtpId, $smtpTag, $smtpAuth, $smtpSecure, $smtpHost, $smtpPort);
            if ($messageSmtp == 1) {
                $data['alertsuccess'] = "SMTP Setting edited Successfully";
            } else {
                $data['alerterror'] = "SMTP Setting edit Error";
            }
            $data['redirect'] = "site/viewSmtpSetting";
            $this->load->view("redirect", $data);
        }
    }

    function viewSenderSmtpSetting() {
        $this->load->library("pagination");
        $config = array();
        $config["base_url"] = site_url("site/viewSenderSmtpSetting");
        $config["total_rows"] = $this->message_model->senderSmtpRowCount();
        $config["per_page"] = 20;
        $config["uri_segment"] = 3;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $this->pagination->initialize($config);

        $data['menuDetail'] = $this->getMenuData();
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $data['smtpArray'] = $this->message_model->getSmtpData();
        $data['emailSignature'] = $this->message_model->getemailsignature();
        $data["table"] = $this->message_model->fetchSenderSmtpSetting($config["per_page"], $page);
        $data['pageCount'] = $page;
        $data["links"] = $this->pagination->create_links();
        $data['page'] = 'viewSenderSmtpSetting';
        $data['title'] = 'view Smtp setting';
        $this->load->view('template', $data);
    }

    function deleteSenderSmtpSetting() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("viewSenderSmtpSetting");
        $this->message_model->deleteSenderSmtp($this->input->get('id'));
        $data['alertsuccess'] = "Sender SMTP Deleted Successfully";
        $data['redirect'] = 'site/viewSenderSmtpSetting';
        $data['title'] = 'View Sender SMTP Setting';
        $this->load->view('redirect', $data);
    }

    function editSenderSmtpSetting($id) {
        $data['menuDetail'] = $this->getMenuData();
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $this->userStateChangeSet("editSenderSmtpSetting");
        $data['before'] = $this->message_model->beforeeditSenderSmtp($id);
        $data['smtpArray'] = $this->message_model->getSmtpData();
        $data['emailSignature'] = $this->message_model->getemailsignature();
        $data['page'] = 'editSenderSmtpSetting';
        $data['title'] = 'Sender SMTP Setting';
        $this->load->view('template', $data);
    }

    function editSenderSmtpSubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        // $data['editmode']="0";
        // $this->form_validation->set_rules(      'password', 'Password','trim');
        $this->form_validation->set_rules('email', 'Email ID', 'trim|required|valid_email');
        $this->form_validation->set_rules('emailSignature', 'Signature', 'trim');
        $this->form_validation->set_rules('selectedSmtp', 'SMTP', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $senderID = $this->input->post('senderID');
            $data['smtpArray'] = $this->message_model->getSmtpData();
            $data['emailSignature'] = $this->message_model->getemailsignature();
            $data['before'] = $this->message_model->beforeeditSenderSmtp($senderID);
            $data['page'] = 'editSenderSmtpSetting';
            $data['title'] = 'Edit Sender SMTP Setting';
            $this->load->view('template', $data);
        } else {
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            $emailSignature = $this->input->post('emailSignature');
            $selectedSmtp = $this->input->post('selectedSmtp');
            $senderID = $this->input->post('senderID');
            $messageSenderSmtp = $this->message_model->editSenderSmtp($senderID, $email, $password, $emailSignature, $selectedSmtp);
            if ($messageSenderSmtp == 1) {
                $data['alertsuccess'] = "Sender SMTP Setting edited Successfully";
            } else {
                $data['alerterror'] = "Sender SMTP Setting edit Error";
            }
            $data['redirect'] = "site/viewSenderSmtpSetting";
            $this->load->view("redirect", $data);
        }
    }

    function createSenderSmtpSetting() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("createSenderSmtpSetting");
        $data['smtpArray'] = $this->message_model->getSmtpData();
        $data['emailSignature'] = $this->message_model->getemailsignature();
        $data['page'] = 'createSenderSmtpSetting';
        $data['title'] = 'Create Sender SMTP';
        $this->load->view('template', $data);
    }

    function createSenderSmtpSubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        // $data['editmode']="0";
        $this->form_validation->set_rules('email', 'Email ID', 'trim|required|valid_email');
        // $this->form_validation->set_rules('password', 'Password','trim|required');
        $this->form_validation->set_rules('emailSignature', 'Signature', 'trim');
        $this->form_validation->set_rules('selectedSmtp', 'SMTP', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $data['smtpArray'] = $this->message_model->getSmtpData();
            $data['emailSignature'] = $this->message_model->getemailsignature();
            $data['page'] = 'createSenderSmtpSetting';
            $data['title'] = 'Create Sender SMTP Setting';
            $this->load->view('template', $data);
        } else {
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            $emailSignature = $this->input->post('emailSignature');
            $selectedSmtp = $this->input->post('selectedSmtp');
            $messageSmtp = $this->message_model->createSenderSmtp($email, $password, $emailSignature, $selectedSmtp);
            if ($messageSmtp == 1) {
                $data['alertsuccess'] = "New Sender SMTP Created Successfully";
            } else {
                $data['alerterror'] = "New Sender SMTP Creation Error";
            }
            $data['redirect'] = "site/viewSenderSmtpSetting";
            $this->load->view("redirect", $data);
        }
    }

    function processLeadJsonUpdate() {
        $proceessId = $this->input->post('proceessId');
        $proceessLeadJson = $this->input->post('proceessLeadJson');
        $campaignId = $this->input->post('campaignId');
        $processUpdate = $this->process_model->processLeadJsonUpdate($proceessId, $proceessLeadJson);
        if (isset($processUpdate->OUTPUT) && $processUpdate->OUTPUT == "TRUE") {
            $data['alertsuccess'] = "Process lead json updated Successfully";
        } else {
            $data['alerterror'] = isset($processUpdate->LOG) ? $processUpdate->LOG : "Process lead json updated unsuccessfully";
        }
        $data['other'] = "campaign=" . $campaignId;
        $data['redirect'] = "site/viewcampaignprocess";
        $this->load->view("redirect2", $data);
    }

    function processScriptJsonUpdate() {
        $data['menuDetail'] = $this->getMenuData();
        $proceessId = $this->input->post('proceessId');
        $processScriptJson = $this->input->post('processScriptJson');
        $processFileJson = $this->input->post('processFileJson');
        $campaignId = $this->input->post('campaignId');
        $jsonStatus = $this->input->post('jsonStatus');
        $processUpdate = $this->process_model->processScriptJsonUpdate($proceessId, $processScriptJson, $processFileJson, $jsonStatus);
        if ($processUpdate) {
            $data['alertsuccess'] = "Process Script json updated Successfully";
        } else {
            $data['alerterror'] = "Process Script json updated unsuccessfully";
        }
        $data['other'] = "campaign=" . $campaignId;
        $data['redirect'] = "site/viewcampaignprocess";
        $this->load->view("redirect2", $data);
    }

    function LoggerDownload() {

        $checkFeatureAllow = $this->standard_model->checkFeatureAllow('1', 'multilevel_dispose_allow');

        $configArray = array(
            120 => (object) array('variableName' => 'crmUniqueId', 'defaultValue' => '0'),
        );

        $configData = $this->config_model->getMultipleConfig($configArray);

        $custName = $this->input->get_post('custName');
        $process = $this->input->get_post('process');
        $campaign = $this->input->get_post('campaign');
        $mydate = $this->input->get_post('date');
        $mydateto = $this->input->get_post('dateto');
        $phoneno = $this->input->get_post('phoneno');
        $evaluation = $this->input->get_post('evaluation');
        $direction = $this->input->get_post('direction');
        //$dispose = $this->input->get_post('dispose');
        //$dispose2 = $this->input->get_post('dispose2');
        //$dispose3 = $this->input->get_post('dispose3');
        
        $disposeArray = $this->input->get_post('dispose');
        $dispose = '';
        if (count($disposeArray) > 0) {
            foreach ($disposeArray as $key => $fistLevelDispose) {
                if ($dispose == "") {
                    $dispose = '"' . $fistLevelDispose . '"';
                } else {
                    $dispose .= ',"' . $fistLevelDispose . '"';
                }
            }
        }

        $dispose2Array = $this->input->get_post('dispose2');
        $dispose2 = '';
        if (count($dispose2Array) > 0) {
            foreach ($dispose2Array as $key => $secondLevelDispose) {
                if ($dispose2 == "") {
                    $dispose2 = '"' . $secondLevelDispose . '"';
                } else {
                    $dispose2 .= ',"' . $secondLevelDispose . '"';
                }
            }
        }

        $dispose3Array = $this->input->get_post('dispose3');
        $dispose3 = '';
        if (count($dispose3Array) > 0) {
            foreach ($dispose3Array as $key => $thirdLevelDispose) {
                if ($dispose3 == "") {
                    $dispose3 = '"' . $thirdLevelDispose . '"';
                } else {
                    $dispose3 .= ',"' . $thirdLevelDispose . '"';
                }
            }
        }

        $leadset = $this->input->get_post('leadset');
        $dissconnector = $this->input->get_post('dissconnector');
        $loggerdidNumber = $this->input->get_post('loggerdidNumber');

        if ($configData->crmUniqueId == 1) {

            $crmId = $this->input->get_post('crmId');
        } else {

            $crmId = "";
        }
        $accesslevel = $this->session->userdata('accesslevel');
        $agent = "";
        if (( $accesslevel <= 3 || $accesslevel == 10 ) && $accesslevel != "") {

            $agentArray = $this->input->post('agent');

            //echo "<pre>";
            //print_r($agentArray);
            if (count($agentArray) > 0) {
                $selected_agents = $agentArray;
                foreach ($agentArray as $key => $value) {
                    if ($agent == "") {
                        $agent = $value;
                    } else {
                        $agent .= "," . $value;
                    }
                }
            } else {
                $agent = "";
            }
        } else {
            $agent = "";
        }

        $filePath = $this->download_model->LoggerDownload($checkFeatureAllow, $process, $custName, $campaign, $mydate, $mydateto, $phoneno, $dispose, 
            $dispose2, $dispose3, $agent, $leadset, $dissconnector, $loggerdidNumber, $crmId, $evaluation, $direction);
        redirect($filePath);
    }

    function MissCdrDownload() {

        $process = $this->input->post('process');
        $campaign = $this->input->post('campaign');
        $date = $this->input->post('date');
        $dateto = $this->input->post('dateto');
        $phoneno = $this->input->post('phoneno');
        $reason = $this->input->post('reason');
        $missCdrDidNumber = $this->input->post('missCdrDidNumber');
        $missInQueueTime = $this->input->post('missInQueueTime');

        $filePath = $this->download_model->MissCdrDownload($process, $campaign, $date, $dateto, $phoneno, $reason, $missCdrDidNumber, $missInQueueTime);
        redirect($filePath);
    }

    function leadMgmtDownload() {

        $agent = "";
        $process = $this->input->get_post('process');
        $campaign = $this->input->get_post('campaign');
        $mydate = $this->input->get_post('date');
        $mydateto = $this->input->get_post('dateto');
        $phoneno = $this->input->get_post('phoneno');
        $dispose = $this->input->get_post('dispose');
        $dispose2 = $this->input->get_post('dispose2');
        $dispose3 = $this->input->get_post('dispose3');

        if (( $accesslevel <= 3 || $accesslevel == 10 || $accesslevel == 5 || $accesslevel == 6 ) && $this->session->userdata('accesslevel') != "") {

            $agent = $this->input->get_post('agent');
        }

        if ($mydate != "") {

            $a = explode('-', $mydate);
            $result = $a[2] . '-' . $a[0] . '-' . $a[1];
            $mydate = $result;
        }
        if ($mydateto != "") {

            $a = explode('-', $mydateto);
            $result = $a[2] . '-' . $a[0] . '-' . $a[1];
            $mydateto = $result;
        }

        if (( $accesslevel <= 3 || $accesslevel == 10 || $accesslevel == 5 || $accesslevel == 6 ) && $this->session->userdata('accesslevel') != "") {

            $data['agent'] = $this->reporting_model->getagent();
        }

        $filePath = $this->download_model->leadManagementDownload($agent, $process, $campaign, $mydate, $mydateto, $phoneno, $dispose, $dispose2, $dispose3);
        redirect($filePath);
    }

    function recordingLogDownload() {
        $checkFeatureAllow = $this->standard_model->checkFeatureAllow('1', 'multilevel_dispose_allow');

        $agent = $this->input->post('agent');
        $process = $this->input->post('process');
        $campaign = $this->input->post('campaign');
        $mydate = $this->input->post('date');
        $mydateto = $this->input->post('dateto');
        $talktime = $this->input->post('talktime');
        $phoneno = $this->input->post('phoneno');
        $dispose = $this->input->post('dispose');
        $dispose2 = $this->input->post('dispose2');
        $dispose3 = $this->input->post('dispose3');
        $leadset = $this->input->post('leadset');
        $dissconnector = $this->input->post('dissconnector');
        $recodingDidNumber = $this->input->post('recodingDidNumber');
        $filePath = $this->download_model->recordingLogDownload($checkFeatureAllow, $agent, $process, $campaign, $mydate, $mydateto, $talktime, $phoneno, $dispose, $dispose2, $dispose3, $leadset, $dissconnector, $recodingDidNumber);
        redirect($filePath);
    }

    function viewMenuParent() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("View config");
        $data['menuParent'] = $this->menu_model->getMenuParent();
        $data['page'] = 'viewMenuParent';
        $data['title'] = 'Menu Parent';
        $this->load->view('template', $data);
    }

    function createMenuParent() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['page'] = 'createMenuParent';
        $data['title'] = 'Menu Parent';
        $this->load->view('template', $data);
    }

    function createMenuParentSubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->form_validation->set_rules('name', 'Menu Group Name', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $this->userStateChangeSet("Create New config");
            $data['page'] = 'createMenuParent';
            $data['title'] = 'create Menu Parent';
            $this->load->view('template', $data);
        } else {
            $name = $this->input->post('name');
            $detail = $this->input->post('detail');
            if ($this->menu_model->createMenuParent($name, $detail) == 0)
                $data['alerterror'] = "New Menu Group Name could not be created.";
            else
                $data['alertsuccess'] = "Menu Group Name created Successfully.";

            $data['redirect'] = "site/viewMenuParent";
            $this->load->view("redirect", $data);
        }
    }

    function editMenuParent() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "1";
        $data['menuParent'] = $this->menu_model->beforeEditMenuParent($this->input->get('id'));
        if ($data['menuParent']->output == "FALSE") {
            $data['alerterror'] = $data['menuParent']->message;
        }

        $data['page'] = 'editMenuParent';
        $data['title'] = 'Edit Menu Parent';
        $this->load->view('template', $data);
    }

    function editMenuParentSubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->form_validation->set_rules('name', 'Menu Group Name', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $data['menuParent'] = $this->menu_model->beforeEditMenuParent($this->input->post('id'));
            $this->userStateChangeSet("Edit config");
            $data['page'] = 'editMenuParent';
            $data['title'] = 'Edit Menu Parent';
            $this->load->view('template', $data);
        } else {
            $id = $this->input->post('id');
            $name = $this->input->post('name');
            $details = $this->input->post('details');
            if ($this->menu_model->editMenuParent($id, $name, $details) == 0)
                $data['alerterror'] = "Menu Group Name Editing was unsuccesful";
            else
                $data['alertsuccess'] = "Menu Group Name edited Successfully.";
            $data['table'] = $this->config_model->viewconfig();
            $data['redirect'] = "site/viewMenuParent";
            //$data['other']="template=$template";
            $this->load->view("redirect", $data);
        }
    }

    function viewMenu() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("View config");
        $data['menuData'] = $this->menu_model->getMenu();
        $data['menuGroupArray'] = $this->menu_model->getAllMenuGroup();
        $data['page'] = 'viewMenu';
        $data['title'] = 'Menu';
        $this->load->view('template', $data);
    }

    function createMenu() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['menuGroupArray'] = $this->menu_model->getAllMenuGroup();
        $data['page'] = 'createMenu';
        $data['title'] = 'Menu Parent';
        $this->load->view('template', $data);
    }

    function createMenuSubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->form_validation->set_rules('menuName', 'Name', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('menuDescription', 'Description', 'trim');
        $this->form_validation->set_rules('location', 'location', 'trim|required');
        $this->form_validation->set_rules('tooltip', 'Tooltip', 'trim|max_length[50]');
        $this->form_validation->set_rules('menuGroup', 'Menu Group', 'trim|required');
        $this->form_validation->set_rules('suAdminFlag', 'Admin Flag', 'trim|required');
        $this->form_validation->set_rules('startDate', 'Start Date', 'trim|required');
        $this->form_validation->set_rules('endDate', 'End Date', 'trim|required');
        $this->form_validation->set_rules('status', 'Status', 'trim|required');
        $this->form_validation->set_rules('glyphiconName', 'Glyphicon Icon', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            if (validation_errors()) {
                $data['alerterror'] = validation_errors();
            } else {
                $data['alerterror'] = "Please Enter Detail";
            }
            $this->userStateChangeSet("Create New config");
            $data['menuGroupArray'] = $this->menu_model->getAllMenuGroup();
            $data['page'] = 'createMenu';
            $data['title'] = 'create Menu';
            $this->load->view('template', $data);
        } else {
            $menuName = $this->input->post('menuName');
            $location = $this->input->post('location');
            $menuDescription = $this->input->post('menuDescription');
            $tooltip = $this->input->post('tooltip');
            $menuGroup = $this->input->post('menuGroup');
            $suAdminFlag = $this->input->post('suAdminFlag');
            $startDate = $this->input->post('startDate');
            $endDate = $this->input->post('endDate');
            $status = $this->input->post('status');
            $glyphiconName = $this->input->post('glyphiconName');
            $output = $this->menu_model->createMenu($menuName, $location, $menuDescription, $tooltip, $menuGroup, $suAdminFlag, $startDate, $endDate, $status, $glyphiconName);
            if ($output == 0)
                $data['alerterror'] = "New Menu could not be created.";
            else
                $data['alertsuccess'] = "Menu created Successfully.";

            $data['redirect'] = "site/viewMenu";
            $this->load->view("redirect", $data);
        }
    }

    function deleteMenu() {
        $data['menuDetail'] = $this->getMenuData();
        $this->menu_model->deleteMenu($this->input->get('id'));
        $data['alertsuccess'] = "Menu deleted Successfully.";
        $data['redirect'] = "site/viewMenu";
        $this->load->view("redirect", $data);
    }

    function menuAdminFlagChanges($menuStaus, $menuId) {
        $data['menuDetail'] = $this->getMenuData();
        $this->menu_model->menuAdminFlagChanges($menuStaus, $menuId);
        $data['alertsuccess'] = "Admin flag change Successfully.";
        $data['redirect'] = "site/viewMenu";
        $this->load->view("redirect", $data);
    }

    function menuStatusChanges($menuStaus, $menuId) {
        $data['menuDetail'] = $this->getMenuData();
        $this->menu_model->menuStatusChanges($menuStaus, $menuId);
        $data['alertsuccess'] = "Menu status change Successfully.";
        $data['redirect'] = "site/viewMenu";
        $this->load->view("redirect", $data);
    }

    function editmenu() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "1";
        $data['menuData'] = $this->menu_model->beforeEditMenu($this->input->get('id'));
        if ($data['menuData']->output == "FALSE") {
            $data['alerterror'] = $data['menuParent']->message;
        }
        $data['menuGroupArray'] = $this->menu_model->getAllMenuGroup();
        $data['page'] = 'editMenu';
        $data['title'] = 'Edit Menu';
        $this->load->view('template', $data);
    }

    function editMenuSubmit() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();

        $this->form_validation->set_rules('menuName', 'Name', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('menuDescription', 'Description', 'trim');
        $this->form_validation->set_rules('location', 'location', 'trim|required');
        $this->form_validation->set_rules('tooltip', 'Tooltip', 'trim|max_length[50]');
        $this->form_validation->set_rules('menuGroup', 'Menu Group', 'trim|required');
        $this->form_validation->set_rules('suAdminFlag', 'Admin Flag', 'trim|required');
        $this->form_validation->set_rules('startDate', 'Start Date', 'trim|required');
        $this->form_validation->set_rules('endDate', 'End Date', 'trim|required');
        $this->form_validation->set_rules('status', 'Status', 'trim|required');
        $this->form_validation->set_rules('glyphiconName', 'Glyphicon Icon', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $data['menuGroupArray'] = $this->menu_model->getAllMenuGroup();
            $data['menuData'] = $this->menu_model->beforeEditMenu($this->input->get_post('id'));
            $this->userStateChangeSet("Edit Menu");
            $data['page'] = 'editMenu';
            $data['title'] = 'Edit Menu';
            $this->load->view('template', $data);
        } else {
            $id = $this->input->post('id');
            $menuName = $this->input->post('menuName');
            $location = $this->input->post('location');
            $menuDescription = $this->input->post('menuDescription');
            $tooltip = $this->input->post('tooltip');
            $menuGroup = $this->input->post('menuGroup');
            $suAdminFlag = $this->input->post('suAdminFlag');
            $startDate = $this->input->post('startDate');
            $endDate = $this->input->post('endDate');
            $status = $this->input->post('status');
            $glyphiconName = $this->input->post('glyphiconName');
            $output = $this->menu_model->editMenu($id, $menuName, $location, $menuDescription, $tooltip, $menuGroup, $suAdminFlag, $startDate, $endDate, $status, $glyphiconName);
            if ($output == 0)
                $data['alerterror'] = "Menu  Name Editing was unsuccesful";
            else
                $data['alertsuccess'] = "Menu  Name edited Successfully.";
            $data['redirect'] = "site/viewMenu";
            //$data['other']="template=$template";
            $this->load->view("redirect", $data);
        }
    }

    function apiIntegrationView($campaignId, $processId, $callingMode) {
        $data['menuDetail'] = $this->getMenuData();
        $data['campaignId'] = $campaignId;
        $data['processId'] = $processId;
        $data['callingMode'] = $callingMode;
        $data['apiEvent'] = $this->api_model->getCallEvent($callingMode);
        $data['processApiData'] = $this->api_model->getProcessApiJson($processId);// ------enable disable api start
        $data['leadsetJsonData'] = $this->standard_model->getLeadsetJson($processId);
        $data['processLeadJsonData'] = $this->standard_model->processLeadJson($processId);
        $data['crmIdAllow'] = 0;
        $configArray = array(
            120 => (object) array('variableName' => 'crmIdAllow', 'defaultValue' => '0'),
            121 => (object) array('variableName' => 'crmIdName', 'defaultValue' => 'CRM Id'));
        $configData = $this->config_model->getMultipleConfig($configArray);
        if (isset($configData->crmIdAllow)) {

            $data['crmIdAllow'] = $configData->crmIdAllow;
        }

        if (isset($configData->crmIdName)) {

            $data['crmIdName'] = $configData->crmIdName;
        }

        $data['page'] = "apiIntegrationView";
        $this->load->view('template', $data);
    }

    function saveProcessApiJson() {
        $campaignId = trim($this->input->get_post('campaignId'));
        $processId = trim($this->input->get_post('processId'));
        $apiJson = $this->input->get_post('tpt_api_json');
//        enable disable api flag
        $checkapiflag = $this->input->get_post('checkapiflag');

        if ($checkapiflag == "on"){
            $checkapiflag = 1;
        }else{
            $checkapiflag = 0;
        }
        $result = $this->api_model->saveProcessApiJson($processId, $apiJson, $checkapiflag);
        if (isset($result->output) && $result->output = "TRUE") {
            $data['alertsuccess'] = "Process Apis successfully updated";
        } else {
            if (isset($result->message)) {
                $data['alerterror'] = $result->message;
            } else {
                $data['alerterror'] = "Process Apis could not be created.";
            }
        }
        $data['other'] = "campaign=" . $campaignId;
        $data['redirect'] = "form/viewApiProcess";
        $this->load->view("redirect", $data);
    }

    function systemApiIntegrationView() {
        $data['menuDetail'] = $this->getMenuData();
        $data['apiEvent'] = $this->api_model->getSystemEvent();
        $data['ApiData'] = $this->api_model->getSystemApiJson();
        $data['page'] = "systemApiIntegrationView";
        $this->load->view('template', $data);
    }

    function saveSystemApiJson() {
        $apiJson = $this->input->get_post('tpt_api_json');
        $checkapiflag = $this->input->get_post('checkapiflag');
        
        if ($checkapiflag == "on"){
            $checkapiflag = 1;
        }else{
            $checkapiflag = 0;
        }
        $result = $this->api_model->saveSystemApiJson($apiJson, $checkapiflag);
        if (isset($result->output) && $result->output = "TRUE") {
            $data['alertsuccess'] = "Apis successfully updated";
        } else {
            if (isset($result->message)) {
                $data['alerterror'] = $result->message;
            } else {
                $data['alerterror'] = "Apis could not be created.";
            }
        }
        $data['redirect'] = "site/systemApiIntegrationView";
        $this->load->view("redirect", $data);
    }

    function delIframeCallSetUpApi() {
        $processId = $this->input->get_post('processId');
        $campaignId = $this->input->get_post('campaignId');
        $this->api_model->delIframeCallSetUpApi($processId);
        $data['alertsuccess'] = "Apis successfully deleted";
        $data['other'] = "campaign=" . $campaignId;
        $data['redirect'] = "form/viewSystemApiProcess";
        $this->load->view("redirect", $data);
    }

    function delIframeSystemSetUpApi() {
        $this->api_model->delIframeSystemSetUpApi();
        $data['alertsuccess'] = "Apis successfully deleted";
        $data['redirect'] = "form/viewSystemMenuTab";
        $this->load->view("redirect", $data);
    }

    function delTptCallSetUpApi() {
        $processId = $this->input->get_post('processId');
        $campaignId = $this->input->get_post('campaignId');
        $this->api_model->delTptCallSetUpApi($processId);
        $data['alertsuccess'] = "Apis successfully deleted";
        $data['other'] = "campaign=" . $campaignId;
        $data['redirect'] = "form/viewApiProcess";
        $this->load->view("redirect", $data);
    }

    function delTptSystemSetUpApi() {
        $this->api_model->delTptSystemSetUpApi();
        $data['alertsuccess'] = "Apis successfully deleted";
        $data['redirect'] = "site/systemApiIntegrationView";
        $this->load->view("redirect", $data);
    }

    //start video extension view
    function viewVideoExtension() {
        $data['menuDetail'] = $this->getMenuData();
        $data['videoExtensionConf'] = $this->config_model->getVideoExtensionConf();
        $data['videoExtensionData'] = $this->extension_model->getVideoExtensionData();
        $data['page'] = "viewVideoExtension";
        $this->load->view('template', $data);
    }

    //end video extension view

    function deleteVideoExtensionData($videoId) {
        if ($videoId != "" && $videoId != 0) {
            $ExtnOutput = $this->extension_model->deleteVideoExtensionData($videoId);
            if ($ExtnOutput->output == "TRUE") {
                $data['alertsuccess'] = "Video Extension deleted Successfully";
            } else {
                $data['alerterror'] = "Video Extension not  deleted.Please Check Parameter";
            }
        } else {
            $data['alerterror'] = "Video Extension not  deleted.Please Check Parameter";
        }
        $data['redirect'] = "site/viewVideoExtension";
        $this->load->view("redirect", $data);
    }

    function createVideoExtension() {
        // $this->form_validation->set_rules('videoExtnName','Video Extension Name','trim|required|min_length[1]|max_length[50]');
        $this->form_validation->set_rules('TotalVideoExtn', 'Total Video Extension', 'trim|required');
        $this->form_validation->set_rules('AudioExtensionWeb', 'Audio Extension web', 'trim|required');
        $this->form_validation->set_rules('VideoExtensionWeb', 'Video Extension web', 'trim|required');
        $this->form_validation->set_rules('VideoExtensionAgent', 'Video Extension agent', 'trim|required');
        $this->form_validation->set_rules('VideoConfExtnWeb', 'Video Conf extn web', 'trim|required');
        $this->form_validation->set_rules('VideoConfExtnAgent', 'Video Conf extn agent', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $data['menuDetail'] = $this->getMenuData();
            $data['alerterror'] = validation_errors();
            $data['videoExtensionConf'] = $this->config_model->getVideoExtensionConf();
            $data['videoExtensionData'] = $this->extension_model->getVideoExtensionData();
            $data['page'] = "viewVideoExtension";
            $this->load->view('template', $data);
        } else {
            $videoExtnName = $this->input->post('videoExtnName');
            $TotalVideoExtn = $this->input->post('TotalVideoExtn');
            $AudioExtensionWeb = $this->input->post('AudioExtensionWeb');
            $VideoExtensionWeb = $this->input->post('VideoExtensionWeb');
            $VideoExtensionAgent = $this->input->post('VideoExtensionAgent');
            $VideoConfExtnWeb = $this->input->post('VideoConfExtnWeb');
            $VideoConfExtnAgent = $this->input->post('VideoConfExtnAgent');

            $ExtnOutput = $this->extension_model->createVideoExtension($TotalVideoExtn, $AudioExtensionWeb, $VideoExtensionWeb, $VideoExtensionAgent, $VideoConfExtnWeb, $VideoConfExtnAgent);

            if ($ExtnOutput->output == "TRUE") {
                $data['alertsuccess'] = "Video Extension Create Successfully";
            } else {
                $data['alerterror'] = "Video Extension not  Create.Please fill all values";
            }

            $data['redirect'] = "site/viewVideoExtension";
            $this->load->view("redirect", $data);
        }
    }

    function inboundTree() {
        $data['page'] = "inboundTree";
        $data['title'] = "agent Dispose Dashboard";
        $inboundTreeId = 1;
        $data['inboundTree'] = $this->inbound_model->getInboundJson($inboundTreeId);
        $data['ivrJson'] = $this->inbound_model->getIvrJson();
        $data['holidayGroup'] = $this->inbound_model->getHolidayGroup();
        // $data['page']="viewVideoExtension";
        $this->load->view('template', $data);
    }

    function pageNotFound($errorMessage = "SORRY PAGE NOT FOUND", $errorDiscription = "Please contact to Admin", $errorType = "404") {
        $data['errorMessage'] = $errorMessage;
        $data['errorDiscription'] = $errorDiscription;
        $data['errorType'] = $errorType;
        $data['page'] = "pageNotFound";
        $this->load->view('error', $data);
    }

    function chatwindow() {
        $data['menuDetail'] = $this->getMenuData();
        $data['page'] = "GroupChat";
        $this->load->view('template', $data);
    }

    function leadManagement() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $accesslevel = $this->session->userdata('accesslevel');
        $perPage = 50;
        $this->userStateChangeSet("leadManagement");
        $data['checkFeatureAllow'] = $this->standard_model->checkFeatureAllow('1', 'multilevel_dispose_allow');
        $data['editmode'] = "0";
        $agent = "";
        $process = $this->input->get_post('process');
        $campaign = $this->input->get_post('campaign');
        $mydate = $this->input->get_post('date');
        $mydateto = $this->input->get_post('dateto');
        $phoneno = $this->input->get_post('phoneno');
        $dispose = $this->input->get_post('dispose');
        $dispose2 = $this->input->get_post('dispose2');
        $dispose3 = $this->input->get_post('dispose3');

        if (( $accesslevel <= 3 || $accesslevel == 10 || $accesslevel == 5 || $accesslevel == 6 ) && $this->session->userdata('accesslevel') != "") {

            $agent = $this->input->get_post('agent');
        }

        if ($mydate != "") {

            $a = explode('-', $mydate);
            $result = $a[2] . '-' . $a[0] . '-' . $a[1];
            $mydate = $result;
        }
        if ($mydateto != "") {

            $a = explode('-', $mydateto);
            $result = $a[2] . '-' . $a[0] . '-' . $a[1];
            $mydateto = $result;
        }

        if (( $accesslevel <= 3 || $accesslevel == 10 || $accesslevel == 5 || $accesslevel == 6 ) && $this->session->userdata('accesslevel') != "") {

            $data['agent'] = $this->reporting_model->getagent();
        }

        $data['campaign'] = $this->reporting_model->getcampaign();
        // $data['process']            = $this->reporting_model->getprocess();
        $campaignProcessData = $this->process_model->getLiveProcess();
        $data['process'] = $campaignProcessData->process;
        $data['dispose'] = $this->dispose_model->getdispose();
        $refreshtime = $this->config_model->getConfigValue("87");
        if ($refreshtime->output == "TRUE") {
            $data['refreshtime'] = $refreshtime->value;
        } else {
            $data['refreshtime'] = 120;
        }

        $data['selectedagent'] = $agent;
        $data['selectedprocess'] = $process;
        $data['selectedcampaign'] = $campaign;
        $data['selectedphoneno'] = $phoneno;
        $data['selecteddispose'] = $dispose;
        $data['selecteddispose2'] = $dispose2;
        $data['selecteddispose3'] = $dispose3;

        if ($mydate != "")
            $data['selecteddate'] = date("m-d-Y", strtotime($mydate));
        else
            $data['selecteddate'] = "";
        if ($mydateto != "")
            $data['selecteddateto'] = date("m-d-Y", strtotime($mydateto));
        else
            $data['selecteddateto'] = "";

        $this->load->library("pagination");
        $config = array();

        $config['page_query_string'] = FALSE;
        $config['base_url'] = site_url("site/leadManagement");

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $data["table"] = $this->api_model->leadManagement($perPage, $page, $agent, $process, $campaign, $mydate, $mydateto, $phoneno, $dispose, $dispose2, $dispose3);

        if (!empty($data["table"])) {

            $config["total_rows"] = ($this->uri->segment(3)) ? $this->uri->segment(3) + 51 : 51;
        } else {

            $config["total_rows"] = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        }
        $config["per_page"] = $perPage;
        $config["uri_segment"] = 3;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_tag_open'] = '<li class="nextPage">';
        $config['next_tag_close'] = '</li class="previousPage">';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['display_pages'] = FALSE;
        $config['first_link'] = FALSE;
        $config['last_link'] = FALSE;
        $this->pagination->initialize($config);
        $data["links"] = $this->pagination->create_links();
        $limit = $page;
        if ($page == 0) {
            $limit = $perPage;
            $start = 0;
        } else {
            $start = $limit;
            $limit = $perPage;
        }
        $data['limit'] = $limit;
        $data['start'] = $start;
        $phoneSettingArray = array();
        if ($accesslevel == 4) {
            $phoneSetting = $this->process_model->getProcessPhoneSetting();
            if (isset($phoneSetting->result)) {
                foreach ($phoneSetting->result as $key => $value) {
                    $phoneSettingArray[$key] = $value;
                }
            }
        }
        $data['phoneSetting'] = $phoneSettingArray;
        $data['page'] = "leadManagement";
        $data['title'] = "Log Detail";

        $this->load->view('template', $data);
    }

    function leadpenetrationnew() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $accesslevel = $this->session->userdata('accesslevel');
        $perPage = 50;
        $this->userStateChangeSet("leadpenetrationnew");
        $data['checkFeatureAllow'] = $this->standard_model->checkFeatureAllow('1', 'multilevel_dispose_allow');
        $data['editmode'] = "0";
        $process = "";
        $campaign = "";
        $mydate = "";
        $mydateto = "";
        $custName = "";
        $custName = "";
        $dispose = "";
        $callCount = "";
        $leadId = "";
        $process = $this->input->post('process');
        $campaign = $this->input->post('campaign');
        $mydate = $this->input->post('date');
        $mydateto = $this->input->post('dateto');
        $phoneno = $this->input->post('phoneno');
        $callCount = $this->input->post('callCount');
        $leadset = $this->input->post('leadset');
        $leadsetName = $this->input->post('leadsetName');
        $mysqlFlag = $this->input->post('mysqlFlag');
        $mysqlFlagMax = $this->input->post('mysqlFlagMax');

        if ($mydate != "") {
            $a = explode('-', $mydate);
            $result = $a[2] . '-' . $a[0] . '-' . $a[1];
            $mydate = $result;
        }

        if ($mydateto != "") {
            $a = explode('-', $mydateto);
            $result = $a[2] . '-' . $a[0] . '-' . $a[1];
            $mydateto = $result;
        }

        $data['campaign'] = $this->reporting_model->getcampaign();
        $data['process'] = $this->reporting_model->getprocess();
        $data['leadset'] = $this->lead_model->getleadset();
        $data['dispose'] = $this->dispose_model->getdispose();

        if ($mydate != "")
            $data['selecteddate'] = date("m-d-Y", strtotime($mydate));
        else
            $data['selecteddate'] = "";
        if ($mydateto != "")
            $data['selecteddateto'] = date("m-d-Y", strtotime($mydateto));
        else
            $data['selecteddateto'] = "";

        $data['selectedProcess'] = $process;
        $data['selectedCampaign'] = $campaign;
        $data['selectedMydate'] = $mydate;
        $data['selectedMydateto'] = $mydateto;
        $data['selectedPhoneno'] = $phoneno;
        $data['selectedCallCount'] = $callCount;
        $data['selectedLeadset'] = $leadset;
        $data['selectedLeadsetName'] = $leadsetName;

        $this->load->library("pagination");
        $config = array();
        $config['page_query_string'] = FALSE;
        $config['base_url'] = site_url("site/leadpenetrationnew");

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

        $mysqlFlag = "";

        if ($leadset != "") {

            if ($page < $mysqlFlagMax || $mysqlFlagMax == "") {

                $mysqlFlag = "TRUE";
            }
            $data["table"] = $this->cdr_model->fetch_lead_log($perPage, $page, "FALSE", $process, $campaign, $mydate, $mydateto, $phoneno, $callCount, $leadset, $leadsetName, $mysqlFlag, $mysqlFlagMax);
        } else {
            $data["table"] = array();
        }

        if (!empty($data["table"])) {
            $config["total_rows"] = ($this->uri->segment(3)) ? $this->uri->segment(3) + 51 : 51;
        } else {
            $config["total_rows"] = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        }

        $config["per_page"] = $perPage;
        $config["uri_segment"] = 3;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_tag_open'] = '<li class="nextPage">';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li class="previousPage">';
        $config['prev_tag_close'] = '</li>';
        $config['display_pages'] = FALSE;
        $config['first_link'] = FALSE;
        $config['last_link'] = FALSE;
        $this->pagination->initialize($config);
        $data["links"] = $this->pagination->create_links();
        $limit = $page;
        if ($page == 0) {

            $limit = $perPage;
            $start = 0;
        } else {
            $start = $limit;
            $limit = $perPage;
        }
        $data['limit'] = $limit;
        $data['start'] = $start;

        $phoneSettingArray = array();
        if ($accesslevel == 4) {
            $phoneSetting = $this->process_model->getProcessPhoneSetting();
            if (isset($phoneSetting->result)) {
                foreach ($phoneSetting->result as $key => $value) {
                    $phoneSettingArray[$key] = $value;
                }
            }
        }
        $data['phoneSetting'] = $phoneSettingArray;
        $data['page'] = "leadpenetrationnew";
        $data['title'] = "Log Detail";
        $this->load->view('template', $data);
    }

    function downloadleadpenetrationnew() {

        $process = $this->input->post('process');
        $campaign = $this->input->post('campaign');
        $mydate = $this->input->post('date');
        $mydateto = $this->input->post('dateto');
        $phoneno = $this->input->post('phoneno');
        $callCount = $this->input->post('callCount');
        $leadset = $this->input->post('leadset');
        $leadsetName = $this->input->post('leadsetName');


        $filePath = $this->download_model->leadpenetrationDownload($process, $campaign, $mydate, $mydateto, $phoneno, $callCount, $leadset, $leadsetName);
        redirect($filePath);
    }

    function resetleadpenetrationnew() {

        // $data['message']="1";
        // $this->load->view('json',$data);
        $data['redirect'] = "site/leadpenetrationnew";
        $this->load->view('redirect', $data);
    }

    function uploadDnc() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['page'] = "uploadDnc";
        $data['title'] = "Dnc upload";
        $this->load->view('template', $data);
    }

    function uploadDncSubmit() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $uploadPath = $this->config->item('uploadPath');
        $config['upload_path'] = $uploadPath . '/csv/';
        $config['allowed_types'] = '*';
        $config['max_size'] = 1024 * 100;
        $config['encrypt_name'] = TRUE;
        $file_element_name = 'csv';

        $this->load->library('upload', $config);
        $csvfile = "";
        if ($this->upload->do_upload($file_element_name)) {

            $uploaddata = $this->upload->data();
            $csvfile = $uploaddata['full_path'];
        }
        echo $this->upload->display_errors();

        $this->load->library('csvreader');

        $filePath = 'uploads/csv/' . $csvfile;
        $csvData = $this->csvreader->parse_file($csvfile);
        $uploadDnc = $this->dnc_model->uploadDnc($csvData);
        if ($uploadDnc->OUTPUT == "TRUE") {

            $data['alertsuccess'] = "DNC UPLOADED SUCCESSFULLY";
            $data['redirect'] = "site/viewdnc";
            $this->load->view("redirect", $data);
        } else {
            $data['alerterror'] = "ALL DNC NOT UPDATED";
            $data['invalidList'] = $uploadDnc->invalidList;
            $data['page'] = "uploadDnc";
            $data['title'] = "Dnc upload";
            $this->load->view('template', $data);
        }
    }

    public function downloadDncSample() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=dncSample.csv');

        // create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');

        $data = array(
            array(
                'phoneNumber' => 'phoneNumber',
                'campaignId' => 'campaignId',
                'processId' => 'processId',
                'dncType' => 'dncType',
                'callType' => 'callType'
            ),
            array(
                'phoneNumber' => '9898989898',
                'campaignId' => '',
                'processId' => '',
                'dncType' => 'systemDnc',
                'callType' => 'outbound'
            ),
            array(
                'phoneNumber' => '9898989898',
                'campaignId' => '1',
                'processId' => '',
                'dncType' => 'campaignDnc',
                'callType' => 'outbound'
            ),
            array(
                'phoneNumber' => '9898989898',
                'campaignId' => '1',
                'processId' => '1',
                'dncType' => 'processDnc',
                'callType' => 'outbound'
            ),
            array(
                '' => '',
                '' => '',
                '' => '',
                '' => '',
                '' => ''
            ),
            array(
                'phoneNumber' => '9898989898',
                'campaignId' => '',
                'processId' => '',
                'dncType' => 'systemDnc',
                'callType' => 'inbound'
            ),
            array(
                'phoneNumber' => '9898989898',
                'campaignId' => '1',
                'processId' => '',
                'dncType' => 'campaignDnc',
                'callType' => 'inbound'
            ),
            array(
                'phoneNumber' => '9898989898',
                'campaignId' => '1',
                'processId' => '1',
                'dncType' => 'processDnc',
                'callType' => 'inbound'
            )
        );
        foreach ($data as $key => $value) {

            fputcsv($output, $value);
        }
    }

    function test($disposeId) {
        $this->lua_model->deleteMultilevelDispose($disposeId);
    }

    function updateAgentPerformance($lastDate) {
        $query = "DELETE FROM `agent_performance` where date(`date`) = " . date('Y-m-d', strtotime($date));
        $this->db->query($query);
        $allUserdata = $this->db->query("SELECT id, username, CONCAT(`firstname`, ' ',`lastname` ) as `full_name` FROM `user` WHERE `status` < 2 ")->result();
        $agentCsv = "";
        $agentUsername = array();
        $agentFullname = array();

        foreach ($allUserdata as $allUserdataKey => $allUserdataValue) {
            if ($agentCsv == "") {
                $agentCsv = $allUserdataValue->id;
            } else {
                $agentCsv = $agentCsv . "," . $allUserdataValue->id;
            }

            $agentUsername[$allUserdataValue->id] = $allUserdataValue->username;
            $agentFullname[$allUserdataValue->id] = $allUserdataValue->full_name;
        }
        $this->lua_model->setAgentPerformanceInMysql($agentCsv, $lastDate, $agentUsername, $agentFullname);
        echo date('Y-m-d', strtotime($date));
    }

    function viewleadbyleadsetAccessLevel() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->load->library("pagination");
        $config = array();

        if ($this->input->get_post('leadset')) {
            $newdata = array(
                'leadsetid' => $this->input->get_post('leadset')
            );
            $this->session->set_userdata($newdata);
            $data['leadsetid'] = $this->input->get_post('leadset');
            $config['page_query_string'] = FALSE;
            $config['base_url'] = site_url("site/viewleadbyleadsetAccessLevel");
        } else {

            $config['page_query_string'] = FALSE;
            $config['base_url'] = site_url("site/viewleadbyleadsetAccessLevel");
            $data['leadsetid'] = $this->session->userdata('leadsetid');
        }
        $config["total_rows"] = $this->lead_model->lead_count();
        $config["per_page"] = 50;
        $config["uri_segment"] = 3;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $data["table"] = $this->lead_model->leadInLeadset($config["per_page"], $page);
        $data["links"] = $this->pagination->create_links();
        $data['start'] = $page;
        $this->userStateChangeSet("View leadset");
        $data['page'] = 'viewleadbyleadsetAccessLevel';
        $data['title'] = 'View leadset';
        $this->load->view('template', $data);
    }

    function createleadAccessLevel() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("createlead");
        $leadset = $this->input->get_post('leadset');
        $data['editmode'] = "0";
        $data['leadset'] = 0;
        $data['jsonlimit'] = $this->lead_model->getjsonlimit();
        $data['leadJson'] = $this->lead_model->getLeadsetJson($leadset);
        $data['leadsets'] = $this->lead_model->getleadset();
        $data['page'] = 'createleadAccessLevel';
        $data['title'] = 'Create lead';
        $this->load->view('template', $data);
    }

    function createleadsubmitAccessLevel() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|valid_email');
        $this->form_validation->set_rules('phone', 'Phone', 'trim');
        $this->form_validation->set_rules('leadset', 'Leadset', 'trim');
        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $data['leadsets'] = $this->lead_model->getleadset();
            $data['jsonlimit'] = $this->lead_model->getjsonlimit();
            $data['page'] = 'createleadAccessLevel';
            $data['title'] = 'Create lead';
            $this->load->view('template', $data);
        } else {
            $name = $this->input->post('name');
            $email = $this->input->post('email');
            $phone = $this->input->post('phone');
            $leadset = $this->input->post('leadset');
            $jsoncolumn = $this->input->post('jsoncolumn');
            $jsonvalue = $this->input->post('jsonvalue');
            if ($this->lead_model->create($name, $email, $phone, $leadset, $jsoncolumn, $jsonvalue) == 0)
                $data['alerterror'] = "New lead could not be created.";
            else
                $data['alertsuccess'] = "lead created Successfully.";
            //$data['table']=$this->lead_model->viewlead();
            $data['redirect'] = "site/viewleadbyleadsetAccessLevel";
            $data['other'] = "leadset=" . $leadset;
            $this->load->view("redirect", $data);
        }
    }

    function editLeadAccessLevel() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("editlead");
        $data['editmode'] = "1";
        $data['before'] = $this->lead_model->beforeedit($this->input->get('id'));
        $data['jsonlimit'] = $this->lead_model->getjsonlimit();
        $data['leadset'] = $this->lead_model->getleadset();
        $data['page'] = 'editLeadAccessLevel';
        $data['title'] = 'Edit lead';
        $this->load->view('template', $data);
    }

    function editLeadAccessLevelsubmit() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|valid_email');
        $this->form_validation->set_rules('phone', 'Phone', 'trim');
        $this->form_validation->set_rules('leadset', 'Leadset', 'trim');
        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $data['leadset'] = $this->lead_model->getleadset();
            $data['jsonlimit'] = $this->lead_model->getjsonlimit();
            $data['before'] = $this->lead_model->beforeeditlead($this->input->post('id'));
            $data['page'] = 'editLeadAccessLevel';
            $data['title'] = 'Edit lead';
            $this->load->view('template', $data);
        } else {
            $id = $this->input->post('id');
            $name = $this->input->post('name');
            $email = $this->input->post('email');
            $phone = $this->input->post('phone');
            $leadset = $this->input->post('leadset');
            $jsoncolumn = $this->input->post('jsoncolumn');
            $jsonvalue = $this->input->post('jsonvalue');
            if ($this->lead_model->edit($id, $name, $email, $phone, $leadset, $jsoncolumn, $jsonvalue) == 0)
                $data['alerterror'] = "lead Editing was unsuccesful";
            else
                $data['alertsuccess'] = "lead edited Successfully.";
            //$data['table']=$this->lead_model->viewlead();
            $data['redirect'] = "site/viewleadbyleadsetAccessLevel";
            $data['other'] = "leadset=" . $leadset;
            $this->load->view("redirect2", $data);
            /* $data['page']='viewusers';
              $data['title']='View Users';
              $this->load->view('template',$data); */
          }
      }

      function deleteLeadAccessLevel() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("deletelead");
        $data['editmode'] = "0";
        $leadId = $this->input->get('id');
        $leadsetId = $this->input->get('leadsetId');
        $this->lead_model->deletelead($leadId, $leadsetId);
        $data['alertsuccess'] = "lead Deleted Successfully";
        $data['redirect'] = "site/viewleadbyleadsetAccessLevel";
        $data['other'] = "leadset=" . $this->input->get('leadset');
        $this->load->view("redirect2", $data);
    }

    function uploadLeadAccessLevel() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $data['leadsetid'] = $this->input->get_post('leadset');
        $data['page'] = 'uploadLeadAccessLevel';
        $data['title'] = 'Upload Lead';
        $this->load->view('template', $data);
    }

    function uploadLeadSubmitAccessLevel() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $leadset = $this->input->get_post("leadsetid");
        //uploading files
        // echo "leadset".$leadset;
        $config['upload_path'] = './uploads/csv/';
        $config['allowed_types'] = '*';
        $config['max_size'] = 1024 * 100;
        $config['encrypt_name'] = TRUE;

        $this->load->library('upload', $config);
        $file_element_name = 'csv';
        $csvfile = "";
        if ($this->upload->do_upload($file_element_name)) {
            $uploaddata = $this->upload->data();
            $csvfile = $uploaddata['full_path'];
        }
        echo $this->upload->display_errors();

        $this->load->library('csvreader');
        $filePath = 'uploads/csv/' . $csvfile;
        $removeduplicate = $this->input->post('removeduplicate');
        $csvData = $this->csvreader
        ->parse_file($csvfile);

        $uploadLeadData = $this->lead_model->uploadlead($csvData, $removeduplicate, $leadset);
        // print_r($uploadLeadData);
        if ($uploadLeadData->output == "FALSE")
            $data['alerterror'] = "Lead data Not Uploaded because <br/>" . $uploadLeadData->error;
        else
            $data['alertsuccess'] = $uploadLeadData->message;

        if ($uploadLeadData->notInsertedDataFlag == "TRUE") {
            // header("Location: ".site_url()."/site/viewleadbyleadset?leadset=$leadset");
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=Leadset.csv');

            // create a file pointer connected to the output stream
            $output = fopen('php://output', 'w');
            // fputcsv($output, $uploadLeadData->notInsertedData);
            foreach ($uploadLeadData->notInsertedData as $key => $value) {
                fputcsv($output, $value);
            }
        } else {
            $webAppPath = $this->config->item('webAppPath');


            if ($leadset == "") {
                $data['redirect'] = "site/viewLeadsetByAccessLevel";
                $this->load->view("redirect", $data);
            } else {
                $data['other'] = "leadset=" . $leadset;
                $data['redirect'] = "site/viewleadbyleadsetAccessLevel";
                $this->load->view("redirect2", $data);
            }
        }
    }

    function viewHolidays() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("viewHolidays");
        $holidayGroupId = $this->input->get_post('holidayGroupId');
        $holidayGroupName = $this->input->get_post('holidayGroupName');

        if ($holidayGroupId != "" && $holidayGroupName != "") {
            $data['table'] = $this->inbound_model->viewHolidays($holidayGroupId);
            $data['holidayGroupId'] = $holidayGroupId;
            $data['holidayGroupName'] = $holidayGroupName;
            $data['page'] = 'viewHolidays';
            $data['title'] = 'view Holiday';
            $this->load->view('template', $data);
        } else {

            $data['alerterror'] = "Holiday Group Name and Id Not Found";
            $data['redirect'] = "site/viewHolidayGroup";
            $this->load->view("redirect", $data);
        }
    }

    function createHoliday() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['holidayGroupId'] = $this->input->get_post('holidayGroupId');
        $data['holidayGroupName'] = $this->input->get_post('holidayGroupName');
        $data['oldFile'] = $this->inbound_model->getSoundFileList();
        $data['name'] = '';
        $data['holidayDate'] = '';
        $data['description'] = '';
        $data['oldFileName'] = '';

        $data['page'] = 'createHoliday';
        $data['title'] = 'create Holiday ';
        $this->load->view('template', $data);
    }

    function createHolidaySubmit() {

        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('status', 'status', 'required');
        $this->form_validation->set_rules('holidayDate', 'date', 'required');

        $data['holidayGroupId'] = $this->input->get_post('holidayGroupId');
        $data['holidayGroupName'] = $this->input->get_post('holidayGroupName');
        $data['name'] = $this->input->get_post('name');
        $data['holidayDate'] = $this->input->get_post('holidayDate');
        $data['description'] = $this->input->get_post('description');
        $data['oldFileName'] = $this->input->get_post('oldFileName');

        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $data['holidayGroupId'] = $this->input->get_post('holidayGroupId');
            $data['holidayGroupName'] = $this->input->get_post('holidayGroupName');
            $data['oldFile'] = $this->inbound_model->getSoundFileList();
            $data['page'] = 'createHoliday';
            $data['title'] = 'create Holiday';
            $this->load->view('template', $data);
        } else {

            if ($this->input->get_post('oldFileName') == "") {

                $config['upload_path'] = $this->config->item('uploadIvrPath');
                $config['allowed_types'] = '*';
                $config['max_size'] = '1024*1000';
                $ivrName = $_FILES['userfile']['name'];
                $extension = pathinfo($ivrName, PATHINFO_EXTENSION);
                $config['file_name'] = uniqid() . "." . $extension;
                $this->load->library('upload', $config);

                if (!$this->upload->do_upload('userfile')) {
                    $data['alerterror'] = $this->upload->display_errors();
                    // $holidayGroupId       = $this->input->get_post('holidayGroupId');
                    // $holidayGroupName     = $this->input->get_post('holidayGroupName');
                    // $data['redirect']     = "site/createHoliday";
                    // $data['other']        = "holidayGroupId=$holidayGroupId&holidayGroupName=$holidayGroupName";
                    // $this->load->view("redirect",$data);
                    $data['oldFile'] = $this->inbound_model->getSoundFileList();
                    $data['page'] = 'createHoliday';
                    $data['title'] = 'create Holiday';
                    $this->load->view('template', $data);
                } else {
                    $uploadedFileData = $this->upload->data();
                    $holidayDate = explode(" ", $this->input->get_post('holidayDate'));
                    $holidayName = $this->input->get_post('name');
                    $holidayFileName = $uploadedFileData['file_name'];
                    $holidayDescription = $this->input->get_post('description');
                    $startDate = date("Y-m-d", strtotime($holidayDate[0]));
                    $endDate = date("Y-m-d", strtotime($holidayDate[4]));
                    $startTime = date("H:i:s", strtotime($holidayDate[1] . " " . $holidayDate[2]));
                    $endTime = date("H:i:s", strtotime($holidayDate[5] . " " . $holidayDate[6]));
                    $holidayGroupId = $this->input->get_post('holidayGroupId');
                    $holidayGroupName = $this->input->get_post('holidayGroupName');
                    $holidayStatus = $this->input->get_post('status');
                    $data['createHolidayData'] = $this->inbound_model->createHolidaySubmit($holidayName, $holidayFileName, $holidayDescription, $startDate, $endDate, $startTime, $endTime, $holidayGroupId, $holidayGroupName, $holidayStatus);
                    // print_r($data);
                    if ($data['createHolidayData']->output == "FALSE") {

                        $data['alerterror'] = $data['createHolidayData']->ErrorMessage;
                        // $data['title']='create Holiday';
                        // $holidayGroupId       = $this->input->get_post('holidayGroupId');
                        // $holidayGroupName     = $this->input->get_post('holidayGroupName'); 
                        // $data['redirect']="site/createHoliday";
                        // $data['other']="holidayGroupId=$holidayGroupId&holidayGroupName=$holidayGroupName";
                        // $this->load->view("redirect",$data);
                        $data['oldFile'] = $this->inbound_model->getSoundFileList();
                        $data['page'] = 'createHoliday';
                        $data['title'] = 'create Holiday';
                        $this->load->view('template', $data);
                    } else {
                        $data['alertsuccess'] = "Holiday created successfully.";
                        $holidayGroupId = $this->input->get_post('holidayGroupId');
                        $holidayGroupName = $this->input->get_post('holidayGroupName');
                        $data['table'] = $this->inbound_model->viewHolidays($holidayGroupId);
                        $data['title'] = 'view Holiday';
                        $data['redirect'] = "site/viewHolidays";
                        $data['other'] = "holidayGroupId=$holidayGroupId&holidayGroupName=$holidayGroupName";
                        $this->load->view("redirect", $data);
                    }
                }
            } else {

                $holidayDate = explode(" ", $this->input->get_post('holidayDate'));
                $holidayName = $this->input->get_post('name');
                $holidayFileName = $this->input->get_post('oldFileName');
                $holidayDescription = $this->input->get_post('description');
                $startDate = date("Y-m-d", strtotime($holidayDate[0]));
                $endDate = date("Y-m-d", strtotime($holidayDate[4]));
                $startTime = date("H:i:s", strtotime($holidayDate[1] . " " . $holidayDate[2]));
                $endTime = date("H:i:s", strtotime($holidayDate[5] . " " . $holidayDate[6]));
                $holidayGroupId = $this->input->get_post('holidayGroupId');
                $holidayGroupName = $this->input->get_post('holidayGroupName');
                $holidayStatus = $this->input->get_post('status');
                $data['createHolidayData'] = $this->inbound_model->createHolidaySubmit($holidayName, $holidayFileName, $holidayDescription, $startDate, $endDate, $startTime, $endTime, $holidayGroupId, $holidayGroupName, $holidayStatus);
                // print_r($data);
                if ($data['createHolidayData']->output == "FALSE") {

                    $data['alerterror'] = $data['createHolidayData']->ErrorMessage;
                    // $data['title']='create Holiday';
                    // $data['redirect']="site/createHoliday";
                    // $data['other']="holidayGroupId=$holidayGroupId&holidayGroupName=$holidayGroupName";
                    // $this->load->view("redirect",$data);
                    $data['oldFile'] = $this->inbound_model->getSoundFileList();
                    $data['page'] = 'createHoliday';
                    $data['title'] = 'create Holiday';
                    $this->load->view('template', $data);
                } else {
                    $data['alertsuccess'] = "Holiday created successfully.";
                    $holidayGroupId = $this->input->get_post('holidayGroupId');
                    $holidayGroupName = $this->input->get_post('holidayGroupName');
                    $data['table'] = $this->inbound_model->viewHolidays($holidayGroupId);
                    $data['title'] = 'view Holiday';
                    $data['redirect'] = "site/viewHolidays";
                    $data['other'] = "holidayGroupId=$holidayGroupId&holidayGroupName=$holidayGroupName";
                    $this->load->view("redirect", $data);
                }
            }
        }
    }

    function editHoliday() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['holidayId'] = $this->input->get_post('holidayId');
        $data['holidayName'] = $this->input->get_post('holidayName');
        $data['oldFile'] = $this->inbound_model->getSoundFileList();
        $data['before'] = $this->inbound_model->beforeeditHoliday($this->input->get_post('holidayId'));
        if ($data['before']->output == "FALSE") {
            $data['table'] = $this->inbound_model->viewHolidays($holidayId);
            $data['holidayId'] = $holidayId;
            $data['holidayName'] = $holidayName;
            $data['page'] = 'viewHolidays';
            $data['title'] = 'view Holiday';
            $this->load->view('template', $data);
        } else {
            $data['page'] = 'editHoliday';
            $data['title'] = 'create Holiday ';
            $this->load->view('template', $data);
        }
    }

    function editHolidaySubmit() {

        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('status', 'status', 'required');
        $this->form_validation->set_rules('holidayDate', 'date', 'required');
        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $data['holidayId'] = $this->input->get_post('holidayId');
            $data['holidayName'] = $this->input->get_post('holidayName');
            $data['before'] = $this->inbound_model->beforeeditHoliday($this->input->get_post('holidayId'));
            if ($data['before']->output == "FALSE") {
                $data['table'] = $this->inbound_model->viewHolidays($holidayId);
                $data['holidayId'] = $holidayId;
                $data['holidayName'] = $holidayName;
                $data['page'] = 'viewHolidays';
                $data['title'] = 'view Holiday';
                $this->load->view('template', $data);
            } else {
                $data['page'] = 'editHoliday';
                $data['title'] = 'edit Holiday ';
                $this->load->view('template', $data);
            }
        } else {

            if ($this->input->get_post('oldFileName') != "") {

                $holidayDate = explode(" ", $this->input->get_post('holidayDate'));
                $holidayId = $this->input->get_post('holidayId');
                $holidayName = $this->input->get_post('name');
                $holidayFileName = $this->input->get_post('oldFileName');
                $holidayDescription = $this->input->get_post('description');
                $startDate = date("Y-m-d", strtotime($holidayDate[0]));
                $endDate = date("Y-m-d", strtotime($holidayDate[4]));
                $startTime = date("H:i:s", strtotime($holidayDate[1] . " " . $holidayDate[2]));
                $endTime = date("H:i:s", strtotime($holidayDate[5] . " " . $holidayDate[6]));
                $holidayGroupId = $this->input->get_post('holidayGroupId');
                $holidayGroupName = $this->input->get_post('holidayGroupName');
                $holidayStatus = $this->input->get_post('status');
                $data['createHolidayData'] = $this->inbound_model->editHolidaySubmit($holidayId, $holidayName, $holidayFileName, $holidayDescription, $startDate, $endDate, $startTime, $endTime, $holidayGroupId, $holidayGroupName, $holidayStatus);
                // print_r($data);
                if ($data['createHolidayData']->output == "FALSE") {

                    $data['alerterror'] = $data['createHolidayData']->ErrorMessage;
                    $data['title'] = 'edit Holiday';
                    $data['redirect'] = "site/editHoliday";
                    $data['other'] = "holidayId=$holidayId&holidayName=$holidayName";
                    $this->load->view("redirect", $data);
                } else {
                    $data['alertsuccess'] = "Holiday updated successfully.";
                    $holidayId = $this->input->get_post('holidayId');
                    $holidayName = $this->input->get_post('holidayName');
                    $data['table'] = $this->inbound_model->viewHolidays($holidayId);
                    $data['title'] = 'view Holiday';
                    $data['redirect'] = "site/viewHolidays";
                    $data['other'] = "holidayGroupId=$holidayGroupId&holidayGroupName=$holidayGroupName";
                    $this->load->view("redirect", $data);
                }
            } else {

                $config['upload_path'] = $this->config->item('uploadIvrPath');
                $config['allowed_types'] = '*';
                $config['max_size'] = '1024*1000';
                $this->load->library('upload', $config);

                if (!$this->upload->do_upload('userfile')) {
                    $data['alerterror'] = $this->upload->display_errors();
                    $holidayId = $this->input->get_post('holidayId');
                    $data['redirect'] = "site/editHoliday";
                    $data['other'] = "holidayId=$holidayId";
                    $this->load->view("redirect", $data);
                } else {
                    $uploadedFileData = $this->upload->data();
                    $holidayDate = explode(" ", $this->input->get_post('holidayDate'));
                    $holidayName = $this->input->get_post('name');
                    $holidayFileName = $uploadedFileData['file_name'];
                    $holidayDescription = $this->input->get_post('description');
                    $startDate = date("Y-m-d", strtotime($holidayDate[0]));
                    $endDate = date("Y-m-d", strtotime($holidayDate[4]));
                    $startTime = date("H:i:s", strtotime($holidayDate[1] . " " . $holidayDate[2]));
                    $endTime = date("H:i:s", strtotime($holidayDate[5] . " " . $holidayDate[6]));
                    $holidayGroupId = $this->input->get_post('holidayGroupId');
                    $holidayGroupName = $this->input->get_post('holidayGroupName');
                    $holidayStatus = $this->input->get_post('status');
                    $oldFileName = $this->input->get_post('oldFileName');
                    $data['createHolidayData'] = $this->inbound_model->editHolidaySubmit($holidayName, $holidayFileName, $holidayDescription, $startDate, $endDate, $startTime, $endTime, $holidayGroupId, $holidayGroupName, $holidayStatus);
                    // print_r($data);
                    if ($data['createHolidayData']->output == "FALSE") {

                        $data['alerterror'] = $data['createHolidayData']->ErrorMessage;
                        $data['title'] = 'edit Holiday';
                        $data['redirect'] = "site/editHoliday";
                        $data['other'] = "holidayId=$holidayId";
                        $this->load->view("redirect", $data);
                    } else {
                        $data['alertsuccess'] = "Holiday updated successfully.";
                        $holidayId = $this->input->get_post('holidayId');
                        $holidayGroupId = $this->input->get_post('holidayGroupId');
                        $holidayGroupName = $this->input->get_post('holidayGroupName');
                        $data['table'] = $this->inbound_model->viewHolidays($holidayId);
                        $data['title'] = 'view Holiday';
                        $data['redirect'] = "site/viewHolidays";
                        $data['other'] = "holidayGroupId=$holidayGroupId&holidayGroupName=$holidayGroupName";
                        $this->load->view("redirect", $data);
                    }
                }
            }
        }
    }

    function deleteHoliday() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $holidayId = $this->input->get_post('holidayId');
        $holidayGroupId = $this->input->get_post('holidayGroupId');
        $holidayGroupName = $this->input->get_post('holidayGroupName');
        $data['deleteHolidayGroup'] = $this->inbound_model->deleteHoliday($holidayId, $holidayGroupId, $holidayGroupName);
        if ($data['deleteHolidayGroup']->output == "FALSE")
            $data['alerterror'] = "Holiday Cann't Delete. <br/>" . $data['editHolidayGroupData']->ErrorMessage;
        else
            $data['alertsuccess'] = "Holiday Deleted Successfully.";

        $data['redirect'] = "site/viewHolidays";
        $data['other'] = "holidayGroupId=$holidayGroupId&holidayGroupName=$holidayGroupName";
        $this->load->view("redirect", $data);
    }

    function viewHolidayGroup() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("viewHolidayGroup");
        $data['table'] = $this->inbound_model->viewHolidayGroup();
        $data['page'] = 'viewHolidayGroup';
        $data['title'] = 'view Holiday Group';
        $this->load->view('template', $data);
    }

    function createHolidayGroup() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['page'] = 'createHolidayGroup';
        $data['title'] = 'create Holiday Group';
        $this->load->view('template', $data);
    }

    function createHolidayGroupSubmit() {
        $this->form_validation->set_rules('name', 'Name', 'trim|required|is_unique[holiday_group.holiday_group_name]');
        $this->form_validation->set_rules('status', 'status', 'required');
        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $data['page'] = 'createHolidayGroup';
            $data['title'] = 'create Holiday Group';
            $this->load->view('template', $data);
        } else {
            $holidayGroupName = $this->input->get_post('name');
            $holidayGroupDescription = $this->input->get_post('description');
            $holidayGroupStatus = $this->input->get_post('status');
            $data['createHolidayData'] = $this->inbound_model->createHolidayGroupSubmit($holidayGroupName, $holidayGroupDescription, $holidayGroupStatus);
            if ($data['createHolidayData']->output == "FALSE")
                $data['alerterror'] = "New Holiday Group Cann't Create<br/>" . $data['createHolidayData']->ErrorMessage;
            else
                $data['alertsuccess'] = "New Holiday Group created Successfully.";
            $data['redirect'] = "site/viewHolidayGroup";
            $this->load->view("redirect", $data);
        }
    }

    function editHolidayGroup() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $holidayGroupId = $this->input->get_post('holidayGroupId');
        $data['before'] = $this->inbound_model->beforeedit($holidayGroupId);
        $data['page'] = 'editHolidayGroup';
        $data['title'] = 'create Holiday Group';
        $this->load->view('template', $data);
    }

    function editHolidayGroupSubmit() {
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('status', 'status', 'required');
        if ($this->form_validation->run() == FALSE) {
            $data['alerterror'] = validation_errors();
            $data['page'] = 'editHolidayGroup';
            $data['title'] = 'edit Holiday Group';
            $this->load->view('template', $data);
        } else {
            $holidayGroupId = $this->input->get_post('id');
            $holidayGroupName = $this->input->get_post('name');
            $holidayGroupDescription = $this->input->get_post('description');
            $holidayGroupStatus = $this->input->get_post('status');
            $data['editHolidayGroupData'] = $this->inbound_model->editHolidayGroupSubmit($holidayGroupId, $holidayGroupName, $holidayGroupDescription, $holidayGroupStatus);
            if ($data['editHolidayGroupData']->output == "FALSE") {
                $data['alerterror'] = "Holiday Group Cann't Update<br/>" . $data['editHolidayGroupData']->ErrorMessage;
                $data['before'] = $this->inbound_model->beforeedit($holidayGroupId);
                $data['page'] = 'editHolidayGroup';
                $data['title'] = 'edit Holiday Group';
                $this->load->view('template', $data);
            } else {
                $data['alertsuccess'] = "Holiday Group Updated Successfully.";
                $data['redirect'] = "site/viewHolidayGroup";
                $this->load->view("redirect", $data);
            }
        }
    }

    function deleteHolidayGroup() {
        $holidayGroupId = $this->input->get_post('holidayGroupId');
        $data['deleteHolidayGroup'] = $this->inbound_model->deleteHolidayGroup($holidayGroupId);
        // print_r($data['deleteHolidayGroup']);
        if ($data['deleteHolidayGroup']->output == "FALSE")
            $data['alerterror'] = "Holiday Group Cann't Delete. <br/>" . $data['deleteHolidayGroup']->ErrorMessage;
        else
            $data['alertsuccess'] = "Holiday Group Deleted Successfully.";
        $data['redirect'] = "site/viewHolidayGroup";
        $this->load->view("redirect", $data);
    }

    function viewVipList() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['table'] = $this->vipnumber_model->getVipList();
        $this->userStateChangeSet("View Vip List");
        $data['page'] = 'viewVipList';
        $data['title'] = 'view Vip List';
        $this->load->view('template', $data);
    }

    function createVipList() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("View Vip List");
        $data['vipListName'] = $this->vipnumber_model->getVipListName();
        $data['page'] = 'createVipList';
        $data['title'] = 'Create Vip List';
        $this->load->view('template', $data);
    }

    function viewVipNumber() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['table'] = $this->vipnumber_model->getVipListNumbers();
        $this->userStateChangeSet("View Vip List");
        $data['page'] = 'viewVipList';
        $data['title'] = 'view Vip List';
        $this->load->view('template', $data);
    }

    function uploadVipNumbers() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $this->userStateChangeSet("upload Vip Number");
        $data['leadsetId'] = $leadsetId = $this->input->get_post("leadset");
        $data['vipListDetail'] = $this->vipnumber_model->getVipListDetail($leadsetId);
        $data['page'] = "uploadVipNumbers";
        $data['title'] = "Upload VIP Numbers";
        $this->load->view('template', $data);
    }

    function uploadVipNumberSubmit() {

        $data['editmode'] = "0";
        $uploadPath = $this->config->item('uploadPath');
        $config['upload_path'] = $uploadPath . '/csv/';
        $config['allowed_types'] = '*';
        $config['max_size'] = 1024 * 100;
        $config['encrypt_name'] = TRUE;
        $file_element_name = 'csv';
        $vipListId = $this->input->get_post('vipListId');
        $vipListName = $this->input->get_post('vipListName');

        $this->load->library('upload', $config);
        $csvfile = "";
        if ($this->upload->do_upload($file_element_name)) {

            $uploaddata = $this->upload->data();
            $csvfile = $uploaddata['full_path'];
        }
        echo $this->upload->display_errors();

        $this->load->library('csvreader');

        $filePath = 'uploads/csv/' . $csvfile;
        $csvData = $this->csvreader->parse_file($csvfile);
        $uploadVipNumber = $this->vipnumber_model->uploadVipNumber($csvData, $vipListId, $vipListName);
        if ($uploadVipNumber->OUTPUT == "TRUE") {

            $data['alertsuccess'] = "VIP NUMBER UPLOADED SUCCESSFULLY";
            $data['redirect'] = "form/viewVipLeadsByVipList";
            $data['other'] = "leadset=" . $this->input->get($vipListId);
            $this->load->view("redirect2", $data);
        } else {
            $data['alerterror'] = "ALL VIP LIST NOT UPDATED";
            $data['invalidList'] = $uploadVipNumber->invalidList;
            $data['page'] = "uploadVipNumbers";
            $data['title'] = "Upload VIP Numbers";
            $this->load->view('template', $data);
        }
    }

    public function downloadVipNumberSample() {

        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=vipSample.csv');

        // create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');

        $data = array(
            array(
                'phoneNumber' => 'phoneNumber'
            ),
            array(
                'phoneNumber' => '989XXXXXXX'
            ),
            array(
                'phoneNumber' => '989XXXXXXX'
            )
        );
        foreach ($data as $key => $value) {

            fputcsv($output, $value);
        }
    }

    function agentDisposeLogFix() {

        $selectedCampaign = $this->input->get_post('selectedCampaign');
        $selectedProcess = $this->input->get_post('selectedProcess');
        $startDate = $this->input->get_post('startDate');
        $endDate = $this->input->get_post('endDate');

        $reportOpt = $this->download_model->agentDisposeLogFix($selectedCampaign, $selectedProcess, $startDate, $endDate);
        redirect($reportOpt);
    }

    function approachedEarlier() {
        $inbound_drop_cdr_id = $this->input->get_post('id');
        $data['result'] = $this->inbound_model->setApproached($inbound_drop_cdr_id);
        exit;
    }

    function crmloginconf() {
        $data['menuDetail'] = $this->getMenuData();
        $data['campaignData'] = $this->campaign_model->getCrmLoginConfDetails();
        //echo "<pre>";print_r($data['campaignData']);exit;
        //$result = $this->campaign_model->getCrmLoginConfDetails($campid);
//      $data['crm_conf_data'] = array();
//      if(count($result) > 0 ){
//         $data['crm_conf_data'] = json_decode($result[0]->crm_login_conf,true);
//      }
        //echo "<pre>";print_r($data['crm_conf_data']);exit;
        //$data['campaignId'] = $campid;
        $data['page'] = "crmloginconf";
        $data['title'] = "Crm Login Api Configuration";
        $this->load->view('template', $data);
    }
    
    function agentqualityDownload() {

        $agent  = $this->input->post('agent');
        $campaign = $this->input->post('campaign');
        $process = $this->input->post('process');
        $date = $this->input->post('date');
        $dateto = $this->input->post('dateto');
        $dispose = $this->input->post('dispose');
        $dispose2 = $this->input->post('dispose2');
        $dispose3 = $this->input->post('dispose3');

        $filePath = $this->download_model->agentqualityDownload($agent, $campaign, $process, $date, $dateto, $dispose, 
            $dispose2, $dispose3);
        redirect($filePath);
    }

    public function logManagement() {
        $data['menuDetail'] = $this->getMenuData();
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $this->userStateChangeSet("logManage");
        $data['editmode'] = "0";
        $data['page'] = 'viewllogManagement';
        $data['title'] = 'Log Manage';
        $this->load->view('template', $data);
    }
    
    public function UserDownload() {
        $filePath = $this->download_model->UserDownload();
        redirect($filePath);
    }
    


    public function backendGatewayList() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
       // $data['table'] = $this->gateway_model->getGatewayDetail();
        $data['editmode'] = "0";
        $data['page'] = 'backendGatewayList1.php';
        $data['title'] = 'View backend Gateway Detail';
        $this->load->view('template', $data);
    }

    public function addBackendGateway() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $data['page'] = 'addBackendGateway1.php';
        $data['title'] = 'Add Gateway';
        $this->load->view('template', $data);
    }

    function validateInput($valueReceived){
        $valueReceived = trim($valueReceived);
        $valueReceived = stripslashes($valueReceived);
        $valueReceived = htmlspecialchars($valueReceived);
        return $valueReceived;
    }

    public function editBackendGateway() {
       // ini_set('display_errors', 1);
       // ini_set('display_startup_errors', 1);
       // error_reporting(E_ALL);


//set initial values
       $gatewayName = $gatewayIp = $gwRegister ="ThisIsInitialBlankValue";
       $domError = $elementErr = $gatewayNameErr = $gatewayIpErr = "ThisIsInitialBlankValue" ;
       $nameOfParamText1 =  $nameOfValText1 = $nameOfParamText2 = $nameOfValText2 = array("ThisIsInitialBlankValue"=>"ThisIsInitialBlankValue");
       $gatewayNameDuplicate = false;
       $gatewayIpDuplicate = false;

//purify data received


//main parameters

       $gatewayName = $this->validateInput($_POST['gatewayName']);
       $gatewayIp = $this->validateInput($_POST['gatewayIp']);
       $gwRegister = $this->validateInput($_POST['gwRegister']);

       //create backend  gateway
       if (isset($gatewayName) && isset($gatewayIp) && isset($gwRegister)){ 

        $dom = new DOMDocument();
        $dom->encoding = 'utf-8';
        $dom->xmlVersion = '1.0';
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->load('sip/gateways.xml') or die("unable to open gateway.xml<br>");

        //$gateways =   $dom->getElementsByTagName("gateways")[0];
        $gateways =   $dom->getElementsByTagName("gateways")->item(0);


     //check for duplicate gateway Name
        $gatewayNameArray = $dom->getElementsByTagName('gateway');

        //echo '<pre>';var_dump($dom);exit;
        if($gatewayNameArray->length > 0){
            foreach ($gatewayNameArray as $values){
            //echo $values->getAttribute('name').'<br>';
                if ($gatewayName == $values->getAttribute('name')){
                //global $gatewayNameDuplicate;
                    $gatewayNameDuplicate = true;
                }
            }
        }

                      //check for duplicate gateway IP
        $gatewayIpArray = $dom->getElementsByTagName('param');
        if($gatewayIpArray->length > 0){
            foreach ($gatewayIpArray as $values){
           // echo $values->getAttribute('value').'<br>';
                if ($gatewayIp == $values->getAttribute('value')){
                  $gatewayIpDuplicate = true;
              }
          }
      }
       // echo "<pre>";
       // echo ":::gatewayNameDuplicate :::".$gatewayNameDuplicate ;
       // echo ":::gatewayIpDuplicate :::".$gatewayIpDuplicate ;
       // echo "</pre>";
       //exit;
      if($gatewayNameDuplicate == false &&  $gatewayIpDuplicate == false){

        //set gateway Name ex: priGateway1
        $gateway = $dom ->createElement('gateway');
        $gatewayAttr  = new DOMAttr('name', $gatewayName);
        $gateway->setAttributeNode($gatewayAttr);

       //set ip of Gateway
        $paramProxyIp = $dom->createElement('param');   
        $paramProxyIpAttr1 = new DOMAttr('name','proxy');
        $paramProxyIp->setAttributeNode($paramProxyIpAttr1);
        $paramProxyIpAttr2 = new DOMAttr('value',$gatewayIp);
        $paramProxyIp->setAttributeNode($paramProxyIpAttr2);
        $gateway->appendChild($paramProxyIp);

        //set true or false registration
        $paramReg = $dom->createElement('param');
        $paramRegAttr1 = new DOMAttr('name','register');
        $paramReg->setAttributeNode($paramRegAttr1);
        $paramRegAttr2 = new DOMAttr('value',$gwRegister);
        $paramReg->setAttributeNode($paramRegAttr2);
        $gateway->appendChild($paramReg);



        if(!(empty($_POST['nameOfParamText1'])) && !(empty($_POST['nameOfValText1'])) )
        {
    // echo "<pre>";
    // print_r($_POST['nameOfParamText1']);
    // print_r($_POST['nameOfValText1']);
    // echo "</pre>";

            foreach($_POST['nameOfParamText1'] as $key=>$value ){
        // echo "<pre>";
        // echo "$key => $value";
        // echo "</pre>";
                $c = $dom->createElement('param');
                $d = new DOMAttr("name",$_POST['nameOfParamText1'][$key]);
                $c->setAttributeNode($d);         
                $e = new DOMAttr("value",$_POST['nameOfValText1'][$key]);
                $c->setAttributeNode($e);         
                $gateway->appendChild($c);

            }

        }

        $gateways->appendChild($gateway);

        $dom->save("sip/gateways.xml");
        $data['alertsuccess'] = 'gateway created successfully';
        $data['redirect'] = "site/backendGatewayList";
        $this->load->view("redirect", $data);

    }
    else{
     $data['alerterror'] = 'kindly set unique gateway name or gateway ip';
     $data['redirect'] = "site/addBackendGateway";
     $this->load->view("redirect", $data);
 }
}else{
   $data['alerterror'] = 'gateway veriables not found';
   $data['redirect'] = "site/addBackendGateway";
   $this->load->view("redirect", $data);
}
}
//end of  editBackendGateway function 


 public function editBackendGatewayList() {
        $data['userButtonSetting'] = $this->data['userButtonSetting'];
        $data['menuDetail'] = $this->getMenuData();
        $data['editmode'] = "0";
        $data['page'] = 'editBackendGatewayList.php';
        $data['title'] = 'Edit Backend Gateway List';
        $this->load->view('template', $data);
    }


public function updateBackendGateway(){
    print_r($_POST) ;
}



//    public function uploadUser() {
//        $data['userButtonSetting'] = $this->data['userButtonSetting'];
//        $data['menuDetail'] = $this->getMenuData();
//        $data['editmode'] = "0";
//        $data['page'] = 'uploadUser';
//        $data['title'] = 'Upload User';
//        $this->load->view('template', $data);
//    }


//end of site class
}
?>
