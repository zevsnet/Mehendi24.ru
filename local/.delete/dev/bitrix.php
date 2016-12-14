<?
@set_time_limit(0);
@ignore_user_abort(true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
/*
 * Загружаем фотографии с  сайта kupiudachno.ru
 */

$query_Search = "http://bitrix.cmsmagazine.ru/works/?sk=tcy&so=desc&st=3&bt=&server=&cpp=1000&pn=7";



require_once('parser/phpQuery/phpQuery.php');

$arLINK = getArrLink_WEb($query_Search);
file_put_contents('link.csv',$arLINK,FILE_APPEND);

/*
 *Находим ссылку на товар
 */
function getArrLink_WEb($url)
{
    $offSite = file_get_contents($url);
    phpQuery::newDocument($offSite);
    $exportItem = array();
    foreach(pq('.gray_bg a') as $a)
    {
        $domain = pq($a)->attr('_href');

        if(filter_var($domain, FILTER_VALIDATE_URL))
        {
            $pos_ = stripos($domain, '/');
            if($pos_ != strlen($domain)+1){
                $exportItem[] = $domain . '/restore.php'."\r\n";
            }
            else
            {
                $exportItem[] = $domain . 'restore.php'."\r\n";
            }

        }
        /*
         else{
            $domain = pq($a)->attr('href');
            if(filter_var($domain, FILTER_VALIDATE_URL))
            {
                $pos_ = stripos($domain, '/');
                if($pos_ != strlen($domain)+1){
                    $exportItem[] = $domain . '/restore.php'."\r\n";
                }
                else
                {
                    $exportItem[] = $domain . 'restore.php'."\r\n";
                }

            }
        }
         */
    }

    return $exportItem;
}
?>