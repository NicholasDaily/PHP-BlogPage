<?php 
	session_start(); 
	session_destroy();
?>
<!DOCTYPE HTML>
<html>

<head>
	<title>United Huskies-Login</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<style>
	*{
	box-sizing: border-box;
}

html,
body{
	width: 100%;
	min-height: 100%;
	padding: 0;
	margin: 0;
	background: #404040;
}

header{
	width: 100%;
	margin-bottom: 5px;
	border-bottom: solid 2px black;
	background: white;
	border-bottom-right-radius: 5px;
	border-bottom-left-radius: 5px;
}
#wrapper{
	background: lightgray;
	margin-left: 5px;
	margin-right: 5px;
	border-radius: 3px;
	padding: 5px;
	min-height: 500px;
	position: relative;
}

form {
	background: #ffdab9;
	border-radius: 5px;
	padding: 10px;
	
}

input {
	display: block;
	margin-top: 10px;
	margin-bottom: 10px;
	margin-left: auto;
	margin-right: auto;
	width: calc(100% - 40px);
}

button{
	margin-left: auto;
	margin-right: auto;
	display: block;
	padding: 10px;
	width: calc(100% - 40px);
}
	</style>
</head>

<body>
	<div id="wrapper">
		<form id="login">
			<input name="email" type="email" id="email" placeholder="email@google.com" required>
			<input name="password" type="password" id="password" placeholder="password" required>
			<button>Sign in</button>
		</form>
		
		<script>
			var form = document.getElementById("login");
			var button = document.querySelector("#login > button");
			form.addEventListener('submit', function (event){
				event.preventDefault();
				button.click();
			});
			
			button.addEventListener('click', function(){
				var request = new XMLHttpRequest();
				request.open("POST", "controllers/login-validation.php");
				var formData = new FormData(form);
				request.send(formData);
				
				request.onreadystatechange = function(){
						if(request.readyState === 4){
							if(request.status === 200){
								var response = parseInt(request.responseText);
								if(response === 0){
									alert("Invalid login attempt");
								}else if(response === 1){
									window.location.replace("index.php");
								}else{
									alert(request.responseText);
								}
							}
						}
				}
			});
		</script>
	</div>
</body>