<?php
namespace MultiFeedReader\Models;

abstract class Base
{
	/**
	 * Property dictionary for all tables
	 */
	private static $properties = array();
	
	private $is_new = true;
	
	private $data;
	
	public function __set( $name, $value ) {
		if ( self::has_property( $name ) ) {
			$this->set_property( $name, $value );
		} else {
			$this->$name = $value;
		}
	}
	
	private function set_property( $name, $value ) {
		$this->data[ $name ] = $value;
	}
	
	public function __get( $name ) {
		if ( self::has_property( $name ) ) {
			return $this->get_property( $name );
		} else {
			return $this->$name;
		}
	}
	
	private function get_property( $name ) {
		return $this->data[ $name ];
	}
	
	/**
	 * Define a property with name and type.
	 * 
	 * Currently only supports basics.
	 * @todo enable additional options like NOT NULL, DEFAULT etc.
	 * 
	 * @param string $name Name of the property / column
	 * @param string $type mySQL column type 
	 */
	public static function property( $name, $type ) {
		$class = get_called_class();
		
		if ( ! isset( self::$properties[ $class ] ) ) {
			self::$properties[ $class ] = array();
		}
		
		self::$properties[ $class ][] = array(
			'name' => $name,
			'type' => $type
		);
	}
	
	private static function properties() {
		$class = get_called_class();
		
		if ( ! isset( self::$properties[ $class ] ) ) {
			self::$properties[ $class ] = array();
		}
		
		return self::$properties[ $class ];
	}
	
	public static function has_property( $name ) {
		return in_array( $name, self::property_names() );
	}
	
	private static function property_names() {
		return array_map( function ( $p ) { return $p[ 'name' ]; }, self::properties() );
	}
	
	/**
	 * True if not yet saved to database. Else false.
	 */
	public function is_new() {
		return $this->is_new;
	}
	
	public function flag_as_not_new() {
		$this->is_new = false;
	}
	
	/**
	 * Saves changes to database.
	 */
	public function save() {
		global $wpdb;
		
		if ( $this->is_new() ) {
			$sql = 'INSERT INTO '
			     . self::table_name()
			     . ' ( '
			     . implode( ',', self::property_names() )
			     . ' ) '
			     . 'VALUES'
			     . ' ( '
			     . implode( ',', array_map( array( $this, 'property_name_to_sql_value' ), self::property_names() ) )
			     . ' );'
			;
			$success = $wpdb->query( $sql );
		} else {
			throw new \Exception("missing implementation");
		}
		
		$this->is_new = false;
		
		return $success;
	}
	
	private function property_name_to_sql_value( $p ) {
		if ( $this->$p ) {
			return "'{$this->$p}'";
		} else {
			return 'NULL';
		}
	}
	
	/**
	 * Create database table based on defined properties.
	 * 
	 * Automatically includes an id column as auto incrementing primary key.
	 * @todo allow model changes
	 */
	public static function build() {
		global $wpdb;
		
		$property_sql = array();
		$properties = self::properties();
		foreach ( $properties as $property ) {
			$property_sql[] = "`{$property['name']}` {$property['type']}";
		}
		
		$sql = 'CREATE TABLE IF NOT EXISTS '
		     . self::table_name()
		     . ' ('
		     . implode( ',', $property_sql )
		     . ' );'
		;
		
		$wpdb->query( $sql );
	}
	
	/**
	 * Retrieves the database table name.
	 * 
	 * The name is derived from the namespace an class name. Additionally, it
	 * is prefixed with the global WordPress database table prefix.
	 * @todo cache
	 * 
	 * @return string database table name
	 */
	public static function table_name() {
		global $wpdb;
		
		// get name of implementing class
		$table_name = get_called_class();
		// replace backslashes from namespace by underscores
		$table_name = str_replace( '\\', '_', $table_name );
		// remove Models subnamespace from name
		$table_name = str_replace( 'Models_', '', $table_name );
		// all lowercase
		$table_name = strtolower( $table_name );
		// prefix with $wpdb prefix
		return $wpdb->prefix . $table_name;
	}	
}
