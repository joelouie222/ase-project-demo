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
                    <a href="#" class="navbar-brand">View Equipment Details</a>
               </div>
               <!-- MENU LINKS -->
               <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-nav-first">
                         <li><a href="index.php" class="smoothScroll">Home</a></li>
						<li><a href="devices.php" class="smoothScroll">Devices</a></li>
						<li><a href="manufacturers.php" class="smoothScroll">Manufactures</a></li>
                         <li><a href="search.php" class="smoothScroll">Search Equipment</a></li>
						<li class="active"><a href="search.php" class="smoothScroll">View Equipment</a></li>
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
     <section id="feature">
          <div class="container">
               <div class="row">
                   <?php
				   		include("../functions.php");
		   
				   		$eid = trim($_GET['q']);	
				   		
				   		// $eid MUST BE INT
						if (($eid == NULL) || (!is_numeric($eid))) {
							echo "<div><h1>Invalid equipment id!</h1></div>";
						} else {
							$check = call_api("view_equipment", "eid=$eid");
		
							if ($check == FALSE) {
								// error, unable to establish connection with API		
								echo "<div><h1>Unable to establish connection with API. Try again later!</h1></div>";
							} else {
								$result = json_decode($check, true);

								if ($result == NULL) {
									// api returned no data
									echo "<div><h1>Something went wrong. Try again later!</h1></div>";					
								} else {
									$resultPayload = explode("MSG: ", $result[1]);
									
									switch ($resultPayload[1]) {
										case "Missing equipment id":
											echo "<div><h1>Equipment id is missing!</h1></div>";
											break;
										case "Invalid equipment id":
											echo "<div><h1>Invalid equipment id!</h1></div>";
											break;
										case "Equipment with this id does not exist in database":
											echo "<div><h1>Equipment with this id does not exist in database!</h1></div>";
											break;
										default:
											$eqObject = json_decode($resultPayload[1], true);
									
											if (is_array($eqObject)) {	
//												foreach($resultPayload[1] as $id=>$eqObject) {
													echo "<div><table border=1 style='width:100%; border-collapse: collapse;'>";
													echo "<tr style='background: #ebebeb'>
															<th style='padding: 8px;'>Field</th>
															<th style='padding: 8px;'>Value</th>
														</tr>";
													echo "<tr>
															<td style='padding: 8px;'> DEVICE TYPE </td>
															<td style='padding: 8px;'>".$eqObject['device_name']."</td>
														</tr>";
													echo "<tr>
															<td style='padding: 8px;'> MANUFACTURER </td>
															<td style='padding: 8px;'>".$eqObject['manufacturer_name']."</td>
														</tr>";
													echo "<tr>
															<td style='padding: 8px;'> SERIAL NUMBER </td>
															<td style='padding: 8px;'>".$eqObject['serial_number']."</td>
														</tr>";
													echo "<tr>
															<td style='padding: 8px;'> STATUS </td>
															<td style='padding: 8px;'>".$eqObject['status']."</td>
														</tr>";
													echo "</table></div><br>";								
													echo "<div><a href='modify.php?q=$eid' style='display: inline-block; padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px; border: none; cursor: pointer;'>Modify Equipment</a></div>";
//												}
											} else {
												echo "<div><h1>Something went wrong. Try again later!</h1></div>";	
											}
			
									} // switch
	
								}
							}
	
						}	
					?>
               </div>
          </div>
     </section>
</body>
</html>