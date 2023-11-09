<?php

define('USER_AGENT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36');
define('COOKIE_FILE', sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'cookies_aidevs.txt');
define('NL', "\n");
define('CRNL', "\r\n");


class App
{
	const APIKEY_OPENAI = 'APIKEY_OPENAI';
	const APIKEY_AIDEVS = 'APIKEY_AIDEVS';

	protected $url_aidevs = 'https://zadania.aidevs.pl';
	protected $url_openai = 'https://api.openai.com';

	protected $api_key;
	protected $task_name;

	function __construct($key, $task)
	{
		$this->task_name = $task;
		$this->api_key = self::getApiKey($key);
	}

	/**
	 * Pobiera APIKEY ze zmiennej środowiskowej konfiguracji APACHE:
	 * SetEnv APIKEY_AIDEVS 33c...0d9
	 */
	protected static function getApiKey($key)
	{
		return $_SERVER[$key];
	}

	public function setUrlAidevs($url) {
		$this->url_aidevs = $url;
	}

	static function htmlStart($title, $subtitle, $icons = true)
	{
		echo '<!doctype html>' . NL;
		echo '<head>' . NL;
		echo '<meta charset="utf-8">' . NL;
		echo '<meta http-equiv="X-UA-Compatible" content="IE=edge">' . NL;
		echo '<meta name="google-site-verification" content="oWx3XUH80x8HqqMJDP-p2bZMYd0QutAGriiyA_n1dYA">' . NL;
		echo '<meta name="robots" content="noindex,nofollow">' . NL;
		echo '<meta name="viewport" content="width=device-width, initial-scale=1">' . NL;
		echo '<title>' . $title . '</title>' . NL;
		echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">' . NL;
		if ($icons) echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">' . NL;
		echo '</head>' . NL;
		echo '<html lang="pl">' . NL;
		echo '<div class="container">' . NL;
		echo '<h1>';
		if ($_SERVER['SCRIPT_NAME'] != '/index.php') echo '<a href="/"><i class="bi bi-house-door"></i></a> ';
		echo $title . '<br><small class="h3">' . $subtitle . '</small></h1>' . NL;
		//echo '<pre>' . print_r($_SERVER, 1) . NL;
		echo '<hr>' . NL;
	}

	static function htmlEnd()
	{
		echo '</div>' . NL;
		echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>' . NL;
		echo '</body>' . NL;
		echo '</html>' . NL;
	}

	static function initApp()
	{
		ini_set('display_errors', 1);
		ini_set('memory_limit', '128M');
		ini_set('display_startup_errors', 'On');
		error_reporting(E_ALL);
		set_time_limit(600);
		date_default_timezone_set('Europe/Warsaw');
		setlocale(LC_CTYPE, 'pl_PL.utf-8');
		mb_internal_encoding('UTF-8');
		mb_http_output('UTF-8');
	}

	static function getSafeApiKey($key, $max_len = 3, $hellip = '…')
	{
		$s = self::getApiKey($key);
		$len_part = floor(($max_len) / 2);
		$res0 = mb_substr($s, 0, ($max_len - $len_part));
		$res1 = mb_substr($s, -$len_part);
		return rtrim($res0, '.') . $hellip . ltrim($res1, '.');
	}
}

function r($v) {
	var_dump($v);
	exit;
}