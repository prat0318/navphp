<?php

echo("<b>getallheaders()</b><br>\n");
    $headers = getallheaders();
    for(reset($headers); $key = key($headers); next($headers)) {
    echo "\$headers['$key'] = ".$headers[$key]."<br>\n";
    }

echo "<br>\n<b>apache_request_headers()</b><br>\n";
$rheaders = apache_request_headers();
foreach ($rheaders as $header => $value) {
   echo "['$header']: $value <br>\n";
}

echo "<br>\n<b>_SERVER stringarray</b><br>\n";
  $crlf=chr(13).chr(10);
  $br='<br />'.$crlf;
  $p='<br /><br />'.$crlf;
  foreach ($_SERVER as $key => $datum) {
    echo '$_SERVER[\''.$key.'\'] : '.$datum.$br;
  }
  echo $p;

echo "<br>\n<b>no ENV{'HTTP_REFERER'}</b><br>\n";
echo "<br>\n<b>no GLOBALS array</b><br>\n";
  exit;

?>
