		</div>
		<br />
		<br />
	</body>
</html>

<style>



</style>

<script>

$(document).ready(function(){

    /*$('.dropdown-menu').click(function(e) {
        e.stopPropagation();
    });*/

    $('#searchbar').keyup(function(){

        var query = $(this).val();
        if(query != '')
        {
            $.ajax({
                url:"search_action.php",
                method:"POST",
                data:{query:query},
                success:function(data)
                {
                    $('.countryList').html(data);
                }
            });
        }
        else
        {
            $('.countryList').html('');
        }

    });

    $(document).on('click', '.list-group-item', function(){
        $('#searchbar').val($.trim($(this).text()));
        //$('#countryList').fadeOut();
        $('.countryList').html('');

    });

    
    var is_open = 'no';

    $('#friend_request_area').click(function(){
        if(is_open == 'no')
        {
           load_friends_request_list_data();
           is_open = 'yes';
        }
    });

    function count_un_seen_friend_request()
    {
        var action = 'count_un_seen_friend_request';

        $.ajax({
            url:"friend_action.php",
            method:"POST",
            data:{action:action},
            success:function(data)
            {
                if(data > 0)
                {
                    $('#unseen_friend_request_area').html('<span class="label label-danger">'+data+'</span>');
                    is_open = 'no';
                }
            }
        })
    }   

    setInterval(function(){
        count_un_seen_friend_request();
    }, 5000);

    function load_friends_request_list_data()
    {
        var action = 'load_friend_request_list';
        $.ajax({
            url:"friend_action.php",
            method:"POST",
            data:{action:action},
            beforeSend:function()
            {
                $('#friend_request_list').html('<li align="center"><i class="fa fa-circle-o-notch fa-spin"></i></li>');
            },
            success:function(data)
            {
                $('#friend_request_list').html(data);
                remove_friend_request_number();
            }
        })
    }   

    function remove_friend_request_number()
    {
        $.ajax({
            url:"friend_action.php",
            method:"POST",
            data:{action:'remove_friend_request_number'},
            success:function(data)
            {
                $('#unseen_friend_request_area').html('');
            }
        })
    }

    $('.dropdown-menu').click(function(event){

        event.preventDefault();

        var request_id = event.target.getAttribute('data-request_id');

        if(request_id > 0)
        {
            $.ajax({
                url:"friend_action.php",
                method:"POST",
                data:{request_id:request_id, action:'accept_friend_request'},
                beforeSend:function()
                {
                    $('#accept_friend_request_button_'+request_id).attr('disabled', 'disabled');
                    $('#accept_friend_request_button_'+request_id).html('<i class="fa fa-circle-o-notch fa-spin"></i> wait...');
                },
                success:function()
                {
                    load_friends_request_list_data();
                }
            })
        }

        return false;

    });

    $('#friends_list').html('<div align="center" style="line-height:200px;"><i class="fa fa-circle-o-notch fa-spin"></i></div>');

    setTimeout(function(){
        load_friends();
    }, 5000);

    function load_friends(query = '')
    {
        $.ajax({
            url:"friend_action.php",
            method:"POST",
            data:{action:'load_friends', query:query},
            success:function(data)
            {
                $('#friends_list').html(data);
            }
        });
    }

    $(document).on('keyup', '#search_friend', function(){
        var search_value = $('#search_friend').val();
        if(search_value != '')
        {
            load_friends(search_value);
        }
        else
        {
            load_friends();
        }
    });

    $('#share_button').click(function(){
        var content = $('#content_area').html();

        var action = $('#share_button').data('action');

        var post_id = $('#share_button').data('post_id');

        $.ajax({
            url:"post_action.php",
            method:"POST",
            data:{content:content, action:action, post_id:post_id},
            dataType:"JSON",
            beforeSend:function()
            {
                $('#share_button').attr('disabled', 'disabled');
                $('#share_button').html('<i class="fa fa-circle-o-notch fa-spin"></i> Post');
            },
            success:function(data)
            {
                $('#share_button').attr('disabled', false);
                $('#share_button').html('Post');
                $('#content_area').html('');
                $('#temp_url_content').remove();
                $('#timeline_area').append('<div id="last_post_details"><div align="center" style="font-size:36px; color:#ccc"><i class="fa fa-circle-o-notch fa-spin"></i></div></div>');

                if(action == 'update')
                {
                    setTimeout(function(){
                        $('#last_post_details').html(make_post(data.content, data.user_image, data.user_name));

                        $('#'+data.temp_id).imagesGrid({
                            images:data.media_array
                        });

                    }, 5000);
                }
                else
                {
                    setTimeout(function(){
                        $('#last_post_details').html(make_post(data.content, data.user_image, data.user_name));
                    }, 5000);
                }
            }
        })
    });

    function make_post(content, user_image, user_name)
    {
        html = `
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-9">
                        `+user_image+`&nbsp;<a href="#"><b>`+user_name+`</b></a> <span class="text-muted">has share post</span>
                    </div>
                </div>
            </div>
            <div class="panel-body" style="font-size:20px;">
                `+content+`
            </div>
        </div>
        `;

        return html;
    }

    $('#content_area').keyup(function(){
        var content = $('#content_area').html();

        var regex = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;

        var url = content.match(regex);

        if(url != null)
        {
            if(url.length > 0)
            {
                $.ajax({
                    url:"post_action.php",
                    method:"POST",
                    data:{action:'load_url_content', url:url},
                    dataType:"json",
                    beforeSend:function()
                    {
                        $('#content_area').append('<div id="temp_url_content"></div>');
                    },
                    success:function(data)
                    {
                        var html = `
                        <a href="`+data.link+`" target="_blank">
                            <div style="background-color:#f9f9f9">
                                `+data.media+`
                                <h3 style="color:#888">`+data.title+`</h3>
                                <p class="text-muted">`+data.description+`</p>
                            </div>
                        </a>
                        `;
                        $('#temp_url_content').html(html);
                    }
                })
            }
        }
        return false;
    });

    $('#multiple_files').on('change', function(){

        var form_data = new FormData();

        var file_input = $('#multiple_files');

        var total_files = file_input[0].files.length;

        for(var count = 0; count < total_files; count++)
        {
            var file_name = file_input[0].files[count].name;

            var extension = file_name.split('.').pop().toLowerCase();

            if(jQuery.inArray(extension, ['gif','png','jpg','jpeg']) == -1)
            {
                alert('Invalid Image File');

                $('#multiple_files').val('');

                return false;
            }

            var file_size = file_input[0].files[count].size;

            if(file_size > 1000000)
            {
                alert('Image size Very Big');

                $('#multiple_files').val('');

                return false;
            }

            form_data.append('files[]', file_input[0].files[count]);
        }

        $.ajax({
            url:"upload.php",
            type:"POST",
            data:form_data,
            dataType:"json",
            contentType:false,
            processData:false,
            beforeSend:function(){
                $('#share_button').attr('disabled', 'disabled');

                $('#content_area').after('<div id="temp_url_content"><div align="center" style="font-size:36px; color:#ccc"><i class="fa fa-circle-o-notch fa-spin"></i></div></div>');
            },
            success:function(data)
            {
                $('#share_button').attr('disabled', false);
                $('#share_button').attr('data-post_id', data.post_id);
                $('#share_button').attr('data-action', 'update');
                $('#temp_url_content').html(data.html_data);
            }
        })
    });

    $(document).on('click', '.close', function(){

        var media_id = $(this).data('media_id');

        var path = $(this).data('path');

        $.ajax({
            url:"upload.php",
            method:"POST",
            data:{media_id:media_id, path:path, action:'remove_media'},
            success:function()
            {
                $('#media-'+media_id).fadeOut('slow');
            }
        });
    });

});

</script>