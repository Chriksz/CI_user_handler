<?php



class users_model extends CI_Model{ 
	public function __construct()
	{
		$this->load->database();
	}


	public function insert_user($username, $usermail, $birth)
	{
		$randomstring = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
		$data = array(
			'u_nickname' => $username,
			'u_password' => sha1($this->input->post('password').$randomstring),
			'u_email'=> $usermail,
			'u_reg_date' => date("Y-m-d"),
			'u_birth' =>$birth,
			'u_salt' => $randomstring
		);

		return $this->db->insert('user', $data);
	}

	public function gen_verification($id)
	{
		$title = sha1(date("Y:m:d s").$id);
		$data = array(
			'u_pwres' => $title,
		);

		$this->db->where('nickname', $id);
		$this->db->update('user', $data);
		return $title; 
	}


	public function update_table($data)
	{
		$this->db->where($data['where']);

		if (isset($data['set']))
		{
			$this->db->set($data['set']);
		}

		if (isset($data['fset']))
		{
			$this->db->set($data['fset'], null, false);
		}

		return $this->db->update($data['tablename']);

	}


	public function arg_check($tablename, $data)
	{
		if (isset($data['where']))
		{
		$this->db->where($data['where']);
		}
		if (isset($data['fwhere']))
		{
			$this->db->where($data['fwhere'], null, false);
	
		}	
		return  $this->db->get($tablename);	
	}
}
?>