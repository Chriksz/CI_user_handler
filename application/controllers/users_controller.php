<?php
class Users_controller extends CI_Controller 
{

	// I didn't implement proper CSRF protection. I highly recommend to turn on the CI CSRF protection!
	
	
	// Fetched data about the user.	
	public $loginfo;
	// Clarified birth date.
	private $validbirth;
	//HTML header title, styles etc
	public $headerarray;
	//additional view files
	//use like the standard view() function, the first element of each given array must be the path string
	public $plusview = FALSE;
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('users_model');
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
		$try++;
		$this->session->set_userdata('try', $try );
		if ($try>3)
		{
			$this->form_validation->set_rules('captcha', 'Character', 'required|alpha_numeric|callback_captvalidate');
		}


		if ($this->form_validation->run('login') == FALSE)
		{
			// Captcha generating
			if ($try>3)
			{
				$cap = $this->_captcha_maker();
			}
			else
			{
			$cap = FALSE;
			}
			// Header title.
			$this->headerarray['title'] = 'bejelentkezes';
			$this->plusview = array('users/login', $cap);  
			$this->view_maker();

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
			
			$this->plusview = array('templates/redirect');  
			$this->view_maker();
		}
	}


	
	public function get_forgotten_pw()
	{
		//password reset attempting

		if ($this->form_validation->run('fg_pw') == FALSE)
		{
			$cap = $this->_captcha_maker();
			$this->headerarray['title'] = 'elfelejtett jelszó';
			$this->plusview = array('users/forgpw', $cap);  
			$this->view_maker();
		}
		else 
		{
			//Send verification email, to confirm the password resetting
			$username = $this->input->post('username');
			$email = $this->input->post('email');
			$key = $this->users_model->gen_verification($username);
			$link = "<html><head></head><body><h1>Ha nem Ön kezdeményezte a jelszóváltoztatást, hagyja figyelmen kívül eme e-mailt!</h1> <p>Ha a honlappal kapcsolatban bármi problémája van, keressen fel minket: service@addonline.hu</p><p> Az alábbi linkre kattintva újragenerálhatja jelszavát:</p>";
			$url = prep_url(site_url('elfelejtettjelszo'));
			$link .= "<p><a href='$url/$key'>Link</a></p></body></html>";	
			$subject = "nevalaszolj";
			$this->_mailer($link, $email, $subject);

			$this->headerarray['title'] = 'regisztráció';
			$this->plusview = array('users/success');  
			$this->view_maker();
		}
	}

	public function password_reset($key)
	{
		$data['where']['u_pwres'] = $key;
		$result = $this->users_model->arg_check('user', $data);
		// If $key doesn't exist, redirect.
		if (!$result->num_rows())
		{
			$url = prep_url(site_url('bejelentkezes'));	
			redirect($url, 'refresh');
		}
		//new password input
		if ($this->form_validation->run('password_reset') == FALSE)
		{

			$this->plusview = array( 'users/passres',  $data['where']);
			$this->view_maker();
		}
		else
		{
			// update the user's password field
			$result = $result->result_array();
			$updata['where']['u_nickname'] = $result[0]['u_nickname']; 
			$updata['tablename'] = 'user';
			$updata['fset'] = array(
			'u_password' => 'sha1(concat('.$this->db->escape($this->input->post('password')).', u_salt))'
			);
			$this->users_model->update_table($updata);
			// Verification key regenerating.
			$this->users_model->gen_verification($updata['where']['u_nickname']);
			$url = prep_url(site_url('bejelentkezes'));	
			redirect($url, 'refresh');
		}
	
	}	

	public function registration()
	{
		
		if ($this->form_validation->run('registration') == FALSE)
		{
			$cap = $this->_captcha_maker();

			$this->headerarray['title'] = 'regisztráció';
			$this->plusview = array( 'users/regist', $cap);
			$this->view_maker();
		}
		else
		{
			// Insert the new user's data into the db
			$username = $this->input->post('username');
			$usermail = $this->input->post('email');
			$this->users_model->insert_user($username, $usermail, $this-> validbirth);	
			
			//Send verification email, to make sure the given email exists 
			$key = $this->users_model->gen_verification($username);
			$link = "<html><head></head><body><h1> Köszönjük regisztrálását!</h1> <p>Ha a honalappal kapcsolatban bármi problémája van, keressen fel minket: service@addonline.hu</p><p> Az alábbi linkre kattintva érvényesítheti regisztrációját:</p>";
			$url = prep_url(site_url('regisztracio'));
			$link .= "<p><a href='$url/$username/$key'>Link</a></p></body></html>";			
			$subject = "nevalaszolj";
			$this->_mailer($link, $usermail, $subject);
			
			$this->plusview = array( 'users/success');
			$this->view_maker();
		}
	}
	public function logout()
	{
		// unset cookie data
		$this->session->sess_destroy();
		$url = prep_url(site_url());
		redirect($url, 'refresh');
	}
	
	public function verify($name, $key)
	{
		$data['where']['u_nickname'] = $name;
		$data['where']['u_pwres'] = $key;
		$result = $this->users_model->arg_check('user', $data);
		
		// If $key doesn't exist, redirect.
		if (!$result->num_rows())
		{
			$this->load->view('templates/redirect');
			return;
		}
		$updata['where']['u_nickname'] = $data['where']['u_nickname'];
		
		// Save the successful email confirmation into the db.
		$updata['set']['u_valid'] = 'Y';
		$updata['tablename'] = 'user';
		$this->users_model->update_table($updata);
		$this->users_model->gen_verification($updata['where']['u_nickname']);
		$result = $result->result_array();
		
		// Save important user's data into cookie, like a successful login
		$sessdata = array(
		'username' => $updata['where']['u_nickname'],
		'user_id' => $result[0]['u_user_id']
		);
		$this->session->set_userdata($sessdata);
		$this->load->view('templates/redirect');
	}

	private function _captcha_maker()
	{
		$url = prep_url(base_url('captcha'));
		$vals = array(
			'img_path' => './captcha/',
			'img_url' => $url.'/',
			'img_width' => '150',
			'img_height' => 30,
		);
		$cap = create_captcha($vals);
		$this->session->set_userdata('captword', $cap['word']);
		return $cap;
	
	}
	
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
	
	private function view_maker()
	{
		$this->load->view('templates/header', $this->headerarray);
		if($this->plusview != FALSE)
		{
			if (is_array($this->plusview[0]))
			{
				foreach ($this->plusview as $view)
				{   
					if (!isset($view[1]))
					{
						$view[1] = array();
					}
					$this->load->view($view[0], $view[1]);
				}
			}
			else 
			{
				if (!isset($this->plusview[1]))
				{
					$this->plusview[1] = array();	
				}
				$this->load->view($this->plusview[0], $this->plusview[1]);
			}
		}
		$this->load->view('templates/footer');	
	}
	

	public function birth_check()
	{
		$days = $this->input->post('days');
		$months = $this->input->post('months');
		$years= $this->input->post('years');
		$birth = "$years-$months-$days";
		
		// user must be minimum 14 years old
		$mindate = date('Y')-14;
		$mindate .= '-'.date('n');
		$mindate .= '-'.date('j');
		
		if ($birth >= $mindate)
		{
			$this->form_validation->set_message('birth_check', "Ön túl fiatal $birth!");
			return false;
		}
		$this-> validbirth = $birth;
		return TRUE;
	}
	
	public function unique_check($data, $type)
	{
		// If the given email and nickname is already in the db, return false
		if ($type == 'email')
		{
			$array['where']['u_email'] = $data;
		}
		else
		{
			$array['where']['u_nickname'] = $data;
		}
		$result = $this->users_model->arg_check('user', $array);
		
		if (!$result->num_rows())
		{ 
			return TRUE;
		}
		$this->form_validation->set_message('unique_check', 'A(z) %s már foglalt!');
			return FALSE;
	}

	public function capt_validate($str)
	{
		$valchap = $this->session->userdata('captword');
		// Compare the captcha to the input string, upper/lower case does not matter
		if (strtolower ($valchap) == strtolower ($str))
		{
			return TRUE;
		}
		$this->form_validation->set_message('captvalidate', 'Helytelen a beírt karaktersor!');
		return FALSE;	
	}


	public function login_data_check($str)
	{
		// check the given password and nickname
		$data['where']['u_nickname'] = $this->input->post('username');
		$email = $this->input->post('email');
		if ( $email != FALSE)
		{
			$data['where']['u_email'] = $str;
		}
		else
		{
			$data['fwhere']['u_password'] = 'sha1(concat('.$this->db->escape($str).', u_salt))';
		}
		$result = $this->users_model->arg_check('user', $data);
		
		if (!$result->num_rows())
		{
			$this->form_validation->set_message('isvalid', "Helytelen adatok!");
			return FALSE;	
		}

		$this->loginfo = $result->result_array();
		return TRUE;	
	}

}


?>