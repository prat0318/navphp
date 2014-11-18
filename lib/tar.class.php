<?php
/*
  Author: aldo
  For: SnowCMS (www.snowcms.com) or for whatever use
  License: GNU GPL v3 License (www.gnu.org/licenses/gpl-3.0.txt )

  Class: Tar

  With this class, you can open and read tarballs and gzipped tarballs.
  You can also create tarballs, and gzip them as well.
*/
class Tar
{
  # Variable: filename
  private $filename;

  # Variable: filemtime
  private $filemtime;

  # Variable: fp
  private $fp;

  # Variable: mode
  private $mode;

  # Variable: files
  private $files;

  # Variable: gzipped
  private $is_gzipped;

  # Variable: is_ustar
  private $is_ustar;

  /*
    Constructor: __construct
  */
  public function __construct($filename = null, $mode = 'r')
  {
    $this->filename = null;
    $this->filemtime = null;
    $this->fp = null;
    $this->mode = null;
    $this->files = null;
    $this->is_gzipped = null;
    $this->is_ustar = null;

    if(!empty($filename))
      $this->open($filename, $mode);
  }

  /*
    Method: open

    Opens the specified tar file for either reading or writing.

    Parameters:
      string $filename - The name of the file to open.
      string $mode - r to open the tar file for reading, and w
                     to open the tar file for writing.

    Returns:
      bool - Returns true on success, false on failure.

    Note:
      If the mode is set to reading, opening if the file doesn't exist,
      of course. However, if set to writing, the file will be created if
      it doesn't exist already, but if it does, the file will be overwritten!
  */
  public function open($filename, $mode = 'r')
  {
    # Already doing something right now? Sorry!
    if(!empty($this->mode))
      return false;

    $mode = strtolower($mode);

    if($mode == 'r')
    {
      # Open the file for reading, if it exists!
      if(!file_exists($filename) || (file_exists($filename) && !is_file($filename)))
        return false;

      $filename = realpath($filename);

      # Now open it for reading.
      $fp = fopen($filename, 'rb');

      if(empty($fp))
        return false;

      $this->filename = $filename;
      $this->fp = $fp;
      $this->mode = 'r';

      # It's mine now ;) Sorta.
      flock($this->fp, LOCK_SH);

      # Check to see if it is gzipped, because if it is, that needs handling first!
      # The first couple of bytes will tell us that...
      $magic = unpack('H2a/H2b', fread($this->fp, 2));

      if(strtolower($magic['a']. $magic['b']) == '1f8b')
        $this->is_gzipped = true;
      else
        $this->is_gzipped = false;

      # Not gzipped? We can check if it is UStar formatted!
      $this->check_format();

      # Back to 0!
      fseek($this->fp, 0);

      return true;
    }
    elseif($mode == 'w')
    {
      # Just try to open it.
      $fp = fopen($filename, 'wb');

      if(empty($fp))
        return false;

      $this->filename = $filename;
      $this->fp = $fp;
      $this->mode = 'w';
      $this->files = array();
      $this->is_gzipped = false;

      # This time it IS mine :P
      flock($this->fp, LOCK_EX);

      return true;
    }

    return false;
  }

  /*
    Method: check_format

    Checks to see if the current tarball is setup with the older format, or the
    newer UStar format.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this method.
  */
  private function check_format()
  {
    if(empty($this->mode) || $this->mode != 'r' || $this->is_gzipped())
      return;

    fseek($this->fp, 257);

    # At position 257, there should be ustar...
    $ustar = strtolower(trim(str_replace(chr(0), '', fread($this->fp, 6))));
    $this->is_ustar = $ustar == 'ustar';

    fseek($this->fp, 0);
  }

  /*
    Method: files

    When the mode is read, then all the file information inside the current
    tar file will be returned, otherwise the current files which will be added
    to the tar file will be returned.

    Parameters:
      none

    Returns:
      array
  */
  public function files()
  {
    if(empty($this->mode) || ($this->mode == 'r' && $this->is_gzipped()))
      return false;

    if($this->mode == 'r')
    {
      # Did we already do this? Make sure the file hasn't been modified since, either.
      if($this->filemtime !== null && $this->filemtime >= filemtime($this->filename) && !empty($this->files))
        return $this->files;

      # Get the number of bytes in the file.
      fseek($this->fp, 0, SEEK_END);
      $bytes = ftell($this->fp);
      fseek($this->fp, 0);

      # Some of the header data needs to be converted from octal to decimal.
      $octal = array('mode', 'uid', 'gid', 'size', 'mtime', 'chksum', 'type');

      # And then the format of what we shall read!
      $format = 'a100name/a8mode/a8uid/a8gid/a12size/a12mtime/a8chksum/a1type/a100linkname';

      # Now, if the tar is in UStar format, we read a bit extra ;)
      if($this->is_ustar())
        $format .= '/a6magic/a2version/a32uname/a32gname/a8devmajor/a8devminor/a155prefix';

      $files = array();
      while($bytes > 0)
      {
        $file = unpack($format, ($header = fread($this->fp, 512)));

        # Remove extra spacing, and convert octals to decimals!
        foreach($file as $key => $value)
        {
          $file[$key] = trim($value);

          if(in_array($key, $octal))
            $file[$key] = octdec($file[$key]);
        }

        # Is it a file or directory?
        $file['is_dir'] = substr($file['name'], -1, 1) == '/';

        # Just save the position of the file, for later use!
        $file['pos'] = ftell($this->fp);

        # Ignore the file data, for now (The file size must be a multiple of 512)...
        $seek = $file['size'] + (($file['size'] / (double)512) == 0 ? 0 : 512 - ($file['size'] % 512));
        fseek($this->fp, $seek, SEEK_CUR);

        # Remove some bytes.
        $bytes -= 512 + $seek;

        # File name not empty? Then it's good (For some reason, there are empty records added, at least 2!)
        if(!empty($file['name']))
          $files[] = $file;
      }

      # Cache it, for a bit.
      $this->files = $files;
      $this->filemtime = filemtime($this->filename);

      return $files;
    }
    elseif($this->mode == 'w')
    {
      # Just return what we got!
      return $this->files;
    }

    return false;
  }

  /*
    Method: extract

    Extracts the files out of the tar file, if the mode is reading.

    Parameters:
      string $destination - Where to extract the tarball.
      bool $safe_mode - It is possible for people to have such file names as ../../someImportantFile.sys
                        and overwrite important system files. By setting this option to true, any ../ will
                        be removed from the file or directory name.

    Returns:
      bool - Returns true on success, false on failure.

    Note:
      Of course, if the file is gzipped, this method will return false, you can check out the
      <Tar::ungzip> method to ungzip the tarball.
  */
  public function extract($destination, $safe_mode = true)
  {
    if(empty($this->mode) || $this->mode != 'r' || $this->is_gzipped())
      return false;

    # Does the destination exist? Is it a directory?
    if(!file_exists($destination))
    {
      $made = mkdir($destination);

      if(empty($made))
        # We tried, but it failed! :(
        return false;
    }
    elseif(file_exists($destination) && !is_dir($destination))
      # It isn't a directory, silly pants!
      return false;

    # Turn it into an absolute path.
    $destination = realpath($destination);

    # The files method saves the position of the file, so yeah... Simple enough, really.
    $this->files();

    # Before we get head over heels, are there even any files?
    if(count($this->files))
    {
      foreach($this->files as $file)
      {
        # Prepend the the destination to the file name!
        $file['name'] = $destination. '/'. $file['name'];

        # Safe mode on..?
        if(!empty($safe_mode))
          $file['name'] = strtr($file['name'], array('../' => '', '/..' => ''));

        # Now, is it a directory, or a file?
        if($file['is_dir'])
          # Make that directory, and we are done.
          @mkdir($file['name']);
        else
        {
          # It's a file, super fun!
          fseek($this->fp, $file['pos']);

          # Open the file that needs creation.
          $fp = fopen($file['name'], 'wb');

          if(empty($fp))
            continue;

          # Small enough to do it quickly?
          if($file['size'] <= 8192 && $file['size'] > 0)
            fwrite($fp, fread($this->fp, $file['size']));
          elseif($file['size'] > 8192)
          {
            # Nope...
            $left = $file['size'];
            while($left > 0)
            {
              fwrite($fp, fread($this->fp, $left >= 8192 ? 8192 : $left));
              $left -= $left >= 8192 ? 8192 : $left;
            }
          }
        }
      }

      fseek($this->fp, 0);
    }

    return true;
  }

  /*
    Method: ungzip

    Just incase, if the tarball is gzipped, you can ungzip it via this method.

    Parameters:
      none

    Returns:
      bool - Returns true on success, false on failure.

    Note:
      This requires the PHP extension www.php.net/zlib, though most hosts seem to have it.

      The contents of the ungzipped file will be written to the current file.
  */
  public function ungzip()
  {
    if(empty($this->mode) || $this->mode != 'r' || !$this->is_gzipped())
      return false;

    # We already checked the magic number (which is how the gzipped attribute was set to true),
    # next check the compression method, which should be 8! Get out the flag while we are at it too.
    fseek($this->fp, 2);
    $info = unpack('Ccm/Cflg', fread($this->fp, 2));

    # Compression method not 8? Then I'm not up to the task! (The only method right now is 8 anyways >.>)
    if($info['cm'] != 8)
      return false;

    # Skip past the modified time, XFL and OS, I don't really care about that :P
    fseek($this->fp, 6, SEEK_CUR);

    # File name? Don't want it! But gotta get past it.
    if($info['flg'] & 8 || $info['flg'] & 3) # Should be 3, 7z seems to do 8? o.O
      while(fread($this->fp, 1) != chr(0))
        # Just keep going!
        continue;

    # Comment, perhaps?
    if($info['flg'] & 4)
      while(fread($this->fp, 1) != chr(0))
        # Just keep going, again!
        continue;

    # CRC16 stuff?
    if($info['flg'] & 1)
      # Skip past the next 2 bytes.
      fseek($this->fp, 2, SEEK_CUR);

    # Now we need to store the data to inflate it!
    $tar = '';

    # How many bytes do we need to read?
    $cur_pos = ftell($this->fp);
    fseek($this->fp, 0, SEEK_END);
    $bytes = ftell($this->fp);
    fseek($this->fp, $cur_pos);

    while($bytes > 0)
    {
      $tar .= fread($this->fp, $bytes >= 8192 ? 8192 : $bytes);
      $bytes -= $bytes >= 8192 ? 8192 : $bytes;
    }

    # Ungzip it now, then we can write it to the current file!!!
    $tar = gzinflate($tar);

    # Can't write to a file that is opened in read only, can we?
    fclose($this->fp);

    $this->fp = fopen($this->filename, 'wb');
    flock($this->fp, LOCK_EX);
    fwrite($this->fp, $tar);
    fclose($this->fp);

    # Now open it in read only mode :P
    $this->fp = fopen($this->filename, 'rb');
    flock($this->fp, LOCK_SH);

    # All done! And it is no longer gzipped! :)
    $this->is_gzipped = false;

    # Oh! And check to see the tarballs format, just incase!
    $this->check_format();

    return true;
  }

  /*
    Method: add_file

    Adds a file to the tarball that is currently being created.

    Parameters:
      string $filename - The name of the file to add to the tarball.
      string $new_filename - The new name of the file (including the relative
                             path, so just the files name to have it in
                             the root directory of the tarball).

    Returns:
      bool - Returns true on success, false on failure.

    Note:
      If no new file name is supplied, and the file is within the current
      working directory, then the new file name will be created automatically,
      so the parameter is not required, however, if it is not within the
      current working directory, adding will fail unless you supply the name.

      Also, any ../ references in the new file name will be removed!
  */
  public function add_file($filename, $new_filename = null)
  {
    # Check the usual, and whether or not the file exists.
    if(empty($this->mode) || $this->mode != 'w' || !file_exists($filename) || !is_file($filename))
      return false;

    # Resolve the absolute file path.
    $filename = realpath($filename);

    # No new filename supplied? Alright, I'll try my best!
    if(empty($new_filename) && substr($filename, 0, strlen(getcwd())) == getcwd())
      $new_filename = substr($filename, strlen(getcwd()) + 1, strlen($filename));
    elseif(empty($new_filename))
      return false;

    # Is the new file name a directory? Nuh uh!
    if(substr($new_filename, -1, 1) == '/')
      return false;

    # Remove any ./ or ../
    $new_filename = strtr($new_filename, array('../' => '', '/..' => ''));

    # Add it to the files array, and thats it, for now.
    $this->files[$new_filename] = array(
                                    'name' => $filename,
                                    'stat' => stat($filename),
                                  );

    return true;
  }

  /*
    Method: add_from_string

    Adds a file from a string to the tarball that is currently being created.

    Parameters:
      string $filename - The name of the file that will be created inside the tarball.
      string $file - The contents of the file.

    Returns:
      bool - Returns true on success, false on failure.

    Note:
      Just as with <Tar::add_file>, any ../ references in the file name will
      be removed.
  */
  public function add_from_string($filename, $file)
  {
    # Check the usual and whether or not you are trying to make a directory ;)
    if(empty($this->mode) || $this->mode != 'w' || empty($filename) || substr($filename, -1, 1) == '/')
      return false;

    # No ../ ;)
    $filename = strtr($filename, array('../' => '', '/..' => ''));

    # Simply add the file data.
    $this->files[$filename] = array(
                                'data' => $file,
                                'stat' => array(
                                            'dev' => 0,
                                            'ino' => 0,
                                            'mode' => 755,
                                            'nlink' => 0,
                                            'uid' => 0,
                                            'gid' => 0,
                                            'rdev' => 0,
                                            'size' => strlen($file),
                                            'atime' => 0,
                                            'mtime' => 0,
                                            'ctime' => 0,
                                            'blksize' => 0,
                                            'blocks' => 0,
                                          ),
                              );

    return true;
  }

  /*
    Method: add_empty_dir

    Adds an empty directory to the tarball that is currently being created.

    Parameters:
      string $dirname - The directory name to be created inside the tarball.

    Returns:
      bool - Returns true on success, false on failure.

    Note:
      Any ../ references will be removed.
  */
  public function add_empty_dir($dirname)
  {
    if(empty($this->mode) || $this->mode != 'w' || empty($dirname))
      return false;

    # Don't have a / at the end, it is needed, but I can do it :P
    if(substr($dirname, -1, 1) != '/')
      $dirname .= '/';

    $dirname = strtr($dirname, array('../' => '', '/..' => ''));

    # Add it, done!
    $this->files[$dirname] = array(
                               'stat' => array(
                                           'dev' => 0,
                                           'ino' => 0,
                                           'mode' => 755,
                                           'nlink' => 0,
                                           'uid' => 0,
                                           'gid' => 0,
                                           'rdev' => 0,
                                           'size' => 0,
                                           'atime' => 0,
                                           'mtime' => 0,
                                           'ctime' => 0,
                                           'blksize' => 0,
                                           'blocks' => 0,
                                         ),
                             );

    return true;
  }

  /*
    Method: set_gzip

    When the tarball is created (written to the file), and if this is set to true,
    and tarball will be gzipped before the file is closed.

    Parameters:
      bool $gzip - Whether or not to gzip the tarball.

    Returns:
      bool - Returns true on success, false on failure.
  */
  public function set_gzip($gzip = true)
  {
    if(empty($this->mode) || $this->mode != 'w')
      return false;

    $this->is_gzipped = !empty($gzip);
    return true;
  }

  /*
    Method: save

    Saves the created tarball into a file.

    Parameters:
      none

    Returns:
      bool - Returns true on success, false on failure.

    Note:
      If the file is written successfully, the <Tar::close> method is called automatically.

      Also, if you want to have the tarball gzipped or in the UStar format, check out
      the <Tar::set_gzip> and <Tar::set_ustar> methods.
  */
  public function save()
  {
    if(empty($this->mode) || $this->mode != 'w')
      return false;

    fseek($this->fp, 0);

    if(count($this->files) > 0)
    {
      # Used later :P
      $format = array(
                  'mode' => array(6, ' '. chr(0)),
                  'uid' => array(6, ' '. chr(0)),
                  'gid' => array(6, ' '. chr(0)),
                  'size' => array(11, ' '),
                  'mtime' => array(11, ' '),
                );

      foreach($this->files as $filename => $file)
      {
        # Some special stuff needs to be done to certain things ;)
        foreach($format as $key => $f)
        {
          $file['stat'][$key] = str_pad(decoct($file['stat'][$key]), $f[0], ' ', STR_PAD_LEFT). $f[1];
        }

        # Make the generic header...
        $header = str_pad($filename, 100, chr(0)). $file['stat']['mode']. $file['stat']['uid']. $file['stat']['gid']. $file['stat']['size']. $file['stat']['mtime']. '        '. (!isset($file['data']) && !isset($file['name']) ? 5 : 0). str_repeat(chr(0), 100);

        # Calculate the headers checksum by converting it to their decimal value...
        $checksum = 0;
        for($i = 0; $i < 257; $i++)
          $checksum += ord($header[$i]);
        $checksum = decoct($checksum);

        # Make it again, but with extra padding ;)
        $header = str_pad($filename, 100, chr(0)). $file['stat']['mode']. $file['stat']['uid']. $file['stat']['gid']. $file['stat']['size']. $file['stat']['mtime']. str_pad($checksum, 6, ' ', STR_PAD_LEFT). ' '. chr(0). (!isset($file['data']) && !isset($file['name']) ? 5 : 0). str_repeat(chr(0), 355);

        # Write the header to the file now.
        fwrite($this->fp, $header);

        # Now for the file data...
        $data = isset($file['data']) ? $file['data'] : (isset($file['name']) ? file_get_contents($file['name']) : '');

        # We may need to append NUL bytes in order to make it take up multiples of 512 bytes.
        $length = octdec($file['stat']['size']);
        if($length > 0 && ($length / (double)512) != 0)
          $data .= str_repeat(chr(0), 512 - ($length % 512));

        # And the data!
        fwrite($this->fp, $data);
      }
    }

    # The end of the tar contains at least 2 512 byte blocks of NUL's... Weird.
    fwrite($this->fp, str_repeat(chr(0), 1024));

    # Did you want this to be gzipped..?
    if($this->is_gzipped())
    {
      # Save the file name, close everything, then get the files contents.
      $filename = $this->filename;
      $this->close();
      $file = file_get_contents($filename);

      # Open up the file, in writing mode, of course!
      $fp = fopen($filename, 'wb');

      # MINE!
      flock($fp, LOCK_EX);

      # Just one second! If you already added a .gz to the end of the file, let's just remove it
      # for the sake of the "original" name ;)
      if(substr($filename, -3, 3) == '.gz')
        $filename = substr($filename, 0, strlen($filename) - 3);

      # Now write it :P
      fwrite($fp, chr(31). chr(139). chr(8). chr(8). pack('V', filemtime($filename)). chr(0). chr(0). basename($filename). chr(0). gzdeflate($file, 9). pack('VV', crc32($file), strlen($file)));
      fclose($fp);
    }

    $this->close();
    return true;
  }

  /*
    Method: close

    Closes all opened files and sets all attributes to null.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this method.

    Note:
      This method is automatically called in the objects destructor.
  */
  public function close()
  {
    if(!empty($this->mode))
    {
      @fclose($this->fp);

      $this->filename = null;
      $this->filemtime = null;
      $this->fp = null;
      $this->mode = null;
      $this->files = null;
      $this->is_gzipped = null;
      $this->is_ustar = null;
    }
  }

  /*
    Method: filename

    Parameters:
      none

    Returns:
      string - Returns the current file which has been opened with <Tar::open>.
  */
  public function filename()
  {
    return $this->filename;
  }

  /*
    Method: mode

    Parameters:
      none

    Returns:
      string - Returns the current mode, r for read, w for write, null for nothing.
  */
  public function mode()
  {
    return $this->mode;
  }

  /*
    Method: is_gzipped

    Parameters:
      none

    Returns:
      bool - Returns true if the current tarball is gzipped, false if not.

    Note:
      The file does not need to be opened in read only in order for this to return true,
      if you set the file to be gzipped when creating a tarball, this will return true
      as well.
  */
  public function is_gzipped()
  {
    return $this->is_gzipped;
  }

  /*
    Method: is_ustar

    Parameters:
      none

    Returns:
      bool - Returns true if the current tarball is in the UStar format, false if not.
  */
  public function is_ustar()
  {
    return $this->is_ustar;
  }

  /*
    Destructor: __destruct
  */
  public function __destruct()
  {
    $this->close();
  }
}
?>