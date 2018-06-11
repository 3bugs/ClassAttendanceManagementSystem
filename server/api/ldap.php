<?php
define('ERROR_CODE_SUCCESS', 0);

define('KEY_ERROR_CODE', 'error_code');
define('KEY_ERROR_MESSAGE', 'error_message');
define('KEY_ERROR_MESSAGE_MORE', 'error_message_more');
define('KEY_USER', 'user');

define('LDAP_HOST', 'sdu-ldap.dusit.ac.th');
define('LDAP_PORT', '389');
define('LDAP_BASE_DN', 'dc=dusit,dc=ac,dc=th');

error_reporting(E_ERROR | E_PARSE);
header('Content-type: application/json; charset=utf-8');

header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');

header('Access-Control-Allow-Origin: *');

$response = array();

$username = $_POST['username'];
$password = $_POST['password'];

if (!isset($username) || !isset($password)) {
    $response[KEY_ERROR_CODE] = 1;
    $response[KEY_ERROR_MESSAGE] = 'Required parameter(s) missing!';
    $response[KEY_ERROR_MESSAGE_MORE] = '';

    echo json_encode($response);
    exit();
}

$conn = ldap_connect(LDAP_HOST, LDAP_PORT);
if ($conn) {
    $searchResult = @ldap_search($conn, LDAP_BASE_DN, 'uid=' . $username);

    if ($searchResult) {
        $result = @ldap_get_entries($conn, $searchResult);

        if (isset($result[0])) {
            if (@ldap_bind($conn, $result[0]['dn'], $password)) {
                //$uid = getUid($result[0]['dn']);                    // UID นักศึกษา/พนักงาน
                //$facultyCode = $result[0]['facultycode'][0];    // รหัสคณะ
                // print_r($result[0]); // แสดง Array ค่าทั้งหมดของพนักงาน/นักศึกษาคนนั้น

                $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
                $response[KEY_ERROR_MESSAGE] = '';
                $response[KEY_ERROR_MESSAGE_MORE] = '';

                $user = array();
                $user['id'] = 0;
                $user['username'] = $result[0]['uid'][0];
                //$user['id_code'] = $result[0]['idcode'][0];
                //$user['first_name_th'] = $result[0]['thcn'][0];
                //$user['last_name_th'] = $result[0]['thsn'][0];
                //$user['first_name_en'] = $result[0]['givenname'][0];
                //$user['last_name_en'] = $result[0]['sn'][0];
                $user['display_name'] = $result[0]['displayname'][0];
                //$user['display_name_en'] = $result[0]['cn'][0];
                //$user['email'] = $result[0]['mail'][0];
                //$user['result[0]'] = $result[0];

                $response[KEY_USER] = $user;

                session_start();
                $_SESSION['username'] = $user['username'];
                $_SESSION['display_name'] = $user['display_name'];
            } else {
                $response[KEY_ERROR_CODE] = 5;
                $response[KEY_ERROR_MESSAGE] = 'ชื่อผู้ใช้หรือรหัสผ่าน ไม่ถูกต้อง';
                $response[KEY_ERROR_MESSAGE_MORE] = '';
            }
        } else {
            $response[KEY_ERROR_CODE] = 4;
            $response[KEY_ERROR_MESSAGE] = 'ไม่มีชื่อผู้ใช้นี้ในระบบ';
            $response[KEY_ERROR_MESSAGE_MORE] = '';
        }
    } else {
        $response[KEY_ERROR_CODE] = 3;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเชื่อมต่อ LDAP (ldap_search)';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    }
} else {
    $response[KEY_ERROR_CODE] = 2;
    $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเชื่อมต่อ LDAP (ldap_connect)';
    $response[KEY_ERROR_MESSAGE_MORE] = '';
}

echo json_encode($response);
exit();

function getUid($dn)
{
    $startUidPosition = strpos($dn, 'uid=') + 4;
    $commaPosition = strpos($dn, ',', $startUidPosition);
    return substr($dn, $startUidPosition, $commaPosition - $startUidPosition);
}
?>
