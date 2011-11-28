<?php
namespace MultiFeedReader\Models;

class FeedCollection extends Base
{
	
	/**
	 * Retrieve the current collection based on global variables.
	 * 
	 * @return FeedCollection
	 */
	public static function current() {
		$id = (int) $_REQUEST[ 'choose_template_id' ];
		
		if ( $id ) {
			return self::find_by_id( $id );
		} else {
			return self::get_default();
		}
	}
	
	/**
	 * Retrieve the default collection.
	 * @todo manage as wp setting
	 * 
	 * @return FeedCollection
	 */
	public static function get_default() {
		return self::first();
	}
	
	/**
	 * Relationship: FeedCollection has many Feeds
	 */
	public function feeds() {
		return \MultiFeedReader\Models\Feed::find_by_feed_collection_id( $this->id );
	}
}

FeedCollection::property( 'id', 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY' );
FeedCollection::property( 'name', 'VARCHAR(255)' );
FeedCollection::property( 'before_template', 'TEXT' );
FeedCollection::property( 'body_template', 'TEXT' );
FeedCollection::property( 'after_template', 'TEXT' );
// FeedCollection::has_many( 'MultiFeedReader\Models\Feed', array( 'plural' => 'feeds' ) );
FeedCollection::build();