<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_conex = "localhost";
$database_conex = "bdinventario";
$username_conex = "root";
$password_conex = "1618748";
$conex = mysql_pconnect($hostname_conex, $username_conex, $password_conex) or trigger_error(mysql_error(),E_USER_ERROR); 
?>