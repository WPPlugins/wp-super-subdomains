<?php

/*
Plugin Name: WP Super Subdomains
Plugin URI: http://www.jamviet.com
Description: This plugin allow you create subdomain without using Wordpress Multisite ! Setup your main categories, tag, pages, and authors as subdomains in one click !
Author: Jam Viet
Version: 1.1
Author URI: http://www.jamviet.com
*/

function jamchecked( $value, $current) {
	if ( ! is_array( $value ) ) return;
	if ( in_array( $current, $value ) ) {
		echo 'checked="checked"';
	}
}

class jamvietdotcom_options_supersubdomain {

	function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	function admin_menu() {
		add_options_page(
			'Subdomains Setup',
			'Super subdomains',
			'manage_options',
			'setup-subdomain',
			array(
				$this,
				'jamvietdotcom_settings_page'
			)
		);
	}

	function  jamvietdotcom_settings_page() {
		?>
		<div class="wrap">
			<h1><span class="dashicons dashicons-admin-tools"></span> Setup subdomain for your website</h1>
			<p>Here, you will set your author page, page, category and #tag turn to subdomain</p>
			<?php 
				$pass = true;
				$url = home_url();
				$U = parse_url( $url );
				echo '<p><em>';
				if ( strpos( $U['host'], 'www' ) === false ) {
					$pass = false;
					echo '<span class="dashicons dashicons-no-alt"></span> You must set your homepage to www like <b>www.'. $U['host'] . '</b> befor turn on Super Subdomain in Setting Menu, "www." avoid error for plugin to work';
				} else {
					echo '<span class="dashicons dashicons-yes"></span> Your home page is <b>'. $U['host'] . '</b>';
				}
				echo '</em></p>';
				
				echo '<p>';
				if ( get_option('permalink_structure') != '/%category%/%postname%.html' ) {
					$pass = false;
					echo '<span class="dashicons dashicons-no-alt"></span> Opps ! You should set your Permalink to <em>/%category%/%postname%.html</em>';
				} else {
					echo '<span class="dashicons dashicons-yes"></span> Great ! Your Permalink set to <i>'. get_option('permalink_structure') . '</i>';
				}
				echo '</p>';
				echo '<p>';
					if ( ! $pass)
						echo '<span class="dashicons dashicons-flag"></span> You can not active this plugin, fix it first';
					else
						echo '<span class="dashicons dashicons-yes"></span> Now you can use this plugin without worry';
				echo '</p>';
				
				if( isset($_POST['submit'])) {
					@update_option('subdomain_function', ($_POST['subdomain_function']) );
				}
				if ( ! $pass) {
					delete_option('subdomain_function');
				}
				
				$sub_func = get_option('subdomain_function');
			?>
			<form action="" method="post" name="supersubdomain_setup">
			<table class="form-table">
				<tbody>
				<tr>
					<th scope="row"><label for="subdomain_function">Turn on Subdomain for:</label></th>
					<td>
						<label><input  <?php jamchecked( $sub_func, 'category' ); ?>  type="checkbox" name="subdomain_function[]" value="category"> Category <small style="color: #999"> - catname.domain.com</small></label><br>
						<label><input  <?php jamchecked( $sub_func, 'tag' ); ?>  type="checkbox" name="subdomain_function[]" value="tag"> Tag <small style="color: #999"> - tagname.domain.com</small></label><br>
						<label><input <?php jamchecked( $sub_func, 'author' ); ?> type="checkbox" name="subdomain_function[]" value="author"> Author <small style="color: #999"> - user.domain.com</small></label><br>
						<label><input <?php jamchecked( $sub_func, 'page' ); ?> type="checkbox" name="subdomain_function[]" value="page"> Page <small style="color: #999"> - aboutus.domain.com</small></label><br>
					</td>
					
				</tr>
				</tbody>
			</table>
			<p>
				* Notice:
				<ol>
					<li>If you active subdomain for category, subcategory will like this <em>cat.domain.com/sub-cat</em> and post will like this: <em>cat.domain.com/post.html</em> or <i>cat.domain.com/sub-cat/post.html</i>
					<li>All old links now can be redirect to new links, it can not harm your SEO, i use 301 redirect
					<li>By default, i will redirect all 404 link like this: <b>notfound.domain.com</b> to <i>domain.com/opps_404_error</i>
					<li>I use double slash (//link) before link, so if your site using <b>https</b>, all link will be okey ! ( please test it )
					<li>This plugin is based on plugin name "wp subdomains revisited", although i do not using his function or class, just the method to slove problem !
					<li>The Premium plugin will come soon, so if you have an idea or want to comment, touch me <a href="http://www.jamviet.com/2016/03/plugin-wp-super-subdomains-create-subdomains-second.html" target="_blank">here</a>
					<li>I spend all the day to create this plugin, so if you want to invite me a cup of coffee, do not hesitate to <span class="dashicons dashicons-heart"></span> <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=9MSQMWM4VAYTN&lc=VN&item_name=Jam%20Project&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted" target="_blank"> donate me via PAYPAL</a>
				</ol>
			</p>
			<p class="submit"><input type="submit" value="<?php _e('Save') ?>" class="button button-primary" id="submit" name="submit"></p>
			</form>
		</div>
		<?php
	}
}

new jamvietdotcom_options_supersubdomain;
/*
	Tất cả các category sẽ bị biến thành category.vietdex.com
	Tất cả các category con biến thành category.vietdex.com/cat
	Tất cả các bài viết sẽ là category.vietdex.com/cat/abc.html
	Tất cả các tag sẽ là tag.vietdex.com/hash.hash
	
	Căn bản là đang dùng wp subdomain nhưng nó dài dòng quá với lại cài xong chậm bỏ cha nên được,
	tức liền phang luôn một mạch xong cái class luôn ...
*/
Class Start_init_subdomain {
	
	var $subdomain; // các thông tin liên quan tới subdomain category,tag,author
	var $slug; // chứa slug
	var $type; // dùng để phân biệt đấy là cái gì ... và add vào custome query của wp
	/*
	*	cần page gì là subdomain thì có thể dùng var page này ...
	*/
	var $pagedata;
	/*
		Đơn giản là không muốn gọi lại nhiều lần cái thông tin user nên để thế này chạy cho nó nhanh ...
		 // dùng để lưu trữ thông tin user lại nếu đang trong author ///
	*/
	
	var $root;// = 'vietdex.com';
	var $url;// = 'http://www.vietdex.com';
	
	var $subdomain_setting;
	
	function __construct() {
	
		// Phải có để nó chạy Custome query
		if (function_exists('create_initial_taxonomies')) {
			create_initial_taxonomies();
		}
		$this->url = home_url();
			$U = parse_url($this->url);
			$V = $U['host'];
		$this->root = str_replace('www.', '', $V);
		
		$this->subdomain_setting = $sub_func = get_option('subdomain_function');
		if ( ! empty ( $sub_func ) ) {
			$this->Inital_subdomain();
			$this->addActions();
			$this->addFilters();
		}
	}
	
	/* kiểm tra xem đây là subdomain gì ... */
	function Inital_subdomain() {
		$sub = $_SERVER['HTTP_HOST'];
		$sl = $_SERVER['REQUEST_URI'];
		$findsub = explode('.', $sub);
		$this->subdomain = $subdomain = $findsub[0];
		$subdomain_setting = $this->subdomain_setting;
		$slug = max( explode('/', $sl));
		$continue = true;
		/*
			Cần phải check để trả về loại nào
			Type =  1: cat
					2: tag
					3: author
					4: Page
					0: homepage
		*/
		if ( $subdomain == 'www' || count($findsub)< 3 ) {
			$this->type = 0;
		} else {
			/*
				Duyệt theo thứ tự, cat, tag, author, page 
			*/
			if ( in_array('category', $subdomain_setting ) ) {
				// nếu bật Category
				if ( get_term_by( 'slug', $subdomain, 'category' )  ) {
					/* thay hang */
					$this->type = 1;
					if ( $slug )
						/* for sub category */
						$this->slug = $slug;
					else
						/* for category parent */
						$this->slug = $subdomain;
					$continue = false;
				}
			}
			
			if ( in_array('tag', $subdomain_setting ) && $continue == true ) {
				// nếu bật Tag
				if ( get_term_by( 'slug', $subdomain, 'post_tag' )  ) {
					/* thay hang */
					$this->type = 2;
					if ( $slug )
						/* for sub category */
						$this->slug = $slug;
					else
						/* for category parent */
						$this->slug = $subdomain;
					$continue = false;
				}
			}
			
			if ( in_array('author', $subdomain_setting ) && $continue == true ) {
				// nếu bật Author
				if ( WP_User::get_data_by( 'slug', $subdomain ) ) {
					$this->type = 3;
					$this->slug = $subdomain;
					$this->subdomain = $subdomain;
					$continue = false;
				}
			}
			if ( in_array('page', $subdomain_setting ) && $continue == true ) {
				// nếu bật Page
				if ( get_page_by_path($subdomain) ) {
				// dùng cho custome page ...
					$this->type = 4;
					$this->slug = $this->subdomain = $subdomain;
					$continue = false;
				}
			}
			
			if ( $continue == true ) {
				/* Opsss nothing found ! I create a 404 error link */
				$url = ( home_url('/opps_404_error') );
				// 301 Moved Permanently
				header("Location: $url",TRUE,301);
				die('x-1');
			}
		
		} // endelse
	}


	/* nhóm này làm nhiệm vụ đổi đường dẫn trong page= và một vài chức năng khác nữa ... */
	// return this uri 
	function this_uri() {
		return 'http://'. $this->subdomain .'.'. $this->root . '/';
	}
	function supersubdomain_getUrlPath($url) {
		$parsed_url = parse_url($url);
		
		if(isset($parsed_url['path'])) {
		$path = ( (substr($parsed_url['path'], 0, 1) == '/') ? substr($parsed_url['path'], 1) : $parsed_url['path'] );
		} else {
			$path = '';
		}
		$path .= ( isset($parsed_url['query']) ? '?'.$parsed_url['query'] : '' );
		$path .= ( isset($parsed_url['fragment']) ? '#'.$parsed_url['fragment'] : '' );

		return $path;	
	}
	function changeGeneralLink( $link ) {
		$path = $this->supersubdomain_getUrlPath($link);
		$link = $this->this_uri() . $path;
		return $link;
	}
	
	// action //
	function addActions() {
		add_action( 'init', array($this, 'supersubdomain_init'), 99, 1 );
		add_action( 'wp', array( $this, 'jamviet_redirect' ), 99, 1 );
		
	}
	
	// filter
	function addFilters() {
		// khối subdomain chính làm cho wp hiểu đường dẫn này là cái gì  ///
		add_filter( 'rewrite_rules_array', array($this, 'jamviet_rewrite_rules' ));
		add_filter( 'root_rewrite_rules', array( $this, 'jamviet_root_rewrite_rules' ) );

		/* dùng cho page = me ... ... */
		$subdomain_setting = $this->subdomain_setting;
		foreach ( $subdomain_setting as $v ) {
			
			if ( $v == 'category'):
				add_filter( 'category_rewrite_rules', array( $this, 'jamviet_category_rewrite_rules' ), 99, 1 );
				add_filter( 'category_link', array($this, 'jamviet_category_link'), 99, 2 );
				add_filter( 'post_link', array($this, 'jamviet_post_link'), 99, 2 ); // post 
				add_filter( 'post_rewrite_rules', array( $this, 'jamviet_post_rewrite_rules' ), 99, 1 );
			endif;
			if ( $v == 'tag'):
				add_filter( 'tag_rewrite_rules', array($this, 'jamviet_tag_rewrite_rules' ), 99, 1 );
				add_filter( 'tag_link', array($this, 'jamviet_tag_link'), 99, 2 );
			endif;
			if ( $v == 'author'):
				add_filter( 'author_rewrite_rules', array( $this, 'jamviet_author_rewrite_rules' ), 99, 1 );
				add_filter( 'author_link', array( $this, 'jamviet_author_link'), 99, 2 );
			endif;
			if ( $v == 'page'):
				add_filter( 'page_rewrite_rules', array( $this, 'jamviet_page_rewrite_rules' ), 99, 1 );
				add_filter( 'page_link', array($this, 'jamviet_page_link'), 99, 2 ); // page
			endif;
		}
		
		
		
		/* URL Filters */
		//add_filter( 'bloginfo_url', array( $this, 'jamviet_filter_bloginfo_url'), 10, 2 );
		//add_filter( 'bloginfo', array( $this, 'jamviet_filter_bloginfo'), 10, 2 );
		
		
		
		#add_filter( 'post_type_link', array($this, 'jamviet_custom_post_link'), 10, 2 ); // Custom Post 
		
		if ( $this->type > 0 )
			add_filter( 'get_pagenum_link', array( $this, 'changeGeneralLink' ) );
	}
	
	/* flush rule ==> đã chỉnh lại ... */
	function supersubdomain_init () {
	// nếu cái này bị lỗi sẽ k xem được trang chủ ... đã tìm đc nguyên nhân và cần khắc phục hàm này ...
		if ( ! is_admin() ) {
			if (function_exists('set_transient'))
				set_transient('rewrite_rules', "");
			update_option('rewrite_rules', "");
		}
	}
	

/* đoạn này cải tiến vô biên, xóa hết các mặc định của WP đi rồi ...*/
/* nhẹ cơ sở dữ liệu vì các phần này được cache vào trong csdl mà ... ...*/
	function jamviet_rewrite_rules( $rules ) {
		//var_dump($rules);
		/*
		    foreach ($rules as $rule => $rewrite) {
				if ( preg_match('/(feed|attachment|comment-page|trackback|search)/',$rule) || preg_match('/(year|monthnum|attachment)/',$rewrite) ) {
					unset($rules[$rule]);
				}
			}
		*/
		//var_dump($rules);
		return $rules;
	}
	
	function jamviet_root_rewrite_rules( $rules ) {
	/* ảnh hưởng tới nhiều cái của trang chủ và liên quan tới tìm kiếm ! */
		if ( $this->type == 0) {
			/* khoa tam thoi
			unset( $rules);
			$rules = array();
			$rules["([^/]+)/([^/]+)?$"] = "index.php?mode=\$matches[1]&key=\$matches[2]";
			$rules["([^/]+)/([^/]+)/([^/]+)/?$"] = "index.php?mode=\$matches[1]&key=\$matches[2]&function=\$matches[3]";
			$rules["([^/]+)/([^/]+)/page/?([0-9]{1,})/?$"] = "index.php?mode=\$matches[1]&key=\$matches[2]&paged=\$matches[3]";
			*/
		}
		return $rules;
	}
	#///////////////////////////////////// KHỐI CHỈNH LẠI ĐƯỜNG DẪN CỦA TẤT TẬT ///////////////////////////////////#
	
	/* chỉnh lại đường dẫn của category */
	
	function jamviet_category_link( $link, $term_id ) {
		// return the link
		$data = get_category( $term_id);
			if ( $data->parent == 0)
				return '//'. $data->slug . '.' . $this->root;
		return preg_replace('#www.'.$this->root .'/category/(.+?)/(.+?)#','$1.'.$this->root .'/$2', $link);
	}

	/* chỉnh lại đường dẫn của post */
		/* chỉnh lại đường dẫn của post */
	function jamviet_post_link( $link, $post_object ) {
		return preg_replace('#www.'.$this->root .'/(.+?)/(.+?)#','$1.'.$this->root .'/$2', $link);
	}
		/* chỉnh lại đường dẫn của post */
	function jamviet_page_link( $link, $PID ) {
		$data = get_post($PID);
		//avoid using https //
		return '//'. $data->post_name . '.' . $this->root;
	}
	
	/* chỉnh lại đường dẫn của author */
	/* kì thực là đơn giản :P chỉ cần xáo chúng lên thôi */
	function jamviet_author_link($link, $id) {
		$newlink =  preg_replace('#www.'.$this->root .'/(.+)/(.+)#', '$2.'.$this->root .'/', $link);
		return $newlink;
	}
	/*
		Chỉnh lại đường dẫn của Tag
		Phải có .hash đằng cuối kẻo xung nhau ...
	*/
	
	function jamviet_tag_link($taglink) {
			return preg_replace('#www.'.$this->root .'/tag/(.+)#','$1.'.$this->root, $taglink);
	}
	
	
	
	#/////////////////////////////////// END CHỈNH LẠI ĐƯỜNG DẪN ////////////////////////////////#
	
	
	#/////////////////////////////////// REWRITE ////////////////////////////#
	
	// nhìn chung là phải có cái này ...
	function getRewriteRules() {
		switch ( $this->type ) {
			case 1 :
				$field = 'category_name';
			break;
			case 2 :
				$field = 'tag';
			break;
			case 3 :
				$field = 'author_name';
			break;
				// 3 là tag rồi ...
			case 4 :
				$field = 'pagename';
				/* extra là để kết hợp với plugin writing blog đó nhé ... */
			break;
			default:
				$field = 'category_name';
			break;
		}
		unset( $rules);
		$rules = array();
		//$rules["feed/(feed|rdf|rss|rss2|atom)/?$"] = "index.php?" . $field . "=" . $this->slug . "&feed=\$matches[1]";
		//$rules["(feed|rdf|rss|rss2|atom)/?$"] = "index.php?" . $field . "=" . $this->slug . "&feed=\$matches[1]";
		$rules["page/?([0-9]{1,})/?$"] = "index.php?" . $field . "=" . $this->slug . "&paged=\$matches[1]";
		/* remember, if /$? will break something */
		$rules["$"] = "index.php?" . $field . "=" . $this->slug;
		
		return $rules;
	}
	
	/* Viết lại đường dẫn cho author */
	function jamviet_author_rewrite_rules( $rules ) {
		// See if we're on a category subdomain
		if ( $this->type == 3) {
			$rules = $this->getRewriteRules();
		}
		return $rules;
	}	
	/* Viết lại đường dẫn cho cat */
	function jamviet_category_rewrite_rules( $rules ) {
		// See if we're on a category subdomain
		if ( $this->type == 1) {
			$rules = $this->getRewriteRules( );
		}
		
		return $rules;
	}
	
	/*
		Viết lại đường dẫn cho tag
	*/
	
    function jamviet_tag_rewrite_rules( $rules = array() ) {
		if ( $this->type == 2 ) {
			$rules = $this->getRewriteRules( );
		}
		return $rules;
    }
	
	/* post */
	function jamviet_page_rewrite_rules( $rules ) {
		if ( $this->type == 4 ) {
			$rules = $this->getRewriteRules( );
		}
		return $rules;
	}
	
	
	
	/* bài viết ... */
	function jamviet_post_rewrite_rules( $rules ) {
		global  $wp_rewrite;
		if ( strstr( $wp_rewrite->permalink_structure, '%category%' ) ) {
			// Grab the permalink structure
			$perma_tmp = $wp_rewrite->permalink_structure;
			
			// Remove the /%category section
			$perma_tmp = str_replace('/%category%','',$perma_tmp);
			
			// Create the extra rules using this new structure
			$extra_rules = $wp_rewrite->generate_rewrite_rules($perma_tmp, EP_PERMALINK);
			
			// Now we have to remove the rule that matches a category on it's own
			// this is reinstated later but just can't come before the extra rules
			$unset_key = array_search('index.php?category_name=$matches[1]', $extra_rules);

			if ($unset_key) {
				unset($extra_rules[$unset_key]);
			}
			
			// Check for the problem attachment rules and remove them.
			// Pray this doesn't break anything ;)
			foreach ($extra_rules as $regexp => $url) {
				if (strpos($url, 'attachment=$matches') && (strpos($regexp, 'attachment') === false)) {
					unset($extra_rules[$regexp]); 
				}
			}
			
			// merge to two rule sets into one
			$rules = array_merge($extra_rules, $rules);
		}
		
		return $rules;
	}
	
	
	// kiểm tra xem nó có lỗi gì không ! Nếu đang dạng www sẽ redirect sao cho đúng !
	function jamviet_redirect ($redirect = '') {
		global $wp_query;
		$redirect = false;
		$subdomain_setting = $this->subdomain_setting;
		/*
		Trong truong hop nguoi ta su dung domain.com?page=x
		thi chung ta redirect chung ngay lap tuc ...
		*/
		if ( $this->type == 0) {
				// Check if it's a category
			if ( $wp_query->is_category && in_array('category', $subdomain_setting) ) {
				$catID = get_query_var('cat');
				$redirect = get_term_link( $catID, 'category' );
			}
			// Check if Canonical Redirect is turned on
			if ($wp_query->is_single && in_array('category', $subdomain_setting) ) {
					$canonical = get_permalink($wp_query->post->ID);
					$redirect = $canonical;
			}
			// check xem có phải là page ko ?
			if ($wp_query->is_page  && in_array('page', $subdomain_setting) ) {
				$redirect = 'http://'. get_query_var('pagename') . '.' . $this->root;
			}
			if ($wp_query->is_tag  && in_array('tag', $subdomain_setting) ) {
					$canonical = get_term_link(get_query_var('tag'), 'post_tag');
					$redirect = $canonical;
			}
			if ($wp_query->is_author  && in_array('author', $subdomain_setting) ) {
					$canonical = get_author_posts_url(get_query_var('author'));
					$redirect = $canonical;
			}
		/*} elseif ( $this->type == 3) {
			// nếu không có tag nào được chọn ....
			if ( strlen($_SERVER['REQUEST_URI']) < 2 )
				$redirect = home_url();
			// nếu hiển thị nhầm sang category ?
			if ( ! $wp_query->is_tag )
				$redirect = home_url();
		*/
		} elseif ( $wp_query->is_home && $this->type != 0 ) {
			// bị cái bug nào đó, hiển thị trang chủ mà lại đường dẫn khác ... chán ...
			/* tạm thời khóa lại do cái này đang dính lỗi ...*/
			//$redirect = home_url();
		}
		// If a redirect is found then do it
		if ($redirect) {
			wp_redirect($redirect, 301);
			exit();
		}
	}

}

// Run the Plugin
new Start_init_subdomain;