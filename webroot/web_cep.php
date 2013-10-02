<?php


/**
 * Proxy to republica_virtual request.  They only accept http request, on facebook it must be https.
 * It is a temporary workarround until we code a the ZIP code database. 
 *
 * @return text
 */	


if (!isset($_GET['cep']))
{
	
	echo "CEP não informado";
	exit;
}
else
{
	$cep = $_GET['cep'];
	
}
		$url = "http://cep.republicavirtual.com.br/web_cep.php?cep=" . $cep . "&formato=javascript";
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
        
        $output = curl_exec($ch); 
        curl_close($ch);

        echo $output;
