<?php
require 'app.lib.php';

class AIDevs extends App
{
	private $task = 'blogger';
	private $task_field = 'blog';

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
	 * Zadanie: Bloger w OpenAI
	 */
	public function blogerTexts($arr_text)
	{
		if (!is_array($arr_text)) $arr_text[] = (string)$arr_text;


		$jsonData = self::jsonEncodeFlags($arr_text);

		$data = [
			'model' => 'gpt-4',	// davinci
			'messages' => [
				[
					'role' => 'system',
					'content' => 'To jest rozmowa z blogerem.'],
				[
					'role' => 'user',
					'content' => 'W formacie JSON przesyłam ci 4 tytuły na wpis blogowy. '.
						'W odpowiedzi zwróć 4 akapity tekstu odpowiadające danym tytułom. '.
						'Dane zwróć w formacie JSON, bez żadnych dodatkowych komentarzy!'.
						NL . NL.
						$jsonData
				]
			]
		];

		// Opcje dla kontekstu strumienia
		$options = [
			'http' => [
				'method' => 'POST',
				'header' =>
					'Content-Type: application/json' . CRNL
					. 'Authorization: Bearer ' . self::getApiKey(App::APIKEY_OPENAI),
				'content' => self::jsonEncodeFlags($data),
				'ignore_errors' => true
			]
		];
		$context = stream_context_create($options);

		$url = $this->url_openai . '/v1/chat/completions';
		$result = file_get_contents($url, false, $context);


		// Sprawdzenie, czy wystąpił błąd
		if ($result === false) {
			throw new Exception('Problem with the OpenAI API request.');
		}

		$result_decode = json_decode($result, true);
		$result_content = json_decode($result_decode['choices']['0']['message']['content'], true);
		$result_text = array_values($result_content);

		return $result_text;
	}

	/**
	 * Wyślij odpowiedź
	 */
	public function sendAnswer($token, $data)
	{
		$jsonData = self::jsonEncodeFlags(['answer' => $data]);
		$options = [
			'http' => [
				'header'  => 'Content-type: application/json' . CRNL
					. "Content-Length: " . strlen($jsonData) . CRNL,
				'method'  => 'POST',
				'content' => $jsonData,
			],
		];

		//rb($options);

		$context  = stream_context_create($options);

		$url = $this->url_aidevs . '/answer/' . $token;

		$result = file_get_contents($url, false, $context);
		$resultData = json_decode($result, true);

		return '/answer/{token: '.$token.'} returned:<br><pre>' . print_r($resultData, 1) . '</pre>' . NL;
	}

	/**
	 * Zadanie: Blogger
	 */
	public function run()
	{
		// KROK 1: Pobierz token
		$token = $this->getToken();

		// KROK 2: Pobierz zadanie
		$task = $this->getTask($token);

		$result_text = $this->blogerTexts($task[$this->task_field]);

		// KROK 4: Wyślij odpowiedź
		$result = $this->sendAnswer($token, $result_text);

		echo '<h2>Wynik:</h2>';
		echo $result;
		echo '<hr>';

	}

}

App::initApp();
App::htmlStart('AIDevs', 'Zadanie C01L04: `blogger`');

$ai = new AIDevs(App::APIKEY_AIDEVS, 'blogger');

$ai->run();

App::htmlEnd();

?>
