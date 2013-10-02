<?php
App::uses('StoresProduct', 'Model');

/**
 * StoresProduct Test Case
 *
 */
class StoresProductTestCase extends CakeTestCase {
/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array('app.stores_product', 'app.store', 'app.user', 'app.fb_album', 'app.fb_fanpage', 'app.transaction', 'app.product', 'app.category');

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->StoresProduct = ClassRegistry::init('StoresProduct');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->StoresProduct);

		parent::tearDown();
	}

}
