<?php
namespace Albixon;

/**
 * Class HttpAuth
 * @package Albixon
 * @author Petr Lžičař
 */
class HttpAuth extends \Nette\Object
{

    private $credentials = array(
        'foto' => 'fotoalbixon2016',
    );

    private $allowedRanges = array(
        "172.20/16",
        "127.0.0.1/32",
        "88.146.176.146/32",
        "81.30.237.162/32",
        '172.31.10.0/24',
        '10.242.2.0/24',
    );


    public function __construct()
    {
        //var_dump($_SERVER['PHP_AUTH_USER']);
//        var_dump($_SERVER['PHP_AUTH_PW']);

        if (php_sapi_name() != "cli") {
            if ($this->isIpInAllowedRanges($this->getIpAddress()) !== true) {
                if ((!isset($_SERVER['PHP_AUTH_USER']) || !isset($this->credentials[$_SERVER['PHP_AUTH_USER']]) || $_SERVER['PHP_AUTH_PW'] !== $this->credentials[$_SERVER['PHP_AUTH_USER']])
                    && isset($_GET['albixon'])
                ) {
                    header('WWW-Authenticate: Basic realm="NAHRADA VPN"');
                    header('HTTP/1.1 401 Unauthorized', true, 401);
                    echo '<h1>Authentication failed.</h1>';
                    die();
                } elseif (!isset($_SERVER['PHP_AUTH_USER']) || !isset($this->credentials[$_SERVER['PHP_AUTH_USER']]) || $_SERVER['PHP_AUTH_PW'] != $this->credentials[$_SERVER['PHP_AUTH_USER']]) {
                    header('HTTP/1.1 403 Forbidden', true, 403);
                    echo '<h1>Pro přístup mimo firmu musíte využít VPN.</h1>';
                    echo '<p><strong>Pokud jste oprávněn přistoupit bez VPN klikněte <a href="?albixon">zde</a>.</strong></p>';
                    die;
                }
            }
        }
    }

    /**
     * Get the users's IP address.
     *
     * @return string
     */
    private function getIpAddress()
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim(reset($ips));
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }

    private function isIpInAllowedRanges($ip)
    {
        foreach ($this->allowedRanges as $iprange) {
            if (($this->ip_in_range($ip, $iprange)) === true) {
                return true;
            }
        }
        return false;
    }

// ip_in_range
// This function takes 2 arguments, an IP address and a "range" in several
// different formats.
// Network ranges can be specified as:
// 1. Wildcard format:     1.2.3.*
// 2. CIDR format:         1.2.3/24  OR  1.2.3.4/255.255.255.0
// 3. Start-End IP format: 1.2.3.0-1.2.3.255
// The function will return true if the supplied IP is within the range.
// Note little validation is done on the range inputs - it expects you to
// use one of the above 3 formats.
    private function ip_in_range($ip, $range)
    {
        if (strpos($range, '/') !== false) {
            // $range is in IP/NETMASK format
            list($range, $netmask) = explode('/', $range, 2);
            if (strpos($netmask, '.') !== false) {
                // $netmask is a 255.255.0.0 format
                $netmask = str_replace('*', '0', $netmask);
                $netmask_dec = ip2long($netmask);
                return ((ip2long($ip) & $netmask_dec) == (ip2long($range) & $netmask_dec));
            } else {
                // $netmask is a CIDR size block
                // fix the range argument
                $x = explode('.', $range);
                while (count($x) < 4) $x[] = '0';
                list($a, $b, $c, $d) = $x;
                $range = sprintf("%u.%u.%u.%u", empty($a) ? '0' : $a, empty($b) ? '0' : $b, empty($c) ? '0' : $c, empty($d) ? '0' : $d);
                $range_dec = ip2long($range);
                $ip_dec = ip2long($ip);

                # Strategy 1 - Create the netmask with 'netmask' 1s and then fill it to 32 with 0s
                #$netmask_dec = bindec(str_pad('', $netmask, '1') . str_pad('', 32-$netmask, '0'));

                # Strategy 2 - Use math to create it
                $wildcard_dec = pow(2, (32 - $netmask)) - 1;
                $netmask_dec = ~$wildcard_dec;

                return (($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec));
            }
        } else {
            // range might be 255.255.*.* or 1.2.3.0-1.2.3.255
            if (strpos($range, '*') !== false) { // a.b.*.* format
                // Just convert to A-B format by setting * to 0 for A and 255 for B
                $lower = str_replace('*', '0', $range);
                $upper = str_replace('*', '255', $range);
                $range = "$lower-$upper";
            }

            if (strpos($range, '-') !== false) { // A-B format
                list($lower, $upper) = explode('-', $range, 2);
                $lower_dec = (float)sprintf("%u", ip2long($lower));
                $upper_dec = (float)sprintf("%u", ip2long($upper));
                $ip_dec = (float)sprintf("%u", ip2long($ip));
                return (($ip_dec >= $lower_dec) && ($ip_dec <= $upper_dec));
            }

            echo 'Range argument is not in 1.2.3.4/24 or 1.2.3.4/255.255.255.0 format';
            return false;
        }

    }

}
