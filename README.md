# AI_DEVS 2 - Zadania

## Wymagania
- PHP 5.6 i lepszy

## Uruchomienie
Skrypt przechowuje i pobiera sekrety ze zmiennych środowiskowych.

Ustaw zmienną środowiskową w konfiguracji Virtual Hosta za pomocą wpisu:
```
SetEnv AIDEVS_API_KEY 33c...0d9
```
- `.htaccess` w katalogu twojego projektu (Apache2, dla dowolnego systemu)
- `httpd-vhosts.conf` (dla Windows)
- `/etc/httpd/conf.d/..` (dla CentOS, Red Hat)
- `/etc/apache2/sites-available/..` (dla Debian, Ubuntu)

### Menu index.php
/index.php zawiera menu do wszystkich skryptów zadań