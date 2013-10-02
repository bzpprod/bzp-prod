<?php
#Configure::load('amazon');

/**
 * AmazonComponent
 *
 * Provides an entry point into the Amazon SDK.
 */
class AmazonComponent extends Component {

  /**
   * Constructor
   * saves the controller reference for later use
   * @param ComponentCollection $collection A ComponentCollection this component can use to lazy load its components
   * @param array $settings Array of configuration settings.
   */
  public function __construct(ComponentCollection $collection, $settings = array()) {
    $this->_controller = $collection->getController();
    parent::__construct($collection, $settings);
  }
  
  /**
   * Initialization method. Triggered before the controller's `beforeFilfer`
   * method but after the model instantiation.
   *
   * @param Controller $controller
   * @param array $settings
   * @return null
   * @access public
   */
  public function initialize(Controller $controller) {
    // Handle loading our library firstly...
    #App::build(array('Vendor' => array(
    #  APP.'Plugin'.DS.'Amazonsdk'.DS .'Vendor'.DS)
    #));    
    App::import('Vendor', 'Amazon', array(
      'file' => 'AWSSDKforPHP'.DS.'sdk.class.php'
    ));
  }

  /**
   * PHP magic method for satisfying requests for undefined variables. We
   * will attempt to determine the service that the user is requesting and
   * start it up for them.
   *
   * @var string $variable
   * @return mixed
   * @access public
   */
  public function __get($variable) {
    // Build a class name.  (ex: SDB -> AmazonSDB)
    $class = 'Amazon'.$variable;

    // Create the service if class exists.
    if (class_exists($class)) {
      // Store away the requested class for future usage.
      $this->$variable = $this->__createService($class);

      // Return the class back to the caller
      return $this->$variable;
    }
    return false;
  }

  /**
   * Instantiates and returns a new instance of the requested `$class`
   * object.
   *
   * @param string $class
   * @return object
   * @access private
   */
  private function __createService($class) {
    $options = array('credentials' => '@default');
    if (Configure::read('Aws.certificate_authority') !== null) {
      $options['certificate_authority'] = Configure::read('Aws.certificate_authority');
    }
    if (Configure::read('Aws.token') !== null) {
      $options['token'] = Configure::read('Aws.token');
    }
    return new $class($options);
  }

}