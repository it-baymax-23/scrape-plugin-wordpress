<?php 

	if(!defined('ABSPATH'))
	{
		exit;
	}

	class Readable
	{

		
		public $class_readable  = '/description/i';
		public $class_unreadable = '/combx|comment|community|disqus|extra|foot|header|menu|remark|rss|shoutbox|sidebar|sponsor|ad-break|agegate|pagination|pager|popup|top/i';
		public $title;
		public $content;
		public $document;

		public function __construct($html)
		{
			$html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8");
			try{
				$this->document = new DomDocument();
				$this->document->preserveWhiteSpace = false;
				@$this->document->loadHTML($html);
			}
			catch(Exception $e)
			{	
				var_dump($e->getMessage());exit;
			}
		}

		public function init()
		{
			if(!isset($this->document->documentElement))
			{
				return false;
			}
			$this->remove('script');
			$this->remove('link');
			$this->remove('head');
			$this->remove('style');

			$body = $this->document->getElementsByTagName('body')->item(0);
			$this->title = $this->getTitle($body);
			$this->content = $this->getcontent($body);
			if($this->content)
			{
				return true;
			}
		}

		public function remove($tag)
		{
			$elements = $this->document->documentElement->getElementsByTagName($tag);

			foreach($elements as $element)
			{
				$element->parentNode->removeChild($element);
			}
		}

		public function getDetail()
		{
			return $this->content;
		}

		public function getTitle($body)
		{
			
			return "aaa";
		}

		public function getcontent($body)
		{
			$allelement = $this->document->getElementsByTagName('*');

			$score_element = array();
			
			foreach($i = 0;($node = $allelement->item($i));$i++)
			{
				$tagName = $node->tagName;
				
				$tag_match = $node->getAttribute('class') . $node->getAttribute('id');

				if(preg_match($this->class_readable,$tag_match) && !preg_match($this->class_unreadable,$tag_match) && $tagName = 'BODY')
				{
					array_push($score_element,$node);
					$node->parentNode->removeChild($node);
					$i--;
				}

			}

			$tmp_doc = new DOMDocument();
			foreach($score_element as $scores)
			{
				$tmp_doc->appendChild($tmp_doc->importNode($scores,true));
			}

			var_dump($tmp_doc->saveHTML());exit;
			return $tmp_doc->saveHTML();
		}

		public function getContentHTML($element)
		{
				$html = '';
			  $children = $element->childNodes;

			  foreach ($children as $child) {	
		  		$tmp_doc = new DOMDocument();
		    	$tmp_doc->appendChild($tmp_doc->importNode($child,true));
		    	$html .= $tmp_doc->saveHTML();		 
			}
		 	return $html;
		}
	}
?>