<!DOCTYPE html>
<html lang="en">

<head>
	<title>Login V14</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

	<link href="css/login-form.css" type="text/css" rel="stylesheet">

</head>

<body>



	<div class="login-wrapper col-sm-6 col-sm-offset-3">
		<div class="container-center">
			<div class="panel mb-0">
				<div class="panel-heading">
					<div class="view-header">
						<div class="logo-box p-2"><img src="images/logo.png"></div>

						<div class="header-title">
							<h3> Password assistance </h3>
							<small>
								<strong>Please enter your enter Register Email Id.</strong>
							</small>



							<div class="failed">
								<div style="color:#FF0000">
									<strong class="erro-sms">
									</strong>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<form id="loginForm">
						<div class="form-group mb-2">
							<label class="control-label" for="username">Enter Email Id <span class="help-text small"></span></label>
							<input type="email" placeholder="Enter  Email Id" required="required" value="" name="email" id="email" class="form-control">

						</div>

						<div class="form-group mt-3 Forgot">
							<div><button class="btn btn-primary pull-right" onclick="window.location.href='thanks-link.php'" type="button">Submit</button></div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</body>



</html>
