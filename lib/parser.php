<?php
namespace MultiFeedReader\Parser;

/**
 * Parses the given template.
 * 
 * @param string $template - HTML template.
 * @param array $values - Feed item values.
 * @param array $feed - Feed values.
 * @return string Parsed template.
 */
function parse( $template, $values, $feed ) {
	
	// replace global feed stuff
	$template = str_replace( '%FEEDTITLE%', $feed[ 'title' ], $template );
	$template = str_replace( '%FEEDSUBTITLE%', $feed[ 'subtitle' ], $template );
	$template = str_replace( '%FEEDSUMMARY%', $feed[ 'summary' ], $template );
	$template = str_replace( '%FEEDLINK%', $feed[ 'link' ], $template );
	$template = str_replace( '%FEEDLANGUAGE%', $feed[ 'language' ], $template );
	
	// insert thumbnail, optional dimensions (width x height)
    // Examples: %FEEDTHUMBNAIL%, %FEEDTHUMBNAIL|50x50%
    $template = preg_replace_callback( '/%FEEDTHUMBNAIL(?:\|(\d+)x(\d+))?%/', function ( $matches ) use ( $feed ) {
        $src = $feed[ 'image' ];
        
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

	// replace feed item stuff
	$template = str_replace( '%TITLE%', $values[ 'title' ], $template );
	$template = str_replace( '%SUBTITLE%', $values[ 'subtitle' ], $template );
	$template = str_replace( '%CONTENT%', $values[ 'content' ], $template );
	$template = str_replace( '%DURATION%', $values[ 'duration' ], $template );
	$template = str_replace( '%SUMMARY%', $values[ 'summary' ], $template );
	$template = str_replace( '%LINK%', $values[ 'link' ], $template );
	$template = str_replace( '%GUID%', $values[ 'guid' ], $template );
	$template = str_replace( '%DESCRIPTION%', $values[ 'description' ], $template );
	$template = str_replace( '%ENCLOSURE%', $values[ 'enclosure' ], $template );
	$template = str_replace( '%DATE%', date( get_option( 'date_format' ), $values[ 'pubDateTime' ] ), $template );
	
	// truncated description
	$template = preg_replace_callback(
	    '/%DESCRIPTION\|(\d+)%/',
	    function ( $matches ) use ( $values ) {
			$stripped = strip_tags( $values[ 'description' ] );
			$short = preg_replace( '/((\S+\s){' . $matches[ 1 ] . '}(\w+))(.*)/i', '${1}', $stripped );
			
			$ellipsis = '';
			if ( $short != $stripped )
				$ellipsis = ' ...';
				
				
	        return $short . $ellipsis;
	    },
	 	$template
	 );
	
	// custom date format
	$template = preg_replace_callback(
	    '/%DATE\|(.+)%/',
	    function ( $matches ) use ( $values ) {
	        return date( $matches[ 1 ], $values[ 'pubDateTime' ] );
	    },
	 	$template
	 );

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