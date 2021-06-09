<?php

//search.php

if(isset($_GET["query"]))
{
	$query = urldecode($_GET["query"]);

	$query = preg_replace("#[^a-z 0-9?!]#i", "", $query);
}

if(!isset($query))
{
	header('location:home.php');
}
else
{
	include('header.php');

?>
	
		<div class="row">
			<div class="col-md-9">
				<h3>Search Result for <b><?php echo $query; ?></b></h3>
				<div id="search_result_area">
					<div class="wrapper-preview">
						<i class="fa fa-circle-o-notch fa-spin" style="font-size:24px"></i>
					</div>
				</div>
			</div>
		</div>


		<script>
		$(document).ready(function(){
			var query_result = "<?php echo $query; ?>";

			$('#searchbar').val(query_result);

			load_data(query_result, 1);

			function load_data(query_result, page)
			{
				$.ajax({
					url:"search_action.php",
					method:"POST",
					data:{query_result:query_result, page:page},
					success:function(data)
					{
						$('#search_result_area').html(data);
					}
				})
			}

			$(document).on('click', '.page-link', function(){
				var page = $(this).data('page_number');

				if(page > 0)
				{
					load_data(query_result, page);
				}
			});

			$(document).on('click', '.request_button', function(){
				var to_id = $(this).data('userid');

				var action = 'send_request';

				if(to_id > 0)
				{	
					$.ajax({
						url:"friend_action.php",
						method:"POST",
						data:{to_id:to_id, action:action},
						beforeSend:function()
						{
							$('#request_button_'+to_id).attr('disabled', 'disabled');
							$('#request_button_'+to_id).html('<i class="fa fa-circle-o-notch fa-spin"></i> Sending...');
						},
						success:function(data)
						{
							$('#request_button_'+to_id).html('<i class="fa fa-clock-o" aria-hidden="true"></i> Request Send');
						}
					});
				}

			});

		});
		</script>
<?php

	include('footer.php');
}

?>