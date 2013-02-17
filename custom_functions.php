<?php

// Using hooks is absolutely the smartest, most bulletproof way to implement things like plugins,
// custom design elements, and ads. You can add your hook calls below, and they should take the 
// following form:
// add_action('thesis_hook_name', 'function_name');
// The function you name above will run at the location of the specified hook. The example
// hook below demonstrates how you can insert Thesis' default recent posts widget above
// the content in Sidebar 1:
// add_action('thesis_hook_before_sidebar_1', 'thesis_widget_recent_posts');

// Delete this line, including the dashes to the left, and add your hooks in its place.

/**
 * function custom_bookmark_links() - outputs an HTML list of bookmarking links
 * NOTE: This only works when called from inside the WordPress loop!
 * SECOND NOTE: This is really just a sample function to show you how to use custom functions!
 *
 * @since 1.0
 * @global object $post
*/
function custom_bookmark_links() {
  global $post;
?>
<ul class="bookmark_links">
	<li><a rel="nofollow" href="http://delicious.com/save?url=<?php urlencode(the_permalink()); ?>&amp;title=<?php urlencode(the_title()); ?>" onclick="window.open('http://delicious.com/save?v=5&amp;noui&amp;jump=close&amp;url=<?php urlencode(the_permalink()); ?>&amp;title=<?php urlencode(the_title()); ?>', 'delicious', 'toolbar=no,width=550,height=550'); return false;" title="Bookmark this post on del.icio.us">Bookmark this article on Delicious</a></li>
</ul>
<?php
}

//move nav bar below header
function full_width_nav() { ?>
	<div id="nav_area" class="full_width">
		<div class="page">
			<?php thesis_nav_menu(); ?>
		</div>
	</div>
<?php }
remove_action('thesis_hook_before_header', 'thesis_nav_menu');
//add_action('thesis_hook_before_content_area', 'full_width_nav');

function custom_image_header() {
?>
    <p id="logo"><a href="<?php bloginfo('url'); ?>"><img src="<?php echo bloginfo('url') . '/wp-content/themes/thesis_17/custom/images/logo.png'; ?>" alt="<?php bloginfo('name'); ?>" title="<?php bloginfo('name'); ?>"/></a></p>
<?php
}
add_action('thesis_hook_header', 'custom_image_header');

function header_nav() {
	echo '<div id="nav_area">';
	thesis_nav_menu();
	echo '</div>';
}
add_action('thesis_hook_header', 'header_nav');

function custom_footer() {
?>
    <p>Copyright &copy 1989-<?php echo date("Y"); ?> <a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a>. All rights reserved.</p><br/>
<?php
}
add_action('thesis_hook_footer', 'custom_footer');

// Changes the comment link to exclude brackets
function my_skin_comments_link($link) {
	$link = str_replace("comments", "", $link);
	return $link; 
}
remove_filter('thesis_comments_link', 'default_skin_comments_link');
add_filter('thesis_comments_link', 'my_skin_comments_link');

// Moves the comment number to the head of the post
remove_action('thesis_hook_after_post', 'thesis_comments_link');
add_action('thesis_hook_before_headline', 'thesis_comments_link');

//DateBox
function custom_byline() {
if (!(is_page(array('about', 'archives', 'search')) || is_404())){
	echo '<div class="datebox"><div class="month">';
	the_time('M');
	echo '</div><div class="day">'; 
	the_time('j');
	echo '</div></div>';
}
}
add_action('thesis_hook_before_headline', 'custom_byline');

function custom_body_class($classes) {
	if(is_home() || is_front_page()){
		$classes[] = 'home';
	} elseif(is_404()) {
		$classes[] = 'fourohfour';
	}
	return $classes;
}
add_filter('thesis_body_classes', 'custom_body_class');

function custom_search_bar(){ ?>
		<div id="search-bar">
			<script type="text/javascript" src="http://www.google.com/jsapi"></script>
			<script type="text/javascript">
			  google.load('search', '1');
			  google.setOnLoadCallback(function() {
				google.search.CustomSearchControl.attachAutoCompletion(
				  '004720287845589182491:k7xvn4rlb_c',
				  document.getElementById('q'),
				  'cse-search-box');
			  });
			</script>
			<form action="http://www.david-merrick.com/search" id="cse-search-box">
			  <div>
				<input type="hidden" name="cx" value="004720287845589182491:k7xvn4rlb_c" />
				<input type="hidden" name="cof" value="FORID:10" />
				<input type="hidden" name="ie" value="UTF-8" />
				<span class="left"></span><input type="text" name="q" id="q" autocomplete="off" size="31" /><span class="right"></span>
				<input type="submit" name="sa" value="Search" style="display: none;"/>
			  </div>
			</form>
		</div>
<?php }
add_action('thesis_hook_header', 'custom_search_bar');

function check_cdn_exclude($url) {
	$my_cdn_excludes = array('wp-admin');
	$ref = $_SERVER["HTTP_REFERER"];
	foreach ($my_cdn_excludes as $exclude){
		if (preg_match("/{$exclude}/", $url)>0) return true;
		if (preg_match("/{$exclude}/", $ref)>0) return true;
	}
	return false;
}

function filter_cdn_elements($url) {
	if(!preg_match("/AppEngine\-Google/", $_SERVER['HTTP_USER_AGENT'])){ //To prevent recursion from CDN
		$my_cdn_old_urls = "www.david-merrick.com";
		$my_cdn_new_urls = "cdn.david-merrick.com/" . $my_cdn_old_urls;
		if (check_cdn_exclude($url) ) return $url;
			$url = preg_replace("/{$my_cdn_old_urls}/", $my_cdn_new_urls,$url, 1);
	}
	return $url;
}

function filter_cdn_inpost($text) {
        $text = str_replace('www.david-merrick.com/wp-content/uploads/', 'cdn.david-merrick.com/www.david-merrick.com/wp-content/uploads/', $text);
        return $text;
}

add_filter('style_loader_src','filter_cdn_elements');
add_filter('script_loader_src','filter_cdn_elements');
add_filter('theme_root_uri','filter_cdn_elements');
add_filter('the_content', 'filter_cdn_inpost');
