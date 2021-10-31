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
			// Attribute to store the token
			$this->RegisterAttributeString('Token','');
			$this->RegisterAttributeString('Tiket_Status','');

			// timer to Update the Token 300000 = 300 s = 5 min
			$this->RegisterTimer('UpdateToken', 1800000, 'PVEIO_ReNewToken($_IPS[\'TARGET\']);');

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
			$recived = json_decode($JSONString);
			$data = utf8_decode($recived->Buffer);
			//$this->SendDebug(__FUNCTION__, 'I got from the Node:' , 0);
			//$this->SendDebug(__FUNCTION__,  $data, 0);
			
			$token = $this->ReadAttributeString('Token');
			//$this->SendDebug(__FUNCTION__, 'I have this token:' , 0);
			//$this->SendDebug(__FUNCTION__, $token , 0);

			$url = $this->ReadPropertyString('Url');
			$port = $this->ReadPropertyInteger('Port');


			$curl = curl_init();

            curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://'.$url.':'.$port.'/api2/json//nodes/'.$data.'/status',
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_COOKIE => 'PVEAuthCookie='.$token,
					CURLOPT_SSL_VERIFYPEER => false,
				));

			$result = curl_exec($curl);
			$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			if (curl_error($curl)) {
    			echo 'Error:' . curl_error($curl);
			}
			
			curl_close($curl);
			$daten =  $result . PHP_EOL;
			// Status 
			$this->WriteAttributeString('Tiket_Status', $httpcode);

			if ($httpcode == 200){

				$returndata = $daten;
			}
			else {

				$returndata = '';
				$this->ReNewToken();

			}
			// now i can do somting with $data and $token
			

			//$this->SendDebug(__FUNCTION__, 'I send back to the node:' , 0);
			//$this->SendDebug(__FUNCTION__, $returndata , 0);
			
			// send back to the node:
			return $returndata;
		}

		public function Send(string $Text)
		{
			$this->SendDataToChildren(json_encode(['DataID' => '{F32EAE78-97AA-E390-804A-8600121F361A}', 'Buffer' => $Text]));
		}

		// this function runs cyclic as defined in create()
		public function ReNewToken()
		{
			$status_tiket = $this->ReadAttributeString('Tiket_Status');

			if ($status_tiket != 200){
		
			
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

				//$this->SendDebug('ReNewToken()', 'Response:' . $result, 0);
				
				curl_close($curl);

				if ($httpcode != 200){
					$tiket = 'Fehler';
					$this->SendDebug('ReNewToken()', 'Denied permission', 0);
				}
				if ($httpcode == 200){
					$json = json_decode($result, true);
					$tiket = ($json['data']['ticket']);
					// store the token
					$this->WriteAttributeString('Token', $tiket);
					
				}
				$this->SendDebug('ReNewToken()', 'Response:' . $tiket, 0);
				
		}

			if ($status_tiket == 200){

				$this->SendDebug('ReNewToken()', 'Tiket Valid '  , 0);

			}	

		}
	}