<?php
namespace MultiFeedReader\Models;

class Feed extends Base
{
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
}

Feed::property( 'id', 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY' );
Feed::property( 'feed_collection_id', 'INT' );
Feed::property( 'url', 'VARCHAR(255)' );
// Feed::belongs_to( 'MultiFeedReader\Models\FeedCollection' );
Feed::build();