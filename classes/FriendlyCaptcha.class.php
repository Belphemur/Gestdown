<?php
/*
    Friendly Captcha v1.01
    FriendlyCaptcha Class
    (c) 2009 Chris Hepner

    This file is part of Friendly CAPTCHA.

    Friendly CAPTCHA is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Friendly CAPTCHA is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Friendly CAPTCHA.  If not, see <http://www.gnu.org/licenses/>.


*/

abstract class FriendlyCaptcha
{

	//
	// CUSTOMIZABLE SETTINGS
	// Applies to all child CAPTCHAs
	//
	
	// Format output for correct document type, if applicable
	// Valid input is "html" or "xhtml"
	const DEFAULT_OUTPUT_TYPE = "xhtml";
	
	//Should debug errors be printed to screen?
	// TRUE or FALSE
	const DEBUG_ERRORS = TRUE;
	
	// Arbitrary value for some token session security - change this to any valid constant
	//
	const SESSION_SALT = "Ame-no-Tsuki";
	
	//End customizable settings
	//====================================
	
	public $obfuscation_keys_hide = array();
	public $obfuscation_keys_show = array();
	public $solution;
	protected $output_string = NULL;
	
	function __construct()
	{
		//Start a session if there isn't one already
		if(!session_id())
			session_start();
	}	
	
	//Prints class errors to screen if debug_errors is enabled
	protected function reportError($message, $line)
	{
		if(FriendlyCaptcha::DEBUG_ERRORS == TRUE)
		{
			echo "FriendlyCaptcha Error: '" . $message . "' at line " . $line . " of class file";
		}
	}
	
	//Obfuscates an entire string, one character at a time
	public function obfuscateString($string, $type = "char")
	{
		//50% chance for numbers to be converted to spelled out words

		if($type == "int" && mt_rand(0,5) == 1)
		{
			$string = $this->convertIntToWord($string);
			$type = "char";
		}
		
		$char_array = preg_split('//', $string, -1, PREG_SPLIT_NO_EMPTY);
		
		foreach($char_array as &$single_char)
		{
			if($type == "char")
				$single_char = $this->obfuscateChar($single_char);
			elseif($type == "int")
				$single_char = $this->obfuscateInteger($single_char);
			else
				$this->reportError("obfuscateString type invalid", __LINE__);
		}
		$obfuscated_string = implode("", $char_array);
		
		return $obfuscated_string;
	}
	
	//Obfuscates a single character
	protected function obfuscateChar($char)
	{
		//Allow for chance of &nbsp encoding for spaces
		if(preg_match('/^\s$/', $char))
			$boundary = 3;
		else
			$boundary = 2;

			if(mt_rand(0,1) == 1)
				$optional_zero = "0";
			else
				$optional_zero = "";
		
		switch (mt_rand(0,$boundary))
		{
		
			//decimal entity
				case 0:
				$obfuscated_char = ord($char);
				$obfuscated_char = "&#" . $optional_zero . $obfuscated_char . ";";
				break;
			//hex entity
			case 1:
				$obfuscated_char = dechex(ord($char));
				$obfuscated_char = "&#x" . $optional_zero . $obfuscated_char . ";";
				break;
			//no obfuscation
			case 2:
				$obfuscated_char = $char;
				break;
			//If space, allow for html entity
			case 3:
				$obfuscated_char = "&nbsp;";
				break;
		}
		return $obfuscated_char;
	}
	
	
	protected function obfuscateInteger($integer)
	{
		
		if(mt_rand(0,1) == 1)
			$optional_zero = "0";
		else
			$optional_zero = "";
		
		//Pick a random method of obfuscation
		switch(mt_rand(0,2))
		{
			//decimal entity
			case 0:
				$obfuscated_num = $integer + 48;
				$obfuscated_num = "&#" . $optional_zero . $obfuscated_num . ";";
				break;
			//hex entity
			case 1:
				$obfuscated_num = dechex($integer + 48);
				$obfuscated_num = "&#x" . $optional_zero . $obfuscated_num . ";";
				break;
			//No obfuscation
			case 2:
				$obfuscated_num = $integer;
				break;
		}
		
		//Add some meaningless html tags on occasion (1/5th of the time) for good measure
		switch(mt_rand(0,4))
		{
			case 1:
				//CSS class must start with a letter
				$random_letter = chr(rand(ord("a"), ord("z"))); 
				
				$obfuscation_key = $random_letter . substr(md5(mt_rand(1,1000)), rand(-5,-10));
				$this->obfuscation_keys_show[] = $obfuscation_key;
				$obfuscated_num = '<span id="' . $obfuscation_key . '">' . $obfuscated_num . '</span>';
				break;
			default:
				break;
		}
		
		return $obfuscated_num;
	}
	
	protected function obfuscateOperator()
	{
		//Pick a random operator from array
		$obfuscated_operator = $this->operator_alternatives[$this->operator][array_rand($this->operator_alternatives[$this->operator])];
		
		//For 'normal' symbolic operator:
		if(strlen($obfuscated_operator) == 1)
		{
			$obfuscated_operator = $this->obfuscateChar($obfuscated_operator);
		}
		//For spelled-out operator:
		else
		{
			$obfuscated_operator = $this->obfuscateString($obfuscated_operator, 'char');
		}
		
		return $obfuscated_operator;
	}
	
	// Converts integer to spelled-out word
	// Currently only works for numbers 0 -99
	protected function convertIntToWord($integer)
	{
		$integer = (int) $integer;
			
		$digits_single = array ( "zero", "un", "deux", "trois", "quatre", "cinq", "six", "sept", "huit", "neuf");
		$digits_tens = array (10 => "dix", 11 => "onze", 12 => "douze", 13 => "treize", 14 => "quatorze", 15 => "quinze", 16 => "seize", 17 => "dix-sept", 18 => "dix-huit", 19 => "dix-neuf");
		$digits_double = array (2 => "vingt", 3 => "trente", 4 => "quarante", 5 => "cinquante", 6 => "soixante", 7 => "soixante dix", 8 => "quatre-vingt", 9 =>" quatre vingt dix ");
		
		if($integer >= 0 && $integer <= 9)
			return $digits_single[$integer];
		elseif($integer >= 10 && $integer <= 19)
			return $digits_tens[$integer];
		elseif($integer >= 20 && $integer <= 99)
		{
			if($integer % 10 == 0)
				return $digits_double[$integer/10];
			else
				return $digits_double[floor($integer/10)] . '-' . $digits_single[$integer % 10];
		}
		else
		{
			// Number out of range
			$this->reportError("Number $integer out of range for word conversion", __LINE__);
			return false;
		}
	}
	
	//Validates supplied answer
	public function checkAnswer($input_answer,$id='')
	{
		if($id=='')
			if(isset($_SESSION['solution']))
				$sol=$_SESSION['solution'];
			else
				return false;
		else
			if(isset($_SESSION["solution_$id"]))
				$sol=$_SESSION["solution_$id"];
			else
				return false;
			
		if($sol == md5($input_answer) && $_SESSION['fingerprint'] == md5( $_SERVER['HTTP_USER_AGENT'] . FriendlyCaptcha::SESSION_SALT))
			return true;
		else
		{
			
			return false;
		}
	}
	
}
