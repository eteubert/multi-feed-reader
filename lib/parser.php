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

    // insert thumbnail, optional dimensions (width x height)
    // Examples: %THUMBNAIL%, %THUMBNAIL|50x50%
    $template = preg_replace_callback( '/%THUMBNAIL(?:\|(\d+)x(\d+))?%/', function ( $matches ) use ( $values ) {
        $src = $values[ 'thumbnail' ];
        
        if ( $src ) {
            if ( $matches[ 1 ] && $matches[ 2 ] ) {
                $out = '<img src="' . $src . '" width="' . $matches[ 1 ] .  '" height="' . $matches[ 2 ] .  '" />';
            } else {
                $out = '<img src="' . $src . '" />';
            }
        } else {
            $out = '';
        }
        
        return $out;
    }, $template );
	
	return $template;
}