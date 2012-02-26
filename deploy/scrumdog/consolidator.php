<?php
require dirname(__FILE__).'/lib/vendor/JSMin/jsmin-1.1.1.php';
require dirname(__FILE__).'/lib/vendor/Minify/CSS.php';

/** THE CONSOLIDATOR 
*
* This script consolidates JS and CSS files in a symfony project
*
* @author Damien Filiatrault
* @date 7/30/2009
**/	

$consolidator = new Consolidator();
//$consolidator->layout_files = array('apps/frontend/templates/layout.php');
$consolidator->run();

class Consolidator
{
	/** Begin Public Configuration Variables (You may adjust these to the needs of your project) **/
	public $layout_files = array('apps/frontend/templates/layout.php');
	
	public $consilidate_css = true;
	public $consilidate_js = true;
	
	public $delete_original_files = false;
	
	public $css_function = 'stylesheet_tag';
	public $js_function = 'javascript_include_tag';
	
	public $web_directory = 'web';
	public $css_directory = 'css';
	public $js_directory = 'js';

	public $add_new_lines_between_files = false;

	public $minify_js = true;
	public $minify_css = true;
	
	/** Private Variables **/
	
	private $js_files = array();
	private $css_files = array();
	private $css_print_files = array();
	
	/** Public Functions **/
	public function run()
	{
		//Find all of the files in the static content directories and version them
		foreach($this->layout_files as $layout_file)
		{
			$real_file = dirname(__FILE__).'/'.$layout_file;
			$this->consolidate_js_files($real_file);
			$this->consolidate_css_files($real_file);
		}

		echo(">> Consolidated ".count($this->js_files)." javascript files\n");
		echo(">> Consolidated ".count($this->css_files)." normal css files\n");
		echo(">> Consolidated ".count($this->css_print_files)." print css files\n");
	}
	
	/** Private Functions **/
	
	private function consolidate_js_files($layout_file)
	{
		$first_js_line = NULL;
		$pattern = '/'.$this->js_function.'\(.*\)/';
		if($line_array = file($layout_file, FILE_IGNORE_NEW_LINES))
		{	
			$new_line_array = array();
			$line_number = 0;
			foreach($line_array as $line)
			{
				$line_number++;
				//echo($line."\n");
				$matches = array();
				if(preg_match($pattern, $line , $matches))
				{
					if(is_null($first_js_line))
						$first_js_line = $line_number;
						
					//gather the js file name
					foreach($matches as $match)
					{
						$match = str_replace($this->js_function, '', $match);
						$match = str_replace("(", '', $match);
						$match = str_replace(")", '', $match);
						$match = str_replace("'", '', $match);
						$match = str_replace('"', '', $match);
						$match = trim($match);
						$this->js_files[] = $match;
						echo($match."\n");
					}					
				}
				else
				{
					$new_line_array[] = $line;
				}
			}
			
			$line_number = 0;
			$new_file_string = '';
			foreach($new_line_array as $line)
			{
				$line_number++;
				if($line_number==$first_js_line)
				{
					$consilated_js_line = $this->get_consolidated_js_line();
					if(count($this->js_files)>0)
					{	
						$new_file_string .= "\t".$consilated_js_line."\n";
					}
				}
				$new_file_string .= $line."\n";
			}
			
			$file_handle = fopen($layout_file, 'w');
			fwrite($file_handle, $new_file_string);
			fclose($file_handle);	
		}
		else
		{
			echo($layout_file." is not valid!\n");
		}
		
	}
	
	private function get_consolidated_js_line()
	{
		$file_name_string = '';
		$file_contents_string = '';
		$path_prefix = dirname(__FILE__).'/'.$this->web_directory;
		$dest_prefix = $path_prefix.'/'.$this->js_directory.'/';
		foreach($this->js_files as $js_file)
		{
			$file_name_string .= $js_file.',';
			
			//get the contents of the file
			$file_contents = file_get_contents($path_prefix.$js_file);
			$file_contents_string .= $file_contents;
			if($this->add_new_lines_between_files)
				$file_contents_string .= "\n\r";
		}
		
		//create the new consolidated filename
		$md5 = md5($file_name_string);
		$orig_file_name_string = $md5.'.js';
		$min_file_name_string = $md5.'.min.js';
		
		//save the new consolidated filename
		$orig_file_path = $dest_prefix.$orig_file_name_string;
		file_put_contents($orig_file_path, $file_contents_string);
		echo(">> Saved consolidated file to $orig_file_path\n");
		
		//minify
		if($this->minify_js)
		{
			$min_file_path = $dest_prefix.$min_file_name_string;
			echo(">> Minifiying consolidated file to $min_file_path\n");
			
			//This is the old YUI code
			//$command = "java -jar ".dirname(__FILE__)."/yuicompressor-2.4.2.jar $orig_file_path -o $min_file_path";
			//exec($command);

			//This is the new JSMin code
			$minified_contents = JSMin::minify(file_get_contents($orig_file_path));
			$consolidated_file_name_string = $min_file_name_string;
			file_put_contents($min_file_path, $minified_contents);
		}
		else
		{
			$consolidated_file_name_string = $orig_file_name_string;
		}
		
		//gzip file
				
		
		$line = '<?php echo '.$this->js_function.'("'.$consolidated_file_name_string.'") ?>';
		return $line;
	}
	
	private function consolidate_css_files($layout_file)
	{
		$first_css_line = NULL;
		$pattern = '/'.$this->css_function.'\(.*\)/';
		if($line_array = file($layout_file, FILE_IGNORE_NEW_LINES))
		{	
			$new_line_array = array();
			$line_number = 0;
			foreach($line_array as $line)
			{
				$line_number++;
				//echo($line."\n");
				$matches = array();
				if(preg_match($pattern, $line , $matches))
				{
					//echo($match."\n");
					
					if(is_null($first_css_line))
						$first_css_line = $line_number;
						
					//gather the css file name
					foreach($matches as $match)
					{
						$match_array = explode(',', $match);
						$file_name = $match_array[0];
						$params = $match_array[1];
						
						$file_name = str_replace($this->css_function, '', $file_name);
						$file_name = str_replace("(", '', $file_name);
						$file_name = str_replace(")", '', $file_name);
						$file_name = str_replace("'", '', $file_name);
						$file_name = str_replace('"', '', $file_name);
						$file_name = trim($file_name);
						
						if(strpos($params, 'print'))
						{
							$this->css_print_files[] = $file_name;
						}
						else
						{
							$this->css_files[] = $file_name;
						}
						
						echo($file_name."\n");
					}					
				}
				else
				{
					$new_line_array[] = $line;
				}
			}
			
			$line_number = 0;
			$new_file_string = '';
			foreach($new_line_array as $line)
			{
				$line_number++;
				if($line_number==$first_css_line)
				{
					$consolidated_css_line = $this->get_consolidated_css_line();
					$consolidated_print_css_line = $this->get_consolidated_css_line('print');
					if(count($this->css_files)>0)
					{	
						$new_file_string .= "\t".$consolidated_css_line."\n";
					}
					if(count($this->css_print_files)>0)
					{
						echo("printcss = '".$consolidated_print_css_line."'\n");
						$new_file_string .= "\t".$consolidated_print_css_line."\n";
					}
				}
				$new_file_string .= $line."\n";
			}
			
			$file_handle = fopen($layout_file, 'w');
			fwrite($file_handle, $new_file_string);
			fclose($file_handle);	
		}
		else
		{
			echo($layout_file." is not valid!\n");
		}
		
	}
	
	private function get_consolidated_css_line($mode = 'normal')
	{
		$file_name_string = '';
		$file_contents_string = '';
		$path_prefix = dirname(__FILE__).'/'.$this->web_directory;
		$dest_prefix = $path_prefix.'/'.$this->css_directory.'/';
		if($mode=='print')
		{
			$my_array = $this->css_print_files;
		}
		else
		{
			$my_array = $this->css_files;
		}
		
		foreach($my_array as $css_file)
		{
			$file_name_string .= $css_file.',';
			
			//get the contents of the file
			$file_contents = file_get_contents($path_prefix.$css_file);
			$file_contents_string .= $file_contents;
			if($this->add_new_lines_between_files)
				$file_contents_string .= "\n\r";
		}
		
		//create the new consolidated filename
		$md5 = md5($file_name_string);
		$orig_file_name_string = $md5.'.css';
		$min_file_name_string = $md5.'.min.css';
		
		//save the new consolidated filename
		$orig_file_path = $dest_prefix.$orig_file_name_string;
		file_put_contents($orig_file_path, $file_contents_string);
		echo(">> Saved consolidated file to $orig_file_path\n");
		
		//minify
		if($this->minify_css)
		{
			$min_file_path = $dest_prefix.$min_file_name_string;
			$consolidated_file_name_string = $min_file_name_string;
			echo(">> Minifiying consolidated file to $min_file_path\n");

			//This is the old YUI technique
			//$command = "java -jar ".dirname(__FILE__)."/yuicompressor-2.4.2.jar $orig_file_path -o $min_file_path";
			//exec($command);
			
			//This is the new Minify code
			$minified_contents = Minify_CSS::minify(file_get_contents($orig_file_path));
			$consolidated_file_name_string = $min_file_name_string;
			file_put_contents($min_file_path, $minified_contents);
		}
		else
		{
			$consolidated_file_name_string = $orig_file_name_string;
		}
		
		//gzip file
				
		if($mode=='print')
		{
			$line = '<?php echo '.$this->css_function.'("'.$consolidated_file_name_string.'", array("media" => "print")) ?>';
		}
		else
		{
			$line = '<?php echo '.$this->css_function.'("'.$consolidated_file_name_string.'", array("media" => "all")) ?>';
		}
		
		return $line;
	}
}