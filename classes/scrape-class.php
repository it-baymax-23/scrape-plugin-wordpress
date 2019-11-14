<?php

use PicoFeed\Reader\Reader;
use PicoFeed\Config\Config;
use PicoFeed\Scraper\Scraper;

require_once(plugin_dir_path(__FILE__).'/libs/autoload.php');


class Scrape
{
	public static $task_id = 0;
	public static $count_id = 0;
	

	public function initajax()
	{
		add_action("wp_ajax_" . "get_url", array($this, "ajax_url_load"));
		add_action('wp_ajax_'."get_post",array($this,'ajax_post_type'));
		add_action('wp_ajax_'."get_post_category",array($this,'ajax_post_category'));
		add_action('wp_ajax_'.'get_category',array($this,'get_category'));
		// add_action('wp_ajax_'.'createcategory',array($this,'createcategory'));
		$this->scrape_savepost();
		$this->initaction();
	}

	public function createcategory($data)
	{
		$data = $data;
		$tax = $data['taxname'];
		$name = $data['value'];
		
		$id = term_exists($name,$tax,null);
	
		if($id)
		{
			return $id['term_id'];
		}
		else
		{
			$categoryid = wp_insert_category(array('taxonomy'=>$tax,'cat_name'=>$name));
			return $categoryid;	
		}	
	}

	public function get_category()
	{
		$tax = $_POST['data_tax'];
		$taxonomies = array();
		$taxonomies = get_terms('category');
		$tax_value = array();

		foreach($taxonomies as $value)
		{
			array_push($tax_value,array('id'=>$value->term_id,'name'=>$value->name));
		}
		echo json_encode($tax_value);
		exit;
	}

	public function scrape_savepost()
	{
		add_action('save_post',array($this,'savepost'),10,2);
	}

	public function initaction()
	{
		add_action('wp_trash_post',array($this,'action_trash_post'));
		add_action('load-edit.php',array($this,'scrape_action'));
	}

	public function action_trash_post($postid)
	{
		$post = get_post($postid);
		if($post->post_type == 'ns_scrape')
		{
			$post_id = get_post_meta($postid,'postid');
			if($post_id)
			{
				wp_delete_post($post_id,true);
			}	
		}
	}

	public function get_url_element($url)
	{
		$str_split = array();

		$token_string = substr($url,2,strlen($url)-2);
		$strtok = strtok($token_string,'[/]');

		$array = array();$array_string = array();
		$index = 0;
		while($strtok)
		{	
			if(!is_numeric($strtok))
			{
				array_push($array_string,$strtok);
			}
			$strtok = strtok('[/]');
		}

		return $array_string;
	}

	public function get_url_dom_element($dom,$element)
	{
		$document = new DOMXPath($dom);

		$element_string = '/';
		foreach($element as $elements)
		{
			$element_string .=  '/' . $elements ;
		}

		$dom_document = $document->query($element_string);
		
		return $dom_document;
	}

	public function setfeaturedimage($real_value,$id,$enable)
	{
		set_time_limit(40);
		$filename = basename($real_value);
		if(!$enable)
		{
			$filename = hash('ripemd160',$filename) . '.jpg';	
		}

		$wp_filetype = wp_check_filetype( $filename, null );
		$file = '';
		$upload_dir       = wp_upload_dir();
		if(!$enable)
		{
			if(!file_exists($real_value))
			{
				if( wp_mkdir_p( $upload_dir['path'] ) ) {
				    $file = $upload_dir['path'] . '/' . $filename;
				} else {
				    $file = $upload_dir['basedir'] . '/' . $filename;
				}

			
				try{
					$image_data       = file_get_contents($real_value);
					file_put_contents( $file, $image_data );	
				}
				catch(Exception $e)
				{
					return;
				}
				
			}	
		}
		else{
			$file = $real_value;
		}	
		
		// Set attachment data
		$attachment = array(
		    'post_mime_type' => $wp_filetype['type'],
		    'post_title'     => sanitize_file_name( $real_value ),
		    'post_content'   => '',
		    'post_status'    => 'inherit'
		);

		// Create the attachment
		$attach_id = wp_insert_attachment( $attachment, $file, $id );
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		// Define attachment metadata
		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
		// Assign metadata to attachment
		wp_update_attachment_metadata( $attach_id, $attach_data );

		// And finally assign featured image to post
		set_post_thumbnail( $id, $attach_id );

}
	
	public function get_auto_content($document)
	{	
		require_once "read.php";
		$readability = new Read($document->saveHTML());
		$readability->debug = false;
		$readability->convertLinksToFootnotes = false;
		$result = $readability->init();
		if ($result) {
			$content = $readability->getContent()->innerHTML;
			return $content;
		} else {
			return '';
		}
	}

	public function get_auto_title($document)
	{
		require_once "read.php";
		$readability = new Read($document->saveHTML());
		$readability->debug = false;
		$readability->convertLinksToFootnotes = false;
		$result = $readability->init();
		if ($result) {
			$content = $readability->getContentTitle()->innerHTML;
			return $content;
		} else {
			return '';
		}
	}

	public function add_post($post_content,$domxpath,$address = null)
	{
		$post = array(); $template = array(); $featured_image = array();
		$postmeta = array();
		if($post_content['title_from_feed'] == 'title_from_feed')
		{
			$post['post_title'] = $address['title'];
			$template['post_title'] = $address['title'];
		}
		else
		{
			if(strpos($post_content['post_title'],'html/body')>0)
			{
				$title = $post_content['post_title'];
				$title_element = $domxpath->query($title);
				$post['post_title'] = $title_element->item(0)->nodeValue;
				$template['post_title'] = $post['post_title'];	
			}
			else
			{
				$post['post_title'] = $post_content['post_title'];
				$template['post_title'] = $post['post_title'];	
			}
		}
		
		if(!$post['post_title'])
		{
			$document = $domxpath->document;
			$post['post_title'] = $this->get_auto_title($document);
			$template['post_title'] = $post['post_title'];
		}
		if($post_content['content_type'] == 'fromfeed')
		{
			$post['post_content'] = $address['content'];
			$template['post_content'] = $address['content'];
		}
		else
		{
			if(!isset($post_content['post_content']))
			{
				$document = $domxpath->document;
				$post['post_content'] = $this->get_auto_content($document);
				$template['post_content'] = $post['post_content'];
			}	
			else
			{
				if(strpos($post_content['post_content'],'html/body')>=0)
				{
					$title = $post_content['post_content'];
					$title_element = $domxpath->query($title);
					$node = $title_element->item(0);
					$post['post_content'] =  $this->get_content($this->get_html_from_node($title_element->item(0)));
					$template['post_content'] = $post['post_content'];	
				}
			}	
		}

		$category = '';
		if(strpos($post_content['category_value'],'html/body')>0)
		{
			$title = $post_content['category_value'];
			$title_element = $domxpath->query($title);
			$category = $title_element->item(0)->nodeValue;
			$template['categories'] = $category;
		}
		else
		{
			$category = $post_content['category_value'];
			$template['categories'] = $category;
		}
		$template['post_url'] = $post_content['post_url'];
		$catid = '';
		
		if($category)
		{
			$catid = $this->createcategory(array('taxname'=>$post_content['tax'],'value'=>$category));
		}
		$category_array = array();
		if(count($post_content['post_category'])>0)
		{
			$category_array = array_merge($category_array,$post_content['post_category']);
		}
		if($catid)
		{
			array_push($category_array,$catid);
		}
		if($post_content['post_tags'])
		{
			$posttags = '';
			if(strpos($post_content['post_tags'],'html/body')>0)
			{
				$title = $post_content['post_tags'];
				$title_element = $domxpath->query($title);
				$posttags = $title_element->item(0)->nodeValue;
				$template['post_tags'] = $posttags;
			}
			else
			{
				$posttags = $post_content['post_tags'];
				$template['post_tags'] = $posttags;
			}
			if($posttags)
			{
				$seps = $post_content['tags_seperator'];
				if(!$seps)
				{
					$seps = '';
				}
				$posttags = $this->getposttags($posttags,$seps);
				$post['tags_input'] = $posttags;
			}
		}

		if($post_content['excerpt'])
		{
			if(strpos($post_content['excerpt'],'html/body')>0)
			{
				$title = $post_content['excerpt'];
				$title_element = $domxpath->query($title);
				$excerpt = $title_element->item(0)->nodeValue;
			}
			else
			{
				$excerpt = $post_content['excerpt'];
			}
			if($excerpt)
			{
				$post['post_excerpt'] = $excerpt;
			}
		}

		if($post_content['post_custom_field'])
		{
			foreach($post_content['post_custom_field'] as $key => $value)
			{
				$realvalue = $value['Custom_Value'];
				if(strpos($value['Custom_Value'],'html/body') > 0)
				{
					$custom_element = $domxpath->query($value['Custom_Value']);
					$custom = $custom_element->item(0)->nodeValue;
					$realvalue = $custom;
				}
				$postmeta[$value['Custom_Name']] = $realvalue;
				$template[$value['Custom_Name']] = $realvalue;
			}		
		}

		if($post_content['featured_type'] == 'feed')
		{
			$f_image = array('url'=>$address['featured_image'],'enable'=>false);
			$template['post_thumbnail'] = '<img src="' . $address['featured_image'] . '"/>';
		}
		else
		{
			if($post_content['post_featured'])
			{
				$real_value = $post_content['post_featured'];
				if(strpos($post_content['post_featured'],'html/body') > 0)
				{
					try{
						$value_dom = $domxpath->query($post_content['post_featured']);
						if($value_dom->length > 0)
						{
							$real_value = $value_dom->item(0)->getAttribute('src');
							 $tmp_doc = new DOMDocument();
						    $tmp_doc->appendChild($tmp_doc->importNode($value_dom->item(0),true));
						    $html = $tmp_doc->saveHTML();
							$template['post_thumbnail'] = $html;

							if(!$real_value)
							{
								$value_dom_element = $value_dom->item(0);
								while($value_dom_element->tagName && $value_dom_element->tagName != 'img')
								{
									$value_dom_element = $value_dom_element->childNodes->item(0);
									if($value_dom_element->tagName)
									{
										$real_value = $value_dom_element->getAttribute('src');
									}	
									
								}
							}
						}
						
					}
					catch(Exception $e)
					{

					}
				$enable = false;
				}
				else
				{
					$enable = true;
				}

				$f_image = array('url'=>$real_value,'enable' => $enable);
			}
		}
		

		if($post_content['featured_type'] == 'feed')
		{
			$template['post_date'] = $address['date'];
		}
		else
		{
			if(strpos($post_content['date'],'html/body')>0)
			{
				$date_element = $domxpath->query($post_content['date']);
				$date = $date_element->item(0)->nodeValue;
				$template['post_date'] = $date;
			}
			else
			{
				$template['post_date'] = $post_content['date'];
			}

			if($post_content['post_content_template'])
			{
				$post['post_content'] = $this->get_template_content($post_content['post_content_template'],$template);
			}
		}
		

		if($post_content['post_title_template'])
		{
			$post['post_title'] = $this->get_template_content($post_content['post_title_template'],$template);
		}
		$post['post_name'] = $post['post_title'];
		$post['post_status'] = $post_content['post_status'];
		$id = wp_insert_post($post);
		
		foreach($postmeta as $key => $value)
		{
			update_post_meta($id,$key,$value);
		}

		if($f_image['url'])
		{
			$f_image['id'] = $id;
			array_push($featured_image,$f_image);	
		}
		wp_set_post_categories($id,$category_array,false);
		return array('id'=>$id,'featured_image' => $featured_image);
	}

	public function get_template_content($post_content,$post)
	{
		$template = array('[scrape_content]','[scrape_title]','[scrape_date]','[scrape_categories]','[scrape_tags]','[scrape_thumbnail]','[scrape_url]');

		$value = $post_content;

		foreach($template as $key => $template_value)
		{
			if(preg_match($template_value, $post_content))
			{
				$key_data = str_replace('[scrape_','post_',$template_value);
				$key_data = str_replace(']','',$key_data);
				$value = str_replace($template_value,$post[$key_data],$value);
			}
		}

		$matches = array();
		preg_match_all('/\[scrape_meta name="([^"]*)"\]/', $value, $matches);

		$full_match = $matches[0];
		$name_match = $matches[1];

		if(!empty($full_match))
		{
			$combined = array_combine($full_match,$name_match);

			foreach($combined as $meta_name => $meta_value)
			{
				$val = $post[$meta_value]?$post[$meta_value]:'';
				
				$value = str_replace($meta_name,$val,$value);
			}
		}

		return $value;
	}

	public function scrape_action()
	{
		set_time_limit(0);
		$nonce = $_GET['_wpnonce'];
		if(wp_verify_nonce($nonce,'scrape_action'))
		{
			$action = $_GET['action'];
			$postid = $_GET['post_id'];
			$postid_array = array();$featured_image = array();
			if($action == 'play')
			{
				$postmeta = get_post_meta($postid);
				
				$post_content = array(); $post_meta = array();

				$post_key_value = array('','post_title','post_content','post_content_template','post_categoryxpath_tax','post_categoryxpath','post_category','post_tags','excerpt','post_custom_field','tags_seperator','post_title_template','post_status','post_featured','post_url','date','date_type','featured_type','title_from_feed','content_type');
				
				foreach($postmeta as $key => $value)
				{
					if($key == 'post_title')
					{
						continue;
					}
					$post_key = str_replace('scrape_','',$key);
					
					if(array_search($post_key, $post_key_value))
					{
						if($post_key == 'post_categoryxpath_tax')
						{
							$post_content = array_merge($post_content,array('tax' => $value[0]));
						}
						else if($post_key == 'post_categoryxpath')
						{
							$post_content = array_merge($post_content,array('category_value' => $value[0]));
						}
						else if($post_key == 'post_category')
						{
							$values = unserialize($value[0]);
							$post_content = array_merge($post_content,array('post_category' => $values));
						}
						else if($post_key == 'post_custom_field')
						{
							$values = unserialize($value[0]);
							$post_content = array_merge($post_content,array($post_key => $values));
						}
						else
						{
							$post_content = array_merge($post_content,array($post_key => $value[0]));
						}
					}
				}
				if($post_content['date_type'] == 'runtime')
				{

					$post_content['date'] = date('Y-m-d H:i:s');
				}

				$post_content['post_name'] = $post_content['post_title'];
				if($postmeta['scrape_task_type'][0] == 'single')
				{
					$dom = $this->get_html_element($postmeta['scrape_post_url'][0]);
					$domxpath = new DOMXPath($dom);
					$result = $this->add_post($post_content,$domxpath);

				 	array_push($featured_image,$result['featured_image'][0]);
				 	array_push($postid_array,$result['id']);
				}

				else if($postmeta['scrape_task_type'][0] == 'list')
				{
					$dom = $this->get_html_element($postmeta['scrape_post_url'][0]);
					$postvalue = $postmeta['scrape_post_value'][0];

					$urlhelp = $this->get_url_element($postvalue);

					$element = $this->get_url_dom_element($dom,$urlhelp);
				

					foreach($element as $elements)
					{
						$f_image = array();
						$url = '';
						$template = array();
						while($elements->tagName != 'a' && $elements->tagName != 'body')
						{
							$elements = $elements->parentNode;
						}
						$url = $elements->getAttribute('href');
						if(!$url)
						{
							continue;
						}
						$dom_url = $this->get_html_element($url);
						$domxpath = new DOMXPath($dom_url);
						$result = $this->add_post($post_content,$domxpath);					
						array_push($postid_array,$result['id']);

						if(count($result['featured_image']) > 0)
						{
							array_push($featured_image,$result['featured_image'][0]);
						}
						if(count($postid_array) >= 6)
						{
							break;
						}
					}
				}
				else if($postmeta['scrape_task_type'][0] == 'feed')
				{
					set_time_limit(40);
					$addresses = $this->get_feeds($postmeta['scrape_post_url'][0],$post_content);
					foreach($addresses as $address)
					{
						$dom = $this->get_html_element($address['address']);
						$domxpath = new DOMXPath($dom);
						$result = $this->add_post($post_content,$domxpath,$address);
					 	array_push($featured_image,$result['featured_image'][0]);
					 	array_push($postid_array,$result['id']);
					 	if(count($postid_array) > 6)
					 	{
					 		break;
					 	}
					}
				}

				foreach($featured_image as $key => $value)
				{
				
					$this->setfeaturedimage($value['url'],$value['id'],$value['enable']);
				}
				update_post_meta($postid,'postid',$postid_array);
				update_post_meta($postid,'scrape_work_status','running');
				$date = date('Y-m-d H:i:s');
				update_post_meta($postid,'scrape_run_time',$date);	
				
			}
			else if($action == 'pause')
			{
				$id = get_post_meta($postid,'postid');
				
				foreach($id[0] as $ids)
				{
					wp_delete_post($ids,true);	
				}
				
				update_post_meta($postid,'postid','');
				update_post_meta($postid,'scrape_work_status','waiting');
				$date = date('Y-m-d H:i:s');
				update_post_meta($postid,'scrape_end_time',$date);
				update_post_meta($postid,'scrape_start_time',get_post_meta($postid,'scrape_run_time')[0]);
				$count = get_post_meta($postid,'scrape_run_count')[0];
				$count ++;
				update_post_meta($postid,'scrape_run_count',$count);
			}
			else if($action == 'copy')
			{
				$post = get_post($postid);
				$post_array = $post->to_array();
				$post_array['ID'] = '';
				$post_id = wp_insert_post($post_array);
				$postmeta = get_post_meta($postid);

				foreach($postmeta as $key => $value)
				{
					update_post_meta($post_id,$key,$value[0]);
				}
				update_post_meta($post_id,'scrape_work_status','waiting');
				update_post_meta($post_id,'scrape_run_count',0);
				update_post_meta($post_id,'scrape_start_time','');
				update_post_meta($post_id,'scrape_end_time','');
				update_post_meta($post_id,'postid','');
			}
			wp_redirect(add_query_arg('post_type', 'ns_scrape', admin_url('/edit.php')));	
			exit;
		}

	}

	public function get_content($string_html)
	{
		$html_str = mb_convert_encoding($string_html, 'HTML-ENTITIES', 'UTF-8');
		$document= new DomDocument();
		$document->preserveWhiteSpace = false;
		$document->loadHTML('<?xml encoding="utf-8"?><div>' . $html_str . '</div>');
		$document->removeChild($document->doctype);
		$document->removeChild($document->firstChild);
		$document->replaceChild($document->firstChild->firstChild->firstChild, $document->firstChild);
		return $document->saveHTML();
	}

	public function getposttags($tag,$seps)
	{
		$tag_array = array();
		if($seps)
		{
			$tag_string = strtok($tag,$seps);
			while($tag_string)
			{
				array_push($tag_array,$tag_string);
				strtok($seps);
			}
		}
		else
		{
			array_push($tag_array,$tag);
		}
		return $tag_array;
	}

	public function get_content_auto($content)
	{
		$content_array = array();
		$content = substr($content, 2,strlen($content)-2);
		$content_string = strtok($content,'/');
		while($content_string)
		{
			array_push($content_array,$content_string);
			$content_string = strtok('/');
		}
		$content_string = '';
		for($i=0;$i<count($content_array)-2;$i++)
		{
			$content_string .= $content_array[$i] . '/';
		}

		$content_string = '//'.substr($content_string,0,strlen($content_string)-1);
		return $content_string;
	}

	public function get_header_from_dom($dom)
	{
		$nodes = $dom->getElementsByTagName('head');
		foreach($nodes as $node)
		{
			return $this->get_html_from_node($node);	
		}
		
	}

	private function get_html_from_node($node){
	  $html = '';
	  $children = $node->childNodes;

	  foreach ($children as $child) {
	    $tmp_doc = new DOMDocument();
	    $tmp_doc->appendChild($tmp_doc->importNode($child,true));
	    $html .= $tmp_doc->saveHTML();
	  } 
	  return $html;
	}

	public function savepost($postid,$postobject)
	{
		if($_POST['post_type'] == 'ns_scrape' && !empty($_POST['data']) && $_GET['wp-post-new-reload'])
		{
			$_GET['wp-post-new-reload'] = false;
			$data = $_POST['data'];
			$post = $postobject->to_array();
			$array_key = array();
			foreach($post as $key => $posts)
			{
				if(isset($data[$key]))
				{
					$post[$key] = $data[$key];
					array_push($array_key,$key);
				}
			}
			$post['post_name'] = $data['post_title'];
			$post['post_status'] = "publish";
			wp_update_post($post,false);
			update_post_meta($postid,'scrape_work_status','waiting');
			update_post_meta($postid,'scrape_run_count',0);
			update_post_meta($postid,'scrape_start_time','');
			update_post_meta($postid,'scrape_end_time','');
			update_post_meta($postid,'scrape_post_id',$postid);
			foreach($data as $key => $post_data)
			{
				if(array_search($key,$array_key) > 0)
				{
					continue;
				}
				update_post_meta($postid,$key,$post_data);
				
			}
			$wp_nonce = wp_create_nonce('scrape_action');
			$url = admin_url('edit.php?post_type=ns_scrape&action=play&post_id='.$postid.'&_wpnonce='.$wp_nonce);
			echo json_encode(array('url'=>$url));
			exit;	
		}
	}

	public function ajax_post_type()
	{
		$args = array('public' => true);
		$array_post = array();
		foreach(get_post_types($args,'name') as $post_types)
		{
			array_push($array_post,$post_types->name);
		}
		echo json_encode($array_post);
	}

	public function ajax_post_category()
	{
		if(isset($_POST['post_name']))
		{
			$post = get_object_taxonomies($_POST['post_name'],'objects');
			$post_array = array();
			foreach($post as $key => $value)
			{
				array_push($post_array,$value->name);
			}
			echo json_encode($post_array);
		}
	}

	public function get_html_element($address,$feeds= Null)
	{
		update_site_option('scrape_user_agent', $_SERVER['HTTP_USER_AGENT']);
			
			$args = array(
				'sslverify' => false,
				'timeout' => 60,
				'user-agent' => get_site_option('scrape_user_agent'),
                'httpversion' => '1.1',
                'headers' => array('Connection' => 'keep-alive')
			);
			
			if (isset($_GET['cookie_names'])) {
				$args['cookies'] = array_combine(array_values($_GET['cookie_names']), array_values($_GET['cookie_values']));
			}
			$article = '';
			if(isset($feeds))
			{
				$reader = new Reader;
				try{
					$resource = $reader->download($address);	
				}
				catch(Exception $e)
				{
					return;
				}
	            $parser = $reader->getParser(
	                $resource->getUrl(),
	                $resource->getContent(),
	                $resource->getEncoding()
	            );


            	$feed = $parser->execute();
				            	
            	foreach ($feed->items as $item) {
	                $article = $item->getUrl();
            	}
			}
			$body_address = '';
			if(!$article)
			{
				$body_address = $address;
			}
			else
			{
				$body_address = $article;
			}

			$request = wp_remote_get($body_address, $args);
				if (is_wp_error($request)) {
					wp_die($request->get_error_message());
				}
				$body = wp_remote_retrieve_body($request);

				$body = trim($body);
				if (substr($body, 0, 3) == pack("CCC", 0xef, 0xbb, 0xbf)) {
					$body = substr($body, 3);
				}

				$dom = new DOMDocument();
	            $dom->preserveWhiteSpace = false;

				//$charset = "";
				
				//$body = iconv($charset, "UTF-8//IGNORE", $body);

				if ($body === false) {
					wp_die("utf-8 convert error");
				}
				$body = preg_replace(
					array(
					"'<\s*script[^>]*[^/]>(.*?)<\s*/\s*script\s*>'is",
					"'<\s*script\s*>(.*?)<\s*/\s*script\s*>'is",
					"'<\s*noscript[^>]*[^/]>(.*?)<\s*/\s*noscript\s*>'is",
					"'<\s*noscript\s*>(.*?)<\s*/\s*noscript\s*>'is"
					), array(
					"",
					"",
					"",
					""
					), $body);

				$body = mb_convert_encoding($body, 'HTML-ENTITIES', 'UTF-8');
				@$dom->loadHTML('<?xml encoding="utf-8" ?>' . $body);
				$url = parse_url($address);
				$url = $url['scheme'] . "://" . $url['host'];
				$head = $dom->getElementsByTagName('head')->item(0);
				$base = $dom->getElementsByTagName('base')->item(0);
				$html_base_url = null;
				if (!is_null($base)) {
					$html_base_url = $this->create_absolute_url($base->getAttribute('href'), $url, null);
				}


			$imgs = $dom->getElementsByTagName('img');
			if ($imgs->length) {
				foreach ($imgs as $item) {
					$item->setAttribute('src', $this->create_absolute_url(
							trim($item->getAttribute('src')), $address, $html_base_url
					));
				}
			}

			$as = $dom->getElementsByTagName('a');
			if ($as->length) {
				foreach ($as as $item) {
					$item->setAttribute('href', $this->create_absolute_url(
							trim($item->getAttribute('href')), $address, $html_base_url
					));
				}
			}

            $links = $dom->getElementsByTagName('link');
            if ($links->length) {
                foreach ($links as $item) {
                    $item->setAttribute('href', $this->create_absolute_url(
                        trim($item->getAttribute('href')), $address, $html_base_url
                    ));
                }
            }

			$all_elements = $dom->getElementsByTagName('*');
			foreach ($all_elements as $item) {
				if ($item->hasAttributes()) {
					foreach ($item->attributes as $name => $attr_node) {
						if (preg_match("/^on\w+$/", $name)) {
							$item->removeAttribute($name);
						}
					}
				}
			}


			return $dom;
	}

	public function get_feeds($address,$postcontent)
	{
		$reader = new Reader;
		try{
			$resource = $reader->download($address);	
		}
		catch(Exception $e)
		{
			return;
		}
        $parser = $reader->getParser(
            $resource->getUrl(),
            $resource->getContent(),
            $resource->getEncoding()
        );


    	$feed = $parser->execute();
		 
		 $addresses = array();
		 $address_array = array();
    	foreach ($feed->items as $item) {
            $address =  $item->getUrl();
             $address_array = array('content'=>$item->getContent(),'featured_image'=>$item->getEnclosureUrl(),'title'=>$item->getTitle(),'date'=>$item->getDate(),'address'=>$item->getUrl());
            array_push($addresses,$address_array);
    	}
    	return $addresses;
	}

	public function get_html_charset($header,$body)
	{
		$charset = preg_match("/<meta(?!\s*(?:name|value)\s*=)(?:[^>]*?content\s*=[\s\"']*)?([^>]*?)[\s\"';]*charset\s*=[\s\"']*([^\s\"'\/>]*)[\s\"']*\/?>/i", $body, $matches);

	}
	private function grabContent($url, $downloader = '')
    {
        if (!function_exists('file_get_html')) {
            require_once(plugin_dir_path(__FILE__).'/libs/simplehtml/simple_html_dom.php');
        }


        $parts = parse_url($url);
        $domain = $parts['scheme'].'://'.$parts['host'];

        if (isset($parts['port']) && $parts['port'] && ($parts['port'] != '80')) {
            $domain .= ':'.$parts['port'];
        }
        // Relative path URL
        $relativeUrl = $domain;
        if (isset($parts['path']) && $parts['path']) {
            $pathParts = explode('/', $parts['path']);
            if (count($pathParts)) {
                unset($pathParts[count($pathParts)-1]);
                $relativeUrl = $domain.'/'.implode('/',$pathParts);
            }
        }
       
        $args = array(
				'sslverify' => false,
				'timeout' => 60,
				'user-agent' => get_site_option('scrape_user_agent'),
                'httpversion' => '1.1',
                'headers' => array('Connection' => 'keep-alive')
			);
        $html = wp_remote_get($url,$args);
        return $html;
        // if (!$html || !is_object($html)) {
        //     return 'Error loading HTML';
        // }

        // // Remove all script tags
        // foreach($html->find('script') as $element) {
        //     $element->outertext = '';
        // }

        // // Remove meta
        // foreach($html->find('meta[http-equiv*=refresh]') as $meta) {
        //     $meta->outertext = '';
        // }

        // // Remove meta x-frame
        // foreach($html->find('meta[http-equiv*=x-frame-options]') as $meta) {
        //     $meta->outertext = '';
        // }

        // // Modify image and CSS URL's adding domain name if needed
        // foreach($html->find('img') as $element) {
        //     $src = trim($element->src);
        //     if (strlen($src)>2 && (substr($src, 0, 1) == '/') && ((substr($src, 0, 2) != '//'))) {
        //         $src = $domain . $src;
        //     } elseif (substr($src, 0, 2) == '//') {
        //         $src = 'http:'.$src;
        //     } elseif (substr($src, 0, 4) != 'http') {
        //         $src = $relativeUrl .'/'.$src;
        //     }
        //     if (strpos($downloader, '?')) {
        //         $element->src = $downloader.'&url='.base64_encode($src);
        //     } else {
        //         $element->src = $downloader.'?url='.base64_encode($src);
        //     }
        // }

        // // Replace all styles URL’s
        // foreach($html->find('link') as $element) {
        //     $src = trim($element->href);
        //     if (strlen($src)>2 && (substr($src, 0, 1) == '/') && ((substr($src, 0, 2) != '//'))) {
        //         $src = $domain.$src;
        //     } elseif ((substr($src, 0, 4) != 'http') && (substr($src, 0, 2) != '//')) {
        //         $src = $relativeUrl .'/'.$src;
        //     }
        //     $element->href = $src;
        // }

        // // Append our JavaScript and CSS
        // //$head = $html->find("head", 0);
        // $scripts = '<script type="text/javascript" src="'.rssap_plugin_url( 'admin/js/jquery.js' ).'"></script>';
        // $scripts .= '<script type="text/javascript" src="'.rssap_plugin_url( 'admin/js/extractor.js' ).'?'.time().'"></script>';
        // $scripts .= '<link rel="stylesheet" type="text/css" href="'.rssap_plugin_url( 'admin/css/extractor.css' ).'">';

        // //$head->innertext .= $scripts;

        // $html = str_replace('</body>', $scripts.'</body>', $html);
        // return $html;
    }

	public function ajax_url_load() {
		if (isset($_GET['address'])) {
			echo $this->get_html_element($_GET['address'],$_GET['feed'])->saveHTML();
			wp_die();
		}
	}


    public function create_absolute_url($rel, $base, $html_base) {
        $rel = trim($rel);
		if (substr($rel, 0, 11) == 'data:image/') {
			return $rel;
		}

		if (!empty($html_base)) {
			$base = $html_base;
		}
		return WP_Http::make_absolute_url($rel, $base);
	}

	

}