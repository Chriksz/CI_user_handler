<?php



class Users_model extends CI_Model
{ 
	public function __construct()
	{
            $this->load->database();
	}

        /**
         * 
         * @param string $username
         * @param string $usermail
         * @param string $birth
         * @return Object
         */
	public function insert_user($username, $usermail, $birth)
	{
            //generate salt
            $randomstring = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
            $data = array(
                    'u_nickname' => $username,
                    'u_password' => sha1($this->input->post('password').$randomstring),
                    'u_email'=> $usermail,
                    'u_regdate' => date("Y-m-d"),
                    'u_birth' =>$birth,
                    'u_salt' => $randomstring
            );

            return $this->db->insert('user', $data);
	}
        /**
         * 
         * @param string $name
         * @return string
         */
	public function gen_verification($name)
	{
            $title = sha1(date("Y:m:d s").$name);
            $data = array(
                    'u_pwres' => $title,
            );

            $this->db->where('nickname', $name);
            $this->db->update('user', $data);
            return $title; 
	}
        /**
         * 
         * @param string $username
         * @param string $userdata
         * @param bool $iskey
         * @return Object
         */
        public function get_valid_user($username, $userdata, $iskey = false)
        {
            $this->db->where('u_nickname', $username);
            if ($iskey)
            {
                $this->db->where('u_pwres', $userdata);
            }
            else if ( $this->form_validation->valid_email($userdata))
            {
                    $this->db->where('u_email', $userdata);
            }
            else
            {
                    $this->db->where('u_password', $this->_get_pw_hash($userdata), false);
            }
            return $this->db->get('user');
        }
        /**
         * 
         * @param string $username
         * @param string $password
         */
        public function update_user_pw($username, $password)
        {
            $this->db->where('u_nickname', $username); 
            $this->db->set('u_password', $this->_get_pw_hash($password), false);
            $this->db->update('user');
            $this->users_model->gen_verification($username);
        }
        /**
         * 
         * @param string $password
         * @return string
         */
        private function _get_pw_hash($password)
        {
            return 'sha1(concat('.$this->db->escape($password).', u_salt))';
        }
        /**
         * 
         * @param string $username
         */
        public function confirm_user($username)
        {
            $this->db->where('u_nickname', $username);
           // Save the successful email confirmation into the db. 
           $this->db->set('u_valid', 'Y');
           $this->db->update('user');
           $this->users_model->gen_verification($username);
            
        }
}