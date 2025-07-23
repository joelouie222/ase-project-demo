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
                    <a href="#" class="navbar-brand">All Manufactures</a>
               </div>
               <!-- MENU LINKS -->
               <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-nav-first">
                         <li><a href="index.php" class="smoothScroll">Home</a></li>
						<li><a href="devices.php" class="smoothScroll">Devices</a></li>
						 <li class="active"><a href="manufacturers.php" class="smoothScroll">Manufactures</a></li>
                         <li><a href="search.php" class="smoothScroll">Search Equipment</a></li>
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
				   		echo "<h2>LIST OF ALL MANUFACTURERS (includes inactive)</h2>";
//				   		echo "<h3>(includes inactive)</h3>";
				   		
						// LIST MANUFACTURERS
				   		$check = call_api("list_manufacturers", "inactive=y");
		
						if ($check == FALSE) {
							// error, unable to establish connection with API
							echo "<h3>Unable to establish connection with API Server.</h3>";
						}
						$resultManuArray = json_decode($check, true);

						if ($resultManuArray == NULL) {
							// api returned no data
							echo "<h3>There was a problem retrieving the data. Try again later.</h3>";
						}		   
				   		$ManuPayload = explode("MSG: ", $resultManuArray[1]);
				   		$manufacturers=json_decode($ManuPayload[1], true);
				   		
				   		if (is_array($manufacturers)) {	
							echo "<div style='overflow-x:auto;'>";
							echo 	"<table style='width:100%; border-collapse: collapse;'>";
							echo		 "<tr>
											<th style='padding: 8px;'>ID</th>
											<th style='padding: 8px;'>Manufacturer Name</th>
											<th style='padding: 8px;'></th>
										</tr>";
												
										foreach($manufacturers as $key=>$value) {
											echo "<tr>";
											echo 	"<td style='padding: 8px;'>" . $key . "</td>";
											echo 	"<td style='padding: 8px;'>" . $value . "</td>";		
											echo "<td style='padding: 8px;'><a href='modifyManufacturer.php?q=".$key."' style='display: inline-block; padding: 5px 10px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px; border: none; cursor: pointer;'>Modify</a></td>";
											echo "</tr>";
										}
							echo 	"</table>";
							echo "</div>";
						} else {
							echo "<h3>There was a problem retrieving the data. Try again later.</h3>";
						}
				   ?>
				</div>
          </div>
     </section>
</body>
</html>