<?php
namespace ServiceProvider;

class GatewayTransport {

    public static function submit($apiBaseUrl,$apiKey,$host, $t_request,$transactionType) {

        $process = curl_init($host);
        $json = json_encode($t_request);
       
        if (!empty($json))
          Logger::getInstance()->write("with message " .  $json, Logger::DEBUG, get_class());

        curl_setopt($process, CURLOPT_HTTPHEADER, array('Authorization:'.$apiKey,'Accept: application/json', 'Content-type: application/json'));

        if (!empty($t_request)) { 
            curl_setopt($process, CURLOPT_POST, 1);
            curl_setopt($process, CURLOPT_POSTFIELDS, $json);
        }

        curl_setopt($process, CURLOPT_URL, $host);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($process);
        $error = curl_error($process);
        curl_close($process);

        if ($response === false) {
          throw new \Exception("cURL error " . $error);
        }

        Logger::getInstance()->write("Response $response", Logger::DEBUG, get_class() );

        return $response;
    }
}
?>
