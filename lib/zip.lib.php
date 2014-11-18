<?php

/**
 * Zip file creation class.
 * Makes zip files.
 *
 * Based on :
 *
 *  http://www.zend.com/codex.php?id=535&single=1
 *  By Eric Mueller <eric@themepark.com>
 *
 *  http://www.zend.com/codex.php?id=470&single=1
 *  by Denis125 <webmaster@atlant.ru>
 *
 *  a patch from Peter Listiak <mlady@users.sourceforge.net> for last modified
 *  date and time of the compressed file
 *
 * Official ZIP file format: http://www.pkware.com/appnote.txt
 *
 * @access  public
 */
class zipfile
{
    
    var $datasec      = array();

    
    var $ctrl_dir     = array();

    var $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00";

    var $old_offset   = 0;



    function unix2DosTime($unixtime = 0) {
        $timearray = ($unixtime == 0) ? getdate() : getdate($unixtime);

        if ($timearray['year'] < 1980) {
        	$timearray['year']    = 1980;
        	$timearray['mon']     = 1;
        	$timearray['mday']    = 1;
        	$timearray['hours']   = 0;
        	$timearray['minutes'] = 0;
        	$timearray['seconds'] = 0;
        } // end if

        return (($timearray['year'] - 1980) << 25) | ($timearray['mon'] << 21) | ($timearray['mday'] << 16) |
                ($timearray['hours'] << 11) | ($timearray['minutes'] << 5) | ($timearray['seconds'] >> 1);
    } // end of the 'unix2DosTime()' method

    function addFile($data, $name, $time = 0)
    {
        $name     = str_replace('\\', '/', $name);

        $dtime    = dechex($this->unix2DosTime($time));
        $hexdtime = '\x' . $dtime[6] . $dtime[7]
                  . '\x' . $dtime[4] . $dtime[5]
                  . '\x' . $dtime[2] . $dtime[3]
                  . '\x' . $dtime[0] . $dtime[1];
        eval('$hexdtime = "' . $hexdtime . '";');

        $fr   = "\x50\x4b\x03\x04";
        $fr   .= "\x14\x00";            // ver needed to extract
        $fr   .= "\x00\x00";            // gen purpose bit flag
        $fr   .= "\x08\x00";            // compression method
        $fr   .= $hexdtime;             // last mod time and date

        // "local file header" segment
        $unc_len = strlen($data);
        $crc     = crc32($data);
        $zdata   = gzcompress($data);
        $zdata   = substr(substr($zdata, 0, strlen($zdata) - 4), 2); // fix crc bug
        $c_len   = strlen($zdata);
        $fr      .= pack('V', $crc);             // crc32
        $fr      .= pack('V', $c_len);           // compressed filesize
        $fr      .= pack('V', $unc_len);         // uncompressed filesize
        $fr      .= pack('v', strlen($name));    // length of filename
        $fr      .= pack('v', 0);                // extra field length
        $fr      .= $name;

        // "file data" segment
        $fr .= $zdata;

        // "data descriptor" segment (optional but necessary if archive is not
        // served as file)
        $fr .= pack('V', $crc);                 // crc32
        $fr .= pack('V', $c_len);               // compressed filesize
        $fr .= pack('V', $unc_len);             // uncompressed filesize

        // add this entry to array
        $this -> datasec[] = $fr;

        // now add to central directory record
        $cdrec = "\x50\x4b\x01\x02";
        $cdrec .= "\x00\x00";                
        $cdrec .= "\x14\x00";                
        $cdrec .= "\x00\x00";                
        $cdrec .= "\x08\x00";                
        $cdrec .= $hexdtime;                 
        $cdrec .= pack('V', $crc);           
        $cdrec .= pack('V', $c_len);         
        $cdrec .= pack('V', $unc_len);       
        $cdrec .= pack('v', strlen($name) ); 
        $cdrec .= pack('v', 0 );             
        $cdrec .= pack('v', 0 );             
        $cdrec .= pack('v', 0 );             
        $cdrec .= pack('v', 0 );             
        $cdrec .= pack('V', 32 );            

        $cdrec .= pack('V', $this -> old_offset ); 
        $this -> old_offset += strlen($fr);

        $cdrec .= $name;
        $this -> ctrl_dir[] = $cdrec;

    } // end of the 'addFile()' method


    
    function file()
    {
        $data    = implode('', $this -> datasec);
        $ctrldir = implode('', $this -> ctrl_dir);

        return
            $data .
            $ctrldir .
            $this -> eof_ctrl_dir .
            pack('v', sizeof($this -> ctrl_dir)) . 
            pack('v', sizeof($this -> ctrl_dir)) .  
            pack('V', strlen($ctrldir)) .           
            pack('V', strlen($data)) .              
            "\x00\x00";                             
    } // end of the 'file()' method

} // end of the 'zipfile' class
?>