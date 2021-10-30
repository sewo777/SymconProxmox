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
			// timer to Update the Token 300000 = 300 s = 5 min
			$this->RegisterTimer('UpdateToken', 300000, 'PVEIO_ReNewToken($this->InstanceID);');

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
			$this->SendDebug(__FUNCTION__, 'I got from the Node:' , 0);
			$this->SendDebug(__FUNCTION__,  $data, 0);
			
			$token = $this->ReadAttributeString('Token');
			$this->SendDebug(__FUNCTION__, 'I have this token:' , 0);
			$this->SendDebug(__FUNCTION__, $token , 0);
			
			// now i can do somting with $data and $token
			$returndata = 'Success';

			$this->SendDebug(__FUNCTION__, 'I send back to the node:' , 0);
			$this->SendDebug(__FUNCTION__, $returndata , 0);
			
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
			$url = $this->ReadPropertyString('Url');
			$port = $this->ReadPropertyInteger('Port');
			$username = urlencode($this->ReadPropertyString('Username'));
            $password = urlencode($this->ReadPropertyString('Password'));

			/*
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
				// store the token
				$this->WriteAttributeString('Token', $token);
			}
			*/
			//return $token;
		}
	}
