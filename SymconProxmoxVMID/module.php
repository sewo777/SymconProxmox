<?php

declare(strict_types=1);
	class SymconProxmoxVMID extends IPSModule
	{
		public function Create()
		{
			//Never delete this line!
			parent::Create();
			//Default variables
			$this->RegisterPropertyBoolean('Uptime', true);
			$this->RegisterPropertyBoolean('Name', true);
			$this->RegisterPropertyBoolean('Status', true);
			//Additional Variables
			$this->RegisterPropertyBoolean('CPU(s)', true);
			$this->RegisterPropertyBoolean('Memory Total', true);
			$this->RegisterPropertyBoolean('Memory Used', true);
			$this->RegisterPropertyBoolean('Root Disk Size', true);
			$this->RegisterPropertyBoolean('Root Disk Used', false);
			

			$this->RegisterPropertyString('Node', '');
			$this->RegisterPropertyInteger('VM or LXC', 0);
			$this->RegisterPropertyInteger('VM/LXC ID', 100);
			$this->RegisterPropertyInteger('UpdateInterval', 60);
			$this->RegisterTimer('Update', $this->ReadPropertyInteger('UpdateInterval') * 1000, 'PVEID_Update($_IPS[\'TARGET\']);');
       
			$this->ConnectParent('{03F746E8-3F0F-C53E-8F1A-BF9A4DAFAC76}');

		}

		public function Destroy()
		{
			//Never delete this line!
			parent::Destroy();
		}



		public function ApplyChanges()
		{
			$this->SetTimerInterval('Update', $this->ReadPropertyInteger('UpdateInterval') * 1000);

			//Defaul Varaibles

			if ($this->ReadPropertyBoolean('Name') === true){
				$this->RegisterVariableString('vm_name', $this->Translate('Name'),'',0);
			}
			else{
				$this->UnregisterVariable("vm_name");
			}

			if ($this->ReadPropertyBoolean('Uptime') === true){
				$this->RegisterVariableString('pveuptime', $this->Translate('Uptime'),'',1);
			}
			else{
				$this->UnregisterVariable("pveuptime");
			}

			if ($this->ReadPropertyBoolean('Status') === true){
				$this->RegisterVariableString('vm_status', $this->Translate('Status'),'',2);
			}
			else{
				$this->UnregisterVariable("vm_status");
			}

			//Additional Variables

			if ($this->ReadPropertyBoolean('CPU(s)') === true){
				$this->RegisterVariableInteger('pvecpus', $this->Translate('CPU(s)'),'',3);
			}
			else{
				$this->UnregisterVariable("pvecpus");
			}

			if ($this->ReadPropertyBoolean('Root Disk Size') === true){
				$this->RegisterVariableFloat('disk_max', $this->Translate('Root Disk Storage Size'), 'PVE_Speicher',4);
			}
			else{
				$this->UnregisterVariable("disk_max");
			}
			
			if (($this->ReadPropertyBoolean('Root Disk Used') === true) and ($this->ReadPropertyInteger('VM or LXC') == 1)) {
				$this->RegisterVariableFloat('disk_used', $this->Translate('Root Disk Storage Used'), 'PVE_Speicher',5);
			}
			else{
				$this->UnregisterVariable("disk_used");
				
			}

			if ($this->ReadPropertyBoolean('Memory Total') === true){
				$this->RegisterVariableFloat('memory_total', $this->Translate('Memory Total'), 'PVE_Speicher',6);
			}
			else{
				$this->UnregisterVariable("memory_total");
			}

			if ($this->ReadPropertyBoolean('Memory Used') === true){
				$this->RegisterVariableFloat('memory_used', $this->Translate('Memory Used'), 'PVE_Speicher',7);
			}
			else{
				$this->UnregisterVariable("memory_used");
			}


			//Never delete this line!
			parent::ApplyChanges();
		}
		
		public function Update()
		{

			$array = array (
				0 => 'ID',
				1 => $this->ReadPropertyString('Node'), 
				2 => $this->ReadPropertyInteger('VM or LXC'),
				3 => $this->ReadPropertyInteger('VM/LXC ID')
			); 
			$data = implode(';', $array); 

			//Daten an I/O Schicken
			$return = $this->Send_To_Parent($data);

			//Empfangene Daten Aufbereiten
			$json = json_decode($return, true);

			//JSON auf gültigkeit Prüfen
			$status_json = (json_last_error() == JSON_ERROR_NONE);
;

			if ($status_json == true) {

				$mem_total = ($json['data']['maxmem']);
				$mem_total = $mem_total / pow(2,30);

				$mem_used = ($json['data']['mem']);
				$mem_used = $mem_used / pow(2,30);


				$pve_uptime = ($json['data']['uptime']);

				$pve_name = ($json['data']['name']);
				$pve_status = ($json['data']['status']);

				$pve_disk_used = ($json['data']['disk']);
				$pve_disk_used = $pve_disk_used / pow(2,30);

				$pve_disk_write = ($json['data']['maxdisk']);
				$pve_disk_write = $pve_disk_write / pow(2,30);

				$pve_disk_read = ($json['data']['diskread']);
				$pve_disk_read = $pve_disk_read / pow(2,30);

				$pve_cpus = ($json['data']['cpus']);
				
				//Defaul Varaibles

				if ($this->ReadPropertyBoolean('Name') === true){
					SetValue($this->GetIDForIdent('vm_name'), ($pve_name));
				}

				if ($this->ReadPropertyBoolean('Uptime') === true){
					SetValue($this->GetIDForIdent('pveuptime'),$this->Uptime($pve_uptime));
				}
				

				if ($this->ReadPropertyBoolean('Status') === true){

					if ($pve_status == 'running'){

						SetValue($this->GetIDForIdent('vm_status'), $this->Translate("Running"));
					}
					if ($pve_status == 'stopped'){

						SetValue($this->GetIDForIdent('vm_status'), $this->Translate("Stoped"));
					}
					
				}
				

				//Additional Variables

				if ($this->ReadPropertyBoolean('CPU(s)') === true){
					SetValue($this->GetIDForIdent('pvecpus'), $pve_cpus);
				}
				

				if ($this->ReadPropertyBoolean('Root Disk Size') === true){
					SetValue($this->GetIDForIdent('disk_max'), ($pve_disk_write));
				}
				
				
				if (($this->ReadPropertyBoolean('Root Disk Used') === true) and ($this->ReadPropertyInteger('VM or LXC') == 1)) {
					SetValue($this->GetIDForIdent('disk_used'), ($pve_disk_used));	
				}
				

				if ($this->ReadPropertyBoolean('Memory Total') === true){
					SetValue($this->GetIDForIdent('memory_total'), $mem_total);
				}
				

				if ($this->ReadPropertyBoolean('Memory Used') === true){
					SetValue($this->GetIDForIdent('memory_used'), $mem_used);
				}
		
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
				IPS_SetVariableProfileDigits('PVE_Speicher', 2);
         		IPS_SetVariableProfileText('PVE_Speicher', "", " GB");
			}

		}
	}