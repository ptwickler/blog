<?php

$post_text = $_POST['post'];
$email = $_POST['email'];
$name = $_POST['first_name'] . " " . $_POST['last_name'];

// Sets up the database connection. Returns the database connection object
function db_connect(){
    $host = '127.0.0.1';
    $user = 'twickler';
    $pw = '123456';
    $database = 'blog';

    $db = new mysqli($host,$user,$pw,$database) or die("Cannot connect to MySQL.");

    return $db;

}

// Inserts an entry from the add an entry form into the db.
function insert_blog_entry($post_text,$email,$name) {
   $db = db_connect();

    $c_name = $name;
    $c_email = $email;
    $c_post_text = $post_text;

    $command = "INSERT INTO blog_entry (author_name,post_date,post_text,email) VALUES ('".$db->real_escape_string($c_name)."', now(),'".$db->real_escape_string($c_post_text)."','".$db->real_escape_string($c_email)."');";

    $db->query($command);

    $db->close();
}

// Gets the content from the db and builds the HTML to display the list of all blog entries.
function display_all_entries(){
    $db = db_connect();

    $command = "SELECT * FROM blog_entry;";

    $result = $db->query($command);


    echo '<!DOCTYPE html>
<html lang="en">
<head>
<title>My Entries
</title>
<link rel="stylesheet" href="blog.css">
<body><h1>Blog Posts</h1>';

    // Iterates through the db-returned data and builds the HTML to view all the entries.
    while ($data = $result->fetch_object()) {
        print '<div class="wrapper">
               <h3><a href="blog_entry.php?blogId=' . $data->blogId . '">View Post</a></h3>
                 <div class="entry">' . $data->post_text . '</div><div class="post_date">'. $data->post_date . '</div><div class="auth_name">Post by: ' . $data->author_name . '</div></div>';

    }

    print '<h2><a class="add" href="blog.html">Add a Post!</a></h2></body></html>';
    $result->free();
    $db->close();

}


// Pulls entry and comment content from db and generates the HTML to display a single entry and the comments
// associated with it.
function display_one_entry($blogId){
    $db = db_connect();

    $post = $blogId;

    $command_entry = "SELECT * FROM blog_entry WHERE blogId =". $post . " ORDER BY post_date DESC;";

    $command_comment = "SELECT * FROM blog_comments WHERE blogId =" . $post . ";";

    $result_comment = $db->query($command_comment);

    $comments = '<div class="comment_entry_wrapper">';

    // Iterates through the results of the comments and builds the HTML for the comments.
    while($data_comment = $result_comment->fetch_object()){

        $comments .= '<div class="comment_name">Comment by: ' . $data_comment->author_name . '</div><!-- end .comment_name--><div class="comment_text">'. $data_comment->comment_text . '</div><!-- end .comment_text--><div class = "comment_date">Comment posted on: ' . $data_comment->comment_date .'</div><!-- end .comment_date--><div class="comment_email"><a href="mailto"' .$data_comment->comment_email .'">'.$data_comment->comment_email .'</a></div><!-- end .comment_email-->';
    }

    $comments .='</div><!--end .comment_wrapper-->';


    $result_entry = $db->query($command_entry);

    // Iterates through the result object for the blog entry and builds in the HTML.
    while ($data = $result_entry->fetch_object()) {
        print '<!DOCTYPE html><html lang="en">
        <head>
          <title>'. $data->author_name .'
          </title>
          <link rel="stylesheet" href="blog.css">
        <body>
          <div class="wrapper">
          <div class="entry"><h2>Post: </h2>' . $data->post_text . '<div class="post_date">'. $data->post_date . '</div><!-- end .post_date-->
          <div class="auth_name">Post by: ' . $data->author_name . '</div></div><!-- end .entry-->
          <div class="comment_wrapper"><h3>Comments:</h3>';

    }

    echo $comments . '</div><!-- end .comment_wrapper--></div><!-- end .wrapper-->';
    echo '<a href="blog_entry.php?display_add_comment=1&entryId='.$post.'">Add a comment!</a><br/>';

    echo '<a href="blog_entry.php">Go back to the list of posts
     </body>
     </html>';

    $result_entry->free();
    $db->close();
}

// Displays the add a comment form, adding in the blogId to associate it with a particular blog entry.
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


// Adds a comment to the db.
function insert_comment($entryId,$post){
    $db = db_connect();

    $blogId = $entryId;
    $user_info = $post;

    $name = $user_info['comment_author'];

    $comment_text = $user_info['comment_text'];

    $comment_email = $user_info['comment_email'];

    $command = "INSERT INTO blog_comments (blogId,author_name,comment_date,comment_text,comment_email) VALUES (" . $blogId . ",'" . $db->real_escape_string($name) . "',now(),'" . $db->real_escape_string($comment_text) . "','" . $db->real_escape_string($comment_email) . "');";

    $db->query($command);

    $db->close();

    display_all_entries(); // Returns user to the list of all entries.
}


// These if / elseif statements control what the user sees: all the entries or just one entry, the add comment
// form. It also will display the add comment form if appropriate.
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



