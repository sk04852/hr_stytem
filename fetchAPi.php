<?php
header('content Type:application/json');

$conn=mysqli_connect("localhost","root",",","hr system")or 
die("connection failes");
$sql="select*from user roles";
$results=mysqli_query($conn,$sql)or die("sql query failes")
if (mysqli_num_rows($result)>0){
    $output=mysqli_fetchall($result,MYSQLI-ASSOC);
    echo json_encode($output);
}
else{
    echo json_encode(array('message=>'no record found''status=>false));

}
?>