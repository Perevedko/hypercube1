gate2.* - это разные компоненты одного парсера альбомов
gate21.* - это разные компоненты одного парсера альбомов закрытых групп
gate_wall2.* - это разные компоненты одного парсера стен
gate_users.* - компоненты парсера для ид-шок юзеров
gate_finish.php - скрипт для создания дампов
gate_*_shell.php - реализации парсеров для запуска непосредственно с консоли
parser_cfg.php - настройки (пока только параметры mysql-соединений)
parser_tools.php - куча функций для парсина
sazha_*.php - инструменты для работы с сажей

Настоятельно рекомендую использовать .htaccess с таким содержанием (AuthUserFile надо подправить):
AuthType Basic
AuthName "Password Protected Area"
AuthUserFile I:/xampp/htdocs3/.htpasswd
Require valid-user