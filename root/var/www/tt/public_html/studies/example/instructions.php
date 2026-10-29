<?php include('study.php'); ?><!DOCTYPE html>
<html>
	<head>
		<title>Example Study</title>
		<?php include($_SERVER['tt_head']); ?>
		<style>
			.past p { display: none; }
			.future { display: none; }
		</style>
	</head>
	<body>
		<main>
			<h1>Example Study</h1>
			<blockquote>This is an example of the kinds of research studies that can be run using Tandem Tales. This study requires subjects to complete a short tutorial, and then they will play The Crown as both the player and the game master. Subjects are automatically distributed evenly between two groups: a control group that plays as the player first and an experimental group that plays as the game master first. Subjects rate their enjoyment of individual sessions and also rate which sessions they liked better, allowing for both between- and within-subjects analysis.</blockquote>
			<p>You will play several games with a partner where you tell short fictional stories in your web browser. When it is your turn, you will see a list of choices for what you can do next. A story has two sides: the player and the game master.</p>
			<div class="<?php echo($steps[0]['status']); ?>">
				<h2>Step 1: Play the Tutorial as the Player</h2>
				<p>First, please play a short tutorial story as the player. You will control one character, a customer who is thirsty. Your goal is to enter the store, buy a cup of coffee or a cup of herbal tea, and leave.</p>
				<p><a href="<?php echo($steps[0]['url']); ?>">Click here to play the tutorial as the player.</a></p>
			</div>
			<div class="<?php echo($steps[1]['status']); ?>">
				<h2>Step 2: Play the Tutorial as the Game Master</h2>
				<p>Now, please play the same tutorial story as the game master. The game master controls all of the other characters in the story.</p>
				<p><a href="<?php echo($steps[1]['url']); ?>">Click here to play the tutorial as the game master.</a></p>
			</div>
			<div class="<?php echo($steps[2]['status']); ?>">
				<h2>Step 3: Play The Crown</h2>
				<p>Now, please play a new story called The Crown. After you play, you will be asked one question about your experience.</p>
				<p><a href="<?php echo($steps[2]['url']); ?>">Click here to play The Crown.</a></p>
			</div>
			<div class="<?php echo($steps[3]['status']); ?>">
				<h2>Step 4: Play The Crown Again</h2>
				<p>Finally, please play The Crown again. After you play, you will be asked two questions about your experience.</p>
				<p><a href="<?php echo($steps[3]['url']); ?>">Click here to play The Crown again.</a></p>
			</div>
			<div class="<?php echo($steps[4]['status']); ?>">
				<h2>You are finished!</h2>
				<p>Thank you for participating in this study. The choices you made and questions you answered have been recorded, but we do not record your name, email address, or other identifying information. Please see out <a href="/consent.php">consent page</a> for all the details.</p>
				<?php echo(summarize_sessions($user->sessions)); ?>
				<p>Total time played: <?php echo(duration($user->getDuration())); ?></p>
				<blockquote>
					<p>Check <tt><?php echo(STUDY_LOGS . $study->id); ?></tt> to see this study's data.</p>
					<p>You can <a href="/studies/example">participate again</a> as a new user.</p>
				</blockquote>
			</div>
<?php if($step < 4) { ?>
			<small>
				<p>There are no right or wrong choices. You should make the choices that seem most fun and tell the best story for you and your partner.</p>
				<p>You should play the story until it ends. If you leave before it ends you may not get credit for this story. If you pass control to your partner without taking any actions 3 times in a row, the story will end early and you may not get credit for this story. If your partner leaves the story before it ends, you will still get credit for this story.</p>
				<p>If your web browser crashes or you need to leave the story for some other reason, you can return to this page to resume this study.</p>
				<p>There is no way to chat with your partner or see who your partner is. You can only make choices from the list of actions shown on your screen.</p>
			</small>
<?php } ?>
		</main>
		<?php include($_SERVER['tt_footer']); ?>
	</body>
</html>