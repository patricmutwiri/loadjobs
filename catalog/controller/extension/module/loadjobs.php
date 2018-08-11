<?php
class ControllerExtensionModuleLoadjobs extends Controller {
    public function index($setting) {
        $this->load->language('extension/module/loadjobs');
        $limit = $setting['limit'];
        $status = $setting['status'];
        if(!$status) {
            echo 'jobs not enabled';
        } else {
            $data['jobs'] = array();
            // query 
            $query = $this->db->query("SELECT * FROM ".DB_PREFIX."jobs WHERE status=1");
            $jobs = $query->rows;
            $data['jobs'] = $jobs;
            //json
            if(isset($this->request->get['json'])) {
                if($this->request->get['json'] == 'patricks') { 
                    echo json_encode($data);
                }
            } else {
                return $this->load->view('extension/module/loadjobs', $data);
            }
        }
    }
}
