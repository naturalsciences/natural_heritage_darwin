<?php
class FeedParse {
  protected $document;
  protected $filename;
  protected $feedItems = array();

  public function __construct($filename)
  {
    $this->document = new DOMDocument();
    $this->filename = $filename;
    
  }

  public function parse($limit = 5)
  {
    if(file_exists($this->filename))
    {
      $this->document->load($this->filename);
      if($this->document->firstChild->nodeName == 'rss')
        $this->parseRss($limit);
      elseif($this->document->firstChild->nodeName == 'feed')
        $this->parseAtom($limit);
    }
    return $this->feedItems;
  }

  protected function parseRss($limit)
  {
    $i=0;
    foreach ($this->document->getElementsByTagName('item') as $node)
    {
      // Stop reading the feed if limit is reached
      if($i++ >= $limit) break;

      $itemRSS = array ( 
       'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
       'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
       'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
       'date' => ($node->getElementsByTagName('pubDate')->length?strtotime($node->getElementsByTagName('pubDate')->item(0)->nodeValue):time()),
       );
      array_push($this->feedItems, $itemRSS);
    }
    return true;
  }

  protected function parseAtom($limit)
  {
    $i=0;
    foreach ($this->document->getElementsByTagName('entry') as $node)
    {
      // Stop reading the feed if limit is reached
      if($i++ >= $limit) break;

      $itemRSS = array ( 
       'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
       'desc' => $node->getElementsByTagName('summary')->item(0)->nodeValue,
       'link' => $node->getElementsByTagName('link')->item(0)->getAttribute('href'),
       'date' => ($node->getElementsByTagName('updated')->length?strtotime($node->getElementsByTagName('updated')->item(0)->nodeValue):time()),
       );
      array_push($this->feedItems, $itemRSS);
    }
    return true;
  }
}
