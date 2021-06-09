<?php

//function.php

function make_avatar($character)
{
    $path = "avatar/". time() . ".png";
    $image = imagecreate(200, 200);
    $red = rand(0, 255);
    $green = rand(0, 255);
    $blue = rand(0, 255);
    imagecolorallocate($image, $red, $green, $blue);  
    $textcolor = imagecolorallocate($image, 255,255,255);  

    imagettftext($image, 100, 0, 55, 150, $textcolor, 'font/arial.ttf', $character);  
    //header("Content-type: image/png");  
    imagepng($image, $path);
    imagedestroy($image);
    return $path;
}

function Get_user_avatar($user_id, $connect)
{
    $query = "
    SELECT user_avatar FROM register_user 
    WHERE register_user_id = '".$user_id."'
    ";

    $statement = $connect->prepare($query);

    $statement->execute();

    $result = $statement->fetchAll();

    foreach($result as $row)
    {
        return '<img src="'.$row['user_avatar'].'" width="25" class="img-circle" />';
    }
}

function load_country_list()
{
    $output = '';
    $countries = array("Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe");
    foreach($countries as $country)
    {
        $output .= '<option value="'.$country.'">'.$country.'</option>';
    }
    return $output;
}

function Get_user_profile_data($user_id, $connect)
{
    $query = "
    SELECT * FROM register_user 
    WHERE register_user_id = '".$user_id."'
    ";
    return $connect->query($query);
}

function Get_user_profile_data_html($user_id, $connect)
{
    $result = Get_user_profile_data($user_id, $connect);

    $output = '
    <div class="table-responsive">
        <table class="table">
    ';

    foreach($result as $row)
    {
        if($row['user_avatar'] != '')
        {
            $output .= '
            <tr>
                <td colspan="2" align="center" style="padding:16px 0">
                    <img src="'.$row["user_avatar"].'" width="175" class="img-thumbnail img-circle" />
                </td>
            </tr>
            ';
        }

        $output .= '
        <tr>
            <th>Name</th>
            <td>'.$row["user_name"].'</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>'.$row["user_email"].'</td>
        </tr>
        <tr>
            <th>Birth Date</th>
            <td>'.$row["user_birthdate"].'</td>
        </tr>
        <tr>
            <th>Gender</th>
            <td>'.$row["user_gender"].'</td>
        </tr>
        <tr>
            <th>Address</th>
            <td>'.$row["user_address"].'</td>
        </tr>
        <tr>
            <th>City</th>
            <td>'.$row["user_city"].'</td>
        </tr>
        <tr>
            <th>Zip</th>
            <td>'.$row["user_zipcode"].'</td>
        </tr>
        <tr>
            <th>State</th>
            <td>'.$row["user_state"].'</td>
        </tr>
        <tr>
            <th>Country</th>
            <td>'.$row["user_country"].'</td>
        </tr>
        ';
    }
    $output .= '
        </table>
    </div>
    ';

    return $output;
}

function wrap_tag($argument)
{
    return '<b>' . $argument . '</b>';
}

function Get_user_avatar_big($user_id, $connect)
{
    $query = "
    SELECT user_avatar FROM register_user 
    WHERE register_user_id = '".$user_id."'
    ";
    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();
    foreach($result as $row)
    {
        return '<img src="'.$row['user_avatar'].'" class="img-responsive img-circle" />';
    }
}

function Get_request_status($connect, $from_user_id, $to_user_id)
{
    $output = '';

    $query = "
    SELECT request_status 
    FROM friend_request 
    WHERE (request_from_id = '".$from_user_id."' AND request_to_id = '".$to_user_id."') 
    OR (request_from_id = '".$to_user_id."' AND request_to_id = '".$from_user_id."') 
    AND request_status != 'Confirm'
    ";

    $result = $connect->query($query);

    foreach($result as $row)
    {
        $output = $row["request_status"];
    }

    return $output;
}

function Get_user_name($connect, $user_id)
{
    $query = "
    SELECT user_name 
    FROM register_user 
    WHERE register_user_id = '".$user_id."' 
    ";
    $result = $connect->query($query);
    foreach($result as $row)
    {
        return $row['user_name'];
    }
}

function clean_text($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function get_date()
{
    return date("Y-m-d") . ' ' . date("H:i:s", STRTOTIME(date('h:i:sa')));
}

function file_get_contents_curl($url)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}

?>