<?php

function file_data($path) {
    return file_get_contents($path);
}

function hex_string($num) {
    $tmp_string = dechex($num);
    if (strlen($tmp_string) < 2) {
        $tmp_string = '0' . $tmp_string;
    }
    return $tmp_string;
}

function reverse($num) {
    $tmp_string = dechex($num);
    if (strlen($tmp_string) < 2) {
        $tmp_string = '0' . $tmp_string;
    }
    return hexdec(substr($tmp_string, 1) . substr($tmp_string, 0, 1));
}

function RBIT($num) {
    $result = '';
    $tmp_string = decbin($num);
    while (strlen($tmp_string) < 8) {
        $tmp_string = '0' . $tmp_string;
    }
    for ($i = 0; $i < 8; $i++) {
        $result .= $tmp_string[7 - $i];
    }
    return bindec($result);
}

class XG {
    private $length;
    private $debug;
    private $hex_CE0;

    public function __construct($debug) {
        $this->length = 0x14;
        $this->debug = $debug;
        $this->hex_CE0 = [0x05, 0x00, 0x50, mt_rand(0, 0xFF), 0x47, 0x1e, 0x00, 8 * mt_rand(0, 0x1F)];
    }

    private function addr_BA8() {
        $tmp = '';
        $hex_BA8 = range(0, 0xFF);
        for ($i = 0; $i < 0x100; $i++) {
            if ($i == 0) {
                $A = 0;
            } elseif ($tmp) {
                $A = $tmp;
            } else {
                $A = $hex_BA8[$i - 1];
            }
            $B = $this->hex_CE0[$i % 0x8];
            if ($A == 0x05) {
                if ($i != 1) {
                    if ($tmp != 0x05) {
                        $A = 0;
                    }
                }
            }
            $C = $A + $i + $B;
            while ($C >= 0x100) {
                $C -= 0x100;
            }
            if ($C < $i) {
                $tmp = $C;
            } else {
                $tmp = '';
            }
            $D = $hex_BA8[$C];
            $hex_BA8[$i] = $D;
        }
        return $hex_BA8;
    }

    private function initial($debug, $hex_BA8) {
        $tmp_add = [];
        $tmp_hex = $hex_BA8;
        for ($i = 0; $i < $this->length; $i++) {
            $A = $debug[$i];
            $B = empty($tmp_add) ? 0 : end($tmp_add);
            $C = $hex_BA8[$i + 1] + $B;
            while ($C >= 0x100) {
                $C -= 0x100;
            }
            $tmp_add[] = $C;
            $D = $tmp_hex[$C];
            $tmp_hex[$i + 1] = $D;
            $E = $D + $D;
            while ($E >= 0x100) {
                $E -= 0x100;
            }
            $F = $tmp_hex[$E];
            $G = $A ^ $F;
            $debug[$i] = $G;
        }
        return $debug;
    }

    private function calculate($debug) {
        for ($i = 0; $i < $this->length; $i++) {
            $A = $debug[$i];
            $B = reverse($A);
            $C = $debug[($i + 1) % $this->length];
            $D = $B ^ $C;
            $E = RBIT($D);
            $F = $E ^ $this->length;
            $G = ~$F;
            while ($G < 0) {
                $G += 0x100000000;
            }
            $H = hexdec(substr(dechex($G), -2));
            $debug[$i] = $H;
        }
        return $debug;
    }

    public function main() {
        $result = '';
        foreach ($this->calculate($this->initial($this->debug, $this->addr_BA8())) as $item) {
            $result .= hex_string($item);
        }

        return sprintf('8402%s%s%s%s%s',
            hex_string($this->hex_CE0[7]),
            hex_string($this->hex_CE0[3]),
            hex_string($this->hex_CE0[1]),
            hex_string($this->hex_CE0[6]),
            $result
        );
    }
}

function getxg($param = "", $stub = "", $cookie = "") {
    $gorgon = [];
    $ttime = time();

    $url_md5 = md5($param);
    for ($i = 0; $i < 4; $i++) {
        $gorgon[] = hexdec(substr($url_md5, 2 * $i, 2));
    }

    if ($stub) {
        for ($i = 0; $i < 4; $i++) {
            $gorgon[] = hexdec(substr($stub, 2 * $i, 2));
        }
    } else {
        for ($i = 0; $i < 4; $i++) {
            $gorgon[] = 0x0;
        }
    }

    if ($cookie) {
        $cookie_md5 = md5($cookie);
        for ($i = 0; $i < 4; $i++) {
            $gorgon[] = hexdec(substr($cookie_md5, 2 * $i, 2));
        }
    } else {
        for ($i = 0; $i < 4; $i++) {
            $gorgon[] = 0x0;
        }
    }

    $gorgon = array_merge($gorgon, [0x0, 0x8, 0x10, 0x9]);

    $Khronos = dechex($ttime);
    for ($i = 0; $i < 4; $i++) {
        $gorgon[] = hexdec(substr($Khronos, 2 * $i, 2));
    }

    return [
        'X-Gorgon' => (new XG($gorgon))->main(),
        'X-Khronos' => (string)$ttime
    ];
}

function get_stub($data) {
    if (is_array($data)) {
        $data = json_encode($data);
    }

    if (is_string($data)) {
        $data = utf8_encode($data);
    }

    if (empty($data)) {
        return "00000000000000000000000000000000";
    }

    return strtoupper(md5($data));
}

function get_profile($session_id, $device_id, $iid) {
    try {
        $data = null;
        $param = sprintf("device_id=%s&iid=%s&id=kaa&version_code=34.0.0&language=en&app_name=lite&app_version=34.0.0&carrier_region=SA&tz_offset=10800&mcc_mnc=42001&locale=en&sys_region=SA&aid=473824&screen_width=1284&os_api=18&ac=WIFI&os_version=17.3&app_language=en&tz_name=Asia/Riyadh&carrier_region1=SA&build_number=340002&device_platform=iphone&device_type=iPhone13,4", $device_id, $iid);
        $sig = getxg($param, null, null);
        $url = sprintf("https://api16.tiktokv.com/aweme/v1/user/profile/self/?%s", $param);
        $headers = [
            "content-type: application/x-www-form-urlencoded; charset=UTF-8",
            "Cookie: sessionid=$session_id",
            "sdk-version: 2",
            "user-agent: com.zhiliaoapp.musically/432424234 (Linux; U; Android 5; en; fewfwdw; Build/PI;tt-ok/3.12.13.1)",
            "X-Gorgon: " . $sig["X-Gorgon"],
            "X-Khronos: " . $sig["X-Khronos"]
        ];
        $options = [
            'http' => [
                'header' => implode("\r\n", $headers),
                'method' => 'GET'
            ]
        ];
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $res = json_decode($response, true);
        return $res["user"]["unique_id"];
    } catch (Exception $e) {
        return "None";
    }
}

function check_is_changed($last_username, $session_id, $device_id, $iid) {
    return get_profile($session_id, $device_id, $iid) !== $last_username;
}

function change_username($session_id, $device_id, $iid, $last_username, $new_username) {
    $data = sprintf("unique_id=%s", urlencode($new_username));
    $param = sprintf("device_id=%s&iid=%s&residence=SA&version_name=1.1.0&os_version=17.4.1&app_name=tiktok_snail&locale=en&ac=4G&sys_region=SA&version_code=1.1.0&channel=App%%20Store&op_region=SA&os_api=18&device_brand=iphone&idfv=%s-1ED5-4350-9318-77A1469C0B89&device_platform=iphone&device_type=iPhone13,4&carrier_region1=&tz_name=Asia/Riyadh&account_region=eg&build_number=11005&tz_offset=10800&app_language=en&carrier_region=&current_region=&aid=364225&mcc_mnc=&screen_width=1284&uoo=1&content_language=&language=en&cdid=%s&openudid=%s&app_version=1.1.0&scene_id=830", $device_id, $iid, $iid, $iid, $iid);
    $sig = getxg($param, $data, null);

    $url = sprintf("https://api16.tiktokv.com/aweme/v1/commit/user/?%s", $param);
    $headers = [
        "content-type: application/x-www-form-urlencoded; charset=UTF-8",
        "Cookie: sessionid=$session_id",
        "sdk-version: 2",
        "user-agent: com.zhiliaoapp.musically/$device_id (Linux; U; Android 5; en; $iid; Build/PI;tt-ok/3.12.13.1)",
        "X-Gorgon: " . $sig["X-Gorgon"],
        "X-Khronos: " . $sig["X-Khronos"]
    ];
    $options = [
        'http' => [
            'header' => implode("\r\n", $headers),
            'method' => 'POST',
            'content' => $data
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $result = $response;

    if (strpos($result, "unique_id") !== false && check_is_changed($last_username, $session_id, $device_id, $iid)) {
        return "Username change successful.";
    } else {
        return "Failed to change username: " . $result;
    }
}

// function main() {
//     $device_id = strval(mt_rand(777777788, 999999999999));
//     $iid = strval(mt_rand(777777788, 999999999999));
//     echo "Enter the sessionid: ";
//     $session_id = trim(fgets(STDIN));

//     $user = get_profile($session_id, $device_id, $iid);
//     if ($user !== "None") {
//         echo "Your current TikTok username is: $user\n";
//         echo "Enter the new username you wish to set: ";
//         $new_username = trim(fgets(STDIN));
//         echo change_username($session_id, $device_id, $iid, $user, $new_username);
//     } else {
//         echo "not work sessionid\n";
//     }
// }

// main();
?>
