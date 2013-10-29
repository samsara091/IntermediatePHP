<?php
    require("connection.php");
	session_start();

	if(!isset($_SESSION['logged_in']))
	{
		header("Location: index.php");
	}
?>
<!DOCTYPE HTML>
<html> 
<head>
	<link rel="stylesheet" type="text/css" href="index.css">
</head>
<body>
Welcome <?= $_SESSION['user']['first_name'] ." " . $_SESSION['user']['last_name'] ?>!

<form action="process.php" method="post">
		<input id ="post_window" type="text" name="message" placeholder="Post a message" />
		<input type="hidden" name="action" value="message" />
		<input type="submit" value="Post a message" />
</form>

<?php 
        $query = "SELECT message, messages.created_at, first_name, last_name, messages.id FROM messages" 
        			. " INNER JOIN users on messages.user_id = users.id "
        			. " WHERE user_id = '" . mysql_real_escape_string($_SESSION['userId']) 
        			. "' order by created_at DESC ";
		$messages = fetch_all($query);
		if(count($messages)>0) {
			foreach($messages as $message)
			{
		
	?>
				<div id="message">

					<?php
						echo $message['first_name'] . " " . $message['last_name'] . ":" . $message['created_at'] . "<br />";
						echo $message['message']; 
						$query = "SELECT users.first_name, users.last_name, comments.comment, comments.created_at FROM comments "
									. "INNER JOIN messages on comments.message_id = messages.id " 
									. "INNER JOIN users on comments.user_id = users.id "
									. "WHERE message_id = '" . mysql_real_escape_string($message['id']) . "' order by comments.created_at DESC ";
						$comments = fetch_all($query);
						if(count($comments)>0) {
							foreach($comments as $comment)
							{
								echo "<br />" . $comment['first_name'] . " " . $comment['last_name'] . ": " . $comment['created_at'] . "<br/>";
								echo $comment['comment']; 
							}
						}
					?>
					<form action="process.php" method="post">
							<input id ="post_comment" type="text" name="comment" placeholder="Post a comment" />
							<input type="hidden" name="message_id" value="<?php echo $message['id'];?>" />
							<input type="hidden" name="action" value="comment" />
							<input type="submit" value="Post a comment" />
					</form>
				</div>
	<?php 
			}
		}
?>

<a href="process.php">Log Off</a>

</body>
</html>