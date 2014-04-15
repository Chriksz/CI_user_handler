<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of view_controller
 *
 * @author Chriss
 */
class View_maker {
    //put your code here
    	//HTML header title, styles etc
	private $headerarray;
	//additional view files, data for rendering
	//use like the standard view() function, the first element of each given array must be the path string
	private $plusview = array();
	//posttype for error displaying
	private $_postmode;
        // CI super object
        protected $CI;
        // default value: all time scripts
        private $_scripts;
        
        public function __construct()
	{
                $this->CI =& get_instance();
                $this->_scripts = $this->CI->config->item('standard_scripts');
	}
        
        public function set_postmode($setting)
        {
            define('POSTMODE', $setting);
            $this->_postmode = $setting;
        }
        public function set_headerarray($header)
        {
            $this->headerarray = $header;
        }
        public function set_plusview($plusview)
        {

               $this->plusview[] = $plusview;

        }
        public function set_script($script)
        {
            if (is_array($script))
            {
                foreach ($script as $scriptpiece)
                {
                    $this->_scripts[] = $scriptpiece;
                }
            }
            else
            {
                 $this->_scripts[] = $script;
            }
        }
    	public function view_maker()
	{
		$this->CI->load->view('templates/header', $this->headerarray);
		if(!empty($this->plusview))
		{
                        foreach ($this->plusview as &$view)
                        {
                            if (!isset($view[1]))
                            {
				$view[1] = array();
                            }
                            $this->CI->load->view($view[0], $view[1]);
                        }
		}
                
		$footer['scripts'] = $this->_scripts;
                $footer['postmode'] = $this->_postmode;
		$this->CI->load->view('templates/footer', $footer);	
	}
}