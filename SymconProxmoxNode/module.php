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
		
		public function Update()
		{
			// this function is called, to get data from the I/O
			$data = 'Give me Data';


			$debug = 'I send to the I/O: ' . $data;
			$this->SendDebug(__FUNCTION__,  $debug , 0);

			$return = $this->Send_To_Parent($data);

			$debug= 'I got from the I/O: ' . $return;
			$this->SendDebug(__FUNCTION__,  $debug , 0);
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
	}