<?php

$user = array(
            'field'   => 'username',
            'label'   => 'Felhasználónév',
            'rules'   => 'required|min_length[2]|max_length[40]|alpha_numeric'
        );
$captcha = array(
                'field'   => 'captcha',
                'label'   => 'Character',
                'rules'   => 'required|alpha_numeric|callback_captvalidate'
            );
$password = array(
                'field'   => 'password',
                'label'   => 'Jelszó',
                'rules'   => 'required|min_length[5]'
                );
$pwconf =   array(
                'field'   => 'passconf',
                'label'   => 'Jelszó',
                'rules'   => 'required'
        );  		

$config = array( 
		'login' => 
		array(
                        $user,
		
			array(
				'field'   => $password['field'],
				'label'   => $password['label'],
				'rules'   => $password['rules'].'|callback_login_data_check'
			)
		),
    		'loginwithcap' => 
		array(
                        $user,
		
			array(
				'field'   => $password['field'],
				'label'   => $password['label'],
				'rules'   => $password['rules'].'|callback_login_data_check'
			),
                        $captcha
		),
		
		'fg_pw' => 
		array(
                        $user,
			
			array(
				'field'   => 'email',
				'label'   => 'Email',
				'rules'   => 'required|valid_email|callback_login_data_check'
			),
			
                        $captcha

		),
		'registration' => 
		array(
			array(
				'field'   => $user['field'],
				'label'   => $user['label'],
				'rules'   => $user['rules'].'|callback_unique_check[nickname]'
			),
                        $captcha,
			
			array(
				'field'   => $password['field'],
				'label'   => $password['label'],
				'rules'   => $password['rules'].'|matches[passconf]'
			),
			
                        $captcha,
			
			array(
				'field'   => 'email',
				'label'   => 'Email',
				'rules'   => 'required|valid_email|callback_unique_check[email]'
			),
			array(
				'field'   => 'years',
				'label'   => 'Év',
				'rules'   => 'required|numeric'
			),
			array(
				'field'   => 'months',
				'label'   => 'Hónap',
				'rules'   => 'required|numeric'
			),
			array(
				'field'   => 'days',
				'label'   => 'Nap',
				'rules'   => 'required|numeric'
			),
			array(
				'field'   => 'license',
				'label'   => 'Feltételek',
				'rules'   => 'required'
			),
		),
		'password_reset'=> array(
			array(
				'field'   => $password['field'],
				'label'   => $password['label'],
				'rules'   => $password['rules'].'|matches[passconf]'
			),
                        $pwconf
		)
);



  				  





?>