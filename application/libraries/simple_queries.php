<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of simple_queries
 *
 * @author Chriss
 */
class simple_queries {
    //put your code here
        protected $CI;
        public function __construct()
	{
                $this->CI =& get_instance();
	}
    
    	public function update_table($data)
	{
		$this->CI->db->where($data['where']);

		if (isset($data['set']))
		{
			$this->CI->db->set($data['set']);
		}

		if (isset($data['fset']))
		{
			$this->CI->db->set($data['fset'], null, false);
		}

		return $this->CI->db->update($data['tablename']);

	}


	public function arg_check($tablename, $data = false)
	{
		if (isset($data['select']))
		{
                    $this->CI->db->select($data['select']);
		}
                if (isset($data['fselect']))
		{
                    $this->CI->db->select($data['fselect'], false);
		}
                if (isset($data['join']))
		{
			$this->CI->db->join($data['join'][0], $data['join'][1]);
		}
		if (isset($data['where']))
		{
			$this->CI->db->where($data['where']);
		}
		if (isset($data['fwhere']))
		{
			$this->CI->db->where($data['fwhere'], null, false);
	
		}
                if (isset($data['order']))
                {
                    
                    $this->CI->db->order_by($data['order'][0], $data['order'][1]);
                }
                if (isset($data['limit']))
                {
                   
                    $this->CI->db->limit($data['limit']);
                }
                
		return  $this->CI->db->get($tablename);	
	}
        
        
        public function get_enum_values( $table, $field )
        {
             $type = $this->CI->db->query( "SHOW COLUMNS FROM {$table} WHERE Field = '{$field}'" )->row( 0 )->Type;
             preg_match('/^enum\((.*)\)$/', $type, $matches);
             foreach( explode(',', $matches[1]) as $value )
            {
            $enum[] = trim( $value, "'" );
             }
            return $enum;
        }
        
        public function or_sorter($szlev, $prefix)
	{
	
		// make where or where line
                if (empty($szlev))
                {
                   $string = "{$prefix} IS NULL"; 
                }
		$first = true;
		if (is_array($szlev))
		{
			foreach ($szlev as $lev)
			{
			
				if ($first)
				{
					$string = "{$prefix} =". $this->CI->db->escape($lev);
					$first = false;
				}
				else
				{
					$string .= " OR {$prefix} =". $this->CI->db->escape($lev);
				}
			
			}
		}
		else 
		{
			$string = "{$prefix} =". $this->CI->db->escape($szlev);
		
		}
		
		return $string;
	}
     
    
    
}
