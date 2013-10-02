<?php
App::uses('Store', 'Model');

/**
 * Store Test Case
 *
 */
class StoreTestCase extends CakeTestCase {
/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array('app.store', 'app.user', 'app.fb_album', 'app.fb_fanpage', 'app.transaction', 'app.product', 'app.category', 'app.stores_product');

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Store = ClassRegistry::init('Store');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Store);

		parent::tearDown();
	}

}
