<?php
	class Twitter {
		private $user;
		private $password;
		protected $_curl;

		public function setCredentials($user,$password){
			$this->user = $user;
			$this->password = $password;
		}

		public function verifyCredentials($format = "xml"){
			$verified = $this->_call("http://twitter.com/account/verify_credentials.".$format);
			if(!$verified){
				return false;
			}
			return $verified;
		}
		
		public function update($status,$format = "xml",$extra = false){
			$data = array();
			if($extra){
				$fields = array('lat','long','in_reply_to_status_id');
				foreach($fields as $field){
					if(isset($extra[$field])){
						$data[$field] = $extra[$field];
					}
				}
			}
			$data = array_merge($data,array("status" => $status));
			if(!$this->_call("http://twitter.com/statuses/update.".$format,"POST",$data)){
				return false;
			}
			return true;
		}
		
		public function updateProfile($profiledata,$format = "xml"){
			if(!$this->_call("http://twitter.com/account/update_profile.".$format,"POST",$profiledata)){
				return false;
			}
			return true;
		}

		public function directMessageNew($message,$recipient,$format = "xml"){
			if(!$this->_call("http://twitter.com/direct_messages/new.".$format,"POST",array("screen_name" => $recipient,"text" => $message))){
				return false;
			}
			return true;
		}

		public function getMentions($since_id = 0){
			$params = array('count' => 200);
			if ($since_id) {
				$params['since_id'] = $since_id;
			}
			if ($result = $this->_call('http://twitter.com/statuses/mentions.xml', 'GET', $params)) {
				$result = simplexml_load_string($result);
				$mentions = array();
				foreach ($result->status as $mention) {
					$mentions[] = $mention;
				}
				return $mentions;
			}
			return false;
		}

		protected function _call($url,$action = "GET",$params = null){
			$response = $this->_callDetailed($url,$action,$params);
			if(!is_string($response)){
				return false;
			}
			return $response;
		}

		protected function _initCurl()
		{
			if (!$this->_curl !== null) {
				@curl_close($this->_curl);
			}
			$this->_curl = curl_init();
			curl_setopt($this->_curl, CURLOPT_USERPWD, $this->user . ':' . $this->password);
			curl_setopt($this->_curl, CURLOPT_HEADER, 0);
			curl_setopt($this->_curl, CURLOPT_USERAGENT, 'lulzkaffee bot');
			curl_setopt($this->_curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($this->_curl, CURLOPT_TIMEOUT, 10);
		}

		protected function _callDetailed($url,$action = "GET",$params = null){
			if(!$this->user || !$this->password){
				return false;
			}

			$this->_initCurl();
			$query = array();
			foreach ($params as $key => $value) {
				if (trim($value)) {
					$query[] = $key . '=' . urlencode(trim($value));
				}
			}
			if ($action === 'POST') {
				curl_setopt($this->_curl, CURLOPT_POST, true);
				curl_setopt($this->_curl, CURLOPT_POSTFIELDS, implode('&', $query));
			} elseif ($action === 'GET') {
				$url .= '?' . implode('&', $query);
			}
			curl_setopt($this->_curl, CURLOPT_URL, $url);
			$result = curl_exec($this->_curl);
			$info   = curl_getinfo($this->_curl);
			if ($result && $info['http_code'] == 200) {
				return $result;
			}

			return false;
		}
	}
?>
