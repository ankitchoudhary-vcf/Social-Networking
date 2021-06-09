<?php

//profile.php

include('header.php');

$message = '';

if(isset($_POST["edit"]))
{
	if(empty($_POST["user_name"]))
	{
		$message = '<div class="alert alert-danger">Name is required</div>';
	}
	else
	{
		if(isset($_FILES["user_avatar"]["name"]))
		{
			if($_FILES["user_avatar"]["name"] != '')
			{
				$image_name = $_FILES["user_avatar"]["name"];

				$valid_extensions = array('jpg', 'jpeg', 'png');

				$extension = pathinfo($image_name, PATHINFO_EXTENSION);

				if(in_array($extension, $valid_extensions))
				{
					$upload_path = 'avatar/' . time() . '.' . $extension;
					if(move_uploaded_file($_FILES["user_avatar"]["tmp_name"], $upload_path))
					{
						$user_avatar = $upload_path;
					}
				}
				else
				{
					$message .= '<div class="alert alert-danger">Only .jpg, .jpeg and .png Image allowed to upload</div>';
				}
			}
			else
			{
				$user_avatar = $_POST["hidden_user_avatar"];
			}
		}
		else
		{
			$user_avatar = $_POST["hidden_user_avatar"];
		}

		if($message == '')
		{
			$data = array(
				':user_name'				=>	$_POST["user_name"],
				':user_avatar'			=>	$user_avatar,
				':user_birthdate'		=>	$_POST["user_birthdate"],
				':user_gender'			=>	$_POST["user_gender"],
				':user_address'			=>	$_POST["user_address"],
				':user_city'				=>	$_POST["user_city"],
				':user_zipcode'			=>	$_POST["user_zipcode"],
				':user_state'				=>	$_POST["user_state"],
				':user_country'			=>	$_POST["user_country"],
				':register_user_id'	=>	$_POST["register_user_id"]
			);

			$query = "
			UPDATE register_user 
			SET user_name = :user_name, 
			user_avatar = :user_avatar, 
			user_birthdate = :user_birthdate, 
			user_gender = :user_gender, 
			user_address = :user_address, 
			user_city = :user_city, 
			user_zipcode = :user_zipcode, 
			user_state = :user_state, 
			user_country = :user_country 
			WHERE register_user_id = :register_user_id
			";

			$statement = $connect->prepare($query);

			$statement->execute($data);

			header("location:profile.php?action=view&success=1");
		}
	}
}

?>

<div class="row">
	<div class="col-md-9">
	<?php
	if(isset($_GET["action"]))
	{
		if($_GET["action"] == "view")
		{
			if(isset($_GET["success"]))
			{
				echo '
				<div class="alert alert-success">Profile Edited Successfully</div>
				';
			}
	?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="row">
					<div class="col-md-9">
						<h3 class="panel-title">Profile Details</h3>
					</div>
					<div class="col-md-3" align="right">
						<a href="profile.php?action=edit" class="btn btn-success btn-xs">Edit</a>
					</div>
				</div>
			</div>
			<div class="panel-body">
				<?php
				echo Get_user_profile_data_html($_SESSION["user_id"], $connect);
				?>
			</div>
		</div>
	<?php
		}

		if($_GET["action"] == 'edit')
		{
			$result = Get_user_profile_data($_SESSION["user_id"], $connect);

			foreach($result as $row)
			{
	?>
		<script>
		$(document).ready(function(){

			$('#user_gender').val("<?php echo $row["user_gender"]; ?>");

			$('#user_country').val("<?php echo $row["user_country"]; ?>");

			$('#user_birthdate').datepicker({
				assumeNearbyYear: true,
				format:'yyyy-mm-dd'
			});
		});
		</script>
		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="row">
					<div class="col-md-9">
						<h3 class="panel-title">Edit Profile</h3>
					</div>
					<div class="col-md-3" align="right">
						<a href="profile.php?action=view" class="btn btn-primary btn-xs">View</a>
					</div>
				</div>
			</div>
			<div class="panel-body">
				<?php
				echo $message;
				?>
				<form method="post" enctype="multipart/form-data">
					<div class="form-group">
						<div class="row">
							<label class="col-md-4" align="right">Name</label>
							<div class="col-md-8">
								<input type="text" name="user_name" id="user_name" class="form-control" value="<?php echo $row["user_name"];  ?>" />
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="row">
							<label class="col-md-4" align="right">Date of Birth</label>
							<div class="col-md-8">
								<input type="text" name="user_birthdate" id="user_birthdate"  class="form-control" readonly value="<?php echo $row["user_birthdate"]; ?>" />
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="row">
							<label class="col-md-4" align="right">Gender</label>
							<div class="col-md-8">
								<select name="user_gender" id="user_gender" class="form-control">
									<option value="Male">Male</option>
									<option value="Female">Female</option>
								</select>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="row">
							<label class="col-md-4" align="right">Address</label>
							<div class="col-md-8">
								<input type="text" name="user_address" id="user_address" class="form-control" value="<?php echo $row["user_address"]; ?>" />
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="row">
							<label class="col-md-4" align="right">City</label>
							<div class="col-md-8">
								<input type="text" name="user_city" id="user_city" class="form-control" value="<?php echo $row["user_city"];  ?>" />
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="row">
							<label class="col-md-4" align="right">Zipcode</label>
							<div class="col-md-8">
								<input type="text" name="user_zipcode" id="user_zipcode" class="form-control" value="<?php echo $row["user_zipcode"]; ?>" />
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="row">
							<label class="col-md-4" align="right">State</label>
							<div class="col-md-8">
								<input type="text" name="user_state" id="user_state" class="form-control" value="<?php echo $row["user_state"]; ?>" />
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="row">
							<label class="col-md-4" align="right">Country</label>
							<div class="col-md-8">
								<select name="user_country" id="user_country" class="form-control">
									<option value="">Select Country</option>
									<?php 

									echo load_country_list();

									?>
								</select>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="row">
							<label class="col-md-4" align="right">Profile</label>
							<div class="col-md-8">
								<input type="file" name="user_avatar" />
								<br />
								<?php
								Get_user_avatar($row["register_user_id"], $connect);
								?>
								<br />
								<input type="hidden" name="hidden_user_avatar" value="<?php echo $row["user_avatar"]; ?>" />
								<br />
							</div>
						</div>
					</div>
					<div class="form-group" align="center">
						<input type="hidden" name="register_user_id" value="<?php echo $row["register_user_id"]; ?>" />
						<input type="submit" name="edit" class="btn btn-primary" value="Edit" />
					</div>
				</form>
			</div>
		</div>
	<?php
			}
		}
	}
	?>

	</div>
	<div class="col-md-3">

	</div>
</div>

<?php

include('footer.php');

?>