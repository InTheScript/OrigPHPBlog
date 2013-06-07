<?php

// This is the composer autoloader. Used by
// the markdown parser and RSS feed builder.
require 'vendor/autoload.php';

// Explicitly including the dispatch framework,
// and our functions.php file
require 'app/includes/dispatch.php';
require 'app/includes/functions.php';

// Load the configuration file
config('source', 'app/config.ini');

// The front page of the blog.
// This will match the root url
get('/index', function () {

	$page = from($_GET, 'page');
	//sets the $page var = to the end portion of the URL after 'http://localhost/OrigPHPBlog/?page=' i.e. '2' for page 2
	
	//prints out the variables to screen for debugging. At this point it's null as it hasn't been set
	
	$page = $page ? (int)$page : 1;
	//The above is called a conditional operator
	//It's the shorthand version of an if-else statement
	//It means that if $page has an integer value use it, alternativley if is zero (or null) set it to 1 (the first page).
	//Type casting the page variable to an integer with the (int syntax)
	
	//A long hand alternative is below
	//if ($page == null){
		//$page = 1;
	//}else{
		//$page = 2;
	//}
	
	//var_dump("Test " . $page);
	dump("HELLO" . " my name is");
	//prints out the variable information to
	
	$posts = get_posts($page);
	
	if(empty($posts) || $page < 1){
		// a non-existing page
		not_found();
	}
	
    render('main',array(
    	'page' => $page,
		'posts' => $posts,
		'has_pagination' => has_pagination($page)
	));
});

// The post page
get('/:year/:month/:name',function($year, $month, $name){

	$post = find_post($year, $month, $name);

	if(!$post){
		not_found();
	}

	render('post',array(
		'title' => $post->title .' ⋅ ' . config('blog.title'),
		'p' => $post
	));
});

// The JSON API
get('/api/json',function(){

	header('Content-type: application/json');

	// Print the 10 latest posts as JSON
	echo generate_json(get_posts(1, 10));
});

// Show the RSS feed
get('/rss',function(){

	header('Content-Type: application/rss+xml');

	// Show an RSS feed with the 30 latest posts
	echo generate_rss(get_posts(1, 30));
});


// If we get here, it means that
// nothing has been matched above

get('.*',function(){
	not_found();
});

// Serve the blog
dispatch();
