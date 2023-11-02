<?php
require 'app.lib.php';



class AIDevs extends App
{
	private $task = 'helloapi';

	/**
	 * 1. Pobiera token
	 */
	public function getToken()
	{
		$url = $this->base_url . '/token/' . $this->task;
		$data = json_encode(['apikey' => $this->api_key]);
		$options = [
			'http' => [
				'header'  => 'Content-type: application/json' . CRNL
					. "Content-Length: " . strlen($data) . CRNL,
				'method'  => 'POST',
				'content' => $data,
			],
		];

		$context  = stream_context_create($options);

		$result = file_get_contents($url, false, $context);	// Zwraca: [code, msg, token]
		$data = json_decode($result, true);

		if ($data['code'] !== 0) throw new Exception('Error: ' . $data['msg']);

		return $data['token'];
	}

	/**
	 * 2. Pobierz zadanie
	 */
	public function getTask($token)
	{
		$task_url = $this->base_url . '/task/' . $token;
		$result = file_get_contents($task_url);
		$data = json_decode($result, true);
		if ($data['code'] !== 0) throw new Exception('Error: ' . $data['msg']);

		return $data['cookie'];
	}

	/**
	 * 3. Wyślij odpowiedź
	 */
	public function sendAnswer($token, $cookie)
	{
		$data = json_encode(['answer' => $cookie]);
		$options = [
			'http' => [
				'header'  => 'Content-type: application/json' . CRNL
					. "Content-Length: " . strlen($data) . CRNL,
				'method'  => 'POST',
				'content' => $data,
			],
		];
		$context  = stream_context_create($options);

		$url = $this->base_url . '/answer/' . $token;
		$result = file_get_contents($url, false, $context);
		$data = json_decode($result, true);

		return '/answer/{token} returned:<br><pre>' . print_r($data, 1) . '</pre>' . NL;
	}

	/**
	 * Zadanie1: helloapi
	 */
	public function task1_Helloapi()
	{
		// KROK 1: Pobierz token
		$token = $this->getToken();

		// KROK 2: Pobierz zadanie
		$cookie = $this->getTask($token);

		// KROK 3: Wyślij odpowiedź
		$result = $this->sendAnswer($token, $cookie);

		echo '<h2>Wynik:</h2>';
		echo $result;
		echo '<hr>';

	}

}

App::initApp();
App::htmlStart('AIDevs', 'Zadanie `helloapi`');

$ai = new AIDevs(App::APIKEY_AIDEVS, 'helloapi');

$ai->task1_Helloapi();

App::htmlEnd();

?>
