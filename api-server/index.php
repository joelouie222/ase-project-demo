<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            background-color: #f8f9fa;
            color: #343a40;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: #ffffff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        h1, h2, h3, h4 {
            color: #212529;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 10px;
            margin-top: 20px;
        }
        h1 {
            text-align: center;
            border-bottom: 3px solid #007bff;
        }
        h2 {
            margin-top: 20px;
            color: #0056b3;
        }
        code, pre {
            font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, Courier, monospace;
            background-color: #e9ecef;
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 0.9em;
        }
        pre {
            padding: 15px;
            overflow-x: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #dee2e6;
        }
        th {
            background-color: #f2f2f2;
            font-weight: 600;
        }
        .endpoint-section {
            margin-bottom: 40px;
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            background: #fdfdfd;
        }
        .success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
            padding: .75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: .25rem;
        }
         .error {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
            padding: .75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: .25rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>API Documentation</h1>

        <h3>API Server</h3>
        <pre>https://ec2-18-118-111-93.us-east-2.compute.amazonaws.com/api/</pre>

        <div class="endpoint-section">
            <h2>Add New Equipment</h2>
            <div>This endpoint adds a new piece of equipment to the database. It requires a valid and active device ID (<code>did</code>), a valid and active manufacturer ID (<code>mid</code>), and a unique serial number (<code>sn</code>). The endpoint validates that the provided did and mid exist in their respective tables and are marked as 'active'. It also ensures the serial number does not already exist in the database to prevent duplicates.</div>
            <p><strong>Endpoint URI:</strong> <code>/api/add_equipment</code></p>
            <p><strong>Example:</strong> <code>/api/add_equipment?did=1&mid=1&sn=SN-XYZ12345</code></p>

            <h4>Parameters</h4>
            <table>
                <thead>
                    <tr><th>Parameter</th><th>Description</th></tr>
                </thead>
                <tbody>
                    <tr><td><code>did</code></td><td>Required. Unsigned integer value. The id number for the device type. Must be a valid and active <code>did</code> that exist in the database.</td></tr>
                    <tr><td><code>mid</code></td><td>Required. Unsigned integer value. The id number for the manufacturer. Must be a valid and active <code>mid</code> that exist in the database.</td></tr>
                    <tr><td><code>sn</code></td><td>Required. The unique alpha-numeric serial number of the equipment. Must be in the format SN- followed by 1 to 64 alphanumeric characters.</td></tr>
                </tbody>
            </table>

            <h4>Responses</h4>            
            <div class="success"><strong>SUCCESS:</strong> Equipment successfully added to database.</div>
            <table>
                <thead>
                    <tr><th>Condition</th><th>Response</th></tr>
                </thead>
                <tbody>
                    <tr>
                    <td>Valid did, mid, sn</td>
                    <td><pre>{"Status": "Success", "MSG": "Equipment successfully added to database", "Action": "None"}</pre></td>
                </tr>
                </tbody>
            </table>

            <!-- <h4>Errors</h4> -->
            <div class="error"><strong>ERROR:</strong> There was a problem with the request. </div>
            <table>
                <thead>
                    <tr><th>Condition</th><th>Response</th></tr>
                </thead>
                <tbody>
                    <tr><td>Null did</td><td><pre>{"Status": "ERROR", "MSG": "Missing device id", "Action": "query_device"}</pre></td></tr>
                    <tr><td>Null mid</td><td><pre>{"Status": "ERROR", "MSG": "Missing manufacturer id", "Action": "query_manufacturer"}</pre></td></tr>
                    <tr><td>Null sn</td><td><pre>{"Status": "ERROR", "MSG": "Missing serial number", "Action": "none"}</pre></td></tr>
                    <tr><td>Invalid did</td><td><pre>{"Status": "ERROR", "MSG": "Invalid device id", "Action": "none"}</pre></td></tr>
                    <tr><td>Invalid mid</td><td><pre>{"Status": "ERROR", "MSG": "Invalid manufacturer id", "Action": "none"}</pre></td></tr>
                    <tr><td>Invalid sn</td><td><pre>{"Status": "ERROR", "MSG": "Invalid serial number", "Action": "none"}</pre></td></tr>
                    <tr><td>No device match</td><td><pre>{"Status": "ERROR", "MSG": "Device does not exist in database", "Action": "add_device"}</pre></td></tr>
                    <tr><td>Inactive device</td><td><pre>{"Status": "ERROR", "MSG": "Device status inactive", "Action": "modify_device"}</pre></td></tr>
                    <tr><td>No manufacturer match</td><td><pre>{"Status": "ERROR", "MSG": "Manufacturer does not exist in database", "Action": "add_manufacturer"}</pre></td></tr>
                    <tr><td>Inactive manufacturer</td><td><pre>{"Status": "ERROR", "MSG": "Manufacturer status inactive", "Action": "modify_manufacturer"}</pre></td></tr>
                    <tr><td>Duplicate serial number</td><td><pre>{"Status": "ERROR", "MSG": "Serial number exists in database", "Action": "query_equipment"}</pre></td></tr>
                    <tr><td>Default</td><td><pre>{"Status": "ERROR", "MSG": "{error message}", "Action": "None"}</pre></td></tr>
                </tbody>
            </table>
        </div>

        <div class="endpoint-section">
            <h2>Add New Device</h2>
            <div>This endpoint creates a new device type in the database. It requires a unique device name (<code>dname</code>) as a parameter. The name must be between 1 and 64 alphanumeric characters and can include spaces. The endpoint will return an error if the device name is missing, invalid, or already exists in the database. Upon success, the new device is created with an 'active' status.</div>
            <p><strong>Endpoint URI:</strong> <code>/api/add_device</code></p>
            <p><strong>Example:</strong> <code>/api/add_device?dname=Cisco%20Router%204300</code></p>
            

            <h4>Parameters</h4>
            <table>
                <thead>
                    <tr><th>Parameter</th><th>Description</th></tr>
                </thead>
                <tbody>
                    <tr><td><code>dname</code></td><td>Required. The unique alpha-numeric name for the device type. Must be under 64 characters. Can include spaces (url encoded).</td></tr>
                </tbody>
            </table>

            <h4>Responses</h4>
            <div class="success"><strong>SUCCESS:</strong> Device successfully added to the database.</div>            
            <table>
                <thead>
                    <tr><th>Condition</th><th>Response</th></tr>
                </thead>
                <tbody>
                    <tr>
                    <td>Valid dname</td>
                    <td><pre>{"Status": "Success", "MSG": "Device successfully added to database", "Action": "None"}</pre></td>
                </tr>
                </tbody>
            </table>


            <!-- <h4>Errors</h4> -->
            <div class="error"><strong>ERROR:</strong> There was a problem with the request. </div>
            <table>
                 <thead>
                    <tr><th>Condition</th><th>Response</th></tr>
                </thead>
                <tbody>
                    <tr><td>Null dname</td><td><pre>{"Status": "ERROR", "MSG": "Missing device name", "Action": "None"}</pre></td></tr>
                    <tr><td>Invalid dname</td><td><pre>{"Status": "ERROR", "MSG": "Invalid device name", "Action": "None"}</pre></td></tr>
                    <tr><td>Duplicate dname</td><td><pre>{"Status": "ERROR", "MSG": "Device already exists in database", "Action": "None"}</pre></td></tr>
                    <tr><td>Default</td><td><pre>{"Status": "ERROR", "MSG": "{error message}", "Action": "None"}</pre></td></tr>
                </tbody>
            </table>
        </div>

        <div class="endpoint-section">
            <h2>Add New Manufacturer</h2>
            <div>This endpoint creates a new manufacturer in the database. It requires a unique manufacturer name (<code>mname</code>) as a parameter. The name must be between 1 and 64 alphanumeric characters and can include spaces. The endpoint will return an error if the name is missing, invalid, or already exists in the database. Upon success, the new manufacturer is created with an 'active' status.</div>
            <p><strong>Endpoint URI:</strong> <code>:/api/add_manufacturer</code></p>
            <p><strong>Example:</strong> <code>/api/add_manufacturer?mname=Hewlett%20Packard</code></p>
            
            <h4>Parameters</h4>
            <table>
                <thead>
                    <tr><th>Parameter</th><th>Description</th></tr>
                </thead>
                <tbody>
                    <tr><td><code>mname</code></td><td>Required. The unique alpha-numeric name for the manufacturer. Must be under 64 characters. Can include spaces (url encoded).</td></tr>
                </tbody>
            </table>

            <h4>Responses</h4>
            <div class="success"><strong>SUCCESS:</strong> Manufacturer successfully added to the database.</div>
            <table>
                <thead>
                    <tr><th>Condition</th><th>Response</th></tr>
                </thead>
                <tbody>
                    <tr>
                    <td>Valid dname</td>
                    <td><pre>{"Status": "Success", "MSG": "Manufacturer successfully added to database", "Action": "None"}</pre></td>
                </tr>
                </tbody>
            </table>

            <!-- <h4>Errors</h4> -->
            <div class="error"><strong>ERROR:</strong> There was a problem with the request. </div>
            <table>
                 <thead>
                    <tr><th>Condition</th><th>Response</th></tr>
                </thead>
                <tbody>
                    <tr><td>Null mname</td><td><pre>{"Status": "ERROR", "MSG": "Missing manufacturer name", "Action": "None"}</pre></td></tr>
                    <tr><td>Invalid mname</td><td><pre>{"Status": "ERROR", "MSG": "Invalid manufacturer name", "Action": "None"}</pre></td></tr>
                    <tr><td>Duplicate mname</td><td><pre>{"Status": "ERROR", "MSG": "Manufacturer already exists in database", "Action": "None"}</pre></td></tr>
                    <tr><td>Default</td><td><pre>{"Status": "ERROR", "MSG": "{error message}", "Action": "None"}</pre></td></tr>
                </tbody>
            </table>
        </div>
        
        <div class="endpoint-section">
            <h2>Search Equipment</h2>
            <div>This endpoint searches for equipment based on various criteria. At least one of the following parameters must be provided: <code>dname</code>, <code>mname</code>, or <code>sn</code>. The search performs a "wildcard" match, so partial values are acceptable. By default, it returns only active equipment, but this can be overridden by setting the inactive parameter to 'y'.</div>
            <p><strong>Endpoint URI:</strong> <code>/api/search_equipment</code></p>
            <p><strong>Example:</strong> <code>/api/search_equipment?dname=Router</code></p>           
            <p><code>/api/search_equipment?mname=Cisco&inactive=y</code></p>  
            <p><code>/api/search_equipment?dname=Switch&mname=HP&sn=A1B2</code></p> 

            <h4>Parameters</h4>
            <table>
                <thead>
                    <tr><th>Parameter</th><th>Description</th></tr>
                </thead>
                <tbody>
                    <tr><td><code>dname</code></td><td>Required/Optional. The unique alpha-numeric name for the device type. Must be under 64 characters. Can be partial or complete.</td></tr>
                    <tr><td><code>mname</code></td><td>Required/Optional. The unique alpha-numeric name for the manufacturer. Must be under 64 characters. Can be partial or complete.</td></tr>
                    <tr><td><code>sn</code></td><td>Required/Optional. The unique alpha-numeric serial number of the equipment. Must be under 128 characters. Can be partial or complete.</td></tr>
                    <tr><td><code>inactive</code></td><td>Optional. Should the search include equipment that are flagged as "Inactive"? Can either be "y" or "n". Default value is "n" if omitted.</td></tr>
                </tbody>
            </table>

            <h4>Responses</h4>
            <div class="success"><strong>SUCCESS:</strong> Returns a list of equipment objects matching the criteria.</div>
            <table>
                <thead>
                    <tr><th>Condition</th><th>Response</th></tr>
                </thead>
                <tbody>
                    <tr>
                    <td>Valid dname</td>
                    <td><pre>{"Status": "Success", "MSG": "{equipment objects}", "Action": "None"}</pre></td>
                </tr>
                </tbody>
            </table>
            
            <!-- <h4>Errors</h4> -->
            <div class="error"><strong>ERROR:</strong> There was a problem with the request. </div>
            <table>
                 <thead>
                    <tr><th>Condition</th><th>Response</th></tr>
                </thead>
                <tbody>
                    <tr><td>Null dname, mname, sn</td><td><pre>{"Status": "ERROR", "MSG": "Missing search parameters", "Action": "None"}</pre></td></tr>
                    <tr><td>Invalid dname</td><td><pre>{"Status": "ERROR", "MSG": "Invalid device name format", "Action": "None"}</pre></td></tr>
                    <tr><td>Invalid mname</td><td><pre>{"Status": "ERROR", "MSG": "Invalid manufacturer name format", "Action": "None"}</pre></td></tr>
                    <tr><td>Invalid sn</td><td><pre>{"Status": "ERROR", "MSG": "Invalid serial number format", "Action": "none"}</pre></td></tr>
                    <tr><td>No match</td><td><pre>{"Status": "ERROR", "MSG": "There are no equipment matching the search parameters", "Action": "none"}</pre></td></tr>
                    <tr><td>Default</td><td><pre>{"Status": "ERROR", "MSG": "{error message}", "Action": "None"}</pre></td></tr>
                </tbody>
            </table>
        </div>
        
        <div class="endpoint-section">
            <h2>View Equipment</h2>
            <div>This endpoint retrieves a single piece of equipment from the database by its unique equipment ID (<code>eid</code>). It requires the eid as a parameter and will return an error if the ID is missing, not a number, or does not exist in the database.</div>
            <p><strong>Endpoint URI:</strong> <code>/api/view_equipment</code></p>
            <p><strong>Example:</strong> <code>/api/view_equipment?eid=123</code></p>            
            
            <h4>Parameters</h4>
            <table>
                <thead>
                    <tr><th>Parameter</th><th>Description</th></tr>
                </thead>
                <tbody>
                    <tr><td><code>eid</code></td><td>Required. Unsigned integer value. The id number for the equipment. Must be a valid <code>eid</code> that exist in the database.</td></tr>
                </tbody>
            </table>

            <h4>Responses</h4>
            <div class="success"><strong>SUCCESS:</strong> Returns a single equipment object.</div>
            <table>
                <thead>
                    <tr><th>Condition</th><th>Response</th></tr>
                </thead>
                <tbody>
                    <tr>
                    <td>Valid dname</td>
                    <td><pre>{"Status": "Success", "MSG": "{equipment object}", "Action": "None"}</pre></td>
                </tr>
                </tbody>
            </table>
            
            <!-- <h4>Errors</h4> -->
            <div class="error"><strong>ERROR:</strong> There was a problem with the request. </div>
            <table>
                 <thead>
                    <tr><th>Condition</th><th>Response</th></tr>
                </thead>
                <tbody>
                    <tr><td>Null eid</td><td><pre>{"Status": "ERROR", "MSG": "Missing equipment id", "Action": "None"}</pre></td></tr>
                    <tr><td>Invalid eid</td><td><pre>{"Status": "ERROR", "MSG": "Invalid equipment id", "Action": "None"}</pre></td></tr>
                    <tr><td>No match</td><td><pre>{"Status": "ERROR", "MSG": "Equipment with this id does not exist in database", "Action": "None"}</pre></td></tr>
                    <tr><td>Default</td><td><pre>{"Status": "ERROR", "MSG": "{error message}", "Action": "None"}</pre></td></tr>
                </tbody>
            </table>
        </div>
        
        <div class="endpoint-section">
            <h2>Modify Equipment</h2>        
            <div>This endpoint modifies an existing piece of equipment identified by a required equipment ID (<code>eid</code>). At least one optional parameter (<code>did</code>, <code>mid</code>, <code>sn</code>, <code>status</code>) must be provided to update. The endpoint validates all provided IDs to ensure they exist and checks that any new serial number (<code>sn</code>) is not a duplicate. The new status must be either 'active' or 'inactive'.</div>
            <p><strong>Endpoint URI:</strong> <code>/api/modify_equipment</code></p>
            <p><strong>Example:</strong> <code>/api/modify_equipment?eid=123&status=inactive</code></p>  
            <p><code>/api/modify_equipment?eid=123&mid=5&sn=SN-NEWSERIAL123</code></p> 
            
            <h4>Parameters</h4>
            <table>
                <thead>
                    <tr><th>Parameter</th><th>Description</th></tr>
                </thead>
                <tbody>
                    <tr><td><code>eid</code></td><td>Required. Unsigned integer value. The id number for the equipment. Must be a valid <code>eid</code> that exist in the database.</td></tr>
                    <tr><td><code>did</code></td><td>Optional. Unsigned integer value. The <em>NEW</em> id number for the device type. Must be a valid <code>did</code> that exist in the database.</td></tr>
                    <tr><td><code>mid</code></td><td>Optional. Unsigned integer value. The <em>NEW</em>  id number for the manufacturer. Must be a valid <code>mid</code> that exist in the database. </td></tr>
                    <tr><td><code>sn</code></td><td>Optional. The <em>NEW</em> unique alpha-numeric serial number of the equipment. Must be under 128 characters. </td></tr>
                    <tr><td><code>status</code></td><td>Optional. The <em>NEW</em> status of the equipment. Can be either "active" or "inactive". </td></tr>
                </tbody>
            </table>

            <h4>Responses</h4>
            <div class="success"><strong>SUCCESS:</strong> Equipment successfully modified in the database.</div>
            <table>
                <thead>
                    <tr><th>Condition</th><th>Response</th></tr>
                </thead>
                <tbody>
                    <tr>
                    <td>Valid dname</td>
                    <td><pre>{"Status": "Success", "MSG": "Equipment successfully modified in the database", "Action": "None"}</pre></td>
                </tr>
                </tbody>
            </table>
            
            <!-- <h4>Errors</h4> -->
            <div class="error"><strong>ERROR:</strong> There was a problem with the request. </div>
            <table>
                 <thead>
                    <tr><th>Condition</th><th>Response</th></tr>
                </thead>
                <tbody>
                    <tr><td>Null eid</td><td><pre>{"Status": "ERROR", "MSG": "Missing equipment id", "Action": "query_equipment"}</pre></td></tr>
                    <tr><td>Invalid eid</td><td><pre>{"Status": "ERROR", "MSG": "Invalid equipment id", "Action": "None"}</pre></td></tr>
                    <tr><td>No match</td><td><pre>{"Status": "ERROR", "MSG": "Equipment with this id does not exist in database", "Action": "query_equipment"}</pre></td></tr>
                    <tr><td>Null did, mid, sn, status</td><td><pre>{"Status": "ERROR", "MSG": "Missing new equipment information", "Action": "None"}</pre></td></tr>
                    <tr><td>Invalid did</td><td><pre>{"Status": "ERROR", "MSG": "Invalid device id", "Action": "none"}</pre></td></tr>
                    <tr><td>No match device</td><td><pre>{"Status": "ERROR", "MSG": "Device with this id does not exist in database", "Action": "query_device"}</pre></td></tr>
                    <tr><td>Invalid mid</td><td><pre>{"Status": "ERROR", "MSG": "Invalid manufacturer id", "Action": "none"}</pre></td></tr>
                    <tr><td>No match manufacturer</td><td><pre>{"Status": "ERROR", "MSG": "Manufacturer with this id does not exist in database", "Action": "query_manufacturer"}</pre></td></tr>
                    <tr><td>Invalid sn</td><td><pre>{"Status": "ERROR", "MSG": "Invalid serial number", "Action": "none"}</pre></td></tr>
                    <tr><td>Duplicate sn</td><td><pre>{"Status": "ERROR", "MSG": "Serial number exists in database", "Action": "query_equipment"}</pre></td></tr>
                    <tr><td>Invalid status</td><td><pre>{"Status": "ERROR", "MSG": "Invalid status", "Action": "None"}</pre></td></tr>
                    <tr><td>Default</td><td><pre>{"Status": "ERROR", "MSG": "{error message}", "Action": "None"}</pre></td></tr>
                </tbody>
            </table>
        </div>

        <div class="endpoint-section">
            <h2>Modify Device</h2>            
            <div>This endpoint modifies an existing device type identified by a required device ID (<code>did</code>). You must provide at least one optional parameter to update: a new device name (<code>dname</code>) or a new status (<code>status</code>). The endpoint validates that the provided did exists and ensures the new <code>dname</code>, if provided, is not already in use by another device. The new status must be either 'active' or 'inactive'.</div>
            <p><strong>Endpoint URI:</strong> <code>/api/modify_device</code></p>
            <p><strong>Example:</strong> <code>/api/modify_device?did=45&dname=Cisco%20ISR%204321</code></p>  
            <p><code>/api/modify_device?did=45&status=inactive</code></p>

            <h4>Parameters</h4>
            <table>
                <thead>
                    <tr><th>Parameter</th><th>Description</th></tr>
                </thead>
                <tbody>
                    <tr><td><code>did</code></td><td>Required. Unsigned integer value. The id number for the device type. Must be a valid <code>did</code> that exist in the database.</td></tr>
                    <tr><td><code>dname</code></td><td>Optional. The <em>NEW</em> unique alpha-numeric name for the device type. Must be under 64 characters.</td></tr>
                    <tr><td><code>status</code></td><td>Optional. The <em>NEW</em> status of the equipment. Can be either "active" or "inactive". </td></tr>
                </tbody>
            </table>

            <h4>Responses</h4>
            <div class="success"><strong>SUCCESS:</strong> Device successfully modified.</div>
            <table>
                <thead>
                    <tr><th>Condition</th><th>Response</th></tr>
                </thead>
                <tbody>
                    <tr>
                    <td>Valid dname</td>
                    <td><pre>{"Status": "Success", "MSG": "Device successfully modified", "Action": "None"}</pre></td>
                </tr>
                </tbody>
            </table>

            <!-- <h4>Errors</h4> -->
            <div class="error"><strong>ERROR:</strong> There was a problem with the request. </div>
            <table>
                <thead>
                    <tr><th>Condition</th><th>Response</th></tr>
                </thead>
                <tbody>
                    <tr><td>Null did</td><td><pre>{"Status": "ERROR", "MSG": "Missing device id", "Action": "query_device"}</pre></td></tr>
                    <tr><td>Invalid did</td><td><pre>{"Status": "ERROR", "MSG": "Invalid device id", "Action": "none"}</pre></td></tr>
                    <tr><td>Null dname, status</td><td><pre>{"Status": "ERROR", "MSG": "Missing new device information", "Action": "none"}</pre></td></tr>
                    <tr><td>Invalid dname</td><td><pre>{"Status": "ERROR", "MSG": "Invalid device name", "Action": "none"}</pre></td></tr>
                    <tr><td>Invalid status</td><td><pre>{"Status": "ERROR", "MSG": "Invalid status", "Action": "none"}</pre></td></tr>
                    <tr><td>Duplicate dname</td><td><pre>{"Status": "ERROR", "MSG": "Device with this name already exist in database", "Action": "query_device"}</pre></td></tr>
                    <tr><td>No match device</td><td><pre>{"Status": "ERROR", "MSG": "Device with this id does not exist in database", "Action": "query_device"}</pre></td></tr>
                    <tr><td>Default</td><td><pre>{"Status": "ERROR", "MSG": "{error message}", "Action": "None"}</pre></td></tr>
                </tbody>
            </table>
        </div>

        <div class="endpoint-section">
            <h2>Modify Manufacturer</h2>
            <div>This endpoint modifies an existing manufacturer identified by a required manufacturer ID (<code>mid</code>). Provide at least one of the two optional parameter to update: a new manufacturer name (<code>mname</code>) or a new status (<code>status</code>). The endpoint validates that the provided mid exists and ensures the new <code>mname</code>, if provided, is not already in use by another manufacturer. The new status must be either 'active' or 'inactive'.</div>
            <p><strong>Endpoint URI:</strong> <code>/api/modify_manufacturer</code></p>
            <p><strong>Example:</strong> <code>/api/modify_manufacturer?mid=12&mname=Cisco%20Systems%20Inc</code></p>  
            <p><code>/api/modify_manufacturer?mid=12&status=inactive</code></p> 

            <h4>Parameters</h4>
            <table>
                <thead>
                    <tr><th>Parameter</th><th>Description</th></tr>
                </thead>
                <tbody>
                    <tr><td><code>mid</code></td><td>Required. Unsigned integer value. The id number for the manufacturer. Must be a valid mid that exist in the database.</td></tr>
                    <tr><td><code>mname</code></td><td>Optional. The <em>NEW</em> unique alpha-numeric name for the manufacturer. Must be under 64 characters.</td></tr>
                    <tr><td><code>status</code></td><td>Optional. The <em>NEW</em> status of the manufacturer. Can be either "active" or "inactive". </td></tr>
                </tbody>
            </table>

            <h4>Responses</h4>
            <div class="success"><strong>SUCCESS:</strong> Manufacturer successfully modified.</div>
            <table>
                <thead>
                    <tr><th>Condition</th><th>Response</th></tr>
                </thead>
                <tbody>
                    <tr>
                    <td>Valid dname</td>
                    <td><pre>{"Status": "Success", "MSG": "Manufacturer successfully modified", "Action": "None"}</pre></td>
                </tr>
                </tbody>
            </table>

            <!-- <h4>Errors</h4> -->
            <div class="error"><strong>ERROR:</strong> There was a problem with the request. </div>
            <table>
                <thead>
                    <tr><th>Condition</th><th>Response</th></tr>
                </thead>
                <tbody>
                    <tr><td>Null mid</td><td><pre>{"Status": "ERROR", "MSG": "Missing manufacturer id", "Action": "query_manufacturer"}</pre></td></tr>
                    <tr><td>Invalid mid</td><td><pre>{"Status": "ERROR", "MSG": "Invalid manufacturer id", "Action": "none"}</pre></td></tr>
                    <tr><td>Invalid mname</td><td><pre>{"Status": "ERROR", "MSG": "Invalid manufacturer name", "Action": "none"}</pre></td></tr>
                    <tr><td>Invalid status</td><td><pre>{"Status": "ERROR", "MSG": "Invalid status", "Action": "none"}</pre></td></tr>
                    <tr><td>Duplicate mname</td><td><pre>{"Status": "ERROR", "MSG": "Manufacturer with this name already exist in database", "Action": "query_manufacturer"}</pre></td></tr>
                    <tr><td>No match manufacturer</td><td><pre>{"Status": "ERROR", "MSG": "Manufacturer with this id does not exist in database", "Action": "query_manufacturer"}</pre></td></tr>
                    <tr><td>Default</td><td><pre>{"Status": "ERROR", "MSG": "{error message}", "Action": "None"}</pre></td></tr>
                </tbody>
            </table>
        </div>

        <div class="endpoint-section">
            <h2>View Device</h2>            
            <div>This endpoint retrieves a single device type from the database using its unique device ID (<code>did</code>). It requires the did as a parameter and will return an error if the ID is missing, not a number, or does not exist in the database.</div>
            <p><strong>Endpoint URI:</strong> <code>/api/view_device</code></p>
            <p><strong>Example:</strong> <code>/api/view_device?did=45</code></p>  
            
            <h4>Parameters</h4>
            <table>
                <thead>
                    <tr><th>Parameter</th><th>Description</th></tr>
                </thead>
                <tbody>
                    <tr><td><code>did</code></td><td>Required. Unsigned integer value. The id number for the device type. Must be a valid <code>did</code> that exist in the database.</td></tr>
                </tbody>
            </table>

            <h4>Responses</h4>
            <div class="success"><strong>SUCCESS:</strong> Returns a single device object.</div>            
            <table>
                <thead>
                    <tr><th>Condition</th><th>Response</th></tr>
                </thead>
                <tbody>
                    <tr>
                    <td>Valid dname</td>
                    <td><pre>{"Status": "Success", "MSG": "{Device Object}", "Action": "None"}</pre></td>
                </tr>
                </tbody>
            </table>
            
            <!-- <h4>Errors</h4> -->
            <div class="error"><strong>ERROR:</strong> There was a problem with the request. </div>
            <table>
                <thead>
                    <tr><th>Condition</th><th>Response</th></tr>
                </thead>
                <tbody>
                    <tr><td>Null did</td><td><pre>{"Status": "ERROR", "MSG": "Missing device id", "Action": "query_device"}</pre></td></tr>
                    <tr><td>Invalid did</td><td><pre>{"Status": "ERROR", "MSG": "Invalid device id", "Action": "none"}</pre></td></tr>
                    <tr><td>No match device</td><td><pre>{"Status": "ERROR", "MSG": "Device with this id does not exist in database", "Action": "query_device"}</pre></td></tr>
                    <tr><td>Default</td><td><pre>{"Status": "ERROR", "MSG": "{error message}", "Action": "None"}</pre></td></tr>
                </tbody>
            </table>
        </div>

        <div class="endpoint-section">
            <h2>View Manufacturer</h2>            
            <div>This endpoint retrieves a single manufacturer from the database using its unique manufacturer ID (<code>mid</code>). It requires the mid as a parameter and will return an error if the ID is missing, not a number, or does not exist in the database.</div>
            <p><strong>Endpoint URI:</strong> <code>/api/view_manufacturer</code></p>
            <p><strong>Example:</strong> <code>/api/view_manufacturer?mid=12</code></p>  
            
            <h4>Parameters</h4>
            <table>
                <thead>
                    <tr><th>Parameter</th><th>Description</th></tr>
                </thead>
                <tbody>
                    <tr><td><code>mid</code></td><td>Required. Unsigned integer value. The id number for the manufacturer. Must be a valid <code>mid</code> that exist in the database.</td></tr>
                </tbody>
            </table>

            <h4>Responses</h4>
            <div class="success"><strong>SUCCESS:</strong> Returns a single manufacturer object.</div>            
            <table>
                <thead>
                    <tr><th>Condition</th><th>Response</th></tr>
                </thead>
                <tbody>
                    <tr>
                    <td>Valid dname</td>
                    <td><pre>{"Status": "Success", "MSG": "{Manufacturer Object}", "Action": "None"}</pre></td>
                </tr>
                </tbody>
            </table>
            
            <!-- <h4>Errors</h4> -->
            <div class="error"><strong>ERROR:</strong> There was a problem with the request. </div>
            <table>
                <thead>
                    <tr><th>Condition</th><th>Response</th></tr>
                </thead>
                <tbody>
                    <tr><td>Null mid</td><td><pre>{"Status": "ERROR", "MSG": "Missing manufacturer id", "Action": "query_manufacturer"}</pre></td></tr>
                    <tr><td>Invalid mid</td><td><pre>{"Status": "ERROR", "MSG": "Invalid manufacturer id", "Action": "none"}</pre></td></tr>
                    <tr><td>No match manufacturer</td><td><pre>{"Status": "ERROR", "MSG": "Manufacturer with this id does not exist in database", "Action": "query_manufacturer"}</pre></td></tr>
                    <tr><td>Default</td><td><pre>{"Status": "ERROR", "MSG": "{error message}", "Action": "None"}</pre></td></tr>
                </tbody>
            </table>
        </div>

        <div class="endpoint-section">
            <h2>Query Device</h2>            
            <div>This endpoint retrieves a single device type from the database by performing an exact match on its name (<code>dname</code>). It requires the dname as a parameter and will return an error if the name is missing or does not exist in the database. Upon success, a single device object is returned.</div>
            <p><strong>Endpoint URI:</strong> <code>/api/query_device</code></p>
            <p><strong>Example:</strong> <code>/api/query_device?dname=Cisco%20Router%204300</code></p> 
            
            <h4>Parameters</h4>
            <table>
                <thead>
                    <tr><th>Parameter</th><th>Description</th></tr>
                </thead>
                <tbody>
                    <tr><td><code>dname</code></td><td>Required. The unique alpha-numeric name for the device type. Must be under 64 characters. Can have spaces (url encoded). Must be an exact match.</td></tr>
                </tbody>
            </table>

            <h4>Responses</h4>
            <div class="success"><strong>SUCCESS:</strong> Returns a device object matching the name.</div>            
            <table>
                <thead>
                    <tr><th>Condition</th><th>Response</th></tr>
                </thead>
                <tbody>
                    <tr>
                    <td>Valid dname</td>
                    <td><pre>{"Status": "Success", "MSG": "{Device Object}", "Action": "None"}</pre></td>
                </tr>
                </tbody>
            </table>
            
            <!-- <h4>Errors</h4> -->
            <div class="error"><strong>ERROR:</strong> There was a problem with the request. </div>
            <table>
                <thead>
                    <tr><th>Condition</th><th>Response</th></tr>
                </thead>
                <tbody>
                    <tr><td>Null dname</td><td><pre>{"Status": "ERROR", "MSG": "Missing device name", "Action": "None"}</pre></td></tr>
                    <tr><td>Invalid dname</td><td><pre>{"Status": "ERROR", "MSG": "Invalid device name", "Action": "none"}</pre></td></tr>
                    <tr><td>No match device</td><td><pre>{"Status": "ERROR", "MSG": "There are no device with this name", "Action": "add_device"}</pre></td></tr>
                    <tr><td>Default</td><td><pre>{"Status": "ERROR", "MSG": "{error message}", "Action": "None"}</pre></td></tr>
                </tbody>
            </table>
        </div>

        <div class="endpoint-section">
            <h2>Query Manufacturer</h2>            
            <div>This endpoint retrieves a single manufacturer from the database by performing an exact match on its name (<code>mname</code>). It requires the <code>mname</code> as a parameter and will return an error if the name is missing or does not exist in the database. Upon success, a single manufacturer object is returned.</div>
            <p><strong>Endpoint URI:</strong> <code>/api/query_manufacturer</code></p>
            <p><strong>Example:</strong> <code>/api/query_manufacturer?mname=Hewlett%20Packard</code></p> 
            
            <h4>Parameters</h4>
            <table>
                <thead>
                    <tr><th>Parameter</th><th>Description</th></tr>
                </thead>
                <tbody>
                    <tr><td><code>mname</code></td><td>Required/Optional. The unique alpha-numeric name for the manufacturer. Must be under 64 characters. Can contain spaces (url encoded). Must be an exact match.</td></tr>
                </tbody>
            </table>

            <h4>Responses</h4>
            <div class="success"><strong>SUCCESS:</strong> Returns a manufacturer object matching the name.</div>            
            <table>
                <thead>
                    <tr><th>Condition</th><th>Response</th></tr>
                </thead>
                <tbody>
                    <tr>
                    <td>Valid dname</td>
                    <td><pre>{"Status": "Success", "MSG": "{Manufacturer Object}", "Action": "None"}</pre></td>
                </tr>
                </tbody>
            </table>
            
            <!-- <h4>Errors</h4> -->
            <div class="error"><strong>ERROR:</strong> There was a problem with the request. </div>
            <table>
                <thead>
                    <tr><th>Condition</th><th>Response</th></tr>
                </thead>
                <tbody>
                    <tr><td>Null mname</td><td><pre>{"Status": "ERROR", "MSG": "Missing manufacturer name", "Action": "None"}</pre></td></tr>
                    <tr><td>Invalid mname</td><td><pre>{"Status": "ERROR", "MSG": "Invalid manufacturer name", "Action": "none"}</pre></td></tr>
                    <tr><td>No match manufacturer</td><td><pre>{"Status": "ERROR", "MSG": "There are no manufacturer with this name", "Action": "add_manufacturer"}</pre></td></tr>
                    <tr><td>Default</td><td><pre>{"Status": "ERROR", "MSG": "{error message}", "Action": "None"}</pre></td></tr>
                </tbody>
            </table>
        </div>

        <div class="endpoint-section">
            <h2>Query Equipment</h2>            
            <div>This endpoint retrieves a single piece of equipment from the database by performing an exact match on its serial number (<code>sn</code>). It requires the <code>sn</code> as a parameter and will return an error if the serial number is missing, improperly formatted, or does not exist in the database. The serial number must be in the format SN- followed by 1 to 64 alphanumeric characters. Upon success, returns a single equipment object is returned.</div>
            <p><strong>Endpoint URI:</strong> <code>/api/query_equipment</code></p>
            <p><strong>Example:</strong> <code>/api/query_equipment?sn=SN-A1B2C3D4</code></p> 
            
            <h4>Parameters</h4>
            <table>
                <thead>
                    <tr><th>Parameter</th><th>Description</th></tr>
                </thead>
                <tbody>
                    <tr><td><code>sn</code></td><td>Required. The unique alpha-numeric serial number of the equipment. Must start with SN- followed by 1 to 64 alpha-numeric characters. Must be an exact match.</td></tr>
                </tbody>
            </table>

            <h4>Responses</h4>
            <div class="success"><strong>SUCCESS:</strong> Returns an equipment object matching the serial number.</div>            
            <table>
                <thead>
                    <tr><th>Condition</th><th>Response</th></tr>
                </thead>
                <tbody>
                    <tr>
                    <td>Valid dname</td>
                    <td><pre>{"Status": "Success", "MSG": "{Equipment Object}", "Action": "None"}</pre></td>
                </tr>
                </tbody>
            </table>

            <!-- <h4>Errors</h4> -->
            <div class="error"><strong>ERROR:</strong> There was a problem with the request. </div>
            <table>
                <thead>
                    <tr><th>Condition</th><th>Response</th></tr>
                </thead>
                <tbody>
                    <tr><td>Null sn</td><td><pre>{"Status": "ERROR", "MSG": "Missing serial number", "Action": "None"}</pre></td></tr>
                    <tr><td>Invalid sn</td><td><pre>{"Status": "ERROR", "MSG": "Invalid serial number", "Action": "none"}</pre></td></tr>
                    <tr><td>No match equipment</td><td><pre>{"Status": "ERROR", "MSG": "There are no equipment with this serial number", "Action": "add_equipment"}</pre></td></tr>
                    <tr><td>Default</td><td><pre>{"Status": "ERROR", "MSG": "{error message}", "Action": "None"}</pre></td></tr>
                </tbody>
            </table>
        </div>

        <div class="endpoint-section">
            <h2>List Devices</h2>            
            <div>This endpoint retrieves a list of all device types from the database. By default, it returns only devices with an 'active' status. An optional inactive parameter can be set to 'y' to include inactive devices in the result.</div>
            <p><strong>Endpoint URI:</strong> <code>/api/list_devices</code></p>
            <p><strong>Example:</strong> <code>/api/list_devices?inactive=y</code></p> 

            <h4>Parameters</h4>
            <table>
                <thead>
                    <tr><th>Parameter</th><th>Description</th></tr>
                </thead>
                <tbody>
                    <tr><td><code>inactive</code></td><td>Optional. Should the search include device type that are flagged as "Inactive"? Can either be "y" or "n". Default value is "n" if omitted.</td></tr>
                </tbody>
            </table>

            <h4>Responses</h4>
            <div class="success"><strong>SUCCESS:</strong> Returns a list of all device objects.</div>            
            <table>
                <thead>
                    <tr><th>Condition</th><th>Response</th></tr>
                </thead>
                <tbody>
                    <tr>
                    <td>Valid dname</td>
                    <td><pre>{"Status": "Success", "MSG": "{Device Objects}", "Action": "None"}</pre></td>
                </tr>
                </tbody>
            </table>
            
            <!-- <h4>Errors</h4> -->
            <div class="error"><strong>ERROR:</strong> There was a problem with the request. </div>
            <table>
                <thead>
                    <tr><th>Condition</th><th>Response</th></tr>
                </thead>
                <tbody>
                    <tr><td>SQL search error</td><td><pre>{"Status": "ERROR", "MSG": "There was a problem retrieving the data. Try again later.", "Action": "None"}</pre></td></tr>
                    <tr><td>Default</td><td><pre>{"Status": "ERROR", "MSG": "{error message}", "Action": "None"}</pre></td></tr>
                </tbody>
            </table>
        </div>

        <div class="endpoint-section">
            <h2>List Manufacturers</h2>            
            <div>This endpoint retrieves a list of all manufacturers from the database. By default, it returns only manufacturers with an 'active' status. An optional inactive parameter can be set to 'y' to include inactive manufacturers in the result.</div>
            <p><strong>Endpoint URI:</strong> <code>/api/list_manufacturers</code></p>
            <p><strong>Example:</strong> <code>/api/list_manufacturers?inactive=y</code></p> 
            
            <h4>Parameters</h4>
            <table>
                <thead>
                    <tr><th>Parameter</th><th>Description</th></tr>
                </thead>
                <tbody>
                    <tr><td><code>inactive</code></td><td>Optional. Should the search include manufacturer that are flagged as "Inactive"? Can either be "y" or "n". Default value is "n" if omitted.</td></tr>
                </tbody>
            </table>

            <h4>Responses</h4>
            <div class="success"><strong>SUCCESS:</strong> Returns a list of all manufacturer objects.</div>            
            <table>
                <thead>
                    <tr><th>Condition</th><th>Response</th></tr>
                </thead>
                <tbody>
                    <tr>
                    <td>Valid dname</td>
                    <td><pre>{"Status": "Success", "MSG": "{Manufacturer Objects}", "Action": "None"}</pre></td>
                </tr>
                </tbody>
            </table>
            
            <!-- <h4>Errors</h4> -->
            <div class="error"><strong>ERROR:</strong> There was a problem with the request. </div>
            <table>
                <thead>
                    <tr><th>Condition</th><th>Response</th></tr>
                </thead>
                <tbody>
                    <tr><td>SQL search error</td><td><pre>{"Status": "ERROR", "MSG": "There was a problem retrieving the data. Try again later.", "Action": "None"}</pre></td></tr>
                    <tr><td>Default</td><td><pre>{"Status": "ERROR", "MSG": "{error message}", "Action": "None"}</pre></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>