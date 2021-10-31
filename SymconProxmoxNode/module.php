<?php

declare(strict_types=1);
	class SymconProxmoxNode extends IPSModule
	{
		public function Create()
		{
			//Never delete this line!
			parent::Create();
			$this->RegisterPropertyString('Node', '');
			$this->RegisterPropertyInteger('UpdateInterval', 60);
			$this->RegisterTimer('Update', $this->ReadPropertyInteger('UpdateInterval') * 1000, 'PVENODE_Update($_IPS[\'TARGET\']);');
       
			$this->RequireParent('{03F746E8-3F0F-C53E-8F1A-BF9A4DAFAC76}');
			$this->CreateVariableProfile();
		}

		public function Destroy()
		{
			//Never delete this line!
			parent::Destroy();
		}

		public function ApplyChanges()
		{
			$this->SetTimerInterval('Update', $this->ReadPropertyInteger('UpdateInterval') * 1000);
			$this->RegisterVariableFloat('memory_total', $this->Translate('Memory Total'), 'PVE_Speicher', 1);
			$this->RegisterVariableFloat('memory_used', $this->Translate('Memory Used'), 'PVE_Speicher', 2);
			$this->RegisterVariableFloat('memory_free', $this->Translate('Memory Free'), 'PVE_Speicher', 3);
			$this->RegisterVariableString('pveuptime', $this->Translate('Uptime'));
			$this->RegisterVariableString('pvecpumodel', $this->Translate('CPU'));
			$this->RegisterVariableInteger('pvecpusockets', $this->Translate('CPU Sockets'));
			$this->RegisterVariableInteger('pvecpucore', $this->Translate('CPU Cores'));
			$this->RegisterVariableInteger('pvecpus', $this->Translate('CPU(s)'));

			//Never delete this line!
			parent::ApplyChanges();
		}
		
		public function Update()
		{
			// this function is called, to get data from the I/O
			$data = $this->ReadPropertyString('Node');


			//$debug = 'I send to the I/O: ' . $data;
			//$this->SendDebug(__FUNCTION__,  $debug , 0);

			$return = $this->Send_To_Parent($data);

			//Empfangene Daten Aufbereiten
			$json = json_decode($return, true);

			//JSON auf gültigkeit Prüfen
			$status_json = (json_last_error() == JSON_ERROR_NONE);
			//$this->SendDebug(__FUNCTION__,  $status_json , 0);

			if ($status_json == true) {

				$mem_total = ($json['data']['memory']['total']);
				$mem_total = $mem_total / pow(2,30);

				$mem_used = ($json['data']['memory']['used']);
				$mem_used = $mem_used / pow(2,30);

				$mem_free = ($json['data']['memory']['free']);
				$mem_free = $mem_free / pow(2,30);

				$pve_uptime = ($json['data']['uptime']);

				$pve_cpumodel = ($json['data']['cpuinfo']['model']);
				$pve_cpusocket = ($json['data']['cpuinfo']['sockets']);
				$pve_cpucores = ($json['data']['cpuinfo']['cores']);
				$pve_cpus = ($json['data']['cpuinfo']['cpus']);
				

			
				SetValue($this->GetIDForIdent('memory_total'), $mem_total);
				SetValue($this->GetIDForIdent('memory_used'), $mem_used);
				SetValue($this->GetIDForIdent('memory_free'), $mem_free);
				SetValue($this->GetIDForIdent('pveuptime'),$this->Uptime($pve_uptime));
				SetValue($this->GetIDForIdent('pvecpumodel'), $pve_cpumodel);
				SetValue($this->GetIDForIdent('pvecpusockets'), $pve_cpusocket);
				SetValue($this->GetIDForIdent('pvecpucore'), $pve_cpucores);
				SetValue($this->GetIDForIdent('pvecpus'), $pve_cpus);
		}

		}

		public function ReceiveData($JSONString)
		{
			$data = json_decode($JSONString);
			IPS_LogMessage('Device RECV', utf8_decode($data->Buffer));
		}

		protected function Send_to_Parent($Buffer)
		{
			$return = $this->SendDataToParent(json_encode([
				'DataID' => "{A8F28772-131A-351B-DEC1-BA2995EAE4C3}",
				'Buffer' => utf8_encode($Buffer),
			]));
			return $return;
		}

		private function Uptime($secs)
		{
			
			$now = date_create('now', new DateTimeZone('GMT'));
			$here = clone $now;
			$here->modify($secs.' seconds');

			$diff = $now->diff($here);

		 
			return $diff->format('%ad %hh  %im %ss');
		}


		private function CreateVariableProfile()
		{
			if (!IPS_VariableProfileExists('PVE_Speicher')) {
				IPS_CreateVariableProfile('PVE_Speicher', 2);
				IPS_SetVariableProfileValues('PVE_Speicher', 0, 0, 0);
				IPS_SetVariableProfileDigits('PVE_Speicher', 1);
         		IPS_SetVariableProfileText('PVE_Speicher', "", " GB");
			}

		}
	}