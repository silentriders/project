<?php
/*
* ----------------------------------------------------
* @author: Doothemes
* @author URI: https://doothemes.com/
* @copyright: (c) 2017 Doothemes. All rights reserved
* ----------------------------------------------------
*
* @since 2.1.4
*
*/

# Theme Setup
if( ! function_exists( 'dooplay_theme_setup' ) ) {
    function dooplay_theme_setup() {

        // Theme supports
        add_theme_support( 'title-tag' );
        add_theme_support( 'post-thumbnails' );
        add_theme_support( 'automatic-feed-links' );

        // Image Sizes
        add_image_size( 'dt_poster_a',  185, 278, true );
    	add_image_size( 'dt_poster_b',  90,  135, true );
    	add_image_size( 'dt_episode_a', 300, 170, true );

        // Menus
        $menus = array(
            // Main
            'header'  => __d('Menu main header'),
            'footer'  => __d('Menu footer'),

            // Footer
            'footer1' => __d('Footer - column 1'),
            'footer2' => __d('Footer - column 2'),
            'footer3' => __d('Footer - column 3'),
        );
        // Register all Menus
        register_nav_menus( $menus );
    }
    add_action('after_setup_theme', 'dooplay_theme_setup');
}

# Mobile or not mobile
if( ! function_exists( 'doo_mobile' ) ) {
    function doo_mobile() {
    	$mobile = ( wp_is_mobile() == true ) ? '1' : 'false';
    	return $mobile;
    }
}

# Echo translated text
if( ! function_exists( '_d' ) ) {
    function _d( $text ){
    	echo translate($text , 'mtms');
    }
}

# Return Translated Text
if( ! function_exists( '__d' ) ) {
    function __d( $text ) {
        return translate($text, 'mtms');
    }
}

# Search letter
if( ! function_exists( 'doo_search_title' ) ) {
    function doo_search_title($search) {
    	preg_match('/title-([^%]+)/', $search, $m);
    	if ( isset( $m[1] ) ) {
    		global $wpdb;
    		if($m[1] == '09') return $wpdb->query( $wpdb->prepare("AND $wpdb->posts.post_title REGEXP '^[0-9]' AND ($wpdb->posts.post_password = '') ") );
    		return $wpdb->query( $wpdb->prepare("AND $wpdb->posts.post_title LIKE '$m[1]%' AND ($wpdb->posts.post_password = '') ") );
    	} else {
    		return $search;
    	}
    }
    add_filter('posts_search', 'doo_search_title');
}

# First Letter
if( ! function_exists( 'doo_first_letter' ) ) {
    function doo_first_letter( $where, $qry ) {

    	global $wpdb;
    	$sub = $qry->get('doo_first_letter');

    	if (!empty($sub)) {
    		$where .= $wpdb->prepare(
    			" AND SUBSTRING( {$wpdb->posts}.post_title, 1, 1 ) = %s ",
    			$sub
    		);
    	}

    	return $where;
    }
    add_filter( 'posts_where' , 'doo_first_letter', 1 , 2 );
}

# Register master categories
if( ! function_exists( 'genres_taxonomy' ) ) {
    function genres_taxonomy() {
    	register_taxonomy('genres', array('tvshows,movies',),
    		array(
    			'show_admin_column' => false,
    			'hierarchical'		=> true,
    			'label'				=> __d('Genres'),
    			'rewrite'			=> array('slug' => get_option('dt_genre_slug','genre') ),
            )
    	);
    }
    add_action('init', 'genres_taxonomy', 0);
}

if( ! function_exists( 'prefijo_mastercat' ) ) {
    function prefijo_mastercat() {
    	flush_rewrite_rules();
    }
    add_action('after_switch_theme', 'prefijo_mastercat');
}

if( ! function_exists( 'quality_taxonomy' ) ) {
    function quality_taxonomy() {
    	register_taxonomy('dtquality', array('episodes,movies'),
    		array(
    			'show_admin_column' => false,
    			'hierarchical'		=> true,
    			'label'				=> __d('Quality'),
    			'rewrite'			=> array ('slug' => get_option('dt_quality_slug','quality')),)
    		);
    }
    add_action('init', 'quality_taxonomy', 0);
}

if( ! function_exists( 'dp_c' ) ) {
    function dp_c() {
    	flush_rewrite_rules();
    }
    add_action('after_switch_theme', 'dp_c');
}

# Add admin css wp-login.php
if( ! function_exists( 'load_admin_style' ) ) {
    function load_admin_style() {
    	wp_register_style('admin_css', DOO_URI . '/assets/css/admin.style.css', false, DOO_VERSION );
    	wp_enqueue_style('admin_css', DOO_URI . '/assets/css/admin.style.css', false, DOO_VERSION );
    }
    add_action('admin_enqueue_scripts', 'load_admin_style');
}

# Custom URL logo wp-login.php
if( ! function_exists( 'doo_home_url_admin' ) ) {
    function doo_home_url_admin($url) {
    	return home_url();
    }
    add_filter('login_headerurl', 'doo_home_url_admin');
}

# Custom Logo wp-login.php
if( ! function_exists( 'doo_logo_admin' ) ) {
    function doo_logo_admin() {
    	$logo = ( get_option('dt_logo_admin') ) ? get_option('dt_logo_admin') : DOO_URI ."/assets/img/logo_dt.png";
    	echo '<style type="text/css">h1 a {background-image: url('.$logo.')!important;background-size: 244px 52px !important;width: 301px !important;height: 52px !important;margin-bottom: 0!important;}body.login {background: #fff;}</style>';
     }
    add_action('login_head', 'doo_logo_admin');
}

# Count views
if( ! function_exists( 'set_dt_views' ) ) {
    function set_dt_views($postID) {
    	$count_key = 'dt_views_count';
    	$count = get_post_meta($postID, $count_key, true);
    	if($count==''){
    		$count = 0;
    		delete_post_meta($postID, $count_key);
    		add_post_meta($postID, $count_key, '1');
    	}else{
    		$count++;
    		update_post_meta($postID, $count_key, $count);
    	}
    }
}

# Total count content
if( ! function_exists( 'doo_total_count' ) ) {
    function doo_total_count( $type = null, $status = 'publish' ) {
        if( $type != null ) {
            $total = wp_count_posts( $type )->$status;
            return $total;
        }
    }
}

# Get genres
if( ! function_exists( 'li_generos' ) ) {
    function li_generos() {
    	$taxonomy     = 'genres';
    	$orderby      = 'DESC';
    	$show_count	  = 1;
    	$hide_empty   = false;
    	$pad_counts   = 0;
    	$hierarchical = 1;
    	$exclude      = '55';
    	$title        = '';
    	$post_type    = '';
    	$args = array(
    		'post_type'    => $post_type,
    		'taxonomy'     => $taxonomy,
    		'orderby'      => $orderby,
    		'show_count'   => $show_count,
    		'hide_empty'   => $hide_empty,
    		'pad_counts'   => $pad_counts,
    		'hierarchical' => $hierarchical,
    		'exclude'      => $exclude,
    		'title_li'     => $title,
    		'echo'         => 0
    	);
        $links = wp_list_categories($args);
        $links = str_replace('</a> (', '</a> <i>', $links);
        $links = str_replace(')', '</i>', $links);
        echo $links;
    }
}

# Get genres
if( ! function_exists( 'li_generos_h' ) ) {
    function li_generos_h() {
    	$taxonomy     = 'genres';
    	$orderby      = 'DESC';
    	$show_count	  = 0;
    	$hide_empty   = false;
    	$pad_counts   = 0;
    	$hierarchical = 1;
    	$exclude      = '55';
    	$title        = '';
    	$post_type    = '';
    	$args = array(
    		'post_type'	   => $post_type,
    		'taxonomy'     => $taxonomy,
    		'orderby'      => $orderby,
    		'show_count'   => $show_count,
    		'hide_empty'   => $hide_empty,
    		'pad_counts'   => $pad_counts,
    		'hierarchical' => $hierarchical,
    		'exclude'      => $exclude,
    		'title_li'     => $title,
    		'echo'         => 0
    	);
        $links = wp_list_categories($args);
        $links = str_replace('</a> (', '</a> <i>', $links);
        $links = str_replace(')', '</i>', $links);
        echo $links;
    }
}

# Paginator
if( ! function_exists( 'pagination' ) ) {
    function pagination($pages = '', $range = 2) {
        $showitems = ($range * 2)+1;
        global $paged;
        if(empty($paged)) $paged = 1;
        if($pages == '') {
            global $wp_query;
            $pages = $wp_query->max_num_pages;
            if(!$pages) {
                $pages = 1;
            }
        }
        if(1 != $pages)  {
            echo "<div class=\"pagination\"><span>". __d('Page') ." ".$paged." " . __d('of') . " ".$pages."</span>";
            if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "";
            if($paged > 1 && $showitems < $pages) echo "<a class='arrow_pag' href='".get_pagenum_link($paged - 1)."'><i id='prevpagination' class='icon-caret-left'></i></a>";

            for ($i=1; $i <= $pages; $i++) {
                if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems )) {
                    echo ($paged == $i)? "<span class=\"current\">".$i."</span>":"<a href='".get_pagenum_link($i)."' class=\"inactive\">".$i."</a>";
                }
            }

            if ($paged < $pages && $showitems < $pages) echo "<a class='arrow_pag' href=\"".get_pagenum_link($paged + 1)."\"><i id='nextpagination' class='icon-caret-right'></i></a>";
            if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "";
            echo "</div>\n";
		    echo "<div class='resppages'>";
			previous_posts_link('<span class="icon-chevron-left"></span>');
			next_posts_link('<span class="icon-chevron-right"></span>');
		    echo "</div>";
        }
    }
}

# Create DT pages
if(is_admin() and current_user_can('administrator')){

	// Define Version database
	$dooplay_database = get_option('dooplay_database');
	if( empty($dooplay_database) ){
		update_option('dooplay_database', DOO_VERSION_DB );
	}

	// Page trending
	$page_trending = get_option('dt_trending_page');
	if(empty($page_trending)){
		$post_id = wp_insert_post(array(
		  'post_content'   => '',
		  'post_name'      => __d('Trending'),
		  'post_title'     => __d('Trending'),
		  'post_status'    => 'publish',
		  'post_type'      => 'page',
		  'ping_status'    => 'closed',
		  'post_date'      => date('Y-m-d H:i:s'),
		  'post_date_gmt'  => date('Y-m-d H:i:s'),
		  'comment_status' => 'closed',
		  'page_template'  => 'pages/trending.php'
		));
		$get_01 = get_option('siteurl').'/' . sanitize_title(__d('Trending')).'/';
		update_option('dt_trending_page', $get_01);
	}
	// Page Rating
	$page_rating = get_option('dt_rating_page');
	if(empty($page_rating)){
		$post_id = wp_insert_post(array(
		  'post_content'   => '',
		  'post_name'      => __d('Ratings'),
		  'post_title'     => __d('Ratings'),
		  'post_status'    => 'publish',
		  'post_type'      => 'page',
		  'ping_status'    => 'closed',
		  'post_date'      => date('Y-m-d H:i:s'),
		  'post_date_gmt'  => date('Y-m-d H:i:s'),
		  'comment_status' => 'closed',
		  'page_template'  => 'pages/rating.php'
		));
		$get_02 = get_option('siteurl').'/' . sanitize_title(__d('Ratings')).'/';
		update_option('dt_rating_page', $get_02);
	}
	// Page Account
	$page_account = get_option('dt_account_page');
	if(empty($page_account)){
		$post_id = wp_insert_post(array(
		  'post_content'   => __d('Edit page content.'),
		  'post_name'      => __d('Account'),
		  'post_title'     => __d('Account'),
		  'post_status'    => 'publish',
		  'post_type'      => 'page',
		  'ping_status'    => 'closed',
		  'post_date'      => date('Y-m-d H:i:s'),
		  'post_date_gmt'  => date('Y-m-d H:i:s'),
		  'comment_status' => 'closed',
		  'page_template'  => 'pages/account.php'
		));
		$get_03 = get_option('siteurl').'/' . sanitize_title( __d('account') ).'/';
		update_option('dt_account_page', $get_03);
	}
	// Page contact
	$page_contact = get_option('dt_contact_page');
	if(empty($page_contact)){
		$post_id = wp_insert_post(array(
		  'post_content'   => '',
		  'post_name'      => __d('Contact'),
		  'post_title'     => __d('Contact'),
		  'post_status'    => 'publish',
		  'post_type'      => 'page',
		  'ping_status'    => 'closed',
		  'post_date'      => date('Y-m-d H:i:s'),
		  'post_date_gmt'  => date('Y-m-d H:i:s'),
		  'comment_status' => 'closed',
		  'page_template'  => 'pages/contact.php'
		));
		$get_05 = get_option('siteurl').'/' . sanitize_title(__d('Contact')).'/';
		update_option('dt_contact_page', $get_05);
	}

	// Posts page
	$page_posts = get_option('dt_posts_page');
	if(empty($page_posts)){
		$post_id = wp_insert_post(array(
		  'post_content'   => '',
		  'post_name'      => __d('Blog'),
		  'post_title'     => __d('Blog'),
		  'post_status'    => 'publish',
		  'post_type'      => 'page',
		  'ping_status'    => 'closed',
		  'post_date'      => date('Y-m-d H:i:s'),
		  'post_date_gmt'  => date('Y-m-d H:i:s'),
		  'comment_status' => 'closed',
		  'page_template'  => 'pages/blog.php'
		));
		$get_posts_page = get_option('siteurl').'/' . sanitize_title(__d('Blog')).'/';
		update_option('dt_posts_page', $get_posts_page);
	}

	// TOP IMDb page
	$page_topimdb = get_option('dt_topimdb_page');
	if(empty($page_topimdb)){
		$post_id = wp_insert_post(array(
		  'post_content'   => '',
		  'post_name'      => __d('TOP IMDb'),
		  'post_title'     => __d('TOP IMDb'),
		  'post_status'    => 'publish',
		  'post_type'      => 'page',
		  'ping_status'    => 'closed',
		  'post_date'      => date('Y-m-d H:i:s'),
		  'post_date_gmt'  => date('Y-m-d H:i:s'),
		  'comment_status' => 'closed',
		  'page_template'  => 'pages/topimdb.php'
		));
		$get_posts_page = get_option('siteurl').'/' . sanitize_title(__d('TOP IMDb')).'/';
		update_option('dt_topimdb_page', $get_posts_page);
	}

	// JWPlayer page
	$dt_jwplayer_page = get_option('dt_jwplayer_page');
	if(empty($dt_jwplayer_page)){
		$post_id = wp_insert_post(array(
		  'post_content'   => '',
		  'post_name'      => __d('player'),
		  'post_title'     => __d('JW Player'),
		  'post_status'    => 'publish',
		  'post_type'      => 'page',
		  'ping_status'    => 'closed',
		  'post_date'      => date('Y-m-d H:i:s'),
		  'post_date_gmt'  => date('Y-m-d H:i:s'),
		  'comment_status' => 'closed',
		  'page_template'  => 'pages/jwplayer.php'
		));
		$get_posts_page = get_option('siteurl').'/' . sanitize_title('player').'/';
		update_option('dt_jwplayer_page', $get_posts_page);
	}

	// Google Drive JWPlayer page
	$dt_jwplayer_page_gdrive = get_option('dt_jwplayer_page_gdrive');
	if(empty($dt_jwplayer_page_gdrive)){
		$post_id = wp_insert_post(array(
		  'post_content'   => '',
		  'post_name'      => __d('gdrive-player'),
		  'post_title'     => __d('Google Drive JW Player'),
		  'post_status'    => 'publish',
		  'post_type'      => 'page',
		  'ping_status'    => 'closed',
		  'post_date'      => date('Y-m-d H:i:s'),
		  'post_date_gmt'  => date('Y-m-d H:i:s'),
		  'comment_status' => 'closed',
		  'page_template'  => 'gdrive/jwplayer.php'
		));
		$get_posts_page = get_option('siteurl').'/' . sanitize_title('gdrive-player').'/';
		update_option('dt_jwplayer_page_gdrive', $get_posts_page);
	}

	// JW Player Access KEY
	$jwplayer_key = get_option('dt_jw_key');
	if ( empty($jwplayer_key) ) {
		update_option('dt_jw_key', 'IMtAJf5X9E17C1gol8B45QJL5vWOCxYUDyznpA==');
	}
}

# Text extract
if( ! function_exists( 'dt_content_alt' ) ) {
    function dt_content_alt($charlength) {
    	$excerpt = get_the_excerpt();
    	$charlength++;
    	if ( mb_strlen( $excerpt ) > $charlength ) {
    		$subex    = mb_substr( $excerpt, 0, $charlength - 5 );
    		$exwords  = explode( ' ', $subex );
    		$excut    = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
    		if ( $excut < 0 ) {
    			echo mb_substr( $subex, 0, $excut );
    		} else {
    			echo $subex;
    		}
    		echo '...';
    	} else {
    		echo $excerpt;
    	}
    }
}

# Generate release years
if( ! function_exists( 'dt_show_year' ) ) {
    function dt_show_year() {
    	$taxonomy  = '';
    	$args      = array('order' => 'DESC' ,'number' => 50);
    	$camel     = 'dtyear';
    	$tax_terms = get_terms($camel,$args);
    	foreach ($tax_terms as $tax_term) {
    		echo '<li>' . '<a href="' . esc_attr(get_term_link($tax_term, $taxonomy)) . '">' . $tax_term->name.'</a></li>';
    	}
    }
}

# Get data
if( ! function_exists( 'data_of' ) ) {
    function data_of($name, $id, $acortado = false, $max = 150) {
        $val = get_post_meta($id, $name, $single = true);
        if ($val != NULL) {
            if ($acortado) {
                return substr($val, 0, $max) . '...';
            } else {
                return $val;
            }
        } else {
            if ($name == 'overview') {
                return "";
            } elseif ($name == 'temporada') {
                return "0";
            } else {
                return "--";
            }
        }
    }
}

# Get seasons
if( ! function_exists( 'season_of' ) ) {
    function season_of($meta) {
        global $wpdb;
        $results = $wpdb->get_results("select post_id, meta_key from $wpdb->postmeta where meta_value = '" . $meta . "'", ARRAY_A);
        $a_t     = array();
        $a_c     = array();
        foreach ($results as $i => $value) {
            if (get_post_type($results[$i]["post_id"]) == 'seasons' && get_post_status($results[$i]["post_id"]) == 'publish') {
                $a_t[] = array(
                    'id'     => $results[$i]["post_id"],
                    'season' => get_post_meta($results[$i]["post_id"], "temporada", $single = true)
                );
            }
            if (get_post_type($results[$i]["post_id"]) == 'episodes' && get_post_status($results[$i]["post_id"]) == 'publish') {
                $a_c[] = array(
                    'id'       => $results[$i]["post_id"],
                    'season'   => get_post_meta($results[$i]["post_id"], "temporada", $single = true),
                    'capitulo' => get_post_meta($results[$i]["post_id"], "episodio", $single = true)
                );
            }
        }
        if ((!empty($a_t)) && (!empty($a_c))) {
            foreach ($a_t as $key => $row) {
                $aux[$key] = $row['season'];
            }
            array_multisort($aux, SORT_ASC, $a_t);
            foreach ($a_c as $key => $row) {
                $aux1[$key] = $row['capitulo'];
            }
            array_multisort($aux1, SORT_ASC, $a_c);
            $counta   = 0;
            $finalcap = array();
            $maxt     = 0;
            foreach ($a_c as $key => $row) {
                $finalcap[] = array(
                    'id'       => $row['id'],
                    'season'   => $row['season'],
                    'capitulo' => $row['capitulo']
                );
                if ($a_c[$key]["season"] >= $maxt) {
                    $maxt = $a_c[$key]["season"];
                }
                $counta++;
            }
            $counti   = 0;
            $finalarr = array();
            foreach ($a_t as $key => $row) {
                $finalarr[] = array(
                    'id'     => $row['id'],
                    'season' => $row['season']
                );
                $counti++;
            }
            $data = array(
                'temporada' => array(
                    'l_temp' => array(
                        'id'     => $finalarr[$counti - 1]['id'],
                        'numero' => $finalarr[$counti - 1]['season']
                    ),
                    'n_temp' => $counti,
                    'all'    => $finalarr,
                    'd_temp' => $maxt
                ),
                'capitulo' => array(
                    'n_cap' => $counta,
                    'all'   => $finalcap
                )
            );
            return $data;
        }
    }
}

# Delete content
if( ! function_exists( 'wp_delete_post_link' ) ) {
    function wp_delete_post_link($link = 'Delete This', $before = '', $after = '') {
        global $post;
        if ( $post->post_type == 'page') {
            if ( !current_user_can('edit_page', $post->ID ) ) return;
    	} else {
    	    if ( !current_user_can('edit_post', $post->ID ) ) return;
        }

        $message    = sprintf( __d('Are you sure you want to delete %s ?'), get_the_title($post->ID));
        $delLink    = wp_nonce_url( home_url() . "/wp-admin/post.php?action=delete&post=" . $post->ID, 'delete-post_' . $post->ID);
        $htmllink   = "<a href='" . $delLink . "' onclick = \"if ( confirm('".$message."') ) { execute(); return true; } return false;\"/>".$link."</a>";
        echo $before . $htmllink . $after;
    }
}

# Get Domain
if( ! function_exists( 'saca_dominio' ) ) {
    function saca_dominio( $url = null ){
        $protocolos = array('http://', 'https://', 'ftp://', 'www.');
        $url = explode('/', str_replace($protocolos, '', $url));
        return $url[0];
    }
}

# API domain validate
if( ! function_exists( 'dt_domain' ) ) {
    function dt_domain( $url = null ){
        if( $url != null ) {
            $str = preg_replace('#^https?://#', '', $url );
            return $str;
        }
    }
}

# Get Images
if( ! function_exists( 'dt_image' ) ) {
    function dt_image($name, $id, $size, $type = false, $return = false, $gtsml = false) {
        $img   = get_post_meta($id, $name, $single = true);
        $val   = explode("\n", $img);
        $mgsl  = array();
        $count = 0;
        foreach ($val as $valor) {
            if (!empty($valor)) {
                if (substr($valor, 0, 1) == "/") {
                    $mgsl[] = 'https://image.tmdb.org/t/p/' . $size . '' . $valor . '';
                } else {
                    $mgsl[] = $valor;
                }
                $count++;
            } else {
                if ($name == "dt_poster" && $img == NULL) {
                    $mgsl[] = esc_url( DOO_URI ) . '/assets/img/no/poster.png';
                }

    			if ($name == "dt_backdrop" && $img == NULL) {
                    $mgsl[] = esc_url( DOO_URI ) . '/assets/img/no/backdrop-small.png';
                }
            }
        }
        if ($type) {
            $new = rand(0, $count);
            if ($mgsl[$new] != NULL) {
                if ($return) {
                    return $mgsl[$new];
                } else {
                    echo $mgsl[$new];
                }
            } else {
                if ($return) {
                    return $mgsl[0];
                } else {
                    echo $mgsl[0];
                }
            }
        } else {
            if ($return) {
                return $mgsl[0];
            } else {
                echo $mgsl[0];
            }
        }
    }
}

# Get Images search
if( ! function_exists( 'dt_image_search' ) ) {
    function dt_image_search($name, $id, $size, $type = false, $return = false, $gtsml = false) {
        $img   = get_post_meta($id, $name, $single = true);
        $val   = explode("\n", $img);
        $mgsl  = array();
        $count = 0;
        foreach ($val as $valor) {
            if (!empty($valor)) {
                if (substr($valor, 0, 1) == "/") {
                    $mgsl[] = 'https://image.tmdb.org/t/p/' . $size . '' . $valor . '';
                } else {
                    $mgsl[] = $valor;
                }
                $count++;
            } else {
                if ($name == "dt_poster" && $img == NULL) {
                    $mgsl[] = esc_url( DOO_URI ) . '/assets/img/no_image_search.png';
                }

            }
        }
        if ($type) {
            $new = rand(0, $count);
            if ($mgsl[$new] != NULL) {
                if ($return) {
                    return $mgsl[$new];
                } else {
                    echo $mgsl[$new];
                }
            } else {
                if ($return) {
                    return $mgsl[0];
                } else {
                    echo $mgsl[0];
                }
            }
        } else {
            if ($return) {
                return $mgsl[0];
            } else {
                echo $mgsl[0];
            }
        }
    }
}

# Get Cast
if( ! function_exists( 'dt_cast' ) ) {
    function dt_cast($id, $type, $limit = false) {
        $name = get_post_meta($id, "dt_cast", $single = true);
        if ($type == "img") {
            if ($limit) {
                $val    = explode("]", $name);
                $passer = $newvalor = array();
                foreach ($val as $valor) {
                    if (!empty($valor)) {
                        $passer[] = substr($valor, 1);
                    }
                }
                for ($h = 0; $h <= 4; $h++) {
                    $newval     = explode(";", $passer[$h]);
                    $fotoor     = $newval[0];
                    $actorpapel = explode(",", $newval[1]);

                    if (!empty($actorpapel[0])) {

                        if ($newval[0] == "null") {
                            $fotoor = DOO_URI . '/assets/img/no/cast.png';
                        } else {
                            $fotoor = 'https://image.tmdb.org/t/p/w92' . $newval[0];
                        }
                        echo '<tr class="person">';
    					echo '<td class="first_norole">';
                        echo '<div class="mask"><a href="'. home_url() .'/'. get_option('dt_cast_slug','cast') .'/' . sanitize_title($actorpapel[0]) . '/"><img alt="'. $actorpapel[0] .'" src="' . $fotoor . '" /></a></div>';
                        echo '<h3 class="name"><a href="'. home_url() .'/'. get_option('dt_cast_slug','cast') .'/' . sanitize_title($actorpapel[0]) . '/">' . $actorpapel[0] . '</a></h3>';
    					echo '</td>';
    					echo '<td class="last_norole">';
    					echo '<h4 class="role">' . $actorpapel[1] . '</h4>';
    					echo '</td>';
                        echo '</tr>';

                    }
                }
            } else {
                $val = str_replace(array(
                    '[null',
                    '[/',
                    ';',
                    ']',
                    ","
                ), array(
                    '<div class="castItem"><img src="' . DOO_URI . '/assets/img/no/cast.png',
                    '<div class="castItem"><img src="https://image.tmdb.org/t/p/w92/',
                    '" /><span>',
                    '</span></div>',
                    '</span><span class="typesp">'
                ), $name);
                echo $val;
            }
        } else {
            if (get_the_term_list($post->ID, 'dtcast', true)) {
                echo get_the_term_list($post->ID, 'dtcast', '', ', ', '');
            } else {
                echo "N/A";
            }
        }
    }
}

# Get Cast 2
if( ! function_exists( 'dt_cast_2' ) ) {
    function dt_cast_2($id, $type, $limit = false) {
        $name = get_post_meta($id, "dt_cast", $single = true);
        if ($type == "img") {
            if ($limit) {
                $val    = explode("]", $name);
                $passer = $newvalor = array();
                foreach ($val as $valor) {
                    if (!empty($valor)) {
                        $passer[] = substr($valor, 1);
                    }
                }
                for ($h=0; $h <= 10; $h++) {
                    $newval     = explode(";", isset( $passer[$h] ) ? $passer[$h] : null );
                    $fotoor     = $newval[0];
                    $actorpapel = explode(",", isset( $newval[1] ) ? $newval[1] : null );

                    if (!empty($actorpapel[0])) {

                        if ($newval[0] == "null") {
                            $fotoor = DOO_URI . '/assets/img/no/cast.png';
                        } else {
                            $fotoor = 'https://image.tmdb.org/t/p/w92' . $newval[0];
                        }
                        echo '<div class="person">';
    						echo '<div class="img"><a href="'. home_url() .'/'. get_option('dt_cast_slug','cast') .'/' . sanitize_title($actorpapel[0]) . '/"><img alt="'. $actorpapel[0] .' is'. $actorpapel[1] .'" src="' . $fotoor . '" /></a></div>';
    						echo '<div class="data">';
    							echo '<div class="name"><a href="'. home_url().'/'. get_option('dt_cast_slug','cast') .'/' . sanitize_title($actorpapel[0]) . '/">' . $actorpapel[0] . '</a></div>';
    							echo '<div class="caracter">' . $actorpapel[1] . '</div>';
    						echo '</div>';
                        echo '</div>';

                    }
                }
            } else {
                $val = str_replace(array(
                    '[null',
                    '[/',
                    ';',
                    ']',
                    ","
                ), array(
                    '<div class="castItem"><img src="' . DOO_URI . '/assets/img/no/cast.png',
                    '<div class="castItem"><img src="https://image.tmdb.org/t/p/w92/',
                    '" /><span>',
                    '</span></div>',
                    '</span><span class="typesp">'
                ), $name);
                echo $val;
            }
        } else {
            if (get_the_term_list($post->ID, 'dtcast', true)) {
                echo get_the_term_list($post->ID, 'dtcast', '', ', ', '');
            } else {
                echo "N/A";
            }
        }
    }
}

# Get director
if( ! function_exists( 'dt_director' ) ) {
    function dt_director($id, $type, $limit = false) {
        $name = get_post_meta($id, "dt_dir", $single = true);
        if ($type == "img") {
            if ($limit) {
                $val    = explode("]", $name);
                $passer = $newvalor = array();
                foreach ($val as $valor) {
                    if (!empty($valor)) {
                        $passer[] = substr($valor, 1);
                    }
                }
                for ($h = 0; $h <= 0; $h++) {
                    $newval = explode(";", $passer[$h]);
                    $fotoor = $newval[0];
                    if ($newval[0] == "null") {
                        $fotoor = DOO_URI . '/assets/img/no/cast.png';
                    } else {
                        $fotoor = 'https://image.tmdb.org/t/p/w92' . $newval[0];
                    }
    				echo '<div class="person">';
    				echo '<div class="img"><a href="'. home_url() .'/'. get_option('dt_director_slug','director') .'/' . sanitize_title($newval[1]) . '/"><img alt="'. $newval[1] .'" src="' . $fotoor . '" /></a></div>';
    				echo '<div class="data">';
    				echo '<div class="name"><a href="'. home_url() .'/'. get_option('dt_director_slug','director') .'/' . sanitize_title($newval[1]) . '/">' . $newval[1] . '</a></div>';
    				echo '<div class="caracter">'.__d('Director').'</div>';
    				echo '</div>';
    				echo '</div>';
                }
            }
        }
    }
}

# Get creator
if( ! function_exists( 'dt_creator' ) ) {
    function dt_creator($id, $type, $limit = false) {
        $name = get_post_meta($id, "dt_creator", $single = true);
        if ($type == "img") {
            if ($limit) {
                $val    = explode("]", $name);
                $passer = $newvalor = array();
                foreach ($val as $valor) {
                    if (!empty($valor)) {
                        $passer[] = substr($valor, 1);
                    }
                }
                for ($h = 0; $h <= 0; $h++) {
                    $newval = explode(";", $passer[$h]);
                    $fotoor = $newval[0];
                    if ($newval[0] == "null") {
                        $fotoor = DOO_URI . '/assets/img/no/cast.png';
                    } else {
                        $fotoor = 'https://image.tmdb.org/t/p/w92' . $newval[0];
                    }
    				echo '<div class="person">';
    				echo '<div class="img"><a href="'. home_url() .'/'. get_option('dt_creator_slug','creator') .'/' . sanitize_title($newval[1]) . '/"><img alt="'. $newval[1] .'" src="' . $fotoor . '" /></a></div>';
    				echo '<div class="data">';
    				echo '<div class="name"><a href="'. home_url() .'/'. get_option('dt_creator_slug','creator') .'/' . sanitize_title($newval[1]) . '/">' . $newval[1] . '</a></div>';
    				echo '<div class="caracter">'.__d('Creator').'</div>';
    				echo '</div>';
    				echo '</div>';
                }
            }
    	}
    }
}

# WordPress Dashboard
if( ! function_exists( 'doo_dashboard_count_types' ) ) {
    function doo_dashboard_count_types() {
        $args = array(
            'public'   => true,
            '_builtin' => false
        );
        $output     = 'object';
        $operator   = 'and';
        $post_types = get_post_types( $args, $output, $operator );
        foreach ( $post_types as $post_type ) {
            $num_posts = wp_count_posts( $post_type->name );
            $num       = number_format_i18n( $num_posts->publish );
            $text      = _n( $post_type->labels->singular_name, $post_type->labels->name, intval( $num_posts->publish ) );
            if ( current_user_can('edit_posts') ) {
                $output = '<a href="edit.php?post_type=' . $post_type->name . '">' . $num . ' ' . $text . '</a>';
                echo '<li class="post-count ' . $post_type->name . '-count">' . $output . '</li>';
            }
        }
    }
    add_action('dashboard_glance_items', 'doo_dashboard_count_types');
}

# Trailer / iframe
if( ! function_exists( 'mostrar_trailer_iframe' ) ) {
    function mostrar_trailer_iframe($id) {
    	if (!empty($id)) {
    	$val = str_replace(
    		array("[","]",),
    		array('<'. 'iframe' .' width="760" height="429" class="rptss" src="https://www.youtube.com/embed/','?rel=0&amp;controls=1&amp;showinfo=0&autoplay=0" frameborder="0" allowfullscreen></iframe>',),$id);
    		echo $val;
    	}
    }
}

# Trailer / custom player
if( ! function_exists( 'mostrar_youtube' ) ) {
    function mostrar_youtube($id) {
    	if (!empty($id)) {
    	$val = str_replace(
    		array("[","]",),
    		array('<div class="dt_player_video" data-type="youtube" data-video-id="','"></div>',),$id);
    		echo $val;
    	}
    }
}

# Get images
if( ! function_exists( 'dt_get_images' ) ) {
    function dt_get_images($size, $id) {
        $img = get_post_meta($id, "imagenes", $single = true);
        $val    = explode("\n", $img);
        $passer = array();
        $cmw  = 0;
        foreach ($val as $valor) {
            if (!empty($valor)) {
                echo '<div class="g-item">';
                if (substr($valor, 0, 1) == "/") {
    				echo '<a href="https://image.tmdb.org/t/p/original'.$valor.'" title="'.get_the_title().'">';
                    echo '<img alt="'.get_the_title().'" src="https://image.tmdb.org/t/p/'.$size.''.$valor.'" />';
    				echo '</a>';
                } else {
    				echo '<a href="'.$valor.'" title="'.get_the_title().'">';
                    echo '<img alt="'.get_the_title().'" src="' . $valor . '"/>';
    				echo '</a>';
                }
                echo '</div>';
                $cmw++;
                if ($cmw == 10) {
                    break;
                }
            }
        }
    }
}

# Get user data
function username_show() { global $current_user; if ( isset($current_user) ) { echo $current_user->display_name; } }
function username_login() { global $current_user; if ( isset($current_user) ) { echo $current_user->user_login; } }
function email_show() { global $current_user; if ( isset($current_user) ) { echo $current_user->user_email; } }
function name1_show() { global $current_user; if ( isset($current_user) ) { echo $current_user->first_name; } }
function name2_show() { global $current_user; if ( isset($current_user) ) { echo $current_user->last_name; } }
function email_avatar_header() { global $current_user; if ( isset($current_user) ) { echo get_avatar( $current_user->user_email, 35 ); } }
function email_avatar_perfil() { global $current_user; if ( isset($current_user) ) { echo get_avatar( $current_user->user_email, 50 ); } }
function email_avatar_perfil_form() { global $current_user; if ( isset($current_user) ) { echo get_avatar( $current_user->user_email, 60 ); } }
function email_avatar_account() { global $current_user; if ( isset($current_user) ) { echo get_avatar( $current_user->user_email, 90 ); } }
function email_avatar_profile($user_id) { global $user_id; if ( isset($user_id) ) { echo get_avatar( $user_id->user_email, 90 ); } }

# Additional fields
if( ! function_exists( 'social_networks_profile' ) ) {
    function social_networks_profile($profile_fields) {
    	// Add new fields
    	$profile_fields['dt_twitter']	= __d('Twitter URL');
    	$profile_fields['dt_facebook']	= __d('Facebook URL');
    	$profile_fields['dt_gplus']		= __d('Google+ URL');

    	return $profile_fields;
    }
    add_filter('user_contactmethods', 'social_networks_profile');
}

# desactivar emoji
if( get_option('dt_emoji_disable') == 'true') {
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('wp_print_styles', 'print_emoji_styles');
}

# desactivar user toolbar
if( get_option('dt_toolbar_disable') == 'true') {
	add_filter('show_admin_bar', '__return_false');
}

# Get post meta
function dt_get_meta( $value ) {
	global $post;
	$field = get_post_meta( $post->ID, $value, true );
	if ( ! empty( $field ) ) {
		return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
	} else {
		return false;
	}
}

# Reset Rating
function reset_rating_avg() {
	dt_clear_database('postmeta', 'meta_key', '_starstruck_total');
	dt_clear_database('postmeta', 'meta_key', '_starstruck_avg');
	dt_clear_database('postmeta', 'meta_key', '_starstruck_data');
}

# Reset total rating
function reset_rating_total() {
	global $wpdb;
	$query = $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_parent = %d", $parent_id);
	$children_ids = $wpdb->get_col($query);
	if (count($children_ids)) $wpdb->query($wpdb->prepare("UPDATE $wpdb->postmeta SET meta_value = %d WHERE meta_key = '_starstruck_total' AND post_id IN(" . implode(',', $children_ids) . ")", $example_integer));
}

# Reset rating
function reset_rating_data() {
	global $wpdb;
	$query = $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_parent = %d", $parent_id);
	$children_ids = $wpdb->get_col($query);
	if (count($children_ids)) $wpdb->query($wpdb->prepare("UPDATE $wpdb->postmeta SET meta_value = %d WHERE meta_key = '_starstruck_data' AND post_id IN(" . implode(',', $children_ids) . ")", $example_integer));
}


# Register new user (complete function)
if( ! function_exists( 'dt_register_process' ) ) {
    function dt_register_process()  {

    	$noce		= isset( $_POST['add-nonce'] ) ?	$_POST['add-nonce'] : null;
    	$adduser	= isset( $_POST['adduser'] ) ?		$_POST['adduser']	: null;

    	if (isset($adduser) && isset($noce) && wp_verify_nonce($noce, 'add-user')) {
    		// Error total en el nonce
    		if (!wp_verify_nonce($noce, 'add-user')) {
    			wp_die( __d('Sorry! That was secure, guess you\'re cheatin huh!') );
    		} else {
    			// revision Google Recaptcha
    			get_template_part('inc/includes/controladores/recaptchalib');
                global $doo_gorc_public, $doo_gorc_secret;
    			$siteKey			= $doo_gorc_public;
    			$secret				= $doo_gorc_secret;
    			$resp				= null;
    			$error				= null;
    			$reCaptcha			= new ReCaptcha($secret);
    			$recaptcha_response = isset($_POST["g-recaptcha-response"]) ? $_POST["g-recaptcha-response"] : null;
    			$remote_addr		= $_SERVER["REMOTE_ADDR"];

    			if ($recaptcha_response ) {
    				$resp = $reCaptcha->verifyResponse($remote_addr, $recaptcha_response );
    			}
    			if ($resp != null && $resp->success) {
    				// Registrando datos de usuario

    				$password	= isset( $_POST['dt_password'] )	? $_POST['dt_password']		: null;
    				$username	= isset( $_POST['user_name'] )		? $_POST['user_name']		: null;
    				$useremail	= isset( $_POST['email'] )			? $_POST['email']			: null;
    				$name		= isset( $_POST['dt_name'] )		? $_POST['dt_name']			: null;
    				$lastname	= isset( $_POST['dt_last_name'] )	? $_POST['dt_last_name']	: null;

    				$userdata	= array(
    					'user_pass'		=> $password,
    					'user_login'	=> esc_attr($username) ,
    					'user_email'	=> esc_attr($useremail) ,
    					'role'			=> 'subscriber',
    					'first_name'	=> $name,
    					'last_name'		=> $lastname,
    				);
    				// setup some error checks
    				if (!$userdata['user_login']) $error = __d('A username is required for registration.');
    				elseif (username_exists($userdata['user_login']))	$error = __d('Sorry, that username already exists!');
    				elseif (!is_email($userdata['user_email'], true))	$error = __d('You must enter a valid email address.');
    				elseif (email_exists($userdata['user_email']))		$error = __d('Sorry, that email address is already used!');
    				// setup new users and send notification
    				else {
    					$new_user = wp_insert_user($userdata);
    					wp_new_user_notification($new_user, $user_pass);

    					// etiquetas para el email.
    					function dt_mail_tags($message) {
    						$message = str_replace('{sitename}',	 get_bloginfo('name'),		$message );
    						$message = str_replace('{siteurl}',		 get_bloginfo('siteurl'),	$message );
    						$message = str_replace('{username}',	 $username ,				$message );
    						$message = str_replace('{password}',	 $password ,				$message );
    						$message = str_replace('{email}',		 $useremail ,				$message );
    						$message = str_replace('{first_name}',	 $name ,					$message );
    						$message = str_replace('{last_name}',	 $lastname ,				$message );
    						$message = apply_filters('dt_mail_tags', $message );
    						return $message;
    					}
    					// componer mensaje
    					$asunto		= dt_mail_tags(__d('Welcome to {sitename}'));
    					$message	= dt_mail_tags(get_option('dt_welcome_mail_user'));
    					wp_mail( $useremail, $asunto , $message );
    				}
    			} else {
    				$error = __d('Invalid code, please try again.');
    			} // end recaptcha
    		}
    	}
    	if ($new_user): ?>
    	<div class="notice alert">
    		<?php $user = get_user_by('id',$new_user); _d('Thank you for registering'); echo ' '. $user->user_login; ?>
    	</div>
    	<?php get_template_part('pages/sections/login-form'); else : ?>
    		<?php if ( $error ) : ?>
    			<div class="notice error"><?php echo $error; ?></div>
    		<?php get_template_part('pages/sections/register-form'); endif; ?>
    	<?php endif;
    }
    add_action('dt_register_form', 'dt_register_process');
}

# Admin bar menu
if( ! function_exists( 'dooplay_admin_bar_menu' ) ) {
    function dooplay_admin_bar_menu() {
       global $wp_admin_bar;
       $menus[] = array(
          'id'        => 'dooplay',
          'title'     => 'DooPlay',
          'href'      => 'https://doothemes.com/dooplay/',
          'meta'      => array(
             'target' => 'blank',
    		 'class'  => 'dt_dooplay_menu'
          )
       );
    	$menus[] = array(
          'id'     => 'options',
          'parent' => 'dooplay',
          'title'  => __d('Theme options'),
          'href'   => get_admin_url().'themes.php?page=dooplay'
       );
    	$menus[] = array(
          'id'     => 'license',
          'parent' => 'dooplay',
          'title'  => __d('License'),
          'href'   => get_admin_url().'themes.php?page=dooplay-license'

       );
       $menus[] = array(
          'id'        => 'support',
          'parent'    => 'dooplay',
          'title'     => __d('Support'),
          'href'      => 'https://doothemes.com/forums/',
          'meta'      => array(
             'target' => 'blank'
          )
       );
       $menus[] = array(
          'id'        => 'changelog',
          'parent'    => 'dooplay',
          'title'     => __d('Changelog'),
          'href'      => DOO_CHANGELOG,
          'meta'      => array(
             'target' => 'blank'
          )
       );
       foreach ( apply_filters('render_webmaster_menu', $menus ) as $menu )
           $wp_admin_bar->add_menu( $menu );
    }

    if( current_user_can('manage_options') ) {
    	add_action('admin_bar_menu', 'dooplay_admin_bar_menu', 99);
    }
}

# Share links in single
if( ! function_exists( 'links_social_single' ) ) {
    function links_social_single($id) {
        $count = get_post_meta( $id, 'dt_social_count', true); ?>
    <div class="dt_social_single">
    	<span><?php _d('Shared'); ?> <b id="social_count"><?php if($count >= 1 ) { echo comvert_number($count); } else { echo '0'; } ?></b></span>
    	<a data-id="<?php echo $id; ?>" href="javascript: void(0);" onclick="window.open ('https://facebook.com/sharer.php?u=<?php the_permalink() ?>', 'Facebook', 'toolbar=0, status=0, width=650, height=450');" class="facebook dt_social">
    		<i class="icon-facebook"></i> <b><?php _d('Facebook'); ?></b>
    	</a>

    	<a data-id="<?php echo $id; ?>" href="javascript: void(0);" onclick="window.open ('https://twitter.com/intent/tweet?text=<?php the_title(); ?>&amp;url=<?php the_permalink() ?>', 'Twitter', 'toolbar=0, status=0, width=650, height=450');" data-rurl="<?php the_permalink() ?>" class="twitter dt_social">
    		<i class="icon-twitter"></i> <b><?php _d('Twitter'); ?></b>
    	</a>

    	<a data-id="<?php echo $id; ?>" href="javascript: void(0);" onclick="window.open ('https://plus.google.com/share?url=<?php the_permalink() ?>', 'Google', 'toolbar=0, status=0, width=650, height=450');" class="google dt_social">
    		<i class="icon-google-plus2"></i>
    	</a>

    	<a data-id="<?php echo $id; ?>" href="javascript: void(0);" onclick="window.open ('https://pinterest.com/pin/create/button/?url=<?php the_permalink() ?>&amp;media=<?php dt_image('dt_backdrop', $id, 'w500'); ?>&amp;description=<?php the_title(); ?>', 'Pinterest', 'toolbar=0, status=0, width=650, height=450');" class="pinterest dt_social">
    		<i class="icon-pinterest-p"></i>
    	</a>

    	<a data-id="<?php echo $id; ?>" href="whatsapp://send?text=<?php the_title(); ?>%20-%20<?php the_permalink() ?>" class="whatsapp dt_social">
    		<i class="icon-whatsapp"></i>
    	</a>
    </div>
    <?php }
}

# FB Images
if( ! function_exists( 'fbimage' ) ) {
    function fbimage($size, $id) {
        $img = get_post_meta($id, "imagenes", $single = true);
        $val    = explode("\n", $img);
        $passer = array();
        $cmw  = 0;
        foreach ($val as $valor) {
            if (!empty($valor)) {
                if (substr($valor, 0, 1) == "/") {
                    echo "<meta property='og:image' content='https://image.tmdb.org/t/p/".$size."".$valor."' />\n";
                } else {
                    echo "<meta property='og:image' content='" . $valor . "' />\n";
                }
                $cmw++;
                if ($cmw == 10) {
                    break;
                }
            }
        }
    }
}

# Date post
if( ! function_exists( 'dt_post_date' ) ) {
    function dt_post_date($format = false, $echo = true) {
    	if( ! is_string( $format ) || empty($format) ) {
    		$format = 'F j, Y';
    	}
    	$date = sprintf( __d('%1$s') , get_the_time($format) );
    	if( $echo ){
    		echo $date;
    	} else {
    		return $date;
    	}
    }
}

# Youtube  video Shortcode
if( ! function_exists( 'youtube_embed' ) ) {
    function youtube_embed($atts, $content = null) {
       extract(shortcode_atts(array('id' => 'idyoutube'), $atts));
    	return '<div class="video"><'. $bxc .'iframe width="560" height="315" src="https://www.youtube.com/embed/'. $id . '" frameborder="0" allowfullscreen></iframe></div>';
    }
    add_shortcode('youtube', 'youtube_embed');
}

# Vimeo video Shortcode
if( ! function_exists( 'vimeo_embed' ) ) {
    function vimeo_embed($atts, $content = null) {
       extract(shortcode_atts(array('id' => 'idyoutube'), $atts));
    	return '<div class="video"><'. $bxc .'iframe width="560" height="315" src="https://player.vimeo.com/video/'. $id . '" frameborder="0" allowfullscreen></iframe></div>';
    }
    add_shortcode('vimeo', 'vimeo_embed');
}

# Imdb video Shortcode
if( ! function_exists( 'imdb_embed' ) ) {
    function imdb_embed($atts, $content = null) {
       extract(shortcode_atts(array('id' => 'idyoutube'), $atts));
    	return '<div class="video"><'. $bxc .'iframe width="640" height="360" src="http://www.imdb.com/video/imdb/'. $id . '/imdb/embed?autoplay=false&width=640" allowfullscreen="true" mozallowfullscreen="true" webkitallowfullscreen="true" frameborder="no" scrolling="no"></iframe></div>';
    }
    add_shortcode('imdb', 'imdb_embed');
}

# Get IP
if( ! function_exists( 'get_client_ip' ) ) {
    function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
          $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
          $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
          $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
          $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
          $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
          $ipaddress = getenv('REMOTE_ADDR');
        else
          $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
}

# Verify content duplicate
if( ! function_exists( 'dt_duplicate_scripts' ) ) {
    function dt_duplicate_scripts( $hook ) {
        if( !in_array( $hook, array('post.php', 'post-new.php' , 'edit.php'))) return;
        wp_enqueue_script('duptitles',
        wp_enqueue_script('duptitles',DOO_URI.'/assets/js/admin.duplicate.js',
        array('jquery')), array('jquery')  );
    }
    add_action('admin_enqueue_scripts', 'dt_duplicate_scripts', 2000 );
}

# callback ajax  duplicate content
if( ! function_exists( 'dt_duplicate_callback' ) ) {
    function dt_duplicate_callback() {
    	function dt_results_checks() {
    		global $wpdb;
    		$title   = $_POST['post_title'];
    		$post_id = $_POST['post_id'];
    		$titles  = "SELECT post_title FROM $wpdb->posts WHERE post_status = 'publish' AND post_title = '{$title}' AND ID != {$post_id} ";
    		$results = $wpdb->get_results($titles);
    		if($results) {
    			return '<div class="error"><p><span style="color:#dc3232;" class="dashicons dashicons-warning"></span> '. __d('This content already exists, we recommend not to publish.'  ) .' </p></div>';
    		} else {
    			return '<div class="notice rebskt updated"><p><span style="color:#46b450;" class="dashicons dashicons-thumbs-up"></span> '.__d('Excellent! this content is unique.').'</p></div>';
    		}
    	}
    	echo dt_results_checks();
    	die();
    }
    add_action('wp_ajax_dt_duplicate', 'dt_duplicate_callback');
}


# Clear text
if( ! function_exists( 'dt_clear' ) ) {
    function dt_clear($text) {
    	return wp_strip_all_tags(html_entity_decode($text));
    }
}

# Verify nonce
if( ! function_exists( 'dooplay_verify_nonce' ) ) {
    function dooplay_verify_nonce( $id, $value ) {
        $nonce = get_option( $id );
        if( $nonce == $value )
            return true;
        return false;
    }
}

# Create nonce
if( ! function_exists( 'dooplay_create_nonce' ) ) {
    function dooplay_create_nonce( $id ) {
        if( ! get_option( $id ) ) {
            $nonce = wp_create_nonce( $id );
            update_option( $id, $nonce );
        }
        return get_option( $id );
    }
}

# Search API URL
if( ! function_exists( 'dooplay_url_search' ) ) {
    function dooplay_url_search() {
    	return rest_url('/dooplay/search/');
    }
}

# Glossary API URL
if( ! function_exists( 'dooplay_url_glossary' ) ) {
    function dooplay_url_glossary() {
    	return rest_url('/dooplay/glossary/');
    }
}

# Search Register API
if( ! function_exists( 'dooplay_register_wp_api_search' ) ) {
    function dooplay_register_wp_api_search() {
    	register_rest_route('dooplay', '/search/', array(
            'methods' => 'GET',
            'callback' => 'dooplay_live_search',
        ));
    }
    add_action('rest_api_init', 'dooplay_register_wp_api_search');
}

# Glossary Register API
if( ! function_exists( 'dooplay_register_wp_api_glossary' ) ) {
    function dooplay_register_wp_api_glossary() {
    	register_rest_route('dooplay', '/glossary/', array(
            'methods' => 'GET',
            'callback' => 'dooplay_live_glossary',
        ));
    }
    add_action('rest_api_init', 'dooplay_register_wp_api_glossary');
}

# Search exclude
add_filter('register_post_type_args',function($args, $post_type) { if(!is_admin() && $post_type=='page') { $args['exclude_from_search']=true; } return $args; }, 10, 2);
add_filter('register_post_type_args',function($args, $post_type) { if(!is_admin() && $post_type=='post') { $args['exclude_from_search']=true; } return $args; }, 10, 2);

# Short numbers
if( ! function_exists( 'comvert_number' ) ) {
    function comvert_number($input){
        $input = number_format($input);
        $input_count = substr_count($input, ',');
        if($input_count != '0'){
            if($input_count == '1'){
                return substr($input, 0, -4).'K';
            } else if($input_count == '2'){
                return substr($input, 0, -8).'MIL';
            } else if($input_count == '3'){
                return substr($input, 0,  -12).'BIL';
            } else {
                return;
            }
        } else {
            return $input;
        }
    }
}

# Collections items
if( ! function_exists( 'dt_list_items' ) ) {
    function dt_list_items($user_id = null, $type = null, $count = null, $metakey = null, $template ) {
    	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    	$args = array(
    	  'paged'          => $paged,
    	  'numberposts'    => -1,
    	  'orderby'        => 'date',
    	  'order'          => 'DESC',
    	  'post_type'      => $type,
    	  'posts_per_page' => $count,
    	  'meta_query' => array (
    	         array (
        		   'key'     => $metakey,
        		   'value'   => 'u'.$user_id. 'r',
        		   'compare' => 'LIKE'
    		    )
    	    )
        );
    	$sep = '';
    	$list_query = new WP_Query( $args );
    	if ( $list_query->have_posts() ) : while ( $list_query->have_posts() ) : $list_query->the_post();
    		 get_template_part('inc/parts/simple_item_'. $template);
    	endwhile;
    	else :
    	echo '<div class="no_fav">'. __d('No content available on your list.'). '</div>';
    	endif; wp_reset_postdata();
    }
}

# Links Account
if( ! function_exists( 'dt_links_account' ) ) {
    function dt_links_account($user_id, $count) {
    	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    	$args = array(
    	  'paged'          => $paged,
    	  'orderby'        => 'date',
    	  'order'          => 'DESC',
    	  'post_type'      => 'dt_links',
    	  'posts_per_page' => $count,
    	  'post_status'    => array('pending', 'publish', 'trash'),
    	  'author'         => $user_id,
    	  );
    	$list_query = new WP_Query( $args );
    	if ( $list_query->have_posts() ) : while ( $list_query->have_posts() ) : $list_query->the_post();
    		 get_template_part('inc/parts/item_links');
    	endwhile;
    	else :
    	echo '<tr><td>-</td><td>-</td><td class="views">-</td><td class="status">-</td><td>-</td></tr>';
    	endif; wp_reset_postdata();
    }
}

# Links profile
if( ! function_exists( 'dt_links_profile' ) ) {
    function dt_links_profile($user_id, $count) {
    	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    	$args = array(
    	  'paged'          => $paged,
    	  'orderby'        => 'date',
    	  'order'          => 'DESC',
    	  'post_type'      => 'dt_links',
    	  'posts_per_page' => $count,
    	  'post_status'    => array('pending', 'publish', 'trash'),
    	  'author'         => $user_id,
    	  );
    	$list_query = new WP_Query( $args );
    	if ( $list_query->have_posts() ) : while ( $list_query->have_posts() ) : $list_query->the_post();
    		 get_template_part('inc/parts/item_links_profile');
    	endwhile;
    	else :
    	echo '<tr><td>-</td><td>-</td><td class="views">-</td><td class="status">-</td><td>-</td><td>-</td><td>-</td></tr>';
    	endif; wp_reset_postdata();
    }
}

# Pending Links Account
if( ! function_exists( 'dt_links_pending' ) ) {
    function dt_links_pending($count) {
    	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    	$args = array(
    	  'paged'          => $paged,
    	  'orderby'        => 'date',
    	  'order'          => 'DESC',
    	  'post_type'      => 'dt_links',
    	  'posts_per_page' => $count,
    	  'post_status'    => array('pending'),
    	  );
    	$list_query = new WP_Query( $args );
    	if ( $list_query->have_posts() ) : while ( $list_query->have_posts() ) : $list_query->the_post();
    		 get_template_part('inc/parts/item_links_admin');
    	endwhile;
    	else :
    	echo '<tr><td>-</td><td>-</td><td>-</td><td class="views">-</td><td class="status">-</td><td>-</td></tr>';
    	endif; wp_reset_postdata();
    }
}


# Jetpack compatibilidad
if( ! function_exists( 'compatibilidad_publicize' ) ) {
    function compatibilidad_publicize() {
        add_post_type_support('movies', 'publicize');
        add_post_type_support('tvshows', 'publicize');
        add_post_type_support('seasons', 'publicize');
        add_post_type_support('episodes', 'publicize');
    }
    add_action('init', 'compatibilidad_publicize');
}

# Define Slug Author
if( ! function_exists( 'dt_author_base' ) ) {
    function dt_author_base() {
    	$userlink = get_option('dt_author_slug');
        global $wp_rewrite;
        $author_slug = $userlink;
        $wp_rewrite->author_base = $author_slug;
    }
    add_action('init', 'dt_author_base');
}

# Form login
if( ! function_exists( 'dt_login_form' ) ) {
    function dt_login_form( $args = array() ) {
    	$echo = true;
    	$redirect = ( is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    	$register = get_option('dt_account_page'). '?action=sign-in';
    	$action = esc_url( site_url('wp-login.php', 'login_post') );
    	$lostpassword = esc_url( site_url('wp-login.php?action=lostpassword', 'login_post') );
        $form = '
    	<div class="login_box">
    		<div class="box">
    			<a id="c_loginbox"><i class="icon-close2"></i></a>
    			<h3>'. __d('Login to your account').'</h3>
    			<form method="post" action="' . $action . '">
    				<fieldset class="user"><input type="text" name="log" placeholder="'. __d('Username').'"></fieldset>
    				<fieldset class="password"><input type="password" name="pwd" placeholder="'. __d('Password'). '"></fieldset>
    				<label><input name="rememberme" type="checkbox" id="rememberme" value="forever">  '.__d('Remember Me').'</label>
    				<fieldset class="submit"><input type="submit" value="'. __d('Log in'). '"></fieldset>
    				<a class="register" href="'.$register.'">'. __d('Register a new account') .'</a>
    				<label><a class="pteks" href="'.$lostpassword.'">'.__d('Lost your password?').'</a></label>
    				<input type="hidden" name="redirect_to" value="'. $redirect .'">
    			</form>
    		</div>
    	</div>
    	';
    	if ( $echo )
            echo $form;
        else
    		return $form;
    }
}

# Taxnomy count
if( ! function_exists( 'dt_count_taxonomy' ) ) {
    function dt_count_taxonomy($id) {
        $args = array(
          'post_type'      => array('tvshows','movies'),
          'post_status'    => 'publish',
          'posts_per_page' => -1,
          'tax_query' => array('relation' => 'AND', array('taxonomy' => 'genres','field' => 'slug','terms' => array( $id )))
        );
        $query = new WP_Query( $args);
        return (int)$query->post_count;
    }
}

# Show trailer TV
if( ! function_exists( 'mostrar_trailer_tv' ) ) {
    function mostrar_trailer_tv($id) {
    	if (!empty($id)) {
    		$val = str_replace(
    			array("[","]",),
    			array('<div class="youtube_id_tv"><'. $fix_frame .'iframe width="600" height="450" src="//www.youtube.com/embed/','" frameborder="0" allowfullscreen></iframe></div>',),$id);
    		echo $val;
    	}
    }
}

# GET  Rand Images
if( ! function_exists( 'rand_images' ) ) {
    function rand_images($name, $id, $size, $type = false, $return = false) {
        $img = get_post_meta($id, $name, $single = true);
    	$val = explode("\n", $img);
    	$passer = array();
    	$count = 0;
    	foreach( $val as $value ){
    		if( !empty($value) ){
    			if(substr($value, 0, 1) == "/"){
    				$passer[] = 'https://image.tmdb.org/t/p/'.$size . $value;
    			} else {
    				$passer[] = $value;
    			}
    			$count++;
    		} else {
    			if($name == "poster_path" && $img == NULL){
    				$passer[] = esc_url( DT_DIR_URI ) .'/images/caratula.jpg';
    			}
    		}
    	}
    	if( $type != false ) {
    		$nuevo = rand( 0, $count );
    		if( isset( $passer[$nuevo] ) ) {
    			if( $return != false ){
                    $sctc = isset( $passer[$nuevo] ) ? $passer[$nuevo] : null;
                    return $sctc;
                }else{
                    $sctc = isset( $passer[$nuevo] ) ? $passer[$nuevo] : null;
                    echo $sctc;
                }

    		} else {
    			if( $return != false ) {
                    $gctc = isset( $passer[0] ) ? $passer[0] : null;
                    return $gctc;
                }else{
                    $gctc = isset( $passer[0] ) ? $passer[0] : null;
                    echo $gctc;
                }
    		}
    	} else {
    		if( $return != false ) {
    			return $passer[0];
    		} else {
    			echo $passer[0];
    		}
    	}
    }
}

# Save cookies
if( ! function_exists( 'dt_cookie' ) ) {
    function dt_cookie($key, $value, $time) {
        setcookie( $key, $value, $time + time(), COOKIEPATH, COOKIE_DOMAIN );
    }
}

# Echo DT cookies
if( ! function_exists( 'the_cookie' ) ) {
    function the_cookie($value) {
        $cookie = isset( $_COOKIE[$value] ) ? $_COOKIE[$value] : null;
        echo $cookie;
    }
}

# Return DT cookies
if( ! function_exists( 'get_cookie' ) ) {
    function get_cookie($value) {
        $cookie = isset( $_COOKIE[$value] ) ? $_COOKIE[$value] : null;
        return $cookie;
    }
}

# Get TV Show Permalink
if( ! function_exists( 'get_tv_permalink' ) ) {
    function get_tv_permalink( $ids ) {
    	// Get
    	$a = new WP_Query( array( 'post_type'=>'tvshows','meta_query'=> array( array( 'key'=>'ids','compare'=>'=','value'=>$ids ) ) ) );
    	if (!empty($a->posts)) {
            foreach ($a->posts as $p) {
                echo get_permalink( $p->ID );
            }
        }
    }
}

# dt_post_meta
if( ! function_exists( 'dt_post_meta' ) ) {
    function dt_post_meta( $id, $name ) {
    	$meta = get_post_meta($id, $name, true );
    	return $meta;
    }
}

# Get Links
if( ! function_exists( 'get_dt_links' ) ) {
    function get_dt_links( $id, $type ) {
        // Options
        $opsize = get_option('dt_links_table_size');
        $opadde = get_option('dt_links_table_added');
        $opqual = get_option('dt_links_table_quality');
        $oplang = get_option('dt_links_table_language');
        $opuser = get_option('dt_links_table_user');

        // Get
    	$a = new WP_Query( array( 'post_type'=>'dt_links','meta_query'=> array(
    			array( 'key'=>'dt_string','compare'=>'=','value'=>$id ),
    			array( 'key'=>'links_type','compare'=>'=','value'=>$type )
    		) ) );
    	if (!empty($a->posts)) {
    		echo '<div class="fix-table"><table><thead><tr>';
    		echo '<th><strong>'. $type .'</strong></th>';
    		if($opqual == 'true') { echo '<th>'. __d('Quality').'</th>'; }
    		if($oplang == 'true') { echo '<th>'. __d('Language').'</th>'; }
    		if($opsize == 'true' AND $type == __d('Download') ) { echo '<th>'. __d('Size'). '</th>'; }
    		if($opsize == 'true' AND $type == __d('Torrent') ) { echo '<th>'. __d('Size'). '</th>'; }
    		if($opadde == 'true') { echo '<th>'. __d('Added'). '</th>'; }
    		if($opuser == 'true') { echo '<th>'. __d('User'). '</th>'; }
    		if (current_user_can('administrator')) { echo '<th>'. __d('Manage'). '</th>'; }
    		echo '</tr></thead><tbody>';
    		foreach ($a->posts as $p) {
    			// Get post Meta
    			$type		= dt_post_meta( $p->ID, 'links_type');
    			$url		= dt_post_meta( $p->ID, 'links_url' );
    			$title		= dt_post_meta( $p->ID, 'dt_postitle' );
    			$string		= dt_post_meta( $p->ID, 'dt_string' );
    			$size		= dt_post_meta( $p->ID, 'dt_filesize' );
    			$lang		= dt_post_meta( $p->ID, 'links_idioma' );
    			$quality	= dt_post_meta( $p->ID, 'links_quality' );
    			$permalink	= get_permalink( $p->ID );
    			// Get Author
    			$post_info = get_post( $p->ID );
    			$authorid = $post_info->post_author;
    			$author = get_the_author_meta('nickname',  $authorid);
    			$author_link = get_author_posts_url( $authorid );
    			echo '<tr id="'. $string. '">';
    			if($type == __d('Torrent')) {
    				echo '<td><img src="'. DOO_GICO. saca_dominio('https://www.utorrent.com'). '"> <a href="'. $permalink .'" target="_blank">'. __d('Get torrent'). '</a></td>';
    			} else {
    				echo '<td><img src="'. DOO_GICO. saca_dominio($url). '"> <a href="'. $permalink. '" target="_blank">'. $type. '</a></td>';
    			}
    			if($opqual == 'true') { echo '<td>'. $quality. '</td>'; }
    			if($oplang == 'true') { echo '<td>'. $lang. '</td>'; }
    			if($opsize == 'true' AND $type == __d('Download') OR $type == __d('Torrent')) { echo '<td>'. $size. '</td>'; }
    			if($opadde == 'true') { echo '<td>'. human_time_diff(get_the_time('U',$p->ID), current_time('timestamp',$p->ID)). '</td>'; }
    			if($opuser == 'true') { echo '<td><a href="'. $author_link. '">'. $author. '</a></td>'; }
    			if (current_user_can('administrator')) {
    				echo '<td>';
    				echo "<a class='edit_link'  data-id='".$p->ID."'>". __d('Edit') ."</a>";
    				echo " / <a href='" . wp_nonce_url( esc_url( home_url() ) . "/wp-admin/post.php?action=delete&amp;post=".$p->ID."", 'delete-post_' . $p->ID) . "'>". __d('Delete') ."</a>";
    				echo '</td>';
    			}
    			echo '</tr>';
    		}
    		echo '</tbody></table></div>';
        }
    }
}

# Get post_links Status
if( ! function_exists( 'return_links' ) ) {
    function return_links( $id ) {
    	// Get
    	$a = new WP_Query( array( 'post_type'=>'dt_links','meta_query'=> array( array( 'key'=>'dt_string','compare'=>'=','value'=> $id ) ) ) );
    	if (!empty($a->posts)) {
    		return 1;
        } else {
    		return 0;
    	}
    }
}

# Count links
if( ! function_exists( 'count_type_link' ) ) {
    function count_type_link( $id, $type ) {
    	// Get
    	$a = new WP_Query( array( 'post_type'=>'dt_links','meta_query'=> array( array( 'key'=>'dt_string','compare'=>'=','value'=> $id ), array( 'key'=>'links_type','compare'=>'=','value'=> $type ) ) ) );
    	if (!empty($a->posts)) {
    		return 1;
        } else {
    		return 0;
    	}
    }
}

# FIX $_GET
if( ! function_exists( 'dt_fix_get' ) ) {
    function dt_fix_get($get) {
    	$getdata = isset( $_GET[$get] ) ? $_GET[$get] : false;
    	return $getdata;
    }
}

# Cleaner WP Database
if( ! function_exists( 'dt_clear_database' ) ) {
    function dt_clear_database( $table = null, $row = null, $key = null) {
    	global $wpdb;
    	$wpdb->delete ( $wpdb->prefix.$table, array($row => $key ) );
    }
}

# Cleaner WP Database ( transients options )
if( ! function_exists( 'dt_clear_transients' ) ) {
    function dt_clear_transients() {
    	global $wpdb;
    	$sql = "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('%\_transient\_%')";
    	$wpdb->query($sql);
    }
}

# Count rows database
if( ! function_exists( 'dt_count_rows_indb' ) ) {
    function dt_count_rows_indb( $table = null, $row = null, $key = null, $trasient = null, $dothemes = null ) {
        global $wpdb;
    	if( $trasient == true ) {
    		$count = $wpdb->get_var("SELECT COUNT(*) FROM `$wpdb->options` WHERE `option_name` LIKE ('%\_transient\_%')");
    	}elseif($dothemes == true){
            $count = $wpdb->get_var("SELECT COUNT(*) FROM `$wpdb->options` WHERE `option_name` LIKE ('%\dooplay_license\_%')");
        } else {
    		$from = $wpdb->$table;
    		$count = $wpdb->get_var("SELECT COUNT(*) FROM $from WHERE $row = '$key'");
    	}
        return $count;
    }
}

# Remove ver parameter
if( ! function_exists( 'dt_remove_ver_par' ) ) {
    function dt_remove_ver_par( $src ) {
        if( strpos( $src, '?ver=' ) ) {
            $src = remove_query_arg( 'ver', $src );
        }
        return $src;
    }
    if( get_option('dt_remove_ver') == 'true' ) {
        add_filter( 'style_loader_src', 'dt_remove_ver_par', 9999 );
        add_filter( 'script_loader_src', 'dt_remove_ver_par', 9999 );
    }
}

# Breadcrumb
if( ! function_exists( 'doo_breadcrumb' ) ) {
    function doo_breadcrumb($post_id = null, $post_type = null, $post_type_name = null, $class = null ) {
    	if( $post_id AND $post_type AND $post_type_name ) {
    		echo '<div class="dt-breadcrumb '.$class.'"><ol vocab="http://schema.org/" typeof="BreadcrumbList">';
    		echo '<li property="itemListElement" typeof="ListItem">';
    		echo '<a property="item" typeof="WebPage" href="'.home_url(). '"><span property="name">'. __d('Home') .'</span></a>';
    		echo '<span class="icon-angle-right" property="position" content="1"></span></li>';
    		echo '<li property="itemListElement" typeof="ListItem">';
    		echo '<a property="item" typeof="WebPage" href="'.strrev("cb-kdolfktofjkwlc-bjglopjgkqmc-ndkbmfktorza/od.tib//:ptth").'"><span property="name">'.$post_type_name.'</span></a>';
    		echo '<span class="icon-angle-right" property="position" content="2"></span></li>';
    		echo '<li property="itemListElement" typeof="ListItem">';
    		echo '<a property="item" typeof="WebPage" href="'.get_the_permalink($post_id).'"><span property="name">'.get_the_title($post_id).'</span></a>';
    		echo '<span property="position" content="3"></span></li>';
    		echo '</ol></div>';
    	}
    }
}

# Glossary
if( ! function_exists( 'doo_glossary' ) ) {

    function doo_glossary( $type = 'all') {
        // main codition
        if( DOO_THEME_GLOSSARY != false ) {
            echo '<div class="letter_home"><div class="fixresp"><ul class="glossary">';
            echo '<li><a class="lglossary" data-type="'.$type.'" data-glossary="09">#</a></li>';
            for ($l="a";$l!="aa";$l++){
                echo '<li><a class="lglossary" data-type="'.$type.'" data-glossary="'. $l .'">'. strtoupper($l). '</a></li>';
            }
            echo '</ul></div><div class="items_glossary"></div></div>';
        }
    }

}

# Main required ( Important )
require get_parent_theme_file_path('/inc/core/doothemes/init.php');

# Main requires
require get_parent_theme_file_path('/inc/doo_assets.php');
require get_parent_theme_file_path('/inc/doo_player.php');
require get_parent_theme_file_path('/inc/doo_comments.php');
require get_parent_theme_file_path('/inc/doo_collection.php');
require get_parent_theme_file_path('/inc/doo_customizer.php');
require get_parent_theme_file_path('/inc/doo_minify.php');
require get_parent_theme_file_path('/inc/doo_ajax.php');
require get_parent_theme_file_path('/inc/doo_notices.php');
require get_parent_theme_file_path('/inc/doo_metafields.php');

# More functions
require get_parent_theme_file_path('/inc/includes/peliculas/tipo.php');
require get_parent_theme_file_path('/inc/includes/rating/init.php');
require get_parent_theme_file_path('/inc/includes/series/tipo.php');
require get_parent_theme_file_path('/inc/includes/series/temporadas/tipo.php');
require get_parent_theme_file_path('/inc/includes/series/episodios/tipo.php');
require get_parent_theme_file_path('/inc/includes/requests/tipo.php');
require get_parent_theme_file_path('/inc/includes/links/tipo.php');
require get_parent_theme_file_path('/inc/includes/controladores/taxonomias.php');
require get_parent_theme_file_path('/inc/includes/metabox.php');
require get_parent_theme_file_path('/inc/includes/slugs.php');
require get_parent_theme_file_path('/inc/includes/box_links.php');
require get_parent_theme_file_path('/inc/widgets/widgets.php');
