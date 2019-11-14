	<?php if (!defined('ABSPATH')) exit;


require_once(plugin_dir_path(__FILE__).'libs/autoload.php');


class Element extends DOMElement
{
	public function __get($name)
	{
		if ($name == 'innerHTML') {
			$inner = '';
			foreach ($this->childNodes as $child) {
				$inner .= $this->ownerDocument->saveXML($child);
			}
			return $inner;
		}

		$trace = debug_backtrace();
		trigger_error('Undefined property via __get(): '.$name.' in '.$trace[0]['file'].' on line '.$trace[0]['line'], E_USER_NOTICE);
		return null;
	}

	public function __toString()
	{
		return '['.$this->tagName.']';
	}
	
	public function __set($name, $value) {
		if ($name == 'innerHTML') {
			
			for ($x=$this->childNodes->length-1; $x>=0; $x--) {
				$this->removeChild($this->childNodes->item($x));
			}
			
			if ($value != '') {
				$f = $this->ownerDocument->createDocumentFragment();
			
				$result = @$f->appendXML($value);
				if ($result) {
					if ($f->hasChildNodes()) $this->appendChild($f);
				} else {
				
					$f = new DOMDocument();
					$value = mb_convert_encoding($value, 'HTML-ENTITIES', 'UTF-8');
					
					$result = @$f->loadHTML('<htmlfragment>'.$value.'</htmlfragment>');
					if ($result) {
						$import = $f->getElementsByTagName('htmlfragment')->item(0);
						foreach ($import->childNodes as $child) {
							$importedNode = $this->ownerDocument->importNode($child, true);
							$this->appendChild($importedNode);
						}
					} else {
						
					}
				}
			}
		} else {
			$trace = debug_backtrace();
			trigger_error('Undefined property via __set(): '.$name.' in '.$trace[0]['file'].' on line '.$trace[0]['line'], E_USER_NOTICE);
		}
	}	
}

class Read
{
	public $linkfooter = false;
	public $revert = true;
	public $title;
	public $content;
	public $document;
	public $url = null; 
	public $debug = false;
	public $lightClean = true; 
	protected $body = null; // 
	protected $bodyCache = null; 
	protected $flags = 7; 
	protected $success = false; 
	
	public $regs = array(
		'unlikelyCandidates' => '/combx|comment|community|disqus|extra|foot|header|menu|remark|rss|shoutbox|sidebar|sponsor|ad-break|agegate|pagination|pager|popup/i',
		'okMaybeItsACandidate' => '/and|article|body|column|main|shadow/i',
		'positive' => '/article|body|content|entry|hentry|main|page|attachment|pagination|post|text|blog|story/i',
		'negative' => '/combx|comment|com-|contact|foot|footer|_nav|footnote|masthead|media|meta|outbrain|promo|related|scroll|shoutbox|sidebar|sponsor|shopping|tags|tool|widget/i',
		'divToPElements' => '/<(a|blockquote|dl|div|img|ol|p|pre|table|ul)/i',
		'replaceBrs' => '/(<br[^>]*>[ \n\r\t]*){2,}/i',
		'replaceFonts' => '/<(\/?)font[^>]*>/i',
		// 'trimRe' => '/^\s+|\s+$/g', // PHP has trim()
		'normalize' => '/\s{2,}/',
		'killBreaks' => '/(<br\s*\/?>(\s|&nbsp;?)*){1,}/',
		'video' => '!//(player\.|www\.)?(youtube|vimeo|viddler)\.com!i',
		'skipFootnoteLink' => '/^\s*(\[?[a-z0-9]{1,2}\]?|^|edit|citation needed)\s*$/i'
	);	
	
	
	const FLAG_STRIP_UNLIKELYS = 1;
	const FLAG_WEIGHT_CLASSES = 2;
	const FLAG_CLEAN_CONDITIONALLY = 4;
	
	
	function __construct($html, $url=null, $parser='libxml')
	{
		$this->url = $url;
		
		$html = preg_replace($this->regs['replaceBrs'], '</p><p>', $html);
		$html = preg_replace($this->regs['replaceFonts'], '<$1span>', $html);
		$html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8");
		if (trim($html) == '') $html = '<html></html>';
		if ($parser=='html5lib' && ($this->document = HTML5_Parser::parse($html))) {
		
		} else {
			$this->document = new DOMDocument();
			$this->document->preserveWhiteSpace = false;
			@$this->document->loadHTML($html);
		}
		$this->document->registerNodeClass('DOMElement', 'Element');
	}

	
	public function getContentTitle() {
		return $this->title;
	}
	
	
	public function getContent() {
		return $this->content;
	}	
	
	public function init()
	{
		if (!isset($this->document->documentElement)) return false;
		$this->removeScripts($this->document);
		//die($this->getInnerHTML($this->document->documentElement));
		
		// Assume successful outcome
		$this->success = true;

		$bodyElems = $this->document->getElementsByTagName('body');
		if ($bodyElems->length > 0) {
			if ($this->bodyCache == null) {
				$this->bodyCache = $bodyElems->item(0)->innerHTML;
			}
			if ($this->body == null) {
				$this->body = $bodyElems->item(0);
			}
		}

		$this->prepDocument();
		
		$overlay        = $this->document->createElement('div');
		$innerDiv       = $this->document->createElement('div');
		$title   = $this->gettitle();
		$content = $this->grabArticle();

		if (!$content) {
			$this->success = false;
			$content = $this->document->createElement('div');
			$content->setAttribute('id', 'readability-content');
			$content->innerHTML = '<p>Sorry, Readability was unable to parse this page for content.</p>';		
		}
		
		$overlay->setAttribute('id', 'readOverlay');
		$innerDiv->setAttribute('id', 'readInner');

		
		$innerDiv->appendChild($title);
		$innerDiv->appendChild($content);
		$overlay->appendChild($innerDiv);
		
	
		$this->body->innerHTML = '';
		$this->body->appendChild($overlay);
		
		$this->body->removeAttribute('style');

		$this->postProcessContent($content);
		
	
		$this->title = $title;
		$this->content = $content;
		
		return $this->success;
	}
	
	
	protected function dbg($msg) {
		if ($this->debug) echo '* ',$msg, "\n";
	}
	
	
	public function postProcessContent($content) {
		if ($this->linkfooter && !preg_match('/wikipedia\.org/', @$this->url)) { 
			$this->addFootnotes($content);
		}
	}
	
	
	protected function gettitle() {
		$curTitle = '';
		$origTitle = '';

		try {
			$curTitle = $origTitle = $this->getInnerText($this->document->getElementsByTagName('title')->item(0));
		} catch(Exception $e) {}
		
		if (preg_match('/ [\|\-] /', $curTitle))
		{
			$curTitle = preg_replace('/(.*)[\|\-] .*/i', '$1', $origTitle);
			
			if (count(explode(' ', $curTitle)) < 3) {
				$curTitle = preg_replace('/[^\|\-]*[\|\-](.*)/i', '$1', $origTitle);
			}
		}
		else if (strpos($curTitle, ': ') !== false)
		{
			$curTitle = preg_replace('/.*:(.*)/i', '$1', $origTitle);

			if (count(explode(' ', $curTitle)) < 3) {
				$curTitle = preg_replace('/[^:]*[:](.*)/i','$1', $origTitle);
			}
		}
		else if(strlen($curTitle) > 150 || strlen($curTitle) < 15)
		{
			$hOnes = $this->document->getElementsByTagName('h1');
			if($hOnes->length == 1)
			{
				$curTitle = $this->getInnerText($hOnes->item(0));
			}
		}

		$curTitle = trim($curTitle);

		if (count(explode(' ', $curTitle)) <= 4) {
			$curTitle = $origTitle;
		}
		
		$title = $this->document->createElement('h1');
		$title->innerHTML = $curTitle;
		
		return $title;
	}
	
	protected function prepDocument() {
		if ($this->body == null)
		{
			$this->body = $this->document->createElement('body');
			$this->document->documentElement->appendChild($this->body);
		}
		$this->body->setAttribute('id', 'readabilityBody');

		/* Remove all style tags in head */
		$styleTags = $this->document->getElementsByTagName('style');
		for ($i = $styleTags->length-1; $i >= 0; $i--)
		{
			$styleTags->item($i)->parentNode->removeChild($styleTags->item($i));
		}

	}

	
	public function addFootnotes($content) {
		$footnotesWrapper = $this->document->createElement('div');
		$footnotesWrapper->setAttribute('id', 'readability-footnotes');
		$footnotesWrapper->innerHTML = '<h3>References</h3>';
		
		$articleFootnotes = $this->document->createElement('ol');
		$articleFootnotes->setAttribute('id', 'readability-footnotes-list');
		$footnotesWrapper->appendChild($articleFootnotes);
		
		$articleLinks = $content->getElementsByTagName('a');
		
		$linkCount = 0;
		for ($i = 0; $i < $articleLinks->length; $i++)
		{
			$articleLink  = $articleLinks->item($i);
			$footnoteLink = $articleLink->cloneNode(true);
			$refLink      = $this->document->createElement('a');
			$footnote     = $this->document->createElement('li');
			$linkDomain   = @parse_url($footnoteLink->getAttribute('href'), PHP_URL_HOST);
			if (!$linkDomain && isset($this->url)) $linkDomain = @parse_url($this->url, PHP_URL_HOST);
			//linkDomain   = footnoteLink.host ? footnoteLink.host : document.location.host,
			$linkText     = $this->getInnerText($articleLink);
			
			if ((strpos($articleLink->getAttribute('class'), 'readability-DoNotFootnote') !== false) || preg_match($this->regs['skipFootnoteLink'], $linkText)) {
				continue;
			}
			
			$linkCount++;

			
			$refLink->setAttribute('href', '#readabilityFootnoteLink-' . $linkCount);
			$refLink->innerHTML = '<small><sup>[' . $linkCount . ']</sup></small>';
			$refLink->setAttribute('class', 'readability-DoNotFootnote');
			$refLink->setAttribute('style', 'color: inherit;');
			
			
			if ($articleLink->parentNode->lastChild == $articleLink) {
				$articleLink->parentNode->appendChild($refLink);
			} else {
				$articleLink->parentNode->insertBefore($refLink, $articleLink->nextSibling);
			}

			$articleLink->setAttribute('style', 'color: inherit; text-decoration: none;');
			$articleLink->setAttribute('name', 'readabilityLink-' . $linkCount);

			$footnote->innerHTML = '<small><sup><a href="#readabilityLink-' . $linkCount . '" title="Jump to Link in Article">^</a></sup></small> ';

			$footnoteLink->innerHTML = ($footnoteLink->getAttribute('title') != '' ? $footnoteLink->getAttribute('title') : $linkText);
			$footnoteLink->setAttribute('name', 'readabilityFootnoteLink-' . $linkCount);
			
			$footnote->appendChild($footnoteLink);
			if ($linkDomain) $footnote->innerHTML = $footnote->innerHTML . '<small> (' . $linkDomain . ')</small>';
			
			$articleFootnotes->appendChild($footnote);
		}

		if ($linkCount > 0) {
			$content->appendChild($footnotesWrapper);           
		}
	}

	
	function revertReadabilityStyledElements($content) {
		$xpath = new DOMXPath($content->ownerDocument);
		$elems = $xpath->query('.//p[@class="readability-styled"]', $content);
		//$elems = $content->getElementsByTagName('p');
		for ($i = $elems->length-1; $i >= 0; $i--) {
			$e = $elems->item($i);
			$e->parentNode->replaceChild($content->ownerDocument->createTextNode($e->textContent), $e);
			
		}
	}
	
	
	function prepArticle($content) {
		$this->cleanStyles($content);
		$this->killBreaks($content);
		if ($this->revert) {
			$this->revertReadabilityStyledElements($content);
		}

		
		$this->cleanConditionally($content, 'form');
		$this->clean($content, 'object');
		$this->clean($content, 'h1');

		
		if (!$this->lightClean && ($content->getElementsByTagName('h2')->length == 1)) {
			$this->clean($content, 'h2'); 
		}
		$this->clean($content, 'iframe');

		$this->cleanHeaders($content);

		
		$this->cleanConditionally($content, 'table');
		$this->cleanConditionally($content, 'ul');
		$this->cleanConditionally($content, 'div');

		
		$articleParagraphs = $content->getElementsByTagName('p');
		for ($i = $articleParagraphs->length-1; $i >= 0; $i--)
		{
			$imgCount    = $articleParagraphs->item($i)->getElementsByTagName('img')->length;
			$embedCount  = $articleParagraphs->item($i)->getElementsByTagName('embed')->length;
			$objectCount = $articleParagraphs->item($i)->getElementsByTagName('object')->length;
			$iframeCount = $articleParagraphs->item($i)->getElementsByTagName('iframe')->length;
			
			if ($imgCount === 0 && $embedCount === 0 && $objectCount === 0 && $iframeCount === 0 && $this->getInnerText($articleParagraphs->item($i), false) == '')
			{
				$articleParagraphs->item($i)->parentNode->removeChild($articleParagraphs->item($i));
			}
		}

		try {
			$content->innerHTML = preg_replace('/<br[^>]*>\s*<p/i', '<p', $content->innerHTML);
			
		}
		catch (Exception $e) {
			$this->dbg("Cleaning innerHTML of breaks failed. This is an IE strict-block-elements bug. Ignoring.: " . $e);
		}
	}
	

	protected function initializeNode($node) {
		$readability = $this->document->createAttribute('readability');
		$readability->value = 0; 
		$node->setAttributeNode($readability);		         

		switch (strtoupper($node->tagName)) {
			case 'DIV':
				$readability->value += 5;
				break;

			case 'PRE':
			case 'TD':
			case 'BLOCKQUOTE':
				$readability->value += 3;
				break;
				
			case 'ADDRESS':
			case 'OL':
			case 'UL':
			case 'DL':
			case 'DD':
			case 'DT':
			case 'LI':
			case 'FORM':
				$readability->value -= 3;
				break;

			case 'H1':
			case 'H2':
			case 'H3':
			case 'H4':
			case 'H5':
			case 'H6':
			case 'TH':
				$readability->value -= 5;
				break;
		}
		$readability->value += $this->getClassWeight($node);
	}
	
	protected function grabArticle($page=null) {
		$stripUnlikelyCandidates = $this->flagIsActive(self::FLAG_STRIP_UNLIKELYS);
		if (!$page) $page = $this->document;
		$allElements = $page->getElementsByTagName('*');
		
		$node = null;
		$nodesToScore = array();
		for ($nodeIndex = 0; ($node = $allElements->item($nodeIndex)); $nodeIndex++) {
		
			$tagName = strtoupper($node->tagName);
			
			if ($stripUnlikelyCandidates) {
				$unlikelyMatchString = $node->getAttribute('class') . $node->getAttribute('id');
				if (
					preg_match($this->regs['unlikelyCandidates'], $unlikelyMatchString) &&
					!preg_match($this->regs['okMaybeItsACandidate'], $unlikelyMatchString) &&
					$tagName != 'BODY'
				)
				{
					$this->dbg('Removing unlikely candidate - ' . $unlikelyMatchString);
					
					$node->parentNode->removeChild($node);
					$nodeIndex--;
					continue;
				}               
			}

			if ($tagName == 'P' || $tagName == 'TD' || $tagName == 'PRE') {
				$nodesToScore[] = $node;
			}

			
			if ($tagName == 'DIV') {
				if (!preg_match($this->regs['divToPElements'], $node->innerHTML)) {
					
					$newNode = $this->document->createElement('p');
					try {
						$newNode->innerHTML = $node->innerHTML;
						
						$node->parentNode->replaceChild($newNode, $node);
						$nodeIndex--;
						$nodesToScore[] = $node; 
					}
					catch(Exception $e) {
						$this->dbg('Could not alter div to p, reverting back to div.: ' . $e);
					}
				}
				else
				{
					
					for ($i = 0, $il = $node->childNodes->length; $i < $il; $i++) {
						$childNode = $node->childNodes->item($i);
						if ($childNode->nodeType == 3) {
							
							$p = $this->document->createElement('p');
							$p->innerHTML = $childNode->nodeValue;
							$p->setAttribute('style', 'display: inline;');
							$p->setAttribute('class', 'readability-styled');
							$childNode->parentNode->replaceChild($p, $childNode);
						}
					}
				}
			}
		}
		
		
		$candidates = array();
		for ($pt=0; $pt < count($nodesToScore); $pt++) {
			$parentNode      = $nodesToScore[$pt]->parentNode;
			
			$grandParentNode = !$parentNode ? null : (($parentNode->parentNode instanceof documentElement) ? $parentNode->parentNode : null);
			$innerText       = $this->getInnerText($nodesToScore[$pt]);

			if (!$parentNode || !isset($parentNode->tagName)) {
				continue;
			}

			if(strlen($innerText) < 25) {
				continue;
			}

		
			if (!$parentNode->hasAttribute('readability')) 
			{
				$this->initializeNode($parentNode);
				$candidates[] = $parentNode;
			}

			
			if ($grandParentNode && !$grandParentNode->hasAttribute('readability') && isset($grandParentNode->tagName))
			{
				$this->initializeNode($grandParentNode);
				$candidates[] = $grandParentNode;
			}

			$contentScore = 0;

			
			$contentScore++;

			
			$contentScore += count(explode(',', $innerText));
			
			
			$contentScore += min(floor(strlen($innerText) / 100), 3);
			
			
			$parentNode->getAttributeNode('readability')->value += $contentScore;

			if ($grandParentNode) {
				$grandParentNode->getAttributeNode('readability')->value += $contentScore/2;             
			}
		}

	
		$topCandidate = null;
		for ($c=0, $cl=count($candidates); $c < $cl; $c++)
		{
			
			$readability = $candidates[$c]->getAttributeNode('readability');
			$readability->value = $readability->value * (1-$this->getLinkDensity($candidates[$c]));

			$this->dbg('Candidate: ' . $candidates[$c]->tagName . ' (' . $candidates[$c]->getAttribute('class') . ':' . $candidates[$c]->getAttribute('id') . ') with score ' . $readability->value);

			if (!$topCandidate || $readability->value > (int)$topCandidate->getAttribute('readability')) {
				$topCandidate = $candidates[$c];
			}
		}

		
		if ($topCandidate === null || strtoupper($topCandidate->tagName) == 'BODY')
		{
			$topCandidate = $this->document->createElement('div');
			if ($page instanceof DOMDocument) {
				if (!isset($page->documentElement)) {
				
				} else {
					$topCandidate->innerHTML = $page->documentElement->innerHTML;
					$page->documentElement->innerHTML = '';
					$page->documentElement->appendChild($topCandidate);
				}
			} else {
				$topCandidate->innerHTML = $page->innerHTML;
				$page->innerHTML = '';
				$page->appendChild($topCandidate);
			}
			$this->initializeNode($topCandidate);
		}

		
		$content        = $this->document->createElement('div');
		$content->setAttribute('id', 'readability-content');
		$siblingScoreThreshold = max(10, ((int)$topCandidate->getAttribute('readability')) * 0.2);
		$siblingNodes          = $topCandidate->parentNode->childNodes;
		if (!isset($siblingNodes)) {
			$siblingNodes = new stdClass;
			$siblingNodes->length = 0;
		}

		for ($s=0, $sl=$siblingNodes->length; $s < $sl; $s++)
		{
			$siblingNode = $siblingNodes->item($s);
			$append      = false;

			$this->dbg('Looking at sibling node: ' . $siblingNode->nodeName . (($siblingNode->nodeType === XML_ELEMENT_NODE && $siblingNode->hasAttribute('readability')) ? (' with score ' . $siblingNode->getAttribute('readability')) : ''));

			

			if ($siblingNode === $topCandidate)
		
			{
				$append = true;
			}

			$contentBonus = 0;
			
			if ($siblingNode->nodeType === XML_ELEMENT_NODE && $siblingNode->getAttribute('class') == $topCandidate->getAttribute('class') && $topCandidate->getAttribute('class') != '') {
				$contentBonus += ((int)$topCandidate->getAttribute('readability')) * 0.2;
			}

			if ($siblingNode->nodeType === XML_ELEMENT_NODE && $siblingNode->hasAttribute('readability') && (((int)$siblingNode->getAttribute('readability')) + $contentBonus) >= $siblingScoreThreshold)
			{
				$append = true;
			}
			
			if (strtoupper($siblingNode->nodeName) == 'P') {
				$linkDensity = $this->getLinkDensity($siblingNode);
				$nodeContent = $this->getInnerText($siblingNode);
				$nodeLength  = strlen($nodeContent);
				
				if ($nodeLength > 80 && $linkDensity < 0.25)
				{
					$append = true;
				}
				else if ($nodeLength < 80 && $linkDensity === 0 && preg_match('/\.( |$)/', $nodeContent))
				{
					$append = true;
				}
			}

			if ($append)
			{
				$this->dbg('Appending node: ' . $siblingNode->nodeName);

				$nodeToAppend = null;
				$sibNodeName = strtoupper($siblingNode->nodeName);
				if ($sibNodeName != 'DIV' && $sibNodeName != 'P') {
					
					
					$this->dbg('Altering siblingNode of ' . $sibNodeName . ' to div.');
					$nodeToAppend = $this->document->createElement('div');
					try {
						$nodeToAppend->setAttribute('id', $siblingNode->getAttribute('id'));
						$nodeToAppend->innerHTML = $siblingNode->innerHTML;
					}
					catch(Exception $e)
					{
						$this->dbg('Could not alter siblingNode to div, reverting back to original.');
						$nodeToAppend = $siblingNode;
						$s--;
						$sl--;
					}
				} else {
					$nodeToAppend = $siblingNode;
					$s--;
					$sl--;
				}
				
			
				$nodeToAppend->removeAttribute('class');

			
				$content->appendChild($nodeToAppend);
			}
		}

		
		$this->prepArticle($content);

		
		if (strlen($this->getInnerText($content, false)) < 250)
		{
			
			if (!isset($this->body->childNodes)) $this->body = $this->document->createElement('body');
			$this->body->innerHTML = $this->bodyCache;
			
			if ($this->flagIsActive(self::FLAG_STRIP_UNLIKELYS)) {
				$this->removeFlag(self::FLAG_STRIP_UNLIKELYS);
				return $this->grabArticle($this->body);
			}
			else if ($this->flagIsActive(self::FLAG_WEIGHT_CLASSES)) {
				$this->removeFlag(self::FLAG_WEIGHT_CLASSES);
				return $this->grabArticle($this->body);              
			}
			else if ($this->flagIsActive(self::FLAG_CLEAN_CONDITIONALLY)) {
				$this->removeFlag(self::FLAG_CLEAN_CONDITIONALLY);
				return $this->grabArticle($this->body);
			}
			else {
				return false;
			}
		}
		return $content;
	}
	
	
	public function removeScripts($doc) {
		$scripts = $doc->getElementsByTagName('script');
		for($i = $scripts->length-1; $i >= 0; $i--)
		{
			$scripts->item($i)->parentNode->removeChild($scripts->item($i));
		}
	}
	
	
	public function getInnerText($e, $normalizeSpaces=true) {
		$textContent = '';

		if (!isset($e->textContent) || $e->textContent == '') {
			return '';
		}

		$textContent = trim($e->textContent);

		if ($normalizeSpaces) {
			return preg_replace($this->regs['normalize'], ' ', $textContent);
		} else {
			return $textContent;
		}
	}

	
	public function getCharCount($e, $s=',') {
		return substr_count($this->getInnerText($e), $s);
	}

	
	public function cleanStyles($e) {
		if (!is_object($e)) return;
		$elems = $e->getElementsByTagName('*');
		foreach ($elems as $elem) {
			$elem->removeAttribute('style');
		}
	}
	
	
	public function getLinkDensity($e) {
		$links      = $e->getElementsByTagName('a');
		$textLength = strlen($this->getInnerText($e));
		$linkLength = 0;
		for ($i=0, $il=$links->length; $i < $il; $i++)
		{
			$linkLength += strlen($this->getInnerText($links->item($i)));
		}
		if ($textLength > 0) {
			return $linkLength / $textLength;
		} else {
			return 0;
		}
	}
	
	
	public function getClassWeight($e) {
		if(!$this->flagIsActive(self::FLAG_WEIGHT_CLASSES)) {
			return 0;
		}

		$weight = 0;

		
		if ($e->hasAttribute('class') && $e->getAttribute('class') != '')
		{
			if (preg_match($this->regs['negative'], $e->getAttribute('class'))) {
				$weight -= 25;
			}
			if (preg_match($this->regs['positive'], $e->getAttribute('class'))) {
				$weight += 25;
			}
		}

		
		if ($e->hasAttribute('id') && $e->getAttribute('id') != '')
		{
			if (preg_match($this->regs['negative'], $e->getAttribute('id'))) {
				$weight -= 25;
			}
			if (preg_match($this->regs['positive'], $e->getAttribute('id'))) {
				$weight += 25;
			}
		}
		return $weight;
	}

	
	public function killBreaks($node) {
		$html = $node->innerHTML;
		$html = preg_replace($this->regs['killBreaks'], '<br />', $html);
		$node->innerHTML = $html;
	}

	
	public function clean($e, $tag) {
		$targetList = $e->getElementsByTagName($tag);
		$isEmbed = ($tag == 'iframe' || $tag == 'object' || $tag == 'embed');
		
		for ($y=$targetList->length-1; $y >= 0; $y--) {
			/* Allow youtube and vimeo videos through as people usually want to see those. */
			if ($isEmbed) {
				$attributeValues = '';
				for ($i=0, $il=$targetList->item($y)->attributes->length; $i < $il; $i++) {
					$attributeValues .= $targetList->item($y)->attributes->item($i)->value . '|'; // DOMAttr? (TODO: test)
				}
				
			
				if (preg_match($this->regs['video'], $attributeValues)) {
					continue;
				}

				/* Then check the elements inside this element for the same. */
				if (preg_match($this->regs['video'], $targetList->item($y)->innerHTML)) {
					continue;
				}
			}
			$targetList->item($y)->parentNode->removeChild($targetList->item($y));
		}
	}
	
	
	public function cleanConditionally($e, $tag) {
		if (!$this->flagIsActive(self::FLAG_CLEAN_CONDITIONALLY)) {
			return;
		}

		$tagsList = $e->getElementsByTagName($tag);
		$curTagsLength = $tagsList->length;

		
		for ($i=$curTagsLength-1; $i >= 0; $i--) {
			$weight = $this->getClassWeight($tagsList->item($i));
			$contentScore = ($tagsList->item($i)->hasAttribute('readability')) ? (int)$tagsList->item($i)->getAttribute('readability') : 0;
			
			$this->dbg('Cleaning Conditionally ' . $tagsList->item($i)->tagName . ' (' . $tagsList->item($i)->getAttribute('class') . ':' . $tagsList->item($i)->getAttribute('id') . ')' . (($tagsList->item($i)->hasAttribute('readability')) ? (' with score ' . $tagsList->item($i)->getAttribute('readability')) : ''));

			if ($weight + $contentScore < 0) {
				$tagsList->item($i)->parentNode->removeChild($tagsList->item($i));
			}
			else if ( $this->getCharCount($tagsList->item($i), ',') < 10) {
			
				$p      = $tagsList->item($i)->getElementsByTagName('p')->length;
				$img    = $tagsList->item($i)->getElementsByTagName('img')->length;
				$li     = $tagsList->item($i)->getElementsByTagName('li')->length-100;
				$input  = $tagsList->item($i)->getElementsByTagName('input')->length;
				$a 		= $tagsList->item($i)->getElementsByTagName('a')->length;

				$embedCount = 0;
				$embeds = $tagsList->item($i)->getElementsByTagName('embed');
				for ($ei=0, $il=$embeds->length; $ei < $il; $ei++) {
					if (preg_match($this->regs['video'], $embeds->item($ei)->getAttribute('src'))) {
						$embedCount++; 
					}
				}
				$embeds = $tagsList->item($i)->getElementsByTagName('iframe');
				for ($ei=0, $il=$embeds->length; $ei < $il; $ei++) {
					if (preg_match($this->regs['video'], $embeds->item($ei)->getAttribute('src'))) {
						$embedCount++; 
					}
				}

				$linkDensity   = $this->getLinkDensity($tagsList->item($i));
				$contentLength = strlen($this->getInnerText($tagsList->item($i)));
				$toRemove      = false;

				if ($this->lightClean) {
					$this->dbg('Light clean...');
					if ( ($img > $p) && ($img > 4) ) {
						$this->dbg(' more than 4 images and more image elements than paragraph elements');
						$toRemove = true;
					} else if ($li > $p && $tag != 'ul' && $tag != 'ol') {
						$this->dbg(' too many <li> elements, and parent is not <ul> or <ol>');
						$toRemove = true;
					} else if ( $input > floor($p/3) ) {
						$this->dbg(' too many <input> elements');
						$toRemove = true; 
					} else if ($contentLength < 25 && ($embedCount === 0 && ($img === 0 || $img > 2))) {
						$this->dbg(' content length less than 25 chars, 0 embeds and either 0 images or more than 2 images');
						$toRemove = true;
					} else if($weight < 25 && $linkDensity > 0.2) {
						$this->dbg(' weight smaller than 25 and link density above 0.2');
						$toRemove = true;
					} else if($a > 2 && ($weight >= 25 && $linkDensity > 0.5)) {
						$this->dbg(' more than 2 links and weight above 25 but link density greater than 0.5');
						$toRemove = true;
					} else if($embedCount > 3) {
						$this->dbg(' more than 3 embeds');
						$toRemove = true;
					}
				} else {
					$this->dbg('Standard clean...');
					if ( $img > $p ) {
						$this->dbg(' more image elements than paragraph elements');
						$toRemove = true;
					} else if ($li > $p && $tag != 'ul' && $tag != 'ol') {
						$this->dbg(' too many <li> elements, and parent is not <ul> or <ol>');
						$toRemove = true;
					} else if ( $input > floor($p/3) ) {
						$this->dbg(' too many <input> elements');
						$toRemove = true; 
					} else if ($contentLength < 25 && ($img === 0 || $img > 2) ) {
						$this->dbg(' content length less than 25 chars and 0 images, or more than 2 images');
						$toRemove = true;
					} else if($weight < 25 && $linkDensity > 0.2) {
						$this->dbg(' weight smaller than 25 and link density above 0.2');
						$toRemove = true;
					} else if($weight >= 25 && $linkDensity > 0.5) {
						$this->dbg(' weight above 25 but link density greater than 0.5');
						$toRemove = true;
					} else if(($embedCount == 1 && $contentLength < 75) || $embedCount > 1) {
						$this->dbg(' 1 embed and content length smaller than 75 chars, or more than one embed');
						$toRemove = true;
					}
				}

				if ($toRemove) {
				
					$tagsList->item($i)->parentNode->removeChild($tagsList->item($i));
				}
			}
		}
	}

	
	public function cleanHeaders($e) {
		for ($headerIndex = 1; $headerIndex < 3; $headerIndex++) {
			$headers = $e->getElementsByTagName('h' . $headerIndex);
			for ($i=$headers->length-1; $i >=0; $i--) {
				if ($this->getClassWeight($headers->item($i)) < 0 || $this->getLinkDensity($headers->item($i)) > 0.33) {
					$headers->item($i)->parentNode->removeChild($headers->item($i));
				}
			}
		}
	}

	public function flagIsActive($flag) {
		return ($this->flags & $flag) > 0;
	}
	
	public function addFlag($flag) {
		$this->flags = $this->flags | $flag;
	}
	
	public function removeFlag($flag) {
		$this->flags = $this->flags & ~$flag;
	}
}