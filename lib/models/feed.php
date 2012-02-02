<?php
namespace MultiFeedReader\Models;

function xpath( $xml, $path ) {
	$element = $xml->xpath( $path );
	return $element[ 0 ];
}

class Feed extends Base
{
	public function parse() {
		$result = array();

		$xml_string = file_get_contents( $this->url );
		$xml = new \SimpleXMLElement( $xml_string );
		
		$result[ 'feed' ] = array(
			'title'    => (string) xpath( $xml, './channel/title' ),
			'link'     => (string) xpath( $xml, './channel/link'),
			'subtitle' => (string) xpath( $xml, './channel/itunes:subtitle'),
			'summary'  => (string) xpath( $xml, './channel/itunes:summary'),
			'image'    => (string) xpath( $xml, './channel/itunes:image[1]')->attributes()->href
		);
		
		$result[ 'items' ] = array();
		
		$items = $xml->xpath( './channel/item' );
		foreach ( $items as $item ) {
			$result[ 'items' ][] = array(
				'content'     => (string) xpath( $item, './content:encoded'),
				'duration'    => (string) xpath( $item, './itunes:duration'),
                'thumbnail'   => (string) $this->extract_thumbnail( $item ),
				'subtitle'    => (string) xpath( $item, './itunes:subtitle'),
				'summary'     => (string) xpath( $item, './itunes:summary'),
				'title'       => (string) xpath( $item, './title'),
				'link'        => (string) xpath( $item, './link'),
				'pubDate'     => (string) xpath( $item, './pubDate'),
				'pubDateTime' => strtotime( xpath( $item, './pubDate' ) ),
				'guid'        => (string) xpath( $item, './guid'),
				'description' => (string) xpath( $item, './itunes:description'),
				'enclosure'   => (string) xpath( $item, './enclosure[1]')->attributes()->url
			);
		}
		
		return $result;
	}
	
	public static function find_by_feed_collection_id( $id ) {
		global $wpdb;

		$class = get_called_class();
		$models = array();

		$rows = $wpdb->get_results( 'SELECT * FROM ' . self::table_name() . ' WHERE feed_collection_id = ' . (int) $id );
		
		foreach ( $rows as $row ) {
			$model = new $class();
			$model->flag_as_not_new();
			foreach ( $row as $property => $value ) {
				$model->$property = $value;
			}
			$models[] = $model;
		}

		return $models;
	}
	
	/**
	 * Extract thumbnail from feed item.
	 * 
	 * Use <itunes:image> if available. Otherwise, use the first <img> in
	 * <content:encoded> which is larger than 1x1 (to skip counter pixels).
	 * 
	 * @param $item
	 * @return string | NULL
	 */
	private function extract_thumbnail( $item ) {
		// look for <itunes:image> and use this if available
		$thumbnail_node = xpath( $item, './itunes:image[1]');
		if ( $thumbnail_node )
			return (string) $thumbnail_node->attributes()->href;
		
		// otherwise look for the first available <img>
		$doc = new \DOMDocument();
		$encoded_content = (string) xpath( $item, './content:encoded');
		$success = $doc->loadHTML( $encoded_content );
		
		if ( ! $success )
			return false;
		
		$xml2 = simplexml_import_dom( $doc );
		$images = $xml2->xpath('//img');
		
		foreach ( $images as $image )
			if ( $image[ 'height' ] > 1 && $image[ 'width' ] > 1 )
				return $image[ 'src' ];
		
		return NULL;
	}
}

Feed::property( 'id', 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY' );
Feed::property( 'feed_collection_id', 'INT' );
Feed::property( 'url', 'VARCHAR(255)' );
// Feed::belongs_to( 'MultiFeedReader\Models\FeedCollection' );