<?php

namespace Net;

class CurlMulti {
	
	private $mh;
	private $curls;
	
	public function __construct(){
		
		$this->mh = curl_multi_init();
	}
	

    protected $headers = [
        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        //'Accept-Encoding' => 'gzip, deflate',
        'Accept-Language' => 'pl,en-US;q=0.7,en;q=0.3',
        'Connection' => 'keep-alive',
        'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:35.0) Gecko/20100101 Firefox/35.0'
    ];

    public function setHeaders(array $headers) {
        $this->headers = $headers;
    }
    public function getHeaders() {
        $this->headers = $headers;
    }
    private function createHeaders($dataHeaders) {
        $dataHeaders = array_merge($this->headers, $dataHeaders);
        $headers = [];
        foreach ($dataHeaders as $k => $v) {
            $headers[] = $k . ': ' . $v;
        }
        return $headers;
    }

    function prepare(array $data) {

        $data['headers']['Host'] = parse_url($data['url'], PHP_URL_HOST);
        $data['headers'] = $this->createHeaders($data['headers']);

        $curl = curl_init();
        //curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookies.txt');
        curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookies.txt');
        curl_setopt($curl, CURLOPT_URL, $data['url']);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_CAINFO, dirname(__FILE__) . "/cacert.pem");
        if (!empty($data['headers'])) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $data['headers']);
        }
        if (!empty($data['referer'])) {
            curl_setopt($curl, CURLOPT_REFERER, $data['referer']);
        }
        if (array_key_exists('post', $data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data['post']);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        
        
        curl_multi_add_handle($this->mh,$curl);
        $this->curls[] = $curl;
    }
	
	public function closeAll(){
		foreach($this->curls as $curl){
			curl_multi_remove_handle($this->mh, $curl);
		}
		curl_multi_close($this->mh);
		
	}
	public function getPage(){
		
		
		$running=null;
			do {
				curl_multi_exec($this->mh, $running);
				curl_multi_select($this->mh);
			} while ($running > 0);
			
		foreach($this->curls as $curl){
			var_dump(curl_getinfo($curl, CURLINFO_HTTP_CODE));
			if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == 200) {
				var_dump(curl_multi_getcontent($curl));  // get results
			}else{
				var_dump('errorrrr');exit;
			}

		}
	}
}


$c = new CurlMulti();

$data=[
	'url' =>'http://newzend.com'
];
for($i= 0; $i<=5;$i++){
	$c->prepare($data);
}
$c->getPage();
$c->closeAll();





