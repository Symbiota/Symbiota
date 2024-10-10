<?php

/**
 * RdfUtility.php
 *
 * Utility class and functions for working with RDF.
 *
 * Currently contains support for identification of response type preference
 * (turtle, rdf/xml, or html) from parsing the http Accept header.
 *
 * @author Paul J. Morris mole@morris.net
 */


/*
// Example use:

$done=FALSE;
$accept = RdfUtility::parseHTTPAcceptHeader($_SERVER['HTTP_ACCEPT']);
while (!$done && list($key, $mediarange) = each($accept)) {
    if ($mediarange=='text/turtle') {
       deliverTurtle();
       $done = TRUE;
    }
    if ($mediarange=='application/rdf+xml') {
       deliverRdfXml();
       $done = TRUE;
    }
}
if (!$done) {
   deliverHtml();
}

*/



/**
 * Function suitable for use as a value_compare_function in usort.
 * Intended to sort an exploded http Accept header on q value and position.
 * Sorts in order of q value, then position, then specificity.
 *
 * Assumes that first and second have attributes 'q', 'position', and 'mediarange'.
 * @see AcceptElement
 *
 * param: first, the element to be compared with second on q and position.
 * param: second, the element to be compared with first on q and position.
 *
 * return: 1 if first is greater than second, -1 if first is equal
 *   to second, or 0 if first is the same as second.  Note that usort
 *   treats the relative order as undefined when values are the same.
 */
function compareQ($first, $second) {
      // First, compare on q, largest q value comes first.
      $diff = $second->q - $first->q;
      if ($diff > 0)
      {
        $diff = 1;
      } else if ($diff < 0)
      {
        $diff = -1;
      } else
      {
        if (strpos($first->mediarange,';')!==false || strpos($second->mediarange,';')!==false ||
            strpos($first->mediarange,'*')!==false || strpos($second->mediarange,'*')!==false )
        {
            // Second, if q values are the same, sort by specificity, failing over to position
            $diff = substr_count($first->mediarange,'*') - substr_count($second->mediarange,'*');
            if ($diff==0)
            {
                $diff = -(substr_count($first->mediarange,';') - substr_count($second->mediarange,';'));
            }
            if ($diff==0) {
               $diff = $first->position - $second->position;
            }
        } else
        {
           // Third, if q values are the same and no wildcards or parameters are included, sort by position
           $diff = $first->position - $second->position;
        }
      }
      // make sure that response is an integer
      if ($diff <  0) { $diff = -1; }
      if ($diff >  0) { $diff =  1; }
      return $diff;
}

class AcceptElement {
   public $q;
   public $position;
   public $mediarange;
}

class RdfUtility {

   /**
    * Split an http Accept header into an ordered array of media ranges (mime types).
    * see: http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
    *
    * param: header, a string containing an http Accept header.
    *
    * return: an array of media ranges, ordered first by q value, then by position.
    */
   public static function parseHTTPAcceptHeader($header) {
      $result = array();
      $accept = array();
      // Spit the listed mime types in the header into an array
      foreach (preg_split('/\s*,\s*/', $header) as $pos => $mime) {
        $element = new AcceptElement;
        $element->position = $pos;
        // extract the media range, and if provided, the q value.
        if (preg_match(",^(\S+)\s*;\s*(?:q)=([0-9\.]+),i", $mime, $rangeparams)) {
          // a q value was specified, extract the q and mime type
          $element->mediarange = $rangeparams[1];
          $element->q = (double)$rangeparams[2];
        } else {
          // If no q value was specified, the default value is q=1.
          $element->mediarange = $mime;
          $element->q = 1;
        }
        $accept[] = $element;
      }

      // sort the array on q value and position, return the ordered list of mime types
      usort($accept, "compareQ");
      foreach ($accept as $sorted) {
        $result[$sorted->mediarange] = $sorted->mediarange;
      }
      return $result;
   }

   public static function namespaceAbbrev($namespace) {
       $result = $namespace;
       $result = str_replace("http://rs.tdwg.org/dwc/terms/","dwc:",$result);
       $result = str_replace("http://xmlns.com/foaf/0.1/","foaf:",$result);
       $result = str_replace("http://rs.tdwg.org/dwc/iri/","dwciri:",$result);
       $result = str_replace("http://purl.org/dc/elements/1.1/","dc:",$result);
       $result = str_replace("http://purl.org/dc/terms/","dcterms:",$result);
       return $result;
   }

}


// Examples
//print_r(Utility::parseHTTPAcceptHeader('text/turtle;q=1.0,text/xml,application/xml,application/xhtml+xml,text/html; q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5'));
//print_r(Utility::parseHTTPAcceptHeader('text/xml;q=0.9,application/xml;q=0.9,application/xhtml+xml;q=0.9,text/html;q=0.9,application/rdf+xml;q=1, text/plain;q=0.8,image/png,*/*;q=0.5'));
// TODO: Unit Tests for correct sort order
// Test cases, see  http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
//print_r(Utility::parseHTTPAcceptHeader('text/*, text/html, text/html;level=1, */*'));
//print_r(Utility::parseHTTPAcceptHeader('text/*;q=0.3, text/html;q=0.7, text/html;level=1, text/html;level=2;q=0.4, */*;q=0.5'));

?>