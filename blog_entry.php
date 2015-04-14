<?php
#-------------------#
# POST Variables    #
#-------------------#
$post_text = $_POST['post'];
$email = $_POST['email'];
$name = $_POST['first_name'] . " " . $_POST['last_name'];

#---------------#
# Functions     #
#---------------#

function insert_blog_entry($post_text,$email,$name) {
    $host = 'sql.useractive.com';
    $user = 'ptwickle';
    $pw = 'reguetraI4';
    $database = 'ptwickle';

    $c_name = $name;
    $c_email = $email;
    $c_post_text = $post_text;

    $db = new mysqli($host,$user,$pw,$database) or die("Cannot connect to MySQL.");

    $command = "INSERT INTO blog_entry (author_name,post_date,post_text,email) VALUES ('".$db->real_escape_string($c_name)."', now(),'".$db->real_escape_string($c_post_text)."','".$db->real_escape_string($c_email)."');";

    $db->query($command);

    $db->close();
}

// This function handles gathering the data in each blog post and assembling and displaying the html
function display_all_entries(){
    $host = 'sql.useractive.com';
    $user = 'ptwickle';
    $pw = 'reguetraI4';
    $database = 'ptwickle';

    $db = new mysqli($host,$user,$pw,$database) or die("Cannot connect to MySQL.");

    $command = "SELECT * FROM blog_entry order by post_date desc;";

    $result = $db->query($command);


    print '<!DOCTYPE html>
<html lang="en">
<head>
<title>My Entries
</title>
<link rel="stylesheet" href="http://ptwickle.userworld.com/phpsql1/blog.css">
<body>';

    while ($data = $result->fetch_object()) {
        print '<div class="wrapper">
               <a href="blog_entry.php?blogId=' . $data->blogId . '">View Post</a>,<br>
                 <div class="entry">' . $data->post_text . '</div><div class="post_date">'. $data->post_date . '</div><div class="auth_name">Post by: ' . $data->author_name . '</div></div>';

    }

    print '</body></html>';
    $result->free();
    $db->close();
}

#------------#
# Main	     #
#------------#

// If the query contains "entry" and "entry" is set to 1, add the info to the database
if ($_GET['entry'] == 1) {
    insert_blog_entry($post_text,$email,$name);
    display_all_entries();
}

// If the query does not contain "entry" but, rather, it contains "blogId," display that post instead
// all the posts.
elseif($_GET['blogId']) {
    $blogId = $_GET['blogId'];

    $host = 'sql.useractive.com';
    $user = 'ptwickle';
    $pw = 'reguetraI4';
    $database = 'ptwickle';

    $db = new mysqli($host,$user,$pw,$database) or die("Cannot connect to MySQL.");

    $command = "SELECT * FROM blog_entry where blogId =". $blogId . ";";

    $result = $db->query($command);

    // Loops through the result object to pick out the bits of data and pumps them into html
    while ($data = $result->fetch_object()) {
        print '<!DOCTYPE html><html lang="en">
          <head>
            <title>'. $data->author_name .'
            </title>
            <link rel="stylesheet" href="http://ptwickle.userworld.com/phpsql1/blog.css">
          <body>
            <div class="wrapper">
              <div class="entry_display">' . $data->post_text . '</div>
              <div class="post_date">'. $data->post_date . '</div>
              <div class="auth_name">Post by: ' . $data->author_name . '</div>
            </div>';
    }

    print '<a href="blog_entry2.php">Go back to the list of posts </body></html>';
    $result->free();
    $db->close();
}

// If GET is not set, meaning they have not entered a new post by submitting the form, nor have
// they clicked a particular post in the list of posts, just display the list.
else {
    display_all_entries();
}



