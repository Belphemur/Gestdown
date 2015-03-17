<?php
class Ftp
{
	private $host,$user,$pwd,$conn,$connected,$debug;
	//Start the ftp connection
	function __construct($Fuser, $Fpass, $Fhost,$Fdebug=false)
	{
		$this->user	= $Fuser;
		$this->pwd	= $Fpass;
		$this->host	= $Fhost;
		$this->debug= $Fdebug;
		
		$this->conn=ftp_connect($this->host);
		$this->connected = ftp_login($this->conn, $this->user, $this->pwd);
	}
	//If the connection success
	private function isConnected()
	{
		if ((! $this->conn)|| (! $this->connected)) 
		{
			throw new Exception("Echec de la Tentative de connexion à {$this->host} avec {$this->user}");
			return false;
		}
		else
		{
			if($this->debug)
				echo "Connection avec succès à {$this->host} avec {$this->user}\n";
			return true;
		}
	}
	function upload($source,$remote,$mode=FTP_BINARY)
	{
		if($this->isConnected())
		{
			if(!ftp_put($this->conn, $remote, $source, $mode))
			{
				throw new Exception("L'upload Ftp a échoué!");
				return false;
			}
			else
			{
				if($this->debug)
					echo "L'upload de $source sur $remote à été effectué avec succès\n";
				return true;
			}
		}
		
	}
	//Stop ftp connection
	function __destruct() 
	{
   		ftp_quit($this->conn);
    }
}

?>