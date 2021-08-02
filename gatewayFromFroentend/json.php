<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Json extends CI_Controller {

    private $data;

    public function getdispose() {
        $data["message"] = $this->dispose_model->viewdisposebyprocess($this->input->get_post("process"));
        $this->load->view("json", $data);
    }

    public function getSelectedDispose() {
        $data["message"] = $this->dispose_model->selectedDispose($this->input->get_post("process"));
        $this->load->view("json", $data);
    }

    public function getAgentScript() {
        $data["message"] = $this->process_model->getAgentScript($this->input->get_post("process"));
        $this->load->view("json", $data);
    }

    public function getManageDispose() {
        $data["message"] = $this->dispose_model->getManageDispose($this->input->get_post("process"));
        $this->load->view("json", $data);
    }

    public function getQualityDisposeParameter() {

        $data["message"] = $this->dispose_model->getQualityDisposeParameter($this->input->get_post("processId"));
        $this->load->view("json", $data);
    }

    public function getQualityDispose() {

        $data["message"] = $this->dispose_model->getQualityDispose($this->input->get_post("process"));
        $this->load->view("json", $data);
    }

    public function getLeadDispose() {
        $accesslevel = $this->session->userdata('accesslevel');
        // print_r($accesslevel);
        if ($accesslevel == 5) {

            $data["message"] = $this->dispose_model->getQualityProcessDispose($this->input->get_post("process"));
        } else if ($accesslevel == 7 || $accesslevel == 8 || $accesslevel == 9) {

            $data["message"] = $this->dispose_model->getVerifierProcessDispose($this->input->get_post("process"));
        } else if ($accesslevel == 12) {

            $data["message"] = $this->dispose_model->getBackOfficeProcessDispose($this->input->get_post("process"));
        } else {

            $data["message"] = $this->dispose_model->getLeadProcessDispose($this->input->get_post("process"));
        }
        $this->load->view("json", $data);
    }

    public function getVerifierDispose() {
        $data["message"] = $this->dispose_model->getVerifierDispose($this->input->get_post("process"));
        $this->load->view("json", $data);
    }

    public function getBackOfficeDispose() {
        $data["message"] = $this->dispose_model->getBackOfficeDispose($this->input->get_post("process"));
        $this->load->view("json", $data);
    }

    public function setDefaultDispose() {
        $disposeId = $this->input->get_post("disposeId");
        $processId = $this->input->get_post("processId");
        $data["message"] = $this->dispose_model->setDefaultDispose($disposeId, $processId);
        $this->load->view("json", $data);
    }

    public function addDisposeToProcess() {
        $disposeId = $this->input->get_post("disposeId");
        $processId = $this->input->get_post("processId");
        $processName = $this->input->get_post("processName");
        $data["message"] = $this->dispose_model->addDisposeToProcess($disposeId, $processId, $processName);
        $this->load->view("json", $data);
    }

    public function removeDisposeToProcess() {
        $disposeId = $this->input->get_post("disposeId");
        $processId = $this->input->get_post("processId");
        $data["message"] = $this->dispose_model->removeDisposeToProcess($disposeId, $processId);
        $this->load->view("json", $data);
    }

    public function getcampaignid() {
        $processid = $this->input->get_post('value');

        $data['message'] = $this->db->query("SELECT `campaign` FROM `process` WHERE `id`=" . $processid)->result();
        $this->load->view('json', $data);
    }

    //campaign json function start

    public function getEnabledProcess() {
        $campaignId = $this->input->get_post("campaignId");
        $data["message"] = $this->process_model->getEnabledProcess($campaignId);
        $this->load->view('json', $data);
    }

    //campaign json function end
    // leadset json function start
    public function deleteLeadsetCheck() {
        $leadsetId = $this->input->get_post("leadsetId");
        $data["message"] = $this->lead_model->deleteLeadsetCheck($leadsetId);
        $this->load->view('json', $data);
    }

    // leadset json function end

    public function deleteUserCheck() {
        $userId = $this->input->get_post("userId");
        $data["message"] = $this->user_model->deleteUserCheck($userId);
        $this->load->view('json', $data);
    }

    public function getDashBoardBalance() {
        $data['message'] = $this->reporting_model->getDashBoardBalance();
        $this->load->view('json', $data);
    }

    public function dashboardFilterData() {
        $dashboardFilterData = new stdClass();
        $startDate = $this->input->get_post("startDate");
        $endDate = $this->input->get_post("endDate");
        $processType = $this->input->get_post("processType");

        $dashboardFilterData->disposeData = $this->user_model->superAgentdashboarddata($startDate, $endDate, $processType);
        $dashboardFilterData->callDetail = $this->reporting_model->dashboardFilterData($startDate, $endDate, $processType);
        $data['message'] = $dashboardFilterData;
        $this->load->view('json', $data);
    }

    public function getAgentName() {
        $agentId = $this->input->get_post('GetAgentId');
        $data['message'] = $this->db->query("SELECT `username` FROM `user` WHERE `id`=" . $agentId)->result();
        $this->load->view('json', $data);
    }

    public function getReadyUser() {
        $agentId = $this->input->get_post('agentId');
        $processId = $this->input->get_post('processId');
        $campaignId = $this->input->get_post('campaignId');
        $data['message'] = $this->user_model->getReadyUser($agentId, $processId, $campaignId);
        $this->load->view('json', $data);
    }

    public function getReadyUserConference() {
        $agentId = $this->input->get_post('agentId');
        $processId = $this->input->get_post('processId');
        $campaignId = $this->input->get_post('campaignId');
        $data['message'] = $this->user_model->getReadyUserConference($agentId, $processId, $campaignId);
        $this->load->view('json', $data);
    }

    public function getagent() {
        $data["message"] = $this->user_model->getreadyagents();
        $this->load->view("json", $data);
    }

    public function getleadbynumber() {
        $callnumber = $this->input->get_post('callnumber');
        $process = $this->input->get_post('process');
        $data['message'] = $this->lead_model->getleadbynumber($callnumber, $process);
        $this->load->view('json', $data);
    }

    public function getLeadDetailById() {
        $customerId = $this->input->get_post('customerId');
        $process = $this->input->get_post('process');
        $customerNumber = $this->input->get_post('customerNumber');
        $leadsetId = $this->input->get_post('leadsetId');
        $data['message'] = $this->lead_model->getLeadDetailById($customerId, $process, $customerNumber, $leadsetId);
        $this->load->view('json', $data);
    }

    public function getnextLeadlist() {
        $customerId = $this->input->get_post('customerId');
        $process = $this->input->get_post('process');
        $customerNumber = $this->input->get_post('customerNumber');
        $leadsetId = $this->input->get_post('leadsetId');
        $listInfo = $this->input->get_post('listInfo');
        $data['message'] = $this->lead_model->getnextLeadlist($customerId, $process, $customerNumber, $leadsetId, $listInfo);
        $this->load->view('json', $data);
    }

    public function getListLeadOnly() {
        $process = $this->input->get_post('process');
        $leadsetId = $this->input->get_post('leadsetId');
        $data['message'] = $this->lead_model->getListLead($process, $leadsetId);
        $this->load->view('json', $data);
    }

    public function getListLead() {
        $customerId = $this->input->get_post('customerId');
        $process = $this->input->get_post('process');
        $customerNumber = $this->input->get_post('customerNumber');
        $leadsetId = $this->input->get_post('leadsetId');
        $data['message'] = $this->lead_model->getListLeadDetail($customerId, $process, $customerNumber, $leadsetId);
        $this->load->view('json', $data);
    }

    public function getNextListLead() {
        $customerId = $this->input->get_post('customerId');
        $process = $this->input->get_post('process');
        $customerNumber = $this->input->get_post('customerNumber');
        $leadsetId = $this->input->get_post('leadsetId');
        $data['message'] = $this->lead_model->getNextListLead($customerId, $process, $customerNumber, $leadsetId);
        $this->load->view('json', $data);
    }

    public function savelistData() {
        $customerId = $this->input->get_post('customerId');
        $process = $this->input->get_post('process');
        $customerNumber = $this->input->get_post('customerNumber');
        $leadsetId = $this->input->get_post('leadsetId');
        $listInfo = $this->input->get_post('listInfo');
        $data['message'] = $this->lead_model->savelistData($customerId, $process, $customerNumber, $leadsetId, $listInfo);
        $this->load->view('json', $data);
    }

    public function getextension() {

        $data["message"] = $this->extension_model->extensionallocation($this->input->get_post('extension'), $this->input->get_post('siptype'));
        $this->load->view("json", $data);
    }

    public function getVideoExtensionAgent() {
        $data["message"] = $this->extension_model->getVideoExtensionAgent();
        $this->load->view("json", $data);
    }

    public function getVideoConfExtnAgent() {
        $data["message"] = $this->extension_model->getVideoConfExtnAgent();
        $this->load->view("json", $data);
    }

    public function setVideoExtensionAgent() {
        $extension = $this->input->get_post('extension');
        $data["message"] = $this->extension_model->setVideoExtensionAgent($extension);
        $this->load->view("json", $data);
    }

    public function setVideoExtensionWeb() {
        $extension = $this->input->get_post('extension');
        $data["message"] = $this->extension_model->setVideoExtensionWeb($extension);
        $this->load->view("json", $data);
    }

    public function setAudioExtensionWeb() {
        $extension = $this->input->get_post('extension');
        $data["message"] = $this->extension_model->setAudioExtensionWeb($extension);
        $this->load->view("json", $data);
    }

    public function setVideoConfExtnWeb() {
        $extension = $this->input->get_post('extension');
        $data["message"] = $this->extension_model->setVideoConfExtnWeb($extension);
        $this->load->view("json", $data);
    }

    public function setVideoConfExtnAgent() {
        $extension = $this->input->get_post('extension');
        $data["message"] = $this->extension_model->setVideoConfExtnAgent($extension);
        $this->load->view("json", $data);
    }

    public function checkVideoExtensionSet() {
        $TotalVideoExtn = $this->input->get_post('TotalVideoExtn');
        $AudioExtensionWeb = $this->input->get_post('AudioExtensionWeb');
        $VideoExtensionWeb = $this->input->get_post('VideoExtensionWeb');
        $VideoExtensionAgent = $this->input->get_post('VideoExtensionAgent');
        $VideoConfExtnWeb = $this->input->get_post('VideoConfExtnWeb');
        $VideoConfExtnAgent = $this->input->get_post('VideoConfExtnAgent');

        $data["message"] = $this->extension_model->checkVideoExtensionSet($TotalVideoExtn, $AudioExtensionWeb, $VideoExtensionWeb, $VideoExtensionAgent, $VideoConfExtnWeb, $VideoConfExtnAgent);
        $this->load->view("json", $data);
    }

    public function removeextension() {
        $usersip = $this->input->get_post('usersip');
        $sipmode = $this->input->get_post('sipmode');
        $data["message"] = $this->extension_model->removeextension($usersip, $sipmode);
        $this->load->view("json", $data);
    }

    public function changeuserstate() {
        $state = $this->input->get_post('state');

        $userId = $this->input->get_post('agentid');
        $userName = $this->input->get_post('agentusername');
        $fullName = $this->input->get_post('agentname');

        $pageName = $this->input->get_post('pageName');
        $ipAddress = $this->input->ip_address();
        $userState = $this->input->get_post('userState');
        $UserLastState = $this->input->get_post('UserLastState');

        $campaignId = $this->input->get_post('UserStausCampaignId');
        $campaignName = $this->input->get_post('UserStausCampaignName');
        $processId = $this->input->get_post('UserStausProcessId');
        $processName = $this->input->get_post('UserStausProcessName');
        $customerId = $this->input->get_post('UserStausCustomerId');
        $customerPhoneNumber = $this->input->get_post('UserStausCustomerPhoneNumber');
        $customerName = $this->input->get_post('UserStausCustomerName');
        $LeadsetId = $this->input->get_post('UserStausLeadsetId');
        $LeadsetName = $this->input->get_post('UserStausLeadsetName');
        $referenceUuid = $this->input->get_post('UserStausReferenceUuid');
        $customerUuid = $this->input->get_post('UserStausCustomerUuid');
        $modeOfCalling = $this->input->get_post('UserStausModeOfCalling');
        $userStateArg = $this->input->get_post('PauseName');
        $extension = $this->input->get_post('usersip');
        $sipmode = $this->input->get_post('sipmode');
        $disposeFlag = $this->input->get_post('disposeFlag');
        $Crudarray = $this->input->get_post('Crudarray');
        $callTime = $this->input->get_post('callTime');
        $holdTime = $this->input->get_post('holdTime');
        $muteTime = $this->input->get_post('muteTime');
        $transferTime = $this->input->get_post('transferTime');
        $conferenceTime = $this->input->get_post('conferenceTime');
        // echo  $extension;
        $data["message"] = $this->user_model->changestate($state, $processId, $userId, $userName, $fullName, $pageName, $ipAddress, $userState, $campaignId, $campaignName, $processId, $processName, $customerId, $customerPhoneNumber, $customerName, $referenceUuid, $customerUuid, $LeadsetId, $LeadsetName, $UserLastState, $modeOfCalling, $userStateArg, $extension, $sipmode, $disposeFlag, $Crudarray, "TRUE", $callTime, $holdTime, $muteTime, $transferTime, $conferenceTime);
        $this->load->view('json', $data);
    }

    public function changestateToReady() {
        $state = $this->input->get_post('state');

        $userId = $this->input->get_post('agentid');
        $userName = $this->input->get_post('agentusername');
        $fullName = $this->input->get_post('agentname');

        $pageName = $this->input->get_post('pageName');
        $ipAddress = $this->input->ip_address();
        $userState = $this->input->get_post('userState');
        $UserLastState = $this->input->get_post('UserLastState');

        $campaignId = $this->input->get_post('UserStausCampaignId');
        $campaignName = $this->input->get_post('UserStausCampaignName');
        $processId = $this->input->get_post('UserStausProcessId');
        $processName = $this->input->get_post('UserStausProcessName');
        $customerId = $this->input->get_post('UserStausCustomerId');
        $customerPhoneNumber = $this->input->get_post('UserStausCustomerPhoneNumber');
        $customerName = $this->input->get_post('UserStausCustomerName');
        $LeadsetId = $this->input->get_post('UserStausLeadsetId');
        $LeadsetName = $this->input->get_post('UserStausLeadsetName');
        $referenceUuid = $this->input->get_post('UserStausReferenceUuid');
        $customerUuid = $this->input->get_post('UserStausCustomerUuid');
        $modeOfCalling = $this->input->get_post('UserStausModeOfCalling');
        $userStateArg = $this->input->get_post('PauseName');
        $extension = $this->input->get_post('usersip');
        $sipmode = $this->input->get_post('sipmode');
        $disposeFlag = $this->input->get_post('disposeFlag');
        $Crudarray = $this->input->get_post('Crudarray');

        $data["message"] = $this->user_model->changestate($state, $processId, $userId, $userName, $fullName, $pageName, $ipAddress, $userState, $campaignId, $campaignName, $processId, $processName, $customerId, $customerPhoneNumber, $customerName, $referenceUuid, $customerUuid, $LeadsetId, $LeadsetName, $UserLastState, $modeOfCalling, $userStateArg, $extension, $sipmode, $disposeFlag, $Crudarray, "FALSE");
        $this->load->view('json', $data);
    }

    public function changestateToReadyForSingle() {

        $agentid = $this->input->get_post('agentid');
        $directProcessId = $this->input->get_post('directProcessId');
        $directProcessName = $this->input->get_post('directProcessName');
        $directProcessType = $this->input->get_post('directProcessType');
        $directCampaignId = $this->input->get_post('directCampaignId');
        $directCampaignName = $this->input->get_post('directCampaignName');

        $data["message"] = $this->lua_model->addSingleprocesshashlua($agentid, $directProcessId, $directProcessName, $directProcessType, $directCampaignId, $directCampaignName);
        $this->load->view('json', $data);
    }

    public function viewprofile() {
        $data['message'] = $this->user_model->viewprofile();
        $this->load->view('json', $data);
    }

    public function savelead() {
        $id = $this->input->get_post("id");
        $name = $this->input->get_post("name");
        $email = $this->input->get_post("email");
        $phone = $this->input->get_post("phone");
        $leadset = $this->input->get_post("leadset");
        $jsoncolumn = $this->input->get_post("jsoncolumn");
        $jsonvalue = $this->input->get_post("jsonvalue");
        if ($this->lead_model->savelead($id, $name, $email, $phone, $leadset, $jsoncolumn, $jsonvalue) == 0) {
            $data['message'] = "lead Editing was unsuccesful";
        } else {
            $data['message'] = "lead edited Successfully.";
        }

        $this->load->view('json', $data);
    }

    public function savelead2() {
        $id = $this->input->get_post("id");
        $name = $this->input->get_post("name");
        $email = $this->input->get_post("email");
        $phone = $this->input->get_post("phone");
        $leadset = $this->input->get_post("leadset");
        $jsoncolumn = $this->input->get_post("jsoncolumn");
        $jsonvalue = $this->input->get_post("jsonvalue");
        $callbackdate = date("Y-m-d", strtotime($this->input->get_post("callbackdate")));
        $callbacktime = date("H:i", strtotime($this->input->get_post("callbacktime")));
        $callbacktime = $callbacktime . ":00";
        $callbacktime = date("H:i:s", strtotime($callbacktime));
        $disposecallid = $this->input->get_post("disposecallid");
        if ($this->lead_model->savelead2($id, $name, $email, $phone, $leadset, $jsoncolumn, $jsonvalue, $callbackdate, $callbacktime, $disposecallid) == 0) {
            $data['message'] = "lead Editing was unsuccesful";
        } else {
            $data['message'] = "lead edited Successfully.";
        }

        $this->load->view('json', $data);
    }

    public function getuserprocess() {
        $data['message'] = $this->process_model->getuserprocess();
        $this->load->view('json', $data);
    }

    public function getProcessCounter($processid) {
        $data['message'] = $this->process_model->getProcessCounter($processid);
        $this->load->view('json', $data);
    }

    public function autoSendEmail() {

        $lead = $this->input->get_post("lead");
        $dispose = $this->input->get_post("dispose");
        $rfud = $this->input->get_post("rfud");
        $agentId = $this->input->get_post("agentId");
        $agentName = $this->input->get_post("agentName");
        $agentUsername = $this->input->get_post("agentUsername");
        $campaignId = $this->input->get_post("campaignId");
        $campaignName = $this->input->get_post("campaignName");
        $processId = $this->input->get_post("processId");
        $processName = $this->input->get_post("processName");
        $clientEmail = $this->input->get_post("clientEmail");
        $crmId = $this->input->get_post("crmId");
        $SignatureUrl = '';
        $data['message'] = $this->dispose_model->autoSendEmail($processId, $campaignId, $campaignName, $rfud, $processName, $lead, $agentUsername, $agentId, $agentName, $SignatureUrl, $dispose, $clientEmail, $crmId);
        $this->load->view('json', $data);
    }

    public function autoSendSms() {

        $messageNumber = $this->input->get_post("messageNumber");
        $dispose = $this->input->get_post("dispose");
        $campaignId = $this->input->get_post("campaignId");
        $campaignName = $this->input->get_post("campaignName");
        $rfud = $this->input->get_post("rfud");
        $processId = $this->input->get_post("processId");
        $processName = $this->input->get_post("processName");
        $customerId = $this->input->get_post("customerId");
        $agentUsername = $this->input->get_post("agentUsername");
        $agentid = $this->input->get_post("agentid");
        $agentName = $this->input->get_post("agentName");
        $crmId = $this->input->get_post("crmId");
        $data['message'] = $this->dispose_model->autoSendSms($messageNumber, $dispose, $campaignId, $campaignName, $rfud, $processId, $processName, $customerId, $agentUsername, $agentid, $agentName, $crmId);
        $this->load->view('json', $data);
    }

    public function savedispose() {

        $callbackdate = $this->input->get_post("callbackdate");
        $callbacktime = $this->input->get_post("callbacktime");
        $disposecomment = $this->input->get_post("disposecomment");
        $disposecomment = str_replace("'", "&rsquo;", $disposecomment);
        $number = $this->input->get_post("number");
        $lead = $this->input->get_post("lead");
        $dispose = $this->input->get_post("dispose");
        $disposecallid = $this->input->get_post("disposecallid");
        $usersip = $this->input->get_post("usersip");
        $rfud = $this->input->get_post("rfud");
        $disposeName = $this->input->get_post("disposeName");
        $agentId = $this->input->get_post("agentId");
        $agentName = $this->input->get_post("agentName");
        $agentUsername = $this->input->get_post("agentUsername");
        $campaignId = $this->input->get_post("campaignId");
        $campaignName = $this->input->get_post("campaignName");
        $processId = $this->input->get_post("processId");
        $processName = $this->input->get_post("processName");
        $leadsetId = $this->input->get_post("leadsetId");
        $leadsetName = $this->input->get_post("leadsetName");
        $moc = $this->input->get_post("moc");
        $onCallTime = $this->input->get_post("onCallTime");
        $disconnectedBy = $this->input->get_post("disconnectedBy");
        $customerName = $this->input->get_post("customer_name");
        $leadJson = $this->input->get_post("leadJson");
        $processJson = $this->input->get_post("processJson");
        $dndType = $this->input->get_post("dndType");
        $Qete = $this->input->get_post("Qete");
        $holdSec = $this->input->get_post("holdSec");
        $muteSec = $this->input->get_post("muteSec");
        $transferTime = $this->input->get_post("transferTime");
        $conferenceTime = $this->input->get_post("conferenceTime");
        $emailAutoSendFlag = $this->input->get_post("emailAutoSendFlag");
        $clientEmail = $this->input->get_post("clientEmail");
        $data['message'] = $this->dispose_model->savedisposecall($dispose, $callbackdate, $callbacktime, $disposecomment, $number, $lead, $disposecallid, $rfud, $disposeName, $agentId, $agentName, $agentUsername, $campaignId, $campaignName, $processId, $processName, $leadsetId, $leadsetName, $moc, $onCallTime, $disconnectedBy, $customerName, $leadJson, $processJson, $dndType, $Qete, $holdSec, $muteSec, $transferTime, $conferenceTime, $emailAutoSendFlag, $clientEmail);

        $this->load->view('json', $data);
    }

    // function getcomment()
    // {
    //  $data['message']=$this->dispose_model->getcomment();
    //  $this->load->view( 'json', $data );
    // }
    public function savecdrcomment() {
        $referenceUuid = $this->input->get_post("referenceUuid");
        $listenerUserId = $this->input->get_post("listenerUserId");
        $listenerUserName = $this->input->get_post("listenerUserName");
        $accesslevel = $this->input->get_post("accesslevel");
        $leadId = $this->input->get_post("custId");
        $customerName = $this->input->get_post("customerName");
        $phoneNumber = $this->input->get_post("phoneNumber");
        $campaignId = $this->input->get_post("campaignId");
        $campaignName = $this->input->get_post("campaignName");
        $processId = $this->input->get_post("processId");
        $processName = $this->input->get_post("processName");
        $disposeId = $this->input->get_post("disposeId");
        $disposeName = $this->input->get_post("disposeName");
        $recordListen = 1;
        $rating = $this->input->get_post("rating");
        $comment = $this->input->get_post("commentText");
        $timestamp = $this->input->get_post("timestamp");
        $listenerComment = $this->input->get_post("listenerComment");
        $listenerUserFullName = $this->input->get_post("listenerUserFullName");
        $listenerAccesslevel = $this->input->get_post("listenerAccesslevel");
        $listenerAccesslevelText = $this->input->get_post("listenerAccesslevelText");
        $listenerDatetime = $this->input->get_post("listenerDatetime");
        $listenerDateString = $this->input->get_post("listenerDateString");
        $listenerEpoch = $this->input->get_post("listenerEpoch");
        $userId = $this->input->get_post("userId");
        $userFullname = $this->input->get_post("userFullname");
        $firstDisposeId = $this->input->get_post("firstDisposeId");
        $firstDisposeName = $this->input->get_post("firstDisposeName");
        $secondDisposeId = $this->input->get_post("secondDisposeId");
        $secondDisposeName = $this->input->get_post("secondDisposeName");
        $thirdDisposeId = $this->input->get_post("thirdDisposeId");
        $thirdDisposeName = $this->input->get_post("thirdDisposeName");
        $customerEmail = $this->input->get_post("customerEmail");
        $leadJson = $this->input->get_post("leadJson");
        $leadsetId = $this->input->get_post("leadsetId");
        $leadsetName = $this->input->get_post("leadsetName");
        $alternateNumberFlag = $this->input->get_post("alternateNumberFlag");
        $didNumber = $this->input->get_post("didNumber");
        $callback = $this->input->get_post("callback");
        $callcount = $this->input->get_post("callcount");
        $agentTalktimeSec = $this->input->get_post("agentTalktimeSec");
        $muteTime = $this->input->get_post("muteTime");
        $holdTime = $this->input->get_post("holdTime");
        $transferTime = $this->input->get_post("transferTime");
        $conferenceTime = $this->input->get_post("conferenceTime");
        $inQueueTime = $this->input->get_post("inQueueTime");
        $modeOfCalling = $this->input->get_post("modeOfCalling");
        $disconnectedBy = $this->input->get_post("disconnectedBy");
        $startStamp = $this->input->get_post("startStamp");
        $observationComment = $this->input->get_post("observationComment");
        $issue = $this->input->get_post("issue");
        $resolutionProvided = $this->input->get_post("resolutionProvided");
        $strengths = $this->input->get_post("strengths");
        $actiontkn = $this->input->get_post("actiontkn");
        $custnotes = $this->input->get_post("custnotes");
        $recordingLink = $this->input->get_post("recordingLink");

        $data['message'] = $this->reporting_model->savecdrcomment($referenceUuid, $listenerUserId, $listenerUserName, $accesslevel, $leadId, $customerName, $phoneNumber, $campaignId, $campaignName, $processId, $processName, $disposeId, $disposeName, $recordListen, $rating, $comment, $timestamp, $listenerComment, $listenerUserFullName, $listenerAccesslevel, $listenerAccesslevelText, $listenerDatetime, $listenerDateString, $listenerEpoch, $userId, $userFullname, $firstDisposeId, $firstDisposeName, $secondDisposeId, $secondDisposeName, $thirdDisposeId, $thirdDisposeName, $customerEmail, $leadJson, $leadsetId, $leadsetName, $alternateNumberFlag, $didNumber, $callback, $callcount, $agentTalktimeSec, $muteTime, $holdTime, $transferTime, $conferenceTime, $inQueueTime, $modeOfCalling, $disconnectedBy, $startStamp, $observationComment, $issue, $resolutionProvided, $strengths, $actiontkn, $custnotes);

        $this->load->view('json', $data);
    }

    public function downloadaudio() {
        $baseUrl = $this->config->item('base_url');
        $this->load->helper('download');
        $uuid = $this->input->get_post("uuid");
        $url = "$baseUrl/dumprecords/" . $uuid . "wav";
        force_download($url);
        $data['message'] = "";
        $this->load->view('json', $data);
    }

    public function getuserleads() {
        $agentId = $this->input->get_post('agentid');
        $directProcessId = $this->input->get_post('directProcessId');
        $data['message'] = $this->preview_model->getuserlead($agentId, $directProcessId);
        //$data['message']=$this->process_model->getuserleads($this->input->get_post('cur_lead'));
        $this->load->view('json', $data);
    }

    public function saveuserleads() {
        $processid = $this->input->get_post('processid');
        $lead_id = $this->input->get_post('lead_id');
        $phone = $this->input->get_post('phone');

        $data['message'] = $this->preview_model->saveuserlead($processid, $lead_id, $phone);
        $this->load->view('json', $data);
    }

    public function getleadcomment() {
        $leadid = $this->input->get_post('leadid');
        $process = $this->input->get_post('process');
        $data['message'] = $this->process_model->getleadcomment($leadid, $process);
        $this->load->view('json', $data);
    }

    public function getprevlead() {
        $data['message'] = $this->process_model->getprevlead($this->input->get_post('cur_lead'));
        $this->load->view('json', $data);
    }

    public function getnextlead() {
        $leadId = $this->input->get_post('previewLeadId');
        $leadsetId = $this->input->get_post('previewLeadsetId');
        $leadsetName = $this->input->get_post('previewLeadsetName');
        $processId = $this->input->get_post('previewProcessId');
        $processName = $this->input->get_post('previewProcessName');
        $campaignId = $this->input->get_post('previewCampaignId');
        $campaignName = $this->input->get_post('previewCampaignName');
        $leadScore = $this->input->get_post('previewleadScore');
        $directProcessId = $this->input->get_post('directProcessId');

        $leadScore = $leadScore + 1;
        $data['message'] = $this->preview_model->getNextPreviewLead($this->input->get_post('agentid'), $leadId, $leadsetId, $leadsetName, $processId, $processName, $campaignId, $campaignName, $leadScore, $directProcessId);
        //$this->lua_model->setPreviewLead($leadId,$leadsetId, $leadsetName, $processid, $processName, $campaignId, $campaignName,$leadScore);
        $this->load->view('json', $data);
    }

    public function getNextLeadAfterDispose() {
        $agentId = $this->input->get_post('agentid');
        $processId = $this->input->get_post('directProcessId');
        $data['message'] = $this->preview_model->getuserlead($agentId, $processId);
        $this->load->view('json', $data);
    }

    public function savePreviewLead() {
        $savePreviewLead = new stdClass();
        $leadId = $this->input->get_post('previewLeadId');
        $leadsetId = $this->input->get_post('previewLeadsetId');
        $leadsetName = $this->input->get_post('previewLeadsetName');
        $processId = $this->input->get_post('previewProcessId');
        $processName = $this->input->get_post('previewProcessName');
        $campaignId = $this->input->get_post('previewCampaignId');
        $campaignName = $this->input->get_post('previewCampaignName');
        $leadScore = $this->input->get_post('previewleadScore');
        $savePreviewLead->setPreviewLead = $this->lua_model->setPreviewLead($leadId, $leadsetId, $leadsetName, $processId, $processName, $campaignId, $campaignName, $leadScore);
        $savePreviewLead->increaseCountLeadsetPreviewLead = $this->lua_model->increaseCountLeadsetPreviewLead($processId, $leadsetId);
        $data['message'] = $savePreviewLead;
        $this->load->view('json', $data);
    }

    public function searchlead() {

        $data['message'] = $this->process_model->searchlead($this->input->get_post('searchlead'), $this->input->get_post('cur_lead'));
        $this->load->view('json', $data);
    }

    public function deletelivemonitoring() {
        $process = $this->input->get_post('process');
        $phoneno = $this->input->get_post('phoneno');
        $data['message'] = $this->process_model->deletelivemonitoring($process, $phoneno);
        $this->load->view('json', $data);
    }

    public function getconfig() {
        $data['message'] = $this->config_model->getserverconfig();
        $this->load->view('json', $data);
    }

    public function savepausereason() {
        $pausecode = $this->input->get_post('pausecode');
        $pauseName = $this->input->get_post('pauseName');
        $process = $this->input->get_post('process');
        $processName = $this->input->get_post('processName');
        $campaignId = $this->input->get_post('campaignId');
        $campaignName = $this->input->get_post('campaignName');
        $data['message'] = $this->process_model->savepausereason($pausecode, $pauseName, $process, $processName, $campaignId, $campaignName);
        $this->load->view('json', $data);
    }

    public function checkpreviewprocess() {
        $data['message'] = $this->process_model->checkpreviewprocess();
        $this->load->view('json', $data);
    }

    public function checkListprocess() {
        $data['message'] = $this->process_model->checkListprocess();
        $this->load->view('json', $data);
    }

    public function getratingcomments() {
        $referenceUuid = $this->input->get_post('referenceUuid');
        $processId = $this->input->get_post('processId');
        $data['message'] = $this->process_model->getratingcomments($referenceUuid, $processId);
        $this->load->view('json', $data);
    }

    public function checkPreviewLead() {
        $output = new stdClass();
        $processid = $this->input->get_post('processid');
        $output->previeQueueSetting = $this->process_model->getProcessCallSettings($processid);
        $output->checkPreviewLead = $this->preview_model->checkPreviewLead($processid);
        $data['message'] = $output;
        $this->load->view('json', $data);
    }

    public function churnprocess() {

        $processdispose = $this->input->get_post('processdispose');
        $processid = $this->input->get_post('processid');
        $processName = $this->input->get_post('processName');
        $campaignId = $this->input->get_post('campaignId');
        $campaignName = $this->input->get_post('campaignName');
        $disposeText = $this->input->get_post('disposeText');

        $data['message'] = $this->process_model->churnprocess($processid, $processdispose, $processName, $campaignId, $campaignName, $disposeText);
        $this->load->view('json', $data);
    }

    public function addleadtobeanstalk() {
        $processid = $this->input->get_post('processid');
        //$data['message']=$this->process_model->churnprocess($processid,$processdispose);
        $this->load->view('json', $data);
    }

    public function getprocessdispose() {
        $processid = $this->input->get_post('processid');
        $data['message'] = $this->process_model->getprocessdispose($processid);
        $this->load->view('json', $data);
    }

    public function getProcessDisposeChurn() {
        $processid = $this->input->get_post('processid');
        $leadsetIds = $this->input->get_post('leadsetIds');
        $fromDate = $this->input->get_post('fromDate');
        $data['message'] = $this->process_model->getProcessDisposeChurn($processid, $leadsetIds,$fromDate);
        $this->load->view('json', $data);
    }

    public function saveprocessmode() {
        $processmode = $this->input->get_post('processmode');
        $callingmode = $this->input->get_post('callingmode');
        $processid = $this->input->get_post('processid');
        $processName = $this->input->get_post('processName');
        $campaignId = $this->input->get_post('campaignId');
        $campaignName = $this->input->get_post('campaignName');

        $data['message'] = $this->process_model->saveprocessmode($processid, $processmode, $callingmode, $processName, $campaignId, $campaignName);
        $this->load->view('json', $data);
    }

    public function getProcessRemainingLeadCsv() {
        $callingmode = $this->input->get_post('callingmode');
        $processid = $this->input->get_post('processid');
        $this->process_model->getProcessRemainingLeadCsv($processid, $callingmode);
    }

    public function reloadProcess() {
        $processid = $this->input->get_post('processid');
        $campaignid = $this->input->get_post('campaignid');
        $data['message']['deleteAutoActiveCallOpt'] = $this->lua_model->processReload($processid, $campaignid);
        if ($data['message']['deleteAutoActiveCallOpt'] == "TRUE") {
            $data['message']['processStartOpt'] = $this->api_model->processstart($processid);
        }
        $this->load->view('json', $data);
    }

    public function processstart() {
        $processid = $this->input->get_post('processid');
        $data['message'] = $this->api_model->processstart($processid);
        $this->load->view('json', $data);
    }

    public function processstop() {
        $processid = $this->input->get_post('processid');
        $data['message'] = $this->api_model->processstop($processid);
        $this->load->view('json', $data);
    }

    public function generatequeue() {
        $agentid = $this->input->get_post('agentid');
        $data['message'] = $this->api_model->generatequeue($agentid);
        $this->load->view('json', $data);
    }

    public function generateQueueForSingleProcess() {
        $agentid = $this->input->get_post('agentid');
        $directProcessId = $this->input->get_post('directProcessId');
        $directProcessName = $this->input->get_post('directProcessName');
        $directProcessType = $this->input->get_post('directProcessType');
        $directCampaignId = $this->input->get_post('directCampaignId');
        $directCampaignName = $this->input->get_post('directCampaignName');
        $data['message'] = $this->api_model->generateQueueForSingleProcess($agentid, $directProcessId, $directProcessName, $directProcessType, $directCampaignId, $directCampaignName);
        $this->load->view('json', $data);
    }

    /* function loadfirstlevel()
      {
      $data['message']=$this->reporting_model->livemonitoringdata();
      $this->load->view('json',$data);
      }
      function loadsecondlevel()
      {
      $campaign_id = $this->input->get_post('campaign_id');

      $data['message']=$this->reporting_model->loadsecondlevel($campaign_id);
      $this->load->view('json',$data);
      }
      function loadthirdlevel()
      {
      $campaign_id = $this->input->get_post('campaign_id');
      $processid = $this->input->get_post('processid');
      $data['message']=$this->reporting_model->loadthirdlevel($campaign_id,$processid);
      $this->load->view('json',$data);
      } */

    public function getcurrenttime() {
        $var['date'] = date("Y-m-d");
        $var['time'] = date("H:i");
        $data['message'] = $var;
        $this->load->view('json', $data);
    }

    public function loadsecondlevelquality() {
        $campaign_id = $this->input->get_post('campaign_id');
        $cdrid = $this->input->get_post('cdrid');
        $data['message'] = $this->reporting_model->loadsecondlevelquality($campaign_id, $cdrid);
        $this->load->view('json', $data);
    }

    public function getcampaignprocess() {
        $campaign = $this->input->get_post('campaign');
        $data['message'] = $this->campaign_model->getcampaignprocess($campaign);
        $this->load->view('json', $data);
    }

    public function getleadpenetration() {
        $campaign = $this->input->get_post('campaign');
        $process = $this->input->get_post('process');
        $data['message'] = $this->reporting_model->getleadpenetration($campaign, $process);
        $this->load->view('json', $data);
    }

    public function changeleadstatus() {
        $data['message'] = $this->process_model->changeleadstatus($this->input->get_post('cur_lead'));
        $this->load->view('json', $data);
    }

    public function setleadstatus() {
        $data['message'] = $this->process_model->setleadstatus($this->input->get_post('cur_lead'));
        $this->load->view('json', $data);
    }

    public function isloggedin() {
        $agentid = $this->input->get_post("agentid");
        $data['message'] = 0;
        $is_logged_in = $this->session->userdata('logged_in');
        $loggedin = $this->user_model->isloggedin($agentid);
        if ($is_logged_in !== 'true' || !isset($is_logged_in) || $is_logged_in == "" || $loggedin == 0) {
            //if ( $agentid != $this->session->userdata( 'id' )   ) {
            $this->setuserprocess($agentid);
            $this->user_model->changeloginlog($agentid);
            //$this->session->sess_destroy();
            $data['message'] = 0;
        } else {
            $data['message'] = 1;
        }

        $this->load->view('json', $data);
    }

    // function setuserprocess($userid)
    // {
    //  $this->db->query("DELETE FROM `user_process` WHERE `user`='$userid'");
    //    $process=$this->db->query("SELECT `process_agent`.`process`,`process`.`name` FROM `process_agent` LEFT JOIN `process` ON `process_agent`.`process`=`process`.`id` WHERE `agent`='$userid'")->result();
    //    foreach($process as $processid)
    //    {
    //      $proid=$processid->process;
    //      $this->db->query("INSERT INTO `user_process` (`user`,`process`) VALUES ('$userid','$proid') ");
    //    }
    // }
    public function getcallbackLead() {
        $lead = $this->input->get_post('lead');
        $leadsetId = $this->input->get_post('leadsetId');
        $leadsetName = $this->input->get_post('leadsetName');
        $campaignId = $this->input->get_post('campaignId');
        $campaignName = $this->input->get_post('campaignName');
        $processId = $this->input->get_post('processId');
        $processName = $this->input->get_post('processName');
        $callback = $this->input->get_post('callback');
        $agentId = $this->input->get_post('agentId');
        $timestamp = $this->input->get_post('timestamp');
        $phoneNumber = $this->input->get_post('phoneNumber');
        $callbackJson = $this->input->get_post('callbackJson');
        $data['message'] = $this->callback_model->getcallbackLead($lead, $leadsetId, $leadsetName, $campaignId, $campaignName, $processId, $processName, $callback, $agentId, $timestamp, $phoneNumber, $callbackJson);
        $this->load->view('json', $data);
    }

    public function getusercallback() {
        $data['message'] = $this->callback_model->getusercallback($this->input->get_post('cur_lead'));
        $this->load->view('json', $data);
    }

    public function getprevcallback() {
        $leadid = $this->input->get_post('leadid');
        $dateTime = $this->input->get_post('dateTime');
        $data['message'] = $this->callback_model->getprevcallback($leadid, $dateTime);
        $this->load->view('json', $data);
    }

    public function getnextcallback() {

        // $callbackLeadId      = $this->input->get_post('callbackLeadId');
        // $callbackLeadsetId     = $this->input->get_post('callbackLeadsetId');
        // $callbackLeadsetName   = $this->input->get_post('callbackLeadsetName');
        // $callbackCampaignId    = $this->input->get_post('callbackCampaignId');
        // $callbackCampaignName  = $this->input->get_post('callbackCampaignName');
        // $callbackProcessId     = $this->input->get_post('callbackProcessId');
        // $callbackProcessName   = $this->input->get_post('callbackProcessName');
        $callbackAgentId = $this->input->get_post('callbackAgentId');
        $callbackDateTime = $this->input->get_post('callbackDateTime');
        //$callbackPhoneNumber  = $this->input->get_post('callbackPhoneNumber');
        $data['message'] = $this->callback_model->nextCallback($callbackAgentId, $callbackDateTime);
        //$this->lua_model->insertCallbackLua($callbackLeadId, $callbackLeadsetId, $callbackLeadsetName, $callbackCampaignId, $callbackCampaignName, $callbackProcessId, $callbackProcessName, $callbackAgentId, $callbackDateTime, $callbackPhoneNumber);
        $this->load->view('json', $data);
    }

    public function saveCallbackLead() {
        $callbackLeadId = $this->input->get_post('callbackLeadId');
        $callbackLeadsetId = $this->input->get_post('callbackLeadsetId');
        $callbackLeadsetName = $this->input->get_post('callbackLeadsetName');
        $callbackCampaignId = $this->input->get_post('callbackCampaignId');
        $callbackCampaignName = $this->input->get_post('callbackCampaignName');
        $callbackProcessId = $this->input->get_post('callbackProcessId');
        $callbackProcessName = $this->input->get_post('callbackProcessName');
        $callbackAgentId = $this->input->get_post('callbackAgentId');
        $callbackDateTime = $this->input->get_post('callbackDateTime');
        $callbackPhoneNumber = $this->input->get_post('callbackPhoneNumber');
        $customerName = $this->input->get_post('callbackCustomerName');
        $data['message'] = $this->lua_model->insertCallbackLua($callbackLeadId, $callbackLeadsetId, $callbackLeadsetName, $callbackCampaignId, $callbackCampaignName, $callbackProcessId, $callbackProcessName, $callbackAgentId, $callbackDateTime, $callbackPhoneNumber, $customerName);
        $this->load->view('json', $data);
    }

    public function changeCallBackTime() {
        // print_r($_GET);
        $lead = $this->input->get_post('lead');
        $leadsetId = $this->input->get_post('leadsetId');
        $leadsetName = $this->input->get_post('leadsetName');
        $campaignId = $this->input->get_post('campaignId');
        $campaignName = $this->input->get_post('campaignName');
        $processId = $this->input->get_post('processId');
        $processName = $this->input->get_post('processName');
        $callDate = $this->input->get_post('callDate');
        $callTime = $this->input->get_post('callTime');
        $agentId = $this->input->get_post('agentId');
        $timestamp = $this->input->get_post('timestamp');
        $phoneNumber = $this->input->get_post('phoneNumber');
        $customerName = $this->input->get_post('customerName');
        $data['message'] = $this->callback_model->changeCallBackTime($lead, $leadsetId, $leadsetName, $campaignId, $campaignName, $processId, $processName, $callDate, $callTime, $agentId, $timestamp, $phoneNumber, $customerName);
        $this->load->view('json', $data);
    }

    public function callbacksearchlead() {
        $data['message'] = $this->callback_model->callbacksearchlead($this->input->get_post('searchlead'), $this->input->get_post('cur_lead'));
        $this->load->view('json', $data);
    }

    public function checkcallbackprocess() {
        $userId = $this->session->userdata('id');
        // $data['message']=$this->callback_model->checkcallbackprocess();
        $data['message'] = $this->lua_model->countCallback($userId);
        $this->load->view('json', $data);
    }

    public function deletecallback() {
        $data['message'] = $this->callback_model->deletecallback($this->input->get_post('disposecallid'));
        $this->load->view('json', $data);
    }

    public function getuserpriority() {
        $campaign = $this->input->get_post('campaign');
        $process = $this->input->get_post('process');
        $data['message'] = $this->user_model->getuserpriority($campaign, $process);
        $this->load->view('json', $data);
    }

    public function getmonthyearcallback() {
        $data['message'] = $this->callback_model->getmonthyearcallback($this->input->get_post('cur_lead'), $this->input->get_post('month'), $this->input->get_post('year'));
        $this->load->view('json', $data);
    }

    public function getNotSelectedLeadset($process) {
        $data['message'] = $this->lead_model->getNotSelectedLeadset($process);
        $this->load->view('json', $data);
    }

    public function getNotSelectedAgent() {
        $processId = $this->input->get_post("processId");
        $processType = $this->input->get_post("processType");
        $data['message'] = $this->user_model->getNotSelectedAgent($processId, $processType);
        $this->load->view('json', $data);
    }

    public function getNotSelectedTransferAgent() {
        $processId = $this->input->get_post("processId");
        $data['message'] = $this->user_model->getNotSelectedTransferAgent($processId);
        $this->load->view('json', $data);
    }

    public function getNotSelectedConferenceAgent() {
        $processId = $this->input->get_post("processId");
        $data['message'] = $this->user_model->getNotSelectedConferenceAgent($processId);
        $this->load->view('json', $data);
    }

    public function getNotSelectedTranssConfAgent() {
        $processId = $this->input->get_post("processId");
        $data['message'] = $this->user_model->getNotSelectedTranssConfAgent($processId);
        $this->load->view('json', $data);
    }

    public function getNotSelectedTeamLeader() {
        $processId = $this->input->get_post("processId");
        $data['message'] = $this->user_model->getNotSelectedTeamLeader($processId);
        $this->load->view('json', $data);
    }

    public function getNotSelectedQuality() {
        $processId = $this->input->get_post("processId");
        $data['message'] = $this->user_model->getNotSelectedQuality($processId);
        $this->load->view('json', $data);
    }

    public function getNotSelectedBackOffice() {
        $processId = $this->input->get_post("processId");
        $data['message'] = $this->user_model->getNotSelectedBackOffice($processId);
        $this->load->view('json', $data);
    }

    public function getSelectedAgent() {
        $processId = $this->input->get_post("processId");
        $data['message'] = $this->user_model->getSelectedAgent($processId);
        $this->load->view('json', $data);
    }

    public function getSelectedTransferAgent() {
        $processId = $this->input->get_post("processId");
        $data['message'] = $this->user_model->getSelectedTransferAgent($processId);
        $this->load->view('json', $data);
    }

    public function getSelectedConferenceAgent() {
        $processId = $this->input->get_post("processId");
        $data['message'] = $this->user_model->getSelectedConferenceAgent($processId);
        $this->load->view('json', $data);
    }

    public function getSelectedTransConfAgent() {
        $processId = $this->input->get_post("processId");
        $data['message'] = $this->user_model->getSelectedTransConfAgent($processId);
        $this->load->view('json', $data);
    }

    public function getSelectedTeamLeaderAgent() {
        $processId = $this->input->get_post("processId");
        $data['message'] = $this->user_model->getSelectedTeamLeaderAgent($processId);
        $this->load->view('json', $data);
    }

    public function getSelectedQualityAgent() {
        $processId = $this->input->get_post("processId");
        $data['message'] = $this->user_model->getSelectedQualityAgent($processId);
        $this->load->view('json', $data);
    }

    public function getSelectedBackOfficeAgent() {
        $processId = $this->input->get_post("processId");
        $data['message'] = $this->user_model->getSelectedBackOfficeAgent($processId);
        $this->load->view('json', $data);
    }

    public function getRemanigLead($process) {
        $callingmode = $this->input->get_post("callingmode");
        if ($callingmode == "Preview") {
            $data['message'] = $this->lua_model->countPreviewLead($process);
        } else if ($callingmode == "Auto") {
            $data['message'] = $this->api_model->getRemanigLeadFromBeanstalk($process);
        }
        $this->load->view('json', $data);
    }

    public function addNewLeadSet() {

        $processid = $this->input->get_post("processid");
        $processName = $this->input->get_post("processName");
        $campaignId = $this->input->get_post("campaignId");
        $campaignName = $this->input->get_post("campaignName");
        $callingmode = $this->input->get_post("callingmode");
        $leadset = $this->input->get_post("leadset");

        if ($callingmode == "Preview" || $callingmode == "Auto" || $callingmode == "listview") {

            $checkState = $this->process_model->processStateCheck($processid);

            if ($checkState == "FALSE") {

                $data['message'] = "PROCESS_IS_NOT_LIVE";
                $this->load->view('json', $data);
                return;
            }
        }

        $notAddedLeadId = array();
        foreach ($leadset as $lead) {
            $leadsetId = $lead[0];
            $leadsetName = $lead[1];
            //mapped leadset to process
            $this->preview_model->addtoProcess($processid, $processName, $leadsetId, $leadsetName);
            if ($callingmode == "Preview") {
                $output = $this->preview_model->addtoqueue($leadsetId, $leadsetName, $processid, $processName, $campaignId, $campaignName);
                $notAddedLeadId[$leadsetId] = $output;
            } else if ($callingmode == "Auto") {
                $campaign = "SELECT `campaign` FROM `process` WHERE `id` = $processid";
                $campaign = $this->db->query($campaign)->row();
                $campaign = $campaign->campaign;
                $output = $this->api_model->beanstalk($processid, $campaign, $leadsetId);
                // print_r($output);
            } else if ($callingmode == "listview") {
                $output = $this->process_model->listViewLeadsetAdd($processid, $processName, $leadsetId, $leadsetName);
                $notAddedLeadId[$leadsetId] = $output;
            }
        }
        $data['message'] = $notAddedLeadId;
        $this->load->view('json', $data);
    }

    public function addAgentToProcess() {

        $processid = $this->input->get_post("processid");
        $processName = $this->input->get_post("processName");
        $campaignId = $this->input->get_post("campaignId");
        $campaignName = $this->input->get_post("campaignName");
        $agentArr = $this->input->get_post("agent");
        $processType = $this->input->get_post("processType");
        $data['message'] = $this->process_model->addAgentToProcess($agentArr, $processid, $processName, $campaignId, $campaignName, $processType);
        $this->load->view('json', $data);
    }

    public function addTransferAgentToProcess() {

        $processid = $this->input->get_post("processid");
        $processName = $this->input->get_post("processName");
        $campaignId = $this->input->get_post("campaignId");
        $campaignName = $this->input->get_post("campaignName");
        $agentArr = $this->input->get_post("agent");
        $data['message'] = $this->process_model->addTransferAgentToProcess($agentArr, $processid, $processName, $campaignId, $campaignName);
        $this->load->view('json', $data);
    }

    public function addConferenceAgentToProcess() {

        $processid = $this->input->get_post("processid");
        $processName = $this->input->get_post("processName");
        $campaignId = $this->input->get_post("campaignId");
        $campaignName = $this->input->get_post("campaignName");
        $agentArr = $this->input->get_post("agent");
        $data['message'] = $this->process_model->addConferenceAgentToProcess($agentArr, $processid, $processName, $campaignId, $campaignName);
        $this->load->view('json', $data);
    }

    public function addTranssConfAgentToProcess() {

        $processid = $this->input->get_post("processid");
        $processName = $this->input->get_post("processName");
        $campaignId = $this->input->get_post("campaignId");
        $campaignName = $this->input->get_post("campaignName");
        $agentArr = $this->input->get_post("agent");
        $data['message'] = $this->process_model->addTranssConfAgentToProcess($agentArr, $processid, $processName, $campaignId, $campaignName);
        $this->load->view('json', $data);
    }

    public function addTeamLeaderAgentToProcess() {
        $processid = $this->input->get_post("processid");
        $processName = $this->input->get_post("processName");
        $campaignId = $this->input->get_post("campaignId");
        $campaignName = $this->input->get_post("campaignName");
        $agentArr = $this->input->get_post("agent");
        $data['message'] = $this->process_model->addTeamLeaderAgentToProcess($agentArr, $processid, $processName, $campaignId, $campaignName);
        $this->load->view('json', $data);
    }

    public function removeAgentToProcess() {
        $processid = $this->input->get_post("processid");
        $processName = $this->input->get_post("processName");
        $campaignId = $this->input->get_post("campaignId");
        $campaignName = $this->input->get_post("campaignName");
        $agentArr = $this->input->get_post("agent");
        $processType = $this->input->get_post("processType");
        $data['message'] = $this->process_model->removeAgentToProcess($agentArr, $processid, $processName, $campaignId, $campaignName, $processType);
        $this->load->view('json', $data);
    }

    public function addQualityAgentToProcess() {
        $processid = $this->input->get_post("processid");
        $processName = $this->input->get_post("processName");
        $campaignId = $this->input->get_post("campaignId");
        $campaignName = $this->input->get_post("campaignName");
        $agentArr = $this->input->get_post("agent");
        $data['message'] = $this->process_model->addQualityAgentToProcess($agentArr, $processid, $processName, $campaignId, $campaignName);
        $this->load->view('json', $data);
    }

    public function addBackOfficeAgentToProcess() {
        $processid = $this->input->get_post("processid");
        $processName = $this->input->get_post("processName");
        $campaignId = $this->input->get_post("campaignId");
        $campaignName = $this->input->get_post("campaignName");
        $agentArr = $this->input->get_post("agent");
        $data['message'] = $this->process_model->addBackOfficeAgentToProcess($agentArr, $processid, $processName, $campaignId, $campaignName);
        $this->load->view('json', $data);
    }

    public function removeTransferAgentToProcess() {

        $processid = $this->input->get_post("processid");
        $processName = $this->input->get_post("processName");
        $campaignId = $this->input->get_post("campaignId");
        $campaignName = $this->input->get_post("campaignName");
        $agentArr = $this->input->get_post("agent");
        $data['message'] = $this->process_model->removeTransferAgentToProcess($agentArr, $processid, $processName, $campaignId, $campaignName);
        $this->load->view('json', $data);
    }

    public function removeConferenceAgentToProcess() {

        $processid = $this->input->get_post("processid");
        $processName = $this->input->get_post("processName");
        $campaignId = $this->input->get_post("campaignId");
        $campaignName = $this->input->get_post("campaignName");
        $agentArr = $this->input->get_post("agent");
        $data['message'] = $this->process_model->removeConferenceAgentToProcess($agentArr, $processid, $processName, $campaignId, $campaignName);
        $this->load->view('json', $data);
    }

    public function removeTranConfAgentToProcess() {

        $processid = $this->input->get_post("processid");
        $processName = $this->input->get_post("processName");
        $campaignId = $this->input->get_post("campaignId");
        $campaignName = $this->input->get_post("campaignName");
        $agentArr = $this->input->get_post("agent");
        $data['message'] = $this->process_model->removeTranConfAgentToProcess($agentArr, $processid, $processName, $campaignId, $campaignName);
        $this->load->view('json', $data);
    }

    public function removeTeamLeaderAgentToProcess() {
        $processid = $this->input->get_post("processid");
        $processName = $this->input->get_post("processName");
        $campaignId = $this->input->get_post("campaignId");
        $campaignName = $this->input->get_post("campaignName");
        $agentArr = $this->input->get_post("agent");
        $data['message'] = $this->process_model->removeTeamLeaderAgentToProcess($agentArr, $processid, $processName, $campaignId, $campaignName);
        $this->load->view('json', $data);
    }

    public function removeQualityAgentToProcess() {
        $processid = $this->input->get_post("processid");
        $processName = $this->input->get_post("processName");
        $campaignId = $this->input->get_post("campaignId");
        $campaignName = $this->input->get_post("campaignName");
        $agentArr = $this->input->get_post("agent");
        $data['message'] = $this->process_model->removeQualityAgentToProcess($agentArr, $processid, $processName, $campaignId, $campaignName);
        $this->load->view('json', $data);
    }

    public function removeBackOfficeAgentToProcess() {
        $processid = $this->input->get_post("processid");
        $processName = $this->input->get_post("processName");
        $campaignId = $this->input->get_post("campaignId");
        $campaignName = $this->input->get_post("campaignName");
        $agentArr = $this->input->get_post("agent");
        $data['message'] = $this->process_model->removeBackOfficeAgentToProcess($agentArr, $processid, $processName, $campaignId, $campaignName);
        $this->load->view('json', $data);
    }

    public function getProcessLeadset($process) {
        $data['message'] = $this->lead_model->getProcessLead($process);
        $this->load->view('json', $data);
    }

    public function getProcessPrefix() {
        $data["message"] = $this->process_model->getProcessPrefix($this->input->get_post("process"));
        $this->load->view("json", $data);
    }

    public function setProcessPrefix() {
        $process = $this->input->get_post("process");
        $prefix = $this->input->get_post("prefix");
        $data["message"] = $this->process_model->setProcessPrefix($process, $prefix);
        $this->load->view("json", $data);
    }

    public function getCampaignPrefix() {
        $data["message"] = $this->campaign_model->getCampaignPrefix($this->input->get_post("campaign"));
        $this->load->view("json", $data);
    }

    public function setCampaignPrefix() {
        $campaign = $this->input->get_post("campaign");
        $prefix = $this->input->get_post("prefix");
        $data["message"] = $this->campaign_model->setCampaignPrefix($campaign, $prefix);
        $this->load->view("json", $data);
    }

    public function removeProcessLeadset() {
        $removeProcessLeadset = new stdClass();
        $callingmode = $this->input->get_post("callingmode");
        $processid = $this->input->get_post("processid");
        $processName = $this->input->get_post("processName");
        $campaignId = $this->input->get_post("campaignId");
        $campaignName = $this->input->get_post("campaignName");
        $leadset = $this->input->get_post("leadset");
        $leadsetCsv = "";
        $removeProcessLeadset->output = "FALSE";
        foreach ($leadset as $lead) {
            $leadsetId = $lead[0];
            $leadsetName = $lead[1];
            $this->preview_model->removeFromProcess($processid, $leadsetId);
            if ($leadsetCsv == "") {
                $leadsetCsv = $leadsetId;
            } else {
                $leadsetCsv = $leadsetCsv . "," . $leadsetId;
            }

            if ($callingmode == "listview") {
                $this->process_model->listViewLeadsetRemove($processid, $leadsetId);
            }
        }

        if ($callingmode == "listview") {
            $removeProcessLeadset->output = "TRUE";
            $removeProcessLeadset->listLuaOpt = $this->lua_model->removeLeadsetListLead($processid, $leadsetCsv);
        } else if ($callingmode == "Preview") {

            $removeProcessLeadset->output = "TRUE";
            $removeProcessLeadset->previewLeadRemoveOpt = $this->lua_model->removeLeadsetPreviewLead($processid, $leadsetCsv);
        } elseif ($callingmode == "Auto") {
            $autoProcessQueueEmptyOutput = "";
            $autoProcessQueueEmptyOutput = $this->api_model->processQueueEmpty($processid, $campaignId);
            if ($autoProcessQueueEmptyOutput) {
                $remanningLeadInBeanstalk = $this->api_model->getRemanigLeadFromBeanstalk($processid);
                if ($remanningLeadInBeanstalk == 0) {
                    $removeProcessLeadset->output = "TRUE";
                    $totalLead = $this->api_model->restartAutoProcess($processid);
                    $removeProcessLeadset->totalLead = $totalLead;
                } else {
                    $removeProcessLeadset->message = "beanstalk Queue is not empty to fill";
                }
            } else {
                $removeProcessLeadset->message = "beanstalk empty Doesn't respond";
            }

            $removeProcessLeadset->autoProcessQueueEmptyOutput = $autoProcessQueueEmptyOutput;
        } else {
            $removeProcessLeadset->output = "TRUE";
        }

        $data['message'] = $removeProcessLeadset;
        $this->load->view('json', $data);
    }

    public function changeRatio() {

        $processid = $this->input->get_post("processid");
        $ratio = $this->input->get_post("ratio");
        $callingmode = $this->input->get_post("callingmode");
        $this->process_model->updateRatio($processid, $ratio);
        $data['message'] = $this->lua_model->updateRatiolua($processid, $ratio);
        $this->load->view('json', $data);
    }

    public function getProcessWaitTimeAndTimesToTry() {
        $processId = $this->input->get_post("processId");
        $data['message'] = $this->process_model->getProcessWaitTimeAndTimesToTry($processId);
        $this->load->view('json', $data);
    }

    public function getOneToOneMappingSetting() {
        $processId = $this->input->get_post("processId");
        $data['message'] = $this->process_model->getOneToOneMappingSetting($processId);
        $this->load->view('json', $data);
    }

    public function inboundAgentRouting() {
        $processId = $this->input->get_post("processId");
        $data['message'] = $this->process_model->inboundAgentRouting($processId);
        $this->load->view('json', $data);
    }

    public function setProcessWaitTimeAndTimesToTry() {
        $processId = $this->input->get_post("processId");
        $waitTime = $this->input->get_post("waitTime");
        $timesToTry = $this->input->get_post("timesToTry");
        $callingMode = $this->input->get_post("callingMode");

        $data['message'] = $this->process_model->setProcessWaitTimeAndTimesToTry($processId, $callingMode, $waitTime, $timesToTry);
        $this->load->view('json', $data);
    }

    public function saveAgentRouting() {

        $data = $this->input->post();
        //echo '<pre>';print_r($data);exit;
        $responsedata['message'] = $this->process_model->saveAgentRouting($data);
        $this->load->view('json', $responsedata);
    }

    public function updateProcessDateTime() {
        $startDate = $this->input->get_post("startDate");
        $endDate = $this->input->get_post("endDate");
        $starttime = $this->input->get_post("starttime");
        $endtime = $this->input->get_post("endtime");
        $processid = $this->input->get_post("processid");
        $data['message'] = $this->process_model->updateProcessDateTime($startDate, $endDate, $starttime, $endtime, $processid);
        $this->load->view('json', $data);
    }

    public function cdrreportingdetail() {
        $reference = $this->input->get_post("reference_uuid");
        $qry = "SELECT * FROM `cdr_detail` WHERE `cdr_detail`.`reference_uuid` = '$reference'";
        $data["message"] = $this->db->query($qry)->result_array();
        $this->load->view('json', $data);
    }

    public function sendMessage() {

        $messageNumber = $this->input->get_post("messageNumber");
        $messageContent = $this->input->get_post("messageContent");
        $campaignId = $this->input->get_post("campaignId");
        $campaignName = $this->input->get_post("campaignName");
        $rfud = $this->input->get_post("rfud");
        $processId = $this->input->get_post("processId");
        $processName = $this->input->get_post("processName");
        $customerId = $this->input->get_post("customerId");
        $agentUsername = $this->input->get_post("agentUsername");
        $agentid = $this->input->get_post("agentid");
        $agentName = $this->input->get_post("agentName");
        $crmId = $this->input->get_post("crmId");
        $data["message"] = $this->message_model->sendMessage($messageNumber, $messageContent, $campaignId, $campaignName, $rfud, $processId, $processName, $customerId, $agentUsername, $agentid, $agentName, $crmId);
        $this->load->view('json', $data);
    }

    public function saveSmsProvideDetail() {
        $smsProvider = $this->input->get_post("smsProvider");
        $smsUrl = $this->input->get_post("smsUrl");
        $smsMethod = $this->input->get_post("smsMethod");
        $smsPhone = $this->input->get_post("smsPhone");
        $smsText = $this->input->get_post("smsText");
        $providerId = $this->input->get_post("providerId");
        $smsReplyMethod = $this->input->get_post("smsReplyMethod");
        $smsSuccess = $this->input->get_post("smsSuccess");
        $smsObject = $this->input->get_post("smsObject");
        $data["message"]["update"] = $this->message_model->saveSmsProvideDetail($smsProvider, $smsUrl, $smsMethod, $providerId, $smsPhone, $smsText, $smsReplyMethod, $smsSuccess, $smsObject);
        $data["message"]["table"] = $this->config_model->viewMsgConfig();
        $this->load->view('json', $data);
    }

    public function createSmsProvideDetail() {
        $smsProvider = $this->input->get_post("smsProvider");
        $smsUrl = $this->input->get_post("smsUrl");
        $smsMethod = $this->input->get_post("smsMethod");
        $smsPhone = $this->input->get_post("smsPhone");
        $smsText = $this->input->get_post("smsText");

        $smsReplyMethod = $this->input->get_post("smsReplyMethod");
        $smsObject = $this->input->get_post("smsObject");
        $smsSuccess = $this->input->get_post("smsSuccess");

        $data["message"]["insert"] = $this->message_model->createSmsProvideDetail($smsProvider, $smsUrl, $smsMethod, $smsPhone, $smsText, $smsReplyMethod, $smsObject, $smsSuccess);
        $data["message"]["table"] = $this->config_model->viewMsgConfig();
        $this->load->view('json', $data);
    }

    public function getagentDisposeDashboard() {
        $selectedCampaign = $this->input->get_post("selectedCampaign");
        $selectedProcess = $this->input->get_post("selectedProcess");
        $startDate = $this->input->get_post("startDate");
        $endDate = $this->input->get_post("endDate");
        $disposeData = $this->agentdispose_model->disposeData($selectedCampaign, $selectedProcess, $startDate, $endDate);
        // print_r($disposeData );
        $disposeNewData = new stdClass();
        if ($disposeData->output == "TRUE") {
            $disposeNewData->output = "TRUE";
            $disposeNewData->result = $this->agentdispose_model->disposeHtml($disposeData);
        } else {
            $disposeNewData->output = "FALSE";
            $disposeNewData->message = $disposeData->message;
        }
        $data["message"] = $disposeNewData;
        // $data["message"]   = $this->agentdispose_model->disposeDashboardFilteredData($selectedCampaign, $selectedProcess, $startDate, $endDate);
        $this->load->view('json', $data);
    }

    public function mailSendWithAttachment() {

        // mail send defination start
        $mailSend = new stdClass();
        $emailReceiver = $this->input->get_post("disposeEmailReceiver");
        $emailSubject = $this->input->get_post("disposeEmailSubject");
        $tmp_name = isset($_FILES['fileAttach']['tmp_name']) ?
                $_FILES['fileAttach']['tmp_name'] : array();
        $name = isset($_FILES['fileAttach']['name']) ?
                $_FILES['fileAttach']['name'] : array();

        if ($emailSubject == '') {

            $mailSend->output = "FALSE";
            $mailSend->message = "Empty email subjet not allowed";
            $data["message"] = $mailSend;
            $this->load->view('json', $data);
        } else {

            $emailMessage = $this->input->get_post("disposeEmailMessage");
            $emailFooter = $this->input->get_post("disposeEmailFooter");
            $emailSignature = $this->input->get_post("disposeEmailSignature");
            $campaignId = $this->input->get_post("disposeCnid");
            $campaignName = $this->input->get_post("disposeCnnm");
            $rfud = $this->input->get_post("disposeRfud");
            $processId = $this->input->get_post("disposePrid");
            $processName = $this->input->get_post("disposePrnm");
            $customerId = $this->input->get_post("disposeCrid");
            $agentUsername = $this->input->get_post("disposeAgentusername");
            $agentid = $this->input->get_post("disposeAtid");
            $agentName = $this->input->get_post("disposeAgentname");
            $disposeEmailSender = $this->input->get_post("disposeEmailSender");
            $SignatureUrl = $this->input->get_post("disposeEmailSignatureUrl");
            $disposeEmailBcc = $this->input->get_post("disposeEmailBcc");
            $disposeEmailSubject = $this->input->get_post("disposeEmailSubject");
            $crmId = $this->input->get_post("crmId");

            $disposeEmailCc = array();
            if ($this->input->get_post("disposeEmailCc")) {
                $disposeEmailCc = $this->input->get_post("disposeEmailCc");
            }
            $disposeEmailBcc = array();
            if ($this->input->get_post("disposeEmailBcc")) {
                $disposeEmailBcc = $this->input->get_post("disposeEmailBcc");
            }
            $emailReplay = $this->message_model->sendDisposeEmail($tmp_name, $name, $emailReceiver, $emailSubject, $emailMessage, $emailFooter, $emailSignature, $campaignId, $campaignName, $rfud, $processId, $processName, $customerId, $agentUsername, $agentid, $agentName, $SignatureUrl, $disposeEmailSender, $disposeEmailCc, $disposeEmailBcc, $crmId);

            if ($emailReplay == "Message Sent") {
                $mailSend->output = "TRUE";
                $mailSend->message = $emailReplay;
            } else {
                $mailSend->output = "FALSE";
                $mailSend->message = $emailReplay;
            }
            $data["message"] = $mailSend;
            $this->load->view('json', $data);
        }
    }

    public function leadAttachment() {

        $uploadIVROutput = new stdClass();
        if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {

            $attachFileName = $_FILES['file']['name'];
            $uploadPath = $this->config->item('uploadPath');
            $extension = pathinfo($attachFileName, PATHINFO_EXTENSION);
            $attachFileNewName = "voitekkAttachment_" . uniqid() . "." . $extension;
            $target_file = $uploadPath . "/attachment/" . $attachFileNewName;
            // $uploadIVROutput->setIvrFileNameData  =$this->inbound_model->setIvrFileName( $ivrFileName,$ivrFileDetail,$ivrNewName,$ivrName);
            // if(in_array($_FILES['file']['type'], $allowed)){
            if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
                $uploadIVROutput->output = "TRUE";
                $uploadIVROutput->message = "File uploaded Successfully";
                $uploadIVROutput->currentFileName = $attachFileName;
                $uploadIVROutput->systemFileName = $attachFileNewName;
                // $uploadIVROutput->ivrJson  = $this->inbound_model->getIvrJson();
            } else {
                $uploadIVROutput->output = "FALSE";
                $uploadIVROutput->message = "MOVE UPLOADED FILE FAILED!!";
                $uploadIVROutput->error = error_get_last();
            }
            // }
            // else{
            //   $uploadIVROutput->output = "FALSE";
            //   $uploadIVROutput->message = "Please Upload proper file";
            // }
        } else {
            $uploadIVROutput->output = "FALSE";
            $uploadIVROutput->message = $this->uploadErrorMessage($_FILES['file']['type']);
        }
        $data["message"] = $uploadIVROutput;
        $this->load->view('json', $data);
    }

    //email send functionality start
    public function sendEmail() {
        // print_r($_POST);
        $emailReceiver = $this->input->get_post("emailReceiver");
        $emailSubject = $this->input->get_post("emailSubject");
        $emailMessage = $this->input->get_post("emailMessage");
        $campaignId = $this->input->get_post("campaignId");
        $campaignName = $this->input->get_post("campaignName");
        $rfud = $this->input->get_post("rfud");
        $processId = $this->input->get_post("processId");
        $processName = $this->input->get_post("processName");
        $customerId = $this->input->get_post("customerId");
        $agentUsername = $this->input->get_post("agentUsername");
        $agentid = $this->input->get_post("agentid");
        $agentName = $this->input->get_post("agentName");
        $crmId = $this->input->get_post("crmId");
        $data["message"] = $this->message_model->sendEmail($emailReceiver, $emailSubject, $emailMessage, $campaignId, $campaignName, $rfud, $processId, $processName, $customerId, $agentUsername, $agentid, $agentName, $crmId);
        $this->load->view('json', $data);
    }

    //email send functionality start
    public function sendMailReport() {
        $emailReceiver = $this->input->get_post("emailReceiver");
        $emailSubject = $this->input->get_post("emailSubject");
        $emailMessage = $this->input->get_post("emailMessage");
        $data["message"] = $this->message_model->sendMailReport($emailReceiver, $emailSubject, $emailMessage);
        $this->load->view('json', $data);
    }

    //email send functionality end
    //New call back function

    public function countCallback($agentId) {
        $data["message"] = $this->lua_model->countCallback($agentId);
        $this->load->view('json', $data);
    }

    //new callback end
    //follow up functionality start
    public function checkFollowUp() {
        $userId = $this->session->userdata('id');
        $data['message'] = $this->lua_model->countFollowUp($userId);
        $this->load->view('json', $data);
    }

    public function countFollowUp($agentId) {
        $data["message"] = $this->lua_model->countFollowUp($agentId);
        $this->load->view('json', $data);
    }

    public function getuserFollowUp() {
        $data['message'] = $this->followup_model->getuserFollowUp($this->input->get_post('cur_lead'));
        $this->load->view('json', $data);
    }

    public function getprevFollowUp() {
        $leadid = $this->input->get_post('leadid');
        $dateTime = $this->input->get_post('dateTime');
        $data['message'] = $this->followup_model->getprevFollowUp($leadid, $dateTime);
        $this->load->view('json', $data);
    }

    public function nextFollowUp() {

        $followUpAgentId = $this->input->get_post('followUpAgentId');
        $followUpDateTime = $this->input->get_post('followUpDateTime');
        $data['message'] = $this->followup_model->nextFollowUp($followUpAgentId, $followUpDateTime);
        //$this->lua_model->insertfollowUpLua($followUpLeadId, $followUpLeadsetId, $followUpLeadsetName, $followUpCampaignId, $followUpCampaignName, $followUpProcessId, $followUpProcessName, $followUpAgentId, $followUpDateTime, $followUpPhoneNumber);
        $this->load->view('json', $data);
    }

    public function savefollowUpLead() {
        $followUpLeadId = $this->input->get_post('followUpLeadId');
        $followUpLeadsetId = $this->input->get_post('followUpLeadsetId');
        $followUpLeadsetName = $this->input->get_post('followUpLeadsetName');
        $followUpCampaignId = $this->input->get_post('followUpCampaignId');
        $followUpCampaignName = $this->input->get_post('followUpCampaignName');
        $followUpProcessId = $this->input->get_post('followUpProcessId');
        $followUpProcessName = $this->input->get_post('followUpProcessName');
        $followUpAgentId = $this->input->get_post('followUpAgentId');
        $followUpDateTime = $this->input->get_post('followUpDateTime');
        $followUpPhoneNumber = $this->input->get_post('followUpPhoneNumber');
        $data['message'] = $this->lua_model->insertFollowUpLua($followUpLeadId, $followUpLeadsetId, $followUpLeadsetName, $followUpCampaignId, $followUpCampaignName, $followUpProcessId, $followUpProcessName, $followUpAgentId, $followUpDateTime, $followUpPhoneNumber);
        $this->load->view('json', $data);
    }

    //follow up functionality end
    // live monitoring start
    public function getCampaignWithProcess() {
        $data["message"] = $this->campaign_model->getCampaignWithProcess();
        $this->load->view('json', $data);
    }

    public function getProcessAgentCsv() {
        $processCsv = $this->input->get_post('processCsv');
        $data["message"] = $this->process_model->getProcessAgentCsv($processCsv);
        $this->load->view('json', $data);
    }

    public function getLiveMonitoringData() {
        $agentCsv = $this->input->get_post('processAgentCsv');
        // echo $agentCsv;
        // $agentCsv = 'ALL';
        $data["message"] = $this->lua_model->getUserStatusInfoQ($agentCsv);
        $this->load->view('json', $data);
    }

    // live monitoring end
    // agentPerformanceReport function start

    public function getAgentPerformanceFilteredData() {
        $selectedDate = $this->input->get_post('selectedDate');
        $data['message'] = $this->reporting_model->getAgentPerformanceFilteredData($selectedDate);
        $this->load->view('json', $data);
    }

    public function getSummaryReportFilterJSONData() {
        $startDate = $this->input->get_post('startDate');
        $endDate = $this->input->get_post('endDate');
        $userCsv = $this->input->get_post('userCsv');
        $data['message'] = $this->reporting_model->getSummaryReportFilterJSONData($startDate, $endDate, $userCsv);
        $this->load->view('json', $data);
    }

    // function getSummaryReportFilterData(){
    //   $selectedDate     = $this->input->get_post('selectedDate');
    //   $userCsv          = $this->input->get_post('userCsv');
    //   $data['message']  = $this->reporting_model->getSummaryReportFilterData($selectedDate,$userCsv);
    //   $this->load->view('json',$data);
    // }
    // function pauseBreatkFilteredData(){
    //   $selectedDate     = $this->input->get_post('selectedDate');
    //   $pauseBreakDownReport = $this->reporting_model->pauseBreakDownReport($selectedDate);
    //   $pauseDataArray = array();
    //   $agentPauseData = array();
    //   $pauseData = "";
    //   foreach ($pauseBreakDownReport->pauseData as $pauseDatakey => $pauseDatavalue) {
    //           $totalPauseBreak = 0;
    //           $agentPauseData = array();
    //           array_push($agentPauseData, $pauseBreakDownReport->agentData[$pauseDatakey]);
    //           // echo "<td>".$pauseDatakey."</td>";
    //             // print_r($pauseDatavalue);
    //             foreach ($pauseBreakDownReport->pauseState as $pauseStatekey => $pauseStatevalue) {
    //               $pauseData = "<a id='pauseState' href='#'' onclick='getPauseDetail(".$pauseDatakey.",".$pauseStatevalue->id.")'  class='label'style='color: #464749;font-size: 100%;font-weight: normal;'>".gmdate("H:i:s",$pauseDatavalue[$pauseStatevalue->id])."</a>";
    //               array_push($agentPauseData, $pauseData);
    //               $totalPauseBreak = $totalPauseBreak + $pauseDatavalue[$pauseStatevalue->id];
    //             }
    //            $pauseData = gmdate("H:i:s",$totalPauseBreak);
    //            array_push($agentPauseData, $pauseData);
    //            array_push($pauseDataArray, $agentPauseData);
    //           }
    //            // print_r($pauseDataArray);
    //    $data['message'] = $pauseDataArray;
    //   $this->load->view('json',$data);
    // }
    public function pauseBreatkFilteredData() {
        $selectedDate = $this->input->get_post('selectedDate');
        $pauseBreakDownReport = $this->reporting_model->pauseBreakDownReport($selectedDate);
        $tableHtml = "";
        if (isset($pauseBreakDownReport)) {
            $tableHtml .= '<table id="agentPauseBreakReport" class="table" style="width = 100%">';
            $tableHtml .= '<thead><tr><th>Agent</th>';
            foreach ($pauseBreakDownReport->pauseState as $pauseStatekey => $pauseStatevalue) {
                if ($pauseStatevalue->deleted == 0 || array_key_exists($pauseStatevalue->id, $pauseBreakDownReport->activePauseState)) {
                    $tableHtml .= "<th>" . $pauseStatevalue->name . "</th>";
                }
            }
            $tableHtml .= '<th>Total Pause</th><th>Pause Details </th> </tr></thead>';
            foreach ($pauseBreakDownReport->pauseData as $pauseDatakey => $pauseDatavalue) {
                $totalPauseBreak = 0;
                $tableHtml .= "<tr>";
                $tableHtml .= "<td>" . $pauseBreakDownReport->agentData[$pauseDatakey] . "</td>";
                // $tableHtml .=  "<td>".$pauseDatakey."</td>";
                foreach ($pauseBreakDownReport->pauseState as $pauseStatekey => $pauseStatevalue) {
                    if ($pauseStatevalue->deleted == 0 || array_key_exists($pauseStatevalue->id, $pauseBreakDownReport->activePauseState)) {
                        $tableHtml .= "<td><a id='pauseState' href='#'' onclick='getPauseDetail(" . $pauseDatakey . "," . $pauseStatevalue->id . ")'  class='label'style='color: #464749;font-size: 100%;font-weight: normal;'>" . gmdate("H:i:s", $pauseDatavalue[$pauseStatevalue->id]) . "</a></td>";
                        $totalPauseBreak = $totalPauseBreak + $pauseDatavalue[$pauseStatevalue->id];
                    }
                }

                $tableHtml .= "<td>" . gmdate("H:i:s", $totalPauseBreak) . "</td>";
                $tableHtml .= "<td><button type='button' class='btn btn-sm' onclick=agentPauseDetails('" . $pauseDatakey . "','" . $date . "') id='agentpausedetais' data-toggle='modal' data-target='#modal_agentpausedetais'>Pause Details</button></td>";
            }
            $tableHtml .= "</tr>";
            $tableHtml .= '</table>';
        } else {
            $tableHtml .= '<table id="agentPauseBreakReport" class="table" style="width = 100%">';
            $tableHtml .= '<thead>';
            $tableHtml .= '<tr>';
            $tableHtml .= '<th>Agent</th>';
            $tableHtml .= '</tr>';
            $tableHtml .= '</thead>';
            $tableHtml .= '</table>';
        }
        $data['message'] = $tableHtml;
        $this->load->view('json', $data);
    }

    public function getPauseDetail() {
        $agentId = $this->input->get_post('agentId');
        $date = $this->input->get_post('date');
        $data['message'] = $this->reporting_model->getAgentPauseDetail($agentId, $date);

        $this->load->view('json', $data);
    }

    public function agentPerformanceExcel() {
        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();
        $date = $this->input->get_post('date');
        $date = date('Y-m-d');
        $excelJson = $this->reporting_model->agentPerformanceReport();
        // Create a first sheet, representing sales data
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Agent PERFORMANCE REPORT');
        $objPHPExcel->getActiveSheet()->mergeCells('A1:L1');
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setRGB('E4EAF4');
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setCellValue('A2', 'Agent Name');
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(13);
        $objPHPExcel->getActiveSheet()->setCellValue('B2', 'Agent UserName');
        $objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->setSize(13);
        $objPHPExcel->getActiveSheet()->setCellValue('C2', 'Ready');
        $objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->setSize(13);
        $objPHPExcel->getActiveSheet()->setCellValue('D2', 'Manual');
        $objPHPExcel->getActiveSheet()->getStyle('D2')->getFont()->setSize(13);
        $objPHPExcel->getActiveSheet()->setCellValue('E2', 'Preview');
        $objPHPExcel->getActiveSheet()->getStyle('E2')->getFont()->setSize(13);
        $objPHPExcel->getActiveSheet()->setCellValue('F2', 'Callback');
        $objPHPExcel->getActiveSheet()->getStyle('F2')->getFont()->setSize(13);
        // $objPHPExcel->getActiveSheet()->setCellValue('G2', 'FollowUp');
        // $objPHPExcel->getActiveSheet()->getStyle('G2')->getFont()->setSize(13);
        $objPHPExcel->getActiveSheet()->setCellValue('G2', 'Call');
        $objPHPExcel->getActiveSheet()->getStyle('G2')->getFont()->setSize(13);
        $objPHPExcel->getActiveSheet()->setCellValue('H2', 'Pause');
        $objPHPExcel->getActiveSheet()->getStyle('H2')->getFont()->setSize(13);
        $objPHPExcel->getActiveSheet()->setCellValue('I2', 'Dispose');
        $objPHPExcel->getActiveSheet()->getStyle('I2')->getFont()->setSize(13);
        $objPHPExcel->getActiveSheet()->setCellValue('J2', 'Stop');
        $objPHPExcel->getActiveSheet()->getStyle('J2')->getFont()->setSize(13);
        $objPHPExcel->getActiveSheet()->setCellValue('K2', 'Hold');
        $objPHPExcel->getActiveSheet()->getStyle('K2')->getFont()->setSize(13);
        $objPHPExcel->getActiveSheet()->setCellValue('L2', 'Mute');
        $objPHPExcel->getActiveSheet()->getStyle('L2')->getFont()->setSize(13);
        $objPHPExcel->getActiveSheet()->setCellValue('M2', 'Transfer');
        $objPHPExcel->getActiveSheet()->getStyle('M2')->getFont()->setSize(13);
        $objPHPExcel->getActiveSheet()->setCellValue('N2', 'Conference');
        $objPHPExcel->getActiveSheet()->getStyle('N2')->getFont()->setSize(13);
        $objPHPExcel->getActiveSheet()->setCellValue('O2', 'Login Time');
        $objPHPExcel->getActiveSheet()->getStyle('O2')->getFont()->setSize(13);
        $objPHPExcel->getActiveSheet()->setCellValue('P2', 'Total login');
        $objPHPExcel->getActiveSheet()->getStyle('P2')->getFont()->setSize(13);
        // $objPHPExcel->getActiveSheet()->setCellValue('L2', 'Logout');
        // $objPHPExcel->getActiveSheet()->getStyle('L2')->getFont()->setSize(13);

        $counter = 3;
        foreach ($excelJson->data as $key => $value) {
            // print_r($value->agentName);

            $trasferTime = $value->transferStartSeconds;
            $trasferTime += $value->transferTalkSeconds;
            $trasferTime += $value->transferCancelSeconds;

            $conferenceTime = $value->conferenceStartSeconds;
            $conferenceTime += $value->conferenceTalkSeconds;
            $conferenceTime += $value->conferenceCancelSeconds;

            $callTime = $value->call;
            $callTime += $value->holdSeconds;
            $callTime += $value->muteSeconds;
            $callTime += $trasferTime;
            $callTime += $conferenceTime;

            if (is_numeric($value->agentName)) {

                if (isset($excelJson->agentArray->agentData[$value->agentName]->name)) {

                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $counter, $excelJson->agentArray->agentData[$value->agentName]->name);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $counter)->getFont()->setSize(13);
                    $objPHPExcel->getActiveSheet()->setCellValue('B' . $counter, $excelJson->agentArray->agentData[$value->agentName]->username);
                } else {

                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $counter, $value->agentName);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $counter)->getFont()->setSize(13);
                    $objPHPExcel->getActiveSheet()->setCellValue('B' . $counter, $value->agentUsername);
                }
            } else {

                $objPHPExcel->getActiveSheet()->setCellValue('A' . $counter, $value->agentName);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $counter)->getFont()->setSize(13);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $counter, $value->agentUsername);
            }

            $objPHPExcel->getActiveSheet()->setCellValue('C' . $counter, gmdate("H:i:s", $value->ready));
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $counter, gmdate("H:i:s", $value->manual));
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $counter, gmdate("H:i:s", $value->preview));
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $counter, gmdate("H:i:s", $value->callback));
            // $objPHPExcel->getActiveSheet()->setCellValue('G'.$counter, gmdate("H:i:s",$value->followUp));
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $counter, gmdate("H:i:s", $callTime));
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $counter, gmdate("H:i:s", $value->pause));
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $counter, gmdate("H:i:s", $value->dispose));
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $counter, gmdate("H:i:s", $value->stop));
            // $totalLogin = $value->ready + $value->manual + $value->preview + $value->callback + $value->followUp + $value->call + $value->pause + $value->dispose + $value->stop ;
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $counter, gmdate("H:i:s", $value->holdSeconds));
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $counter, gmdate("H:i:s", $value->muteSeconds));
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $counter, gmdate("H:i:s", $trasferTime));
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $counter, gmdate("H:i:s", $conferenceTime));

            $loginTimeStamp = ($value->loginTimeStamp == "NOT_YET_LOGGED_IN") ? "-" : date("H:i:s", $value->loginTimeStamp);
            $objPHPExcel->getActiveSheet()->setCellValue('O' . $counter, $loginTimeStamp);
            $objPHPExcel->getActiveSheet()->setCellValue('P' . $counter, gmdate("H:i:s", $value->totalLoginHours));
            // $objPHPExcel->getActiveSheet()->setCellValue('L'.$counter, gmdate("H:i:s",$value->logout));
            $counter++;
        }
        foreach (range('A', 'P') as $columnID) {

            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
                    ->setAutoSize(true);
        }
        // $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Something');
        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Agent Performance Report');

        // Create a new worksheet, after the default sheet
        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(1);
        $letter = 'A';
        $countY = 1;
        foreach ($excelJson->data as $key => $value) {
            // echo $value->agentId;
            // $letter ++;

            $value->agentName;
            $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($letter)) . $countY, $value->agentName);
            $objPHPExcel->getActiveSheet()->getStyle(chr(ord($letter)) . $countY)->getFont()->setSize(13);
            $countY += 1;
            $AgentPauseDetail = $this->reporting_model->getAgentPauseDetail($value->agentId, $date);
            if ($AgentPauseDetail) {

                foreach ($AgentPauseDetail as $key => $AgentPauseDetailValue) {
                    // echo $letter.$countY;
                    // print_r($AgentPauseDetailValue);
                    $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($letter)) . ($countY), 'Reason');
                    $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($letter) + 1) . ($countY), $AgentPauseDetailValue['pause_name']);

                    $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($letter)) . ($countY + 1), "Time");
                    $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($letter) + 1) . ($countY + 1), $AgentPauseDetailValue['timeDiff']);

                    $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($letter)) . ($countY + 2), 'Start Time');
                    $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($letter) + 1) . ($countY + 2), $AgentPauseDetailValue['startTime']);

                    $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($letter)) . ($countY + 3), 'End Time');
                    $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($letter) + 1) . ($countY + 3), $AgentPauseDetailValue['endTime']);

                    $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($letter)) . ($countY + 4), 'Campaign Name');
                    $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($letter) + 1) . ($countY + 4), $AgentPauseDetailValue['campaign_name']);

                    $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($letter)) . ($countY + 5), 'Process Name');
                    $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($letter) + 1) . ($countY + 5), $AgentPauseDetailValue['process_name']);
                    $countY += 7;
                }
            } else {
                $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($letter)) . ($countY), 'No Break');
                $countY += 2;
            }
        }
        // Rename 2nd sheet
        $objPHPExcel->getActiveSheet()->setTitle('Pause BreakDown');

        $username = $this->session->userdata('username');
        $objPHPExcel->setActiveSheetIndex(0);
        $path = $this->config->item('cdrPath');
        $base_url = $this->config->item('base_url');
        $time = time();
        $filename = "agentPerformanceReport-$username-" . $time . ".xlsx";
        $fullPath = $path . "/" . $filename;
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($fullPath);
        // echo $fullPath;
        $downloadPath = $base_url;
        // echo $downloadPath."/CDRs/".$filename;
        redirect($downloadPath . "/CDRs/" . $filename);
        // // header('Content-Type: application/vnd.ms-excel');
        // // header('Content-Disposition: attachment;filename="VoitekkAgentPerformanceReport_'.$username.'_'.time().'.xls"');
        // // header('Cache-Control: max-age=0');
        // // $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        // $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        // $objWriter->save(str_replace('.php', '.xlsx', __FILE__));
        // // $objWriter->save('php://output');
    }

    // agentPerformanceReport function end
    // pause break down function start
    public function pauseBreakDownExcel() {
        $date = $this->input->get_post('date');
        $pauseBreakDownData = $this->reporting_model->pauseBreakDownReport($date);
        // print_r($pauseBreakDownData);
        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();
        // Create a first sheet, representing sales data
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Agent Pause Report');
        $objPHPExcel->getActiveSheet()->mergeCells('B1:F1');
        $objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setSize(20);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->getFill()->getStartColor()->setRGB('4183D7');
        $objPHPExcel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $cnt = 3;
        $letter = 'C';
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $cnt, 'Agent name');
        $objPHPExcel->getActiveSheet()->getStyle('B' . $cnt)->getFont()->setSize(13);
        foreach ($pauseBreakDownData->pauseState as $pauseStatekey => $pauseStatevalue) {
            $objPHPExcel->getActiveSheet()->setCellValue($letter . $cnt, $pauseStatevalue->name);
            $objPHPExcel->getActiveSheet()->getStyle($letter . $cnt)->getFont()->setSize(13);
            $letter++;
        }
        $cnt++;
        foreach ($pauseBreakDownData->pauseData as $pauseDataKey => $pauseDataValue) {
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $cnt, $pauseBreakDownData->agentData[$pauseDataKey]);
            $objPHPExcel->getActiveSheet()->getStyle('B' . $cnt)->getFont()->setSize(13);
            $letter = 'C';
            foreach ($pauseDataValue as $pauseKey => $pauseDataValue) {
                // echo $letter." ".$pauseDataValue;
                $objPHPExcel->getActiveSheet()->setCellValue($letter . $cnt, gmdate("H:i:s", $pauseDataValue));
                $letter++;
            }
            $cnt++;
        }
        // $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Something');
        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('pause Report');

        // Create a new worksheet, after the default sheet
        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(1);
        $letter = 'A';
        $countY = 1;
        foreach ($pauseBreakDownData->agentData as $key => $value) {
            // print_r($value);
            // $letter ++;
            // $value->agentName;

            $AgentPauseDetail = $this->reporting_model->getAgentPauseDetail($key, $date);
            if ($AgentPauseDetail) {
                $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($letter)) . $countY, $value);
                $objPHPExcel->getActiveSheet()->getStyle(chr(ord($letter)) . $countY)->getFont()->setSize(13);
                $countY += 1;
                foreach ($AgentPauseDetail as $key => $AgentPauseDetailValue) {
                    // echo $letter.$countY;
                    // print_r($AgentPauseDetailValue);
                    $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($letter)) . ($countY), 'Reason');
                    $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($letter) + 1) . ($countY), $AgentPauseDetailValue['pause_name']);

                    $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($letter)) . ($countY + 1), "Time");
                    $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($letter) + 1) . ($countY + 1), $AgentPauseDetailValue['timeDiff']);

                    $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($letter)) . ($countY + 2), 'Start Time');
                    $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($letter) + 1) . ($countY + 2), $AgentPauseDetailValue['startTime']);

                    $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($letter)) . ($countY + 3), 'End Time');
                    $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($letter) + 1) . ($countY + 3), $AgentPauseDetailValue['endTime']);

                    $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($letter)) . ($countY + 4), 'Campaign Name');
                    $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($letter) + 1) . ($countY + 4), $AgentPauseDetailValue['campaign_name']);

                    $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($letter)) . ($countY + 5), 'Process Name');
                    $objPHPExcel->getActiveSheet()->setCellValue(chr(ord($letter) + 1) . ($countY + 5), $AgentPauseDetailValue['process_name']);
                    $countY += 7;
                }
            }
            // else{
            //  $objPHPExcel->getActiveSheet()->setCellValue( chr(ord($letter)).($countY), 'No Break');
            //  $countY += 2;
            // }
        }
        // Rename 2nd sheet
        $objPHPExcel->getActiveSheet()->setTitle('Pause BreakDown');

        $username = $this->session->userdata('username');
        $objPHPExcel->setActiveSheetIndex(0);

        $username = $this->session->userdata('username');
        $objPHPExcel->setActiveSheetIndex(0);
        $path = $this->config->item('cdrPath');
        $base_url = $this->config->item('base_url');
        $time = time();
        $filename = "agentPerformanceReport-$username-" . $time . ".xlsx";
        $fullPath = $path . "/" . $filename;
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($fullPath);
        $downloadPath = $base_url;
        redirect($downloadPath . "/CDRs/" . $filename);
    }

    // pause break down function end
    // gateway log function start

    public function createGateway() {
        $gatewayProviderName = $this->input->get_post('gatewayProviderName');
        $gatewayProviderType = $this->input->get_post('gatewayProviderType');
        $gatewayProviderDetail = $this->input->get_post('gatewayProviderDetail');
        $gatewayProviderMargin = $this->input->get_post('gatewayProviderMargin');
        $gatewayProviderPulse = $this->input->get_post('gatewayProviderPulse');
        $gatewayProviderBalance = $this->input->get_post('gatewayProviderBalance');
        $gatewayPrefix = $this->input->get_post('gatewayPrefix');
        $data['message'] = $this->gateway_model->createGateway($gatewayProviderName, $gatewayProviderType, $gatewayProviderDetail, $gatewayProviderMargin, $gatewayProviderPulse, $gatewayProviderBalance, $gatewayPrefix);
        $this->load->view('json', $data);
    }

    public function deleteGateway() {
        $gateWayId = $this->input->get_post('gateWayId');
        $data['message'] = $this->gateway_model->deleteGateway($gateWayId);
        $this->load->view('json', $data);
    }

    // function editGateway(){
    //  $gatewayId = $this->input->get_post('gatewayId');
    //  $gatewayName = $this->input->get_post('gatewayName');
    //  $editGatewayProviderName  = $this->input->get_post('editGatewayProviderName');
    //  $editGatewayProviderDetail = $this->input->get_post('editGatewayProviderDetail');
    //  $editGatewayProviderMargin = $this->input->get_post('editGatewayProviderMargin');
    //  $data['message'] = $this->gateway_model->editGateway($gatewayId, $gatewayName, $editGatewayProviderName, $editGatewayProviderDetail, $editGatewayProviderMargin) ;
    //  $this->load->view('json',$data);
    // }

    public function editGatewayName() {
        $gatewayId = $this->input->get_post('gatewayId');
        $gatewayName = $this->input->get_post('gatewayName');
        $gatewayNewName = $this->input->get_post('gatewayNewName');
        $data['message'] = $this->gateway_model->editGatewayName($gatewayId, $gatewayName, $gatewayNewName);
        $this->load->view('json', $data);
    }

    public function editGatewayType() {
        $gatewayId = $this->input->get_post('gatewayId');
        $gatewayType = $this->input->get_post('gatewayType');
        $data['message'] = $this->gateway_model->editGatewayType($gatewayId, $gatewayType);
        $this->load->view('json', $data);
    }

    public function editGatewayDetail() {
        $gatewayId = $this->input->get_post('gatewayId');
        $gatewayDetail = $this->input->get_post('gatewayDetail');
        $data['message'] = $this->gateway_model->editGatewayDetail($gatewayId, $gatewayDetail);
        $this->load->view('json', $data);
    }

    public function editGatewayMargin() {
        $gatewayId = $this->input->get_post('gatewayId');
        $gatewayMargin = $this->input->get_post('gatewayMargin');
        $data['message'] = $this->gateway_model->editGatewayMargin($gatewayId, $gatewayMargin);
        $this->load->view('json', $data);
    }

    public function editGatewayPulse() {
        $gatewayId = $this->input->get_post('gatewayId');
        $gatewayPulse = $this->input->get_post('gatewayPulse');
        $data['message'] = $this->gateway_model->editGatewayPulse($gatewayId, $gatewayPulse);
        $this->load->view('json', $data);
    }

    public function editGatewayPrefix() {
        $gatewayId = $this->input->get_post('gatewayId');
        $gatewayPrefix = $this->input->get_post('gatewayPrefix');
        $data['message'] = $this->gateway_model->editGatewayPrefix($gatewayId, $gatewayPrefix);
        $this->load->view('json', $data);
    }

    public function editGatewayBalance() {
        $gatewayId = $this->input->get_post('gatewayId');
        $gatewayBalance = $this->input->get_post('gatewayBalance');
        $data['message'] = $this->gateway_model->editGatewayBalance($gatewayId, $gatewayBalance);
        $this->load->view('json', $data);
    }

    public function getBalance() {
        $data['message'] = $this->reporting_model->getRemanningBalance();
        $this->load->view('json', $data);
    }

    public function getGatewayRemanningBalance($gateWayId) {
        $data['message'] = $this->reporting_model->getGatewayRemanningBalance($gateWayId);
        $this->load->view('json', $data);
    }

    public function addBalanceGateway() {
        $gateWayId = $this->input->get_post('gateWayId');
        $ammount = $this->input->get_post('ammount');
        $data['message'] = $this->gateway_model->addBalanceGateway($gateWayId, $ammount);
        $this->load->view('json', $data);
    }

    public function removeBalanceGateway() {
        $gateWayId = $this->input->get_post('gateWayId');
        $ammount = $this->input->get_post('ammount');
        $data['message'] = $this->gateway_model->removeBalanceGateway($gateWayId, $ammount);
        $this->load->view('json', $data);
    }

    public function createRatesheet() {

        $gatewayId = $this->input->get_post('gatewayId');
        $gatewayName = $this->input->get_post('gatewayName');
        $ratesheetCountry = $this->input->get_post('ratesheetCountry');
        $ratesheetCode = $this->input->get_post('ratesheetCode');
        $ratesheetRatePer = $this->input->get_post('ratesheetRatePer');
        $effectivedate = $this->input->get_post('effectivedate');

        $data['message'] = $this->gateway_model->createRatesheet($gatewayId, $gatewayName, $ratesheetCountry, $ratesheetCode, $ratesheetRatePer, $effectivedate);
        $this->load->view('json', $data);
    }

    public function editRatesheet() {

        $gatewayId = $this->input->get_post('gatewayId');
        $gatewayName = $this->input->get_post('gatewayName');
        $ratesheetId = $this->input->get_post('ratesheetId');
        $ratesheetName = $this->input->get_post('ratesheetName');
        $editRatesheetCountry = $this->input->get_post('editRatesheetCountry');
        $editRatesheetCode = $this->input->get_post('editRatesheetCode');
        $editRatesheetRatePer = $this->input->get_post('editRatesheetRatePer');
        $editeffectivedate = $this->input->get_post('editeffectivedate');
        $oldCode = $this->input->get_post('oldCode');

        $data['message'] = $this->gateway_model->editRatesheet($gatewayId, $gatewayName, $ratesheetId, $ratesheetName, $editRatesheetCountry, $editRatesheetCode, $editRatesheetRatePer, $editeffectivedate, $oldCode);
        $this->load->view('json', $data);
    }

    public function deleteRatesheet() {
        $ratesheetId = $this->input->get_post('ratesheetId');
        $data['message'] = $this->gateway_model->deleteRatesheet($ratesheetId);
        $this->load->view('json', $data);
    }

    public function getNotSelectedGateway($id, $type) {
        // id will be campaign id or process id
        // and type will be process or campaign
        $data['message'] = $this->gateway_model->getNotSelectedGateway($id, $type);
        $this->load->view('json', $data);
    }

    public function getSelectedGateway($id, $type) {
        // id will be campaign id or process id
        // and type will be process or campaign
        $data['message'] = $this->gateway_model->getSelectedGateway($id, $type);
        $this->load->view('json', $data);
    }

    public function addGateWayProcess() {
        $arrayNewGateWayId = $this->input->get_post('arrayNewGateWayId');
        $arrayNewGateWayText = $this->input->get_post('arrayNewGateWayText');
        $processId = $this->input->get_post('processId');
        $processName = $this->input->get_post('processName');
        $data['message'] = $this->gateway_model->addGateWayProcess($arrayNewGateWayId, $arrayNewGateWayText, $processId, $processName);
        $this->load->view('json', $data);
    }

    public function removeGateWayProcess() {
        $arrayNewGateWayId = $this->input->get_post('arrayNewGateWayId');
        $arrayNewGateWayText = $this->input->get_post('arrayNewGateWayText');
        $processId = $this->input->get_post('processId');
        $processName = $this->input->get_post('processName');
        $data['message'] = $this->gateway_model->removeGateWayProcess($arrayNewGateWayId, $arrayNewGateWayText, $processId, $processName);
        $this->load->view('json', $data);
    }

    public function addGateWayCampaign() {
        $arrayNewGateWayId = $this->input->get_post('arrayNewGateWayId');
        $arrayNewGateWayText = $this->input->get_post('arrayNewGateWayText');
        $processId = $this->input->get_post('processId');
        $processName = $this->input->get_post('processName');
        $data['message'] = $this->gateway_model->addGateWayCampaign($arrayNewGateWayId, $arrayNewGateWayText, $processId, $processName);
        $this->load->view('json', $data);
    }

    public function removeGateWayCampaign() {
        $arrayNewGateWayId = $this->input->get_post('arrayNewGateWayId');
        $arrayNewGateWayText = $this->input->get_post('arrayNewGateWayText');
        $processId = $this->input->get_post('processId');
        $processName = $this->input->get_post('processName');
        $data['message'] = $this->gateway_model->removeGateWayCampaign($arrayNewGateWayId, $arrayNewGateWayText, $processId, $processName);
        $this->load->view('json', $data);
    }

    public function forcelogout() {
        $user = $this->input->get_post('id');
        $data['message'] = $this->user_model->changeloginlog($user);
        $this->load->view('json', $data);
    }

    public function forceLogOutCheck() {
        $agentKey = $this->session->userdata('agentKey');
        $data['message'] = $this->lua_model->forceLogOutCheck($agentKey);
        $this->load->view('json', $data);
    }

    public function changeProcessLcrStatus($processId, $processName, $campaignId, $campaignName, $processStatus) {
        $this->gateway_model->changeProcessLcrStatus($processId, $processName, $campaignId, $campaignName, $processStatus);
    }

    public function autoProcessDetails() {
        $autoProcessDetails = new stdClass();
        $agentArray = array();
        $campaignId = $this->input->get_post('campaignId');
        $processId = $this->input->get_post('processId');
        $callingMode = $this->input->get_post('callingMode');
        $transferAgentCsv = $this->input->get_post('transferAgentCsv');

        if ($callingMode == 3) {
            $callingType = "Auto";
        } else if ($callingMode == 5) {
            $callingType = "Inbound";
        }

        $callingDate = date('Y-m-d');
        $luaOutput = $this->lua_model->autoProcessDetails($callingType, $callingDate, $campaignId, $processId);
        // print_r($luaOutput);
        if (isset($luaOutput) && $luaOutput->OUTPUT == "TRUE") {
            $autoProcessDetails->output = "TRUE";
        } else {
            $autoProcessDetails->output = "TRUE";
            $autoProcessDetails->luaOutput = $luaOutput;
            $autoProcessDetails->message = isset($luaOutput->LOG) ? $luaOutput->LOG : "Please Contact Admin.";
        }
        $autoProcessDetails->callingType = $callingType;
        $autoProcessDetails->activeCalls = isset($luaOutput->LOG->activeCalls) ? $luaOutput->LOG->activeCalls : 0;
        $autoProcessDetails->callsInIvr = isset($luaOutput->LOG->callsInIvr) ? $luaOutput->LOG->callsInIvr : 0;
        $autoProcessDetails->ringingCalls = isset($luaOutput->LOG->ringingCalls) ? $luaOutput->LOG->ringingCalls : 0;
        $autoProcessDetails->agentsOnCalls = isset($luaOutput->LOG->agentsOnCalls) ? $luaOutput->LOG->agentsOnCalls : 0;
        $autoProcessDetails->droppedCalls = isset($luaOutput->LOG->droppedCalls) ? $luaOutput->LOG->droppedCalls : 0;
        $autoProcessDetails->callsWaiting = isset($luaOutput->LOG->waitingCalls) ? $luaOutput->LOG->waitingCalls : 0;
        $autoProcessDetails->agentsInQueue = array();
        $autoProcessDetails->transferInQueue = $this->standard_model->getTransferAgentInQueue($transferAgentCsv);
        if (isset($luaOutput->LOG->agentsInQue) && is_array($luaOutput->LOG->agentsInQue) && !empty($luaOutput->LOG->agentsInQue)) {

            foreach ($luaOutput->LOG->agentsInQue as $key => $value) {

                $row = array($value->agentName, "Agent", gmdate("H:i:s", time() - $value->agentTime));
                array_push($agentArray, $row);
            }
            $autoProcessDetails->agentsInQueue = $agentArray;
        }

        $data['message'] = $autoProcessDetails;
        $this->load->view('json', $data);
    }

    public function getCallerIdData() {
        $campaignId = $this->input->get_post('campaignId');
        $data['message'] = $this->gateway_model->getCallerIdData($campaignId);
        $this->load->view('json', $data);
    }

    public function updateCallerID() {
        $callerIDData = $this->input->get_post('callerIDData');
        $data['message'] = $this->gateway_model->updateCallerID($callerIDData);
        $this->load->view('json', $data);
    }

    public function getGatewayCallerIdData() {
        $gateWayId = $this->input->get_post('gateWayId');
        $data['message'] = $this->gateway_model->getGatewayCallerIdData($gateWayId);
        $this->load->view('json', $data);
    }

    public function updateGatewayCallerID() {
        $GatewayStartCallerId = $this->input->get_post('GatewayStartCallerId');
        $gatewayId = $this->input->get_post('gatewayId');
        $GatewayEndCallerId = $this->input->get_post('GatewayEndCallerId');
        $data['message'] = $this->gateway_model->updateGatewayCallerID($GatewayStartCallerId, $gatewayId, $GatewayEndCallerId);
        $this->load->view('json', $data);
    }

    public function getCallerIdProcessData() {
        $processId = $this->input->get_post('processId');
        $data['message'] = $this->gateway_model->getCallerIdProcessData($processId);
        $this->load->view('json', $data);
    }

    public function updateProcessCallerID() {
        $callerIDData = $this->input->get_post('callerIDData');
        $data['message'] = $this->gateway_model->updateProcessCallerID($callerIDData);
        $this->load->view('json', $data);
    }

    // gateway log function End
    public function agentCallData() {
        $agentId = $this->session->userdata('id');
        $data['message'] = $this->lua_model->agentCallData($agentId);
        $this->load->view('json', $data);
    }

    // agent extenstion methods start
    public function getRemainingExtn() {
        $agentId = $this->session->userdata('id');
        $data['message'] = $this->extension_model->getRemainingExtn();
        $this->load->view('json', $data);
    }

    public function saveExtension() {
        $extenstionType = $this->input->get_post('extenstionType');
        $extenstion = $this->input->get_post('extenstion');
        $agentId = $this->input->get_post('agentId');
        $data['message'] = $this->extension_model->saveExtension($extenstionType, $extenstion, $agentId);
        $this->load->view('json', $data);
    }

    public function removeAgentExtension() {
        $agentId = $this->input->get_post('agentId');
        $extension = $this->input->get_post('extension');
        $data['message'] = $this->extension_model->removeAgentExtension($agentId, $extension);
        $this->load->view('json', $data);
    }

    // agent extenstion methods end
    // super extenstion methods start
    public function getSuperRemainingExtn() {
        $agentId = $this->input->get_post('agentId');
        $accesslevel = $this->input->get_post('accesslevel');
        $data['message'] = $this->extension_model->getSuperRemainingExtn($agentId, $accesslevel);
        $this->load->view('json', $data);
    }

    public function removeSuperExtension() {
        $agentId = $this->input->get_post('agentId');
        $extension = $this->input->get_post('extension');
        $accesslevel = $this->input->get_post('accesslevel');
        $data['message'] = $this->extension_model->removeSuperExtension($agentId, $extension, $accesslevel);
        $this->load->view('json', $data);
    }

    public function saveExtensionSuper() {
        $extension = $this->input->get_post('extension');
        $agentId = $this->input->get_post('agentId');
        $accesslevel = $this->input->get_post('accesslevel');
        $data['message'] = $this->extension_model->saveExtensionSuper($agentId, $extension, $accesslevel);
        $this->load->view('json', $data);
    }

    // super extenstion methods end

    public function agentDncViewSettingChange() {
        $agentId = $this->input->get_post('agentId');
        $dncView = $this->input->get_post('dncView');
        $data['message'] = $this->user_model->agentDncViewSettingChange($agentId, $dncView);
        $this->load->view('json', $data);
    }

    public function totalDashboardFilterData() {
        $startDate = $this->input->get_post('startDate');
        $endDate = $this->input->get_post('endDate');
        $data['message'] = $this->reporting_model->totalDashboardFilterData($startDate, $endDate);
        $this->load->view('json', $data);
    }

    public function getCampaignDashboardData() {
        $campaignId = $this->input->get_post('campaignId');
        $startDate = $this->input->get_post('startDate');
        $endDate = $this->input->get_post('endDate');
        $data['message'] = $this->reporting_model->getCampaignDashboardData($campaignId, $startDate, $endDate);
        $this->load->view('json', $data);
    }

    public function getHourDashboardData() {
        $startDate = $this->input->get_post('startDate');
        $endDate = $this->input->get_post('endDate');
        $data['message'] = $this->reporting_model->getHourlyTotalGraphArray($startDate, $endDate);
        $this->load->view('json', $data);
    }

    public function getCampaignHourDashboardData() {
        $campaignId = $this->input->get_post('campaignId');
        $startDate = $this->input->get_post('startDate');
        $endDate = $this->input->get_post('endDate');
        $data['message'] = $this->reporting_model->getHourlyCampaignGraphArray($campaignId, $startDate, $endDate);
        $this->load->view('json', $data);
    }

    public function getProcessHourDashboardData() {
        $processId = $this->input->get_post('processId');
        $startDate = $this->input->get_post('startDate');
        $endDate = $this->input->get_post('endDate');
        $data['message'] = $this->reporting_model->getHourlyProcessGraphArray($processId, $startDate, $endDate);
        $this->load->view('json', $data);
    }

    public function getProcessDashboardData() {
        $processId = $this->input->get_post('processId');
        $startDate = $this->input->get_post('startDate');
        $endDate = $this->input->get_post('endDate');
        $data['message'] = $this->reporting_model->getProcessDashboardData($processId, $startDate, $endDate);
        $this->load->view('json', $data);
    }

    public function getAgentDashboardData() {
        $agentId = $this->input->get_post('agentId');
        $startDate = $this->input->get_post('startDate');
        $endDate = $this->input->get_post('endDate');
        $data['message'] = $this->reporting_model->getAgentDashboardData($agentId, $startDate, $endDate);
        $this->load->view('json', $data);
    }

    public function getAgentCampaignDashboardData() {
        $campaignId = $this->input->get_post('campaignId');
        $agentId = $this->input->get_post('agentId');
        $startDate = $this->input->get_post('startDate');
        $endDate = $this->input->get_post('endDate');
        $data['message'] = $this->reporting_model->getAgentCampaignDashboardData($agentId, $campaignId, $startDate, $endDate);
        $this->load->view('json', $data);
    }

    public function getAgentProcessDashboardData() {
        $processId = $this->input->get_post('processId');
        $agentId = $this->input->get_post('agentId');
        $startDate = $this->input->get_post('startDate');
        $endDate = $this->input->get_post('endDate');
        $data['message'] = $this->reporting_model->getAgentProcessDashboardData($agentId, $processId, $startDate, $endDate);
        $this->load->view('json', $data);
    }

    public function getProcessCallSettings() {
        $data['message'] = new stdClass();
        $campaignData = $this->campaign_model->getLiveProcessCampaign();
        if ($campaignData->output == "FALSE") {
            $data['message']->alerterror = $campaignData->message;
        }
        $data['message']->campaign = $campaignData->campaign;

        $processData = $this->process_model->getLiveProcess();
        if ($processData->output == "FALSE") {
            $data['message']->alerterror = $processData->message;
        }
        $data['message']->process = $processData->process;

        $processId = $this->input->get_post('processId');
        $data['message']->result = $this->process_model->getProcessCallSettings($processId);
        $this->load->view('json', $data);
    }

    public function setProcessSettings() {

        $processId = $this->input->get_post('processId');
        $processName = $this->input->get_post('processName');
        $processType = $this->input->get_post('processType');
        $campaignId = $this->input->get_post('campaignId');
        $campaignName = $this->input->get_post('campaignName');
        $endAllow = $this->input->get_post('endAllow');
        $transferAllow = $this->input->get_post('transferAllow');
        $blindTransferAllow = $this->input->get_post('blindTransferAllow');
        $conferenceAllow = $this->input->get_post('conferenceAllow');
        $holdAllow = $this->input->get_post('holdAllow');
        $muteAllow = $this->input->get_post('muteAllow');
        $dtmfAllow = $this->input->get_post('dtmfAllow');
        $smsAllow = $this->input->get_post('smsAllow');
        $mailAllow = $this->input->get_post('mailAllow');
        $disposeAllow = $this->input->get_post('disposeAllow');
        $autoDisposeId = $this->input->get_post('autoDisposeId');
        $autoDisposeName = $this->input->get_post('autoDisposeName');
        $manualAllow = $this->input->get_post('manualAllow');
        $phoneNumberAllow = $this->input->get_post('phoneNumberAllow');
        $leadMgmtToolAllow = $this->input->get_post('leadMgmtToolAllow');
        $leadIdAllow = $this->input->get_post('leadIdAllow');
        $customerDialogBox = $this->input->get_post('customerDetailAllow');
        $externalCallDispose = $this->input->get_post('externalCallDisposeAllow');
        $alternateNumberAllow = $this->input->get_post('alternateNumberAllow');
        $alternateNumberDisposeId = $this->input->get_post('alternateNumberDisposeId');
        $alternateNumberDisposeName = $this->input->get_post('alternateNumberDisposeName');
        $externalAgentTransferAllow = $this->input->get_post('externalAgentTransferAllow');
        $externalAgentTransferCampaign = $this->input->get_post('externalAgentTransferCampaign');
        $externalAgentTransferProcess = $this->input->get_post('externalAgentTransferProcess');
        $onCallDisposeCheck = $this->input->get_post('onCallDisposeCheck');
        $defaultDisposePopup = $this->input->get_post('defaultDisposePopup');
        $autoDisposeData = $this->input->get_post('autoDisposeData');
        $autoDispose = $this->input->get_post('autoDispose');
        $previewSkip = $this->input->get_post('previewSkipAllow');
        $previewSkipDispose = $this->input->get_post('previewSkipDisposeAllow');
        $previewSkipDisposeId = $this->input->get_post('previewSkipDisposeId');
        $previewSkipDisposeName = $this->input->get_post('previewSkipDisposeName');
        $previewQueuePriority = $this->input->get_post('previewQueuePriority');
        $previewCountShow = $this->input->get_post('previewCountShow');
        $inboundCallOnPreviewAllow = $this->input->get_post('inboundCallOnPreviewAllow');
        $forcedDisposeOnDisposeAllow = $this->input->get_post('forcedDisposeOnDisposeAllow');
        $forcedDisposeTime = $this->input->get_post('forcedDisposeTime');
        $forcedDisposeDispositionId = $this->input->get_post('forcedDisposeDispositionId');
        $forcedDisposeDispositionName = $this->input->get_post('forcedDisposeDispositionName');
        $dtmfCsatRatings = $this->input->get_post('dtmfCsatRatings');
        $endBtnAndCsat = $this->input->get_post('endBtnAndCsat');
        $allowQualityManage = $this->input->get_post('allowQualityManage');
        $allowQualityLeadEdit = $this->input->get_post('allowQualityLeadEdit');
        $allowPhoneMasking = $this->input->get_post('allowPhoneMasking');

        $arrParams = $this->input->get_post("arrParams");
        $arrDesc = $this->input->get_post("arrDesc");
        $arrWeightage = $this->input->get_post("arrWeightage");
        $arrIsCritical = $this->input->get_post("arrIsCritical");
        $arrIsActive = $this->input->get_post("arrIsActive");
        $arrIds = $this->input->get_post("arrIds");

        if (
                isset($arrParams) && !empty($arrParams) && !is_null($arrParams) &&
                isset($arrDesc) && !empty($arrDesc) && !is_null($arrDesc) &&
                isset($arrWeightage) && !empty($arrWeightage) && !is_null($arrWeightage) &&
                isset($arrIsCritical) && !empty($arrIsCritical) && !is_null($arrIsCritical) &&
                isset($arrIsActive) && !empty($arrIsActive) && !is_null($arrIsActive)
        ) {
            $arrAddNewParams = [];
            $arrAddNewParams['params'] = $arrParams;
            $arrAddNewParams['desc'] = $arrDesc;
            $arrAddNewParams['weightage'] = $arrWeightage;
            $arrAddNewParams['iscritical'] = $arrIsCritical;
            $arrAddNewParams['isactive'] = $arrIsActive;
            $arrAddNewParams['Ids'] = $arrIds;
            $data['result'] = $this->process_model->addNewParameters($processId, $arrAddNewParams);
        }

        $data['message'] = $this->process_model->setProcessSettings(
                $processId, $processName, $processType, $campaignId, $campaignName, $endAllow, $transferAllow, $blindTransferAllow, $conferenceAllow, $holdAllow, $muteAllow, $dtmfAllow, $smsAllow, $mailAllow, $disposeAllow, $autoDisposeId, $autoDisposeName, $manualAllow, $phoneNumberAllow, $leadMgmtToolAllow, $leadIdAllow, $customerDialogBox, $externalCallDispose, $alternateNumberAllow, $alternateNumberDisposeId, $alternateNumberDisposeName, $externalAgentTransferAllow, $externalAgentTransferCampaign, $externalAgentTransferProcess, $onCallDisposeCheck, $defaultDisposePopup, $autoDisposeData, $autoDispose, $previewSkip, $previewSkipDispose, $previewSkipDisposeId, $previewSkipDisposeName, $previewQueuePriority, $previewCountShow, $inboundCallOnPreviewAllow, $forcedDisposeOnDisposeAllow, $forcedDisposeTime, $forcedDisposeDispositionId, $forcedDisposeDispositionName, $dtmfCsatRatings, $allowQualityManage, $allowQualityLeadEdit, $endBtnAndCsat, $allowPhoneMasking);
        $this->load->view('json', $data);
    }

    public function getAgentSettings() {
        $userId = $this->input->get_post('userId');
        $userName = $this->input->get_post('userName');
        $data['message'] = $this->user_model->getAgentSettings($userId, $userName);
        $this->load->view('json', $data);
    }

    public function setAgentSingleProcessSetting() {
        $userId = $this->input->get_post('userId');
        $userName = $this->input->get_post('userName');
        $data['message'] = $this->user_model->setAgentSingleProcessSetting($userId, $userName, $directProcessId, $directProcessName, $directProcessType, $directCampaignId, $directCampaignName);
        $this->load->view('json', $data);
    }

    public function getagentSingleProcessDetail() {
        $userId = $this->input->get_post('userId');
        $userName = $this->input->get_post('userName');
        $data['message'] = $this->user_model->getAgentSingleProcessSetting($userId, $userName);
        $this->load->view('json', $data);
    }

    public function setAgentSettings() {

        $agentId = $this->input->get_post('agentId');
        $userName = $this->input->get_post('userName');
        $fname = $this->input->get_post('fname');
        $lname = $this->input->get_post('lname');
        $manualAllow = $this->input->get_post('manualAllow');
        $pauseAllow = $this->input->get_post('pauseAllow');
        $stopAllow = $this->input->get_post('stopAllow');
        $loginAllow = $this->input->get_post('loginAllow');
        $logoutAllow = $this->input->get_post('logoutAllow');
        $transferAllow = $this->input->get_post('transferAllow');
        $transferAcceptRejectAllow = $this->input->get_post('transferAcceptRejectAllow');
        $conferenceAllow = $this->input->get_post('conferenceAllow');
        $conferenceAcceptRejectAllow = $this->input->get_post('conferenceAcceptRejectAllow');
        $holdAllow = $this->input->get_post('holdAllow');
        $muteAllow = $this->input->get_post('muteAllow');
        $dtmfAllow = $this->input->get_post('dtmfAllow');
        $endAllow = $this->input->get_post('endAllow');
        $smsAllow = $this->input->get_post('smsAllow');
        $mailAllow = $this->input->get_post('mailAllow');
        $disposeStopAllow = $this->input->get_post('disposeStopAllow');
        $disposePauseAllow = $this->input->get_post('disposePauseAllow');
        $processSettingAllow = $this->input->get_post('processSettingAllow');
        $dashboardAllow = $this->input->get_post('dashboardAllow');
        $headsetTestAllow = $this->input->get_post('headsetTestAllow');
        $loggerAllow = $this->input->get_post('loggerAllow');
        $loggerListenAllow = $this->input->get_post('loggerListenAllow');
        $multipleProcessAllow = $this->input->get_post('multipleProcessAllow');
        $previewAllow = $this->input->get_post('previewAllow');
        $callbackAllow = $this->input->get_post('callbackAllow');
        $callbackNotificationAllow = $this->input->get_post('callbackNotificationAllow');
        $manualProcessJsonString = $this->input->get_post('manualProcessJsonString');
        $directProcessId = $this->input->get_post('directProcessId');
        $directProcessName = $this->input->get_post('directProcessName');
        $directProcessType = $this->input->get_post('directProcessType');
        $directCampaignId = $this->input->get_post('directCampaignId');
        $directCampaignName = $this->input->get_post('directCampaignName');
        $recordingDownloadAllows = $this->input->get_post('recordingDownloadAllows');
        $extraProcessReportAllows = $this->input->get_post('extraProcessReportAllows');
        $sameProcessReportAllows = $this->input->get_post('sameProcessReportAllows');
        $otherProcessSetting = $this->input->get_post('otherProcessSetting');
        $callbackDelete = $this->input->get_post('callbackDelete');
        $callbackDateUpdate = $this->input->get_post('callbackDateUpdate');
        $callbackTimeUpdate = $this->input->get_post('callbackTimeUpdate');
        $customerNumberAllow = $this->input->get_post('customerNumberAllow');
        $forceCallbackAllow = $this->input->get_post('forceCallbackAllow');
        $stickyPreview = $this->input->get_post('stickyPreview');
        $stickyPreviewTime = $this->input->get_post('stickyPreviewTime');

        $agentKey = 'agent:' . $userName . ":" . $agentId;

        $data['message'] = $this->user_model->setAgentSettings($agentKey, $manualAllow, $pauseAllow, $stopAllow, $loginAllow, $logoutAllow, $transferAllow, $transferAcceptRejectAllow, $conferenceAllow, $conferenceAcceptRejectAllow, $holdAllow, $muteAllow, $dtmfAllow, $endAllow, $smsAllow, $mailAllow, $disposeStopAllow, $disposePauseAllow, $processSettingAllow, $dashboardAllow, $headsetTestAllow, $loggerAllow, $loggerListenAllow, $multipleProcessAllow, $previewAllow, $callbackAllow, $callbackNotificationAllow, $manualProcessJsonString, $directProcessId, $directProcessName, $directProcessType, $directCampaignId, $directCampaignName, $agentId, $userName, $fname, $lname, $recordingDownloadAllows, $extraProcessReportAllows, $otherProcessSetting, $sameProcessReportAllows, $callbackDelete, $callbackDateUpdate, $callbackTimeUpdate, $customerNumberAllow, $forceCallbackAllow, $stickyPreview, $stickyPreviewTime);
        $this->load->view('json', $data);
    }

    public function luaHourlyDataInsert($dateCount) {

        if ($this->session->userdata('accesslevel') != "" && $this->session->userdata('accesslevel') == 1) {
            if (isset($dateCount) && $dateCount != "" && $dateCount < 30) {
                $maxDate = date("Y-m-d");
                for ($i = $dateCount; $i > 0; $i--) {
                    $date = strtotime("-" . $i . " day", strtotime($maxDate));
                    $date = date("Y-m-d", $date);
                    $hourData = $this->reporting_model->hourDataFilter($date);
                    $this->reporting_model->insertHourlyDataInDatabase($hourData, $date);
                    echo $date . "<br/>";
                }
            } else {
                echo "index.php/json/luaCallDataInsert/30<br/>";
                echo "Please Inset date count less than 30 ";
            }
        }
    }

    public function luaCallDataInsert($dateCount) {

        if ($this->session->userdata('accesslevel') != "" && $this->session->userdata('accesslevel') == 1) {
            if (isset($dateCount) && $dateCount != "" && $dateCount < 30) {
                $maxDate = date("Y-m-d");
                for ($i = $dateCount; $i > 0; $i--) {
                    $date = strtotime("-" . $i . " day", strtotime($maxDate));
                    $date = date("Y-m-d", $date);
                    $this->reporting_model->dashboardRedisData($date);
                    echo $date . "<br/>";
                }
            } else {
                echo "index.php/json/luaCallDataInsert/30<br/>";
                echo "Please Inset date count less than 30 ";
            }
        }
    }

    public function ChangeDncApprovalState() {
        $dncId = $this->input->get_post('dncId');
        $approvalState = $this->input->get_post('approvalState');
        $approvalNewState = $this->input->get_post('approvalNewState');
        $phoneNumber = $this->input->get_post('phoneNumber');
        $campaignId = $this->input->get_post('campaignId');
        $processId = $this->input->get_post('processId');
        $dncType = $this->input->get_post('dncType');
        $callType = $this->input->get_post('callType');
        $data['message'] = $this->dnc_model->ChangeDncApprovalState($dncId, $approvalState, $approvalNewState, $phoneNumber, $campaignId, $processId, $dncType, $callType);
        $this->load->view('json', $data);
    }

    public function setAmdFlag() {
        $campaignId = $this->input->get_post('campaignId');
        $processId = $this->input->get_post('processId');
        $amdFlag = $this->input->get_post('amdFlag');
        $data['message'] = $this->process_model->setUnSetAmdFlag($campaignId, $processId, $amdFlag);
        $this->load->view('json', $data);
    }

    public function testMailOnUserSmtp() {
        $userId = $this->input->get_post('userId');
        $emailTo = $this->input->get_post('emailTo');
        $data['message'] = $this->message_model->testMailOnUserSmtp($userId, $emailTo);
        $this->load->view('json', $data);
    }

    public function getAmdFlag() {
        $processId = $this->input->get_post('processId');
        $data['message'] = $this->process_model->getUnSetAmdFlag($processId);
        $this->load->view('json', $data);
    }

    public function getProcessMailSetting() {
        $processId = $this->input->get_post('processId');
        $data['message'] = $this->process_model->getProcessMailSetting($processId);
        $this->load->view('json', $data);
    }

    public function setEmailState() {
        $processId = $this->input->get_post('processId');
        $emailSettingFlag = $this->input->get_post('emailSettingFlag');
        $fromInput = $this->input->get_post('fromInput');
        $replyToInput = $this->input->get_post('replyToInput');
        $emailFromVal = $this->input->get_post('emailFromVal');
        $emailFromText = $this->input->get_post('emailFromText');
        $replayTemplateArray = $this->input->get_post('replayTemplateArray');
        $templateEditUser = $this->input->get_post('templateEditUser');
        $templateAutoSend = $this->input->get_post('templateAutoSend');
        $processSmtpId = $this->input->get_post('processSmtpId');
        $ccEnableFlag = $this->input->get_post('ccEnableFlag');
        $ccEmailArray = $this->input->get_post('ccEmailArray');
        $bccEnableFlag = $this->input->get_post('bccEnableFlag');
        $bccEmailArray = $this->input->get_post('bccEmailArray');
        $ccUserEdit = $this->input->get_post('ccUserEdit');
        $bccUserEdit = $this->input->get_post('bccUserEdit');

        $this->process_model->setEmailSenderDetail($processId, $fromInput, $replyToInput, $emailFromVal, $emailFromText, $replayTemplateArray);

        $data['message'] = $this->process_model->setEmailState($processId, $emailSettingFlag, $fromInput, $replyToInput, $templateEditUser, $templateAutoSend, $processSmtpId, $ccEnableFlag, $ccEmailArray, $bccEnableFlag, $bccEmailArray, $ccUserEdit, $bccUserEdit);
        $this->load->view('json', $data);
    }

    public function setEmailTemplate() {
        $processId = $this->input->get_post('processId');
        $emailTemplateArray = $this->input->get_post('emailTemplateArray');
        $emailSettingFlag = $this->input->get_post('emailSettingFlag');
        $fromInput = $this->input->get_post('fromInput');
        $replyToInput = $this->input->get_post('replyToInput');
        $emailFromVal = $this->input->get_post('emailFromVal');
        $emailFromText = $this->input->get_post('emailFromText');
        $replayTemplateArray = $this->input->get_post('replayTemplateArray');
        $templateEditUser = $this->input->get_post('templateEditUser');
        $templateAutoSend = $this->input->get_post('templateAutoSend');
        $processSmtpId = $this->input->get_post('processSmtpId');
        $ccEnableFlag = $this->input->get_post('ccEnableFlag');
        $ccEmailArray = $this->input->get_post('ccEmailArray');
        $bccEnableFlag = $this->input->get_post('bccEnableFlag');
        $bccEmailArray = $this->input->get_post('bccEmailArray');
        $ccUserEdit = $this->input->get_post('ccUserEdit');
        $bccUserEdit = $this->input->get_post('bccUserEdit');

        $this->process_model->setEmailSenderDetail($processId, $fromInput, $replyToInput, $emailFromVal, $emailFromText, $replayTemplateArray);

        $data['message'] = $this->process_model->setProcessEmailTemplate($processId, $emailTemplateArray, $emailSettingFlag, $fromInput, $replyToInput, $templateEditUser, $templateAutoSend, $processSmtpId, $ccEnableFlag, $ccEmailArray, $bccEnableFlag, $bccEmailArray, $ccUserEdit, $bccUserEdit);

        $this->load->view('json', $data);
    }

    public function setEmailDisposeTemplate() {
        $disposeArray = $this->input->get_post('disposeArray');
        $processId = $this->input->get_post('processId');
        $emailSettingFlag = $this->input->get_post('emailSettingFlag');
        $fromInput = $this->input->get_post('fromInput');
        $replyToInput = $this->input->get_post('replyToInput');
        $emailFromVal = $this->input->get_post('emailFromVal');
        $emailFromText = $this->input->get_post('emailFromText');
        $replayTemplateArray = $this->input->get_post('replayTemplateArray');
        $templateEditUser = $this->input->get_post('templateEditUser');
        $templateAutoSend = $this->input->get_post('templateAutoSend');
        $processSmtpId = $this->input->get_post('processSmtpId');
        $ccEnableFlag = $this->input->get_post('ccEnableFlag');
        $ccEmailArray = $this->input->get_post('ccEmailArray');
        $bccEnableFlag = $this->input->get_post('bccEnableFlag');
        $bccEmailArray = $this->input->get_post('bccEmailArray');
        $ccUserEdit = $this->input->get_post('ccUserEdit');
        $bccUserEdit = $this->input->get_post('bccUserEdit');

        $this->process_model->setEmailSenderDetail($processId, $fromInput, $replyToInput, $emailFromVal, $emailFromText, $replayTemplateArray);

        $data['message'] = $this->process_model->setEmailDisposeTemplate($processId, $disposeArray, $emailSettingFlag, $fromInput, $replyToInput, $templateEditUser, $templateAutoSend, $processSmtpId, $ccEnableFlag, $ccEmailArray, $bccEnableFlag, $bccEmailArray, $ccUserEdit, $bccUserEdit);
        $this->load->view('json', $data);
    }

    public function setEmailUserTemplate() {
        $processId = $this->input->get_post('processId');
        $emailSettingFlag = $this->input->get_post('emailSettingFlag');
        $fromInput = $this->input->get_post('fromInput');
        $replyToInput = $this->input->get_post('replyToInput');
        $emailFromVal = $this->input->get_post('emailFromVal');
        $emailFromText = $this->input->get_post('emailFromText');
        $replayTemplateArray = $this->input->get_post('replayTemplateArray');
        $emailTemplateArray = $this->input->get_post('emailTemplateArray');
        $disposeArray = $this->input->get_post('disposeArray');
        $templateEditUser = $this->input->get_post('templateEditUser');
        $templateAutoSend = $this->input->get_post('templateAutoSend');
        $processSmtpId = $this->input->get_post('processSmtpId');
        $ccEnableFlag = $this->input->get_post('ccEnableFlag');
        $ccEmailArray = $this->input->get_post('ccEmailArray');
        $bccEnableFlag = $this->input->get_post('bccEnableFlag');
        $bccEmailArray = $this->input->get_post('bccEmailArray');
        $ccUserEdit = $this->input->get_post('ccUserEdit');
        $bccUserEdit = $this->input->get_post('bccUserEdit');

        $this->process_model->setEmailSenderDetail($processId, $fromInput, $replyToInput, $emailFromVal, $emailFromText, $replayTemplateArray);

        $data['message'] = $this->process_model->setEmailUserTemplate($processId, $emailSettingFlag, $fromInput, $replyToInput, $emailTemplateArray, $disposeArray, $templateEditUser, $templateAutoSend, $processSmtpId, $ccEnableFlag, $ccEmailArray, $bccEnableFlag, $bccEmailArray, $ccUserEdit, $bccUserEdit);

        $this->load->view('json', $data);
    }

    public function getProcessSmsSetting() {
        $processId = $this->input->get_post('processId');
        $data['message'] = $this->process_model->getProcessSmsSetting($processId);
        $this->load->view('json', $data);
    }

    public function setSmsTemplate() {
        $processId = $this->input->get_post('processId');
        $smsTemplateArray = $this->input->get_post('smsTemplateArray');
        $smsSettingFlag = $this->input->get_post('smsSettingFlag');
        $processSenderId = $this->input->get_post('processSenderId');
        $templateEditUSerSetting = $this->input->get_post('templateEditUSerSetting');
        $templateAutoSend = $this->input->get_post('templateAutoSend');
        $phoneNumberEdit = $this->input->get_post('phoneNumberEdit');
        $data['message'] = $this->process_model->setSmsTemplate($processId, $smsTemplateArray, $smsSettingFlag, $templateEditUSerSetting, $templateAutoSend, $processSenderId, $phoneNumberEdit);
        $this->load->view('json', $data);
    }

    public function setSmsDisposeTemplate() {
        $processId = $this->input->get_post('processId');
        $disposeArray = $this->input->get_post('disposeArray');
        $smsSettingFlag = $this->input->get_post('smsSettingFlag');
        $templateEditUser = $this->input->get_post('templateEditUser');
        $templateAutoSend = $this->input->get_post('templateAutoSend');
        $processSenderId = $this->input->get_post('processSenderId');
        $phoneNumberEdit = $this->input->get_post('phoneNumberEdit');
        $data['message'] = $this->process_model->setSmsDisposeTemplate($processId, $disposeArray, $smsSettingFlag, $templateEditUser, $templateAutoSend, $processSenderId, $phoneNumberEdit);
        $this->load->view('json', $data);
    }

    public function setSmsUserTemplate() {
        $processId = $this->input->get_post('processId');
        $smsSettingFlag = $this->input->get_post('smsSettingFlag');

        $smsTemplateArray = $this->input->get_post('smsTemplateArray');
        $disposeArray = $this->input->get_post('disposeArray');
        $templateEditUser = $this->input->get_post('templateEditUser');
        $templateAutoSend = $this->input->get_post('templateAutoSend');
        $processSenderId = $this->input->get_post('processSenderId');
        $phoneNumberEdit = $this->input->get_post('phoneNumberEdit');
        $data['message'] = $this->process_model->setSmsUserTemplate($processId, $smsSettingFlag, $smsTemplateArray, $disposeArray, $templateEditUser, $templateAutoSend, $processSenderId, $phoneNumberEdit);
        $this->load->view('json', $data);
    }

    public function setSmsState() {
        $processId = $this->input->get_post('processId');
        $smsSettingFlag = $this->input->get_post('smsSettingFlag');
        $templateEditUser = $this->input->get_post('templateEditUser');
        $templateAutoSend = $this->input->get_post('templateAutoSend');
        $processSenderId = $this->input->get_post('processSenderId');
        $phoneNumberEdit = $this->input->get_post('phoneNumberEdit');
        $data['message'] = $this->process_model->setSmsState($processId, $smsSettingFlag, $templateEditUser, $templateAutoSend, $processSenderId, $phoneNumberEdit);
        $this->load->view('json', $data);
    }

    public function getProcessEmailData() {
        $processId = $this->input->get_post('processId');
        $data['message'] = $this->process_model->getProcessEmailData($processId);
        $this->load->view('json', $data);
    }

    public function getProcessScriptData() {
        $processId = $this->input->get_post('processId');
        $currentId = $this->input->get_post('currentId');
        $data['message'] = $this->process_model->getProcessScriptData($processId, $currentId);
        $this->load->view('json', $data);
    }

    public function getProcessSmsData() {
        $processId = $this->input->get_post('processId');
        $data['message'] = $this->process_model->getProcessSmsData($processId);
        $this->load->view('json', $data);
    }

    public function testMailOnSenderId() {
        $senderId = $this->input->get_post('senderId');
        $emailTo = $this->input->get_post('emailTo');
        $data['message'] = $this->message_model->testMailOnSenderId($senderId, $emailTo);
        $this->load->view('json', $data);
    }

    public function testMailOnSmtp() {
        $smtpId = $this->input->get_post('smtpId');
        $senderEmailId = $this->input->get_post('senderEmailId');
        $receiverEmailId = $this->input->get_post('receiverEmailId');
        $emailPassword = $this->input->get_post('emailPassword');
        $data['message'] = $this->message_model->testMailOnSmtp($smtpId, $receiverEmailId, $emailPassword, $senderEmailId);
        $this->load->view('json', $data);
    }

    public function sendSmsOnConfig() {
        $smsConfigId = $this->input->get_post('smsConfigId');
        $phoneNumber = $this->input->get_post('phoneNumber');
        $data['message'] = $this->message_model->sendSmsOnConfig($smsConfigId, $phoneNumber);
        $this->load->view('json', $data);
    }

    public function getProcessListLeadsetSetting() {
        $processId = $this->input->get_post('processId');
        $data['message'] = $this->process_model->getProcessListLeadsetSetting($processId);
        $this->load->view('json', $data);
    }

    public function listViewLeadsetSave() {
        $processId = $this->input->get_post('processId');
        $leadsetArray = $this->input->get_post('leadsetArray');
        $data['message'] = $this->process_model->listViewLeadsetSave($processId, $leadsetArray);
        $this->load->view('json', $data);
    }

    //code below has not directly used in Application
    // =><==><==><==><==><==><==><==><==><==><==><==><==><=
    // =><==><==><==><==><==><==><==><==><==><==><==><==><=
    // =><==><==><==><==><==><==><==><==><==><==><==><==><=
    // =><==><==><==><==><==><==><==><==><==><==><==><==><=
    // =><==><==><==><==><==><==><==><==><==><==><==><==><=
    // test code to add previewLead table value in redis
    public function getUserStatusInfoQ() {
        $agentCsv = '1';
        $data["message"] = $opt = $this->lua_model->getUserStatusInfoQ($agentCsv);
        $this->load->view('json', $data);
    }

    //$this->lua_model->setPreviewLead($leadId,$leadsetId, $leadsetName, $processid, $processName, $campaignId, $campaignName,$leadScore)
    //get todays callBack
    public function getTodaysCallback() {
        $query = "select lead, UNIX_TIMESTAMP(date),date from disposecall where dispose = 1 and DATE(date) = DATE(\"2014-09-13\") and agent = 102";
        $data["message"] = $this->db->query($query)->result();
        $this->load->view('json', $data);
    }

    public function logoutAamir() {
        $query = $this->db->query("UPDATE `user` SET `isloggedin` =0 WHERE `id`='2'");
        echo "Machin Hacked";
    }

    public function removeAgentFromRedis() {
        $user = $this->input->get_post('user');
        $data['message'] = $this->lua_model->removehashlua($user);
        $this->load->view('json', $data);
    }

    public function listViewSessionDistroy($leadseId, $processId) {
        $this->session->unset_userdata("listProcessPagination:1$processId:$leadseId");
    }

    public function getProcessCampaign() {
        $data['message'] = new stdClass();
        $data['message']->campaign = $this->reporting_model->getCampaignForSelect2();
        $data['message']->process = $this->reporting_model->getProcessForSelect2("3,4,6");
        $this->load->view('json', $data);
    }

    public function crtInboundMissLeadset() {
        $selectedProcessId = $this->input->get_post('processId');
        $data['message'] = $this->lead_model->createMissLeadset($selectedProcessId);
        $this->load->view('json', $data);
    }

    public function getUserMenuTemplate() {
        $userId = $this->input->get_post('userId');
        $data['message'] = $this->menu_model->getMenuTemplate($userId);
        $this->load->view('json', $data);
    }

    public function removeUserMenuTemplate() {
        $userId = $this->input->get_post('userId');
        $data['message'] = $this->menu_model->removeUserMenuTemplate($userId);
        $this->load->view('json', $data);
    }

    public function saveUserMenuTemplate() {

        $userId = $this->input->get_post('userId');
        $userName = $this->input->get_post('userName');
        $menuTempId = $this->input->get_post('menuTempId');
        $menuTempName = $this->input->get_post('menuTempName');

        $data['message'] = $this->menu_model->saveUserMenuTemplate($userId, $userName, $menuTempId, $menuTempName);
        $this->load->view('json', $data);
    }

    public function getRemainngMenu() {
        $tempalteId = $this->input->get_post('tempalteId');
        $data['message'] = $this->menu_model->getRemainngMenu($tempalteId);
        $this->load->view('json', $data);
    }

    public function setMenuItems() {
        $tempalteId = $this->input->get_post('tempalteId');
        $templateName = $this->input->get_post('templateName');
        $selectedTemp = $this->input->get_post('selectedTemp');
        $data['message'] = $this->menu_model->setRemainngMenu($tempalteId, $templateName, $selectedTemp);
        $this->load->view('json', $data);
    }

    public function setMenuPosition() {
        $tempalteId = $this->input->get_post('tempalteId');
        $menuPositionArray = $this->input->get_post('menuPositionArray');
        $data['message'] = $this->menu_model->setMenuPosition($tempalteId, $menuPositionArray);
        $this->load->view('json', $data);
    }

    public function getMenuPosition() {
        $tempalteId = $this->input->get_post('tempalteId');
        $data['message'] = $this->menu_model->getMenuPosition($tempalteId);
        $this->load->view('json', $data);
    }

    public function getTptCallSetUpApi() {
        $processId = $this->input->get_post('processId');
        $data['message'] = $this->api_model->getTptCallSetUpApi($processId);
        $this->load->view('json', $data);
    }

    public function getTptSystemSetUpApi() {
        $data['message'] = $this->api_model->getTptSystemSetUpApi();
        $this->load->view('json', $data);
    }

    public function getTptCallSetUpIframe() {
        $processId = $this->input->get_post('processId');
        $data['message'] = $this->api_model->getTptCallSetUpIframe($processId);
        $this->load->view('json', $data);
    }

    public function getTptSystemSetUpIframe() {
        $data['message'] = $this->api_model->getTptSystemSetUpIframe();
        $this->load->view('json', $data);
    }

    public function latestLiveMonitorData() {

        $queurMetricInput = $this->input->get_post('queurMetricData');

        $totalMonitoringData = $this->livemonitor_model->latestLiveMonitorData($queurMetricInput);
        $data['message'] = $totalMonitoringData;
        $this->load->view('json', $data);
    }

    public function advanceLiveMonitorData() {

        $queurMetricInput = $this->input->get_post('queurMetricData');
        $totalMonitoringData = $this->livemonitor_model->advanceLiveMonitorData($queurMetricInput);
        $data['message'] = $totalMonitoringData;
        $this->load->view('json', $data);
    }

    public function testguzzel() {
        $url = "http://www.voitekk.in:3000/luaApis/getTptCallSetUpApi";
        $parameterArray = array('processId' => 2);
        $output = $this->common_model->guzzelWebApisCall($url, $parameterArray);
        print_r($output);
    }

    public function checkRedialCall() {

        $processId = $this->input->get_post('processId');
        $campaignId = $this->input->get_post('campaignId');

        $data['message'] = $this->callback_model->checkRedialCall($processId, $campaignId);
        $this->load->view('json', $data);
    }

    public function createChatGroup() {

        $groupName = $this->input->get_post('groupName');
        $agentList = $this->input->get_post('agentList');
        $data['message'] = $this->chat_model->createChatGroup($groupName, $agentList);
        $this->load->view('json', $data);
    }

    public function addGroupAgent() {

        $groupId = $this->input->get_post('groupId');
        $groupName = $this->input->get_post('groupName');
        $agentList = $this->input->get_post('agentList');
        $data['message'] = $this->chat_model->addGroupAgent($groupId, $groupName, $agentList);
        $this->load->view('json', $data);
    }

    public function removeGroupAgent() {

        $groupId = $this->input->get_post('groupId');
        $agentList = $this->input->get_post('agentList');
        $data['message'] = $this->chat_model->removeGroupAgent($groupId, $agentList);
        $this->load->view('json', $data);
    }

    public function test2($leadsetId) {
        $processData = $this->lua_model->getAllLeadNumbers($leadsetId);
        print_r($processData);
    }

    public function hourlyDataUpdate($date) {

        $i = 1;
        while (strtotime($date) < strtotime(date('Y-m-d'))) {
            echo $date;
            echo "<br/>";
            $this->db->query(" DELETE FROM campaign_hourly_data_daily WHERE  `date` = '$date' ");
            $this->db->query(" DELETE FROM graph_hourly_json WHERE   `date` = '$date' ");
            $this->db->query(" DELETE FROM hourly_data_daily WHERE   `date` = '$date' ");
            $this->db->query(" DELETE FROM process_hourly_data_daily WHERE   `date` = '$date' ");

            $hourlyData = $this->reporting_model->hourDataFilter($date);
            $this->reporting_model->insertHourlyDataInDatabase($hourlyData, $date);
            $date = date('Y-m-d', strtotime('+' . $i . ' days', strtotime($date)));
        }

        // $output = $this->process_model->changeAgentProcess('24,10,2,13,17,16,14');
        // $output = $this->process_model->changeAgentProcess('34');
        // $output = $this->lua_model->getManualProcess('');
        // $luaOpt     = $this->userStateChangeSet("callingscreen");
    }

    public function totalDataUpdate($date) {

        $i = 1;
        while (strtotime($date) < strtotime(date('Y-m-d'))) {
            echo $date;
            echo "<br/>";
            $this->db->query(" DELETE FROM campaign_calls_daily WHERE  `date` = '$date' ");
            $this->db->query(" DELETE FROM process_calls_daily WHERE   `date` = '$date' ");
            $this->db->query(" DELETE FROM total_calls_daily WHERE   `date` = '$date' ");
            $this->db->query(" DELETE FROM agent_calls_daily WHERE   `date` = '$date' ");
            $this->db->query(" DELETE FROM agent_campaign_calls_daily WHERE   `date` = '$date' ");
            $this->db->query(" DELETE FROM agent_process_calls_daily WHERE   `date` = '$date' ");

            $this->reporting_model->dashboardRedisData($date);
            $date = date('Y-m-d', strtotime('+' . $i . ' days', strtotime($date)));
        }

        // $output = $this->process_model->changeAgentProcess('24,10,2,13,17,16,14');
        // $output = $this->process_model->changeAgentProcess('34');
        // $output = $this->lua_model->getManualProcess('');
        // $luaOpt     = $this->userStateChangeSet("callingscreen");
    }

    public function testDemo($value = '') {
        $json = $this->reporting_model->hourlySampleData();
        // echo count($json->LOG);
        $hourArray = array();
        $campaignHourlyData = array();
        $processHourlyData = array();
        $agentHourlyData = array();
        foreach ($json->LOG as $key => $value) {
            $object = get_object_vars($value);
            $objectCount = count(explode(":", key($value)));
            // $objectCount  = substr_count($objectKey, ':');
            $objectArray = explode(":", key($value));
            $objectValue = $object[key($value)];
            $objectKey = key($value);
            if ($objectCount == 4) {
                if ($objectArray[1] == "total") {
                    $HourlyPeriod = $objectArray[3];
                    if (!array_key_exists($HourlyPeriod, $hourArray)) {
                        $hourArray[$HourlyPeriod] = new stdClass();
                        $hourArray[$HourlyPeriod]->HourlyPeriod = $HourlyPeriod;
                    }

                    if ($objectArray[2] == "waitTime") {
                        $hourArray[$HourlyPeriod]->total_waitTime = $objectValue;
                    } else if ($objectArray[2] == "calls") {
                        $hourArray[$HourlyPeriod]->total_calls = $objectValue;
                    }
                } else if ($objectArray[1] == "average") {
                    $HourlyPeriod = $objectArray[3];
                    if (!array_key_exists($HourlyPeriod, $hourArray)) {
                        $hourArray[$HourlyPeriod] = new stdClass();
                        $hourArray[$HourlyPeriod]->HourlyPeriod = $HourlyPeriod;
                    }

                    if ($objectArray[2] == "waitTime") {
                        $hourArray[$HourlyPeriod]->average_waitTime = $objectValue;
                    } else if ($objectArray[2] == "duration") {
                        $hourArray[$HourlyPeriod]->average_duration = $objectValue;
                    }
                }
            } else if ($objectCount == 5) {
                $HourlyPeriod = $objectArray[4];
                if (!array_key_exists($HourlyPeriod, $hourArray)) {
                    $hourArray[$HourlyPeriod] = new stdClass();
                    $hourArray[$HourlyPeriod]->HourlyPeriod = $HourlyPeriod;
                }

                if ($objectArray[1] == "total" && $objectArray[2] == "agent" && $objectArray[3] == "duration") {
                    $hourArray[$HourlyPeriod]->total_agent_duration = $objectValue;
                } else if ($objectArray[1] == "total" && $objectArray[2] == "calls") {
                    if ($objectArray[3] == "outbound") {
                        $hourArray[$HourlyPeriod]->total_calls_outbound = $objectValue;
                    } else if ($objectArray[3] == "drop") {
                        $hourArray[$HourlyPeriod]->total_calls_drop = $objectValue;
                    } else if ($objectArray[3] == "noconnect") {
                        $hourArray[$HourlyPeriod]->total_calls_noconnect = $objectValue;
                    } else if ($objectArray[3] == "duration") {
                        $hourArray[$HourlyPeriod]->total_calls_duration = $objectValue;
                    } else if ($objectArray[3] == "agent") {
                        $hourArray[$HourlyPeriod]->total_calls_agent = $objectValue;
                    }
                } else if ($objectArray[1] == "average" && $objectArray[2] == "duration") {
                    if ($objectArray[3] == "outbound") {
                        $hourArray[$HourlyPeriod]->average_duration_outbound = $objectValue;
                    } else if ($objectArray[3] == "inbound") {
                        $hourArray[$HourlyPeriod]->average_duration_inbound = $objectValue;
                    } else if ($objectArray[3] == "agent") {
                        $hourArray[$HourlyPeriod]->average_duration_agent = $objectValue;
                    }
                } else if ($objectArray[1] == "total" && $objectArray[2] == "waitTime") {
                    if ($objectArray[3] == "outbound") {
                        $hourArray[$HourlyPeriod]->total_waitTime_outbound = $objectValue;
                    } else if ($objectArray[3] == "inbound") {
                        $hourArray[$HourlyPeriod]->total_waitTime_inbound = $objectValue;
                    }
                } else if ($objectArray[1] == "average" && $objectArray[2] == "waitTime") {
                    if ($objectArray[3] == "outbound") {
                        $hourArray[$HourlyPeriod]->average_waitTime_outbound = $objectValue;
                    } else if ($objectArray[3] == "inbound") {
                        $hourArray[$HourlyPeriod]->average_waitTime_inbound = $objectValue;
                    }
                } else if ($objectArray[1] == "duration") {
                    if ($objectArray[2] == "process") {
                        $processId = $objectArray[3];

                        if (!array_key_exists($HourlyPeriod, $processHourlyData) || !is_array($processHourlyData[$HourlyPeriod])) {

                            $processHourlyData[$HourlyPeriod] = array();
                            $processHourlyData[$HourlyPeriod][$processId] = new stdClass();
                            $processHourlyData[$HourlyPeriod][$processId]->processId = $processId;
                            $processHourlyData[$HourlyPeriod][$processId]->HourlyPeriod = $HourlyPeriod;
                        } else if (!array_key_exists($processId, $processHourlyData[$HourlyPeriod])) {
                            $processHourlyData[$HourlyPeriod][$processId] = new stdClass();
                            $processHourlyData[$HourlyPeriod][$processId]->processId = $processId;
                            $processHourlyData[$HourlyPeriod][$processId]->HourlyPeriod = $HourlyPeriod;
                        }
                        $processHourlyData[$HourlyPeriod][$processId]->duration = $objectValue;
                    } else if ($objectArray[2] == "campaign") {
                        $campaignId = $objectArray[3];
                        if (!array_key_exists($HourlyPeriod, $campaignHourlyData) || !is_array($campaignHourlyData[$HourlyPeriod])) {
                            $campaignHourlyData[$HourlyPeriod] = array();
                            $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                        } else if (!array_key_exists($campaignId, $campaignHourlyData[$HourlyPeriod])) {
                            $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                        }
                        $campaignHourlyData[$HourlyPeriod][$campaignId]->duration = $objectValue;
                    }
                } else if ($objectArray[1] == "waitTime") {
                    if ($objectArray[2] == "process") {
                        $processId = $objectArray[3];

                        if (!array_key_exists($HourlyPeriod, $processHourlyData) || !is_array($processHourlyData[$HourlyPeriod])) {

                            $processHourlyData[$HourlyPeriod] = array();
                            $processHourlyData[$HourlyPeriod][$processId] = new stdClass();
                            $processHourlyData[$HourlyPeriod][$processId]->processId = $processId;
                            $processHourlyData[$HourlyPeriod][$processId]->HourlyPeriod = $HourlyPeriod;
                        } else if (!array_key_exists($processId, $processHourlyData[$HourlyPeriod])) {
                            $processHourlyData[$HourlyPeriod][$processId] = new stdClass();
                            $processHourlyData[$HourlyPeriod][$processId]->processId = $processId;
                            $processHourlyData[$HourlyPeriod][$processId]->HourlyPeriod = $HourlyPeriod;
                        }
                        $processHourlyData[$HourlyPeriod][$processId]->waitTime = $objectValue;
                    } else if ($objectArray[2] == "campaign") {
                        $campaignId = $objectArray[3];
                        if (!array_key_exists($HourlyPeriod, $campaignHourlyData) || !is_array($campaignHourlyData[$HourlyPeriod])) {
                            $campaignHourlyData[$HourlyPeriod] = array();
                            $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                        } else if (!array_key_exists($campaignId, $campaignHourlyData[$HourlyPeriod])) {
                            $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                        }
                        $campaignHourlyData[$HourlyPeriod][$campaignId]->waitTime = $objectValue;
                    }
                } else if ($objectArray[1] == "average") {
                    if ($objectArray[2] == "process") {
                        $processId = $objectArray[3];

                        if (!array_key_exists($HourlyPeriod, $processHourlyData) || !is_array($processHourlyData[$HourlyPeriod])) {

                            $processHourlyData[$HourlyPeriod] = array();
                            $processHourlyData[$HourlyPeriod][$processId] = new stdClass();
                            $processHourlyData[$HourlyPeriod][$processId]->processId = $processId;
                            $processHourlyData[$HourlyPeriod][$processId]->HourlyPeriod = $HourlyPeriod;
                        } else if (!array_key_exists($processId, $processHourlyData[$HourlyPeriod])) {
                            $processHourlyData[$HourlyPeriod][$processId] = new stdClass();
                            $processHourlyData[$HourlyPeriod][$processId]->processId = $processId;
                            $processHourlyData[$HourlyPeriod][$processId]->HourlyPeriod = $HourlyPeriod;
                        }
                        $processHourlyData[$HourlyPeriod][$processId]->average = $objectValue;
                    } else if ($objectArray[2] == "campaign") {
                        $campaignId = $objectArray[3];
                        if (!array_key_exists($HourlyPeriod, $campaignHourlyData) || !is_array($campaignHourlyData[$HourlyPeriod])) {
                            $campaignHourlyData[$HourlyPeriod] = array();
                            $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                        } else if (!array_key_exists($campaignId, $campaignHourlyData[$HourlyPeriod])) {
                            $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                        }
                        $campaignHourlyData[$HourlyPeriod][$campaignId]->average = $objectValue;
                    }
                }
            } else if ($objectCount == 6) {
                $HourlyPeriod = $objectArray[5];
                if (!array_key_exists($HourlyPeriod, $hourArray)) {
                    $hourArray[$HourlyPeriod] = new stdClass();
                    $hourArray[$HourlyPeriod]->HourlyPeriod = $HourlyPeriod;
                }

                if ($objectArray[1] == "duration") {
                    if ($objectArray[2] == "agent") {
                        if ($objectArray[3] == "process") {
                            $processId = $objectArray[4];

                            if (!array_key_exists($HourlyPeriod, $processHourlyData) || !is_array($processHourlyData[$HourlyPeriod])) {

                                $processHourlyData[$HourlyPeriod] = array();
                                $processHourlyData[$HourlyPeriod][$processId] = new stdClass();
                                $processHourlyData[$HourlyPeriod][$processId]->processId = $processId;
                                $processHourlyData[$HourlyPeriod][$processId]->HourlyPeriod = $HourlyPeriod;
                            } else if (!array_key_exists($processId, $processHourlyData[$HourlyPeriod])) {
                                $processHourlyData[$HourlyPeriod][$processId] = new stdClass();
                                $processHourlyData[$HourlyPeriod][$processId]->processId = $processId;
                                $processHourlyData[$HourlyPeriod][$processId]->HourlyPeriod = $HourlyPeriod;
                            }
                            $processHourlyData[$HourlyPeriod][$processId]->duration_agent = $objectValue;
                        } else if ($objectArray[3] == "campaign") {
                            $campaignId = $objectArray[4];
                            if (!array_key_exists($HourlyPeriod, $campaignHourlyData) || !is_array($campaignHourlyData[$HourlyPeriod])) {
                                $campaignHourlyData[$HourlyPeriod] = array();
                                $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                            } else if (!array_key_exists($campaignId, $campaignHourlyData[$HourlyPeriod])) {
                                $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                            }
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->duration_agent = $objectValue;
                        }
                    } else if ($objectArray[2] == "outbound") {
                        $campaignId = $objectArray[4];
                        if (!array_key_exists($HourlyPeriod, $campaignHourlyData) || !is_array($campaignHourlyData[$HourlyPeriod])) {
                            $campaignHourlyData[$HourlyPeriod] = array();
                            $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                        } else if (!array_key_exists($campaignId, $campaignHourlyData[$HourlyPeriod])) {
                            $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                        }
                        $campaignHourlyData[$HourlyPeriod][$campaignId]->duration_outbound = $objectValue;
                    } else if ($objectArray[2] == "inbound") {
                        $campaignId = $objectArray[4];
                        if (!array_key_exists($HourlyPeriod, $campaignHourlyData) || !is_array($campaignHourlyData[$HourlyPeriod])) {
                            $campaignHourlyData[$HourlyPeriod] = array();
                            $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                        } else if (!array_key_exists($campaignId, $campaignHourlyData[$HourlyPeriod])) {
                            $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                        }
                        $campaignHourlyData[$HourlyPeriod][$campaignId]->duration_inbound = $objectValue;
                    }
                } else if ($objectArray[1] == "total") {
                    if ($objectArray[2] == "calls") {
                        if ($objectArray[3] == "process") {
                            $processId = $objectArray[4];
                            if (!array_key_exists($HourlyPeriod, $processHourlyData) || !is_array($processHourlyData[$HourlyPeriod])) {

                                $processHourlyData[$HourlyPeriod] = array();
                                $processHourlyData[$HourlyPeriod][$processId] = new stdClass();
                                $processHourlyData[$HourlyPeriod][$processId]->processId = $processId;
                                $processHourlyData[$HourlyPeriod][$processId]->HourlyPeriod = $HourlyPeriod;
                            } else if (!array_key_exists($processId, $processHourlyData[$HourlyPeriod])) {
                                $processHourlyData[$HourlyPeriod][$processId] = new stdClass();
                                $processHourlyData[$HourlyPeriod][$processId]->processId = $processId;
                                $processHourlyData[$HourlyPeriod][$processId]->HourlyPeriod = $HourlyPeriod;
                            }
                            $processHourlyData[$HourlyPeriod][$processId]->total_calls = $objectValue;
                        } else if ($objectArray[3] == "campaign") {
                            $campaignId = $objectArray[4];
                            if (!array_key_exists($HourlyPeriod, $campaignHourlyData) || !is_array($campaignHourlyData[$HourlyPeriod])) {
                                $campaignHourlyData[$HourlyPeriod] = array();
                                $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                            } else if (!array_key_exists($campaignId, $campaignHourlyData[$HourlyPeriod])) {
                                $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                            }
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->total_calls = $objectValue;
                        } else if ($objectArray[3] == "agent") {
                            $agentId = $objectArray[4];
                            if (!array_key_exists($HourlyPeriod, $agentHourlyData) || !is_array($agentHourlyData[$HourlyPeriod])) {

                                $agentHourlyData[$HourlyPeriod] = array();
                                $agentHourlyData[$HourlyPeriod][$agentId] = new stdClass();
                                $agentHourlyData[$HourlyPeriod][$agentId]->agentId = $agentId;
                                $agentHourlyData[$HourlyPeriod][$agentId]->HourlyPeriod = $HourlyPeriod;
                            } else if (!array_key_exists($agentId, $agentHourlyData[$HourlyPeriod])) {
                                $agentHourlyData[$HourlyPeriod][$agentId] = new stdClass();
                                $agentHourlyData[$HourlyPeriod][$agentId]->agentId = $agentId;
                                $agentHourlyData[$HourlyPeriod][$agentId]->HourlyPeriod = $HourlyPeriod;
                            }
                            $agentHourlyData[$HourlyPeriod][$agentId]->total_calls = $objectValue;
                        } else {
                            if ($objectArray[3] == "duration") {
                                if ($objectArray[4] == "outbound") {
                                    $hourArray[$HourlyPeriod]->total_calls_duration_outbound = $objectValue;
                                } else if ($objectArray[4] == "inbound") {
                                    $hourArray[$HourlyPeriod]->total_calls_duration_inbound = $objectValue;
                                }
                            } else if ($objectArray[3] == "drop") {
                                if ($objectArray[4] == "outbound") {
                                    $hourArray[$HourlyPeriod]->total_calls_drop_outbound = $objectValue;
                                } else if ($objectArray[4] == "inbound") {
                                    $hourArray[$HourlyPeriod]->total_calls_drop_inbound = $objectValue;
                                }
                            } else if ($objectArray[3] == "noconnect") {
                                if ($objectArray[4] == "outbound") {
                                    $hourArray[$HourlyPeriod]->total_calls_noconnect_outbound = $objectValue;
                                } else if ($objectArray[4] == "inbound") {
                                    $hourArray[$HourlyPeriod]->total_calls_noconnect_inbound = $objectValue;
                                }
                            }
                        }
                    } else if ($objectArray[2] == "noconnect") {
                        if ($objectArray[3] == "process") {
                            $processId = $objectArray[4];
                            if (!array_key_exists($HourlyPeriod, $processHourlyData) || !is_array($processHourlyData[$HourlyPeriod])) {
                                $processHourlyData[$HourlyPeriod] = array();
                                $processHourlyData[$HourlyPeriod][$processId] = new stdClass();
                                $processHourlyData[$HourlyPeriod][$processId]->processId = $processId;
                                $processHourlyData[$HourlyPeriod][$processId]->HourlyPeriod = $HourlyPeriod;
                            } else if (!array_key_exists($processId, $processHourlyData[$HourlyPeriod])) {
                                $processHourlyData[$HourlyPeriod][$processId] = new stdClass();
                                $processHourlyData[$HourlyPeriod][$processId]->processId = $processId;
                                $processHourlyData[$HourlyPeriod][$processId]->HourlyPeriod = $HourlyPeriod;
                            }
                            $processHourlyData[$HourlyPeriod][$processId]->total_noconnect = $objectValue;
                        } else if ($objectArray[3] == "campaign") {
                            $campaignId = $objectArray[4];
                            if (!array_key_exists($HourlyPeriod, $campaignHourlyData) || !is_array($campaignHourlyData[$HourlyPeriod])) {
                                $campaignHourlyData[$HourlyPeriod] = array();
                                $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                            } else if (!array_key_exists($campaignId, $campaignHourlyData[$HourlyPeriod])) {
                                $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                            }
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->total_noconnect = $objectValue;
                        }
                    } else if ($objectArray[2] == "agent" && $objectArray[3] == "duration") {
                        if ($objectArray[4] == "outbound") {
                            $hourArray[$HourlyPeriod]->total_agent_duration_outbound = $objectValue;
                        } else if ($objectArray[4] == "inbound") {
                            $hourArray[$HourlyPeriod]->total_agent_duration_inbound = $objectValue;
                        }
                    } else if ($objectArray[2] == "drop") {
                        if ($objectArray[3] == "process") {
                            $processId = $objectArray[4];
                            if (!array_key_exists($HourlyPeriod, $processHourlyData) || !is_array($processHourlyData[$HourlyPeriod])) {
                                $processHourlyData[$HourlyPeriod] = array();
                                $processHourlyData[$HourlyPeriod][$processId] = new stdClass();
                                $processHourlyData[$HourlyPeriod][$processId]->processId = $processId;
                                $processHourlyData[$HourlyPeriod][$processId]->HourlyPeriod = $HourlyPeriod;
                            } else if (!array_key_exists($processId, $processHourlyData[$HourlyPeriod])) {
                                $processHourlyData[$HourlyPeriod][$processId] = new stdClass();
                                $processHourlyData[$HourlyPeriod][$processId]->processId = $processId;
                                $processHourlyData[$HourlyPeriod][$processId]->HourlyPeriod = $HourlyPeriod;
                            }
                            $processHourlyData[$HourlyPeriod][$processId]->total_drop = $objectValue;
                        } else if ($objectArray[3] == "campaign") {
                            $campaignId = $objectArray[4];
                            if (!array_key_exists($HourlyPeriod, $campaignHourlyData) || !is_array($campaignHourlyData[$HourlyPeriod])) {
                                $campaignHourlyData[$HourlyPeriod] = array();
                                $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                            } else if (!array_key_exists($campaignId, $campaignHourlyData[$HourlyPeriod])) {
                                $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                            }
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->total_drop = $objectValue;
                        }
                    }
                } else if ($objectArray[1] == "waitTime") {
                    if ($objectArray[3] == "campaign") {
                        $campaignId = $objectArray[4];
                        if (!array_key_exists($HourlyPeriod, $campaignHourlyData) || !is_array($campaignHourlyData[$HourlyPeriod])) {
                            $campaignHourlyData[$HourlyPeriod] = array();
                            $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                        } else if (!array_key_exists($campaignId, $campaignHourlyData[$HourlyPeriod])) {
                            $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                        }
                        if ($objectArray[2] == "outbound") {
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->waitTime_drop_outbound = $objectValue;
                        } else if ($objectArray[2] == "inbound") {
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->waitTime_drop_inbound = $objectValue;
                        }
                    }
                } else if ($objectArray[1] == "average") {
                    if ($objectArray[2] == "agent") {
                        if ($objectArray[3] == "process") {
                            $processId = $objectArray[4];
                            if (!array_key_exists($HourlyPeriod, $processHourlyData) || !is_array($processHourlyData[$HourlyPeriod])) {
                                $processHourlyData[$HourlyPeriod] = array();
                                $processHourlyData[$HourlyPeriod][$processId] = new stdClass();
                                $processHourlyData[$HourlyPeriod][$processId]->processId = $processId;
                                $processHourlyData[$HourlyPeriod][$processId]->HourlyPeriod = $HourlyPeriod;
                            } else if (!array_key_exists($processId, $processHourlyData[$HourlyPeriod])) {
                                $processHourlyData[$HourlyPeriod][$processId] = new stdClass();
                                $processHourlyData[$HourlyPeriod][$processId]->processId = $processId;
                                $processHourlyData[$HourlyPeriod][$processId]->HourlyPeriod = $HourlyPeriod;
                            }
                            $processHourlyData[$HourlyPeriod][$processId]->average_agent = $objectValue;
                        } else if ($objectArray[3] == "campaign") {
                            $campaignId = $objectArray[4];
                            if (!array_key_exists($HourlyPeriod, $campaignHourlyData) || !is_array($campaignHourlyData[$HourlyPeriod])) {
                                $campaignHourlyData[$HourlyPeriod] = array();
                                $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                            } else if (!array_key_exists($campaignId, $campaignHourlyData[$HourlyPeriod])) {
                                $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                            }
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->average_agent = $objectValue;
                        }
                    } else if ($objectArray[2] == "waitTime") {
                        if ($objectArray[3] == "process") {
                            $processId = $objectArray[4];
                            if (!array_key_exists($HourlyPeriod, $processHourlyData) || !is_array($processHourlyData[$HourlyPeriod])) {
                                $processHourlyData[$HourlyPeriod] = array();
                                $processHourlyData[$HourlyPeriod][$processId] = new stdClass();
                                $processHourlyData[$HourlyPeriod][$processId]->processId = $processId;
                                $processHourlyData[$HourlyPeriod][$processId]->HourlyPeriod = $HourlyPeriod;
                            } else if (!array_key_exists($processId, $processHourlyData[$HourlyPeriod])) {
                                $processHourlyData[$HourlyPeriod][$processId] = new stdClass();
                                $processHourlyData[$HourlyPeriod][$processId]->processId = $processId;
                                $processHourlyData[$HourlyPeriod][$processId]->HourlyPeriod = $HourlyPeriod;
                            }
                            $processHourlyData[$HourlyPeriod][$processId]->average_waitTime = $objectValue;
                        } else if ($objectArray[3] == "campaign") {

                            $campaignId = $objectArray[4];
                            if (!array_key_exists($HourlyPeriod, $campaignHourlyData) || !is_array($campaignHourlyData[$HourlyPeriod])) {
                                $campaignHourlyData[$HourlyPeriod] = array();
                                $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                            } else if (!array_key_exists($campaignId, $campaignHourlyData[$HourlyPeriod])) {
                                $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                            }
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->average_waitTime = $objectValue;
                        }
                    } else if ($objectArray[2] == "duration" && $objectArray[3] == "agent") {
                        if ($objectArray[4] == "outbound") {
                            $hourArray[$HourlyPeriod]->average_agent_duration_outbound = $objectValue;
                        } else if ($objectArray[4] == "inbound") {
                            $hourArray[$HourlyPeriod]->average_agent_duration_inbound = $objectValue;
                        }
                    } else if ($objectArray[2] == "inbound" && $objectArray[3] == "campaign") {
                        $campaignId = $objectArray[4];
                        if (!array_key_exists($HourlyPeriod, $campaignHourlyData) || !is_array($campaignHourlyData[$HourlyPeriod])) {
                            $campaignHourlyData[$HourlyPeriod] = array();
                            $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                        } else if (!array_key_exists($campaignId, $campaignHourlyData[$HourlyPeriod])) {
                            $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                        }
                        $campaignHourlyData[$HourlyPeriod][$campaignId]->average_inbound = $objectValue;
                    } else if ($objectArray[2] == "outbound" && $objectArray[3] == "campaign") {
                        $campaignId = $objectArray[4];
                        if (!array_key_exists($HourlyPeriod, $campaignHourlyData) || !is_array($campaignHourlyData[$HourlyPeriod])) {
                            $campaignHourlyData[$HourlyPeriod] = array();
                            $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                        } else if (!array_key_exists($campaignId, $campaignHourlyData[$HourlyPeriod])) {
                            $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                        }
                        $campaignHourlyData[$HourlyPeriod][$campaignId]->average_outbound = $objectValue;
                    }
                }
            } else if ($objectCount == 7) {
                $HourlyPeriod = $objectArray[6];
                if (!array_key_exists($HourlyPeriod, $hourArray)) {
                    $hourArray[$HourlyPeriod] = new stdClass();
                    $hourArray[$HourlyPeriod]->HourlyPeriod = $HourlyPeriod;
                }

                if ($objectArray[1] == "duration" && $objectArray[2] == "agent") {
                    if ($objectArray[3] == "inbound" && $objectArray[4] == "campaign") {
                        $campaignId = $objectArray[5];
                        if (!array_key_exists($HourlyPeriod, $campaignHourlyData) || !is_array($campaignHourlyData[$HourlyPeriod])) {
                            $campaignHourlyData[$HourlyPeriod] = array();
                            $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                        } else if (!array_key_exists($campaignId, $campaignHourlyData[$HourlyPeriod])) {
                            $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                        }
                        $campaignHourlyData[$HourlyPeriod][$campaignId]->duration_agent_inbound = $objectValue;
                    } else if ($objectArray[3] == "outbound" && $objectArray[4] == "campaign") {
                        $campaignId = $objectArray[5];
                        if (!array_key_exists($HourlyPeriod, $campaignHourlyData) || !is_array($campaignHourlyData[$HourlyPeriod])) {
                            $campaignHourlyData[$HourlyPeriod] = array();
                            $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                        } else if (!array_key_exists($campaignId, $campaignHourlyData[$HourlyPeriod])) {
                            $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                        }
                        $campaignHourlyData[$HourlyPeriod][$campaignId]->duration_agent_outbound = $objectValue;
                    }
                } else if ($objectArray[1] == "total") {
                    if ($objectArray[2] == "calls") {
                        if ($objectArray[3] == "agent" && $objectArray[4] == "process") {
                            $processId = $objectArray[5];
                            if (!array_key_exists($HourlyPeriod, $processHourlyData) || !is_array($processHourlyData[$HourlyPeriod])) {

                                $processHourlyData[$HourlyPeriod] = array();
                                $processHourlyData[$HourlyPeriod][$processId] = new stdClass();
                                $processHourlyData[$HourlyPeriod][$processId]->processId = $processId;
                                $processHourlyData[$HourlyPeriod][$processId]->HourlyPeriod = $HourlyPeriod;
                            } else if (!array_key_exists($processId, $processHourlyData[$HourlyPeriod])) {
                                $processHourlyData[$HourlyPeriod][$processId] = new stdClass();
                                $processHourlyData[$HourlyPeriod][$processId]->processId = $processId;
                                $processHourlyData[$HourlyPeriod][$processId]->HourlyPeriod = $HourlyPeriod;
                            }
                            $processHourlyData[$HourlyPeriod][$processId]->total_calls_agent = $objectValue;
                        } else if ($objectArray[3] == "agent" && $objectArray[4] == "campaign") {
                            $campaignId = $objectArray[5];
                            if (!array_key_exists($HourlyPeriod, $campaignHourlyData) || !is_array($campaignHourlyData[$HourlyPeriod])) {
                                $campaignHourlyData[$HourlyPeriod] = array();
                                $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                            } else if (!array_key_exists($campaignId, $campaignHourlyData[$HourlyPeriod])) {
                                $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                            }
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->total_calls_agent = $objectValue;
                        } else if ($objectArray[3] == "outbound" && $objectArray[4] == "campaign") {
                            $campaignId = $objectArray[5];
                            if (!array_key_exists($HourlyPeriod, $campaignHourlyData) || !is_array($campaignHourlyData[$HourlyPeriod])) {
                                $campaignHourlyData[$HourlyPeriod] = array();
                                $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                            } else if (!array_key_exists($campaignId, $campaignHourlyData[$HourlyPeriod])) {
                                $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                            }
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->total_calls_outbound = $objectValue;
                        } else if ($objectArray[3] == "inbound" && $objectArray[4] == "campaign") {
                            $campaignId = $objectArray[5];
                            if (!array_key_exists($HourlyPeriod, $campaignHourlyData) || !is_array($campaignHourlyData[$HourlyPeriod])) {
                                $campaignHourlyData[$HourlyPeriod] = array();
                                $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                            } else if (!array_key_exists($campaignId, $campaignHourlyData[$HourlyPeriod])) {
                                $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                            }
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->total_calls_inbound = $objectValue;
                        }
                    } else if ($objectArray[2] == "noconnect") {
                        $campaignId = $objectArray[5];
                        if ($objectArray[3] == "outbound" && $objectArray[4] == "campaign") {
                            if (!array_key_exists($HourlyPeriod, $campaignHourlyData) || !is_array($campaignHourlyData[$HourlyPeriod])) {
                                $campaignHourlyData[$HourlyPeriod] = array();
                                $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                            } else if (!array_key_exists($campaignId, $campaignHourlyData[$HourlyPeriod])) {
                                $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                            }
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->total_noconnect_outbound = $objectValue;
                        } else if ($objectArray[3] == "inbound" && $objectArray[4] == "campaign") {
                            if (!array_key_exists($HourlyPeriod, $campaignHourlyData) || !is_array($campaignHourlyData[$HourlyPeriod])) {
                                $campaignHourlyData[$HourlyPeriod] = array();
                                $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                            } else if (!array_key_exists($campaignId, $campaignHourlyData[$HourlyPeriod])) {
                                $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                            }
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->total_noconnect_inbound = $objectValue;
                        }
                    } else if ($objectArray[2] == "drop") {
                        $campaignId = $objectArray[5];
                        if ($objectArray[3] == "outbound" && $objectArray[4] == "campaign") {
                            if (!array_key_exists($HourlyPeriod, $campaignHourlyData) || !is_array($campaignHourlyData[$HourlyPeriod])) {
                                $campaignHourlyData[$HourlyPeriod] = array();
                                $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                            } else if (!array_key_exists($campaignId, $campaignHourlyData[$HourlyPeriod])) {
                                $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                            }
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->total_drop_outbound = $objectValue;
                        } else if ($objectArray[3] == "inbound" && $objectArray[4] == "campaign") {
                            if (!array_key_exists($HourlyPeriod, $campaignHourlyData) || !is_array($campaignHourlyData[$HourlyPeriod])) {
                                $campaignHourlyData[$HourlyPeriod] = array();
                                $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                            } else if (!array_key_exists($campaignId, $campaignHourlyData[$HourlyPeriod])) {
                                $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                                $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                            }
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->total_drop_inbound = $objectValue;
                        }
                    }
                } else if ($objectArray[1] == "average") {
                    // echo $objectKey."::  $objectValue <br/>";
                    if ($objectArray[2] == "agent" && $objectArray[3] == "outbound" && $objectArray[4] == "campaign") {
                        $campaignId = $objectArray[5];
                        if (!array_key_exists($HourlyPeriod, $campaignHourlyData) || !is_array($campaignHourlyData[$HourlyPeriod])) {
                            $campaignHourlyData[$HourlyPeriod] = array();
                            $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                        } else if (!array_key_exists($campaignId, $campaignHourlyData[$HourlyPeriod])) {
                            $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                        }
                        $campaignHourlyData[$HourlyPeriod][$campaignId]->average_agent_outbound = $objectValue;
                    } else if ($objectArray[2] == "agent" && $objectArray[3] == "inbound" && $objectArray[4] == "campaign") {
                        $campaignId = $objectArray[5];
                        if (!array_key_exists($HourlyPeriod, $campaignHourlyData) || !is_array($campaignHourlyData[$HourlyPeriod])) {
                            $campaignHourlyData[$HourlyPeriod] = array();
                            $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                        } else if (!array_key_exists($campaignId, $campaignHourlyData[$HourlyPeriod])) {
                            $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                        }
                        $campaignHourlyData[$HourlyPeriod][$campaignId]->average_agent_inbound = $objectValue;
                    } else if ($objectArray[2] == "waitTime" && $objectArray[3] == "outbound" && $objectArray[4] == "campaign") {
                        $campaignId = $objectArray[5];
                        if (!array_key_exists($HourlyPeriod, $campaignHourlyData) || !is_array($campaignHourlyData[$HourlyPeriod])) {
                            $campaignHourlyData[$HourlyPeriod] = array();
                            $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                        } else if (!array_key_exists($campaignId, $campaignHourlyData[$HourlyPeriod])) {
                            $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                        }
                        $campaignHourlyData[$HourlyPeriod][$campaignId]->average_waitTime_outbound = $objectValue;
                    } else if ($objectArray[2] == "waitTime" && $objectArray[3] == "inbound" && $objectArray[4] == "campaign") {
                        $campaignId = $objectArray[5];
                        if (!array_key_exists($HourlyPeriod, $campaignHourlyData) || !is_array($campaignHourlyData[$HourlyPeriod])) {
                            $campaignHourlyData[$HourlyPeriod] = array();
                            $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                        } else if (!array_key_exists($campaignId, $campaignHourlyData[$HourlyPeriod])) {
                            $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                            $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                        }
                        $campaignHourlyData[$HourlyPeriod][$campaignId]->average_waitTime_inbound = $objectValue;
                    }
                }
            } else if ($objectCount == 8) {
                $HourlyPeriod = $objectArray[7];
                if (!array_key_exists($HourlyPeriod, $hourArray)) {
                    $hourArray[$HourlyPeriod] = new stdClass();
                    $hourArray[$HourlyPeriod]->HourlyPeriod = $HourlyPeriod;
                }

                if ($objectArray[1] == "total" && $objectArray[2] == "calls" && $objectArray[3] == "agent" && $objectArray[4] == "outbound" && $objectArray[5] == "campaign") {
                    $campaignId = $objectArray[6];
                    if (!array_key_exists($HourlyPeriod, $campaignHourlyData) || !is_array($campaignHourlyData[$HourlyPeriod])) {
                        $campaignHourlyData[$HourlyPeriod] = array();
                        $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                        $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                        $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                    } else if (!array_key_exists($campaignId, $campaignHourlyData[$HourlyPeriod])) {
                        $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                        $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                        $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                    }
                    $campaignHourlyData[$HourlyPeriod][$campaignId]->total_calls_agent_outbound = $objectValue;
                } else if ($objectArray[1] == "total" && $objectArray[2] == "calls" && $objectArray[3] == "agent" && $objectArray[4] == "inbound" && $objectArray[5] == "campaign") {
                    $campaignId = $objectArray[6];
                    if (!array_key_exists($HourlyPeriod, $campaignHourlyData) || !is_array($campaignHourlyData[$HourlyPeriod])) {
                        $campaignHourlyData[$HourlyPeriod] = array();
                        $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                        $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                        $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                    } else if (!array_key_exists($campaignId, $campaignHourlyData[$HourlyPeriod])) {
                        $campaignHourlyData[$HourlyPeriod][$campaignId] = new stdClass();
                        $campaignHourlyData[$HourlyPeriod][$campaignId]->campaignId = $campaignId;
                        $campaignHourlyData[$HourlyPeriod][$campaignId]->HourlyPeriod = $HourlyPeriod;
                    }
                    $campaignHourlyData[$HourlyPeriod][$campaignId]->total_calls_agent_inbound = $objectValue;
                }
            }
        }
        // print_r($hourArray);
    }

    public function dashboardTotalDownload() {

        $base_url = $this->config->item('base_url');
        $time = time();

        $totalData = $this->campaign_model->dashboardTotalDownload();
        $excelTitle = "Total Agent Data";
        $excelTitle = array((object) array('excelTitle' => 'Total Calls', 'excelYAxisLabel' => 'Calls'),
            (object) array('excelTitle' => 'Total Outbound Calls', 'excelYAxisLabel' => 'Calls'),
            (object) array('excelTitle' => 'Total Inbound Calls', 'excelYAxisLabel' => 'Calls'),
            (object) array('excelTitle' => 'Average Time', 'excelYAxisLabel' => 'Time'),
            (object) array('excelTitle' => 'Average Outbound Time', 'excelYAxisLabel' => 'Time'),
            (object) array('excelTitle' => 'Average Inbound Time', 'excelYAxisLabel' => 'Time'),
        );
        $excelYAxisLabel = 'Calls';
        $folderPath = "";
        $username = $this->session->userdata('username');
        $filename = "TotalDashboardData-$username-" . $time . ".xlsx";

        $filename = $this->common_model->arrayToExcelGraph($totalData, $excelTitle, $excelYAxisLabel, $folderPath, $filename);

        redirect($base_url . "/CDRs/" . $filename);
    }

    public function dashboardHourlyDownload() {
        $data['message'] = $this->campaign_model->dashboardHourlyDownload();
        $this->load->view('json', $data);
    }

    public function dashboardCampaignDownload() {
        $campaignId = $this->input->get_post('campaignId');
        $startDate = $this->input->get_post("startDate");
        $endDate = $this->input->get_post("endDate");

        $base_url = $this->config->item('base_url');
        $time = time();

        $totalData = $this->campaign_model->dashboardCampaignDownload($campaignId, $startDate, $endDate);

        $excelTitle = array((object) array('excelTitle' => 'Campaign Calls', 'excelYAxisLabel' => 'Calls'),
            (object) array('excelTitle' => 'Campaign Average Time', 'excelYAxisLabel' => 'Time'),
        );
        $excelYAxisLabel = "";
        $folderPath = "";
        $username = $this->session->userdata('username');
        $filename = "dashboardCampaignDownload-$username-" . $time . ".xlsx";

        $filename = $this->common_model->arrayToExcelGraph($totalData, $excelTitle, $excelYAxisLabel, $folderPath, $filename);

        redirect($base_url . "/CDRs/" . $filename);
    }

    public function dashboardProcessDownload() {
        $processId = $this->input->get_post('processId');
        $startDate = $this->input->get_post("startDate");
        $endDate = $this->input->get_post("endDate");
        $base_url = $this->config->item('base_url');
        $time = time();

        $totalData = $this->campaign_model->dashboardProcessDownload($processId, $startDate, $endDate);
        $excelTitle = array((object) array('excelTitle' => 'Process Calls', 'excelYAxisLabel' => 'Calls'),
            (object) array('excelTitle' => 'Process Average Time', 'excelYAxisLabel' => 'Time'),
        );

        $excelTitle = "Total Agent Data";
        $excelYAxisLabel = 'Calls';
        $folderPath = "";
        $username = $this->session->userdata('username');
        $filename = "dashboardProcessDownload-$username-" . $time . ".xlsx";

        $filename = $this->common_model->arrayToExcelGraph($totalData, $excelTitle, $excelYAxisLabel, $folderPath, $filename);

        redirect($base_url . "/CDRs/" . $filename);
    }

    public function dashboardAgentDownload() {
        $agentId = $this->input->get_post('agentId');
        $startDate = $this->input->get_post('startDate');
        $endDate = $this->input->get_post('endDate');
        $base_url = $this->config->item('base_url');
        $time = time();
        $totalData = $this->campaign_model->dashboardAgentDownload($agentId, $startDate, $endDate);

        $excelYAxisLabel = 'Calls';
        $folderPath = "";
        $username = $this->session->userdata('username');
        $filename = "dashboardAgentDownload-$username-" . $time . ".xlsx";
        $excelTitle = array((object) array('excelTitle' => 'Agent Calls', 'excelYAxisLabel' => 'Calls'),
            (object) array('excelTitle' => 'Agent Average Time', 'excelYAxisLabel' => 'Time'),
        );

        $filename = $this->common_model->arrayToExcelGraph($totalData, $excelTitle, $excelYAxisLabel, $folderPath, $filename);

        redirect($base_url . "/CDRs/" . $filename);
        // $this->load->view('json',$data);
    }

    public function downloadAgentCampaignGraph() {
        $agentId = $this->input->get_post('agentId');
        $campaignId = $this->input->get_post('campaignId');
        $startDate = $this->input->get_post('startDate');
        $endDate = $this->input->get_post('endDate');
        $base_url = $this->config->item('base_url');
        $time = time();
        $totalData = $this->campaign_model->downloadAgentCampaignGraph($agentId, $campaignId, $startDate, $endDate);
        // print_r($totalData );
        $excelTitle = array((object) array('excelTitle' => 'Agent Campaign Calls', 'excelYAxisLabel' => 'Calls'),
            (object) array('excelTitle' => 'Agent Campaign Average Time', 'excelYAxisLabel' => 'Time'),
        );

        $excelYAxisLabel = 'Calls';
        $folderPath = "";
        $username = $this->session->userdata('username');
        $filename = "downloadAgentCampaignGraph-$username-" . $time . ".xlsx";

        $filename = $this->common_model->arrayToExcelGraph($totalData, $excelTitle, $excelYAxisLabel, $folderPath, $filename);

        redirect($base_url . "/CDRs/" . $filename);
        // $this->load->view('json',$data);
    }

    public function downloadAgentProcessGraph() {
        $agentId = $this->input->get_post('agentId');
        $processId = $this->input->get_post('processId');
        $startDate = $this->input->get_post('startDate');
        $endDate = $this->input->get_post('endDate');
        $base_url = $this->config->item('base_url');
        $time = time();
        $totalData = $this->campaign_model->downloadAgentProcessGraph($agentId, $processId, $startDate, $endDate);
        // print_r($totalData );
        $excelTitle = array((object) array('excelTitle' => 'Agent Process Calls', 'excelYAxisLabel' => 'Calls'),
            (object) array('excelTitle' => 'Agent Process Average Time', 'excelYAxisLabel' => 'Time'),
        );
        $excelYAxisLabel = 'Calls';
        $folderPath = "";
        $username = $this->session->userdata('username');
        $filename = "agntprocessDashboardData-$username-" . $time . ".xlsx";

        $filename = $this->common_model->arrayToExcelGraph($totalData, $excelTitle, $excelYAxisLabel, $folderPath, $filename);

        redirect($base_url . "/CDRs/" . $filename);
        // $this->load->view('json',$data);
    }

    public function downloadHourlyTotal() {
        $startDate = $this->input->get_post('startDate');
        $endDate = $this->input->get_post('endDate');
        $base_url = $this->config->item('base_url');
        $time = time();
        $totalData = $this->campaign_model->downloadHourlyTotal($startDate, $endDate);

        $excelTitle = array(
            (object) array('excelTitle' => 'Hourly Total Calls', 'excelYAxisLabel' => 'Calls'),
            (object) array('excelTitle' => 'Hourly Drop Calls', 'excelYAxisLabel' => 'Calls'),
            (object) array('excelTitle' => 'Hourly Non-connect Calls', 'excelYAxisLabel' => 'Calls'),
            (object) array('excelTitle' => 'Hourly Average Duration Graph', 'excelYAxisLabel' => 'Time'),
            (object) array('excelTitle' => 'Hourly Total Duration Graph', 'excelYAxisLabel' => 'Time'),
            (object) array('excelTitle' => 'Hourly Average WaitTime Graph', 'excelYAxisLabel' => 'Time'),
        );

        $excelYAxisLabel = 'Calls';
        $folderPath = "";
        $username = $this->session->userdata('username');
        $filename = "agntprocessDashboardData-$username-" . $time . ".xlsx";

        $filename = $this->common_model->arrayToExcelGraph($totalData, $excelTitle, $excelYAxisLabel, $folderPath, $filename);

        redirect($base_url . "/CDRs/" . $filename);
        // $this->load->view('json',$data);
    }

    public function downloadHourlyCampaign() {
        $campaignHourSelect = $this->input->get_post('campaignHourSelect');

        $startDate = $this->input->get_post('startDate');
        $endDate = $this->input->get_post('endDate');
        $base_url = $this->config->item('base_url');
        $time = time();
        $totalData = $this->campaign_model->downloadHourlyCampaign($campaignHourSelect, $startDate, $endDate);
        $excelTitle = "hourly Campaign Data";
        $excelYAxisLabel = 'Calls';
        $folderPath = "";
        $username = $this->session->userdata('username');
        $filename = "downloadHourlyCampaign-$username-" . $time . ".xlsx";

        $filename = $this->common_model->arrayToExcelGraph($totalData, $excelTitle, $excelYAxisLabel, $folderPath, $filename);

        redirect($base_url . "/CDRs/" . $filename);
        // $this->load->view('json',$data);
    }

    public function downloadHourlyProcess() {
        $processHourlySelect = $this->input->get_post('processHourlySelect');

        $startDate = $this->input->get_post('startDate');
        $endDate = $this->input->get_post('endDate');

        $base_url = $this->config->item('base_url');
        $time = time();
        $totalData = $this->campaign_model->downloadHourlyProcess($processHourlySelect, $startDate, $endDate);
        $excelTitle = "hourly process Data";
        $excelYAxisLabel = 'Calls';
        $folderPath = "";
        $username = $this->session->userdata('username');
        $filename = "agntprocessDashboardData-$username-" . $time . ".xlsx";
        // print_r($totalData);
        $filename = $this->common_model->arrayToExcelGraph($totalData, $excelTitle, $excelYAxisLabel, $folderPath, $filename);

        redirect($base_url . "/CDRs/" . $filename);
        // $this->load->view('json',$data);
    }

    public function updateExtensionLimit() {
        $license = $this->input->get_post('license');
        $extensionlevel = $this->input->get_post('extensionlevel');
        $data['message'] = $this->extension_model->updateLicense($license, $extensionlevel);
        $this->load->view('json', $data);
    }

    public function getGatewayDetailById() {
        $gatewayId = $this->input->get_post('gatewayId');
        $data['message'] = $this->gateway_model->getGatewayDetailById($gatewayId);
        $this->load->view('json', $data);
    }

    public function saveGatewayDetailById() {
        $gatewayName = $this->input->get_post('gatewayName');
        $gatewayId = $this->input->get_post('gatewayId');
        $gatewayDescription = $this->input->get_post('gatewayDescription');
        $gatewayMargin = $this->input->get_post('gatewayMargin');
        $gatewayPulse = $this->input->get_post('gatewayPulse');
        $gatewayBalance = $this->input->get_post('gatewayBalance');
        $gatewayType = $this->input->get_post('gatewayType');
        $gatewayRates = $this->input->get_post('gatewayRates');
        $gatewayPrefix = $this->input->get_post('gatewayPrefix');
        $defaultCallerId = $this->input->get_post('defaultCallerId');

        // $result = $this->gateway_model->getRatesheetDetailById($gatewayId);
        // print_r($result);
        // die();

        $data['message'] = $this->standard_model->addGatewayInRedis($gatewayName, $gatewayId, $gatewayDescription, $gatewayMargin, $gatewayPulse, $gatewayBalance, $gatewayType, $gatewayPrefix, $defaultCallerId);

        $this->load->view('json', $data);
    }

    public function dashboardTotalDataDownload() {
        $graphArray = array(
            array('', 2010, 2011, 2012),
            array('Q1', 12, 15, 21),
            array('Q2', 56, 73, 86),
            array('Q3', 52, 61, 69),
            array('Q4', 30, 32, 0),
        );
        $this->common_model->arrayToExcelGraph($graphArray);
    }

    public function demo() {

        print_r($this->session->userdata('ci_session'));
        $rawCookies = isset($_SERVER['HTTP_COOKIE']) ? $_SERVER['HTTP_COOKIE'] : null;
        $rawLength = strlen($rawCookies);
        echo $rawLength;
    }

    public function testHourly($value = '') {
        $this->reporting_model->getHourlyGraphArray();

        // $jsonArray = array();
        // for ($i=0; $i <10; $i++) {
        //   $jsonObj = new stdClass();
        //   $jsonObj->id = $i;
        //   $jsonObj->name = "namemmm";
        //   $jsonArray[] = $jsonObj;
        // }
        // print_r(json_encode($jsonArray));
        // // $date = date("Y-m-d");`
        // // $output  = $this->reporting_model->hourDataFilter($date);
        // // $this->reporting_model->insertHourlyDataInDatabase($output,$date);
        // // print_r(json_encode($output->hourArray));
        // // print_r(json_encode($output->campaignHourlyData));
        // // print_r(json_encode($output->processHourlyData));
    }

    public function setCallbackTest() {

        $lead = 422;
        $leadsetId = 7;
        $leadsetName = 'rohitDemo';
        $campaignId = 4;
        $campaignName = 'rohitTestCampaign';
        $processId = 12;
        $processName = 'rohitTestPreview';
        $agentId = 2;
        $callback = '2015-03-24 18:50:20';
        $phoneNumber = '919890496017';
        $customerName = 'abc22';
        $this->lua_model->insertCallbackLua($lead, $leadsetId, $leadsetName, $campaignId, $campaignName, $processId, $processName, $agentId, $callback, $phoneNumber, $customerName);
    }

    public function setLuaALLAgent() {
        $setLuaALLAgent = $this->db->query("SELECT id,username,firstname,accesslevel FROM `user`")->result();
        $pageName = "logout";
        $ipAddress = $this->input->ip_address();
        $userState = "logout";
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
        $UserLastState = "logout";
        $userStateArg = "";
        $moc = "";
        foreach ($setLuaALLAgent as $key => $value) {
            echo $value->username;
            $userId = $value->id;
            $userName = $value->username;
            $fullName = $value->firstname;
            $accesslevel = $value->accesslevel;
            $this->lua_model->userStateChange($userId, $userName, $fullName, $pageName, $ipAddress, $userState, $campaignId, $campaignName, $processId, $processName, $customerId, $customerPhoneNumber, $customerName, $referenceUuid, $customerUuid, $LeadsetId, $LeadsetName, $UserLastState, $moc, $userStateArg, $accesslevel);
        }
        echo "Machin already Hacked";
    }

    // inbound tree functionality start
    public function saveInboundJSon() {

        $inboundId = $this->input->get_post('inboundId');
        $inboundType = $this->input->get_post('inboundType');
        $inboundJson = $this->input->get_post('inboundJson');
        $data['message'] = $this->inbound_model->saveInboundJSon($inboundId, $inboundType, $inboundJson);
        $this->load->view('json', $data);
    }

    public function uploadIVR() {

        $uploadIVROutput = new stdClass();
        if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $ivrFileName = $this->input->get_post('ivrFileName');
            $ivrFileDetail = $this->input->get_post('ivrFileDetail');
            $ivrName = $_FILES['file']['name'];
            $allowed = array('audio/mp3', 'audio/wav');

            $extension = pathinfo($ivrName, PATHINFO_EXTENSION);
            $ivrNewName = uniqid() . "." . $extension;
            $uploadIvrPath = $this->config->item('uploadIvrPath');
            $target_file = $uploadIvrPath . $ivrNewName;
            $uploadIVROutput->setIvrFileNameData = $this->inbound_model->setIvrFileName($ivrFileName, $ivrFileDetail, $ivrNewName, $ivrName);
            if (is_writable($uploadIvrPath)) {
                if ($uploadIVROutput->setIvrFileNameData->output == "TRUE") {
                    if (in_array($_FILES['file']['type'], $allowed)) {
                        if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
                            $uploadIVROutput->output = "TRUE";
                            $uploadIVROutput->message = "File uploaded Successfully";
                            $uploadIVROutput->ivrJson = $this->inbound_model->getIvrJson();
                        } else {
                            $uploadIVROutput->output = "FALSE";
                            $uploadIVROutput->message = "MOVE UPLOADED FILE FAILED!!";
                            $uploadIVROutput->error = error_get_last();
                        }
                    } else {
                        $uploadIVROutput->output = "FALSE";
                        $uploadIVROutput->message = "Please Upload proper file";
                    }
                } else {
                    $uploadIVROutput->output = "FALSE";
                    $uploadIVROutput->message = $uploadIVROutput->setIvrFileNameData->ErrorMessage;
                }
            } else {

                $uploadIVROutput->output = "FALSE";
                $uploadIVROutput->message = "The Folder Is Not Writable, Please Check Folder Permisson";
                $uploadIVROutput->folderPath = $target_file;
            }
        } else {
            $uploadIVROutput->output = "FALSE";
            $uploadIVROutput->message = $this->uploadErrorMessage($_FILES['file']['type']);
        }

        $data['message'] = $uploadIVROutput;
        $this->load->view('json', $data);
    }

    private function uploadErrorMessage($code) {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;

            default:
                $message = "Unknown upload error";
                break;
        }
        return $message;
    }

    public function saveManageDispose() {

        $processId = $this->input->get_post('processId');
        $processName = $this->input->get_post('processName');
        $disposeArray = $this->input->get_post('disposeArray');
        $result = $this->dispose_model->saveManageDispose($processId, $processName, $disposeArray);
        $data['message'] = $result;
        $this->load->view('json', $data);
    }

    public function saveQualityDispose() {

        $processId = $this->input->get_post('processId');
        $processName = $this->input->get_post('processName');
        $disposeArray = $this->input->get_post('disposeArray');
        $result = $this->dispose_model->saveQualityDispose($processId, $processName, $disposeArray);
        $data['message'] = $result;
        $this->load->view('json', $data);
    }

    public function saveVerifierDispose() {

        $processId = $this->input->get_post('processId');
        $processName = $this->input->get_post('processName');
        $disposeArray = $this->input->get_post('disposeArray');
        $result = $this->dispose_model->saveVerifierDispose($processId, $processName, $disposeArray);
        $data['message'] = $result;
        $this->load->view('json', $data);
    }

    public function saveBackOfficeDispose() {

        $processId = $this->input->get_post('processId');
        $processName = $this->input->get_post('processName');
        $disposeArray = $this->input->get_post('disposeArray');
        $result = $this->dispose_model->saveBackOfficeDispose($processId, $processName, $disposeArray);
        $data['message'] = $result;
        $this->load->view('json', $data);
    }

    public function getQualityProcessDispose() {

        $processId = $this->input->get_post('processId');
        $result = $this->dispose_model->getQualityProcessDispose($processId);
        $data['message'] = $result;
        $this->load->view('json', $data);
    }

    public function getBackOfficeProcessDispose() {

        $processId = $this->input->get_post('processId');
        $result = $this->dispose_model->getBackOfficeProcessDispose($processId);
        $data['message'] = $result;
        $this->load->view('json', $data);
    }

    public function getVerifierProcessDispose() {

        $processId = $this->input->get_post('processId');
        $result = $this->dispose_model->getVerifierProcessDispose($processId);
        $data['message'] = $result;
        $this->load->view('json', $data);
    }

    public function refreshVideoExtensionSet() {
        $refreshExtension = $this->standard_model->refreshVideoExtensionSet();
        $data['message'] = $refreshExtension;
        $this->load->view('json', $data);
    }

    public function getChatMessage() {
        $processId = $this->input->get_post('processId');
        $result = $this->standard_model->getChatMessage($processId);
        $data['message'] = $result;
        $this->load->view('json', $data);
    }

    public function saveChatMessage() {

        $processId = preg_replace("/(\t|'|\"|\n)/", '', $this->input->get_post('processId'));
        $processName = preg_replace("/(\t|'|\"|\n)/", '', $this->input->get_post('processName'));
        $chatUserNameAllow = preg_replace("/(\t|'|\"|\n)/", '', $this->input->get_post('chatUserNameAllow'));
        $defaultUserName = preg_replace("/(\t|'|\"|\n)/", '', $this->input->get_post('defaultUserName'));
        $headingMessage = preg_replace("/(\t|'|\"|\n)/", '', $this->input->get_post('headingMessage'));
        $startChatMessage = preg_replace("/(\t|'|\"|\n)/", '', $this->input->get_post('startChatMessage'));
        $initiatorChatMessage = preg_replace("/(\t|'|\"|\n)/", '', $this->input->get_post('initiatorChatMessage'));
        $endChatMessage = preg_replace("/(\t|'|\"|\n)/", '', $this->input->get_post('endChatMessage'));
        $offlineChatMessage = preg_replace("/(\t|'|\"|\n)/", '', $this->input->get_post('offlineChatMessage'));
        $holdChatMessage = preg_replace("/(\t|'|\"|\n)/", '', $this->input->get_post('holdChatMessage'));
        $transAgntChatMessage = preg_replace("/(\t|'|\"|\n)/", '', $this->input->get_post('transAgntChatMessage'));
        $transHoldChatMessage = preg_replace("/(\t|'|\"|\n)/", '', $this->input->get_post('transHoldChatMessage'));
        $transUnholdChatMessage = preg_replace("/(\t|'|\"|\n)/", '', $this->input->get_post('transUnholdChatMessage'));
        $confAgntChatMessage = preg_replace("/(\t|'|\"|\n)/", '', $this->input->get_post('confAgntChatMessage'));
        $confHoldChatMessage = preg_replace("/(\t|'|\"|\n)/", '', $this->input->get_post('confHoldChatMessage'));
        $confUnholdChatMessage = preg_replace("/(\t|'|\"|\n)/", '', $this->input->get_post('confUnholdChatMessage'));
        $setChatQueueSetting = preg_replace("/(\t|'|\"|\n)/", '', $this->input->get_post('setChatQueueSetting'));
        $offlineHeadingMessage = preg_replace("/(\t|'|\"|\n)/", '', $this->input->get_post('offlineHeadingMessage'));

        /* passing variable to lua function */
        $agentChatQueueFlag = ($setChatQueueSetting == 1 ? "PRIORITY" : "TIME");
        $agentNameFlag = ($chatUserNameAllow == 1 ? "TRUE" : "FALSE");
        $chatProcessSetting = '{"default_name_flag": "' . $agentNameFlag . '" ,'
                . '"default_name": "' . $defaultUserName . '" ,'
                . '"heading_message": "' . $headingMessage . '" ,'
                . '"offline_heading_message": "' . $offlineHeadingMessage . '" ,'
                . '"agent_chat_queue_flag": "' . $agentChatQueueFlag . '" ,'
                . '"start_chat_message": "' . $startChatMessage . '",'
                . '"initiator_message": "' . $initiatorChatMessage . '",'
                . '"end_chat_message": "' . $endChatMessage . '",'
                . '"offline_chat_message": "' . $offlineChatMessage . '",'
                . '"hold_chat_message": "' . $holdChatMessage . '",'
                . '"transfer_agent": "' . $transAgntChatMessage . '",'
                . '"transfer_hold": "' . $transHoldChatMessage . '",'
                . '"transfer_unhold": "' . $transUnholdChatMessage . '",'
                . '"conference_agent": "' . $confAgntChatMessage . '",'
                . '"conference_hold": "' . $confHoldChatMessage . '",'
                . '"conference_unhold": "' . $confUnholdChatMessage . '"'
                . '}';

        $result = $this->standard_model->saveChatMessage($processId, $processName, $chatUserNameAllow, $defaultUserName, $startChatMessage, $endChatMessage, $holdChatMessage, $transAgntChatMessage, $transHoldChatMessage, $transUnholdChatMessage, $confAgntChatMessage, $confHoldChatMessage, $confUnholdChatMessage, $setChatQueueSetting, $headingMessage, $initiatorChatMessage, $offlineChatMessage, $offlineHeadingMessage);
        if ($result == true) {
            $luaResult = $this->lua_model->setChatProccessJson($processId, $agentChatQueueFlag, $agentNameFlag, $chatProcessSetting);
        }
        $data['message'] = $result;
        $this->load->view('json', $data);
    }

    public function getConcurrentChat() {
        $agentId = $this->input->get_post('userId');
        $result = $this->standard_model->getConcurrentChat($agentId);
        if (empty($result)) {
            $data['message'] = "";
        } else {
            $data['message'] = $result->concurrent_chat;
        }
        $this->load->view('json', $data);
    }

    public function setConcurrentChat() {
        $agentId = $this->input->get_post('userId');
        $currentChat = $this->input->get_post('currentChat');
        $result = $this->standard_model->setConcurrentChat($agentId, $currentChat);
        $data['message'] = $result;
        $this->load->view('json', $data);
    }

    public function getProcessLeadsets() {
        $processId = $this->input->get_post('processId');
        $data['message'] = $this->standard_model->getProcessLeadset($processId);
        $this->load->view('json', $data);
    }

    public function chatLiveMonitorData() {
        $processId = $this->input->get_post('processId');
        $data['message'] = $this->standard_model->chatLiveMonitorData($processId);
        $this->load->view('json', $data);
    }

    public function updateUserSession() {
        $newdata = array('logged_in' => true);
        $this->session->set_userdata($newdata);
        $data['message'] = 1;
        $this->load->view('json', $data);
    }

    public function getCallbackData() {
        $agentIds = $this->input->get_post('selectedAgents');

        //        echo '<pre>';
        //        print_r($agentIds);
        //        exit;

        $result = array();
        if (count($agentIds) > 0) {
            foreach ($agentIds as $key => $agentId) {
                $result[] = $this->callback_model->viewcallback($agentId);
            }
        }

        $data['message'] = $result;
        $this->load->view('json', $data);
    }

    public function setCallbackToNewAgent() {
        $objectData = new stdClass();
        $currentAgent = $this->input->get_post('currentAgent');
        $trasferAgent = $this->input->get_post('trasferAgent');
        $allCallbackLeadId = $this->input->get_post('allCallbackLeadId');
        $objectData->result = $this->lua_model->reassignCallback($currentAgent, $trasferAgent, $allCallbackLeadId);
        $objectData->table = $this->callback_model->viewcallback($this->input->get_post('currentAgent'));
        $data['message'] = $objectData;
        $this->load->view('json', $data);
    }

    public function getDailySummaryReport() {
        $startDate = $this->input->get_post('startDate');
        $endDate = $this->input->get_post('endDate');
        $userCsv = $this->input->get_post('userCsv');
        $result = $this->reporting_model->getDailySummaryReport($startDate, $endDate, $userCsv);
        $data['message'] = $result;
        $this->load->view('json', $data);
    }

    public function getProcessApiDetail() {
        $processId = $this->input->get_post('processId');
        $data['message'] = $this->lead_model->getProcessApiDetail($processId);
        $this->load->view('json', $data);
    }

    public function reGenerateToken() {
        $processId = $this->input->get_post('processId');
        $data['message'] = $this->lead_model->reGenerateToken($processId);
        $this->load->view('json', $data);
    }

    public function saveExternalNoList() {

        $numId = $this->input->get_post("numId");
        $custName = $this->input->get_post("custName");
        $phoneNum = $this->input->get_post("phoneNum");
        $processId = $this->input->get_post("processId");
        $oldNumber = $this->input->get_post("oldNumber");
        $data['message'] = $this->standard_model->saveExternalNoList($numId, $custName, $phoneNum, $processId, $oldNumber);
        $this->load->view('json', $data);
    }

    public function deleteExternalNoList() {
        $numId = $this->input->get_post("numId");
        $processId = $this->input->get_post("processId");
        $phoneNum = $this->input->get_post("phoneNum");
        $data['message'] = $this->standard_model->deleteExternalNoList($numId, $processId, $phoneNum);
        $this->load->view('json', $data);
    }

    public function createExternalNo() {
        $name = $this->input->get_post("name");
        $number = $this->input->get_post("number");
        $processId = $this->input->get_post("processId");
        $processName = $this->input->get_post("processName");
        $campaignId = $this->input->get_post("campaignId");
        $campaignName = $this->input->get_post("campaignName");
        $data['message'] = $this->standard_model->createExternalNo($name, $number, $processId, $processName, $campaignId, $campaignName);
        $this->load->view('json', $data);
    }

    public function setTimezoneSettings() {
        $processId = $this->input->get_post("processId");
        $processName = $this->input->get_post('processName');
        $disposeArray = $this->input->get_post('disposeArray');
        $disposeVerifierArray = $this->input->get_post('disposeVerifierArray');
        $timeZoneFlag = $this->input->get_post("timeZoneFlag");
        $nextCallIntervalHr = $this->input->get_post("nextCallIntervalHr");
        $nextCallIntervalMin = $this->input->get_post("nextCallIntervalMin");
        $nextCallIntervalSec = $this->input->get_post("nextCallIntervalSec");
        $processCallAttempt = $this->input->get_post("processCallAttempt");
        $processMaxCallPerDay = $this->input->get_post("processMaxCallPerDay");
        $USA_FTC_Setting = $this->input->get_post("USA_FTC_Setting");

        $timezonearr = array();
        $timezonearr['estStartTime'] = $this->input->get_post('estStartTime');
        $timezonearr['estEndTime'] = $this->input->get_post('estEndTime');
        $timezonearr['cstStartTime'] = $this->input->get_post("cstStartTime");
        $timezonearr['cstEndTime'] = $this->input->get_post("cstEndTime");
        $timezonearr['mstStartTime'] = $this->input->get_post("mstStartTime");
        $timezonearr['mstEndTime'] = $this->input->get_post("mstEndTime");
        $timezonearr['pstStartTime'] = $this->input->get_post("pstStartTime");
        $timezonearr['pstEndTime'] = $this->input->get_post("pstEndTime");

        $data['message'] = $this->standard_model->setTimezoneSettings($processId, $processName, $timeZoneFlag, $disposeArray, $disposeVerifierArray, $nextCallIntervalHr, $nextCallIntervalMin, $nextCallIntervalSec, $processCallAttempt, $processMaxCallPerDay, $USA_FTC_Setting, $timezonearr);
        $this->load->view('json', $data);
    }

    public function getTimeZoneSetting() {
        $processId = $this->input->get_post("processId");
        $data['message'] = $this->standard_model->getTimeZoneSetting($processId);
        $this->load->view('json', $data);
    }

    public function getInboundDelayTime() {
        $processId = $this->input->get_post("processId");
        $data['message'] = $this->standard_model->getInboundDelayTime($processId);
        $this->load->view('json', $data);
    }

    public function setInboundDelayTime() {
        $processId = $this->input->get_post("processId");
        $delayTimeInSec = $this->input->get_post("delayTimeInSec");
        $data['message'] = $this->standard_model->setInboundDelayTime($processId, $delayTimeInSec);
        $this->load->view('json', $data);
    }

    public function getUserMappedProcess() {
        $userId = $this->input->get_post("userId");
        $data['message'] = $this->standard_model->getUserMappedProcess($userId);
        $this->load->view('json', $data);
    }

    public function getMarketingChatSetting() {

        $processId = $this->input->get_post("processId");
        $data['message'] = $this->standard_model->getMarketingChatSetting($processId);
        $this->load->view('json', $data);
    }

    public function saveCustDownloadJson() {

        $downloadJson = $this->input->get_post("downloadJson");
        $data['message'] = $this->user_model->saveCustDownloadJson($downloadJson);
        $this->load->view('json', $data);
    }

    public function setMarketingChatSetting() {
        $processId = $this->input->get_post("processId");
        $processName = $this->input->get_post("processName");
        $toleranceTimeMin = $this->input->get_post("toleranceTimeMin");
        $toleranceTimeSec = $this->input->get_post("toleranceTimeSec");
        $hostName = $this->input->get_post("hostName");
        $portNumber = $this->input->get_post("portNumber");
        $secureSetting = $this->input->get_post("secureSetting");
        $authUsername = $this->input->get_post("authUsername");
        $authPassword = $this->input->get_post("authPassword");
        $authEmailTitle = $this->input->get_post("authEmailTitle");
        $authEmailTitleImage = $this->input->get_post("authEmailTitleImage");
        $authEmailSubject = $this->input->get_post("authEmailSubject");
        $mappingFlag = $this->input->get_post("mappingFlag");
        $data['message'] = $this->standard_model->setMarketingChatSetting($processId, $processName, $toleranceTimeMin, $toleranceTimeSec, $mappingFlag, $hostName, $portNumber, $secureSetting, $authUsername, $authPassword, $authEmailTitle, $authEmailTitleImage, $authEmailSubject);
        $this->load->view('json', $data);
    }

    public function fixChannelsUtilized() {

        $keyName = $this->input->get_post('keyName');
        $fixBy = $this->input->get_post('fixBy');

        $channelPath = $this->config->item('channelUtilization');
        $url = $channelPath . "/fixChannelsUtilized()";

        $parameters = array(
            'keyName' => $keyName,
            'fixBy' => $fixBy);

        $data['message'] = $this->common_model->guzzelCallDirecctOutput($url, $parameters);
        $this->load->view('json', $data);
    }

    //get leadJosn for agent logger start
    public function getLoggerLeadJson() {
        $disposeId = $this->input->get_post('disposeId');
        $data['message'] = $this->standard_model->getLoggerLeadJson($disposeId);
        $this->load->view('json', $data);
    }

    //get leadJosn for agent logger end
    //change password start
    public function changePassword() {
        $currentpassword = $this->input->get_post('currentpassword');
        $resetPassword = $this->input->get_post('resetPassword');
        $data['message'] = $this->standard_model->changePassword($currentpassword, $resetPassword);
        $this->load->view('json', $data);
    }

    public function setLeadInRedis() {

        $url = "http://123.252.232.234:8080/voitekk/webApis/setLeadDetailInProcess";
        // $url ="https://103.38.38.99/voitekk/webApis/setLeadDetailInProcess";
        // $url ="https://localhost/voitekk/leadUpdater/setLeadDetailInProcess";
        // $ch = curl_init( $url );

        $data['json'] = json_encode(array(
            "customerNumber" => "9890496017",
            "processId" => "58",
            "processName" => "autoTest",
            "processType" => "Auto",
            "campaignId" => "1",
            "campaignName" => "TestCampaign",
            "leadsetId" => "1039",
            "leadsetName" => "autoTest",
                )
        );

        $data['tokenId'] = "8a7eca443778a9599ec1791f858f6df5";
        // $data['tokenId'] = "bf3d47c25f2de41363e81e71e7205814";
        $post = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(//<--- Added this code block
            'Content-Type: application/json',
            'Content-Length: ' . strlen($post))
        );
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        echo "Error -- " . curl_error($ch);
        echo "<br/>";
        curl_close($ch); // Seems like good practice
        echo "Result -- " . $result;
    }

    //change password end
    //get did json start
    public function getDidJson() {
        $data['message'] = $this->standard_model->getDidJson();
        $this->load->view('json', $data);
    }

    //get did json end
    //copyIVRTree start
    public function copyIVRTree() {
        $copyFrom = $this->input->get_post('copyFrom');
        $copyTo = $this->input->get_post('copyTo');
        $jsonToCopy = $this->input->get_post('jsonToCopy');
        $data['message'] = $this->standard_model->copyIVRTree($copyFrom, $copyTo, $jsonToCopy);
        $this->load->view('json', $data);
    }

    //copyIVRTree end
    //copyApiJsonTree start
    public function copyApiJson() {

        $mainProcess = $this->input->get_post('mainProcess');
        $alternateProcessCsv = $this->input->get_post('alternateProcessCsv');
        $data['message'] = $this->process_model->copyApiJson($mainProcess, $alternateProcessCsv);
        $this->load->view('json', $data);
    }

    //copyApiJsonTree end
    //copyProcessTabApiJson start
    public function copyProcessTabApiJson() {

        $mainProcess = $this->input->get_post('mainProcess');
        $alternateProcessCsv = $this->input->get_post('alternateProcessCsv');
        $data['message'] = $this->process_model->copyProcessTabApiJson($mainProcess, $alternateProcessCsv);
        $this->load->view('json', $data);
    }

    //copyProcessTabApiJson

    public function testMailSetting() {

        $emailReceiver = $this->input->get_post('emailReceiver');
        $userName = $this->input->get_post('userName');
        $password = $this->input->get_post('password');
        $SMTPAuth = $this->input->get_post('SMTPAuth');
        $SMTPSecure = $this->input->get_post('SMTPSecure');
        $Host = $this->input->get_post('Host');
        $Port = $this->input->get_post('Port');
        $SMTPDebug = $this->input->get_post('SMTPDebug');

        $data['message'] = $this->message_model->testMailSetting($emailReceiver, $userName, $password, $SMTPAuth, $SMTPSecure, $Host, $Port, $SMTPDebug);
        $this->load->view('json', $data);
    }

    public function getUserLastState() {

        $userId = $this->input->get_post('userId');
        $userState = $this->lua_model->getUserStatusCurrentState($userId);
        $userStateObj = new stdClass();
        if (isset($userState->output[0][4])) {

            $userStateObj->userstate = $userState->output[0][4]->userState;
        } else {

            $userStateObj->userstate = "logout";
        }

        // get last state time
        if (isset($userState->output[0][3])) {

            if ($userState->output[0][3]->epochTimeStamp != 0) {

                $userStateObj->time = date('Y-m-d H:i:s', $userState->output[0][3]->epochTimeStamp);
            } else {

                $userStateObj->time = "-";
            }
        } else {

            $userStateObj->time = "-";
        }
        $data['message'] = $userStateObj;
        $this->load->view('json', $data);
    }

    public function apiTest() {

        $data = array(
            'firstname' => "firstname!@#",
            'lastname' => "lastname!@#",
            'username' => "username!@#",
            'password' => "password!@#",
            'accesslevel' => "accesslevel!@#",
            'email' => "email!@#",
            'mobile' => "mobile!@#",
            'address' => "address",
            'city' => "city!@#",
            'pincode' => "pincode!@#",
            'nationality' => "nationality!@#",
            'bloodgroup' => "bloodgroup!@#",
            'qualification' => "qualification!@#",
            'status' => "status!@#",
            'pan' => "pan!@#",
            'dob' => "dob!@#",
            'gender' => "gender!@#",
            'extension' => "extension!@#",
            'extension_type' => "extension_type!@#",
            'super_admin_flag' => "super_admin_flag!@#",
        );

        $this->user_model->createUserApiCall($data);
        // $querystring = parse_url('http://www.mysite.com?id=05&name=johnny', PHP_URL_QUERY);
        // print_r($querystring);
        // parse_str($querystring, $vars);
        // print_r($vars);

        $url = "http://www.mysite.com?id=05&name=johnny";
        echo substr($url, 0, strpos($url, "?"));
        // echo strstr($url, '?');
        // $parts = parse_url($url);
        // print_r($parts);
        // parse_str($parts['query'], $query);
        // print_r($query);
        // $array = array("value1", "value2", "value3", "...", "valuen");
        // print_r($array);
        // $array_data = implode("array_separator", $array);
        // print_r($array_data);
        // $array = explode("array_separator", $array_data);
        // print_r($array);
        // $config = $this->db->query("SELECT * FROM `config` where id = 97 ")->row();
        // if( $config ){
        //   print_r($config);
        // }
        // else{
        //   print_r("NOT FOUND");
        // }
    }

    public function demoRequest() {

        $url = "https://localhost/voitekk/webApis/setLeadDetailInProcess";
        // $ch = curl_init( $url );

        $data['json'] = json_encode(array(
            "customerNumber" => "9189898989",
            "customerName" => "pareen",
            "customerEmail" => "vijeesh@gmail.com",
            "processId" => "14",
            "processName" => "testDemo",
            "processType" => "Preview",
            "campaignId" => "1",
            "campaignName" => "Campaign",
            "leadsetId" => "30",
            "leadsetName" => "demodeo",
            "email" => "test@gmail.com",
            "order_id_encrypt" => "BVDAXWFG",
            "last_name" => "Test",
            "address" => "2-c,shanti bhavan",
            "pincode" => "400001",
            "city" => "mumbai",
            "state" => "maharashtra",
            "date" => "31-05-16 12:11",
            "utm_source" => "test",
            "delivery_providers_available" => "Delhivery, GoJavasShiprocketEcom express",
            "payment_status" => "Pending",
            "gateway" => "COD",
            "otp_verified" => "No",
            "product_name" => "Obenex6",
            "amount" => "2499",
                )
        );
        //$data['tokenId'] = "c9d53ea34f60b96ef2c9f01a1c5d4c9e";
        $data['tokenId'] = "58a90b5b2927ac16ec6ad69aee7be634";
        $post = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(//<--- Added this code block
            'Content-Type: application/json',
            'Content-Length: ' . strlen($post))
        );
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        echo "Error -- " . curl_error($ch);
        echo "<br/>";
        curl_close($ch); // Seems like good practice
        echo "Result -- " . $result;
    }

    public function disposeCallApiCall() {

        $url = "https://www.voitekk.com/voitekk/chatServer/externalCallEnd";

        $data['userName'] = "userName";
        $data['referenceUuid'] = "referenceUuid";
        $data['processId'] = "processId";
        $data['disposeId'] = "disposeId";
        $data['disposeName'] = "disposeName";
        $post = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(//<--- Added this code block
            'Content-Type: application/json',
            'Content-Length: ' . strlen($post))
        );
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        echo "Error -- " . curl_error($ch);
        echo "<br/>";
        curl_close($ch); // Seems like good practice
        echo "Result -- " . $result;
    }

    //testUserApiCall start
    public function testUserApiCall() {

        $apiOutput = new stdClass();
        $urlType = $this->input->get_post('urlType');
        $urlString = $this->input->get_post('urlString');

        $api_url = substr($urlString, 0, strpos($urlString, "?"));
        $parts = parse_url($urlString);
        if (empty($parts['query'])) {
            $apiOutput->output = "FALSE";
            $apiOutput->getStatusCode = "400";
            $apiOutput->getReasonPhrase = "Bad Request";
            $apiOutput->getResponse = "Please Enter Valid Url";
        } else if (!empty($parts['query'])) {
            parse_str($parts['query'], $api_parameter);

            $userId = $this->session->userdata("id");
            $userName = $this->session->userdata("username");

            $data = array(
                'id' => 4,
                'firstname' => "demo",
                'lastname' => "user",
                'username' => "testUser",
                'password' => "4a722057326566eaa14dbef4f97ca555",
                'accesslevel' => 4,
                'email' => "demouser@voitekk.com",
                'mobile' => "8080808080",
                'address' => "Mumbai",
                'city' => "mumbai",
                'pincode' => "400073",
                'nationality' => "Indian",
                'bloodgroup' => "Arh",
                'qualification' => "B.E",
                'status' => 1,
                'pan' => "hehehehe",
                'dob' => "2016-03-25",
                'gender' => "male",
                'extension' => 0,
                'extension_type' => 0,
                'super_admin_flag' => 0,
            );

            try {

                $parameterArray = array();
                foreach ($api_parameter as $key => $selectedParam) {

                    $value = "";
                    switch ($selectedParam) {
                        case '@userId':
                            $value = $data["id"];
                            break;
                        case '@firstName':
                            $value = $data["firstname"];
                            break;
                        case '@lastName':
                            $value = $data["lastname"];
                            break;
                        case '@userName':
                            $value = $data["username"];
                            break;
                        case '@password':
                            $value = $data["password"];
                            break;
                        case '@accesslevel':
                            $value = $data["accesslevel"];
                            break;
                        case '@email':
                            $value = $data["email"];
                            break;
                        case '@mobile':
                            $value = $data["mobile"];
                            break;
                        case '@address':
                            $value = $data["address"];
                            break;
                        case '@city':
                            $value = $data["city"];
                            break;
                        case '@pincode':
                            $value = $data["pincode"];
                            break;
                        case '@nationality':
                            $value = $data["nationality"];
                            break;
                        case '@bloodGroup':
                            $value = $data["bloodgroup"];
                            break;
                        case '@qualification':
                            $value = $data["qualification"];
                            break;
                        case '@status':
                            $value = $data["status"];
                            break;
                        case '@pan':
                            $value = $data["pan"];
                            break;
                        case '@dob':
                            $value = $data["dob"];
                            break;
                        case '@gender':
                            $value = $data["gender"];
                            break;
                        case '@extension':
                            $value = $data["extension"];
                            break;
                        case '@extensionType':
                            $value = $data["extension_type"];
                            break;
                        case '@superAdminFlag':
                            $value = $data["super_admin_flag"];
                            break;

                        default:
                            $value = $selectedParam;
                            break;
                    }
                    $parameterArray[$key] = $value;
                }
                $apiOutput = $this->common_model->guzzelWebApisCallWithMethod($api_url, $parameterArray, $urlType);
            } catch (Exception $e) {

                $apiOutput->output = "FALSE";
                $apiOutput->getStatusCode = "500";
                $apiOutput->getReasonPhrase = "Internal Server Error";
                $apiOutput->getResponse = "API_RESPONSE_FAILED";
            }
        } else {
            $apiOutput->output = "FALSE";
            $apiOutput->getStatusCode = "500";
            $apiOutput->getReasonPhrase = "Internal Server Error";
            $apiOutput->getResponse = "API_RESPONSE_FAILED";
        }
        $data['message'] = $apiOutput;
        $this->load->view('json', $data);
    }

    public function testLiveMonitor() {

        $processCsv = $this->input->get_post('processCsv');
        $userCsv = $this->input->get_post('userCsv');
        $logFlag = $this->input->get_post('logFlag');
        $data = $this->livemonitor_model->callDataFromRedis($processCsv, $userCsv, $logFlag);
        print_r($data);
    }

    public function testAgentPerformance() {

        $lastDate = '20160816';
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
    }

    //testUserApiCall end

    public function getDroppedCalls() {
        $processIdCsv = $this->input->get_post('processIdCsv');
        $autoProcessIdCsv = $this->input->get_post('autoProcessIdCsv');
        $data['message'] = $this->livemonitor_model->getDroppedCalls($processIdCsv, $autoProcessIdCsv);
        $this->load->view('json', $data);
    }

    public function testApi() {

        $sec = "536813";
        echo $this->common_model->secondsToTime($sec);
    }

    public function mailTest() {
        $emailReceiver = $this->input->get_post('emailReceiver');
        $emailSender = $this->input->get_post('emailSender');
        $userName = $this->input->get_post('userName');
        $password = $this->input->get_post('password');
        $SMTPAuth = $this->input->get_post('SMTPAuth');
        $SMTPSecure = $this->input->get_post('SMTPSecure');
        $Host = $this->input->get_post('Host');
        $Port = $this->input->get_post('Port');
        $SMTPDebug = $this->input->get_post('SMTPDebug');
        $emailSubject = $this->input->get_post('emailSubject');
        $emailMessage = $this->input->get_post('emailMessage');

        $this->load->library('voitekk_PHPMailer');
        $mail = new PHPMailer();
        $mail->IsSMTP(); // we are going to use SMTP
        $mail->SMTPAuth = $SMTPAuth; // enabled SMTP authentication
        $mail->SMTPSecure = $SMTPSecure; // prefix for secure protocol to connect to the server
        $mail->Host = $Host; // setting GMail as our SMTP server
        $mail->Port = $Port; // SMTP port to connect to GMail
        $mail->Username = $userName; // user email address
        $mail->Password = $password; // password in GMail
        $mail->SetFrom($emailSender, ''); //Who is sending the email
        $mail->AddReplyTo($emailSender, ""); //email address that receives the response
        $mail->Subject = $emailSubject;
        $mail->Body = $emailMessage;
        $mail->AltBody = "you may think";
        $mail->SMTPDebug = $SMTPDebug;
        $destino = $emailReceiver; // Who is addressed the email to
        $mail->AddAddress($destino);
        //$mail->AddAttachment("images/phpmailer.gif");      // some attached files
        //$mail->AddAttachment("images/phpmailer_mini.gif"); // as many as you want
        $sendMailReportOpt = new stdClass();
        if (!$mail->Send()) {
            // http_response_code(503);
            $sendMailReportOpt->output = "FALSE";
            $sendMailReportOpt->message = "Error: " . $mail->ErrorInfo;
        } else {
            // http_response_code(200);
            $sendMailReportOpt->output = "TRUE";
            $sendMailReportOpt->message = "Message Sent <br/> Check Test Mail On $emailReceiver";
        }

        print_r("<br/>SMTPAuth ::" . $SMTPAuth);
        print_r("<br/>SMTPSecure ::" . $SMTPSecure);
        print_r("<br/>Host ::" . $Host);
        print_r("<br/>Port ::" . $Port);
        print_r("<br/>userName ::" . $userName);
        print_r("<br/>password ::" . $password);
        print_r("<br/>emailSender ::" . $emailSender);
        print_r("<br/>emailSubject ::" . $emailSubject);
        print_r("<br/>emailMessage ::" . $emailMessage);
        print_r($sendMailReportOpt);
    }

    //get call forwarded number start
    public function getCallForwardNumbers() {
        $userId = $this->input->get_post('userId');
        $data['message'] = $this->standard_model->getCallForwardNumbers($userId);
        $this->load->view('json', $data);
    }

    //get call forwarded number end
    //add call forwarded number start
    public function addCallForwardNumbers() {

        $number = $this->input->get_post('number');
        $agentId = $this->input->get_post('agentId');
        $username = $this->input->get_post('username');
        $agentname = $this->input->get_post('agentname');
        $data['message'] = $this->standard_model->addCallForwardNumbers($agentId, $number, $username, $agentname);
        $this->load->view('json', $data);
    }

    //add call forwarded number end
    //delete call forward number start
    public function deleteCallForwardNumbers() {

        $number = $this->input->get_post('number');
        $agentId = $this->input->get_post('agentId');
        $data['message'] = $this->standard_model->deleteCallForwardNumbers($agentId, $number);
        $this->load->view('json', $data);
    }

    //delete call forward number end
    //get auto dispose array start
    public function getAutoDisposeArray() {

        $multiLevelDisposeAllow = $this->input->get_post('multiLevelDisposeAllow');
        $data['message'] = $this->standard_model->getAutoDisposeArray($multiLevelDisposeAllow);
        $this->load->view('json', $data);
    }

    //get auto dispose array end
    //resend api event start
    public function resendApiEvent() {

        $apiId = $this->input->get_post('apiId');
        $data['message'] = $this->standard_model->getApiLogData($apiId);
        $this->load->view('json', $data);
    }

    //resend api event end
    // get dispose api detail added
    public function getResendApiDocument() {

        $rfud = $this->input->get_post('rfud');
        $processId = $this->input->get_post('processId');
        $data['message'] = $this->api_model->getResendApiDocument($rfud, $processId);
        $this->load->view('json', $data);
    }

    public function resendApiDataOnLogger() {

        $apiUrl = $this->input->get_post('apiUrl');
        $apiParam = $this->input->get_post('apiParam');
        $apiMethod = $this->input->get_post('apiMethod');
        $rfud = $this->input->get_post('rfud');
        $phoneNumber = $this->input->get_post('phoneNumber');
        $customerName = $this->input->get_post('customerName');
        $campaignId = $this->input->get_post('campaignId');
        $campaignName = $this->input->get_post('campaignName');
        $processId = $this->input->get_post('processId');
        $processName = $this->input->get_post('processName');
        $leadsetId = $this->input->get_post('leadsetId');
        $leadsetName = $this->input->get_post('leadsetName');
        $oldApiState = $this->input->get_post('oldApiState');
        $disposeCallId = $this->input->get_post('disposeCallId');
        $oldApiStateId = $this->input->get_post('oldApiStateId');
        $data['message'] = $this->api_model->resendApiDataOnLogger($apiUrl, $apiParam, $apiMethod, $rfud, $phoneNumber, $customerName, $campaignId, $campaignName, $processId, $processName, $leadsetId, $leadsetName, $oldApiState, $disposeCallId, $oldApiStateId);
        $this->load->view('json', $data);
    }

    //get old api log data start
    public function getOldApiLogs() {

        $apiId = $this->input->get_post('apiId');
        $data['message'] = $this->standard_model->getOldApiLogs($apiId);
        $this->load->view('json', $data);
    }

    //
    public function removeAdvanceExtension() {

        $userId = 37;
        $data = $this->lua_model->removeExtension($userId);
        print_r($data);
    }

    //get old api log data end
    public function getAdvanceExtension($userId, $userName, $fullName, $accesslevel, $agentKey) {

        $output = $this->extension_model->getAdvanceExtension($userId, $userName, $fullName, $accesslevel, $agentKey);
        print_r($output);
    }

    public function getMultipleRecording() {

        $referenceUuid = $this->input->get_post('referenceUuid');
        $data['message'] = $this->cdr_model->getMultipleRecording($referenceUuid);
        $this->load->view('json', $data);
    }

    public function getServerDetail() {

        print_r($_SERVER);
    }

    //get quality super data
    public function qualitySuperDashboard() {

        $agent = $this->input->get_post('agent');
        $campaign = $this->input->get_post('campaignId');
        $process = $this->input->get_post('processId');
        $mydate = $this->input->get_post('dateFrom');
        $mydateto = $this->input->get_post('dateTo');
        $qualityAgentId = $this->input->get_post('qualityAgentId');

        $this->load->model('quality_model');
        $data['message'] = $this->quality_model->qualityDashboardData($agent, $campaign, $process, $mydate, $mydateto, $qualityAgentId);
        $this->load->view('json', $data);
    }

    public function getAgentsMissedCallData() {
        $agentid = $this->input->get_post('agentid');
        $data = array();

        $data['message']['data'] = $this->inbound_model->getAgentMissedCall($agentid);
        $this->load->view('json', $data);
    }

    public function getAgentsMissedCallsCounts() {
        $data = array();
        $data['message']['data'] = $this->inbound_model->getAgentMissedCallCount();
        $this->load->view('json', $data);
    }

    public function getAgentList() {
        $data = array();
        $data['message']['results'] = $this->inbound_model->getAgentList();
        $this->load->view('json', $data);
    }

    public function asignAgentToMisscall() {
        $postData = $this->input->post();
        $query = "update inbound_drop_cdr set last_disposed_agent_id =" . $postData['toAgent'] . " , last_disposed_agent_name ='" . $postData['toAgentName'] . "' where last_disposed_agent_id =" . $postData['fromAgent'] . " and dispose_id = 0";
        if (isset($postData['allocation_count']) && !empty($postData['allocation_count'] && $postData['allocation_count'] >= 1)) {
            $query .= " limit " . $postData['allocation_count'];
        }
        //echo $query;
        $updateResult = $this->db->query($query);
        //echo "<pre>";print_r($postData);exit;
    }

    public function reloadLeadSetCheck() {
        $processid = $this->input->get_post('processid');
        $campaignid = $this->input->get_post('campaignid');
        $query = $this->process_model->getProcessStatus($processid);

        if ($query->calling_mode == '4') {
            $response = array();

            if ($query->processstatus != 3) {

                $response['status'] = 'FALSE';
                $response['message'] = 'Pause process before reloading leadset.';
            }
        } else {
            $response['status'] = 'FALSE';
            $response['message'] = 'Process not in preview mode';
        }
        $data['message'] = $response;

        $this->load->view('json', $data);
    }

    public function reloadLeadSet() {
        $processid = $this->input->get_post('processid');
        $campaignid = $this->input->get_post('campaignid');
        $query = $this->process_model->getProcessStatus($processid);
        $data = array();
        if ($query->calling_mode == '4') {
            $response = array();

            if ($query->processstatus != 3) {
                $data['message']['output'] = "FALSE";
                $data['message']['message'] = "Pause process before reloading leadset.";
            } else {

                /* ---------------- Remove leadset from process and redis ----------------------- */
                $removeProcessLeadset = new stdClass();
                $callingmode = $this->input->get_post("callingmode");
                $processid = $this->input->get_post("processid");
                $processName = $this->input->get_post("processName");
                $campaignId = $this->input->get_post("campaignId");
                $campaignName = $this->input->get_post("campaignName");
                $leadset = $this->input->get_post("leadset");

                $leadsetCsv = "";
                $removeProcessLeadset->output = "FALSE";
                if (isset($leadset) && !empty($leadset)) {
                    foreach ($leadset as $lead) {
                        $leadsetId = $lead[0];
                        $leadsetName = $lead[1];
                        $this->preview_model->removeFromProcess($processid, $leadsetId);
                        if ($leadsetCsv == "") {
                            $leadsetCsv = $leadsetId;
                        } else {
                            $leadsetCsv = $leadsetCsv . "," . $leadsetId;
                        }

                        if ($callingmode == "listview") {
                            $this->process_model->listViewLeadsetRemove($processid, $leadsetId);
                        }
                    }

                    if ($callingmode == "Preview") {

                        $removeProcessLeadset->output = "TRUE";
                        $removeProcessLeadset->previewLeadRemoveOpt = $this->lua_model->removeLeadsetPreviewLead($processid, $leadsetCsv);
                    } else {
                        $removeProcessLeadset->output = "TRUE";
                    }
                }

                if ($removeProcessLeadset->output == 'TRUE') {

                    /* ----------------------------- Remap/Add Only fresh leads ---------------- */

                    if ($callingmode == "Preview") {

                        $checkState = $this->process_model->processStateCheck($processid);

                        if ($checkState == "FALSE") {
                            $data['message']['output'] = "FALSE";
                            $data['message']['message'] = "PROCESS_IS_NOT_LIVE";
                            $this->load->view('json', $data);
                            return;
                        }
                    }

                    $notAddedLeadId = array();
                    foreach ($leadset as $lead) {
                        $leadsetId = $lead[0];
                        $leadsetName = $lead[1];
                        //mapped leadset to process
                        $this->preview_model->addtoProcess($processid, $processName, $leadsetId, $leadsetName);
                        if ($callingmode == "Preview") {
                            $output = $this->preview_model->addUnusedLeadsToQueue($leadsetId, $leadsetName, $processid, $processName, $campaignId, $campaignName);
                            $notAddedLeadId[$leadsetId] = $output;
                        }
                    }

                    $data['message']['output'] = "TRUE";
                    $data['message']['message'] = "Leadset reload successfull.";
                    $data['message']['notadded'] = $notAddedLeadId;
                } else {
                    //display remove leadset failed  error
                    $data['message']['output'] = "FALSE";
                    $data['message']['message'] = "Error while removing Leadset. Please contact admin.";
                }
            }
        } else {
            $response['message']['output'] = 'FALSE';
            $response['message']['message'] = 'Process not in preview mode';
        }
        $this->load->view('json', $data);
    }

    public function getStickyDetails() {
        $processId = $this->input->get_post("processId");
        $data['message'] = $this->standard_model->getStickyDetails($processId);
    }

    public function saveStickyPreview() {
        $postData = $this->input->post();
        $data['message'] = "";
        if (isset($postData['processId']) && !empty($postData['processId'])) {
            $data['message'] = $this->process_model->saveStickyPreview($postData);
        } else {
            $data['message'] = json_decode('{"OUTPUT" : "FALSE","message":"Sticky Setting Not Saved."}');
        }

        $this->load->view('json', $data);
    }

    public function markMissedCallAsDialed() {
        $inbound_drop_cdr_id = $this->input->get_post('id');
        $data['result'] = $this->inbound_model->setApproached($inbound_drop_cdr_id);
        exit;
    }

    public function copyQualityManagementParams() {
        $processId = $this->input->post('processId');
        $copyFromProcessId = $this->input->post('copyFromProcessId');
        $data['message'] = $this->campaign_model->copyQualityManagementParams($processId, $copyFromProcessId);
        $this->load->view('json', $data);
    }

    public function getProcessChurnSchedule($process) {
        $data['message'] = $this->process_model->getProcessChurnSchedule($process);
        $this->load->view('json', $data);
    }

    public function getProcessLeadsetdata($process) {
        $data['message'] = $this->process_model->getProcessLeadsetdata($process);
        $this->load->view('json', $data);
    }

    public function churnLeadsetNow() {
        
        $postData = $this->input->post();
        $data['message'] = $this->process_model->churnLeadsetNow($postData);
        $this->load->view('json', $data);
    }

    public function saveChurnSchedule() {
        $postData = $this->input->post();        
        $data['message'] = $this->process_model->saveChurnSchedule($postData);
        $this->load->view('json', $data);
    }

    public function churnleadsetdatascheduler() {

        $processid = $this->input->get_post('processid');
        $leadsetIds = $this->input->get_post('leadsetIds');
        $processName = $this->input->get_post('processName');
        $campaignId = $this->input->get_post('campaignId');
        $campaignName = $this->input->get_post('campaignName');
        $callingmode = $this->input->get_post('callingmode');
        $leadsetDisposeData = $this->input->get_post('leadsetDisposeData');
        $fromchurndate = $this->input->get_post('fromchurndate');
//            $churnstarttime = $this->input->get_post('churnstarttime');
//            $churnendtime = $this->input->get_post('churnendtime');
//            $data['message'] = $this->process_model->churnleadsetdatascheduler($processid, $leadsetIds, $processName, $campaignId, $campaignName, $callingmode,
//                $leadsetDisposeData, $fromchurndate, $churnstarttime, $churnendtime);
        //        echo $abc;
        if ($this->process_model->churnleadsetdatascheduler($processid, $leadsetIds, $processName, $campaignId, $campaignName, $callingmode, $leadsetDisposeData, $fromchurndate)) {
            $data['message'] = "SENT SUCCESSFULLY";
        } else {
            $data['message'] = "ERROR IN SENDING DATA";
        }
        $this->load->view('json', $data);
    }

    //delete child params
    public function deletechildparams() {
        $childid = $this->input->get_post("childid");
        $data['message'] = $this->process_model->deletechildparams($childid);
        $this->load->view('json', $data);
    }

    // delete parent params
    public function deleteparentparams() {
        $parentId = $this->input->get_post("parentId");
        $data['message'] = $this->process_model->deleteparentparams($parentId);
        $this->load->view('json', $data);
    }

    //Backend Gateway page in gateway menu last optioon
    //reloadxml starts
    public function reloadxml() {
        // echo "<pre>";

        $server_bin_path = $this->config->item('server_bin_path');
        $fs_port = $this->config->item('fs_port');
        $fs_password = $this->config->item('fs_password');
        $server_ip = $_SERVER['SERVER_ADDR'];
        $reloadxmlajaxrequest = $this->input->get_post("reloadxmlajax");
        if ($reloadxmlajaxrequest == true && $reloadxmlajaxrequest != "") {

            $cmd = "$server_bin_path/fs_cli -H $server_ip  -P $fs_port -p $fs_password  -x 'reloadxml'";
            //echo $cmd ;
            exec($cmd, $output, $return);
            if ($return == 0) {
                $x = array("status" => "xmlcmdsuccessful");
            } else {
                $x = array("status" => "xmlcmdunsuccessful");
            }
            $data['message'] = $x;
            $this->load->view('json', $data);
        } else {
            $x = array("status" => "unknown");
            $data['message'] = $x;
            $this->load->view('json', $data);
        }
    }

    //reload sofia starts
    public function reloadmodsofia() {
        $server_bin_path = $this->config->item('server_bin_path');
        $fs_port = $this->config->item('fs_port');
        $fs_password = $this->config->item('fs_password');
        $server_ip = $_SERVER['SERVER_ADDR'];
        $reloadsofiarequest = $this->input->get_post("reloadsofiarequest");
        $curruntCallCount = array("noValue" => "noValue");
        //print_r($curruntCallCount);
        if ($reloadsofiarequest == true && $reloadsofiarequest != "") {
            //check if calls are 0
            $cmd = "$server_bin_path/fs_cli -H $server_ip  -P $fs_port  -p $fs_password  -x 'show calls count'";
            exec($cmd, $output, $return);

            if ($return == 0) {
                $a = $output;
                $b = $a[1];
                $c = explode(' ', $b);
                $d = $c[0];
                $curruntCallCount = (int) $d;

                // -----------------------------start
                if ($curruntCallCount == 0) {
                    // echo "curruntCallCount inside of calls 0: $curruntCallCount";
                    $cmd = "$server_bin_path/fs_cli -H $server_ip  -P $fs_port -p $fs_password  -x 'reload mod_sofia'";
                    exec($cmd, $output, $return);

                    if ($return == 0) {
                        $x = array("status" => "sofiacmdsuccessful");
                    } else {
                        $x = array("status" => "sofiacmdunsuccessful");
                    }
                    $data['message'] = $x;
                    $this->load->view('json', $data);
                } else {          //else if calls count is more than 0 execute this
                    // $curruntCallCountMoreThanZero = array("curruntCallCountMoreThanZero"=>"curruntCallCountMoreThanZero");
                    $x = array("status" => "callsAreMoreThanZero");
                    $data['message'] = $x;
                    $this->load->view('json', $data);
                }
                // ---------------------------end
                //make array of currunt call count
                // $curruntCallCountArray = array("curruntCallCount"=>$curruntCallCount);
                // $data['message'] = $curruntCallCountArray;
                // $this->load->view('json', $data);
                // echo "curruntCallCount".$curruntCallCount;
                // echo "<br>";
            } else {
                // echo "check calls count cmd failed";
                $x = array("status" => "showCallsCountError");
                $data['message'] = $x;
                $this->load->view('json', $data);
            }
            // echo "curruntCallCount outside of calls 0: $curruntCallCount";
            // echo var_dump($curruntCallCount);
            //if calls are 0 go below
        } else {
            // echo "no veriable/request found";
            $x = array("status" => "noVariableRequestError");
            $data['message'] = $x;
            $this->load->view('json', $data);
        }
    }

//delete Gateway function start
    public function removeFreeswitchXmlGateway() {
        //echo "i am reached into removeFreeswitchXmlGateway";
        $removeGateway = $this->input->get_post("removeGateway");
        $nameReceived = $this->input->get_post("nameReceived");
        $toBeDeleted = $nameReceived;
        //echo "::: toBeDeleted ::: $toBeDeleted :::";


        if ('removeGateway' == true && 'removeGateway' != "") {
            // echo "<pre>";
            // echo "::: removeGateway ::: $removeGateway";
            // echo "::: toBeDeleted ::: $toBeDeleted";
            // echo "</pre>";
            //delete dateway

            try {
                //echo "::: i am in try function :::";
                $doc = simplexml_load_file('sip/gateways.xml');

                $gateways = $doc->children()->gateways;

                // echo "::: toBeDeleted ::: $toBeDeleted";

                foreach ($gateways[0]->gateway as $key => $gateway) {
                    //print_r($gateway->attributes()['name']['0']);echo $toBeDeleted;exit;
                    if ($gateway->attributes()['name']['0'] == $toBeDeleted) {
                        //echo "::: Gateway Name matched ::: $toBeDeleted";
                        $dom = dom_import_simplexml($gateway);
                        //var_dump($dom);exit;
                        // print_r($dom);exit;
                        $dom->parentNode->removeChild($dom);

                        $x = array("status" => "GatewayDeleted");
                        $data['message'] = $x;
                        $this->load->view('json', $data);
                        break;
                    }
                }
                $dom = new DOMDocument('1.0');
                $dom->preserveWhiteSpace = false;
                $dom->formatOutput = true;
                $dom->loadXML($doc->asXML());
                $dom->save('sip/gateways.xml');
            } catch (Exception $e) {
                //echo "i am in else catch function";
                //  echo $e->getMessage();
                $x = array("status" => "unknown");
                $data['message'] = $x;
                $this->load->view('json', $data);
            }
        }
    }

}

?>
