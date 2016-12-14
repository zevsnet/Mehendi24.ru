<?


require_once('phpQuery/phpQuery.php');

class restore
{
    public $arLink = array();
    public $DIR_DOMAIN = 'http://www.1c-bitrix.ru';
    public $LINK_SITE = "http://www.1c-bitrix.ru/products/cms/projects/index.php?arFilter1_pf%5BTYPE%5D%5B%5D=&arFilter1_pf%5BCLIENT_FIELD%5D=3223&arFilter1_pf%5Bedition%5D=&set_filter=%CF%EE%EA%E0%E7%E0%F2%FC&set_filter=Y&PAGEN_1=";
    //public $LINK_SITE = "http://www.1c-bitrix.ru/products/cms/projects/?PAGEN_1=";
    public $max_page = 20;
    //public $max_page = 235;


    public function __construct()
    {
        $this->find_url();
    }

    public function find_url()
    {
        $page =$this->max_page - 5;
        while ($page < $this->max_page) {

            $offSite = file_get_contents($this->LINK_SITE . $page);
            phpQuery::newDocumentHTML($offSite);
            foreach (pq('.list_project_card a.list_project_name') as $a) {
                $detail_page = $this->DIR_DOMAIN . pq($a)->attr('href');
                if (filter_var($detail_page, FILTER_VALIDATE_URL)) {
                    $url = $this->getUrl($detail_page);
                    file_put_contents('list_url.txt',$url."\r\n",FILE_APPEND);
                    $status = $this->status_restore($url);
                    if ($status['STATUS'])
                    {
                        file_put_contents('list_restore.txt',$url."\r\n",FILE_APPEND);
                        $this->d($url);
                    }
                        //$this->arLink[] = $url;
                }
            }
            $page++;
        }
        //$this->d($this->arLink);

    }

    public function getUrl($url)
    {
        $offDetailPage = file_get_contents($url);
        phpQuery::newDocumentHTML($offDetailPage);
        $i = 1;
        foreach (pq('.news-detail a') as $item) {

            if ($i == 2) {
                $url_link = 'http://' . pq($item)->html();
                $url_link = $this->add_append($url_link);
                return $url_link;
            }
            $i++;
        }
    }

    public function add_append($url)
    {
        $pos_ = stripos($url, '/');

        if ($pos_ != strlen($url) - 1) {
            $url = $url . '/restore.php';
        } else {
            $url = $url . 'restore.php';
        }
        return $url;
    }

    public function status_restore($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $offSite = file_get_contents($url);
            phpQuery::newDocumentHTML($offSite);
            foreach (pq('form#restore') as $restore) {
                return array('URL' => $url, 'STATUS' => true);
            }
        }
        return array('URL' => $url, 'STATUS' => false);
    }

    public function d()
    {
        return $this->dump(func_get_args());
    }

    public function dd()
    {
        return $this->dump(func_get_args());
        die();
    }

    public function dump($aArgs)
    {
        print '<pre class=\'ls-dump prettyprint\' style="margin:5px;padding:5px;border:1px #dd0000 solid; background-color: #fff; text-align: left;">' . PHP_EOL;

        print_r($aArgs);

        print '</pre>';
    }
}

?>