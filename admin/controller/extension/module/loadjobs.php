<?php
class ControllerExtensionModuleLoadjobs extends Controller {
    private $error = array(); // This is used to set the errors, if any.
 
    protected function jobTables()
    {
        // create table
        $jobsT = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "jobs'");
        if(!$jobsT->num_rows) {
            $query = "CREATE TABLE ".DB_PREFIX."jobs (
                      job_id int(11) AUTO_INCREMENT,
                      ref_id varchar(50) NOT NULL,
                      business varchar(50) NOT NULL,
                      position varchar(50) NOT NULL,
                      description varchar(200) NOT NULL,
                      requirements varchar(200) NOT NULL,
                      deadline varchar(20) NOT NULL,
                      status int,
                      PRIMARY KEY  (job_id)
                      )";
            if(!$this->db->query($query)) {
                error_log('jobs table creation failed');
                $this->error['code'] = 'jobs table creation failed';
            }
        }
        
        $appsT = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "job_applications'");
        if(!$appsT->num_rows) {
            $query = "CREATE TABLE ".DB_PREFIX."job_applications (
                      application_id int(11) AUTO_INCREMENT,
                      job_id int(5) NOT NULL,
                      resume varchar(100) NOT NULL,
                      cover varchar(200) NOT NULL,
                      application_date varchar(200) NOT NULL,
                      stages varchar(200) NOT NULL,
                      PRIMARY KEY  (application_id)
                      )";
            if(!$this->db->query($query)) {
                error_log('job_applications table creation failed');
                $this->error['code'] = 'job_applications table creation failed';
            }
        }
        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    public function index() {
        // Loading the language file of loadjobs
        $this->load->language('extension/module/loadjobs'); 
     
        // Set the title of the page to the heading title in the Language file i.e., Load Offers
        $this->document->setTitle($this->language->get('heading_title'));
     
        $this->load->model('setting/module');
        
        // do delete this job
        if (isset($this->request->get['remove_id'])) { 
            $this->deleteJob();
        }

        if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            // Parse all the coming data to Setting Model to save it in database.
            $module_info = $this->model_setting_module->getModule($this->request->get['module_id']);

            $ljsettings = array();
            $ljsettings['status']    = $this->request->post['status'];
            $ljsettings['limit']     = $this->request->post['limit'];
            $ljsettings['name']                     = $this->request->post['name'];
            
            if (!isset($this->request->get['module_id'])) {
                $this->model_setting_module->addModule('loadjobs', $ljsettings);
            } else {
                $this->model_setting_module->editModule($this->request->get['module_id'], $ljsettings);
            }

            $status = $this->saveJobs();
            $data['jstatus'] = json_encode($status);
            // To display the success text on data save
            $this->session->data['success'] = $this->language->get('text_success');
     
            // Redirect to the Module Listing
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }
     
        // Assign the language data for parsing it to view
        $data['heading_title'] = $this->language->get('heading_title');
     
        $data['text_edit']    = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_content_top'] = $this->language->get('text_content_top');
        $data['text_content_bottom'] = $this->language->get('text_content_bottom');      
        $data['text_column_left'] = $this->language->get('text_column_left');
        $data['text_column_right'] = $this->language->get('text_column_right');
     
        $data['entry_code'] = $this->language->get('entry_code');
        $data['entry_limit'] = $this->language->get('entry_limit');
        $data['entry_layout'] = $this->language->get('entry_layout');
        $data['entry_position'] = $this->language->get('entry_position');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_business'] = $this->language->get('entry_business');
        $data['entry_deadline'] = $this->language->get('entry_deadline');
        $data['entry_description'] = $this->language->get('entry_description');
        $data['entry_requirements'] = $this->language->get('entry_requirements');
        $data['entry_position'] = $this->language->get('entry_position');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
     
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_add_module'] = $this->language->get('button_add_module');
        $data['button_remove'] = $this->language->get('button_remove');
         
        // This Block returns the warning if any
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }
     
        // This Block returns the error code if any
        if (isset($this->error['code'])) {
            $data['error_code'] = $this->error['code'];
        } else {
            $data['error_code'] = '';
        }     
     
        // Making of Breadcrumbs to be displayed on site
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        if (!isset($this->request->get['module_id'])) {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/loadjobs', 'user_token=' . $this->session->data['user_token'], true)
            );
        } else {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/loadjobs', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
            );
        }
        
        $data['del_url'] = $this->url->link('extension/module/loadjobs', 'user_token=' . $this->session->data['user_token'], 'SSL');
        
        if (!isset($this->request->get['module_id'])) {
            $data['action'] = $this->url->link('extension/module/loadjobs', 'user_token=' . $this->session->data['user_token'], true);
        } else {
            $data['action'] = $this->url->link('extension/module/loadjobs', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
        }
     
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
        
        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($module_info)) {
            $data['name'] = $module_info['name'];
        } else {
            $data['name'] = '';
        }

        if (isset($this->request->post['limit'])) {
            $data['limit'] = $this->request->post['limit'];
        } elseif (!empty($module_info)) {
            $data['limit'] = $module_info['limit'];
        } else {
            $data['limit'] = '';
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($module_info)) {
            $data['status'] = $module_info['status'];
        } else {
            $data['status'] = '';
        }

        // list jobs
        $data['totaljobs'] = 0;
        $data['jobs'] = null;
        if($this->jobTables()) {
            $jobs = $this->db->query('SELECT * FROM '.DB_PREFIX.'jobs');
            $data['jobs'] = $jobs->rows;
            $data['totaljobs'] = count($data['jobs']);
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/loadjobs', $data));

    }
    // delete job
    protected function deleteJob()
    {
        if (isset($this->request->get['remove_id'])) {
            $job_id = $this->request->get['remove_id'];
            $delJob = $this->db->query("DELETE FROM ".DB_PREFIX."jobs WHERE job_id = '".(int)$job_id."'");
            if($delJob){
                $this->session->data['success'] = 'Job removed successfully';
            } else {
                $this->session->data['success'] = 'Job not removed';
            }
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }
    }

    // save to db
    protected function saveJobs()
    {
        if (isset($this->request->post['loadjobs_text_field'])) {
            $jobs   = array();
            $jobs['status'] = '';
            $count  = count($_POST['loadjobs_text_field']);
            for ($i=0; $i < $count; $i++):
                $jobs['loadjobs_text_field'][$i]        = $_POST['loadjobs_text_field'][$i];
                $jobs['loadjobs_enabled_field'][$i]      = $_POST['loadjobs_enabled_field'][$i];
                $jobs['loadjobs_business_field'][$i]    = $_POST['loadjobs_business_field'][$i];
                $jobs['loadjobs_position_field'][$i]    = $_POST['loadjobs_position_field'][$i];
                $jobs['loadjobs_description_field'][$i] = $_POST['loadjobs_description_field'][$i];
                $jobs['loadjobs_requirements_field'][$i]    = $_POST['loadjobs_requirements_field'][$i];
                $jobs['loadjobs_deadline_field'][$i]        = $_POST['loadjobs_deadline_field'][$i];
                $jobs['job_id'][$i]                         = isset($_POST['job_id'][$i]) ? $_POST['job_id'][$i] : null;
                $jobs['created_at'][$i]                     = date('d-m-y h:i', time());
                // if ref exists
                $withRef = $this->db->query("SELECT * FROM " . DB_PREFIX . "jobs WHERE ref_id = '" . $this->db->escape($jobs['loadjobs_text_field'][$i])."'");
                if($withRef->rows) {
                    // update
                    $jobs['status'][] = "REF ID ". $jobs['loadjobs_text_field'][$i]." exists, try update";
                    if($this->db->query("UPDATE " . DB_PREFIX . "jobs SET `business` = '" . $this->db->escape($jobs['loadjobs_business_field'][$i]) . "', `status` = '" . $this->db->escape($jobs['loadjobs_enabled_field'][$i]) . "', `position` = '" . $this->db->escape($jobs['loadjobs_position_field'][$i]) . "', `description` = '" . $this->db->escape($jobs['loadjobs_description_field'][$i]) . "', `requirements` = '" . $this->db->escape($jobs['loadjobs_requirements_field'][$i]) . "', `ref_id` = '" . $this->db->escape($jobs['loadjobs_text_field'][$i]) . "', `deadline` = '" . $this->db->escape($jobs['loadjobs_deadline_field'][$i]) . "' WHERE job_id = '" . $jobs['job_id'][$i] . "'")) {
                        $jobs['status'][] = "Job with REF ID ". $jobs['loadjobs_text_field'][$i]." updated successfully";
                    } else {
                        $jobs['status'][] = "Job with REF ID ". $jobs['loadjobs_text_field'][$i]." not updated";
                    }
                } else {
                    if(!$this->db->query("INSERT INTO " . DB_PREFIX . "jobs SET `created_at` = '" . $jobs['created_at'][$i] . "', `ref_id` = '" . $this->db->escape($jobs['loadjobs_text_field'][$i]) . "',  `business` = '" . $this->db->escape($jobs['loadjobs_business_field'][$i]) . "', `status` = '" . $this->db->escape($jobs['loadjobs_enabled_field'][$i]) . "', `position` = '" . $this->db->escape($jobs['loadjobs_position_field'][$i]) . "', `description` = '" . $this->db->escape($jobs['loadjobs_description_field'][$i]) . "', `requirements` = '" . $this->db->escape($jobs['loadjobs_requirements_field'][$i]) . "', `deadline` = '" . $this->db->escape($jobs['loadjobs_deadline_field'][$i]) . "'")) {
                        error_log($jobs['loadjobs_text_field'][$i]. ' not saved ');
                        $jobs['status'][] = $jobs['loadjobs_text_field'][$i]. ' not saved ';
                    } else {
                        error_log($jobs['loadjobs_text_field'][$i]. ' well saved ');
                        $jobs['status'][] = $jobs['loadjobs_text_field'][$i]. ' saved ';
                    }
                }
            endfor;
            return $jobs['status'];
        } else {
            return false;
        }
        //save now
    }

    /* Function that validates the data when Save Button is pressed */
    protected function validate() {
 
        // Block to check the user permission to manipulate the module
        if (!$this->user->hasPermission('modify', 'extension/module/loadjobs')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
 
        // Block to check if the loadjobs_text_field is properly set to save into database,
        // otherwise the error is returned
        if (!$this->request->post['loadjobs_text_field']) {
            $this->error['code'] = $this->language->get('error_code');
        }
        // create table
        $jobsT = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "jobs'");
        if(!$jobsT->num_rows) {
            $query = "CREATE TABLE ".DB_PREFIX."jobs (
                      job_id int(11) AUTO_INCREMENT,
                      ref_id varchar(50) NOT NULL,
                      business varchar(50) NOT NULL,
                      position varchar(50) NOT NULL,
                      description varchar(200) NOT NULL,
                      requirements varchar(200) NOT NULL,
                      deadline varchar(20) NOT NULL,
                      status int,
                      created_at varchar(20) NULL,
                      PRIMARY KEY  (job_id)
                      )";
            if(!$this->db->query($query)) {
                error_log('jobs table creation failed');
                $this->error['code'] = 'jobs table creation failed';
            }
        }
        
        $appsT = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "job_applications'");
        if(!$appsT->num_rows) {
            $query = "CREATE TABLE ".DB_PREFIX."job_applications (
                      application_id int(11) AUTO_INCREMENT,
                      job_id int(5) NOT NULL,
                      resume varchar(100) NOT NULL,
                      cover varchar(200) NOT NULL,
                      application_date varchar(200) NOT NULL,
                      stages varchar(200) NOT NULL,
                      created_at varchar(20) NULL,
                      PRIMARY KEY  (application_id)
                      )";
            if(!$this->db->query($query)) {
                error_log('job_applications table creation failed');
                $this->error['code'] = 'job_applications table creation failed';
            }
        }
        /* End Block*/
        // Block returns true if no error is found, else false if any error detected
        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}
