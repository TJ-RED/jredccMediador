<?php 

	function getStatusCodeMessage($status)
	{
		// these could be stored in a .ini file and loaded
		// via parse_ini_file()... however, this will suffice
		// for an example
		$codes = Array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => '(Unused)',
			307 => 'Temporary Redirect',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported'
		);
	 
		return (isset($codes[$status])) ? $codes[$status] : '';
	}
	 
	// Helper method to send a HTTP response code/message
	function sendResponse($status = 200, $body = '', $content_type = 'text/html')
	{
		$status_header = 'HTTP/1.1 ' . $status . ' ' . getStatusCodeMessage($status);
		header($status_header);
		header('Content-type: ' . $content_type);
		echo $body;
	}

?>
<?php

	
	try
	{
		session_start();
		
		$Conn = mysql_connect("jredc.cvhnmdnzo4hd.us-east-1.rds.amazonaws.com","root", "masterola");
		mysql_select_db("jreddb", $Conn);
	}
	catch(Exception $e)
	{
		die ("Error... " . $e);
	}
	
?>
<?php
	try
	{
		if (isset ($_POST["user"]) && isset ($_POST["pwd"]))
		{
			$Usuario = $_POST["user"];
			$PassWord = $_POST["pwd"];
			
			$sql = "Select count(1) as ok, c.dnsCliente  
                    from empresa e, cliente c 
                    where e.idempresa = 1 
                    and   c.idempresa = e.idempresa 
                    and   c.usuarioCliente = '".$Usuario."' 
                    and   c.passwordCliente = AES_ENCRYPT('".$PassWord."', md5(e.llave))";
			
			
			$res = mysql_query($sql, $Conn);
			$ok = 0;
            $Dns = "";
            $NumDisp = 0;
			
			if ($row = mysql_fetch_assoc($res))
			{
				$ok = $row["ok"];
                $Dns = $row["dnsCliente"];
			}
            
            mysql_free_result($res);
            
            mysql_close($Conn);
            
           /* if ($ok == 1)
            {
                $Conn = mysql_connect($Dns.":5792","root", "masterola");
                if (!$Conn)
                {
                    sendResponse(400, "DNS Desconectado...");
                    die("DNS Fuera de servicio");
                }
                else
                {
                    mysql_select_db("localjred", $Conn);
                    
                    $sql = "Select count(1) CantDisp from ListaEstados;";
                    
                    $res = mysql_query($sql, $Conn);
                    
                    while ($row = mysql_fetch_assoc($res))
                    {
                        $NumDisp = $row["CantDisp"];
                    }
                                        
                    mysql_free_result($res);
                }
            }*/
            
            $resultR = array
			        (
				        "inicioExito" => $ok."",
				        "numSwitch" => $NumDisp."",
                        "DNS" => $Dns."",
                        "usr" => $Usuario.""
			        );
                    
            sendResponse(200, json_encode($resultR));			
		}
        
       /* if (isset($_POST["idPin"]) && isset($_POST["action"]))
        {
        }*/
	}
	catch(Exception $e)
	{
		 sendResponse(400, "error no acion");
		die ("Error... " . $e);
	}
?>