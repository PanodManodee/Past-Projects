<!DOCTYPE html>
<html>
<head>
<title>CSS326 Sample</title>
<link rel="stylesheet" href="test.css">
</head>

<body>

<div id="wrapper"> 
	<div id="div_header">
    CSS326 System
    </div>
    <div id="div_subhead">
    <ul id="menu">
        <li><a href="user.php">User Profile</a></li>
        <li><a href="add_user.php">Add User</a></li>
        <li><a href="group.php">User Group</a></li>
        <li><a href="add_group.php">Add User Group</a></li>
    </ul>
    </div>
	<div id="div_main">
		<div id="div_left">
				
		</div>
		<div id="div_content" class="form">
			<!--%%%%% Main block %%%%-->
			<!--Form -->
				<h2>Add User Group</h2>
				<form action="group.php" method="post">
				<label>Group Code</label>
				<input type="text" name="groupcode">
				
				<label>Group Name</label>
				<input type="text" name="groupname">
				
				<label>Remark</label>
				<textarea name="remark" placeholder="Description"></textarea><br>
					
				<label>URL</label>
				<input type="text" name="url">
					
				<div class="center">
					<input type="submit" name="submit" value="Submit">
					<input type="reset" value="Cancel">											
				</div>
				</form>
		</div> <!-- end div_content -->
		
	</div> <!-- end div_main -->
	
	<div id="div_footer">  
		
	</div>

</div>
</body>
</html>

