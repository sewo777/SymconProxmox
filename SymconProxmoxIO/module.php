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

			$this->RegisterAttributeString('Token','');
			$this->RegisterAttributeString('Tiket_Status1','');
			$this->RegisterAttributeString('Tiket_Status2','');
			$this->RegisterAttributeString('Tiket_Status3','');

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
			$data = explode(';', $data);  //string zurück zu array

			

						
			//Token
			$token = $this->ReadAttributeString('Token');

			//Login Data
			$url = $this->ReadPropertyString('Url');
			$port = $this->ReadPropertyInteger('Port');
			

			// if ($data[0] == 'STORAGE'){

			// 	$returndata = '';

			// 	$this->SendDebug(__FUNCTION__,  $data[2] , 0);


			// }


			//Abfrage Storage
			if ($data[0] == 'STORAGE')
			{

				$node = $data[1];
				$storage_name = $data[2];

				//Abfrage Storage
				$curl = curl_init();

				curl_setopt_array($curl, array(
						CURLOPT_URL => 'https://'.$url.':'.$port.'/api2/json//nodes/'.$node.'/storage'.'/'.$storage_name.'/status',
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
				$this->SendDebug(__FUNCTION__,  $daten , 0);
				// Status 
				$this->WriteAttributeString('Tiket_Status3', $httpcode);

				if ($httpcode == 200){

					$returndata = $daten;
				}
				else {

					$returndata = '';
					//Renew Token 
					$this->ReNewToken();

				}
			}



			//Node Name für Abfrage
			if ($data[0] == 'NODE')
			{

				$node = $data[1];

				//Abfrage Nodes
				$curl = curl_init();

				curl_setopt_array($curl, array(
						CURLOPT_URL => 'https://'.$url.':'.$port.'/api2/json//nodes/'.$node.'/status',
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
				$this->WriteAttributeString('Tiket_Status1', $httpcode);

				if ($httpcode == 200){

					$returndata = $daten;
				}
				else {

					$returndata = '';
					//Renew Token 
					$this->ReNewToken();

				}
			}

			//Abfrage ID's (Virtuelle Maschienen)
			if ($data[0] == 'ID')
			{
				
				$node = $data[1];
				$vmctid = $data[3];

				if ($data[2] == 0){

					$vmct = '/qemu';
				}
				if ($data[2] == 1){

					$vmct = '/lxc';
				}

					//Abfrage ID's
					$curl = curl_init();
					
					curl_setopt_array($curl, array(
							CURLOPT_URL => 'https://'.$url.':'.$port.'/api2/json//nodes/'.$node.$vmct.'/'.$vmctid.'/status/current',
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
					$this->WriteAttributeString('Tiket_Status2', $httpcode);

					if ($httpcode == 200){

						$returndata = $daten;
						$this->SendDebug(__FUNCTION__, $returndata , 0);
					}
					else {

						$returndata = '';
						//Renew Token 
						$this->ReNewToken();

					}
			}

			
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
			$status_tiket1 = $this->ReadAttributeString('Tiket_Status1');
			$status_tiket2 = $this->ReadAttributeString('Tiket_Status2');
			$status_tiket3 = $this->ReadAttributeString('Tiket_Status3');

			if (($status_tiket1 != 200) or ($status_tiket2 != 200) or ($status_tiket3 != 200)) {
		
			
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

			if (($status_tiket1 == 200) or ($status_tiket2 == 200)){

				$this->SendDebug('ReNewToken()', 'Tiket Valid '  , 0);

			}	

		}
	}