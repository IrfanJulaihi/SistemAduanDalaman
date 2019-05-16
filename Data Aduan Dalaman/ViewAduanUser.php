<?php require_once('../Connections/Connection1.php'); ?>
<?php
//initialize the session

  session_start();


// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "../index.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "2";
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
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
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

$colname_ViewAduan = "-1";
if (isset($_SESSION['Username'])) {
  $colname_ViewAduan = $_SESSION['Username'];
}
/*mysql_select_db($database_Connection1, $Connection1);
$query_ViewAduan = sprintf("SELECT *,tindakandirujuk.PegawaiDirujuk FROM aduan INNER JOIN tindakandirujuk on tindakandirujuk.NoRujukan =aduan.NoRujukan WHERE PegawaiDirujuk = %s", GetSQLValueString($colname_ViewAduan, "text"));
$ViewAduan = mysql_query($query_ViewAduan, $Connection1) or die(mysql_error());
$row_ViewAduan = mysql_fetch_assoc($ViewAduan);
$totalRows_ViewAduan = mysql_num_rows($ViewAduan);*/







$colname_UserAccount = "-1";

mysql_select_db($database_Connection1, $Connection1);
$query_UserAccount = sprintf("SELECT *,department.Abbreviation FROM useraccount INNER JOIN department on department.departmentID=useraccount.DepartmentID WHERE Username = %s ", GetSQLValueString($colname_ViewAduan, "text"));
$UserAccount = mysql_query($query_UserAccount, $Connection1) or die(mysql_error());
$row_UserAccount = mysql_fetch_assoc($UserAccount);
$totalRows_UserAccount = mysql_num_rows($UserAccount);
//Variable to route it to the pegawai bertanggugjawab
$_SESSION['Name']=$row_UserAccount['Name'];

mysql_select_db($database_Connection1, $Connection1);
$query_ViewAduan = sprintf("SELECT * from aduan WHERE PIC = %s", GetSQLValueString($row_UserAccount['ID'], "text"));
$ViewAduan = mysql_query($query_ViewAduan, $Connection1) or die(mysql_error());
$row_ViewAduan = mysql_fetch_assoc($ViewAduan);
//Variable to show if they are any records for the person in charge
$totalRows = mysql_num_rows($ViewAduan);

//SQL to merge pegawai aduan
$query_MergePegawaiAduan=sprintf("SELECT PegawaiDirujuk from tindakandirujuk 
INNER JOIN aduan
ON aduan.NoRujukan = tindakandirujuk.NoRujukan where PegawaiDirujuk=%s", GetSQLValueString($_SESSION['Name'], "text"));
$Recordset3= mysql_query($query_MergePegawaiAduan, $Connection1) or die(mysql_error());
$row_Recordset3 = mysql_fetch_assoc($Recordset3);
$totalRows_Recordset1 = mysql_num_rows($Recordset3);

//SQL to merge kategori aduan
$query_MergekategoriAduan=sprintf("SELECT * from kategoriaduan
INNER JOIN aduan
ON aduan.Category = kategoriaduan.IDKategoriAduan where category=%s",GetSQLValueString($row_ViewAduan['Category'],"text"));
$KategoriAduan= mysql_query($query_MergekategoriAduan, $Connection1) or die(mysql_error());
$row_kategoriAduan = mysql_fetch_assoc($KategoriAduan);






?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Sistem Aduan Dalaman DBKU</title>
<script type="text/javascript" src="../Admin/assets/js/bootstrap.js"></script>
<script type="text/javascript" src="../Admin/assets/js/jquery-1.11.2.min.js"></script>
<script type="text/javascript" src="../Admin/assets/js/bootstrap-table.js"></script>
<link href="../Admin/assets/css/fresh-bootstrap-table.css" rel="stylesheet" />
<link  rel="stylesheet" href="../css/styles.css" rel="stylesheet" type="text/css" >
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
<link href='https://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="../Admin/assets/css/menubar.css">

</head>



     <script>
function myFunction() {
  var x = document.getElementById("myTopnav");
  if (x.className === "topnav") {
    x.className += " responsive";
  } else {
    x.className = "topnav";
  }
  </script>
 
<body onload="showRecords()">
<div class="w3-container">




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




 


  
   
   
   
   
  
    <div align="center">

        <table id="fresh-table" class="table" >
    <thead style="color:green;">
 
      <th data-field="No">No.</th>
      <th data-field="NoRujukan">No Rujukan</th>
      <th  data-field="Kategori Aduan">Kategori Aduan</th>
      <th data-field="Jenis Aduan">Sub-Kategori Aduan</th>
      <th data-field="Kawasan Aduan<">Kawasan Aduan</th>
      <th data-field="Maklumat Aduan">Maklumat Aduan</th>
      
      
      <th data-field="Status Aduan">Status Aduan</th>
      <th data-field="Masa Aduan">Masa Aduan</th>

    
  
    </thead>
    <tbody>
         <?php $no=1;?>
      
    <?php do { ?>
    <tr>
    <td> <?php echo $no++; ?></td>
      <td> <a href="ViewCase.php?NoRujukan=<?php echo $row_ViewAduan['NoRujukan'];?>"><?php echo $row_ViewAduan['NoRujukan'];?></a></td>
      
        <td><?php echo $row_kategoriAduan['NamaAduan']; ?></td>
        <td></td>
        <td><?php echo $row_ViewAduan['KawasanAduan']; ?></td>
        <td><?php echo $row_ViewAduan['MaklumatAduan']; ?></td>
        
      
        <td><?php echo $row_ViewAduan['StatusAduan']; ?></td>
        <td><?php echo $row_ViewAduan['TimeSubmit']; ?></td>
        
     
    </tr>
    <?php } while ($row_ViewAduan = mysql_fetch_assoc($ViewAduan)); ?>
    </tbody>
  </table>
  <h3 id="showRecords" style="display:none">There are no records to show</h3>
 </div>

<script type="text/javascript">
        var $table = $('#fresh-table'),
            $alertBtn = $('#alertBtn'),
            full_screen = false;

        $().ready(function(){
            $table.bootstrapTable({
                toolbar: ".toolbar",

                showRefresh: false,
                search: true,
                showToggle: true,
                showColumns: false,
                pagination: true,
                striped: true,
                sortable: true,
                pageSize: 10,
                pageList: [8,10,25,50,100],

                formatShowingRows: function(pageFrom, pageTo, totalRows){
                    //do nothing here, we don't want to show the text "showing x of y from..."
                },
                formatRecordsPerPage: function(pageNumber){
                    return pageNumber + " rows visible";
                },
                icons: {
                    refresh: 'fa fa-refresh',
                    toggle: 'fa fa-th-list',
                    columns: 'fa fa-columns',
                    detailOpen: 'fa fa-plus-circle',
                    detailClose: 'fa fa-minus-circle'
                }
            });
        });

        $(function () {
            $alertBtn.click(function () {
                alert("You pressed on Alert");
            });
        });


        function operateFormatter(value, row, index) {
            return [
                '<a rel="tooltip" title="Like" class="table-action like" href="javascript:void(0)" title="Like">',
                    '<i class="fa fa-heart"></i>',
                '</a>',
                '<a rel="tooltip" title="Edit" class="table-action edit" href="javascript:void(0)" title="Edit">',
                    '<i class="fa fa-edit"></i>',
                '</a>',
                '<a rel="tooltip" title="Remove" class="table-action remove" href="javascript:void(0)" title="Remove">',
                    '<i class="fa fa-remove"></i>',
                '</a>'
            ].join('');
        }

        window.operateEvents = {
            'click .like': function (e, value, row, index) {
                alert('You click like icon, row: ' + JSON.stringify(row));
                console.log(value, row, index);
            },
            'click .edit': function (e, value, row, index) {
                console.log(value, row, index);
            },
            'click .remove': function (e, value, row, index) {
                alert('You click remove icon, row: ' + JSON.stringify(row));
                console.log(value, row, index);
            }
        };

    </script>
<script>
 function showRecords()
 {
	 var records="<?php echo $totalRows ?>";
	 
	 if(records=='0'){
		 document.getElementById("fresh-table").style.display="none";
		 document.getElementById("showRecords").style.display="block"
 }
 }
</script>

</body>
</html>
<?php
mysql_free_result($ViewAduan);

mysql_free_result($UserAccount);



?>
