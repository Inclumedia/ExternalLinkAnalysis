<?php
class SpecialExternalLinkAnalysis extends SpecialPage {
	function __construct() {
		parent::__construct( 'ExternalLinkAnalysis' );
	}

	function execute( $par ) {
		$request = $this->getRequest();
		$output = $this->getOutput();
		$this->setHeaders();

		# Get request data from, e.g.
		$param = $request->getText( 'param' );

		# Do stuff
		# ...
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select(
			array( 'externallinks', 'link_desc', 'page' ),
			array( 'el_to', 'ld_desc', 'page_namespace', 'page_title' ),
			array( 'ld_desc IS NOT NULL', 'el_to=ld_url', 'el_from=page_id' )
		);
		$results = array();
		foreach ( $res as $row ) {
			$host = parse_url( $row->el_to, PHP_URL_HOST );
			#echo $host;
			#die();
			if ( !isset( $results[$host] ) ) {
				$results[$host] = array ();
			}
			if ( !isset( $results[$host][$row->el_to] ) ) {
				$results[$host][$row->el_to] = array();
			}
			if ( !isset( $results[$host][$row->el_to]['pages'] ) ) {
				$results[$host][$row->el_to]['pages'] = array();
			}
			$results[$host][$row->el_to]['description'] = $row->ld_desc;
			$pageName = '';
			if ( $row->page_namespace ) {
				$pageName = "{{ns:" . $row->page_namespace . "}}:";
			}
			$pageName .= $row->page_title;
			$results[$host][$row->el_to]['pages'][] = $pageName;
		}
		$wikitext = '';
		foreach ( $results as $resultKey => $resultValue ) {
			$wikitext .= "==" . $resultKey . "==\n";
			foreach ( $resultValue as $resultValueKey => $resultValueValue ) {
				$wikitext .= "===[" . $resultValueKey . ' '
					. $resultValueValue['description'] . " <small>($resultValueKey)</small>" . "]===\n";
			}
			$firstOne = true;
			foreach ( $resultValueValue['pages'] as $page ) {
				if ( !$firstOne ) {
					$wikitext .= ", ";
				}
				$wikitext .= '[[' . str_replace( '_', ' ', $page ) . ']]';
				$firstOne = false;
			}
			$wikitext .= "\n";
		}
		$output->addWikiText( $wikitext );
	}
}