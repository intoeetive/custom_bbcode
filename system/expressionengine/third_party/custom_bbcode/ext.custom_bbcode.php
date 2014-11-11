<?php

/*
=====================================================
 Custom BBCode
-----------------------------------------------------
 http://www.intoeetive.com/
-----------------------------------------------------
 Copyright (c) 2014 Yuri Salimovskiy
=====================================================
 This software is intended for usage with
 ExpressionEngine CMS, version 2.0 or higher
=====================================================
 File: ext.custom_bbcode.php
-----------------------------------------------------
 Purpose: Add your own BBCode/PMCode tags
=====================================================
*/

if ( ! defined('BASEPATH'))
{
	exit('Invalid file request');
}

class Custom_bbcode_ext
{
	public $settings = array();

	public $name = 'Custom BBCode';
	public $version = '1.0';
	public $description = 'Add your own BBCode/PMCode tags';
	public $settings_exist = 'y';
	public $docs_url = 'https://github.com/intoeetive/custom_bbcode';

	public function __construct($settings = '')
	{
		$this->EE =& get_instance();
		$this->settings = $settings;
		
		$this->EE->lang->loadfile('custom_bbcode');  
	}


	/* Activate Extension */
	function activate_extension()
    {
        
        $hooks = array(
    		array(
    			'hook'		=> 'typography_parse_type_start',
    			'method'	=> 'parse_custom_bbcode',
    			'priority'	=> 10
    		)
    	);
    	
        foreach ($hooks AS $hook)
    	{
    		$data = array(
        		'class'		=> __CLASS__,
        		'method'	=> $hook['method'],
        		'hook'		=> $hook['hook'],
        		'settings'	=> '',
        		'priority'	=> $hook['priority'],
        		'version'	=> $this->version,
        		'enabled'	=> 'y'
        	);
            $this->EE->db->insert('extensions', $data);
    	}	

    }
    
    /**
     * Update Extension
     */
    function update_extension($current = '')
    {
    	if ($current == '' OR $current == $this->version)
    	{
    		return FALSE;
    	}
    }
    
    
    /**
     * Disable Extension
     */
    function disable_extension()
    {
    	$this->EE->db->where('class', __CLASS__);
    	$this->EE->db->delete('extensions');
    }
	
	
	
	function settings_form($current)
    {
    	$this->EE->load->helper('form');
    	$this->EE->load->library('table');
        
        $vars = array();
					
		foreach ($current as $bb_tag=>$html)
		{
			$vars['settings']["$bb_tag"] = form_input("$bb_tag", $html);
		}
        $vars['settings']["__bbcode__"] = form_input("__html__", '');

    	return $this->EE->load->view('settings', $vars, TRUE);			
    }
    
    
    
    
    function save_settings()
    {
    	if (empty($_POST))
    	{
    		show_error($this->EE->lang->line('unauthorized_access'));
    	}

		unset($_POST['submit']);
        $settings = array();
        if ($_POST['__bbcode__']!='')
        {
            $settings[$this->EE->input->post('__bbcode__')] = $this->EE->input->post('__html__');
        }
        unset($_POST['__bbcode__']);
        unset($_POST['__html__']);
        
        foreach ($_POST as $key=>$val)
        {
            if ($val!='')
            {
                $settings[$key] = $val;
            }
        }

        $this->EE->db->where('class', __CLASS__);
    	$this->EE->db->update('extensions', array('settings' => serialize($settings)));
    	
    	$this->EE->session->set_flashdata(
    		'message_success',
    	 	$this->EE->lang->line('preferences_updated')
    	);
    }
    
    
	
	public function parse_custom_bbcode($str, $typography, $prefs)
	{
       foreach ($this->settings as $bb_tag=>$html)
       {
           $html_a = explode('|', $html);
           if (count($html_a)==2)
           {
            $str = str_ireplace(array('['.$bb_tag.']', '[/'.$bb_tag.']'),	array($html_a[0], $html_a[1]),	$str); 
           }
       }
       
       return $str;
       
    }


}