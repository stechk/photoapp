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

    public function GetCislaOPPart($partOfOP, $partOfPartner, $countOfReturned = 50)
    {
        $method = "GetCislaOPPart";
//
        $xml_post_string = '<?xml version="1.0" encoding="utf-8" ?>
            <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
              <soap:Header>
                <F2SoapHeader xmlns="http://www.albixon.cz">
                  <login>' . $this->login . '</login>
                  <psw>' . $this->pass . '</psw>
                  <nationalCentralId></nationalCentralId>
                </F2SoapHeader>
              </soap:Header>
              <soap:Body>
                <GetCislaOPPart xmlns="http://www.albixon.cz">
                  <castCisla>' . $partOfOP . '</castCisla>
                  <castPartner>' . $partOfPartner . '</castPartner>
                  <maxPocet>' . $countOfReturned . '</maxPocet>
                </GetCislaOPPart>
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
                        "partner" => $o->getElementsByTagName("partner")[0]->nodeValue,
                        "mistoPlneni" => $o->getElementsByTagName("mistoPlneni")[0]->nodeValue,
                        "kategorie" => $o->getElementsByTagName("kategorie")[0]->nodeValue,

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
            "POST /ErpDataWs/erpdata.asmx HTTP/1.1",
            "Host: 172.20.2.44",
            "Content-Type: text/xml; charset=utf-8",
            "Content-Length: " . strlen($xml_post_string),
            'SOAPAction: "http://www.albixon.cz/' . $method . '"',
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