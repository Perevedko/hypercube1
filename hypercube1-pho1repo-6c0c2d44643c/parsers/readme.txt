gate2.* - ��� ������ ���������� ������ ������� ��������
gate21.* - ��� ������ ���������� ������ ������� �������� �������� �����
gate_wall2.* - ��� ������ ���������� ������ ������� ����
gate_users.* - ���������� ������� ��� ��-��� ������
gate_finish.php - ������ ��� �������� ������
gate_*_shell.php - ���������� �������� ��� ������� ��������������� � �������
parser_cfg.php - ��������� (���� ������ ��������� mysql-����������)
parser_tools.php - ���� ������� ��� �������
sazha_*.php - ����������� ��� ������ � �����

������������ ���������� ������������ .htaccess � ����� ����������� (AuthUserFile ���� ����������):
AuthType Basic
AuthName "Password Protected Area"
AuthUserFile I:/xampp/htdocs3/.htpasswd
Require valid-user