<?php

$post_text = $_POST['post'];
$email = $_POST['email'];
$name = $_POST['first_name'] . " " . $_POST['last_name'];

function insert_blog_entry($post_text,$email,$name) {
    $host = '127.0.0.1';
    $user = 'twickler';
    $pw = '123456';
    $database = 'blog';

    $c_name = $name;
    $c_email = $email;
    $c_post_text = $post_text;

    $db = new mysqli($host,$user,$pw,$database) or die("Cannot connect to MySQL.");

    $command = "INSERT INTO blog_entry (receivename,post_date,post_text,email) VALUES ('".$db->real_escape_string($c_name)."', now(),'".$db->real_escape_string($c_post_text)."','".$db->real_escape_string($c_email)."');";

    $db->query($command);

    $db->close();
}

function display_all_entries(){
    $host = '127.0.0.1';
    $user = 'twickler';
    $pw = '123456';
    $database = 'blog';

    $db = new mysqli($host,$user,$pw,$database) or die("Cannot connect to MySQL.");

    $command = "SELECT * FROM blog_entry;";

    $result = $db->query($command);


    print '<!DOCTYPE html>
<html lang="en">
<head>
<title>My Entries
</title>
<link rel="stylesheet" href="blog.css">
<body><h1>Blog Posts</h1>
<table BORDER="1">
 <tr><td>Name</td><td>Phone Number</td>
<td>Address</td><td>Birthday</td></tr>';


    while ($data = $result->fetch_object()) {
        print '<div class="wrapper">
               <a href="blog_entry2.php?blogId=' . $data->blogId . '">View Post</a>,<br>
                 <div class="entry">' . $data->post_text . '</div><div class="post_date">'. $data->post_date . '</div><div class="auth_name">Post by: ' . $data->author_name . '</div></div>';

    }

    print '<a href="blog.html">Add a Post!</a></body></html>';
    $result->free();
    $db->close();

}

function display_one_entry($blogId){

    $post = $blogId;

    $host = '127.0.0.1';
    $user = 'twickler';
    $pw = '123456';
    $database = 'blog';

    $db = new mysqli($host,$user,$pw,$database) or die("Cannot connect to MySQL.");

    $command = "SELECT * FROM blog_entry where blogId =". $post . " order by post_date DESC;";


    $result = $db->query($command);

    while ($data = $result->fetch_object()) {
        print '<!DOCTYPE html><html lang="en">
<head>
<title>'. $data->author_name .'
</title>
<link rel="stylesheet" href="blog.css">
<body><div class="wrapper">

               <div class="entry">' . $data->post_text . '</div><div class="post_date">'. $data->post_date . '</div>
               <div class="auth_name">Post by: ' . $data->author_name . '</div></div>';

    }
    echo '<a href="blog_entry2.php?display_add_comment=1">Add a comment!</a><br/>';

    print '<a href="blog_entry2.php">Go back to the list of posts<br></body></html>';
    $result->free();
    $db->close();


}

function add_comment_form($entryId,$post){
    $blogId = $entryId;



    $add_comment_form = '<div>
       <form name="add_comment" action="blog_entry2.php?add_comment=1" method="POST">
           <input type="text" name="entryId" hidden value="'.$blogId.'">
           <input type="text" name="comment_author">
           <label for="comment_author">Enter your name: </label>
           <input type="text" width="100px" height="200px" name="comment_text">
             <label for="comment_text">Your comment:</label>

           <input type="text" name="comment_email">
             <label for="comment_email">Leave your email address to receive replies</label>
           <input type="submit">
       </form>

</div>';

    return $add_comment_form;

}


if ($_GET['entry'] == 1) {
    insert_blog_entry($post_text,$email,$name);
    display_all_entries();
}

elseif($_GET['blogId']) {
   display_one_entry($_GET['blogId']);


}

elseif($_GET['add_comment']){
    //call add_comment function


}

elseif($_GET['display_add_comment']){
    $add_comment_form = add_comment_form($_GET['blogId'],$_POST);

    echo $add_comment_form;

}

elseif($_GET['add_comment']){
    
}

else {
    display_all_entries();
}



