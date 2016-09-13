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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO libro (nro, fecha, autor, titulo, id_lug, id_edi, anio, edicion, volumen, paginas, compra, precio, donac, dl, id_pro, observacion) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['numero'], "int"),
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
                       GetSQLValueString($_POST['obs'], "text"));

  mysql_select_db($database_conex, $conex);
  $Result1 = mysql_query($insertSQL, $conex) or die(mysql_error());

  $insertGoTo = "libro.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

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
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/registrar.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Registrar Libros</title>
<!-- InstanceEndEditable -->
<link href="../css/style.css" rel="stylesheet" type="text/css" />

<!-- InstanceBeginEditable name="head" -->
<script src="../../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<script src="../../SpryAssets/SpryValidationTextarea.js" type="text/javascript"></script>
<script src="../../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<link href="../../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="../../SpryAssets/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
<link href="../../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<!-- InstanceEndEditable -->

</head>

<body>
<div id="container">
  <div id="header">
    <h1><!-- InstanceBeginEditable name="Título" -->Registrar Libros<!-- InstanceEndEditable --></h1>
  </div>
  <div id="sideheader"></div>
  <div id="left_column">
    <div class="left_column_boxes">
      <h4>Navegación</h4>
      <div id="navcontainer">
        <ul id="navlist">
          <li></li>
          <li><a href="../inicio.php">Inicio</a></li>
          <li><a href="libro.php">Registrar</a></li>
          <li><a href="../buscar/numero.php">Buscar</a></li>
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
        <li><a href="libro.php">Registrar Libro</a></li>
        <li><a href="lugar.php">Registrar Lugar</a></li>
        <li><a href="editorial.php">Registrar editorial</a></li>
        <li><a href="procedencia.php">Registrar procedencia</a></li>
      </ul>
      <div style="clear: left"></div>
    </div>
    <div id="mostrar"><!-- InstanceBeginEditable name="Contenido2" -->
      <form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?>">
      <table>
            <tr>
              <th align="right" valign="top" scope="row">Número:</th>
            <td><span id="vnumero">
            <input name="numero" type="text" class="fields_contact_us" id="numero" />
            <span class="textfieldRequiredMsg">Se necesita un valor.</span><span class="textfieldInvalidFormatMsg">Formato no válido.</span></span></td>
            </tr>
            <tr>
              <th align="right" valign="top" scope="row">Lugar:</th>
              <td><span id="vlugar">
                <select name="lugar" id="lugar">
                  <option value="">Seleccione...</option>
                  <?php
do {  
?>
                  <option value="<?php echo $row_lugar['id']?>"><?php echo $row_lugar['nombre']?></option>
                  <?php
} while ($row_lugar = mysql_fetch_assoc($lugar));
  $rows = mysql_num_rows($lugar);
  if($rows > 0) {
      mysql_data_seek($lugar, 0);
	  $row_lugar = mysql_fetch_assoc($lugar);
  }
?>
                </select>
              <span class="selectRequiredMsg">Seleccione un elemento.</span></span></td>
            </tr>
            <tr>
              <th align="right" valign="top" scope="row">Editorial:</th>
              <td><span id="veditorial">
                <select name="editorial" id="editorial">
                  <option value="">Seleccione...</option>
                  <?php
do {  
?>
                  <option value="<?php echo $row_editorial['id']?>"><?php echo $row_editorial['nombre']?></option>
                  <?php
} while ($row_editorial = mysql_fetch_assoc($editorial));
  $rows = mysql_num_rows($editorial);
  if($rows > 0) {
      mysql_data_seek($editorial, 0);
	  $row_editorial = mysql_fetch_assoc($editorial);
  }
?>
                </select>
              <span class="selectRequiredMsg">Seleccione un elemento.</span></span></td>
            </tr>
            <tr>
              <th align="right" valign="top" scope="row">Procedencia:</th>
              <td><span id="vprocedencia">
                <select name="procedencia" id="procedencia">
                  <option value="">Seleccione...</option>
                  <?php
do {  
?>
                  <option value="<?php echo $row_procedencia['id']?>"><?php echo $row_procedencia['nombre']?></option>
                  <?php
} while ($row_procedencia = mysql_fetch_assoc($procedencia));
  $rows = mysql_num_rows($procedencia);
  if($rows > 0) {
      mysql_data_seek($procedencia, 0);
	  $row_procedencia = mysql_fetch_assoc($procedencia);
  }
?>
                </select>
              <span class="selectRequiredMsg">Seleccione un elemento.</span></span></td>
            </tr>
            <tr>
              <th align="right" valign="top" scope="row">Fecha:</th>
              <td><span id="vfecha">
              <input name="fecha" type="text" class="fields_contact_us" id="fecha" />
              <span class="textfieldRequiredMsg">Se necesita un valor.</span><span class="textfieldInvalidFormatMsg">Formato no válido.</span></span></td>
            </tr>
            <tr>
              <th align="right" valign="top" scope="row">Autor:</th>
            <td><span id="vautor">
                <input name="autor" type="text" class="fields_contact_us" id="autor" />
              <span class="textfieldRequiredMsg">Se necesita un valor.</span></span></td>
            </tr>
            <tr>
              <th align="right" valign="top" scope="row">Título:</th>
            <td><span id="vtitulo">
                <textarea name="titulo" id="titulo" cols="45" rows="3"></textarea>
              <span class="textareaRequiredMsg">Se necesita un valor.</span></span></td>
            </tr>
            <tr>
              <th align="right" valign="top" scope="row">Año:</th>
              <td><span id="vanio">
              <input name="anio" type="text" class="fields_contact_us" id="anio" />
              <span class="textfieldRequiredMsg">Se necesita un valor.</span><span class="textfieldInvalidFormatMsg">Formato no válido.</span><span class="textfieldMinCharsMsg">Menor a 4 números.</span><span class="textfieldMaxCharsMsg">Mayor a 4 números.</span></span></td>
            </tr>
            <tr>
              <th align="right" valign="top" scope="row">Edicion:</th>
              <td><input name="edicion" type="text" class="fields_contact_us" id="edicion" /></td>
            </tr>
            <tr>
              <th align="right" valign="top" scope="row">Volumen:</th>
              <td><input name="volumen" type="text" class="fields_contact_us" id="volumen" /></td>
            </tr>
            <tr>
              <th align="right" valign="top" scope="row">Páginas:</th>
              <td><input name="paginas" type="text" class="fields_contact_us" id="paginas" /></td>
            </tr>
            <tr>
              <th align="right" valign="top" scope="row">Precio:</th>
              <td><input name="precio" type="text" class="fields_contact_us" id="precio" /></td>
            </tr>
            <tr>
              <th align="right" valign="top" scope="row">Selección:</th>
              <td><input type="checkbox" name="compra" id="compra" />
              <label for="compra">Compra</label><br />
                <input type="checkbox" name="donac" id="donac" />
              <label for="donac">Donación</label><br />
              <input type="checkbox" name="dl" id="dl" />
              <label for="dl">Dl</label></td>
            </tr>
            <tr>
              <th align="right" valign="top" scope="row">Observación:</th>
              <td><textarea name="obs" id="obs" cols="45" rows="3"></textarea></td>
            </tr>
            <tr>
              <th align="right" valign="top" scope="row">&nbsp;</th>
              <td><input type="submit" class="submit_button" value="Registrar Libro" /></td>
            </tr>
          </table>
      <input type="hidden" name="MM_insert" value="form1" />
      </form>
        <script type="text/javascript">
<!--
var sprytextfield1 = new Spry.Widget.ValidationTextField("vautor");
var sprytextarea1 = new Spry.Widget.ValidationTextarea("vtitulo");
var sprytextfield2 = new Spry.Widget.ValidationTextField("vanio", "integer", {minChars:4, maxChars:4});
var sprytextfield3 = new Spry.Widget.ValidationTextField("vfecha", "date", {format:"yyyy-mm-dd", hint:"aaaa-mm-dd"});
var sprytextfield4 = new Spry.Widget.ValidationTextField("vnumero", "integer");
var spryselect1 = new Spry.Widget.ValidationSelect("vlugar");
var spryselect2 = new Spry.Widget.ValidationSelect("veditorial");
var spryselect3 = new Spry.Widget.ValidationSelect("vprocedencia");
//-->
        </script>
      <!-- InstanceEndEditable --></div>
  </div>
  <div id="footer">Copyrigth (c) Corporation</div>
</div>
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($lugar);

mysql_free_result($editorial);

mysql_free_result($procedencia);
?>
