<?php
namespace MultiFeedReader\Parser;

function parse( $template, $values ) {
	
	$template = str_replace( '%TITLE%', $values[ 'title' ], $template );
	$template = str_replace( '%SUBTITLE%', $values[ 'subtitle' ], $template );
	$template = str_replace( '%CONTENT%', $values[ 'content' ], $template );
	$template = str_replace( '%DURATION%', $values[ 'duration' ], $template );
	$template = str_replace( '%SUMMARY%', $values[ 'summary' ], $template );
	$template = str_replace( '%LINK%', $values[ 'link' ], $template );
	// TODO: default human readable pubdate string
	// TODO: customizable human readable pubdate string
	// $template = str_replace( '%TITLE%', $values[ 'pubDate' ], $template );
	// $template = str_replace( '%TITLE%', $values[ 'pubDateTime' ], $template );
	$template = str_replace( '%GUID%', $values[ 'guid' ], $template );
	$template = str_replace( '%DESCRIPTION%', $values[ 'description' ], $template );
	$template = str_replace( '%ENCLOSURE%', $values[ 'enclosure' ], $template );
	
	return $template;
}