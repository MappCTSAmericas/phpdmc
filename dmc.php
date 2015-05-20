<?php

/** dmc.php
 *
 * Teradata provides programmatic access to our email marketing tool, Digital
 * Messaging Center via SOAP. All you need is a special API user within your
 * individual system to do the programing. Once you receive your user data, you
 * can access all of the functionality that is available both in the API and
 * your Digital Messaging Center system.
 *
 * Only Async calls require additional configuration. Async calls are based on
 * special API scripts which are adapted to your particular needs. These API
 * scripts are planned and set up with your Teradata representative.
 *
 * @package DMC_PHPSoapClient
 * @author Teradata Interactive
 * @copyright Copyright (c) 2014, Teradata Interactive
 * @link http://ozoneonline.com
 * @link http://teradata.com
 * @version v1.0.0
 * @filesource
 */

/**
 * Provides basic access to DMC API functions.
 *
 * To use this client to access your instance of Teradata's Digital Messaging
 * Center, include it in your script then instantiate it by providing your
 * SOAP endpoint and login credentials. To display faults for debugging during
 * development, set $fault_trace to 'true'.
 *
 * NOTE: Setting $fault_trace to 'true' will return <b>all</b> faults, including those
 * that are to be expected during normal script operation.
 *
 * <code>
 * <?php
 * include '/path/to/dmc.php';
 *
 * $soap_url = 'https://sslc.teradatadmc.com/[some_path]/api/soap/v2?wsdl';
 * $soap_settings = array(
 *  'login' => 'apiuser@yourdomain.com',
 *  'password' => 'apiuserpassword',
 * );
 * $fault_trace = true;
 *
 * $dmc = new DMC( $soap_url, $soap_settings, $fault_trace );
 * ?>
 * </code>
 *
 * Similarly, if you'd like to use this without providing your credentials for
 * each instance, you can update the class constructor to reflect your login
 * information.
 * <code>
 * public function __construct() {
 *      $this->fault_trace = true;
 *
 *      $this->soap_url = 'https://sslc.teradatadmc.com/[some_path]/api/soap/v2?wsdl';
 *      $this->soap_settings = array(
 *        'login' => 'apiuser@yourdomain.com',
 *        'password' => 'apiuserpassword',
 *      );
 *      $this->soap = new SoapClient( $this->soap_url, $this->soap_settings );
 * }
 * </code>
 *
 * Please refer to the included documentation for more information regarding
 * specific class methods and API functions.
 *
 * @author Nick Silva <nick.silva@teradata.com>
 * @author Jake Lockwood <jakelockwood27@gmail.com>
 * @since v1.0.0
 *
 * @todo add error messaging for incorrect parameter types, etc.
 * @todo add code examples in docblocks where applicable.
 * @todo expand function explainations and augment with Ecircle api documentation where applicable.
 * @todo make error reporting less inntrusive.
 */
class DMC {

    /**
     * SOAP endpoint URL
     *
     * <code>
     * $soap_url = 'https://sslc.teradatadmc.com/[some_path]/api/soap/v2?wsdl';
     * </code>
     *
     * @var string DMC SOAP Endpoint
     * @access private
     *
     * @see http://www.php.net/manual/en/soapclient.soapclient.php
     */
    private $soap_url;

    /**
     * SOAP login credentials and client settings
     *
     * <code>
     * $soap_settings = array(
     *  'login' => 'apiuser@yourdomain.com',
     *  'password' => '1v3ry53cur3p@55w0Rd!'
     * );
     * </code>
     *
     * @var array Array of login credentials and settings
     * @access private
     *
     * @see http://www.php.net/manual/en/soapclient.soapclient.php
     */
    private $soap_settings;

    /**
     * Display SOAP fault and exception information for debugging.
     *
     * <code>
     * $fault_trace = true;
     * </code>
     *
     * @var bool
     * @access private
     */
    private $fault_trace;

    /**
     * Beginning timestamp if benchmarking is true on object construction.
     *
     * @var bool
     * @access private
     */
    private $start_time;

    /**
     * Internal SOAP Client
     *
     * @var obj Internal SOAP Client
     * @access private
     *
     * @see http://www.php.net/manual/en/soapclient.soapclient.php
     */
    private $soap;

    /**
     * Constructs class object and sets up SOAP options.
     *
     * @param string $soap_url SOAP endpoint URL
     * @param array $soap_settings SOAP settings array
     * @param bool $fault_trace Display SOAP fault and exception information for debugging. Defaults to false.
     * @param bool $benchmark Output benchmark statement after actions are completed. (Default: false)
     * @return DMC
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::soap_url
     * @uses DMC::soap_settings
     * @uses DMC::fault_trace
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function __construct( $soap_url, $soap_settings, $fault_trace = false, $benchmark = false ) {
        // These values don't need to be changed.
        $soap_settings['exceptions'] = false;
        $soap_settings['trace'] = true;

        // Change these values if you want to hard-code your SOAP settings.
        $this->fault_trace = $fault_trace;

        $this->soap_url = $soap_url;
        $this->soap_settings = $soap_settings;
        $this->soap = new SoapClient( $this->soap_url, $this->soap_settings );

        if ( $benchmark ) {
            $this->start_time = microtime( true );
        }
    }

    /**
     * If class is instantiated with $benchmark = true, displays seconds taken
     * to complete class actions on script completion.
     *
     * @access public
     * @since v1.0.0
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function __destruct() {
        if ( isset( $this->start_time ) ) {
            $seconds = microtime( true ) - $this->start_time;
            echo "<p>Action completed in $seconds seconds.</p>";
        }
    }

    // SYSTEM METHODS
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Returns API version of current DMC instance
     *
     * @return string API Version
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function api_version() {
        $v = $this->call( 'systemGetApiVersion' );

        return $v ? $v->version : false;
    }

    /**
     * Returns build number of current DMC instance
     *
     * @return string Build number
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function ecm_version() {
        $v = $this->call( 'systemGetEcmVersion' );

        return $v ? str_replace( 'Build ', '', $v->version ) : false;
    }

    /**
     * Returns list of available functions on the current DMC instance.
     *
     * @return array List of available functions
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function functions() {
        foreach ( $this->soap->__getFunctions() as $f ) {
            $fname = explode( ' ', $f );
            $functions[] = substr( $fname[0], 0, ( strlen( $fname[0] ) - 8 ) );
        }

        return $functions;
    }

    // META METHODS
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Creates a new data field where information about users can be stored.
     *
     * With the standard DMC setup, you are permitted to create a limited number of
     * custom attributes with different data types. Each custom user attribute has a
     * unique name. If an attribute is no longer needed, it may be archived. Please
     * contact your customer support representative if you need to delete an
     * attribute.
     *
     * @param string $name Desired custom attribute name
     * @param string $type Acceptable values are 'string', 'number', and 'boolean'
     * @param array $enumeration_values List of acceptable values for this field
     * @param bool $active
     * @return bool
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     *
     * @todo Test 'enumeration_values' parameter.
     */
    public function create_attribute( $name, $type, $enumeration_values = [ ], $active = true ) {
        $a = $this->call( 'metaCreateAttributeDefinitions', [
            'attributeDefinitions' => [
                'name' => $name,
                'type' => strtoupper( $type ),
                'enumerationValues' => $enumeration_values,
                'active' => $active,
            ] ] );

        return $a ? true : false;
    }

    /**
     * Returns a list of all active custom attributes.
     *
     * Attributes with the status 'archived' or 'system' will not be included in
     * the list.
     *
     * @return array|bool List of available custom attribute objects. False if unsuccessful or no attributes.
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function get_attributes() {
        $a = $this->call( 'metaGetAttributeDefinitions' );

        return isset( $a->attributeDefinitions ) ? $a->attributeDefinitions : false;
    }

    /**
     * Archives custom attributes.
     *
     * An archived attribute is still stored in the system. The attribute can still
     * be in use for existing messages and sendouts, but it will not be available in
     *  the GUI message creation process (i.e. personalization builder).
     *
     * @param string $attribute Name of attribute to be archived.
     * @return bool
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function archive_attribute( $attribute ) {
        $a = $this->call( 'metaArchiveAttributeDefinitions', [
            'attributeNames' => $attribute,
                ] );

        return $a ? true : false;
    }

    /**
     * Activates custom attributes.
     *
     * Reactivates archived custom attributes that are named in parameter list.
     * After activation, they can be used in personalization during the message
     * composition process.
     *
     * @param string $attribute Name of attribute to be reactivated.
     * @return bool
     *
     * @access public
     * @since v1.0.0
     * @uses DMC::call()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function activate_attribute( $attribute ) {
        $a = $this->call( 'metaActivateAttributeDefinitions', [
            'attributeNames' => $attribute,
                ] );

        return $a ? true : false;
    }

    /**
     * Returns a list of all currently defined active link categories.
     *
     * @return array|bool
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function get_link_categories() {
        $c = $this->call( 'metaGetLinkCategories' );

        return isset( $c->linkCategories ) ? $c->linkCategories : false;
    }

    /**
     * Creates new link categories.
     *
     * Link categories are automatically assigned to a link via a regex pattern.
     * The link categories group together different links for statistical
     * purposes and can be used to trigger automated processes when a link of a
     * certain category is clicked. The name and pattern are mandatory inputs
     * for the category. The ID is automatically assigned by the system.
     *
     * Input example:
     * <code>
     * $categories = array(
     *   'name' => 'email',
     *   'description' => "Test category - matches an email address",
     *   'pattern' => '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/',
     *  );
     * </code>
     *
     * @param array $categories Associative array of category parameters
     * @return bool
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function create_link_categories( $categories ) {
        $c = $this->call( 'metaCreateLinkCategories', [
            'categories' => $categories,
                ] );

        return isset( $c ) && $c != false ? true : false;
    }

    /**
     * Updates an existing link category identified by the parameter category.
     *
     * Input example:
     * <code>
     * $category = array(
     *   'id' => 3000000009,
     *   'name' => 'email',
     *   'description' => "Test category - now matches any URL",
     *   'pattern' => '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
     *  );
     * </code>
     *
     * @param array $category Associative array of category parameters
     * @return bool
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function update_link_category( $category ) {
        $c = $this->call( 'metaUpdateLinkCategory', [
            'category' => $category,
                ] );

        return isset( $c ) && $c != false ? true : false;
    }

    /**
     * Updates an existing link category or list of categories..
     *
     * Input example:
     * <code>
     * $category = 'deleteThis';
     * $categories = array(
     *   'deleteThisCategory',
     *   'deleteThisCategoryToo',
     *   'deleteThisCategoryAsWell,
     * );
     * </code>
     *
     * @param string|array $categories Name or list of category names to delete.
     * @return bool
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function delete_link_categories( $categories ) {
        $categories = is_array( $categories ) ? $categories : [ $categories ];

        $c = $this->call( 'metaDeleteLinkCategory', [
            'categoryNames' => $categories,
                ] );

        return isset( $c ) && $c != false ? true : false;
    }

    // GROUP METHODS
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Gets a group object by group ID.
     *
     * @param int $group_id Group ID
     * @return obj Group
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function get_group( $group_id ) {
        $g = $this->call( 'groupGet', [
            'groupId' => $group_id,
                ] );

        return isset( $g ) && $g != false ? $g->group : false;
    }

    /**
     * Clones an already-existing group.
     *
     * Creates a clone of a specific group that already exists in the system.
     * Most of the sendout options and delivery settings are identical for the
     * original and the clone. However, group members are not copied; that is,
     * members of the group are not members of the clone. With the options array
     * you have to specify how and with what mandatory parameters the group will
     *  be cloned.
     *
     * Example:
     * <code>
     * $group_id = 4000003587;
     * $options = array(
     *   'name'                     => 'Group Clone', // must be unique in your system
     *   'description'              => 'Group Description',
     *   'email'                    => 'groupclone@yourdomain.teradatadmc.com', // must be valid and unique in your system
     *   'includePreparedMessages'  => bool, // set to true to include all prepared messages
     *   'includeTestUsers'         => bool, // set to true to include test users
     * );
     *
     * $dmc->clone_group( $group_id, $options );
     * </code>
     *
     * NOTE: Your API user must be a group manager of the group you are
     * attempting to clone.
     *
     * @param int $group_id Group ID
     * @param array $options Options array (see example)
     * @return obj|bool Group object on success. False if no group created.
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function clone_group( $group_id, $options ) {
        $g = $this->call( 'groupClone', [
            'groupId' => $group_id,
            'options' => $options,
                ] );

        return isset( $g ) && $g != false ? $g->group : false;
    }

    /**
     * Provides a list of group attributes.
     *
     * @param int $group_id Group ID
     * @return obj|bool Group object on success. False if no group or associated attributes available..
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     *
     * @todo Find out about group attributes
     */
    public function get_group_attributes( $group_id ) {
        $g = $this->call( 'groupGetAttributes', [
            'groupId' => $group_id,
                ] );

        return ( isset( $g ) && $g != false ) ? ( ( isset( $g->attributes ) ) ? $this->sanitize_attributes_object( $g ) : false ) : false;
    }

    /**
     * Creates and updates the group attributes. Existing attributes are
     * overwritten.
     *
     * @param array $attributes Associative array of attributes and values to search for
     * @return array|bool
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function find_group_by_attribute( $attributes ) {
        $g = $this->call( 'groupFindIdsByAttributes', [
            'attributes' => $this->build_attributes_object( $attributes ),
                ] );

        return (isset( $g ) && $g != false & isset( $g->groupIds )) ? $g->groupIds : false;
    }

    /**
     * Creates and updates the group attributes. Existing attributes are
     * overwritten.
     *
     * @param int $group_id Group ID
     * @param array $attributes Associative array of attributes to set
     * @return bool
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function set_group_attributes( $group_id, $attributes ) {
        $g = $this->call( 'groupSetAttributes', [
            'groupId' => $group_id,
            'attributes' => $this->build_attributes_object( $attributes ),
                ] );

        return isset( $g ) && $g != false ? true : false;
    }

    /**
     * Returns all of the prepared messages for a specific group.
     *
     * @param int $group_id Group ID
     * @return array|bool Array of message IDs on success. False if error or no messages found.
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function get_prepared_messages( $group_id ) {
        $m = $this->call( 'groupGetPreparedMessages', [
            'groupId' => $group_id,
                ] );

        return isset( $m ) && $m != false ? ((isset( $m->messageIds ) && is_array( $m->messageIds )) ? $m->messageIds : [ $m->messageIds ]) : false;
    }

    /**
     * Archive a list of groups and any dependent subgroups. Other dependent
     * objects, such as triggers and scheduled tasks, are not archived.
     *
     * @param int|array $group_id Group ID or array of group IDs
     * @return bool False if error.
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function archive_group( $group_id ) {
        $g = $this->call( 'groupArchive', [
            'groupIds' => is_array( $group_id ) ? $group_id : [ $group_id ],
                ] );

        return ( isset( $g ) && $g != false && $g->archivingResults->entityKey ) == $group_id ? true : false;
    }

    /**
     * Activate a previously archived group.
     *
     * @param int|array $group_id Group ID or array of group IDs
     * @return bool False if error.
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function activate_group( $group_id ) {
        $g = $this->call( 'groupActivate', [
            'groupIds' => is_array( $group_id ) ? $group_id : [ $group_id ],
                ] );

        return ( isset( $g ) && $g != false && $g->activatingResults->entityKey == $group_id ) ? true : false;
    }

    // USER METHODS
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Create a user profile from email. Can be used to add user mobile and
     * add profile attributes, if applicable..
     *
     * @param string $email User email
     * @param int $mobile User mobile
     * @param array $attributes Associative array of user profile attributes
     * @return obj
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     * @uses DMC::is_valid_email()
     * @uses DMC::build_attributes_object()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function create_user( $email, $mobile = null, $attributes = null ) {
        if ( $this->is_valid_email( $email ) && $this->get_user_by_email( $email ) == false ) {
            $u = $this->call( 'userCreate', [
                'email' => $email,
                'mobileNumber' => $mobile,
                'attributes' => $attributes == null ? [ ] : $this->build_attributes_object( $attributes ),
                    ] );
        }

        return ( isset( $u ) && $u != false ) ? $u->user : false;
    }

    /**
     * Look up single user
     *
     * @param int $id User ID
     * @return obj DMC user
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function get_user( $id ) {
        $u = $this->call( 'userGet', [
            'userId' => $id ] );

        return $u ? $u->user : false;
    }

    /**
     * Look up single user by email address
     *
     * @param string  $email User email
     * @return obj DMC user
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     * @uses DMC::is_valid_email()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function get_user_by_email( $email ) {
        if ( $this->is_valid_email( $email ) ) {
            $u = $this->call( 'userGetByEmail', [
                'email' => $email ] );
        }

        return ( isset( $u ) && $u != false ) ? $u->user : false;
    }

    /**
     * Look up single user's profile by user ID.
     *
     * @param string  $id User ID
     * @return obj DMC user profile
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     * @uses DMC::sanitize_attributes_object()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function get_profile( $id ) {
        $p = $this->call( 'userGetProfile', [
            'userId' => $id ] );

        return $p ? $this->sanitize_attributes_object( $p ) : false;
    }

    /**
     * Look up single user's profile by email.
     *
     * @param string  $email User email
     * @return obj DMC user profile
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     * @uses DMC::is_valid_email()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function get_profile_by_email( $email ) {
        if ( $this->is_valid_email( $email ) ) {
            $p = $this->call( 'userGetProfileByEmail', [
                'email' => $email ] );
        }

        return ( isset( $p ) && $p != false ) ? $this->sanitize_attributes_object( $p ) : false;
    }

    /**
     * Updates the user's profile by user ID.
     *
     * Update a user's profile with the information saved in the attribute list.
     * This method only changes the information that is explicitly mentioned.
     * Attributes that are not mentioned are not changed.
     *
     * @param int $id
     * @param array $attributes
     * @return bool
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     * @uses DMC::get_user()
     * @uses DMC::build_attributes_object()
     * @see DMC::replace_profile()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function update_profile( $id, $attributes ) {
        if ( $this->get_user( $id ) != false ) {
            $u = $this->call( 'userUpdateProfile', [
                'userId' => $id,
                'attributes' => $this->build_attributes_object( $attributes ),
                    ] );
        }

        return ( isset( $u ) && $u != false ) ? true : false;
    }

    /**
     * Updates the user's profile by email address.
     *
     * Update a user's profile with the information saved in the attribute list.
     * This method only changes the information that is explicitly mentioned.
     * Attributes that are not mentioned are not changed.
     *
     * @param string $email
     * @param array $attributes
     * @return bool
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     * @uses DMC::get_user_by_email()
     * @uses DMC::is_valid_email()
     * @uses DMC::build_attributes_object()
     * @see DMC::update_profile()
     * @see DMC::replace_profile()
     * @see DMC::replace_profile_by_email()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function update_profile_by_email( $email, $attributes ) {
        if ( $this->is_valid_email( $email ) && $this->get_user_by_email( $email ) != false ) {
            $u = $this->call( 'userUpdateProfileByEmail', [
                'email' => $email,
                'attributes' => $this->build_attributes_object( $attributes ),
                    ] );
        }

        return ( isset( $u ) && $u != false ) ? true : false;
    }

    /**
     * Replace the user's profile by user ID.
     *
     * Replaces all attribute values for a specific user. All attribute values
     * that are transmitted with the method are added, and replace any existing
     * values. Any currently existing attribute values that are not found in the
     * API call are deleted.
     *
     * @param int $id
     * @param array $attributes
     * @return bool
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     * @uses DMC::get_user()
     * @uses DMC::build_attributes_object()
     * @see DMC::update_profile()
     * @see DMC::replace_profile()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function replace_profile( $id, $attributes ) {
        if ( $this->get_user( $id ) != false ) {
            $u = $this->call( 'userReplaceProfile', [
                'userId' => $id,
                'attributes' => $this->build_attributes_object( $attributes ),
                    ] );
        }

        return ( isset( $u ) && $u != false ) ? true : false;
    }

    /**
     * Replace the user's profile by email address.
     *
     * @param string $email
     * @param array $attributes
     * @return bool
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     * @uses DMC::get_user_by_email()
     * @uses DMC::is_valid_email()
     * @uses DMC::build_attributes_object()
     * @see DMC::replace_profile()
     * @see DMC::update_profile()
     * @see DMC::update_profile_by_email()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function replace_profile_by_email( $email, $attributes ) {
        if ( $this->is_valid_email( $email ) && $this->get_user_by_email( $email ) != false ) {
            $u = $this->call( 'userReplaceProfileByEmail', [
                'email' => $email,
                'attributes' => $this->build_attributes_object( $attributes ),
                    ] );
        }

        return ( isset( $u ) && $u != false ) ? true : false;
    }

    /**
     * Delete an existing user from the system. False if unsuccessful or user
     * doesn't exist.
     *
     * @param int $id User ID
     * @return bool
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     * @uses DMC::get_user()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function delete_user( $id ) {
        if ( $this->get_user( $id ) ) {
            $u = $this->call( 'userDelete', [
                'userId' => $id,
                    ] );
        }

        return ( isset( $u ) && $u != false ) ? true : false;
    }

    /**
     * Delete an existing user from the system by email. False if unsuccessful
     * or user doesn't exist..
     *
     * @param string $email User email address
     * @return bool
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     * @uses DMC::is_valid_email()
     * @uses DMC::get_user_by_email()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function delete_user_by_email( $email ) {
        if ( $this->is_valid_email( $email ) && $u = $this->get_user_by_email( $email ) ) {
            $d = $this->call( 'userDelete', [
                'userId' => $u->id,
                    ] );
        }

        return ( isset( $d ) && $d != false ) ? true : false;
    }

    // MEMBERSHIP METHODS
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Subscribes a user to a group.
     *
     * Subscribes the given user to the specified group using the designated
     * subscription method (CONFIRMED_OPT_IN, DOUBLE_OPT_IN, OPT_IN).
     *
     * @param integer $user_id
     * @param integer $group_id
     * @param string $subsribe_mode
     *
     * @return boolean
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     *
     * @author Jake Lockwood <jakelockwood27@gmail.com>
     */
    public function subscribe_member( $user_id, $group_id, $subsribe_mode ) {
        $m = $this->call( "membershipSubscribe", [
            "userId" => $user_id,
            "groupId" => $group_id,
            "subscriptionMode" => $subsribe_mode ] );
        return ( isset( $m ) && $m != false ) ? true : false;
    }

    /**
     * Subscribes a user to a group with their email address.
     *
     * Subscribes the given user specified by their email address to the
     * designated group using the desired subscription method
     *  (CONFIRMED_OPT_IN, DOUBLE_OPT_IN, OPT_IN).
     *
     * @param string $email
     * @param integer $group_id
     * @param string $subscribe_mode
     * @return boolean
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     * @uses DMC::is_valid_email()
     *
     * @author Jake Lockwood <jakelockwood27@gmail.com>
     */
    public function subscribe_member_by_email( $email, $group_id, $subscribe_mode ) {
        if ( $this->is_valid_email( $email ) ) {
            $m = $this->call( "membershipSubscribeByEmail", [
                "email" => $email,
                "groupId" => $group_id,
                "subscriptionMode" => $subscribe_mode ] );
        }
        return ( isset( $m ) && $m != false ) ? true : false;
    }

    /**
     * Unsubscribes a user from a specified group.
     *
     * Unsubscribes the user from the group and sends a notification to the
     * group's manager.
     *
     * @param integer $user_id
     * @param integer $group_id
     * @return boolean
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     *
     * @author Jake Lockwood <jakelockwood27@gmail.com>
     */
    public function unsubscribe_member( $user_id, $group_id ) {
        $u = $this->call( "membershipUnsubscribe", [
            "userId" => $user_id,
            "groupId" => $group_id ] );

        return ( isset( $u ) && $u != false ) ? true : false;
    }

    /**
     * Unsubscribes a user from a group based on their email address.
     *
     * Unsubscribes the user from the group and sends an unsubscribe confirmation
     * email to the user.
     *
     * @param string $email
     * @param integer $group_id
     * @return boolean
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::is_valid_email()
     * @uses DMC::call()
     *
     * @author Jake Lockwood <jakelockwood27@gmail.com>
     */
    public function unsubscribe_member_by_email( $email, $group_id ) {
        if ( $this->is_valid_email( $email ) ) {
            $u = $this->call( "membershipUnsubscribeByEmail", [
                "email" => $email,
                "groupId" => $group_id ] );
        }
        return ( isset( $u ) && $u != false ) ? true : false;
    }

    /**
     * Creates a group membership for a user to a group without
     * sending a notification
     *
     * Similar to subscribe_member, a user is subscribed to a group but the only
     * difference is that the notification is not sent.
     *
     * @param integer $user_id
     * @param integer $group_id
     * @return boolean
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     *
     * @author Jake Lockwood <jakelockwood27@gmail.com>
     *
     * @todo verify return values
     */
    public function create_membership( $user_id, $group_id ) {
        $c = $this->call( "membershipCreate", [
            "userId" => $user_id,
            "groupId" => $group_id ] );

        return ( isset( $c ) && $c != false ) ? true : false;
    }

    /**
     * Deletes an existing member from a group
     *
     * Similar to unsubscribe_member, a member is unsubscribed from a group however
     * the user isn't notified about the deletion.
     *
     * @param integer $user_id
     * @param integer $group_id
     * @return boolean
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     *
     * @author Jake Lockwood <jakelockwood27@gmail.com>
     */
    public function delete_membership( $user_id, $group_id ) {
        $d = $this->call( "membershipDelete", [
            "userId" => $user_id,
            "groupId" => $group_id ] );

        return ( isset( $d ) && $d != false ) ? true : false;
    }

    /**
     * Returns a list of all memberships of a given user
     *
     * Returns an array of memberships objects for each group that the user is a
     * part of.
     *
     * @param integer $user_id
     * @return array|bool
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     * @uses DMC::sanitize_memberships_array()
     *
     * @author Jake Lockwood <jakelockwood27@gmail.com>
     */
    public function find_all_memberships( $user_id ) {
        $f = $this->call( "membershipFindAll", [ "userId" => $user_id ] );
        return ( isset( $f ) && $f != false ) ? $this->sanitize_memberships_array( $f->memberships ) : false;
    }

    /**
     * Returns a list of all memberships of a given user designated by email
     *
     * Returns an array of memberships objects for each group that the user is a
     * part of. The user is determined by their email.
     *
     * @param string $email
     * @return array|bool
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::is_valid_email()
     * @uses DMC::call()
     * @uses DMC::sanitize_memberships_array()
     *
     * @author Jake Lockwood <jakelockwood27@gmail.com>
     */
    public function find_all_memberships_by_email( $email ) {
        if ( $this->is_valid_email( $email ) ) {
            $f = $this->call( "membershipFindAllByEmail", [ "email" => $email ] );
        }
        return ( isset( $f ) && $f != false ) ? $this->sanitize_memberships_array( $f->memberships ) : false;
    }

    /**
     * Returns member attributes for a user within a specified group.
     *
     * Returns a collection of member attributes for a user within the specified
     * group. Member attributes are used to save information for an individual
     * user, but in the context of a specific group. A member attribute contains
     * a specific value for each recipient.
     *
     * @param int $user_id
     * @param int $group_id
     * @return array|bool
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::sanitize_attributes_object()
     * @uses DMC::call()
     *
     * @author Jake Lockwood <jakelockwood27@gmail.com>
     */
    public function get_membership_attributes( $user_id, $group_id ) {
        $a = $this->call( "membershipGetAttributes", [ "userId" => $user_id, "groupId" => $group_id ] );

        return ( isset( $a ) && $a != false ) ? $this->sanitize_attributes_object( $a ) : false;
    }

    /**
     * Returns member attributes for a user within a specified group.
     *
     * Returns a collection of member attributes for a user within the specified
     * group. Member attributes are used to save information for an individual
     * user, but in the context of a specific group. A member attribute contains
     * a specific value for each recipient.

     *
     * @param string $email
     * @param int $group_id
     * @return array|bool
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::is_valid_email()
     * @uses DMC::call()
     * @uses DMC::sanitize_attributes_object()
     *
     * @author Jake Lockwood <jakelockwood27@gmail.com>
     */
    public function get_membership_attributes_by_email( $email, $group_id ) {
        if ( $this->is_valid_email( $email ) ) {
            $a = $this->call( "membershipGetAttributesByEmail", [ "email" => $email, "groupId" => $group_id ] );
        }

        return ( isset( $a ) && $a != false ) ? $this->sanitize_attributes_object( $a ) : false;
    }

    /**
     * Updates the member attributes for a user within a certain group.
     *
     * Updates the member attributes for a user within a certain group, if the
     * user is a member of the group. If an attribute already exists, the value
     * will be updated; if not, a new attribute will be created. If an existing
     * member attribute is not in the attribute list being sent in the call, it
     * will be left untouched.
     *
     * @param string $email
     * @param int $group_id
     * @return bool
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::get_user()
     * @uses DMC::call()
     * @uses DMC::build_attributes_object()
     *
     * @author Jake Lockwood <jakelockwood27@gmail.com>
     */
    public function update_membership_attributes( $user_id, $group_id, $attributes ) {
        if ( $this->get_user( $user_id ) != false ) {
            $u = $this->call( 'membershipUpdateAttributes', [
                'userId' => $user_id,
                'groupId' => $group_id,
                'attributes' => $this->build_attributes_object( $attributes ),
                    ] );
        }
        return ( isset( $u ) && $u != false ) ? true : false;
    }

    /**
     * Updates the member attributes for a user within a certain group.
     *
     * Updates the member attributes for a user within a certain group, if the
     * user is a member of the group. If an attribute already exists, the value
     * will be updated; if not, a new attribute will be created. If an existing
     * member attribute is not in the attribute list being sent in the call, it
     * will be deleted.

     *
     * @param int $user_id
     * @param int $group_id
     * @param array $attributes
     * @return bool
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::get_user()
     * @uses DMC::call()
     * @uses DMC::build_attributes_object()
     *
     * @author Jake Lockwood <jakelockwood27@gmail.com>
     */
    public function replace_membership_attributes( $user_id, $group_id, $attributes ) {
        if ( $this->get_user( $user_id ) != false ) {
            $u = $this->call( 'membershipReplaceAttributes', [
                'userId' => $user_id,
                'groupId' => $group_id,
                'attributes' => $this->build_attributes_object( $attributes ),
                    ] );
        }
        return ( isset( $u ) && $u != false ) ? true : false;
    }

    // MESSAGE METHODS
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Sends a prepared message to a single recepient. For multiple recipients,
     * use a foreach loop:
     *
     * <code>
     * $message_id = 1234567890;
     * $recipients = [
     *   'user1@host1.com',
     *   'user2@host2.com',
     *   'user3@host3.com',
     *   'user4@host4.com',
     *   'user5@host5.com',
     * ];
     *
     * foreach ( $recipients as $recipient ) {
     *  $user = $dmc->get_user_by_email( $recipient );
     *  $dmc->sent_single_message( $message_id, $user->id );
     * };
     * </code>
     *
     * @param integer $message_id Message Id
     * @param integer $user_id Receipent Id
     * @param string $extra Additional Message Content
     * @return boolean
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     * @uses DMC::get_user()
     * @uses DMC::validate_message()
     *
     * @author Jake Lockwood <jakelockwood27@gmail.com>
     */
    public function send_single_message( $message_id, $user_id, $extra = null ) {
        if ( $this->get_user( $user_id ) ) {
            $m = $this->call( "messageSendSingle", [ 'messageId' => $message_id, 'recipientId' => $user_id, 'additionalContent' => $extra ] );
        }
        return ( isset( $m ) && $m != false ) ? true : false;
    }

    /**
     * Sends a prepared message template as a transactional message?
     *
     * Sends a previously prepared message template for a group as a
     * transactional message to another user for template use?
     *
     * WARNING: This needs testing... we have no idea what it will actually do.
     *
     * @param integer $message_id
     * @param string $transaction_id
     * @param integer $user_id
     * @param string $extra
     * @return boolean
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     * @uses DMC::get_user()
     * @uses DMC::validate_message()
     *
     * @author Jake Lockwood <jakelockwood27@gmail.com>
     *
     * @todo test functionality and format output
     */
    public function send_transactional_message( $message_id, $transaction_id, $user_id, $extra = null ) {
        if ( $this->get_user( $user_id ) && $this->validate_message( $message_id ) != false ) {
            $m = $this->call( "messangeSendTransactional", [ 'messageId' => $message_id, 'externalTransactionFormula' => $transaction_id,
                'recipientId' => $user_id, 'additionalContent' => $extra ] );
        }
        return ( isset( $m ) && $m != false ) ? true : false;
    }

    /**
     * Returns message personalizations used in the given message.
     *
     * Returns all the personalization references within the message given by
     * the message ID.
     *
     * If message ID is not valid, returns false.
     *
     * @param integer $message_id
     * @return array of Personalizations or boolean
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     * @uses DMC::validate_message()
     *
     * @author Jake Lockwood <jakelockwood27@gmail.com>
     *
     */
    public function get_message_personalizations( $message_id ) {
        if ( $this->validate_message( $message_id ) != false ) {
            $p = $this->call( "messageGetUsedPersonalizations", [ 'messageId' => $message_id ] );
        }
        return ( isset( $p ) && $p != false ) ? $p->attributeNames : false;
    }

    /**
     * Validates the Message ID.
     *
     * Checks to see that the Message ID is valid, doesn't contain any invalid,
     * and if it didn't use any archived Attributes.
     *
     * @param integer $id
     * @return boolean
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::call()
     *
     * @author Jake Lockwood <jakelockwood27@gmail.com>
     */
    public function validate_message( $id ) {
        $v = $this->call( "messageValidate", [ 'messageId' => $id ] );
        return ($v != false && ! isset( $v->validationResult->code ) ) ? true : false;
    }

    // UTILITY METHODS
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Validates a string to check whether or not it's an email address.
     *
     * @param string $string String to be evaluated
     * @return bool
     *
     * @access public
     * @since v1.0.0
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function is_valid_email( $string ) {
        $regex = '/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';

        return preg_match( $regex, $string ) ? true : false;
    }

    /**
     * Compiles DMC info array and uses render_info() to output a phpinfo()
     * style system information page..
     *
     * @return HTML dmcinfo page
     *
     * @access public
     * @since v1.0.0
     *
     * @uses DMC::$soap_url
     * @uses DMC::api_version()
     * @uses DMC::ecm_version()
     * @uses DMC::functions()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    public function dmcinfo() {
        $dmcinfo = [
            'System Information' => [
                'Status' => $this->api_version() ? 'Active' : 'Down',
                'SOAP URL' => $this->soap_url,
                'API Version' => $this->api_version(),
                'Build' => $this->ecm_version(),
                'Available API Functions' => $this->functions(),
            ],
        ];

        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"http://www.w3.org/1999/xhtml\">\n<head>\n<style type=\"text/css\">\nbody {background-color: #ffffff; color: #000000;}\nbody, td, th, h1, h2 {font-family: sans-serif;}\npre {margin: 0px; font-family: monospace;}\na:link {color: #000099; text-decoration: none; background-color: #ffffff;}\na:hover {text-decoration: underline;}\ntable {border-collapse: collapse;}\n.center {text-align: center;}\n.center table { margin-left: auto; margin-right: auto; text-align: left;}\n.center th { text-align: center !important; }\ntd, th { border: 1px solid #000000; font-size: 75%; vertical-align:center;}\nh1 {font-size: 150%;}\nh2 {font-size: 125%;}\n.p {text-align: left;}\n.e {background-color: #000000; font-weight: bold; color: #ffcc00; text-align: right;}\n.h {background-color: #000000; font-weight: bold; color: #ffcc00;}\n.v {background-color: #cccccc; color: #000000;}\n.vr {background-color: #cccccc; text-align: right; color: #000000;}\nimg {float: right; border: 0px; margin-top: 7px;}\nhr {width: 600px; background-color: #cccccc; border: 0px; height: 1px; color: #000000;}\n</style>\n<title>dmcinfo()</title><meta name=\"ROBOTS\" content=\"NOINDEX,NOFOLLOW,NOARCHIVE\" /></head>\n<body><div class=\"center\">    <table border=\"0\" cellpadding=\"5\" width=\"600\">\n<tr class=\"h\"><td>\n<a href=\"http://api.ecircle.com/\"><img border=\"0\" src=\"http://api.ecircle.com/fileadmin/system/img/global/teradata_w137.png\" alt=\"Teradata Logo\" /></a><h1 class=\"p\">Digital Messaging Center</h1>\n</td></tr></table><br />";
        foreach ( $dmcinfo as $name => $section ) {
            echo "<h3>$name</h3>\n<table border=\"0\" cellpadding=\"5\" width=\"600\">\n";
            foreach ( $section as $key => $val ) {
                if ( is_array( $val ) ) {
                    echo "<tr><td class=\"e\" valign=\"top\">$key</td><td class=\"v\"><ul>";
                    foreach ( $val as $v ) {
                        echo "<li>$v</li>";
                    }
                    echo "</ul></td></tr>\n";
                } elseif ( is_string( $key ) ) {
                    echo "<tr><td class=\"e\">$key</td><td class=\"v\">$val</td></tr>\n";
                } else {
                    echo "<tr><td class=\"e\">$val</td></tr>\n";
                }
            }
            echo "</table>\n";
        }
        echo "</div></body>";
    }

    // INTERNAL METHODS
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Cleans returned query objects into more easily usable format.
     *
     * @param obj $object Returned query item
     * @return obj stdClass
     *
     * @access private
     * @since v1.0.0
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    private function sanitize_attributes_object( $object ) {
        $return = new stdClass();

        if ( isset( $object->attributes ) ) {
            foreach ( $object->attributes as $att ) {

                $name = $att->name;
                if ( strpos( $name, '.' ) ) {
                    $var = explode( '.', $name );
                    $name = $var[1];
                }

                $return->{$name} = $att->value;
            }
        }

        return $return;
    }

    /**
     * Prepares attributes for upload to profile
     *
     * @param array $array Profile attributes array
     * @return obj stdClass
     *
     * @access private
     * @since v1.0.0
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    private function build_attributes_object( $array ) {
        $attributes = [ ];

        foreach ( $array as $name => $value ) {
            $attributes[] = [
                'name' => $name,
                'value' => $value,
            ];
        }

        return $attributes;
    }

    /**
     * Simplifies membership objects array to return only 'groupId' values.
     *
     * @param obj $object
     * @return array
     *
     * @access private
     * @since v1.0.0
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    private function sanitize_memberships_array( $object ) {
        $m = [ ];

        foreach ( $object as $o ) {
            if ( isset( $o->groupId ) ) {
                $m[] = $o->groupId;
            }
        }

        return $m;
    }

    /**
     * Initializes PHP SoapClient class and performs SOAP call to DMC server
     *
     * @param string  $function   API function name
     * @param array   $parameters (optional) API function parameters
     * @return obj SOAP response
     *
     * @access private
     * @since v1.0.0
     *
     * @uses DMC::$soap
     * @uses DMC::$fault_trace
     * @uses DMC::soap_drop()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    private function call( $function, $parameters = null ) {
        $reply = $this->soap->$function( $parameters );

        if ( is_soap_fault( $reply ) ) {
            if ( $this->fault_trace == true ) {
                $this->soap_drop( $reply, $function );
            }

            return false;
        }

        return $reply;
    }

    /**
     * Error handler.
     *
     * @param obj $reply SOAP fault object
     * @return void
     *
     * @access private
     * @since v1.0.0
     *
     * @uses DMC::display_fault()
     * @link http://goo.gl/jY9uqr
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    private function soap_drop( $reply ) {
        echo "Something has gone wrong.<br><br>";
        echo "<b>Fault Code&nbsp;&nbsp;=>&nbsp;&nbsp;</b>" . $reply->faultcode . "<br/>";
        echo "<b>Fault Type&nbsp;&nbsp;=>&nbsp;&nbsp;</b>";
        $this->display_fault( $reply->detail );
        echo "<br/><b>Fault Trace:&nbsp;&nbsp;=>&nbsp;&nbsp;</b><br/>";
        $this->display_fault( $reply->getTrace(), 1 );
    }

    /**
     * Recursively outputs error details for debugging.
     *
     * @param obj|array $fault SOAP fault object or array containing fault details
     * @param int $i Indents output
     * @return void
     *
     * @access private
     * @since v1.0.0
     *
     * @uses DMC::display_fault()
     *
     * @author Nick Silva <nick.silva@teradata.com>
     */
    private function display_fault( $fault, $i = 0 ) {

        foreach ( $fault as $key => $value ) {
            if ( ( is_string( $value ) || is_integer( $value ) || is_bool( $value ) ) && ( $value != '' && $value != '->') ) {
                echo str_repeat( '&nbsp;', ( $i * 8 ) ) . "<b>$key&nbsp;&nbsp;=></b>&nbsp;&nbsp;$value<br>";
            } elseif ( is_object( $value ) ) {
                echo str_repeat( '&nbsp;', ( $i ++ * 8 ) ) . "$key<br>";
                $this->display_fault( $value, $i );
            } elseif ( is_array( $value ) ) {
                $this->display_fault( $value, $i ++  );
            }
        }
    }

}

// End of class: DMC
// End of file: dmc.php
