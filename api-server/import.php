<?php
    if (isset($argv[1])) { // Check if the file path argument was passed
        if (file_exists($argv[1])) {
            include("functions.php");
            $dblink=db_connect("equipments_db");

            echo "Hello from php process $argv[0] about to process file:$argv[1]\n";
            
            // Get the full path of the file 
            $fullPath = $argv[1];

            // Extract the filename from the full path and limit it to 25 characters
            // Remove any invalid characters from the filename
            // Replace spaces with hyphens
            $fileName = basename($argv[1]);
            $fileName = preg_replace('/[^a-zA-Z0-9_.-]/', '', $fileName);
            $fileName = substr($fileName, 0, 25);
            $fileName = str_replace(" ", "-", $fileName);

            // Print the full path and filename
            echo "Full path received: " . $fullPath . "\n";
            echo "Extracted filename: " . $fileName . "\n";

            // Initialize the line number for error logging
            $line_number = 1;

            // Open the file for reading
            echo "Opening file... $fileName\n";
            $fp=fopen("$argv[1]","r");

            // print the start time of the script
            $time_start=microtime(true); 
	        echo "PHP ID:$argv[1]-Start time is: $time_start\n";

            while (($row=fgetcsv($fp)) !== FALSE) 
            {
                // Holds the raw data on the row
		        $row_data = implode(',', $row);

                // Holds error log id and type id
                $errorlog_id = null;
                $errortype_id = null;

                // Checks how many fields in the row
		        $num_field = count($row);

                // escapes invalid SQL chars in $row_data (for logging purposes)
		        $escaped_row =  $dblink->real_escape_string($row_data); 
                $escaped_row = $fileName . " - " . $line_number . " - " . $escaped_row;

                // Print the row data being processed
                // echo "Processing row: $escaped_row\n\n";

                // ------------------------- N O T - 3 - F I E L D S --------------------------------------------------------- //
                // IF number of fields is not 3, log an error: ERROR_CODE: Invalid_FIELDNUM
                if ($num_field != 3) {
                    $sql = "INSERT INTO `error_log`(`line_num`, `row_data`) VALUES ('$line_number','$escaped_row')";
                    $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);

                    if (is_null($errorlog_id)) {
                        $sql = "SELECT * FROM `error_log` WHERE `line_num` = $line_number LIMIT 1";
                        $result = $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);

                        while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                            $errorlog_id = $data['auto_id'];
                            break;
                        }
                    }
                
                    // Getting the error type ID for Invalid_FIELDNUM (from the error_types table)
                    $sql = "SELECT * FROM `error_types` WHERE `error_code` = 'Invalid_FIELDNUM'";
                    $result = $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                    
                    while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                        $errortype_id = $data['auto_id'];
                        break;
                    }
                        
                    $sql = "INSERT INTO `error_list`(`error_log_id`, `error_type_id`) VALUES ('$errorlog_id','$errortype_id')";
                    $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);

                    // -------------------------- E X T R A - - C O M M A ----------------------------------------------------- //
                    // removes the fields that are empty, only fields that has data will remain
                    // (ASSUMES THE FIELDS ARE IN RIGHT ORDER, i.e. device field first, then manufacturer, and then serial number)
                    if ($num_field != 0) {
                        $temp = [];
                        $i = 0;
                        while ($i != $num_field) {
                            if (!empty($row[$i])) {
                                array_push($temp, $row[$i]);
                            }
                            $i++;
                        }
                    
                        if (count($temp) != 0) {
                            $row = $temp;
                            
                            // REASSIGNS THE VARIABLES with data from the new $row
                            $num_field = count($row);
                            $row_data = implode(',', $row);
                            $escaped_row = $dblink->real_escape_string($row_data); 	
                            $escaped_row = $fileName . " - " . $line_number . " - " . $escaped_row;				
                        }
                    }
                } // end of "if ($num_field != 3)"


                // ---------------------------- 3 - - F I E L D S------------------------------------------------------- //
                // CASE: There are exactly 3 fields
                if ($num_field == 3) 
                {
                    $emptyrow = false;										// flag if one of column is empty
                    $clean = true;											// flag if one of the column contains an invalid char 
                    $oversize = false;										// flag if one of column's length is over the limit
                    
                    // -------------------------- E M P T Y - - F I E L D S ------------------------------------------------ //
			        // CHECK FOR EMPTY FIELDS: If either of the fields are empty, log and proceed to the next line
			
                    // All fields are empty
                    if (empty($row[0]) && empty($row[1]) && empty($row[2])) {
                        $sql = "INSERT INTO `error_log`(`line_num`, `row_data`) VALUES ('$line_number','$escaped_row')";
                        $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                        
                        if (is_null($errorlog_id)) {
                            $sql = "SELECT * FROM `error_log` WHERE `line_num` = $line_number LIMIT 1";
                            $result = $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);

                            while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                                $errorlog_id = $data['auto_id'];
                                break;
                            }
                        }
                        
                        $sql = "SELECT * FROM `error_types` WHERE `error_code` = 'Empty_ALL'";
                        $result = $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                    
                        while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                            $errortype_id = $data['auto_id'];
                            break;
                        }
                        
                        $sql = "INSERT INTO `error_list`(`error_log_id`, `error_type_id`) VALUES ('$errorlog_id','$errortype_id')";
                        $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                        
                        $line_number++;
                        continue;  // Proceed to the next iteration
                        
                    }
                    else // Only one or two of the fields is/are empty
                    {
                        // Device field is empty
                        if (empty($row[0])) {
                            $sql = "INSERT INTO `error_log`(`line_num`, `row_data`) VALUES ('$line_number','$escaped_row')";
                            $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                            
                            if (is_null($errorlog_id)) {
                                $sql = "SELECT * FROM `error_log` WHERE `line_num` = $line_number LIMIT 1";
                                $result = $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);

                                while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                                    $errorlog_id = $data['auto_id'];
                                    break;
                                }
                            }
                        
                            $sql = "SELECT * FROM `error_types` WHERE `error_code` = 'Empty_DF'";
                            $result = $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                    
                            while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                                $errortype_id = $data['auto_id'];
                                break;
                            }
                        
                            $sql = "INSERT INTO `error_list`(`error_log_id`, `error_type_id`) VALUES ('$errorlog_id','$errortype_id')";
                            $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                            
                            $emptyrow = true;
                        }
                        
                        // Manufacturer field is empty
                        if (empty($row[1])) {			
                            $sql = "INSERT INTO `error_log`(`line_num`, `row_data`) VALUES ('$line_number','$escaped_row')";
                            $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                            
                            if (is_null($errorlog_id)) {
                                $sql = "SELECT * FROM `error_log` WHERE `line_num` = $line_number LIMIT 1";
                                $result = $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);

                                while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                                    $errorlog_id = $data['auto_id'];
                                    break;
                                }
                            }
                        
                            $sql = "SELECT * FROM `error_types` WHERE `error_code` = 'Empty_MF'";
                            $result = $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                    
                            while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                                $errortype_id = $data['auto_id'];
                                break;
                            }
                        
                            $sql = "INSERT INTO `error_list` (`error_log_id`, `error_type_id`) VALUES ('$errorlog_id', '$errortype_id')";
                            $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                            
                            $emptyrow = true;
                        }

                        // Serial number field is empty
                        if (empty($row[2])) {			
                            $sql = "INSERT INTO `error_log`(`line_num`, `row_data`) VALUES ('$line_number','$escaped_row')";
                            $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                            
                            if (is_null($errorlog_id)) {
                                $sql = "SELECT * FROM `error_log` WHERE `line_num` = $line_number LIMIT 1";
                                $result = $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);

                                while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                                    $errorlog_id = $data['auto_id'];
                                    break;
                                }
                            }
                        
                            $sql = "SELECT * FROM `error_types` WHERE `error_code` = 'Empty_SN'";
                            $result = $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                    
                            while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                                $errortype_id = $data['auto_id'];
                                break;
                            }
                        
                            $sql = "INSERT INTO `error_list`(`error_log_id`, `error_type_id`) VALUES ('$errorlog_id','$errortype_id')";
                            $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                            
                            $emptyrow = true;
                        }
                    
                        if ($emptyrow) {
                            $line_number++;
                            continue;  // Proceed to the next iteration
                        }
                    }
                    
                    // ---------------------------- F I E L D - - L E N G T H ---------------------------------------------------- //	
                    // Check whether the data in the field exceed the character limit set in the database for that particular field
                    //
                    // DEVICE field is over the size limit
                    if (strlen($row[0]) > 64) {			
                        $sql = "INSERT INTO `error_log`(`line_num`, `row_data`) VALUES ('$line_number','$escaped_row')";
                        $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                        
                        if (is_null($errorlog_id)) {
                            $sql = "SELECT * FROM `error_log` WHERE `line_num` = $line_number LIMIT 1";
                            $result = $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);

                            while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                                $errorlog_id = $data['auto_id'];
                                break;
                            }
                        }
                        
                        $sql = "SELECT * FROM `error_types` WHERE `error_code` = 'Over_DF'";
                        $result = $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                        while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                            $errortype_id = $data['auto_id'];
                            break;
                        }
                        
                        $sql = "INSERT INTO `error_list`(`error_log_id`, `error_type_id`) VALUES ('$errorlog_id','$errortype_id')";
                        $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                        
                        $oversize = true;
                    }

                    // MANUFACTURER field is over the size limit
                    if (strlen($row[1]) > 64) {	
                        $sql = "INSERT INTO `error_log`(`line_num`, `row_data`) VALUES ('$line_number','$escaped_row')";
                        $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                        
                        if (is_null($errorlog_id)) {
                            $sql = "SELECT * FROM `error_log` WHERE `line_num` = $line_number LIMIT 1";
                            $result = $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);

                            while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                                $errorlog_id = $data['auto_id'];
                                break;
                            }
                        }
                        
                        $sql = "SELECT * FROM `error_types` WHERE `error_code` = 'Over_MF'";
                        $result = $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                        while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                            $errortype_id = $data['auto_id'];
                            break;
                        }
                        
                        $sql = "INSERT INTO `error_list`(`error_log_id`, `error_type_id`) VALUES ('$errorlog_id','$errortype_id')";
                        $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                        
                        $oversize = true;
                    }

                    // SERIAL NUMBER field is over the size limit
                    if (strlen($row[2]) > 128) {	
                        $sql = "INSERT INTO `error_log`(`line_num`, `row_data`) VALUES ('$line_number','$escaped_row')";
                        $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                        
                        if (is_null($errorlog_id)) {
                            $sql = "SELECT * FROM `error_log` WHERE `line_num` = $line_number LIMIT 1";
                            $result = $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);

                            while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                                $errorlog_id = $data['auto_id'];
                                break;
                            }
                        }
                        
                        $sql = "SELECT * FROM `error_types` WHERE `error_code` = 'Over_SN'";
                        $result = $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                        while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                            $errortype_id = $data['auto_id'];
                            break;
                        }
                        
                        $sql = "INSERT INTO `error_list`(`error_log_id`, `error_type_id`) VALUES ('$errorlog_id','$errortype_id')";
                        $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                        
                        $oversize = true;
                    }
                    
                    if ($oversize) {
                        $line_number++;
                        continue;  // Proceed to the next iteration
                    }

                    // ------------------------- I N V A L I D - - C H A R S -------------------------------------------- //	
                    // SANITIZE fields: checking if field has the following invalid characters:
                    //			- single quote (')
                    // 			- double quote ("), 
                    //     		- forward slash (/)
                    //			- backslash (\)	
                    //		If present, sanitize the column data and log
                    $pattern = '/[\'"\/\\\\]/'; 					// regex pattern
                    $invalid_chars = array("'", '"', '/', '\\');	// list of invalid chars
                        
                    // DEVICE field contains invalid characters
                    if (preg_match($pattern, $row[0])) {						
                        $sql = "INSERT INTO `error_log`(`line_num`, `row_data`) VALUES ('$line_number','$escaped_row')";
                        $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                        
                        if (is_null($errorlog_id)) {
                            $sql = "SELECT * FROM `error_log` WHERE `line_num` = $line_number LIMIT 1";
                            $result = $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);

                            while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                                $errorlog_id = $data['auto_id'];
                                break;
                            }
                        }
                        
                        $sql = "SELECT * FROM `error_types` WHERE `error_code` = 'Escape_DF'";
                        $result = $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                        while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                            $errortype_id = $data['auto_id'];
                            break;
                        }
                        
                        $sql = "INSERT INTO `error_list`(`error_log_id`, `error_type_id`) VALUES ('$errorlog_id','$errortype_id')";
                        $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                        
                        $clean = false;
                    }

                    // MANUFACTURER field contains invalid characters
                    if (preg_match($pattern, $row[1])) {	
                        $sql = "INSERT INTO `error_log`(`line_num`, `row_data`) VALUES ('$line_number','$escaped_row')";
                        $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                        
                        if (is_null($errorlog_id)) {
                            $sql = "SELECT * FROM `error_log` WHERE `line_num` = $line_number LIMIT 1";
                            $result = $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);

                            while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                                $errorlog_id = $data['auto_id'];
                                break;
                            }
                        }
                        
                        $sql = "SELECT * FROM `error_types` WHERE `error_code` = 'Escape_MF'";
                        $result = $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                        while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                            $errortype_id = $data['auto_id'];
                            break;
                        }
                        
                        $sql = "INSERT INTO `error_list`(`error_log_id`, `error_type_id`) VALUES ('$errorlog_id','$errortype_id')";
                        $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                        
                        $clean = false;
                    }

                    // SERIAL NUMBER field contains invalid characters
                    if (preg_match($pattern, $row[2])) {	
                        $sql = "INSERT INTO `error_log`(`line_num`, `row_data`) VALUES ('$line_number','$escaped_row')";
                        $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                        
                        if (is_null($errorlog_id)) {
                            $sql = "SELECT * FROM `error_log` WHERE `line_num` = $line_number LIMIT 1";
                            $result = $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);

                            while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                                $errorlog_id = $data['auto_id'];
                                break;
                            }
                        }
                        
                        $sql = "SELECT * FROM `error_types` WHERE `error_code` = 'Escape_SN'";
                        $result = $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                        while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                            $errortype_id = $data['auto_id'];
                            break;
                        }
                        
                        $sql = "INSERT INTO `error_list`(`error_log_id`, `error_type_id`) VALUES ('$errorlog_id','$errortype_id')";
                        $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                        
                        $clean = false;
                    }

                    // If invalid chars exists in either the fields, sanitize $row_data by taking out the invalid chars
                    if (!$clean) {
                        //$row_data = str_replace($invalid_chars, '', $row_data);		// takes out the valid characters on the $row_data
                        
                        $row[0] = str_replace($invalid_chars, '', $row[0]);	
                        $row[1] = str_replace($invalid_chars, '', $row[1]);	
                        $row[2] = str_replace($invalid_chars, '', $row[2]);	
                    }

                    // ---------------------------- D U P L I C A T E - - S N ------------------------------------ //
                    // Check for duplicate serial number
                    $sql="SELECT 1 FROM `serial_nums` WHERE `serial_number` = '$row[2]' LIMIT 1";
                    $result = $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                        
                    if ($result->num_rows > 0) {					
                        $sql = "INSERT INTO `error_log`(`line_num`, `row_data`) VALUES ('$line_number','$escaped_row')";
                        $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                        
                        if (is_null($errorlog_id)) {
                            $sql = "SELECT * FROM `error_log` WHERE `line_num` = $line_number LIMIT 1";
                            $result = $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);

                            while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                                $errorlog_id = $data['auto_id'];
                                break;
                            }
                        }
                        
                        $sql = "SELECT * FROM `error_types` WHERE `error_code` = 'Duplicate_SN'";
                        $result = $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                        while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                            $errortype_id = $data['auto_id'];
                            break;
                        }
                        
                        $sql = "INSERT INTO `error_list`(`error_log_id`, `error_type_id`) VALUES ('$errorlog_id','$errortype_id')";
                        $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                        
                        $line_number++;
                        continue;  // Proceed to the next iteration
                    }

                    // ------------------------- N O - - E R R O R --------------------------------------------------- //
                    // NORMAL ROW DATA: NO ERROR FOUND
                    $deviceID = null;
                    $manufacturerID = null;
                    
                    // Check if the device exists in db, if not, insert it, then get the device ID
                    $sql = "SELECT * FROM `devices` WHERE `name` = '$row[0]'";
                    $result = $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                        
                    if ($result->num_rows > 0) {
                        while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                            $deviceID = $data['auto_id'];
                            break;
                        }
                    } else {		
                        while (empty($deviceID)) {
                            try {		
                                $sql = "INSERT into `devices` (`name`, `status`) values ('$row[0]', 'active')";
                                //	$dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                                
                                $dblink->query($sql);
                                $sql_select = "SELECT * FROM `devices` WHERE `name` = '$row[0]'";
                                $result = $dblink->query($sql_select) or die("Something went wrong with $sql<br>\n".$dblink->error);
                                while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                                        $deviceID = $data['auto_id'];
                                        break;
                                }		
                            } catch (mysqli_sql_exception $e) {
                                // Handle the exception
                                //	echo "Error: ".$e->getMessage()."\n";
                                
                                $sql_select = "SELECT * FROM `devices` WHERE `name` = '$row[0]'";
                                $result = $dblink->query($sql_select) or die("Something went wrong with $sql<br>\n".$dblink->error);
                                
                                while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                                    $deviceID = $data['auto_id'];
                                    break;
                                }
                            }
                        }
                    }

                    // Check if the manufacturer exists in db, if not, insert it, then get the manufacturer ID
                    $sql = "SELECT * FROM `manufacturers` WHERE `name` = '$row[1]'";
                    $result = $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                    
                    if ($result->num_rows > 0) {
                        while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                            $manufacturerID = $data['auto_id'];
                            break;
                        }
                    } else {
                        while (empty($manufacturerID)) {
                            try {
                                $sql = "INSERT into `manufacturers` (`name`, `status`) values ('$row[1]', 'active')";
                                //	$dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);
                                
                                $dblink->query($sql);
                                $sql_select = "SELECT * FROM `manufacturers` WHERE `name` = '$row[1]'";
                                $result = $dblink->query($sql_select) or die("Something went wrong with $sql<br>\n".$dblink->error);
                                while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                                        $manufacturerID = $data['auto_id'];
                                        break;
                                }	
                            } catch (mysqli_sql_exception $e) {	
                                // Handle the exception
                                //	echo "Error: " . $e->getMessage() ."\n";
                                
                                $sql_select = "SELECT * FROM `manufacturers` WHERE `name` = '$row[1]'";
                                $result = $dblink->query($sql_select) or die("Something went wrong with $sql<br>\n".$dblink->error);
                                while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
                                        $manufacturerID = $data['auto_id'];
                                        break;
                                }	
                            }
                        }
                    }
                    
                    // Insert the serial number into the database
                    // If the device and manufacturer IDs are found, insert the serial number
                    $sql="INSERT into `serial_nums` (`device_id`,`manufacturer_id`,`serial_number`, `status`) values ('$deviceID','$manufacturerID','$row[2]', 'active')";
                    $dblink->query($sql) or die("Something went wrong with $sql<br>\n".$dblink->error);


                } // end of "if ($num_field == 3)" 
                
                // Increment the count of processed rows
                $line_number++;

            } // end of "while (($row=fgetcsv($fp)) !== FALSE)"
            $time_end=microtime(true);
            echo "PHP ID:$argv[1]-End Time:$time_end\n";
            
            $seconds=$time_end-$time_start;
            $execution_time=($seconds)/60;
            echo "PHP ID:$argv[1]-Execution time: $execution_time minutes or $seconds seconds.\n";
            
            $rowsPerSecond=$line_number/$seconds;
            echo "PHP ID:$argv[1]-Insert rate: $rowsPerSecond per second\n";

            echo "Closing file... $fileName\n";
            fclose($fp);
            echo "Finished processing $fileName... Deleting file.\n";
            unlink($argv[1]);
        } else {
            echo "Error: File not found at " . $argv[1] . "\n";
            exit(1);
        }

    } else {
        echo "Hello from php process $argv[0] ....\n";
        echo "No file path provided.\n";
        exit(1);
    }
?>