<?php
require 'app.lib.php';

class AIDevs extends App
{
	private $task = 'moderation';
	private $task_field = 'input';

	/**
	 * 1. Pobiera token
	 */
	public function getToken()
	{
		$url = $this->url_aidevs . '/token/' . $this->task;
		$data = json_encode(['apikey' => $this->api_key]);
		$options = [
			'http' => [
				'header'  => 'Content-type: application/json' . CRNL
					. "Content-Length: " . strlen($data) . CRNL,
				'method'  => 'POST',
				'content' => $data,
			],
		];

		$context = stream_context_create($options);

		$result = file_get_contents($url, false, $context);    // Zwraca: [code, msg, token]
		$data = json_decode($result, true);

		if ($data['code'] !== 0) throw new Exception('Error: ' . $data['msg']);

		return $data['token'];
	}

	/**
	 * 2. Pobierz zadanie
	 */
	public function getTask($token)
	{
		$task_url = $this->url_aidevs . '/task/' . $token;
		$result = file_get_contents($task_url);
		$task = json_decode($result, true);

		if ($task['code'] !== 0) throw new Exception('Error: ' . $task['msg']);

		return $task;
	}

	/**
	 * Moderuj teksty w OpenAI
	 */
	public function moderateTexts($arr_text)
	{
		if (!is_array($arr_text)) $arr_text[] = (string)$arr_text;

		$jsonData = json_encode([
			"input" => $arr_text
		]);

		// Opcje dla kontekstu strumienia
		$options = [
			'http' => [
				'method' => 'POST',
				'header' =>
					'Content-Type: application/json' . CRNL
					. 'Authorization: Bearer ' . self::getApiKey(App::APIKEY_OPENAI),
				'content' => $jsonData,
				'ignore_errors' => true
		]];

		$context = stream_context_create($options);

		$url = $this->url_openai . '/v1/moderations';
		$result = file_get_contents($url, false, $context);

		// Sprawdzenie, czy wystąpił błąd
		if ($result === false) {
			throw new Exception('Problem with the OpenAI API request.');
		}

		// Dekodowanie odpowiedzi JSON do tablicy PHP
		$decode_result = json_decode($result, true);
		$moderations = [];
		foreach ($decode_result['results'] as $result) {
			$moderations[] = (int)!empty($result['flagged']);
		}

		return $moderations;
	}


	/**
	 * Wyślij odpowiedź
	 */
	public function sendAnswer($token, $data)
	{
		$jsonData = json_encode(['answer' => $data]);
		$options = [
			'http' => [
				'header'  => 'Content-type: application/json' . CRNL
					. "Content-Length: " . strlen($jsonData) . CRNL,
				'method'  => 'POST',
				'content' => $jsonData,
			],
		];
		$context  = stream_context_create($options);

		$url = $this->url_aidevs . '/answer/' . $token;
		$result = file_get_contents($url, false, $context);
		$resultData = json_decode($result, true);

		return '/answer/{token: '.$token.'} returned:<br><pre>' . print_r($resultData, 1) . '</pre>' . NL;
	}

	/**
	 * Zadanie1: moderation
	 */
	public function run()
	{
		// KROK 1: Pobierz token
		$token = $this->getToken();

		// KROK 2: Pobierz zadanie
		$task = $this->getTask($token);


		// KROK 3: Moderuj zdania w OpenAI
		try {
			$moderation_result = $this->moderateTexts($task[$this->task_field]);

			foreach ($task[$this->task_field] as $key => $sentence) {
				echo 'Zdanie ' . $key  . ': `<code>' . $sentence . '</code>` - , MODERATE='.($moderation_result[$key]?'<strong>TAK</strong>':'NIE').'<br>' . NL;
			}
		} catch (Exception $e) {
			echo 'Caught exception: ', $e->getMessage(), "\n";
		}

		$result = $this->sendAnswer($token, $moderation_result);
		echo '<hr>';
		echo '<h2>Wynik:</h2>';
		echo $result;
	}


}

App::initApp();
App::htmlStart('AIDevs', 'Zadanie C01L04: `moderation`');

$ai = new AIDevs(App::APIKEY_AIDEVS, 'moderation');
$ai->run();



App::htmlEnd();

