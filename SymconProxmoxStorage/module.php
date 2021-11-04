<?php

declare(strict_types=1);
	class SymconProxmoxStorage extends IPSModule
	{
		public function Create()
		{
			//Never delete this line!
			parent::Create();

			$this->RegisterPropertyBoolean('Storage Active', true);
			$this->RegisterPropertyBoolean('Storage Enabled', true);
			$this->RegisterPropertyBoolean('Total Size', true);
			$this->RegisterPropertyBoolean('Used Size', true);
			$this->RegisterPropertyBoolean('Free Size', true);
			$this->RegisterPropertyBoolean('Type', true);

			$this->RegisterPropertyString('Node', '');
			$this->RegisterPropertyString('Storage Name', 'local-lvm');

			$this->RegisterPropertyInteger('UpdateInterval', 60);
			$this->RegisterTimer('Update', $this->ReadPropertyInteger('UpdateInterval') * 1000, 'PVESTORAGE_Update($_IPS[\'TARGET\']);');
       
			$this->ConnectParent('{03F746E8-3F0F-C53E-8F1A-BF9A4DAFAC76}');
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

			//Variables

			 if ($this->ReadPropertyBoolean('Storage Active') === true){
			 	$this->RegisterVariableBoolean('storage_active', $this->Translate('Storage Active'), 'PVE_Storage', 0);
			 }
			 else{
			 	$this->UnregisterVariable("storage_active");
			 }

			if ($this->ReadPropertyBoolean('Storage Enabled') === true){
				$this->RegisterVariableBoolean('storage_enabled', $this->Translate('Storage Enabled'), 'PVE_Storage', 1);
			}
			else{
				$this->UnregisterVariable("storage_enabled");
			}

			if ($this->ReadPropertyBoolean('Total Size') === true){
				$this->RegisterVariableFloat('total_size', $this->Translate('Total Size'), 'PVE_Speicher', 2);
			}
			else{
				$this->UnregisterVariable("total_size");
			}

			if ($this->ReadPropertyBoolean('Used Size') === true){
				$this->RegisterVariableFloat('used_size', $this->Translate('Used Size'), 'PVE_Speicher', 3);
			}
			else{
				$this->UnregisterVariable("used_size");
			}

			if ($this->ReadPropertyBoolean('Free Size') === true){
				$this->RegisterVariableFloat('free_size', $this->Translate('Free Size'), 'PVE_Speicher', 4);
			}
			else{
				$this->UnregisterVariable("free_size");
			}

			if ($this->ReadPropertyBoolean('Type') === true){
				$this->RegisterVariableString('type', $this->Translate('Type'), '', 5);
			}
			else{
				$this->UnregisterVariable("type");
			}

			//Never delete this line!
			parent::ApplyChanges();
		}
		
		public function Update()
		{
			// this function is called, to get data from the I/O
			$array = array (
				0 => 'STORAGE',
				1 => $this->ReadPropertyString('Node'),
				2 => $this->ReadPropertyString('Storage Name')
				
			); 
			$data = implode(';', $array); //arraywerte durch semikolon getrennt als string konvertieren
			

			//$debug = 'I send to the I/O: ' . $data;
			//$this->SendDebug(__FUNCTION__,  $debug , 0);
			//Daten an I/O Schicken
			$return = $this->Send_To_Parent($data);

			//Empfangene Daten Aufbereiten
			$json = json_decode($return, true);

			//JSON auf gültigkeit Prüfen
			$status_json = (json_last_error() == JSON_ERROR_NONE);

				if ($status_json == true) 
				{

					$storage_total = ($json['data']['total']);
					$storage_total = $storage_total / pow(2,30);

					$storage_used = ($json['data']['used']);
					$storage_used = $storage_used / pow(2,30);

					$storage_avail = ($json['data']['avail']);
					$storage_avail = $storage_avail / pow(2,30);

					$storage_enabled = ($json['data']['enabled']);

					$storage_active = ($json['data']['active']);

					$storage_type = ($json['data']['type']);

					
				
					if ($this->ReadPropertyBoolean('Storage Enabled') === true){
						SetValue($this->GetIDForIdent('storage_enabled'), $storage_enabled);
					}

					if ($this->ReadPropertyBoolean('Storage Active') === true){
						SetValue($this->GetIDForIdent('storage_active'), $storage_active);
					}

					if ($this->ReadPropertyBoolean('Total Size') === true){
						SetValue($this->GetIDForIdent('total_size'), $storage_total);
					}

					if ($this->ReadPropertyBoolean('Used Size') === true){
						SetValue($this->GetIDForIdent('used_size'), $storage_used);
					}
					if ($this->ReadPropertyBoolean('Free Size') === true){
						SetValue($this->GetIDForIdent('free_size'), $storage_avail);
					}

					if ($this->ReadPropertyBoolean('Type') === true)
					{


						if ($storage_type === 'zfspool'){
							SetValue($this->GetIDForIdent('type'), 'ZFS (local)');
						}
						if ($storage_type === 'dir'){
							SetValue($this->GetIDForIdent('type'), 'Directory');
						}
						if ($storage_type === 'btrfs'){
							SetValue($this->GetIDForIdent('type'), 'BTRFS');
						}
						if ($storage_type === 'nfs'){
							SetValue($this->GetIDForIdent('type'), 'NFS');
						}
						if ($storage_type === 'cifs'){
							SetValue($this->GetIDForIdent('type'), 'CIFS');
						}
						if ($storage_type === 'pbs'){
							SetValue($this->GetIDForIdent('type'), 'Proxmox Backup');
						}
						if ($storage_type === 'glusterfs'){
							SetValue($this->GetIDForIdent('type'), 'GlusterFS');
						}
						if ($storage_type === 'cephfs'){
							SetValue($this->GetIDForIdent('type'), 'CephFS');
						}
						if ($storage_type === 'lvm'){
							SetValue($this->GetIDForIdent('type'), 'LVM');
						}
						if ($storage_type === 'lvmthin'){
							SetValue($this->GetIDForIdent('type'), 'LVM-thin');
						}
						if ($storage_type === 'iscsi'){
							SetValue($this->GetIDForIdent('type'), 'iSCSI/kernel');
						}
						if ($storage_type === 'iscsidirect'){
							SetValue($this->GetIDForIdent('type'), 'iSCSI/libiscsi');
						}
						if ($storage_type === 'rbd'){
							SetValue($this->GetIDForIdent('type'), 'Ceph/RBD');
						}
						if ($storage_type === 'zfs'){
							SetValue($this->GetIDForIdent('type'), 'ZFS over iSCSI');
						}
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

			if (!IPS_VariableProfileExists('PVE_Storage')) {
				IPS_CreateVariableProfile('PVE_Storage', 0);
				IPS_SetVariableProfileAssociation('PVE_Storage', 0, "Nein", "", 0x585858);
				IPS_SetVariableProfileAssociation('PVE_Storage', 1, "Ja", "", 0x88a08);
         		
			}



		}
	}