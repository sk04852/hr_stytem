<?php 
$servername="local host";
$username="root";
$password="";
$db="hr system";
$conn =new mysqli($servername,$username,$password,$db);
if($conn->connect-error){
	die("connectionfailed:"$conn->connect-eeror);
}
$user roles=$_Post['user roles'];
$Role_ID=$_Post[Role_ID];
$NAME=$_Post[Name];
$query="DELETE FROM user roles Where (Role_ID,NAME)VALUES('$Role_ID',$NAME)";
$results=$conn->query($query);
if($results==1){
	$response=array("status"=>"1","message"=>record succesfuly inserted");
}
	else
	{
		$response=array("status"=>"0","message"=>record not succesfuly inserted");
	}
	echo json_encode($response);
	
}

?>
