<?php
/* Version 0.3 */
/*
    Forked from https://github.com/Yappli/browser-regex-detector/blob/master/script.php
    
    
 */
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', TRUE);

$start = microtime(true);

if(isset($_POST['u_agent']))
    $userAgent = $_POST['u_agent']; // For test purpose only ! Delete-it for production
else
    $userAgent = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'';   

//for testing use from http://www.useragentstring.com/
//$userAgent = "Opera/12.02 (Android 4.1; Linux; Opera Mobi/ADR-1111101157; U; en-US) Presto/2.9.201 Version/12.02";


// initalize vars
$bname = 'Unknown';
$platform = 'Unknown';
$version= $ub = "";

//First get the platform
$platform = GetOS($userAgent);

$browser  = GetBrowser($userAgent);

// check if we have a number
if ($version==null || $version=="") {
    $version="?";
}

$info = array(
    'name'      => $browser,
    'platform'  => $platform,
    'userAgent' => $userAgent
    
);

// Displaying for test. Of course delete-it for production
header("Content-Type: text/plain");
echo "Execution time = ".((microtime(true)-$start)*1000)." ms\n\n";
print_r($info);

/*                            -- end --                                  */
    /**
     * Get Operating System information from the user agent data
     * @param string $useragent user agent string from the client browser
     */
    function GetOS($useragent) 
    { 

        $osPlatform    =   "Unknown OS Platform";

        $aryOS       =   array(
                                '/windows nt 6.2/i'     =>  'Windows 8',
                                '/windows nt 6.1/i'     =>  'Windows 7',
                                '/windows nt 6.0/i'     =>  'Windows Vista',
                                '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                                '/windows nt 5.1/i'     =>  'Windows XP',
                                '/windows xp/i'         =>  'Windows XP',
                                '/windows nt 5.0/i'     =>  'Windows 2000',
                                '/windows me/i'         =>  'Windows ME',
                                '/win98/i'              =>  'Windows 98',
                                '/win95/i'              =>  'Windows 95',
                                '/win16/i'              =>  'Windows 3.11',
                                '/macintosh|mac os x/i' =>  'Mac OS X',
                                '/mac_powerpc/i'        =>  'Mac OS 9',
                                '/linux/i'              =>  'Linux',
                                '/ubuntu/i'             =>  'Ubuntu',
                                '/iphone/i'             =>  'iPhone',
                                '/ipod/i'               =>  'iPod',
                                '/ipad/i'               =>  'iPad',
                                '/android/i'            =>  'Android',
                                '/blackberry/i'         =>  'BlackBerry',
                                '/webos/i'              =>  'Mobile'
                            );

        foreach ($aryOS as $regex => $value) { 

            if (preg_match($regex, $useragent)) {
                $osPlatform    =   $value;
            }

        }

        return $osPlatform;

    }
    
    /**
     * Get Browser information from the user agent.
     * reference: https://github.com/Yappli/browser-regex-detector/blob/master/script.php
     * @param string $useragent user agent string from the client browser
     */
    function GetBrowser($userAgent)
    {
        // Next get the name of the useragent yes seperately and for good reason
        if (preg_match('/Trident/i', $userAgent) && !preg_match('/Opera/i', $userAgent)) {
            if (preg_match('/MSIE/i', $userAgent)) {
                if (preg_match('/chromeframe/i', $userAgent)) {
                    $bname = 'IE with Chrome Frame';
                    $ub = "chromeframe";
                } else {
                    $bname = 'Internet Explorer';
                    $ub = "MSIE";
                }
            } else {
                $bname = 'Internet Explorer';
                // no $ub because we use another pattern
            }
        } else {
        $aryBrowser  =   array(
                                '/firefox/i'    =>  'Firefox',
                                '/safari/i'     =>  'Safari',
                                '/chrome/i'     =>  'Chrome',
                                '/opera/i'      =>  'Opera',
                                '/netscape/i'   =>  'Netscape',
                                '/maxthon/i'    =>  'Maxthon',
                                '/konqueror/i'  =>  'Konqueror',
                                '/mobile/i'     =>  'Handheld Browser',
                                '/iphone/i'     =>  'iPhone',
                                '/bot/i'        =>  'Bot',
                                '/crawler/i'    =>  'Crawler',
                                '/pinterest/i'  =>  'Bot',
                            );

                foreach ($aryBrowser as $regex => $value) { 

                    if (preg_match($regex, $userAgent)) {
                        $bname    =   $value;
                        $ub = $value;
                    }
                }
        }
        // finally get the correct version number
        // Only for IE > 10 (not a cosmetic code !)
        if (preg_match('/Trident/i', $userAgent) && !preg_match('/MSIE/i', $userAgent)) {
            $pattern = '/Trident\/.*rv:([0-9]{1,}[\.0-9.]{0,})/';
            if(preg_match($pattern, $userAgent, $matches) AND isset($matches[1]))
                $version = $matches[1];
        } else {
            // for others (It can be nice to combine this nice code with the code below !)
            $known = array('Version', $ub, 'other');
            $pattern = '#(?<browser>' . join('|', $known) .
            ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
            if (preg_match_all($pattern, $userAgent, $matches)) {
                // see how many we have
                $i = count($matches['browser']); // we have matching
                if ($i != 1) {
                    //we will have two since we are not using 'other' argument yet
                    //see if version is before or after the name
                    if (strripos($userAgent, "Version") < strripos($userAgent, $ub)) {
                        $version= $matches['version'][0];
                    } else {
                        $version= isset($matches['version'][1])?$matches['version'][1]:'';
                    }
                } else {
                    $version= $matches['version'][0];
                }
            }
        }

        // check if we have a number
        // if ($version==null || $version=="") {
        //     $version="?";
        // }

        return $bname . ' ' . $version;
    }

