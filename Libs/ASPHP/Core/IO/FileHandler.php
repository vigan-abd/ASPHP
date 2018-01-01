<?php
namespace ASPHP\Core\IO;

/**
 * @requires class \ASPHP\Core\ErrorHandler\Error
 * @version 1.0
 * @author Vigan
 */
class FileHandler
{
    /**
     * Simple file write
     * @param string $file
     * @param string $text
     * @return bool
	 * @throws \Exception
     */
    public static function WriteFile($file, $mode, $text)
    {
        $fp = fopen($file, $mode, true);
        if(!$fp)
        {
            return false;
        }

        flock($fp, LOCK_EX);
        fwrite($fp, $text, strlen($text));
        flock($fp, LOCK_UN);
        fclose($fp);

        return true;
    }

	/**
	 * @param string $file 
	 * @throws \Exception 
	 * @return string
	 */
	public static function ReadFile($file)
	{
		if (!file_exists($file))
			throw new \Exception("File '{$file}' does not exist!");
		return file_get_contents($file);
	}

    /**
     * @param string $inputName
     * @param string $directory
     * @param string $fileName
     * @throws \Exception
     */
    public static function UploadFile($inputName, $directory, $fileName)
    {
        //$directory = iconv(mb_detect_encoding($directory, mb_detect_order(), true), "ISO1-8859-1//IGNORE", $directory);
        //$fileName = iconv(mb_detect_encoding($fileName, mb_detect_order(), true), "ISO-8859-1//IGNORE", $fileName);
        if($_FILES[$inputName]['error']>0)
        {
            switch($_FILES[$inputName]['error'])
            {
                case 1: throw new \Exception('File exceeded upload_max_filesize');
                case 2: throw new \Exception('File exceeded max_file_size');
                case 3: throw new \Exception('File only partially uploaded');
                case 4: throw new \Exception('No file uploaded');
                case 6: throw new \Exception('No File uploaded');
                case 7: throw new \Exception('Upload failed: Cannot write to disk');
            }
        }

        if(!file_exists($directory))//Check if directory exists
        {
            if(!mkdir($directory))
            {
				throw new \Exception('Could not create directory');
            }
        }

        if(file_exists($directory.$fileName))//Check if file exists
        {
            if(!unlink($directory.$fileName))
            {
				throw new \Exception('Could not insert file');
            }
        }

        if(is_uploaded_file($_FILES[$inputName]['tmp_name']))
        {//tmp_name is temporary directory in server
            if(!move_uploaded_file($_FILES[$inputName]['tmp_name'], $directory.$fileName))
            {
                throw new \Exception('Problem: Could not move file to destination');
            }
        }
        else
        {
            throw new \Exception('Problem: Possible file upload attack. Filename: '.basename($_FILES[$inputName]['name']));
        }
    }

	public static function ListFolderFiles($dir, &$collections)
	{
		$ffs = scandir($dir);

		unset($ffs[array_search('.', $ffs, true)]);
		unset($ffs[array_search('..', $ffs, true)]);

		// prevent empty ordered elements
		if (count($ffs) < 1)
			return;

		foreach($ffs as $ff)
		{
			if(!is_dir($dir.'/'.$ff))
				$collections[] = $dir.'/'.$ff;
			else
				static::ListFolderFiles($dir.'/'.$ff, $collections);
		}
	}
}

?>