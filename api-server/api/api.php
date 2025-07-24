<?php
    include("../functions.php");
    /*$url=$_SERVER['REQUEST_URI'];
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]='Status: ERROR';
	$output[]='MSG: System Disabled';
	$output[]='Action: None';
	//log_error($_SERVER['REMOTE_ADDR'],"SYSTEM DISABLED","SYSTEM DISABLED: $endPoint",$url,"api.php");*/
	$url=$_SERVER['REQUEST_URI'];
	$path = parse_url($url, PHP_URL_PATH);
	$pathComponents = explode("/", trim($path, "/"));
	$endPoint=$pathComponents[1];
	switch($endPoint)
	{
		case "add_equipment":
			$did = $_REQUEST['did'];
			$mid = $_REQUEST['mid'];
			$sn = $_REQUEST['sn'];
			include("add_equipment.php");
			break;
		case "add_device":
			$dname = urldecode($_REQUEST['dname']);
			include("add_device.php");
			break;
		case "add_manufacturer":
			$mname = urldecode($_REQUEST['mname']);
			include("add_manufacturer.php");
			break;
		case "search_equipment":
			$dname = urldecode($_REQUEST['dname']);
			$mname = urldecode($_REQUEST['mname']);
			$sn = $_REQUEST['sn'];
			$inactive = $_REQUEST['inactive'];
//			$page = $_REQUEST['pg'];
			include("search_equipment.php");
			break;
		case "view_device":
			// need did, returns 1 device object
			$did = $_REQUEST['did'];
			include("view_device.php");
			break;
		case "view_manufacturer":
			// need mid, returns 1 manufacturer object
			$mid = $_REQUEST['mid'];
			include("view_manufacturer.php");
			break;	
		case "view_equipment":
			// needs eid, returns 1 equipment object
			$eid = $_REQUEST['eid'];
			include("view_equipment.php");
			break;
		case "modify_device":
			$did = $_REQUEST['did'];
			$newName = urldecode($_REQUEST['dname']);
			$newStatus = $_REQUEST['status'];
			include("modify_device.php");
			break;
		case "modify_manufacturer":
			$mid = $_REQUEST['mid'];
			$newName = urldecode($_REQUEST['mname']);
			$newStatus = $_REQUEST['status'];
			include("modify_manufacturer.php");
			break;
		case "modify_equipment":
			$eid = $_REQUEST['eid'];
			$did = $_REQUEST['did'];
			$mid = $_REQUEST['mid'];
			$sn = $_REQUEST['sn'];
			$newStatus = $_REQUEST['status'];
			include("modify_equipment.php");
			break;
		case "query_device": 
			//needs dname, returns 1 device object
			$dname = urldecode($_REQUEST['dname']);
			include("query_device.php");
			break;
		case "query_manufacturer":
			//needs mname, returns 1 manufacturer object
			$mname = urldecode($_REQUEST['mname']);
			include("query_manufacturer.php");
			break;
		case "query_equipment": 
			//needs $sn, return 1 equipment object
			$sn = $_REQUEST['sn'];
			include("query_equipment.php");
			break;
		case "list_devices":
			$inactive = $_REQUEST['inactive'];
			include("list_devices.php");
			break;
		case "list_manufacturers":
			$inactive = $_REQUEST['inactive'];
			include("list_manufacturers.php");
			break;
		default:
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]='Status: ERROR';
			$output[]='MSG: Invalid or missing endpoint';
			$output[]='Action: None';
			$responseData=json_encode($output);
			// LOG ERROR TO DB
			echo $responseData;
			break;
	}
	die();

?>