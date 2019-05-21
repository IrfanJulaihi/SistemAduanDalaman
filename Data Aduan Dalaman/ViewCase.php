<?php require_once('../Connections/Connection1.php'); ?>
<?php
session_start();
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

$colname_ViewCase = "-1";
if (isset($_GET['NoRujukan'])) {
  $colname_ViewCase = $_GET['NoRujukan'];
}
mysql_select_db($database_Connection1, $Connection1);
$query_ViewCase = sprintf("SELECT * FROM aduan WHERE NoRujukan = %s ORDER BY NoRujukan ASC", GetSQLValueString($colname_ViewCase, "text"));
$ViewCase = mysql_query($query_ViewCase, $Connection1) or die(mysql_error());
$row_ViewCase = mysql_fetch_assoc($ViewCase);
$totalRows_ViewCase = mysql_num_rows($ViewCase);
//Testing new line

$colname_ViewAduan = "-1";
if (isset($_SESSION['Username'])) {
  $colname_ViewAduan = $_SESSION['Username'];
}
mysql_select_db($database_Connection1, $Connection1);
$query_ViewAduan = sprintf("SELECT *,tindakandirujuk.PegawaiDirujuk FROM aduan INNER JOIN tindakandirujuk on tindakandirujuk.NoRujukan =aduan.NoRujukan WHERE PegawaiDirujuk = %s", GetSQLValueString($colname_ViewAduan, "text"));
$ViewAduan = mysql_query($query_ViewAduan, $Connection1) or die(mysql_error());
$row_ViewAduan = mysql_fetch_assoc($ViewAduan);
$totalRows_ViewAduan = mysql_num_rows($ViewAduan);

$colname_UserAccount = "-1";

mysql_select_db($database_Connection1, $Connection1);
$query_UserAccount = sprintf("SELECT * FROM useraccount WHERE Username = %s", GetSQLValueString($colname_ViewAduan, "text"));
$UserAccount = mysql_query($query_UserAccount, $Connection1) or die(mysql_error());
$row_UserAccount = mysql_fetch_assoc($UserAccount);
$totalRows_UserAccount = mysql_num_rows($UserAccount);



//Variable to route it to the pegawai bertanggugjawab
$_SESSION['Name']=$row_UserAccount['Name'];

//SQL to merge pegawai aduan
$query_MergePegawaiAduan=sprintf("SELECT * from tindakandirujuk 
INNER JOIN aduan
ON aduan.NoRujukan = tindakandirujuk.NoRujukan where PegawaiDirujuk=%s", GetSQLValueString($_SESSION['Name'], "text"));
$Recordset3= mysql_query($query_MergePegawaiAduan, $Connection1) or die(mysql_error());
$row_Recordset3 = mysql_fetch_assoc($Recordset3);
$totalRows_Recordset1 = mysql_num_rows($Recordset3);

mysql_select_db($database_Connection1, $Connection1);
$query_ViewAduan = sprintf("SELECT * from aduan WHERE PIC = %s", GetSQLValueString($row_UserAccount['ID'], "text"));
$ViewAduan = mysql_query($query_ViewAduan, $Connection1) or die(mysql_error());
$row_ViewAduan = mysql_fetch_assoc($ViewAduan);
//Variable to show if they are any records for the person in charge
$totalRows = mysql_num_rows($ViewAduan);
//SQL to merge kategori aduan
$query_MergekategoriAduan=sprintf("SELECT * from aduan INNER JOIN kategoriaduan ON kategoriaduan.IDKategoriAduan = aduan.Category where NoRujukan=%s",GetSQLValueString($colname_ViewCase,"text"));
$KategoriAduan= mysql_query($query_MergekategoriAduan, $Connection1) or die(mysql_error());
$row_kategoriAduan = mysql_fetch_assoc($KategoriAduan);


//SQL to merge sub-kategori aduan
$query_MergeSubkategoriAduan=sprintf("SELECT * from aduan
INNER JOIN subkategoriaduan
ON subkategoriaduan.ID=aduan.SubCategory where NoRujukan=%s",GetSQLValueString($colname_ViewCase,"text"));
$SubKategoriAduan= mysql_query($query_MergeSubkategoriAduan, $Connection1) or die(mysql_error());
$row_SubkategoriAduan = mysql_fetch_assoc($SubKategoriAduan);

//SQL to merge aduan with pegawai dirujuk
mysql_select_db($database_Connection1, $Connection1);
$query_PegawaiDirujuk = sprintf("SELECT * from aduan INNER JOIN useraccount ON useraccount.ID = aduan.PIC where PIC =%s", GetSQLValueString($row_UserAccount['ID'], "text"));
$PegawaiDirujuk = mysql_query($query_PegawaiDirujuk, $Connection1) or die(mysql_error());
$row_PegawaiDirujuk = mysql_fetch_assoc($PegawaiDirujuk);


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>View Case</title>
<link href="../css/tableView.css" rel="stylesheet" type="text/css" >
<script src="https://cdnjs.cloudflare.com/ajax/libs/push.js/0.0.11/push.min.js"></script>
<script type="text/javascript" src="../Admin/assets/js/bootstrap.js"></script>
<script type="text/javascript" src="../Admin/assets/js/jquery-1.11.2.min.js"></script>
<script type="text/javascript" src="../Admin/assets/js/bootstrap-table.js"></script>
<link href="../Admin/assets/css/fresh-bootstrap-table.css" rel="stylesheet" />
<link  rel="stylesheet" href="../css/styles.css" rel="stylesheet" type="text/css" >
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
<link href='https://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="../Admin/assets/css/menubar.css">

<script>
function addDirujuk(){
	
  var table = document.getElementById("myTable");
  var row = table.insertRow(14);
  
  var cell1 = row.insertCell(0);
    cell1.innerHTML = "Tindakan dirujuk";
 var cell1 = row.insertCell(1);
 document.getElementById('buttonTindakan').style.visibility='hidden';


  cell1.innerHTML = "<textarea id='textarea2'>";
  

}
</script>

<script>
// Set the date we're counting down to
var countDownDate = new Date("Jul 12, 2019 17:00:25").getTime();

// Update the count down every 1 second
var x = setInterval(function() {

  // Get today's date and time
  var now = new Date().getTime();
    
  // Find the distance between now and the count down date
  var distance = countDownDate - now;
    
  // Time calculations for days, hours, minutes and seconds
  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  var seconds = Math.floor((distance % (1000 * 60)) / 1000);
    
  // Output the result in an element with id="demo"
  document.getElementById("demo").innerHTML = days + "d " + hours + "h "
  + minutes + "m " + seconds + "s ";
    
  // If the count down is over, write some text 
  if (distance < 0) {
    clearInterval(x);
    document.getElementById("demo").innerHTML = "EXPIRED";
  }
}, 1000);
</script>
</head>

<body>

</html>
<?php
mysql_free_result($ViewCase);
?>
<div class="topnav" id="myTopnav">
  <a style=" background-color:#0FED56;">Sistem Aduan Dalaman DBKU</a>
  <a href="DADD2019.php" >Home</a>

  
  <div class="dropdown">
    <button class="dropbtn">Aduan Management 
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
    <a href="#" onclick="showModal()">View Aduan
</a>
     
  
    </div>
  </div> 
  <a href="#about">About</a>
    <a href="<?php echo $logoutAction ?>" class="dropbtn">Logout</a>
  <a href="javascript:void(0);" style="font-size:15px;" class="icon" onclick="myFunction()">&#9776;</a>
</div>



<label><center><?php echo $row_ViewCase['NoRujukan'] ?></center></label><br>

<table id="myTable" border="1" align="center" style="width: 400px" cellspacing="3" class="paleBlueRows">
<thead>
  <th colspan="2">Aduan</th>
  </thead>
  <tr>
 
    <td>Nama Pengadu </td>
    <td style="color: #4E4E4E"><?php echo $row_ViewCase['NamaPengadu'] ?></td>
	  
  </tr>
  <tr>
	  
	<td>No telefon </td>
    <td style="color: #4E4E4E"><?php echo $row_ViewCase['NoTelefon'] ?></td>
  
  </tr>
	<thead>
  <th colspan="2">Kawasan</th>
  </thead>
  <tr>
 
    <td>Kawasan Aduan </td>
    <td style="color: #4E4E4E"><?php echo $row_ViewCase['KawasanAduan'] ?></td>
	  
	  
  </tr>
	<thead>
  <th colspan="2">Maklumat Aduan</th>
  </thead>
  <tr>
   
	  <td>Kategori Aduan </td>
    <td style="color: #4E4E4E"><?php echo $row_kategoriAduan['NamaAduan'] ?></td>
	  
  </tr>
  <tr>
	  
	  <td>Sub Kategori Aduan </td>
    <td style="color: #4E4E4E"><?php echo $row_ViewAduan['SubCategory']?></td>
	  
  </tr>
   <tr>
	  
	  <td>Maklumat Aduan </td>
    <td style="color: #4E4E4E"><?php echo $row_ViewCase['MaklumatAduan'] ?></td>
	  
  </tr>
  <tr>
 
    <td>Tarikh Aduan Diterima </td>
    <td style="color: #4E4E4E"><?php echo date("d-m-Y  ", strtotime($row_ViewCase['TimeSubmit']) ); ?></td>
	  
	  
  </tr>
  <tr>
  
    <td>Masa Aduan Diterima </td>
    <td style="color: #4E4E4E"><?php echo date("h:i:sa ", strtotime($row_ViewCase['TimeSubmit']) ); ?></td>
	  
	  
  </tr>
  <tr>
 
    <td>Status Aduan </td>
    <td><?php echo $row_ViewCase['StatusAduan'] ?>    
   
    <input type="button" value="Proceed">
</td>
  </tr>
   <tr>
   <td>Pegawai dirujuk
   <td><?php echo $row_PegawaiDirujuk['Name'] ?>
    <tr>
 
    <td>Tindakan dirujuk </td>
    <td>
     <textarea readonly="readonly"><?php echo $row_Recordset3['TindakanDirujuk'] ?>   </textarea> <input id="buttonTindakan" type="button" value="+ Tindakan" onClick="addDirujuk()" class="button" id="plus" >
   </td>


 
  </tr>
  
  <tr>
     <td align="center" colspan="2"><input type="button" value="confirm"></td>
  </tr>
</table>
</body>
</html>