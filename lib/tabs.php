<?php
namespace MultiFeedReader\Lib;

/**
 * A helper class to manage tabs in options pages.
 * 
 * Example:
 *   $tabs = new Tabs;
 *   $tabs->set_tab( 'edit', \MultiFeedReader\t( 'Edit Templates' ) );
 *   $tabs->set_tab( 'add', \MultiFeedReader\t( 'Add Templates' ) );
 *   $tabs->set_default( 'edit' );
 *   $tabs->display();
 * 
 * @todo should not be dependent on the knowledge about \MultiFeedReader\Settings\HANDLE
 * @todo refactor into separate file /lib/settings/tabs.php
 */
class Tabs
{
	private $tabs;
	private $default;
	private $enforced_tab = NULL;
	
	public function set_tab( $id, $text ) {
		$this->tabs[ $id ] = $text;
	}
	
	public function set_default( $default ) {
		$this->default = $default;
	}
	
	public function display() {
		$current_tab = $this->get_current_tab();
		?>
		<h2 class="nav-tab-wrapper">
			<?php foreach ( $this->tabs as $id => $name ): ?>
				<a href="<?php echo admin_url( 'options-general.php?page=' . \MultiFeedReader\Settings\HANDLE . '&tab=' . $id ) ?>" class="nav-tab <?php echo ( $id == $current_tab ) ? 'nav-tab-active' : '' ?>">
					<?php echo $name ?>
				</a>
			<?php endforeach ?>
		</h2>
		<?php
	}
	
	public function get_current_tab() {
		$current_tab = $this->default;
		
		if ( $this->enforced_tab !== NULL ) {
			return $this->enforced_tab;
		}
		
		foreach ( $this->tabs as $id => $name ) {
			if ( $_REQUEST[ 'tab' ] == $id ) {
				$current_tab = $id;
				break;
			}
		}
		
		return $current_tab;
	}
	
	/**
	 * Override the default tab selection behaviour.
	 */
	public function enforce_tab( $tab_id ) {
		$this->enforced_tab = $tab_id;
	}
}
