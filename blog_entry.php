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

    $command = "INSERT INTO blog_entry (author_name,post_date,post_text,email) VALUES ('".$db->real_escape_string($c_name)."', now(),'".$db->real_escape_string($c_post_text)."','".$db->real_escape_string($c_email)."');";

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
<body><h1>Blog Posts</h1>';


    while ($data = $result->fetch_object()) {
        print '<div class="wrapper">
               <a href="blog_entry.php?blogId=' . $data->blogId . '">View Post</a>,<br>
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

    $command_entry = "SELECT * FROM blog_entry where blogId =". $post . " order by post_date DESC;";

    $command_comment = "SELECT * FROM blog_comments WHERE blogId =" . $post . ";";

    $result_comment = $db->query($command_comment);

    $comments = '';
    while($data_comment = $result_comment->fetch_object()){

        $comments .= '<div class="comment_name">Comment by: ' . $data_comment->author_name . '</div><div class="comment_text">'. $data_comment->comment_text . '</div><div class = "comment_date">Comment posted on: ' . $data_comment->comment_date .'</div><br/>';
    }







    $result_entry = $db->query($command_entry);

    while ($data = $result_entry->fetch_object()) {
        print '<!DOCTYPE html><html lang="en">
<head>
<title>'. $data->author_name .'
</title>
<link rel="stylesheet" href="blog.css">
<body><div class="wrapper">

               <div class="entry">' . $data->post_text . '</div><div class="post_date">'. $data->post_date . '</div>
               <div class="auth_name">Post by: ' . $data->author_name . '</div>Comments:<br/><br/>';


    }

    echo $comments . '</div><!-- end .wrapper-->';
    echo '<a href="blog_entry.php?display_add_comment=1&entryId='.$post.'">Add a comment!</a><br/>';

    print '<a href="blog_entry.php">Go back to the list of posts<br></body></html>';
    $result_entry->free();
    $db->close();


}

function add_comment_form($entryId,$post){
    $blogId = $entryId;
    $user_info = $post;



    $add_comment_form = '<div>
       <form name="add_comment" action="blog_entry.php?add_comment=1" method="POST">
           <input type="text" name="entryId" hidden value="'.$blogId.'"><br/>
           <label for="comment_author">Enter your name: </label>
           <input type="text" name="comment_author">
           <label for="comment_text">Your comment:</label><br/>
           <textarea class="comment_text"  name="comment_text"></textarea><br/>
<label for="comment_email">Leave your email address to receive replies</label>
           <input type="text" name="comment_email">

           <input type="submit">
       </form>

</div>';

    return $add_comment_form;

}

function insert_comment($entryId,$post)
{
    $blogId = $entryId;
    $user_info = $post;

    $name = $user_info['comment_author'];

    $comment_text = $user_info['comment_text'];

    $comment_email = $user_info['comment_email'];

    $host = '127.0.0.1';
    $user = 'twickler';
    $pw = '123456';
    $database = 'blog';

    $db = new mysqli($host, $user, $pw, $database) or die("Cannot connect to MySQL.");


    $command = "INSERT INTO blog_comments (blogId,author_name,comment_date,comment_text,comment_email) VALUES (" . $blogId . ",'" . $db->real_escape_string($name) . "',now(),'" . $db->real_escape_string($comment_text) . "','" . $db->real_escape_string($comment_email) . "');";


    $db->query($command);

    $db->close();

    display_all_entries();
}

if ($_GET['entry'] == 1) {
    insert_blog_entry($post_text,$email,$name);
    display_all_entries();
}

elseif($_GET['blogId']) {
   display_one_entry($_GET['blogId']);


}

elseif($_GET['add_comment']){


    insert_comment($_POST['entryId'],$_POST);


}

elseif($_GET['display_add_comment']){
    $add_comment_form = add_comment_form($_GET['entryId'],$_POST);

    echo $add_comment_form;

}


else {
    display_all_entries();
}



