<html>
<head>
<meta charset="utf-8">
<title>Advanced Software Engineering</title>
<link href="../assets/css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/font-awesome.min.css">
<link rel="stylesheet" href="../assets/css/owl.carousel.css">
<link rel="stylesheet" href="../assets/css/owl.theme.default.min.css">

<!-- MAIN CSS -->
<link rel="stylesheet" href="../assets/css/templatemo-style.css">
</head>
<body>
<body id="top" data-spy="scroll" data-target=".navbar-collapse" data-offset="50">
     <!-- MENU -->
     <section class="navbar custom-navbar navbar-fixed-top" role="navigation">
          <div class="container">
               <div class="navbar-header">
                    <button class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                         <span class="icon icon-bar"></span>
                         <span class="icon icon-bar"></span>
                         <span class="icon icon-bar"></span>
                    </button>

                    <!-- lOGO TEXT HERE -->
                    <a href="#" class="navbar-brand">Search Result</a>
               </div>
               <!-- MENU LINKS -->
               <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-nav-first">
                         <li><a href="index.php" class="smoothScroll">Home</a></li>
						<li><a href="devices.php" class="smoothScroll">Devices</a></li>
						<li><a href="manufacturers.php" class="smoothScroll">Manufactures</a></li>
                         <li><a href="search.php" class="smoothScroll">Search Equipment</a></li>
						 <li class="active"><a href="search.php" class="smoothScroll">Search Result</a></li>
                         <li><a href="add.php" class="smoothScroll">Add Equipment</a></li>
                    </ul>
               </div>
          </div>
     </section>
 <!-- HOME -->
     <section id="home">
          </div>
     </section>
     <!-- FEATURE -->
     <section id="feature" style="display: flex;justify-content: center;justify-items: center;text-align: center;">
          <div class="container">
               <div class="row">
				   <?php
				   		include("../functions.php");
				   		if (isset($_POST['submit']) && $_POST['submit'] == "submit") {
				
							$dname = trim($_POST['device']);
							$mname = trim($_POST['manufacturer']);
							$sn = trim($_POST['serialnumber']);
							$inactive = trim($_POST['sm']);
							
							
							if (($dname == NULL) && ($mname == NULL) && ($sn == NULL)) {
								redirect("search.php?msg=null");
							}
							
							// Validate device name
							if ($dname!=NULL) {
								// Device name must be:
								//   less than 64 characters
								//   alpha-numeric
								//   may have spaces
								if (!preg_match('/^[a-zA-Z0-9\s]{1,64}$/', $dname)) {
									redirect("search.php?msg=invalidDevice");
								} 
							}
										
							// Validate manufacturername
							if ($mname!=NULL)
							{
								// Manufacturer name must be:
								//   less than 64 characters
								//   alpha-numeric
								//   spaces
								if (!preg_match('/^[a-zA-Z0-9\s]{1,64}$/', $mname)) {
									redirect("search.php?msg=invalidManufacturer");
								} 

							}
							
							// Serial number to be searched
							if ($sn!=NULL) {
								// S N - are optional
								// alpha-numeric up to 64 character
								if (!preg_match('/^S?N?-?[a-zA-Z0-9]{1,64}$/', $sn)) {
									redirect("search.php?msg=invalidSN");
								}
							}
							
							// making sure $inactive can only be either "Y/y" or "n"
							if (($inactive == NULL) || (!preg_match('/^[yY]$/', $inactive))) {
								$inactive = 'n';
							}
							
							$check = call_api("search_equipment", "dname=$dname&mname=$mname&sn=$sn&inactive=$inactive");
		
							if ($check == FALSE) {
								// error for unable to establish connection with API
								redirect("search.php?msg=apiError");
							}

							$result = json_decode($check, true);

							if ($result == NULL) {
								redirect("search.php?msg=error");
							}

							$resultPayload = explode("MSG: ", $result[1]);
							
							switch ($resultPayload[1]) {
								case "Missing search parameters": redirect("search.php?msg=null");
								case "Invalid device name format": redirect("search.php?msg=invalidDevice");
								case "Invalid manufacturer name format": redirect("search.php?msg=invalidManufacturer");
								case "Invalid serial number format": redirect("search.php?msg=invalidSN");
								case "There are no equipment matching the search parameters": redirect("search.php?msg=noMatch");
								default:
									$resultPayload[1] = json_decode($resultPayload[1], true);
									
									if (is_array($resultPayload[1])) {					
										echo "<div style='overflow-x:auto;'>";
										echo 	"<table style='width:100%; border-collapse: collapse;'>";
										echo		 "<tr>
														<th style='padding: 8px;'>Id</th>
														<th style='padding: 8px;'>Device</th>
														<th style='padding: 8px;'>Manufacturer</th>
														<th style='padding: 8px;'>Serial Number</th>
														<th style='padding: 8px;'>Status</th>
														<th style='padding: 8px;'></th>
														<th style='padding: 8px;'></th>
													</tr>";
												
										foreach($resultPayload[1] as $id=>$eqObject) {
											echo "<tr>";
											echo 	"<td style='padding: 8px;'>" . $eqObject['id'] . "</td>";
											echo 	"<td style='padding: 8px;'>" . $eqObject['device_name'] . "</td>";
											echo 	"<td style='padding: 8px;'>" . $eqObject['manufacturer_name'] . "</td>";
											echo 	"<td style='padding: 8px;'>" . $eqObject['serial_number'] . "</td>";
											echo 	"<td style='padding: 8px;'>" . $eqObject['status'] . "</td>";
											
											echo "<td style='padding: 8px;'><a href='modify.php?q=".$eqObject['id']."' style='display: inline-block; padding: 5px 10px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px; border: none; cursor: pointer;'>Modify</a></td>";
											
											echo "<td style='padding: 8px;'><a href='view.php?q=".$eqObject['id']."' style='display: inline-block; padding: 5px 10px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px; border: none; cursor: pointer;'>View</a></td>";
											
											echo "</tr>";
										}
										
										echo 	"</table>";
										echo "</div>";
									} else {
										redirect("?msg=error");
									}	
							} // switch
						} // outer if
					?>
               </div>
          </div>
     </section>
</body>
</html>