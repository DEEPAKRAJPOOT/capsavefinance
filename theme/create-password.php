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
							<h3>Create Password</h3>


						</div>
					</div>
				</div>
				<div class="panel-body">
					<form id="loginForm">
						<div class="form-group mb-2">
							<label class="control-label" for="username">New Pasword <span class="help-text small"></span></label>
							<input type="password" placeholder="******" required="required" value="" name="password" id="password" class="form-control">

						</div>
						<div class="form-group">
							<label class="control-label" for="password">Confirm Pasword<span class="help-text small"></span></label>

							<div class="hideShowPassword-wrapper">
								<input type="password" title="Please enter your password" value="" placeholder="******" required="required" name="password" id="password" class="form-control hideShowPassword-field">

								<button type="button" class="show-pass"><span class="fa fa-eye"></span></button>
							</div>

						</div>
						<div class="form-group mt-3 Forgot">
							<div><button class="btn btn-primary pull-right" onclick="window.location.href='password-change-msg.php'" type="button">Submit</button></div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</body>



</html>
