<?php
/*
    Friendly Captcha v1.01
    MathCaptcha Class
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


class MathCaptcha extends FriendlyCaptcha
{

	//
	// CUSTOMIZABLE SETTINGS
	//
	
	//Default mathematical operator
	//Valid input is 'add', 'subtract', 'multiply', 'divide', or 'random'
	protected $operator = "random";
	
	//End customizable settings
	//====================================
	
	
	//Define attributes
	protected $operator_list = array("add", "subtract", "multiply", "divide");
	protected $problem_numbers = array();
	protected $operator_alternatives = array
											(
												"add" => array("+", "plus", "en plus de"),
												"subtract" => array("-", "moins"),
												"multiply" => array("*", "fois"),
												"divide" => array("/")
												/*"add" => array("+"),
												"subtract" => array("-"),
												"multiply" => array("*"),
												"divide" => array("/")*/
											);
	//protected $question_prefix = array("Quel est la solution de", "Que donne");
	protected $question_prefix = array('');
	
	public function __construct	($operator = NULL)
	{
	
		parent::__construct();
		
		//Check operator value for validity
		if(in_array($operator, $this->operator_list) || $operator == "random")
		{
			$this->operator = $operator;
		}
		elseif($operator != NULL)
			$this->reportError("Operator invalid", __LINE__);
		
		//If operator is set to be random, pick one
		if($this->operator == "random")
			$this->operator = $this->operator_list[array_rand($this->operator_list)];
	}
	
	//Generates the numbers involved in problem, and determines solution
	public function generateProblem($id='')
	{
		if($this->operator == "add")
		{
			$total_numbers = mt_rand(2,3);
			
			for($i = 1; $i<=$total_numbers; $i++)
			{
				$this->problem_numbers[] = mt_rand(1,10);
			}
			
			$this->solution = array_sum($this->problem_numbers);
			
		}
		elseif ($this->operator == "subtract")
		{
		
			$problem_num2 = mt_rand(2, 20);
			
			//First number should be larger than second to avoid negative numbers
			$problem_num1 = mt_rand($problem_num2, $problem_num2 + 10);
			
			array_push($this->problem_numbers, $problem_num1, $problem_num2);
			
			$this->solution = $problem_num1 - $problem_num2;
			
			
		}
		elseif ($this->operator == "multiply")
		{
			$problem_num1 = mt_rand(1, 10);
			$problem_num2 = mt_rand(1, 10);
			
			array_push($this->problem_numbers, $problem_num1, $problem_num2);
			
			$this->solution = $problem_num1 * $problem_num2;
		}
		elseif ($this->operator == "divide")
		{
			$problem_num2 = mt_rand(0, 10);			
			if($problem_num2 != 0)
			{
				$problem_num1 = $problem_num2 * mt_rand(2,3);
				$this->solution = $problem_num1 / $problem_num2;
			}
			else
			{
				$problem_num1 = 0;
				$problem_num2 = mt_rand(1,10);
				$this->solution = 0;
			}
			
			$this->problem_numbers[] = $problem_num1;
			$this->problem_numbers[] = $problem_num2;
			
			
		}
		if($id=='')
		{
			//Add correct solution to session
			$_SESSION['solution'] = md5($this->solution);
		}
		else
			$_SESSION["solution_$id"] = md5($this->solution);
			
		//Add some token security to session
		$_SESSION['fingerprint'] = md5( $_SERVER['HTTP_USER_AGENT'] . FriendlyCaptcha::SESSION_SALT);
		
		//Generate the properly obfuscated output string
		$this->generateOutputString();
		
	}
	
	private function generateOutputString()
	{
		//Obfuscate question numbers
		$output_problem_numbers = $this->problem_numbers;
		foreach($output_problem_numbers as &$obfuscated_num)
		{
			$obfuscated_num = $this->obfuscateString($obfuscated_num, 'int');
		}
		
		//Obfuscate operator
		$output_operator = $this->obfuscateOperator();
		
		//Generate & assemble obfuscated output string
		$this->output_string = $this->obfuscateString($this->question_prefix[array_rand($this->question_prefix)]);
		$this->output_string .= $this->obfuscateChar(' ');
		
		
		$last_index = count($this->problem_numbers) - 1; 
		foreach($output_problem_numbers as $problem_key => $problem_number)
		{
			if($problem_key < $last_index)
			{
				$this->output_string .= $problem_number . $this->hiddenText() . $this->obfuscateChar(' ') . $this->hiddenText();
				$this->output_string .= $this->obfuscateOperator() . $this->hiddenText(). $this->obfuscateChar(' ') . $this->hiddenText();
			}
		}
		
		$this->output_string .= $output_problem_numbers[$last_index] . $this->hiddenText() . $this->obfuscateString(" =");
	}
	

	
	protected function hiddenText($optional = TRUE)
	{
		if($optional == TRUE && mt_rand(0,1) == 1)
			return "";
		
		//CSS class must start with a letter
		$random_letter = chr(rand(ord("a"), ord("z"))); 
			
		$obfuscation_key = $random_letter . substr(md5(mt_rand(1,1000)), rand(-5,-10));
		
		//Prevent collisions between visible and hidden IDs
		if(!$this->obfuscation_keys_show || !in_array($obfuscation_key, $this->obfuscation_keys_show))
		{
			$this->obfuscation_keys_hide[] = $obfuscation_key;
			
			$hidden_string = '<span id="' . $obfuscation_key . '">';
			
			$hidden_string .= $this->obfuscateString(mt_rand(1,100));
			
			$hidden_string .= '</span>';
			
			return $hidden_string;
		}
		else
			return false;
	}
	
	//Prints the math problem to the screen
	public function printProblem($echo=TRUE)
	{
		if($echo)
			echo $this->output_string;
		else
			return $this->output_string;
	}
	
	public function hiddenCss($in_style_sheet = FALSE, $echo = TRUE)
	{
		if(!$in_style_sheet)
			$stylestring = "<style type=\"text/css\">\r\n";
		
		if(!$this->obfuscation_keys_hide && !$this->output_string)
		{
			$this->reportError("CSS could not be created - check if problem has been generated", __LINE__);
			return false;
		}
		elseif(!$this->obfuscation_keys_hide)
			return false;

		
		$last_index = count($this->obfuscation_keys_hide) - 1; 
		foreach($this->obfuscation_keys_hide as $obfuscation_class_key => $obfuscated_class)
		{
			if($obfuscation_class_key < $last_index)
				$stylestring .= '#' . $obfuscated_class . ', ';
			else
				$stylestring .= '#' . $obfuscated_class;
		}
		
		$stylestring .= "{\r\n";
		$stylestring .= "visibility:hidden;\r\n";
		$stylestring .= "display:none;\r\n";
		$stylestring .= "speak:none;\r\n";
		$stylestring .= "}\r\n";
		
		if(!$in_style_sheet)
			$stylestring .= "</style>\r\n";
		
		if($echo == TRUE)
		{
			echo $stylestring;
			return true;
		}
		else
			return $stylestring;
	}
	
	public function hiddenJavascript($in_script_file = FALSE, $echo = TRUE)
	{
		if($in_script_file == FALSE)
			$jsstring = "<script type=\"text/javascript\">\r\n";
		foreach($this->obfuscation_keys_hide as $obfuscate_id)
		{
			$jsstring .= "document.getElementById('".$obfuscate_id."').innerHTML = '';\r\n";
		}
			/*
			$jsstring .= "var instance, parent;\r\n";
			
		foreach($this->obfuscation_keys_hide as $obfuscate_id)
		{
			$jsstring .= "instance = document.getElementById(\"" . $obfuscate_id . "\");\r\n";
			$jsstring .= "parent = instance.parentNode;\r\n";
			$jsstring .= "parent.removeChild(instance);\r\n";
		}
		*/

		if($in_script_file == FALSE)
			$jsstring .= "</script>\r\n";

		if($echo == TRUE)
		{
			echo $jsstring;
			return true;
		}
		else
			return $jsstring;
	}
	
}