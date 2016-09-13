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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE libro SET fecha=%s, autor=%s, titulo=%s, id_lug=%s, id_edi=%s, anio=%s, edicion=%s, volumen=%s, paginas=%s, compra=%s, precio=%s, donac=%s, dl=%s, id_pro=%s WHERE nro=%s",
                       GetSQLValueString($_POST['fecha'], "date"),
                       GetSQLValueString($_POST['autor'], "text"),
                       GetSQLValueString($_POST['titulo'], "text"),
                       GetSQLValueString($_POST['lugar'], "int"),
                       GetSQLValueString($_POST['editorial'], "int"),
                       GetSQLValueString($_POST['anio'], "date"),
                       GetSQLValueString($_POST['edicion'], "text"),
                       GetSQLValueString($_POST['volumen'], "text"),
                       GetSQLValueString($_POST['paginas'], "int"),
                       GetSQLValueString(isset($_POST['compra']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString($_POST['precio'], "int"),
                       GetSQLValueString(isset($_POST['donac']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['dl']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString($_POST['procedencia'], "int"),
                       GetSQLValueString($_POST['principal'], "int"));

  mysql_select_db($database_conex, $conex);
  $Result1 = mysql_query($updateSQL, $conex) or die(mysql_error());

  $updateGoTo = "numero.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_libro = "-1";
if (isset($_GET['nro'])) {
  $colname_libro = $_GET['nro'];
}
mysql_select_db($database_conex, $conex);
$query_libro = sprintf("SELECT * FROM libro WHERE nro = %s", GetSQLValueString($colname_libro, "int"));
$libro = mysql_query($query_libro, $conex) or die(mysql_error());
$row_libro = mysql_fetch_assoc($libro);
$totalRows_libro = mysql_num_rows($libro);

mysql_select_db($database_conex, $conex);
$query_lugar = "SELECT * FROM lugar ORDER BY nombre ASC";
$lugar = mysql_query($query_lugar, $conex) or die(mysql_error());
$row_lugar = mysql_fetch_assoc($lugar);
$totalRows_lugar = mysql_num_rows($lugar);

mysql_select_db($database_conex, $conex);
$query_editorial = "SELECT * FROM editorial ORDER BY nombre ASC";
$editorial = mysql_query($query_editorial, $conex) or die(mysql_error());
$row_editorial = mysql_fetch_assoc($editorial);
$totalRows_editorial = mysql_num_rows($editorial);

mysql_select_db($database_conex, $conex);
$query_procedencia = "SELECT * FROM procedencia ORDER BY nombre ASC";
$procedencia = mysql_query($query_procedencia, $conex) or die(mysql_error());
$row_procedencia = mysql_fetch_assoc($procedencia);
$totalRows_procedencia = mysql_num_rows($procedencia);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/buscar.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Editar</title>
<!-- InstanceEndEditable -->
<link href="../css/style.css" rel="stylesheet" type="text/css" />

<!-- InstanceBeginEditable name="head" -->
<script src="../../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<script src="../../SpryAssets/SpryValidationTextarea.js" type="text/javascript"></script>
<link href="../../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="../../SpryAssets/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
<!-- InstanceEndEditable -->

</head>

<body>
<div id="container">
  <div id="header">
    <h1><!-- InstanceBeginEditable name="Título" -->Editar<!-- InstanceEndEditable --></h1>
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
    <?php if ($totalRows_libro > 0) { // Show if recordset not empty ?>
  <form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?>">
    <table>
      <tr>
        <th align="right" valign="top" scope="row">Número:</th>
        <td><span id="vnumero">
          <input name="numero" type="text" class="fields_contact_us" id="numero" value="<?php echo $row_libro['nro']; ?>" />
          <span class="textfieldRequiredMsg">Se necesita un valor.</span><span class="textfieldInvalidFormatMsg">Formato no válido.</span></span></td>
      </tr>
      <tr>
        <th align="right" valign="top" scope="row">Lugar:</th>
        <td><select name="lugar" id="lugar">
          <?php
do {  
?>
          <option value="<?php echo $row_lugar['id']?>"<?php if (!(strcmp($row_lugar['id'], $row_libro['id_lug']))) {echo "selected=\"selected\"";} ?>><?php echo $row_lugar['nombre']?></option>
          <?php
} while ($row_lugar = mysql_fetch_assoc($lugar));
  $rows = mysql_num_rows($lugar);
  if($rows > 0) {
      mysql_data_seek($lugar, 0);
	  $row_lugar = mysql_fetch_assoc($lugar);
  }
?>
          </select></td>
      </tr>
      <tr>
        <th align="right" valign="top" scope="row">Editorial:</th>
        <td><select name="editorial" id="editorial">
          <?php
do {  
?>
          <option value="<?php echo $row_editorial['id']?>"<?php if (!(strcmp($row_editorial['id'], $row_libro['id_edi']))) {echo "selected=\"selected\"";} ?>><?php echo $row_editorial['nombre']?></option>
          <?php
} while ($row_editorial = mysql_fetch_assoc($editorial));
  $rows = mysql_num_rows($editorial);
  if($rows > 0) {
      mysql_data_seek($editorial, 0);
	  $row_editorial = mysql_fetch_assoc($editorial);
  }
?>
          </select></td>
      </tr>
      <tr>
        <th align="right" valign="top" scope="row">Procedencia:</th>
        <td><select name="procedencia" id="procedencia">
          <?php
do {  
?>
          <option value="<?php echo $row_procedencia['id']?>"<?php if (!(strcmp($row_procedencia['id'], $row_libro['id_pro']))) {echo "selected=\"selected\"";} ?>><?php echo $row_procedencia['nombre']?></option>
          <?php
} while ($row_procedencia = mysql_fetch_assoc($procedencia));
  $rows = mysql_num_rows($procedencia);
  if($rows > 0) {
      mysql_data_seek($procedencia, 0);
	  $row_procedencia = mysql_fetch_assoc($procedencia);
  }
?>
          </select></td>
      </tr>
      <tr>
        <th align="right" valign="top" scope="row">Fecha:</th>
        <td><span id="vfecha">
          <input name="fecha" type="text" class="fields_contact_us" id="fecha" value="<?php echo $row_libro['fecha']; ?>" />
          <span class="textfieldRequiredMsg">Se necesita un valor.</span><span class="textfieldInvalidFormatMsg">Formato no válido.</span></span></td>
      </tr>
      <tr>
        <th align="right" valign="top" scope="row">Autor:</th>
        <td><span id="vautor">
          <input name="autor" type="text" class="fields_contact_us" id="autor" value="<?php echo $row_libro['autor']; ?>" />
          <span class="textfieldRequiredMsg">Se necesita un valor.</span></span></td>
      </tr>
      <tr>
        <th align="right" valign="top" scope="row">Título:</th>
        <td><span id="vtitulo">
          <textarea name="titulo" id="titulo" cols="45" rows="3"><?php echo $row_libro['titulo']; ?></textarea>
          <span class="textareaRequiredMsg">Se necesita un valor.</span></span></td>
      </tr>
      <tr>
        <th align="right" valign="top" scope="row">Año:</th>
        <td><span id="vanio">
          <input name="anio" type="text" class="fields_contact_us" id="anio" value="<?php echo $row_libro['anio']; ?>" />
          <span class="textfieldRequiredMsg">Se necesita un valor.</span><span class="textfieldInvalidFormatMsg">Formato no válido.</span><span class="textfieldMinCharsMsg">Menor a 4 números.</span><span class="textfieldMaxCharsMsg">Mayor a 4 números.</span></span></td>
      </tr>
      <tr>
        <th align="right" valign="top" scope="row">Edicion:</th>
        <td><input name="edicion" type="text" class="fields_contact_us" id="edicion" value="<?php echo $row_libro['edicion']; ?>" /></td>
      </tr>
      <tr>
        <th align="right" valign="top" scope="row">Volumen:</th>
        <td><input name="volumen" type="text" class="fields_contact_us" id="volumen" value="<?php echo $row_libro['volumen']; ?>" /></td>
      </tr>
      <tr>
        <th align="right" valign="top" scope="row">Páginas:</th>
        <td><input name="paginas" type="text" class="fields_contact_us" id="paginas" value="<?php echo $row_libro['paginas']; ?>" /></td>
      </tr>
      <tr>
        <th align="right" valign="top" scope="row">Precio:</th>
        <td><input name="precio" type="text" class="fields_contact_us" id="precio" value="<?php echo $row_libro['precio']; ?>" /></td>
      </tr>
      <tr>
        <th align="right" valign="top" scope="row">Selección:</th>
        <td><input <?php if (!(strcmp($row_libro['compra'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="compra" id="compra" />
          <label for="compra">Compra</label>
          <br />
          <input <?php if (!(strcmp($row_libro['donac'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="donac" id="donac" />
          <label for="donac">Donación</label>
          <br />
          <input <?php if (!(strcmp($row_libro['dl'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="dl" id="dl" />
          <label for="dl">Dl</label></td>
      </tr>
      <tr>
        <th align="right" valign="top" scope="row">Observación:</th>
        <td><textarea name="obs" id="obs" cols="45" rows="3"><?php echo $row_libro['observacion']; ?></textarea></td>
      </tr>
      <tr>
        <th align="right" valign="top" scope="row">&nbsp;</th>
        <td><input type="submit" class="submit_button" value="Editar Libro" /></td>
      </tr>
    </table>        
    <input name="principal" type="hidden" id="principal" value="<?php echo $row_libro['nro']; ?>" />
    <input type="hidden" name="MM_update" value="form1" />
  </form>
  <?php } // Show if recordset not empty ?>
<script type="text/javascript">
<!--
var sprytextfield2 = new Spry.Widget.ValidationTextField("vanio", "integer", {minChars:4, maxChars:4});
var sprytextarea1 = new Spry.Widget.ValidationTextarea("vtitulo");
var sprytextfield1 = new Spry.Widget.ValidationTextField("vautor");
var sprytextfield3 = new Spry.Widget.ValidationTextField("vfecha", "date", {format:"yyyy-mm-dd", hint:"aaaa-mm-dd"});
var sprytextfield4 = new Spry.Widget.ValidationTextField("vnumero", "integer");
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

mysql_free_result($lugar);

mysql_free_result($editorial);

mysql_free_result($procedencia);
?>
