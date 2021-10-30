<?php

declare(strict_types=1);
	class SymconProxmoxIO extends IPSModule
	{
		public function Create()
		{
			$this->RegisterPropertyString('Url', '');
			$this->RegisterPropertyInteger('Port', 8006);
            		$this->RegisterPropertyString('Username', '');
            		$this->RegisterPropertyString('Password', '');
            
			$this->RegisterPropertyBoolean('Active', false);
			//Never delete this line!
			parent::Create();
		}

		public function Destroy()
		{
			//Never delete this line!
			parent::Destroy();
		}

		public function ApplyChanges()
		{
			//Never delete this line!
			parent::ApplyChanges();
		}

		public function ForwardData($JSONString)
		{
			$data = json_decode($JSONString);
			IPS_LogMessage('IO FRWD', utf8_decode($data->Buffer));
				
			
		}

		public function Send(string $Text)
		{
			$this->SendDataToChildren(json_encode(['DataID' => '{F32EAE78-97AA-E390-804A-8600121F361A}', 'Buffer' => $Text]));
		}
		public function Token()
		{
			$url = $this->ReadPropertyString('Url');
			$port = $this->ReadPropertyInteger('Port');
			$username = urlencode($this->ReadPropertyString('Username'));
            $password = urlencode($this->ReadPropertyString('Password'));

			$curl = curl_init();

            curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://'.$url.':'.$port.'/api2/json/access/ticket',
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_POST => 1,
					CURLOPT_POSTFIELDS => 'username='.$username.'@pam&password='.$password,
					CURLOPT_SSL_VERIFYPEER => false,
				));

			$result = curl_exec($curl);
			$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			if (curl_error($curl)) {
    			echo 'Error:' . curl_error($curl);
			}

			$this->SendDebug('Token()', 'Response:' . $result, 0);
			
			curl_close($curl);

			if ($httpcode != 200){
				$token = 'Fehler';
				$this->SendDebug('Token()', 'Denied permission', 0);
			}
			if ($httpcode == 200){
				$json = json_decode($result, true);
				$token = ($json['data']['CSRFPreventionToken']);
			}

			return $token;
		}
	}
