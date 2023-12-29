<?php
require 'app.lib.php';

class AIDevs extends App
{
	private $task = 'liar';
	private $task_field = 'liar';

	/**
	 * Pobiera token
	 */
	public function getToken()
	{
		$url = $this->url_aidevs . '/token/' . $this->task;
		$data = self::jsonEncodeFlags(['apikey' => $this->api_key]);
		$options = [
			'http' => [
				'header'  => 'Content-type: application/json' . CRNL
					. "Accept: application/json" . CRNL
					. "Content-Length: " . strlen($data) . CRNL,
				'method'  => 'POST',
				'content' => $data,
			],
		];

		$context = stream_context_create($options);

		$result = file_get_contents($url, false, $context);	// Zwraca: [code, msg, token]
		$data = json_decode($result, true);

		if ($data['code'] !== 0) throw new Exception('Error: ' . $data['msg']);

		return $data['token'];
	}

	/**
	 * Pobierz zadanie
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
	 * Odbiera odpowiedź, wg. wskazóweK:
	 * sometimes I don't tell the truth
	 */
	public function getAnswer($token, $question)
	{
		$bodyData = http_build_query(['question' => $question]);
		$options = [
			'http' => [
				'method'  => 'POST',
				'header'  => "Content-type: application/x-www-form-urlencoded" . CRNL
					. "Content-Length: " . strlen($bodyData) . CRNL,
				'content' => $bodyData,
			],
		];
		$context  = stream_context_create($options);

		$url = $this->url_aidevs . '/task/' . $token;
		$result = file_get_contents($url, false, $context);
		$resultData = json_decode($result, true);
		if ($resultData['code'] !== 0) throw new Exception('Error: ' . $resultData['msg']);

		return $resultData['answer'];
	}

	/**
	 * Wyślij odpowiedź
	 * Send to /answer/ info if I'm telling the truth. Just value: YES/NO
	 */
	public function analyzingResultAndAnswer($token, $data)
	{
		// Analiza zwróconych danych
		$answer = 'NO';
		if (strpos($data, '###') !== false) {
			$arrData = explode('###', $data);
			if (trim($arrData[1]) != '') {
				$answer = 'YES';
			}
		}


		$jsonData = self::jsonEncodeFlags(['answer' => $answer]);
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
	 * Zadanie: Liar
	 */
	public function run()
	{
		// KROK 1: Pobierz token
		$token = $this->getToken();

		// KROK 2: Pobierz zadanie
		$task = $this->getTask($token);

		$question = 'Name the berry fruit from which a well-known smartphone brand was created? Before the answer, add a separator: \n### and answer the question very briefly, using one word.';

		$result = $this->getAnswer($token,	$question);

		// KROK 4: Wyślij odpowiedź
		$result_answer = $this->analyzingResultAndAnswer($token, $result);

		echo '<h2>Wynik:</h2>';
		echo $result_answer;
		echo '<hr>';

	}

}

App::initApp();
App::htmlStart('AIDevs', 'Zadanie C01L05: `liar`');

$ai = new AIDevs(App::APIKEY_AIDEVS, 'Liar');

$ai->run();

App::htmlEnd();

?>
