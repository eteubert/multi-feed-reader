<?php
namespace MultiFeedReader\Models;

class FeedCollection extends Base
{

	/**
	 * Dictionary of all FeedCollection objects
	 */
	static $feed_collections = NULL;
	
	/**
	 * Reference to the collection itself in self::$feed_collections
	 */
	private $self;
	
	/**
	 * The id of the FeedCollection identifying the collection
	 */
	private $id = NULL;
	
	/**
	 * A list of feed URLs
	 */
	private $feeds = array();
	
	private function __construct( $id ) {
		$this->id = $id;
	}
	
	public function add_feed( $url ) {
		$this->self->feeds[] = $url;
		self::save_all();
	}
	
	public function get_id() {
		return $this->id;
	}
	
	/**
	 * Find single FeedCollection by id.
	 * 
	 * @return false if not found, else the FeedCollection.
	 */
	public static function find_by_id( $id ) {
		self::load_all();
		$collection = self::$feed_collections[ $id ];
		
		if ( $collection ) {
			return $collection;
		} else {
			return false;
		}
	}
	
	/**
	 * Create single FeedCollection by id.
	 * 
	 * @return false if exists already, else the FeedCollection.
	 */
	public static function create_by_id( $id ) {
		self::load_all();
		$collection = self::$feed_collections[ $id ];
		
		if ( $collection ) {
			return false; // exists already
		} else {
			self::$feed_collections[ $id ] = new FeedCollection( $id );
			self::save_all();
			return self::$feed_collections[ $id ];
		}
	}
	
	public static function first() {
		$ids = self::get_ids();
		return FeedCollection::find_by_id( $ids[ 0 ] );
	}
	
	public static function has_entries() {
		self::load_all();
		return is_array( self::$feed_collections ) && count( self::$feed_collections ) > 0;
	}
	
	public static function count() {
		self::load_all();
		return count( self::$feed_collections );
	}
	
	public static function get_ids() {
		return array_keys( self::$feed_collections );
	}
	
	/**
	 * Load all FeedCollections, but only if they have not been loaded yet.
	 */
	private static function load_all() {
		if ( self::$feed_collections !== NULL ) {
			return true;
		}

		self::$feed_collections = get_option( 'feed_collections' );
	}
	
	/**
	 * Save all data to the database
	 */
	private static function save_all() {
		update_option( 'feed_collections', self::$feed_collections );
	}

}

FeedCollection::property( 'name', 'VARCHAR(255)' );
FeedCollection::property( 'before_template', 'TEXT' );
FeedCollection::property( 'body_template', 'TEXT' );
FeedCollection::property( 'after_template', 'TEXT' );
// FeedCollection::has_many( 'MultiFeedReader\Models\Feed' );
FeedCollection::build();