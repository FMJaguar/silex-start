<?php

namespace App\Logging;

use Symfony\Composer\HttpFoundation\Session\Session;

class IdRequestProcessor
{

	private $token;

	public function processRecord(array $record)
	{
		if (null === $this->token) {
			try {
				$this->token = session_id();
			}	catch (\RuntimeException $e) {
				$this->token = '????????';
			}
			$this->token .= '-' . substr(uniqid(), -8);
		}
		$record['extra']['token'] = $this->token;
		return $record;
	}

	public function __invoke(array $record){
		return $this->processRecord($record);
	}
}
