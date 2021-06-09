<?php

//search_action.php

include('database_connection.php');

include('function.php');

session_start();

if(isset($_POST["query"]))
{
	$search_query = preg_replace('#[^a-z 0-9?!]#i', '', $_POST["query"]);

	$search_array = explode(" ", $search_query);

	$replace_array = array_map('wrap_tag', $search_array);

	$condition = '';

	foreach($search_array as $search)
	{
		if(trim($search) != '')
		{
			$condition .= "user_name LIKE '%".$search."%' OR ";
		}
	}
	$condition = substr($condition, 0, -4);

	$query = "
	SELECT * FROM register_user 
    WHERE ".$condition." 
    AND register_user_id != '".$_SESSION["user_id"]."' 
    AND user_email_status = 'verified' 
    LIMIT 10
	";

	$statement = $connect->prepare($query);

	$statement->execute();

	$output = '<div class="list-group">';

	if($statement->rowCount() > 0)
	{
		foreach($statement->fetchAll() as $row)
		{
			$temp_text = $row["user_name"];
			$temp_text = str_ireplace($search_array, $replace_array, $temp_text);
			$output .= '
			<a href="#" class="list-group-item">
				' . $temp_text . '
                <div class="pull-left">
                    '.Get_user_avatar($row["register_user_id"], $connect).' &nbsp;
                </div> 
			</a>
			';
		}
	}
	else
	{
		$output .= '<a href="#" class="list-group-item">No Result Found</a>';
	}
	$output .= '</div>';

	echo $output;
}

if(isset($_POST["query_result"]))
{
	$output = '';
    //$query_string = str_replace(" ", ", ", $_POST["query_result"]);

    $search_array = explode(" ", $_POST["query_result"]);

    $condition = '';
    foreach ($search_array as $search)
    {
        if(trim($search) != '')
        {
            $condition .= "user_name LIKE '%".$search."%' OR ";
        }
    }

    $condition = substr($condition, 0, -4);

    $limit = 5;

    $page = 1;

    if($_POST['page'] > 1)
    {
        $start = (($_POST['page'] - 1) * $limit);
        $page = $_POST['page'];
    }
    else
    {
        $start = 0;
    }

    $query = "
    SELECT * FROM register_user 
    WHERE ".$condition." 
    AND register_user_id != '".$_SESSION["user_id"]."' 
    AND user_email_status = 'verified' 
    ";

    $filter_query = $query . 'LIMIT '.$start.', '.$limit.'';

    $statement = $connect->prepare($query);

    $statement->execute();

    $total_data = $statement->rowCount();

    $statement = $connect->prepare($filter_query);

    $statement->execute();

    $result = $statement->fetchAll();

    if($total_data > 0)
    {
        foreach($result as $row)
        {
            $button = '';

            $status = Get_request_status($connect, $_SESSION['user_id'], $row['register_user_id']);

            if($status == 'Pending')
            {
                $button = '
                <button type="button" name="request_button" class="btn btn-primary" disabled><i class="fa fa-clock-o" aria-hidden="true"></i> Pending</button>
                ';
            }
            else if($status == 'Reject')
            {
                $button = '
                <button type="button" name="request_button" class="btn btn-warning" disabled><i class="fa fa-ban" aria-hidden="true"></i> Reject</button>
                ';
            }
            else
            {
                $button = '
                <button type="button" name="request_button" class="btn btn-primary request_button" id="request_button_'.$row["register_user_id"].'" data-userid="'.$row["register_user_id"].'"><i class="fa fa-user-plus"></i> Add Friend</button>
                ';
            }

            $output .= '
            <div class="wrapper-box">
                <div class="row">
                    <div class="col-md-1 col-sm-3 col-xs-3">
                        '. Get_user_avatar_big($row["register_user_id"], $connect) .'
                    </div>
                    <div class="col-md-8 col-sm-6 col-xs-5">
                        <div class="wrapper-box-title">'.$row["user_name"].'</div>
                        <div class="wrapper-box-description"><i>From '.$row["user_country"].'</i></div>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-4" align="right">
                        '.$button.'
                    </div>
                </div>
            </div>
        ';
        }
    }
    else
    {
        $output .= '
        <div class="wrapper-box">
            <h4 align="center">No Data Found</h4>
        </div>
        ';
    }

    $output .= '
    <br />
    <div align="center">
        <ul class="pagination">
    ';

    $total_links = ceil($total_data/$limit);

    $previous_link = '';

    $next_link = '';

    $page_link = '';

    if($total_links > 5)
    {
        if($page < 5)
        {
            for($count = 1; $count <= 5; $count++)
            {
                $page_array[] = $count;
            }
            $page_array[] = '...';
            $page_array[] = $total_links;
        }
        else
        {
            $end_limit = $total_links - 5;

            if($page > $end_limit)
            {
                $page_array[] = 1;
                $page_array[] = '...';

                for($count = $end_limit; $count <= $total_links; $count++)
                {
                    $page_array[] = $count;
                }
            }
            else
            {
                $page_array[] = 1;
                $page_array[] = '...';
                for($count = $page - 1; $count <= $page + 1; $count++)
                {
                    $page_array[] = $count;
                }
                $page_array[] = '...';
                $page_array[] = $total_links;
            }
        }
    }
    else
    {
        for($count = 1; $count <= $total_links; $count++)
        {
            $page_array[] = $count;
        }
    }

    for($count = 0; $count < count($page_array); $count++)
    {
        if($page == $page_array[$count])
        {
            $page_link .= '
            <li class="page-item active">
                <a class="page-link" href="#">'.$page_array[$count].' <span class="sr-only">(current)</span></a>
            </li>
            ';  

            $previous_id = $page_array[$count] - 1;

            if($previous_id > 0)
            {
                $previous_link = '<li class="page-item"><a class="page-link" href="javascript:void(0)" data-page_number="'.$previous_id.'">Previous</a></li>';
            }
            else
            {
                $previous_link = '
                <li class="page-item disabled">
                    <a class="page-link" href="#">Previous</a>
                 </li>
                ';
            }

            $next_id = $page_array[$count] + 1;

            if($next_id > $total_links)
            {
                $next_link = '
                <li class="page-item disabled">
                    <a class="page-link" href="#">Next</a>
                </li>
                ';
            }
            else
            {
                $next_link = '
                <li class="page-item"><a class="page-link" href="javascript:void(0)" data-page_number="'.$next_id.'">Next</a></li>
                ';
            }

        }
        else
        {
            if($page_array[$count] == '...')
            {
                $page_link .= '
                <li class="page-item disabled">
                    <a class="page-link" href="#">...</a>
                 </li>
                ';
            }
            else
            {
                $page_link .= '
                <li class="page-item"><a class="page-link" href="javascript:void(0)" data-page_number="'.$page_array[$count].'">'.$page_array[$count].'</a></li>
                ';
            }
        }
    }

    $output .= $previous_link . $page_link . $next_link;

    echo $output;
}

?>