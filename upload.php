<?php

//upload.php

include('database_connection.php');

include('function.php');

session_start();

if(isset($_FILES['files']))
{
	$post_data = array(
		':user_id'		=>	$_SESSION["user_id"],
		':post_content'	=>	'',
		':post_code'	=>	md5(uniqid()),
		':post_datetime'=>	get_date(),
		':post_status'	=>	'Draft',
		':post_type'	=>	'Media'
	);

	$insert_query = "
	INSERT INTO posts_table 
	(user_id, post_content, post_code, post_datetime, post_status, post_type) 
	VALUES (:user_id, :post_content, :post_code, :post_datetime, :post_status, :post_type)
	";

	$statement = $connect->prepare($insert_query);

	$statement->execute($post_data);

	$post_id = $connect->lastInsertId();

	$html_data = '';

	for($count = 0; $count < count($_FILES['files']["name"]); $count++)
	{
		$name_array = explode(".", $_FILES['files']["name"][$count]);

		$extension = end($name_array);

		$new_file_name = rand(100000000,999999999) . '.' . $extension;

		$location = 'upload/' . $new_file_name;

		$tmp_name = $_FILES['files']["tmp_name"][$count];

		move_uploaded_file($tmp_name, $location);

		$media_data = array(
			':post_id'		=>	$post_id,
			':media_path'	=>	$location
		);

		$media_insert_query = "
		INSERT INTO media_table 
		(post_id, media_path) 
		VALUES (:post_id, :media_path)
		";

		$statement = $connect->prepare($media_insert_query);

		$statement->execute($media_data);

		$media_id = $connect->lastInsertId();

		$html_data .= '
		<div class="col-lg-2 col-md-3 col-4" style="margin-bottom:8px;" id="media-'.$media_id.'">
			<img src="'.$location.'" class="img-responsive" />
			<button type="button" class="close" data-media_id="'.$media_id.'" data-path="'.$location.'" aria-label="Close" style="position: absolute;top: 0;right: 18px;">
			  	<span aria-hidden="true">&times;</span>
			</button>
		</div>
		';
	}

	$output = array(
		'html_data'		=>	$html_data,
		'post_id'		=>	$post_id
	);

	echo json_encode($output);

}

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'remove_media')
	{
		unlink($_POST["path"]);

		$query = "
		DELETE FROM media_table 
		WHERE media_id = '".$_POST["media_id"]."'
		";

		$connect->query($query);
	}
}

?>