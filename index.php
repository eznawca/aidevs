<?php
require 'app.lib.php';

class Index
{
	const MENU_INDEX = ['helloapi'];

	static function htmlMenu()
	{
		echo '<ul>';
		foreach (self::MENU_INDEX as $row) {
			echo '<li><a href="' . $row . '.php">' . $row . '</a></li>' . NL;
		}
		echo '</ul>';
	}

}


App::initApp();
App::htmlStart('AIDevs', 'Menu kursu AIDevs2');
echo 'APIKEY_AIDEVS: ' . App::getSafeApiKey(App::APIKEY_AIDEVS, 8) . '<br>' . NL;
echo 'APIKEY_OPENAI: ' . App::getSafeApiKey(App::APIKEY_OPENAI, 8) . '<br>' . NL;
Index::htmlMenu();
App::htmlEnd();

?>
