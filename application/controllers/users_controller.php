<?php
/**
 * I didn't implement proper CSRF protection. I highly recommend to turn on the CI CSRF protection!
 */
class Users_controller extends CI_Controller 
{
    /**
     * Fetched data about the user.
     * @var array 
     */
    private $loginfo;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Users_model');
        $this->load->helper(array('form', 'url', 'captcha'));
        $this->load->library(array('session', 'form_validation'));
    }



    public function login()
    {
        // If already logged in, redirect.
        if ($this->session->userdata('user_id'))
        {
                $url = prep_url(site_url());
                redirect($url, 'refresh');
        }
        // Login tries counting.
        $try = intval($this->session->userdata('try'));
        $validator = ($try>3) ? 'loginwithcap' : 'login';

        if ($this->form_validation->run($validator) == FALSE)
        {
            // Captcha generating
            $cap = $try>3 ? $this->_captcha_maker() : FALSE;
            $try++;
            $this->session->set_userdata('try', $try );
            $this->view_maker->title = 'bejelentkezes';
            $this->view_maker->set_style('logins');
            $this->view_maker->set_plusview(array('users/login', $cap));
            $this->view_maker->render_view();

        }
        else
        {
            // Save important user's data into cookie
            $sessdata = array( 
                'user_id' =>  $this->loginfo[0]['u_user_id'],
                'username' => $this->loginfo[0]['u_nickname']
            );
            $this->session->set_userdata($sessdata);
            $this->session->unset_userdata('try');
            $this->view_maker->set_plusview(array('templates/redirect'));
            $this->view_maker->render_view();
        }
    }
    


    public function get_forgotten_pw()
    {
        if ($this->form_validation->run('fg_pw') == FALSE)
        {
            $cap = $this->_captcha_maker();

            $this->view_maker->title = 'elfelejtett jelszó';
            $this->view_maker->set_plusview(array('users/forgpw', $cap));
            $this->view_maker->render_view();

        }
        else 
        {
            //Send verification email, to confirm the password resetting
            $username = $this->input->post('username');
            $email = $this->input->post('email');
            $key = $this->Users_model->gen_verification($username);
            $link = $this->load->view('forgpwmail', array('key' => $key, 'username' => $username),FALSE);	
            $this->_mailer($link, $email, "nevalaszolj");
            $this->view_maker->title = 'regisztráció';
            $this->view_maker->set_plusview(array('users/success'));
            $this->view_maker->render_view();


        }
    }
    /**
     * 
     * @param string $key generated temporary key to identify
     */
    public function password_reset($key)
    {
        $result = $this->db->get_where('user', array('u_pwres' => $key));
        // If $key doesn't exist, redirect.
        if (!$result->num_rows())
        {
            $url = prep_url(site_url('bejelentkezes'));	
            redirect($url, 'refresh');
        }
        //new password input
        if ($this->form_validation->run('password_reset') == FALSE)
        {

            $this->view_maker->set_plusview(array('users/passres',$key));
            $this->view_maker->render_view();
        }
        else
        {
            $result = $result->result_array();
            $this->Users_model->update_user_pw( $result[0]['u_nickname'], $this->input->post('password'));
            $url = prep_url(site_url('bejelentkezes'));	
            redirect($url, 'refresh');
        }

    }	

    public function registration()
    {

        if ($this->form_validation->run('registration') == FALSE)
        {
            $cap = $this->_captcha_maker();
            $this->view_maker->title = 'regisztráció';
            $this->view_maker->set_plusview(array('users/regist', $cap));
            $this->view_maker->render_view();

        }
        else
        {
            // Insert the new user's data into the db
            $username = $this->input->post('username');
            $usermail = $this->input->post('email');
            $this->Users_model->insert_user($username, $usermail, $this-> validbirth);	

            //Send verification email, to make sure the given email exists 
            $key = $this->Users_model->gen_verification($username);
            $mail = $this->load->view('regmail', array('key' => $key, 'username' => $username),FALSE);
            $this->_mailer($mail, $usermail, "nevalaszolj");
            $this->view_maker->set_plusview(array('users/success'));
            $this->view_maker->render_view();
        }
    }
    public function logout()
    {
        $this->session->sess_destroy();
        $url = prep_url(site_url());
        redirect($url, 'refresh');
    }
    /**
     * 
     * @param string $name
     * @param string $key
     */
    public function verify($name, $key)
    {
        $result = $this->Users_model->get_valid_user($name, $key, true);
        // If $key doesn't exist, redirect.
        if (!$result->num_rows())
        {
            $url = prep_url(site_url());	
            redirect($url, 'refresh');
        }
        $this->Users_model->confirm_user($name);
        $result = $result->result_array();
        // Save important user's data into cookie, like a successful login
        $this->session->set_userdata(array(
        'username' => $name,
        'user_id' => $result[0]['u_user_id']
        ));
        $this->load->view('templates/redirect');
    }
    /**
     * 
     * @return Captcha
     */
    private function _captcha_maker()
    {
        $url = prep_url(base_url('captcha'));
        $cap = create_captcha(array(
                'img_path' => './captcha/',
                'img_url' => $url.'/',
                'img_width' => '150',
                'img_height' => 30,
        ));
        $this->session->set_userdata('captword', $cap['word']);
        return $cap;

    }
    /**
     * 
     * @param string $content
     * @param string $recipient
     * @param string $subject
     */
    private function _mailer($content, $recipient, $subject)
    {
        $this->load->library('email');
        $config['mailtype'] = 'html';
        $config['charset'] = 'UTF-8';
        $this->email->initialize($config);
        $this->email->from('your@mail.com', 'yourname');
        $this->email->to($recipient);
        $this->email->subject($subject);
        $this->email->message($content);
        $this->email->send(); 
    }

    /**
     * If the given email or nickname is already in the db, return false
     * @param string $data
     * @param string $type
     * @return boolean
     */
    public function unique_check($data, $type)
    {
        if ($type == 'email')
        {
            $array['u_email'] = $data;
        }
        else
        {
            $array['u_nickname'] = $data;
        }

        if (!$this->db->get_where('user', $array)->num_rows())
        { 
            return TRUE;
        }
        $this->form_validation->set_message('unique_check', 'A(z) %s már foglalt!');
        return FALSE;
    }
    /**
     * Compare the captcha to the input string, upper/lower case does not matter
     * @param string $str
     * @return boolean
     */
    public function capt_validate($str)
    {
        $valchap = $this->session->userdata('captword');
        if (strtolower ($valchap) == strtolower ($str))
        {
            return TRUE;
        }
        $this->form_validation->set_message('captvalidate', 'Helytelen a beírt karaktersor!');
        return FALSE;	
    }

    /**
     * 
     * 
     * @param string $userinput
     * @return boolean
     */
    public function login_data_check($userinput)
    {
        $user = $this->Users_model->get_valid_user($this->input->post('username'), $this->input->post('password'));
        if (!$user->num_rows())
        {
            $this->form_validation->set_message('login_data_check', "Helytelen adatok!");
            return FALSE;	
        }

        $this->loginfo = $user->result_array();
        return TRUE;	
    }

}
