<?php
/**
 * Created by PhpStorm.
 * User: pl
 * Date: 03.09.16
 * Time: 10:45
 */

namespace App\Model;


use Nette\Utils\Arrays;
use Nette\Utils\DateTime;
use Nette\Utils\Json;

class SoapService
{

    private $url; //test
    private $login;
    private $pass;

    /**
     * SoapService constructor.
     */
    public function __construct($soapUrl, $soapLogin, $soapPassword)
    {
        $this->url = $soapUrl;
        $this->login = $soapLogin;
        $this->pass = $soapPassword;
        ini_set("soap.wsdl_cache_enabled", "0");

    }

    public function GetCislaOP($partOfOP, $countOfReturned = 50)
    {
        $method = "GetCislaOP";

        $xml_post_string = '<?xml version="1.0" encoding="utf-8" ?>
            <soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:epas="http://www.epass.cz/EPASSr">
               <soap:Header>
                  <epas:F2SoapHeader>
                     <!--Optional:-->
                     <epas:login>' . $this->login . '</epas:login>
                     <!--Optional:-->
                     <epas:psw>' . $this->pass . '</epas:psw>
                     <!--Optional:-->
                     <epas:nationalCentralId></epas:nationalCentralId>
                  </epas:F2SoapHeader>
               </soap:Header>
              <soap:Body>
                <epas:GetCislaOP>
                  <epas:castCisla>' . $partOfOP . '</epas:castCisla>
                  <epas:maxPocet>' . $countOfReturned . '</epas:maxPocet>
                </epas:GetCislaOP>
              </soap:Body>
            </soap:Envelope>';

        $response = $this->callService($xml_post_string, $method);

        //DEBUG
//        $parser = simplexml_load_string($response);
//        $parser->asXML(__DIR__ . "/test-castcisla.xml");

        if (!$response) {
            return [];
        }
        $doc = new \DOMDocument();
        $doc->loadXML($response);

        $ops = [];
        foreach ($doc->getElementsByTagName("cislaOP")[0]->childNodes as $o) {
            $ops[] =
                [
                    "op" => (int)$o->nodeValue,
                ];

        }
        return $ops;
    }

    /**
     * @param $xml_post_string
     * @param $method
     * @return mixed
     */
    private function callService($xml_post_string, $method)
    {
        $headers = array(
            "POST /EPF2WebService.asmx HTTP/1.1",
            "Host: websrv.albixon.cz",
            "Content-Type: text/xml; charset=utf-8",
            "Content-Length: " . strlen($xml_post_string),
            'SOAPAction: "http://www.epass.cz/EPASSr/' . $method . '"',
        );

        // PHP cURL  for https connection with auth
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_URL, $this->url . "?op=" . $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        //debug
        //curl_setopt($ch, CURLOPT_VERBOSE, true);
        //curl_setopt($ch, CURLOPT_STDERR, fopen('php://stderr', 'w'));
        // converting
        $response = curl_exec($ch);
        //$errno = curl_errno($ch);

        curl_close($ch);

        // converting
        $response1 = str_replace("<soap:Body>", "", $response);
        $response2 = str_replace("</soap:Body>", "", $response1);
        return $response2;
    }

}