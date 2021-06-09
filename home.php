<?php

//home.php

include('header.php');

?>
<style>
[contenteditable] {
  outline: 0px solid transparent;
  min-height:100px;
  height: auto;
  cursor: auto;
}

[contenteditable]:empty:before {
    content: attr(placeholder);
    color:#ccc;
    cursor: auto;
}

[placeholder]:empty:focus:before {
    content: "";
}

#temp_url_content a
{
	text-decoration: none;
}
#temp_url_content a:hover
{
	text-decoration: none;
}
#temp_url_content h3, #temp_url_content p
{
	padding:0 16px 16px 16px;
}

.fileinput-button input {
	    position: absolute;
	    top: 0;
	    right: 0;
	    margin: 0;
	    height: 100%;
	    opacity: 0;
	    filter: alpha(opacity=0);
	    font-size: 200px !important;
	    direction: ltr;
	    cursor: pointer;
	}
</style>
			<div class="row">
				<div class="col-md-8">
					<div class="panel panel-default">
						<div class="panel-heading">
							<div class="row">
		    					<div class="col-md-6">
									<h3 class="panel-title">Create Post</h3>
								</div>
								<div class="col-md-6 text-right">
									<span class="btn btn-success btn-xs fileinput-button">
										<span>Add Files...</span>
										<input type="file" name="files[]" id="multiple_files" multiple />
									</span>
								</div>
							</div>
						</div>
						<div class="panel-body">
							<div id="content_area" contenteditable="true" placeholder="Write Something...."></div>
						</div>
						<div class="panel-footer" align="right">
							<button type="button" name="share_button" id="share_button" class="btn btn-primary btn-sm">Post</button>
						</div>
					</div>
					<br />
					<div id="timeline_area"></div>
				</div>
				<div class="col-md-4">
					<div class="panel panel-default">
						<div class="panel-heading">
							<div class="row">
								<div class="col-xs-6">
									<h3 class="panel-title">Friends</h3>
								</div>
								<div class="col-xs-6">
									<input type="text" name="search_friend" id="search_friend" class="form-control input-sm" placeholder="Search" />
								</div>
							</div>
						</div>
						<div class="panel-body pre-scrollable">
							<div id="friends_list"></div>
						</div>
					</div>
				</div>
			</div>
<?php

include('footer.php');

?>