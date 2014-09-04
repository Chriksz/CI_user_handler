<?php


/**
 * Description of View_maker
 *
 * @author Chriss
 */
class View_maker {
    
    /**
     * Declare the used layout, every layout dir have to contain a footer and header file
     * @var string 
     */
    public $layout = "standard";
    /**
     * Page title
     * 
     * @var string 
     */
     public $_title = "";
     
     /**
      * styles container
      * @var string
      */
     private $_style = array();
    /**
     * additional view files, data for rendering 
     * use like the standard view() function, the first element of each given array must be the path string
     * @var string array 
     */
    private $_plusview = array();
    /**
     * posttype for error displaying
     * @var string 
     */
    private $_postmode;
    /**
     * CI super object
     * @var object 
     */
    protected $CI;
    /**
     * default value: all time scripts
     * @var array 
     */
    private $_scripts = array();

    public function __construct()
    {
            $this->CI =& get_instance();
            $this->_scripts = $this->CI->config->item('standard_scripts');
            $this->_style = $this->CI->config->item('standard_css');
    }
    public function set_style($styles)
    {
        if (is_array($styles))
        {
            $this->_style = $this->_style+$styles;
        }
        else
        {
             $this->_style[] = $styles;
        }
    }
    public function set_postmode($setting)
    {
        $this->_postmode = $setting;
    }
    public function set_plusview($plusview)
    {

           $this->_plusview[] = $plusview;

    }
    public function set_script($script)
    {
        if (is_array($script))
        {
            $this->_scripts = $this->_scripts+$script;
        }
        else
        {
             $this->_scripts[] = $script;
        }
    }
    /**
     * clear all data: title, styles, scripts, plusviews
     */
    public function clear_config()
    {
        $this->title = "";
        $this->_style = array();
        $this->_scripts = array();
        $this->_plusview = array();
    }
    public function render_view()
    {
        
        $this->CI->load->view("templates/{$this->layout}/header", array(
            "title" => $this->_title,
            "style" => $this->_style
        ));
        if(!empty($this->_plusview))
        {
            foreach ($this->_plusview as &$view)
            {
                if (!isset($view[1]))
                {
                    $view[1] = array();
                }
                $this->CI->load->view($view[0], $view[1]);
            }
        }

        $this->CI->load->view("templates/{$this->layout}/footer", array(
            'scripts' => $this->_scripts,
            'postmode' =>$this->_postmode
        ));	
    }
}
