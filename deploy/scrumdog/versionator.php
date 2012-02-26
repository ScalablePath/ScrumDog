<?php

/** THE VERSIONATOR 
*
* This is a script for using SVN revision numbers to improve site cachability.
* This script should be used in conjunction with Subversion and cache headers that expire far in the future.
* This class is meant to be run on production servers to improve performance and the files that are
* created and modified by this script should never be committed to the repository.
*
* @author Damien Filiatrault
* @date 7/6/2009
**/

$versionator = new Versionator();
$versionator->delete_original_file = false;
$versionator->static_content_dirs = array('web');
$versionator->code_replacement_dirs = array('apps/frontend', 'web');
$versionator->run();

class Versionator
{
	/** Begin Public Configuration Variables (You may adjust these to the needs of your project) **/
	
	public $static_file_types = array('jpg', 'jpeg', 'gif', 'png', 'swf', 'js', 'css', 'ico');
	//This is relative to the location of the versionator script
	public $static_content_dirs = array();
	//Will descend recursively into each directory
	public $static_recursive = true;
	
	
	public $code_file_types = array('php', 'html', 'css', 'js', 'yml');
	//This is relative to the location of the versionator script
	public $code_replacement_dirs = array();
	//Will descend recursively into each directory
	public $code_recursive = true;
	
	public $ignore_directories = array('.', '..', '.svn');
	
	public $delete_original_file = true;
	
	/** Private Variables **/
	private $versioned_files = array();
	private $versioned_by_type = array();
	private $modified_code_files = array();
	
	private $total_replacements = 0;
	
	/** Public Functions **/
	public function run()
	{
		//Find all of the files in the static content directories and version them
		foreach($this->static_content_dirs as $dir)
		{
			$real_dir = dirname(__FILE__).'/'.$dir;
			$this->version_static_files($real_dir);
		}
		
		foreach($this->code_replacement_dirs as $dir)
		{
			$real_dir = dirname(__FILE__).'/'.$dir;
			$this->replace_code_instances($real_dir);
		}
		
		//print_r($this->versioned_files);
		//print_r($this->versioned_by_type);
		echo(">> Versioned ".count($this->versioned_files)." files\n");
		echo(">> Replaced $this->total_replacements instances in ".count($this->modified_code_files)." files\n");
	}
	
	/** Private Functions **/
	
	//Search through all of the static file folders
	private function version_static_files($dir)
	{
		$dir_handle = opendir($dir);
		if($dir_handle)
		{
		
			$entries_file = $dir.'/.svn/entries';
			$regex = '/^[0-9]+$/';
		
			while(false !== ($file = readdir($dir_handle)))
			{ 
				$file_extension = substr($file, strrpos($file, '.') + 1);
				$real_file = $dir.'/'.$file;
				
				//if it's a directory descend if we are in recursive mode
				if(is_dir($real_file))
				{
					if(!in_array($file, $this->ignore_directories) && $this->static_recursive===true)
					{
						$this->version_static_files($real_file);
					}
				}
				else
				{
					if(in_array($file_extension, $this->static_file_types))
					{
						//get the revision number
						
						$entries_handle = false;
						if(file_exists($entries_file))
							$entries_handle = fopen($entries_file, 'r');
						
						if($entries_handle)
						{
							$under_control = false;
							$revision_number = NULL;
							
							while(!feof($entries_handle))
							{
								$line = trim(fgets($entries_handle));
								if(!$under_control)								
								{
									if($line==$file)
									{
										$under_control = true;
									}
								}
								else
								{
									if(preg_match($regex, $line))
									{
										$revision_number = $line;
										break;
									}
								}						
							}
							
							if($under_control)
							{
							
								$file_name = substr($file, 0, strrpos($file, '.'));
								$new_file =  $file_name.'-'.$revision_number.'.'.$file_extension;
							
								$real_new_file = $dir.'/'.$new_file;
								copy($real_file, $real_new_file);
								
								if($this->delete_original_file)
									unlink($real_file);
								
								echo($real_new_file."\n");
					
								//store the file in the versioned files array
								$this->versioned_files[$real_file] = $real_new_file;
								if(isset($this->versioned_by_type[$file_extension]) && isset($this->versioned_by_type[$file_extension][$file]))
								{
									echo("!!!ERROR: There are two files with the name $file\n");
								}
									
								$this->versioned_by_type[$file_extension][$file] = $new_file;
							}
							fclose($entries_handle);
						}
						else
						{
							//echo($real_file." is not under source control.\n");
						}
					}
				}					
			}
			closedir($dir_handle);
		}
		else
		{
			echo($dir." not valid!\n");
		}
	}
	
	//Search through all of the code replacement directories
	private function replace_code_instances($dir)
	{	
		if(count($this->versioned_files)>0)
		{	
			foreach($this->versioned_by_type as $file_type => $file_type_array)
			{
				//figure out the search and replace arrays for this file type
				$search_array = array();
				$replace_array = array();
					
				foreach($file_type_array as $orig_file => $new_file)
				{
					$escaped_file_name = str_replace('.', '\.', $orig_file);
					$regex = '/'.$escaped_file_name.'/';
					$search_array[] = $regex;
					$replace_array[] = $new_file;
				}
				
				//narrow down the number of files we need to open by using grep			
				$grep_results = shell_exec('grep -ilR "\.'.$file_type.'" '.$dir);
				$grep_results_array = explode("\n", $grep_results);
					
				$new_results = array();
				foreach($grep_results_array as $grep_result)
				{
					//ignore svn files
					$file_name_array = explode(':', $grep_result);
					$file_path = $file_name_array[0];
					$path_array = explode('/', $file_path);
					$file_extension = substr($file_path, strrpos($file_path, '.') + 1);
					
					if(!in_array('.svn', $path_array) && trim($file_path)!='' && in_array($file_extension, $this->code_file_types))
						$new_results[] = $file_path;
				}
				
				//make the replacements
				foreach($new_results as $file_to_search)
				{
					$file_string = file_get_contents($file_to_search);
					$count = 0;
					$new_string = preg_replace($search_array, $replace_array, $file_string, -1, $count);

					if($count>0)
					{
						$handle = fopen($file_to_search, 'w');
						fwrite($handle, $new_string);
						fclose($handle);
						echo("Replaced $count instances in $file_to_search\n");
						$this->modified_code_files[$file_to_search] = $count;
						$this->total_replacements += $count;
					}				
				}
			}
		}
	}
}