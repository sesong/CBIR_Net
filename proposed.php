<?php
   	include_once "simple_html_dom.php";
	
	   class HTMLParser{
		
		private $url;
		private $refer;
		private $patterns;
		private $buffer;
		private $http_code;
		private $cookie_file;
		
		/**
		* @bref 생성자
		**/
		public function __construct(){
			$this->patterns = array();
			$this->buffer = '';
			$this->http_code = 0;
			$this->cookie_file = 'cookie.txt';
		}
	
		/**
		* @bref url 세팅
		* @param string URL
		**/    
		public function setUrl($url){
			$this->url = $url;
		}
		
		/**
		* @bref 2014.01.28 추가 - REFERER 세팅 : refer없으면 내용이 안나오는 사이트가 있음
		* @param string URL
		**/
		public function setRefer($refer){
			$this->refer = $refer;
		}
	
		/**
		* @bref 패턴과 파싱결과의 row, col 세팅
		* @param string 패턴
		**/    
		public function addPattern($pattern){
			$this->patterns[] = $pattern;
		}
		
		/**
		* @bref 쿠키파일을 지정한다
		* @param string
		**/
		public function setCookieFile($filepath){
			$this->cookie_file = $filepath;
		}
	
		/**
		* @bref 지정한 url의 컨텐츠를 불러들인다
		**/    
		private function loadContent(){
			
			//$agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)'; 
			$agent = 'Mozilla/5.0 (Windows NT 6.1; rv:26.0) Gecko/20100101 Firefox/26.0';
			$curlsession = curl_init(); 
			curl_setopt ($curlsession, CURLOPT_URL,            $this->url); 
			curl_setopt ($curlsession, CURLOPT_HEADER,          1); 
			
			//http 응답코드가 302일때 redirect_url 로 따라감
			curl_setopt ($curlsession, CURLOPT_FOLLOWLOCATION, TRUE);
			curl_setopt ($curlsession, CURLOPT_RETURNTRANSFER,  true); 
			
			curl_setopt ($curlsession, CURLOPT_POST,            0); 
			curl_setopt ($curlsession, CURLOPT_USERAGENT,      $agent); 
			curl_setopt ($curlsession, CURLOPT_REFERER,        $this->refer); 
			curl_setopt ($curlsession, CURLOPT_TIMEOUT,        3); 
			curl_setopt ($curlsession, CURLOPT_COOKIEJAR, $this->cookie_file);
			curl_setopt ($curlsession, CURLOPT_COOKIEFILE, $this->cookie_file);
			
			$this->buffer = curl_exec ($curlsession); 
			$cinfo = curl_getinfo($curlsession);
			
			$this->http_code = $cinfo['http_code'];
			curl_close($curlsession); 
			
			if ($this->http_code != 200) { 
				$this->buffer = '';
			}
		}
		
		/**
		* @bref 결과를 리턴한다
		* @return array 모든 결과가 담긴 배열
		**/
		public function getResult(){
			
			$result = array();
			
			$this->loadContent();
			
			foreach($this->patterns as $item){
				$result[] = $this->getParseResult($item);
			}
			
			return $result;
		}
		
		/**
		* @bref 파싱
		* @param string 패턴
		* @return array 하나의 정규식에 대한 파싱 결과가 담긴 배열
		**/
		private function getParseResult($pattern){
			$result = array();
			preg_match_all($pattern, $this->buffer, $matches);
			
			//첫번째 요소는 날린다
			if(count($matches) > 0)    array_splice($matches, 0, 1);
			
			return $matches;
		}
	}
	
	   class Foo
	{
		public function getDom($url, $post = false)
    	{
			$header = array(
				"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
				"Accept-Language: en-us,en;q=0.5",
				"Connection: keep-alive",
				"Cache-Control: no-cache",
				"Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
				"Pragma: no-cache",
			);
		
			$curlOptions = array(
				CURLOPT_ENCODING => 'gzip,deflate',
				CURLOPT_AUTOREFERER => 1,
				CURLOPT_CONNECTTIMEOUT => 600, // timeout on connect
				CURLOPT_TIMEOUT => 600, // timeout on response
				CURLOPT_URL => $url,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_MAXREDIRS => 9,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_HEADER => 0,
				CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36",
				CURLOPT_VERBOSE => true,
				CURLINFO_HEADER_OUT  => true
			);

         	$curl = curl_init($url);
        	curl_setopt_array($curl, $curlOptions);

		 	curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
         	if($post) { 		 
            	curl_setopt($curl, CURLOPT_POST, true);
            	curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
			}
		 
			$data = curl_exec($curl);
			curl_close($curl);
			$dom = str_get_html($data);
		
			return $dom;
      	}
    
	  
	  
		public function getBestGuess(simple_html_dom $dom)
		{			
			echo $dom->find('a[class=_gUb]', 0)->innertext;
			echo "<br />\n";

			
			return false;
		}
	
		public function getSimilarImage($url)
		{
					
			$dom = $this->getDom($url);
			#$mystring = $dom->find('a[class=bia uh_rl]', 0)->href;
			$mystring = $dom->find('a[class=iu-card-header]', 0)->href;
			$newstring = "https://www.google.com".$mystring;
			$newstring = htmlspecialchars_decode($newstring);

			if($newstring != '')
			{
				$iup = new HTMLParser();
				$iup->setUrl($newstring);
				
				echo $newstring;
				//refer가 필요할 경우
				//$iup->setRefer('http://www.daum.net');
				
				//cookie 파일을 지정한다(변경이 필요한 경우)
				//$iup->setCookieFile('cookie.txt');
			
				//이미지태그의 src를 추출한다
				$iup->addPattern('/<img[^>]*src=["\']?([^>"\']+)["\']?[^>]*>/');
				
				//파싱결과를 돌려줌
				$result = $iup->getResult();

				echo '<pre style="text-align:left">';
    			print_r($result);
    			echo '</pre>';
			
				for($i = 0; $i < 5 ; $i++)
				{
					$img_url = $result[0][0][$i+1];
					$img_dir = 'C:/xampp/htdocs/proposed_similar/similar_'.$i.'.jpg';
					
					copy($img_url, $img_dir);

					$time = time();
					
					sleep(2);
				}

				return 1;
			
			}
			else
				return 0;
					
		}
	
      
	
		
		public function getImageURLSearchDom($url, $randomValue)
		{
			$result = $this->getSimilarImage($url);
			
			$count = 0;
			
			if($result != false)
			{
				
				foreach($result as $link) 
				{

					$ch = curl_init();

					$file = $randomValue.'_'.$count++ . '.jpg';

					$path = 'C:/xampp/htdocs/proposed_similar/';

					$temp = $path . $file;

					file_put_contents($temp, file_get_contents($link[0]));		

					$time = time();
					
					sleep(7);

				}
			}
			
		
		}

	}
	   
	$Foo = new Foo;

	$randomValue = "similar"; 

	$URL = 'https://www.google.com/searchbyimage?site=search&sa=X&image_url=http://210.94.222.115:2424/';
	
	$fileForTargetName = fopen('targetName.txt', 'r');
	$targetNameRandomNumber = fread($fileForTargetName, filesize('targetName.txt'));
	fclose($fileForTargetName);

	$Search_Img_Name = 'target_'.$targetNameRandomNumber.'.jpg';

	$URL = $URL . $Search_Img_Name;


	ini_set('max_execution_time', 300); 
	
	$Foo->getSimilarImage($URL);

?>