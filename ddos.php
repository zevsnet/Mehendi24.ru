<?
	
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	$APPLICATION->SetTitle("Попытка взлома");
?>
<style>h1,.breadcrumbs{display:none;}</style>
<table class="page_not_found" width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="description">
			<div class="title404">Попытка взлома с IP: <?$ip = get_client_ip();
			if(find_ip($ip)){
				//$filename = $_SERVER["DOCUMENT_ROOT"] . "/upload/blacklist.txt";
				$filename = $_SERVER["DOCUMENT_ROOT"] . "/.htaccess";
				file_put_contents($filename, 'Deny from '.$ip . "\r\n",FILE_APPEND); 
			}
			echo $ip;
			?></div>
			<a class="button big_btn" href="<?=SITE_DIR?>"><span>Перейти на главную</span></a>
			<div class="back404">или <a onclick="history.back()">вернуться назад</a></div>
		</td>
	</tr>
	<tr>
		<td class="description">
		<span>
		Статья 272<br>
пункт 1<br>
Неправомерный доступ к охраняемой законом компьютерной информации.
В электронно вычислительной машине (ЭВМ), системе ЭВМ или их сети, 
если это деяние повлекло уничтожение, блокирование, модификацию 
либо копирование информации, нарушение работы ЭВМ, системы ЭВМ 
или их сети, наказывается штрафом в размере до двухсот тысяч рублей 
или в размере заработной платы или иного дохода осужденного за 
период до восемнадцати месяцев, либо исправительными работами на 
срок от шести месяцев до одного года, либо лишением свободы на срок 
до двух лет.<br>
<br>
пункт 2<br>
То же деяние, совершенное группой лиц по предварительному сговору 
или организованной группой либо лицом с использованием своего 
служебного положения, а равно имеющим доступ к ЭВМ, системе ЭВМ 
или их сети, наказывается штрафом в размере от ста тысяч до трехсот 
тысяч рублей или в размере заработной платы или иного дохода 
осужденного за период от одного года до двух лет, либо 
исправительными работами на срок от одного года до двух лет, 
либо арестом на срок от трех до шести месяцев, либо лишением 
свободы на срок до пяти лет.<br>
<br>
Статья 138<br>
пункт 1<br>
Нарушение тайны переписки - это если вы взломали и посмотрели 
сообщения, наказывается штрафом в размере до восьмидесяти тысяч 
рублей или в размере заработной платы или иного дохода осужденного 
за период до шести месяцев, либо обязательными работами на срок от 
ста двадцати до ста восьмидесяти часов, либо исправительными 
работами на срок до одного года.<br>
<br>
пункт 2<br>
Нарушение тайны переписки с помощью служебного положения, т.е. 
на рабочем месте или с помощью специальных технических средств 
(проги, брут) наказывается штрафом в размере от ста тысяч до трехсот 
тысяч рублей или в размере заработной платы или иного дохода 
осужденного за период от одного года до двух лет, либо лишением 
права занимать определенные должности или заниматься определенной 
деятельностью на срок от двух до пяти лет, либо обязательными 
работами на срок от ста восьмидесяти до двухсот сорока часов, 
либо арестом на срок от двух до четырех месяцев.<br>
<br>
пункт 3<br>
Неправомерный доступ к компьютерной информации - сам взлом.
наказывается штрафом в размере от двухсот до пятисот минимальных 
размеров оплаты труда или в размере заработной платы или иного 
дохода осужденного за период от двух до пяти месяцев, либо 
исправительными работами на срок от шести месяцев до одного 
года, либо лишением свободы на срок до двух лет.<br>
<br>
пункт 4<br>
Тоже самое но группа лиц - т.е. Вы и еще ваш друг, друзья вам 
помогали. Наказывается штрафом в размере от пятисот до восьмисот 
минимальных размеров оплаты труда или в размере заработной платы 
или иного дохода осужденного за период от пяти до восьми месяцев, 
либо исправительными работами на срок от одного года до двух лет, 
либо арестом на срок от трех до шести месяцев, либо лишением 
свободы на срок до пяти лет. 
</span>
		</td>
	</tr>
</table>
<?
function openFileCSV($url_file)
{
    $arRes = array();
    $fille = fopen($url_file, "r");
    while(($data = fgetcsv($fille, 1000, ";")) !== false)
    {
        $arRes[] = $data;
    }
    fclose($fille);

    return $arRes;
}
function find_ip($ip)
{
    $filename = $_SERVER["DOCUMENT_ROOT"] . "/upload/detect_ddos.txt";
    if(file_exists($filename))
    {
        $file = openFileCSV($filename);
        foreach($file as $f_ip)
        {
            if($f_ip[0] == $ip)
            {
                return false;
            }
        }

        return true;
    }
    else
    {
        return true;
    }
}

function get_client_ip()
{
    $ipaddress = '';
    if(getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else$ipaddress = 'UNKNOWN';

    return $ipaddress;
}
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>