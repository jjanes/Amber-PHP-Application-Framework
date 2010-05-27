<?PHP
/* new file(file,args,must_exists)
 * File - the filename
 * Args 
 * 		W - writeable
 * 		R - Readable
 * 		C - Create if does not exists
 *
 * must_exists -  File must exists 
 * 	
 * 
 *  Methods
 *        size() - returns files in byes
 *        read() - read contents of file.
 *        exec() - include file and return output in a variable
 *        file() - return full path of file
 *     canRead() - return boolean if can read or not
 *    canWrite() - return boolean if can write 
 *        path() - return path only 
 *        name() - return basename only
 *      exists() - returns boolean if file exists
 *    creation() - returns creation time
 *        last() - return last access time
 * write(string) - write string to file  
 *  
 * 
 * 
 * 
 */
class file 
{
	private $file;						// file name and path
	private $buffer;					
	
	function __construct($file,$arg = 'R',$must_exists = true) 
	{
		$this->file = $file;
		
		$error = array();
			
		if (!file_exists($file) && $must_exists) 
		{
			throw new Exception(sprintf("File (%s) does not exists. [%s:%s]",$file,__FILE__,__LINE__));
		}
		
		// $args = strtoarray(strtoupper($arg));

		if (strpos(strtoupper($arg),'R')) 				// check to make sure file is readable
			$args[] = 'R';
		if (strpos(strtoupper($arg),'W')) 				// check to make sure file is writeable
			$args[] = 'W';
				
		if (count($error) < 1 && $args) 
		{											// create file if it doesnt exist.
			if (array_Search('C',$args)) 
			{
				$res = fopen($file,'w+');
				fclose($res);
			}
			
			foreach ($args as $k => $v) 
			{
				switch ($v) 
				{
					case 'R':
						if (!is_readable($file) && file_exists($file)) 
							{																				// make sure file is readable and exists
							$error[] = sprintf("File (%s) is not readable.",$file);	
						}
						break;
					case 'W':																				// is writable
						if (file_exists($file)) 
						{														
							if (!is_writeable($file)) 
								$error[] = sprintf("File (%s) is not writeable.",$file);
						}
						if (!is_writable(dirname($file))) {
							$error[] = sprintf("Directory (%s) is not writeable.",dirname($file));
						}
						break;
					default:
				}
			}
		}
		
		if (count($error) > 0) 
		{
			$errors = '<br /> ' .join(' <Br />',$error);
			throw new Exception($errors);
		}
	}
	
	public function read() 
	{
		if (is_readable($this->file)) 
		{
			$content = '';
			$handle = fopen($this->file,'r');
			while(!feof($handle)){ $content .= fgets($handle); }
			fclose($handle);
			return $content;
		} else {
			throw new Exception("Cannot read file.");
		}
		
	}
	public function exec() 
	{
	   	ob_start();								// capture file output start buffer
	   	try 
	   	{
    		include($this->file);				// include file
	   	} 
	   	catch (Exception $ex) 					// catch exception 
	   	{
	   		ob_end_clean();						// clean up buffer 
	   		throw new Exception("Could not execute file");
	   	}
    	$return_str = ob_get_contents(); 		
    	ob_end_clean();							//
    	return $return_str;						// return buffer 
	}
	
	// file size
	public function size() 
	{ 
		return filesize($this->file);
	}
	// full path and file
	public function file() 
	{
		return $this->file;
	}
	// can write
	public function canWrite() 
	{
		return (is_writeable($this->file))?true:false; 
	}
	// can read?
	public function canRead() 
	{
		return (is_readable($this->file))?true:false; 
	}	
	// file path
	public function path() 
	{
		return dirname($this->file);
	}
	// base name
	public function name() 
	{
		return basename($this->file);
	}
	// file exists?
	public function exists() 
	{ 
		return file_exists($this->file);
	}
	// creation time
	public function creation() 
	{ 
		return (file_exists($this->file))?mktime($this->file):0;
	}
	// last access time
	public function last() 
	{ 
		return (file_exists($this->file))? filemtime($this->file):0;
	}
	public function write($string) 
	{
		$handle = fopen($this->file,'w');		// open file for writing 
		fwrite($handle,$string);				// write to file
		fclose($handle); 						// close file.
	}
}
?> 