<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */

class SdFile extends BaseSdFile
{
	public $tempUploadedFile = NULL;

	//PUBLIC FUNCTIONS

	//returns an absolute web path for use in img src tags
	public function getSrc()
	{
		return $this->getFileDirectoryPath().'/'.$this->getFilename();
	}
	
	public function getTruncatedFileName($chars = 17)
	{
		$result = $this->getFilename();
		if(strlen($result)>$chars)
		{
			$result = substr($result, 0, $chars).'...';
		}
		return $result;
	}
	
	public function getInfo()
	{
		$filesize = filesize(realpath('.').$this->getSrc());
		$type = ( $filesize > 100000 ) ? "MB" : "KB" ;
		switch($type){  
			case "KB":  
           	$filesize = $filesize * .0009765625;
         		break;  
         	case "MB":  
           	$filesize = $filesize * .0009765625 * .0009765625;
         		break;  
     	}  
        $filesize = ($filesize <= 0) ? 'unknown file size' : round($filesize, 2).' '.$type;

		$ret = '<p>';
		$ret .= '<strong>File Name:</strong> '. $this->filename .'<br />';
		$ret .= '<strong>File Size:</strong> '. $filesize .'<br />';
		$ret .= '<strong>File Type:</strong> '. strtoupper($this->getIconSrc(true));
		$ret .= '</p>';
		return $ret;
	}

	
	public function getModal()
	{
		switch ($this->getIconSrc(true))
		{
			case 'gif':
			case 'png':
			case 'jpg':
			case 'jpeg':
			case 'avi':
			case 'mov':
				$modal = 'nyroModal';
				break;
			default:
				$modal = '';
				break;
		}
		return $modal;
	}

	// This function returns an absolute path to an icon for use in img tags
	public function getIconSrc($justType = false)
	{
		$defaultFilename = '/images/icons/files/default-file.gif';
		$fileType = strtolower(substr($this->filename, strrpos($this->filename, '.') +1));
		
		if($justType) {
			return $fileType;
		}
		
		$filename = '/images/icons/files/'.$fileType.'.png';
		
		return file_exists(realpath('.').$filename) ? $filename : $defaultFilename;

		/*switch($this->getFileExtension())
		{
			case 'gif':
			case 'png':
			case 'jpg':
			case 'jpeg':
				$iconPath = $this->getThumbnailSrc(50, 50);
				break;
			case 'xls':
			case 'xlsx':
			case 'csv':
				$iconPath = '/images/icons/excel-file.gif';
				break;
			case 'doc':
			case 'docx':
				$iconPath = '/images/icons/word-file.gif';
				break;
			case 'pdf':
				$iconPath = '/images/icons/pdf-file.gif';
			default:
				$iconPath = '/images/icons/default-file.gif';
				break;
		}

		return $iconPath;*/
	}

	// This function returns an absolute path to a thumbnail for use in img tags
	// If the thumbnail file does not exist yet, this function will create it
	public function getThumbnailSrc($width, $height, $resizeMethod = 'scale')
	{
		$thumbnailPath = $this->getThumbNailPath($width, $height, $resizeMethod);

		//check if the physical file exists, if not then create this thumbnail
		$realFile = realpath('.').$thumbnailPath;
		if(!file_exists($realFile))
		{
			$this->createThumbnail($width, $height, $resizeMethod);
		}

		return $thumbnailPath;
	}

	//returns the extension of a file. For example 'jpg'
	public function getFileExtension()
	{
		return strtolower(substr(strrchr($this->getFilename(), '.'), 1));
	}

	//Returns the path of the core file on the filesystem
	public function getFilePath()
	{
		return realpath('.').$this->getSrc();
	}

	//overrides the base delete() function so we can delete joins and the physical file
	public function delete(Doctrine_Connection $conn = null)
	{
		//TODO: remove all join records

		//Remove the physical file
		$command = 'rm -rf '.realpath('.').$this->getFileDirectoryPath();
		$commandResult = exec($command);

		//delete the SdFile record
		parent::delete($conn);
	}

	//gets just the path of the file's directory
	public function getFileDirectoryPath($realPath = false)
	{
		$lowerBound = $this->getId() - ($this->getId() % 1000) + 1;
		$upperBound = $lowerBound + 999;
		$result = '/uploads/file/'.$lowerBound.'-'.$upperBound.'/'.$this->getId();

		if($realPath)
			$result = realpath('.').$result;

		return $result;
	}

	//PRIVATE FUNCTIONS

	// This function returns the correct path of the thumbnail on the system
	private function getThumbnailPath($width, $height, $resizeMethod = 'scale')
	{
		$thumbFileName = basename($this->getFilename(), '.'.$this->getFileExtension()).'-'.$width.'x'.$height.'-'.$resizeMethod.'.'.$this->getFileExtension();
		$thumbnailPath = $this->getFileDirectoryPath().'/thumbs/'.$thumbFileName;

		return $thumbnailPath;
	}

	// This function returns the path to the thumbnail directory
	private function getThumbnailDir()
	{
		$thumbnailDir = $this->getFileDirectoryPath().'/thumbs';

		return $thumbnailDir;
	}

	//Generates a thumbnail file given the parameters
	private function createThumbnail($width, $height, $resizeMethod = 'scale')
	{
		list($currentWidth, $currentHeight, $type, $attr) =  getimagesize($this->getFilePath()); 
		
		$oThumbnail = new sfThumbnail($width, $height);
		$oThumbnail->loadFile($this->getFilePath());

		$thumbnailPath = $this->getThumbNailPath($width, $height, $resizeMethod);
		$realThumbnailPath = realpath('.').$thumbnailPath;

		$thumbnailDir = realpath('.').$this->getThumbnailDir();
		if(!file_exists($thumbnailDir))
			mkdir($thumbnailDir);
			
		$oThumbnail->save($realThumbnailPath);
	}


}