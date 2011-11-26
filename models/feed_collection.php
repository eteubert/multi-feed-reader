<?php
namespace MultiFeedReader\Models;

class FeedCollection extends Base
{
	
	public static function has_entries() {
		return self::count() > 0;
	}
	
	public static function count() {
		global $wpdb;
		
		$sql = 'SELECT COUNT(*) FROM ' . self::table_name();
		return (int) $wpdb->get_var( $wpdb->prepare( $sql ) );
	}
	
	public static function first() {
		global $wpdb;
		
		$model = new FeedCollection();
		$model->flag_as_not_new();
		
		$row = $wpdb->get_row( 'SELECT * FROM ' . self::table_name() . ' LIMIT 0,1' );
		foreach ( $row as $property => $value ) {
			$model->$property = $value;
		}
		
		return $model;
	}
	
}

FeedCollection::property( 'id', 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY' );
FeedCollection::property( 'name', 'VARCHAR(255)' );
FeedCollection::property( 'before_template', 'TEXT' );
FeedCollection::property( 'body_template', 'TEXT' );
FeedCollection::property( 'after_template', 'TEXT' );
// FeedCollection::has_many( 'MultiFeedReader\Models\Feed' );
FeedCollection::build();