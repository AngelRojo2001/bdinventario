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

$colname_libro = "-1";
if (isset($_POST['autor'])) {
  $colname_libro = $_POST['autor'];
}
mysql_select_db($database_conex, $conex);
$query_libro = sprintf("SELECT nro, fecha, autor, titulo FROM libro WHERE autor LIKE %s ORDER BY autor ASC", GetSQLValueString("%" . $colname_libro . "%", "text"));
$libro = mysql_query($query_libro, $conex) or die(mysql_error());
$row_libro = mysql_fetch_assoc($libro);
$totalRows_libro = mysql_num_rows($libro);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/buscar.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Buscar Autor</title>
<!-- InstanceEndEditable -->
<link href="../css/style.css" rel="stylesheet" type="text/css" />

<!-- InstanceBeginEditable name="head" -->
<script src="../../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<link href="../../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<!-- InstanceEndEditable -->

</head>

<body>
<div id="container">
  <div id="header">
    <h1><!-- InstanceBeginEditable name="Título" -->Buscar Autor<!-- InstanceEndEditable --></h1>
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
          <li><a href="numero.php">Buscar</a></li>
          <li><a href="../reportes/todo.php">Reportes</a></li>
          <li><a href="#">About us </a></li>
          <li><a href="../logout.php">Salir</a></li>
        </ul>
      </div>
    </div>
  </div>
  <div id="content">
    <div id="listarH">
      <ul>
        <li><a href="numero.php">Buscar por Número</a></li>
        <li><a href="autor.php">Buscar por Autor</a></li>
        <li><a href="titulo.php">Buscar por Título</a></li>
        <li><a href="fecha.php">Buscar por Fecha</a></li>
      </ul>
      <div style="clear: left"></div>
    </div>
    <div id="mostrar"><!-- InstanceBeginEditable name="Contenido2" -->
      <form id="form1" name="form1" method="post" action="autor.php">
        <table align="center">
          <tr>
            <th>Autor:</th>
            <td><span id="vautor">
              <input name="autor" type="text" class="fields_contact_us" id="autor" />
              <br />
              <span class="textfieldRequiredMsg">Se necesita un valor.</span></span></td>
            <td><input name="button" type="submit" class="submit_button" id="button" value="Buscar" /></td>
          </tr>
        </table>
      </form>
      <?php if ($totalRows_libro > 0) { // Show if recordset not empty ?>
        <p>Se encontraron <strong><?php echo $totalRows_libro ?></strong> resultados</strong>:</p>
        <table border="1" align="center" cellspacing="0" class="spacio">
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
              <td><a href="editar.php?nro=<?php echo $row_libro['nro']; ?>"><img src="../images/edit.png" width="22" height="22" alt="Editar" /></a></td>
              <td><a href="borrar.php?nro=<?php echo $row_libro['nro']; ?>"><img src="../images/trash.png" width="22" height="22" alt="Borrar" /></a></td>
            </tr>
            <?php } while ($row_libro = mysql_fetch_assoc($libro)); ?>
        </table>
        <?php } // Show if recordset not empty ?>
      <?php if (isset($_POST['button'])) { ?>
        <?php if ($totalRows_libro == 0) { // Show if recordset empty ?>
          <p>No se encontraron ningun resultado.</p>
        <?php } // Show if recordset empty ?>
  <?php } ?>
<script type="text/javascript">
<!--
var sprytextfield1 = new Spry.Widget.ValidationTextField("vautor");
//-->
      </script>
    <!-- InstanceEndEditable --></div>
  </div>
  <div id="footer">Copyrigth (c) Corporation</div>
</div>
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($libro);
?>
