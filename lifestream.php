<?php
$twitter_user = '';
$feeds = array(
	'http://blog.treypiepmeier.com/feed/',
	'http://solutions.treypiepmeier.com/feed/',
	'http://github.com/trey.atom'
);

function getClass($url)
{
	// Create a CSS class name from the URL of the feed.
	$class = parse_url($url, PHP_URL_HOST); // TODO use regex to get the hostname instead of this flakey PHP function.
	$class = preg_replace("/www\./", "", $class); // Remove `www.`.
	$class = preg_replace("/\.(com|org|net)/", "", $class); // Remove top level domains. Add more as you see fit.
	$class = preg_replace("/\./", "_", $class); // Replace `.`s with `_`s.
	return $class;
}

require_once('simplepie.inc');
foreach ($feeds as $feed) {
	$merge[] = new SimplePie($feed);
}

$merged = SimplePie::merge_items($merge, 0, 20); // Get the 20 most recent items.
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
	<title>Someone's Lifestream</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
</head>
<body>

	<h1>My Lifesteam</h1>
<?php
	$thedate = '';
	foreach ($merged as $item):
		if ($thedate != $item->get_date('F j, Y')) {
			$thedate =  $item->get_date('F j, Y');
			echo '<h2>' . $thedate . '</h2>';
		}
?>
	<div class="item <?php echo getClass($item->feed->get_permalink()); ?>">
		<?php if (stripos($item->feed->get_permalink(), 'twitter.com') ): // This is a Tweet. ?>

		<div class="content">
			<?php
			$tweet = $item->get_description();
			// Tweet parsing mostly from Phwitter: http://jasontan.org/code/phwitter/
			$tweet = preg_replace("/^" . $twitter_user . ":/", "", $tweet); // Strip username from begenning of Tweet.
			$tweet = preg_replace("/(http|https|ftp):\/\/[^\s]*/i","<a href=\"$0\">$0</a>", $tweet); // Add links to URLs
			$tweet = preg_replace("/@([a-zA-Z0-9_]*)/","<a href=\"http://twitter.com/$1\">$0</a>", $tweet); // Make @username a link to a username's profile.
			echo $tweet;
			?>
		</div><!-- .content -->

		<?php else: // Not Twitter ?>

		<h2><a href="<?php echo $item->get_permalink(); ?>"><?php echo $item->get_title(); ?></a></h2>
		<div class="content">
			<?php echo $item->get_description(); ?>
		</div><!-- .content -->

		<?php endif; // end of Twitter check ?>
		<div class="date"><small>Posted at <?php echo $item->get_date('g:i a'); ?></small></div>
	</div><!-- .item -->
	<?php endforeach; ?>
</body>
</html>
