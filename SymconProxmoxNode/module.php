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
		}

		public function Destroy()
		{
			//Never delete this line!
			parent::Destroy();
		}

		public function ApplyChanges()
		{
			$this->SetTimerInterval('Update', $this->ReadPropertyInteger('UpdateInterval') * 1000);
			//Never delete this line!
			parent::ApplyChanges();
		}
		
		public function Send()
		{

			return $this->SendDataToParent(json_encode(['DataID' => '{A8F28772-131A-351B-DEC1-BA2995EAE4C3}']));

			
		}

		public function ReceiveData($JSONString)
		{
			$data = json_decode($JSONString);
			IPS_LogMessage('Device RECV', utf8_decode($data->Buffer));
		}
	}