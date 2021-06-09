<?php

//friend_action.php

include('database_connection.php');

include('function.php');

session_start();

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'send_request')
	{
		sleep(5);
		$data = array(
			':request_from_id'				=>	$_SESSION['user_id'],
			':request_to_id'				=>	$_POST['to_id'],
			':request_status'				=>	'Pending',
			':request_notification_status'	=>	'No'
		);

		$query = "
		INSERT INTO friend_request 
		(request_from_id, request_to_id, request_status, request_notification_status) 
		VALUES (:request_from_id, :request_to_id, :request_status, :request_notification_status)
		";

		$statement = $connect->prepare($query);

		$statement->execute($data);
	}

	if($_POST["action"] == "count_un_seen_friend_request")
	{
		$query = "
		SELECT COUNT(request_id) as Total 
	    FROM friend_request 
	    WHERE request_to_id = '".$_SESSION["user_id"]."' 
	    AND request_status = 'Pending' 
	    AND request_notification_status = 'No'
		";

		$result = $connect->query($query);

		foreach($result as $row)
		{
			echo $row['Total'];
		}
	}

	if($_POST["action"] == "load_friend_request_list")
	{
		sleep(5);
		$query = "
		SELECT * FROM friend_request 
		WHERE request_to_id = '".$_SESSION["user_id"]."' 
	    AND request_status = 'Pending' 
	    ORDER BY request_id DESC
		";

		$result = $connect->query($query);

		$output = '';

		foreach($result as $row)
		{
			$user_data = Get_user_profile_data($row["request_from_id"], $connect);

			$user_name = '';

			foreach($user_data as $user_row)
			{
				$user_name = $user_row["user_name"];
			}

			$output .= '
			<li>
				'.Get_user_avatar($row["request_from_id"], $connect).'&nbsp;<b class="text-primary">'.$user_name . '</b>
				<button type="button" name="accept_friend_request_button" class="btn btn-primary btn-xs pull-right accept_friend_request_button" data-request_id="'.$row["request_id"].'" id="accept_friend_request_button_'.$row["request_id"].'"><i class="fa fa-plus" aria-hidden="true"></i> Accept</button>
			</li>
			';
		}
		echo $output;
	}

	if($_POST["action"] == 'remove_friend_request_number')
	{
		$query = "
		UPDATE friend_request 
		SET request_notification_status = 'Yes' 
		WHERE request_to_id = '".$_SESSION["user_id"]."' 
		AND request_notification_status = 'No'
		";
		$connect->query($query);
	}

	if($_POST["action"] == 'accept_friend_request')
	{
		sleep(5);
		$query = "
		UPDATE friend_request 
		SET request_status = 'Confirm' 
		WHERE request_id = '".$_POST["request_id"]."'
		";
		$connect->query($query);
	}

	if($_POST["action"] == 'load_friends')
	{
		$condition = '';

		if(!empty($_POST["query"]))
		{
			$search_query = preg_replace('#[^a-z 0-9?!]#i', '', $_POST["query"]);

			$search_array = explode(" ", $search_query);

			$condition = ' AND (';

			foreach($search_array as $search)
			{
				if(trim($search) != '')
				{
					$condition .= "register_user.user_name LIKE '%".$search."%' OR ";
				}
			}

			$condition = substr($condition, 0, -4) . ") ";
		}

		$query = "
		SELECT register_user.user_name, friend_request.request_from_id, friend_request.request_to_id FROM register_user 
		INNER JOIN friend_request 
		ON friend_request.request_from_id = register_user.register_user_id 
		OR friend_request.request_to_id = register_user.register_user_id 
		WHERE (friend_request.request_from_id = '".$_SESSION["user_id"]."' OR friend_request.request_to_id = '".$_SESSION["user_id"]."') 
		AND register_user.register_user_id != '".$_SESSION["user_id"]."'
		AND friend_request.request_status = 'Confirm' 
		".$condition."
		GROUP BY register_user.user_name 
		ORDER BY friend_request.request_id DESC
		";	

		$statement = $connect->prepare($query);

		$statement->execute();

		$html = '';

		if($statement->rowCount() > 0)
		{
			
			$count = 0;

			foreach($statement->fetchAll() as $row)
			{
				$temp_user_id = 0;

				if($row["request_from_id"] == $_SESSION["user_id"])
				{
					$temp_user_id = $row["request_to_id"];
				}
				else
				{
					$temp_user_id = $row["request_from_id"];
				}

				$count++;

				if($count == 1)
				{
					$html .= '<div class="row">';
				}

				$html .= '
				<div class="col-md-4" style="margin-bottom:12px;">
					'.Get_user_avatar_big($temp_user_id, $connect).'
					<div align="center"><b><a href="#" style="font-size:12px;">'.Get_user_name($connect, $temp_user_id).'</a></b></div>
				</div>
				';

				if($count == 3)
				{
					$html .= '</div>';
					$count = 0;
				}
			}
		}
		else
		{
			$html = '<h4 align="center">No Friends Found</h4>';
		}
		echo $html;
	}
}

?>