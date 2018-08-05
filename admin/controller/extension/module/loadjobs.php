<?php
class ControllerExtensionModuleLoadjobs extends Controller {
    private $error = array(); // This is used to set the errors, if any.
 
    public function index() {
        // Loading the language file of loadjobs
        $this->load->language('extension/module/loadjobs'); 
     
        // Set the title of the page to the heading title in the Language file i.e., Load Offers
        $this->document->setTitle($this->language->get('heading_title'));
     
        // Load the Setting Model  (All of the OpenCart Module & General Settings are saved using this Model )
        $this->load->model('setting/setting');
     
        // Start If: Validates and check if data is coming by save (POST) method
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            // Parse all the coming data to Setting Model to save it in database.
            $this->model_setting_setting->editSetting('loadjobs', $this->request->post);
     
            // To display the success text on data save
            $this->session->data['success'] = $this->language->get('text_success');
     
            // Redirect to the Module Listing
            $this->response->redirect($this->url->link('extension/module', 'user_token=' . $this->session->data['user_token'], 'SSL'));
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
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'separator' => false
        );
        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_module'),
            'href'      => $this->url->link('extension/module', 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'separator' => ' :: '
        );
        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('extension/module/loadjobs', 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'separator' => ' :: '
        );
          
        $data['action'] = $this->url->link('extension/module/loadjobs', 'user_token=' . $this->session->data['user_token'], 'SSL'); // URL to be directed when the save button is pressed
     
        $data['cancel'] = $this->url->link('extension/module', 'user_token=' . $this->session->data['user_token'], 'SSL'); // URL to be redirected when cancel button is pressed
              
        // limit
        if (isset($this->request->post['loadjobs_limit_field'])) {
            $data['loadjobs_limit_field'] = $this->request->post['loadjobs_limit_field'];
        } else {
            $data['loadjobs_limit_field'] = $this->config->get('loadjobs_limit_field');
        } 
        
        // reference field/code
        if (isset($this->request->post['loadjobs_text_field'])) {
            foreach ($this->request->post['loadjobs_text_field'] as $key => $loadjobs_text_fieldx) {
                $data['loadjobs_text_field'][] = $loadjobs_text_fieldx;
            }
        } else {
                $data['loadjobs_text_field'][] = $this->config->get('loadjobs_text_field');
        }
        $data['totaljobs'] = count($data['loadjobs_text_field']);

        
        // status (enabled / disabled)
        if (isset($this->request->post['loadjobs_status_field'])) {
            foreach ($this->request->post['loadjobs_status_field'] as $key => $loadjobs_status_fieldx) {
                $data['loadjobs_status_field'][] = $loadjobs_status_fieldx;
            }
        } else {
                $data['loadjobs_status_field'][] = $this->config->get('loadjobs_status_field');
        }
        
        // business
        if (isset($this->request->post['loadjobs_business_field'])) {
            foreach ($this->request->post['loadjobs_business_field'] as $key => $loadjobs_business_fieldx) {
                $data['loadjobs_business_field'][] = $loadjobs_business_fieldx;
            }
        } else {
                $data['loadjobs_business_field'][] = $this->config->get('loadjobs_business_field');
        }
        
        // position
        if (isset($this->request->post['loadjobs_position_field'])) {
            foreach ($this->request->post['loadjobs_position_field'] as $key => $loadjobs_position_fieldx) {
                $data['loadjobs_position_field'][] = $loadjobs_position_fieldx;
            }
        } else {
                $data['loadjobs_position_field'][] = $this->config->get('loadjobs_position_field');
        }
        
        // description
        if (isset($this->request->post['loadjobs_description_field'])) {
            foreach ($this->request->post['loadjobs_description_field'] as $key => $loadjobs_description_fieldx) {
                $data['loadjobs_description_field'][] = $loadjobs_description_fieldx;
            }
        } else {
                $data['loadjobs_description_field'][] = $this->config->get('loadjobs_description_field');
        }
        
        // requirements
        if (isset($this->request->post['loadjobs_requirements_field'])) {
            foreach ($this->request->post['loadjobs_requirements_field'] as $key => $loadjobs_requirements_fieldx) {
                $data['loadjobs_requirements_field'][] = $loadjobs_requirements_fieldx;
            }
        } else {
                $data['loadjobs_requirements_field'][] = $this->config->get('loadjobs_requirements_field');
        }
        
        // deadline
        if (isset($this->request->post['loadjobs_deadline_field'])) {
            foreach ($this->request->post['loadjobs_deadline_field'] as $key => $loadjobs_deadline_fieldx) {
                $data['loadjobs_deadline_field'][] = $loadjobs_deadline_fieldx;
            }
        } else {
                $data['loadjobs_deadline_field'][] = $this->config->get('loadjobs_deadline_field');
        }


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/googlemap', $data));

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
        if(!$jobsT) {
            $query = "CREATE TABLE ".DB_PREFIX."jobs (
                      job_id int(11) AUTO_INCREMENT,
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
        if(!$appsT) {
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
        /* End Block*/
        // Block returns true if no error is found, else false if any error detected
        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}
