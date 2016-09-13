<?php require_once('../../Connections/conex.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "Admin,Private";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "../index.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
  $MM_referrer .= "?" . $QUERY_STRING;
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_libro = 30;
$pageNum_libro = 0;
if (isset($_GET['pageNum_libro'])) {
  $pageNum_libro = $_GET['pageNum_libro'];
}
$startRow_libro = $pageNum_libro * $maxRows_libro;

mysql_select_db($database_conex, $conex);
$query_libro = "SELECT nro, fecha, autor, titulo FROM libro ORDER BY nro DESC";
$query_limit_libro = sprintf("%s LIMIT %d, %d", $query_libro, $startRow_libro, $maxRows_libro);
$libro = mysql_query($query_limit_libro, $conex) or die(mysql_error());
$row_libro = mysql_fetch_assoc($libro);

if (isset($_GET['totalRows_libro'])) {
  $totalRows_libro = $_GET['totalRows_libro'];
} else {
  $all_libro = mysql_query($query_libro);
  $totalRows_libro = mysql_num_rows($all_libro);
}
$totalPages_libro = ceil($totalRows_libro/$maxRows_libro)-1;

$queryString_libro = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_libro") == false && 
        stristr($param, "totalRows_libro") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_libro = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_libro = sprintf("&totalRows_libro=%d%s", $totalRows_libro, $queryString_libro);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/reportes.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Reportes</title>
<!-- InstanceEndEditable -->
<link href="../css/style.css" rel="stylesheet" type="text/css" />

<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->

</head>

<body>
<div id="container">
  <div id="header">
    <h1><!-- InstanceBeginEditable name="Título" -->Reportes<!-- InstanceEndEditable --></h1>
  </div>
  <div id="sideheader"></div>
  <div id="left_column">
    <div class="left_column_boxes">
      <h4>Navegación</h4>
      <div id="navcontainer">
        <ul id="navlist">
          <li></li>
          <li><a href="../inicio.php">Inicio</a></li>
          <li><a href="../registrar/libro.php">Registrar</a></li>
          <li><a href="../buscar/numero.php">Buscar</a></li>
          <li><a href="todo.php">Reportes</a></li>
          <li><a href="#">About us </a></li>
          <li><a href="../logout.php">Salir</a></li>
        </ul>
      </div>
    </div>
  </div>
  <div id="content">
    <div id="listarH">
      <ul>
        <li><a href="todo.php">Reporte de todo</a></li>
        <li><a href="#">Reporte por fecha</a></li>
        <li><a href="excel.php">Reporte en Excel</a></li>
        <li><a href="#">Reporte</a></li>
      </ul>
      <div style="clear: left"></div>
    </div>
    <div id="mostrar"><!-- InstanceBeginEditable name="Contenido2" -->
      <table border="1" align="center" cellspacing="0">
        <tr>
          <th scope="col">Número</th>
          <th scope="col">Autor</th>
          <th scope="col">Título</th>
          <th scope="col">Fecha</th>
          <th scope="col">&nbsp;</th>
          <th scope="col">&nbsp;</th>
        </tr>
        <?php do { ?>
          <tr>
            <td><?php echo $row_libro['nro']; ?></td>
            <td><?php echo $row_libro['autor']; ?></td>
            <td><?php echo $row_libro['titulo']; ?></td>
            <td><?php echo $row_libro['fecha']; ?></td>
            <td><a href="../buscar/editar.php?nro=<?php echo $row_libro['nro']; ?>"><img src="../images/edit.png" width="22" height="22" alt="Editar" /></a></td>
            <td><a href="../buscar/borrar.php?nro=<?php echo $row_libro['nro']; ?>"><img src="../images/trash.png" width="22" height="22" alt="Borrar" /></a></td>
          </tr>
          <?php } while ($row_libro = mysql_fetch_assoc($libro)); ?>
      </table>
      <table width="80%" align="center">
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td width="33%"><?php if ($pageNum_libro > 0) { // Show if not first page ?>
              <a href="<?php printf("%s?pageNum_libro=%d%s", $currentPage, max(0, $pageNum_libro - 1), $queryString_libro); ?>">Anterior</a>
              <?php } // Show if not first page ?></td>
          <td width="33%" align="center">&nbsp;<?php echo min($startRow_libro + $maxRows_libro, $totalRows_libro) ?> de <?php echo $totalRows_libro ?></td>
          <td width="33%" align="right"><?php if ($pageNum_libro < $totalPages_libro) { // Show if not last page ?>
  <a href="<?php printf("%s?pageNum_libro=%d%s", $currentPage, min($totalPages_libro, $pageNum_libro + 1), $queryString_libro); ?>">Siguiente</a>
  <?php } // Show if not last page ?></td>
        </tr>
      </table>
    <!-- InstanceEndEditable --></div>
  </div>
  <div id="footer">Copyrigth (c) Corporation</div>
</div>
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($libro);
?>
