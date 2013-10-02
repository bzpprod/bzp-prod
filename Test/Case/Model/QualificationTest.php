<?php
App::uses('Qualification', 'Model');

/**
 * Qualification Test Case
 *
 */
class QualificationTestCase extends CakeTestCase {
/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array('app.qualification', 'app.user', 'app.transaction', 'app.qualified_user');

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Qualification = ClassRegistry::init('Qualification');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Qualification);

		parent::tearDown();
	}

}
