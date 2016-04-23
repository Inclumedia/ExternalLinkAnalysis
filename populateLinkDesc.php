<?php

 /**
  * To the extent possible under law,  I, Mark Hershberger, have waived all copyright and
  * related or neighboring rights to Hello World. This work is published from the
  * United States.
  *
  * @copyright CC0 http://creativecommons.org/publicdomain/zero/1.0/
  * @author Mark A. Hershberger <mah@everybody.org>
  * @ingroup Maintenance
  */

require_once "../../maintenance/Maintenance.php";

class PopulateLinkDesc extends Maintenance {
    public function execute() {
	$dbr = wfGetDB( DB_SLAVE );
	$dbw = wfGetDB( DB_MASTER );
	echo "Adding link_desc rows for externallinks rows lacking them...\n";
	// TODO: Replace this with a DISTINCT query
	$res = $dbr->select( 'externallinks', 'el_to', '1=1' );
	$results = array();
	foreach ( $res as $row ) {
	    if ( !in_array( $row->el_to, $results ) ) {
		$results[] = $row->el_to;
	    }
	}
	foreach ( $results as $result ) {
	    $res = $dbr->selectRow( 'link_desc', 'ld_url', array( 'ld_url' => $result ) );
	    if ( !$res ) {
		echo "	$result ...\n";
		$dbw->insert( 'link_desc', array( 'ld_url' => $result ) );
	    }
	}
	echo "Adding descriptions for link_desc rows lacking them...\n";
	$res = $dbr->select(
	    'link_desc',
	    array( 'ld_id', 'ld_url', 'ld_desc' ),
	    array( 'ld_desc' => NULL )
	);
	foreach ( $res as $row ) {
	    $doc = new DOMDocument();
	    @$doc->loadHTMLFile( $row->ld_url );
	    $xpath = new DOMXPath($doc);
	    echo "	" . $row->ld_url . "\n";
	    if ( $xpath->query('//title')->item(0) ) {
		$title = $xpath->query('//title')->item(0)->nodeValue;
		echo "		$title\n";
		$dbw->update (
		    'link_desc',
		    array( 'ld_desc' => $title ),
		    array( 'ld_id' => $row->ld_id )
		);
	    } else {
		echo "		Unable to retrieve\n";
	    }
	}
	/*$res = $dbr->select(
	    array( 'externallinks', 'link_desc' ),
	    array( 'el_to', 'ld_id', 'ld_desc' ),
	    array( 'el_to' => 'ld_url', 'ld_desc' => NULL )
	);
	foreach( $res as $row ) {
	}*/
    }
}

$maintClass = 'PopulateLinkDesc';
require_once RUN_MAINTENANCE_IF_MAIN;