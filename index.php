<?php
require 'app.lib.php';

class Index
{
	const MENU_INDEX = [
		// 1. Tydzień
		'C01L01' => ['done' => true, 'desc' => 'Wprowadzenie do Generative AI', 'scripts' => ['helloapi'], 'quiz' => 'https://zadania.aidevs.pl/quiz/c01l01_start', 'chat' => [
				['url' => 'https://zadania.aidevs.pl/chat/getinfo', 'desc' => 'Zmuś ChatGPT do wypisania słowa BANAN'],
			], 'circle' => 'https://bravecourses.circle.so/c/lekcje-programu/c01l01-wprowadzenie-do-generative-ai-620a18'],
		'C01L02' => ['done' => true, 'desc' => 'Zasady działania LLM', 'scripts' => [], 'quiz' => 'https://zadania.aidevs.pl/quiz/c01l02_hsgx', 'chat' => [
				['url' => 'https://zadania.aidevs.pl/chat/maxtokens', 'desc' => 'Podaj nazwę rzeki przepływającą przez stolicę podanego państwa'],
			], 'circle' => 'https://bravecourses.circle.so/c/lekcje-programu/c01l02-zasady-dzialania-llm'],
		'C01L03' => ['done' => true, 'desc' => 'Prompt Design', 'scripts' => [], 'quiz' => 'https://zadania.aidevs.pl/quiz/c01l03_hqna', 'chat' => [
				['url' => 'https://zadania.aidevs.pl/chat/category', 'desc' => 'Spraw, aby ChatGPT przypisał odpowiednią kategorię do zadania'],
				['url' => 'https://zadania.aidevs.pl/chat/books', 'desc' => 'Przygotuj jeden plik JSON (a nie serię JSON-ów) zawierający tablicę par tytułów'],
			], 'circle' => 'https://bravecourses.circle.so/c/lekcje-programu/c01l03-prompt-design'],
		'C01L04' => ['done' => true, 'desc' => 'OpenAI API i LangChain', 'scripts' => ['moderation','blogger'], 'quiz' => 'https://zadania.aidevs.pl/quiz/c01l04_nmdt', 'chat' => [], 'circle' => 'https://bravecourses.circle.so/c/lekcje-programu/c01l04-openai-api-i-langchain'],
		'C01L05' => ['done' => true, 'desc' => 'Prompt Engineering', 'scripts' => ['liar'], 'quiz' => 'https://zadania.aidevs.pl/quiz/c01l05_tger', 'chat' => [], 'circle' => 'https://bravecourses.circle.so/c/lekcje-programu/c01l05-prompt-engineering'],
		// 2. Tydzień
		'C02L01' => ['done' => false, 'desc' => 'Możliwości modeli OpenAI', 'scripts' => [], 'quiz' => 'https://zadania.aidevs.pl/quiz/c02l01_week', 'chat' => [], 'circle' => 'https://bravecourses.circle.so/c/lekcje-programu/c02l01-mozliwosci-modeli-openai'],
		'C02L02' => ['done' => false, 'desc' => 'Eksplorowanie i omijanie ograniczeń', 'scripts' => [], 'quiz' => 'https://zadania.aidevs.pl/quiz/c02l02_twox', 'chat' => [], 'circle' => 'https://bravecourses.circle.so/c/lekcje-programu/c02l02-eksplorowanie-i-omijanie-ograniczen'],
		'C02L03' => ['done' => false, 'desc' => 'Techniki pracy z GPT-3.5/GPT-4', 'scripts' => [], 'quiz' => 'https://zadania.aidevs.pl/quiz/c02l03_thhr', 'chat' => [], 'circle' => 'https://bravecourses.circle.so/c/lekcje-programu/c02l03-techniki-pracy-z-gpt-3-5-gpt-4'],
		'C02L04' => ['done' => false, 'desc' => 'Praca z własnymi danymi', 'scripts' => [], 'quiz' => 'https://zadania.aidevs.pl/quiz/c02l04_frex', 'chat' => [], 'circle' => 'https://bravecourses.circle.so/c/lekcje-programu/c02l04-praca-z-wlasnymi-danymi'],
	];

	static function htmlMenu()
	{
		echo '<ul>';
		foreach (self::MENU_INDEX as $k => $row) {
			echo '<li>' . $k . ' — ' . (empty($row['done'])?'<i class="bi bi-circle"></i>':'<i class="bi bi-check-circle-fill"></i>') . ' '. $row['desc'];
			$chats = '';
			if (!empty($row['chat']) && is_array($row['chat'])) {
				foreach ($row['chat'] as $chat) {
					$arr_url = explode('/', trim(parse_url($chat['url'], PHP_URL_PATH), '/'));
					$main_url = array_pop($arr_url);
					$chats .= ', <a href="' . $chat['url'] . '" target="_blank" title="Zadanie z promptem: ' . htmlspecialchars($chat['desc']) . '">' . $main_url . ' <i class="bi bi-terminal"></i></a> ';
				}
			}
			echo $chats;
			if (!empty($row['quiz']))	echo ', <a href="' . $row['quiz'] . '" title="Quiz" target="_blank"><i class="bi bi-patch-question"></i></a>';
			if (!empty($row['scripts']) && is_array($row['scripts']))	{
				foreach ($row['scripts'] as $script) {
					echo ', <a href="' . $script . '.php" title="Zadanie z API" class="fw-bold">' . $script . ' <i class="bi bi-robot"></i></a>';
				}
			}
			if (!empty($row['circle']))	echo ', <a href="' . $row['circle'] . '" title="circle" target="_blank"><i class="bi bi-box-arrow-up-right"></i></a>';
			echo '</li>' . NL;
		}
		echo '</ul>';
	}

}


App::initApp();
App::htmlStart('AIDevs', 'Menu kursu AIDevs2');
echo 'APIKEY_AIDEVS: ' . App::getSafeApiKey(App::APIKEY_AIDEVS, 8) . '<br>' . NL;
echo 'APIKEY_OPENAI: ' . App::getSafeApiKey(App::APIKEY_OPENAI, 8) . '<br>' . NL;
echo '<hr>' . NL;

echo '<a href="https://bravecourses.circle.so/c/informacje" target="_blank">bravecourses.circle.so - Kurs AIDev2</a>' . '<br>' . NL;
echo '<a href="https://zadania.aidevs.pl/" target="_blank">zadania.aidevs.pl - Zadania kontrolne</a>' . '<br>' . NL;
echo '<hr>' . NL;


Index::htmlMenu();
App::htmlEnd();

?>
