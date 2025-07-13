#!/usr/bin/env php
<?php

/*
 * Simple program that uses svg-sanitizer
 * to find issues in files specified on the
 * command line, and prints a JSON output with
 * the issues found on exit.
 */

require_once( __DIR__ . '/data/AttributeInterface.php' );
require_once( __DIR__ . '/data/TagInterface.php' );
require_once( __DIR__ . '/data/AllowedAttributes.php' );
require_once( __DIR__ . '/data/AllowedTags.php' );
require_once( __DIR__ . '/data/XPath.php' );
require_once( __DIR__ . '/ElementReference/Resolver.php' );
require_once( __DIR__ . '/ElementReference/Subject.php' );
require_once( __DIR__ . '/ElementReference/Usage.php' );
require_once( __DIR__ . '/Exceptions/NestingException.php' );
require_once( __DIR__ . '/Helper.php' );
require_once( __DIR__ . '/Sanitizer.php' );

/*
 * Print array as JSON and then
 * exit program with a particular
 * exit-code.
 */

function sysexit(
	$results,
	$status
) {
	echo json_encode(
		$results,
		JSON_PRETTY_PRINT
	);

	exit( $status );
}


/*
 * Main part begins
 */

global $argv;

/*
 * Set up results array, to
 * be printed on exit.
 */
$results = array(
	'totals' => array(
		'errors' => 0,
	),

	'files' => array(
	),
);


/*
 * Catch files to scan from $argv.
 */

$files_to_scan = $argv;
unset( $files_to_scan[0] );

$files_to_scan = array_values(
	$files_to_scan
);

/*
 * Catch no file specified.
 */

if ( empty( $files_to_scan ) ) {
	$results['totals']['errors']++;
	$results['messages'] = array(
		array( 'No files to scan specified' ),
	);

	sysexit(
		$results,
		1
	);
}

/*
 * Initialize the SVG scanner.
 *
 * Make sure to allow custom attributes,
 * and to remove remote references.
 */
$sanitizer = new enshrined\svgSanitize\Sanitizer();

$sanitizer->removeRemoteReferences( true );

/*
 * Scan each file specified to be scanned.
 */

foreach( $files_to_scan as $file_name ) {
	/*
	 * Read SVG file.
	 */
	$svg_file = @file_get_contents( $file_name );

	/*
	 * If not found, report that and continue.
	 */
	if ( false === $svg_file ) {
		$results['totals']['errors']++;

		$results['files'][ $file_name ][] = array(
			'errors' => 1,
			'messages' => array(
				array(
					'message' => 'File specified could not be read (' . $file_name . ')',
					'line' => null,
				),
			),
		);

		continue;
	}

	/*
	 * Sanitize file and get issues found.
	 */
	$sanitize_status = $sanitizer->sanitize( $svg_file );

	$xml_issues = $sanitizer->getXmlIssues();

	/*
	 * If we find no issues, simply note that.
	 */
	if ( empty( $xml_issues ) && ( false !== $sanitize_status ) ) {
		$results['files'][ $file_name ] = array(
			'errors' => 0,
			'messages' => array()
		);
	}

	/*
	 * Could not sanitize the file.
	 */
	else if (
		( '' === $sanitize_status ) ||
		( false === $sanitize_status )
	) {
		$results['totals']['errors']++;

		$results['files'][ $file_name ] = array(
			'errors' => 1,
			'messages' => array(
				array(
					'message' => 'Unable to sanitize file \'' . $file_name . '\'' ,
					'line' => null,
				)
			),
		);
	}

	/*
	 * If we find issues, note it and update statistics.
	 */

	else {
		$results['totals']['errors'] += count( $xml_issues );

		$results['files'][ $file_name ] = array(
			'errors' => count( $xml_issues ),
			'messages' => $xml_issues,
		);
	}

	unset( $svg_file );
	unset( $xml_issues );
	unset( $sanitize_status );
}


/*
 * Exit with a status
 * that reflects what issues
 * we found.
 */
sysexit(
	$results,
	( $results['totals']['errors'] === 0 ? 0 : 1 )
);
