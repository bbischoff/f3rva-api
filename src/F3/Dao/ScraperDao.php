<?php
namespace F3\Dao;

use DOMDocument;
use DOMXPath;
use F3\Util\HttpRequest;

/**
 * DAO class for screen scraping a website.
 *
 * @author bbischoff
 */
class ScraperDao {
	private $httpRequest;

	public function __construct(HttpRequest $httpRequest) {
		$this->httpRequest = $httpRequest;
	}

	public function parsePost($url) {
		// call to get the contents of the post
		$this->httpRequest->init($url);
		$this->httpRequest->setOption(CURLOPT_HEADER, false);
		$this->httpRequest->setOption(CURLOPT_RETURNTRANSFER, true);
		$this->httpRequest->setOption(CURLOPT_BINARYTRANSFER, true);
		$this->httpRequest->setOption(CURLOPT_CONNECTTIMEOUT, 5);

		$html = $this->httpRequest->execute();
		$this->httpRequest->close();
		
		// parse the html contents to a DOM object
		$doc = new DOMDocument();
		libxml_use_internal_errors(true);
		$doc->loadHTML($html);
		$xpath = new DOMXPath($doc);
		
		// query to get the title
		$titleNode = $xpath->query("//article/header//h1[contains(@class, 'post-title')]")->item(0);
		$title = trim($titleNode->nodeValue);

		// query to get the author
		$authorNode = $xpath->query("//article/header//span[contains(@class, 'reviewer')]/a")->item(0);
		$author = trim($authorNode->textContent);
		
		// query to get the workout date
		$dateNode = $xpath->query("//ul/li/strong[text()='When:']")->item(0);
		$dateStr = trim($dateNode->nextSibling->nodeValue);
		$date = date_parse($dateStr);
		
		// query to get the Q
		$qNode = $xpath->query("//ul/li/strong[text()='QIC:']")->item(0);
		$qStr = trim($qNode->nextSibling->nodeValue);
		$split = preg_split("/,|\band\b|&/", $qStr);
		// trim values and remove empty values from the array
		$qArray = array_filter(array_map('trim', $split));
		
		// query to get the PAX
		$paxNode = $xpath->query("//ul/li/strong[text()='The PAX:']")->item(0);
		$paxStr = trim($paxNode->nextSibling->nodeValue);
		$split = preg_split("/,|\band\b|&/", $paxStr);
		// trim values and remove empty values from the array
		$paxArray = array_filter(array_map('trim', $split));
		
		// query to get the tags
		$tags = $xpath->query('//span[@class="cats tagcloud"]/a[@rel="tag"]/text()');
		$tagsArray = array();
		foreach($tags as $tagNode){
			$tagsArray[] = $tagNode->nodeValue;
		}
		
		// create an object to return;
		return (object) array(
			'author' => $author,
			'date' => $date,
			'pax' => $paxArray, 
			'q' => $qArray, 
			'tags' => $tagsArray, 
			'title' => $title
		);
	}
}
?>