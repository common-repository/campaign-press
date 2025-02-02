<?php
require_once 'class/base_classes.php';

define('CS_REST_CUSTOM_FIELD_TYPE_TEXT', 'Text');
define('CS_REST_CUSTOM_FIELD_TYPE_NUMBER', 'Number');
define('CS_REST_CUSTOM_FIELD_TYPE_MULTI_SELECTONE', 'MultiSelectOne');
define('CS_REST_CUSTOM_FIELD_TYPE_MULTI_SELECTMANY', 'MultiSelectMany');
define('CS_REST_CUSTOM_FIELD_TYPE_DATE', 'Date');
define('CS_REST_CUSTOM_FIELD_TYPE_COUNTRY', 'Country');
define('CS_REST_CUSTOM_FIELD_TYPE_USSTATE', 'USState');

/**
 * Class to access a lists resources from the create send API.
 * This class includes functions to create lists and custom fields,
 * along with accessing the subscribers of a specific list
 * @author tobyb
 *
 */
class CS_REST_Lists extends CS_REST_Wrapper_Base {

    /**
     * The base route of the lists resource.
     * @var string
     * @access private
     */
    var $_lists_base_route;

    /**
     * Constructor.
     * @param $list_id string The list id to access (Ignored for create requests)
     * @param $api_key string Your api key (Ignored for get_apikey requests)
     * @param $protocol string The protocol to use for requests (http|https)
     * @param $debug_level int The level of debugging required CS_REST_LOG_NONE | CS_REST_LOG_ERROR | CS_REST_LOG_WARNING | CS_REST_LOG_VERBOSE
     * @param $host string The host to send API requests to. There is no need to change this
     * @param $log CS_REST_Log The logger to use. Used for dependency injection
     * @param $serialiser The serialiser to use. Used for dependency injection
     * @param $transport The transport to use. Used for dependency injection
     * @access public
     */
    function CS_REST_Lists (
    $list_id,
    $api_key,
    $protocol = 'https',
    $debug_level = CS_REST_LOG_NONE,
    $host = 'api.createsend.com',
    $log = NULL,
    $serialiser = NULL,
    $transport = NULL) {
        	
        $this->CS_REST_Wrapper_Base($api_key, $protocol, $debug_level, $host, $log, $serialiser, $transport);
        $this->set_list_id($list_id);
    }

    /**
     * Change the list id used for calls after construction
     * @param $list_id
     * @access public
     */
    function set_list_id($list_id) {
        $this->_lists_base_route = $this->_base_route.'lists/'.$list_id.'/';
    }

    /**
     * Creates a new list based on the provided details.
     * Both the UnsubscribePage and the ConfirmationSuccessPage parameters are optional
     * @param string $client_id The client to create the campaign for
     * @param array $list_details The list details to use during creation.
     *     This array should be of the form
     *     array(
     *         'Title' => string The list title
     *         'UnsubscribePage' => string The page to redirect subscribers to when they unsubscribe
     *         'ConfirmedOptIn' => boolean Whether this list requires confirmation of subscription
     *         'ConfirmationSuccessPage' => string The page to redirect subscribers to when
     *             they confirm their subscription
     *     )
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be the ID of the newly created list
     */
    function create($client_id, $list_details) {
        return $this->post_request($this->_base_route.'lists/'.$client_id.'.json', $list_details);
    }

    /**
     * Updates the details of an existing list
     * Both the UnsubscribePage and the ConfirmationSuccessPage parameters are optional
     * @param string $client_id The client to create the campaign for
     * @param array $list_details The list details to use during creation.
     *     This array should be of the form
     *     array(
     *         'Title' => string The list title
     *         'UnsubscribePage' => string The page to redirect subscribers to when they unsubscribe
     *         'ConfirmedOptIn' => boolean Whether this list requires confirmation of subscription
     *         'ConfirmationSuccessPage' => string The page to redirect subscribers to when
     *             they confirm their subscription
     *     )
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be empty
     */
    function update($list_details) {
        return $this->put_request(trim($this->_lists_base_route, '/').'.json', $list_details);
    }

    /**
     * Creates a new custom field for the current list
     * @param array $custom_field_details The details of the new custom field.
     *     This array should be of the form
     *     array(
     *         'FieldName' => string The name of the new custom field
     *         'DataType' => string The data type of the new custom field
     *             This should be one of
     *             CS_REST_CUSTOM_FIELD_TYPE_TEXT
     *             CS_REST_CUSTOM_FIELD_TYPE_NUMBER
     *             CS_REST_CUSTOM_FIELD_TYPE_MULTI_SELECTONE
     *             CS_REST_CUSTOM_FIELD_TYPE_MULTI_SELECTMANY
     *             CS_REST_CUSTOM_FIELD_TYPE_DATE
     *             CS_REST_CUSTOM_FIELD_TYPE_COUNTRY
     *             CS_REST_CUSTOM_FIELD_TYPE_USSTATE
     *         'Options' => array<string> Valid options for either Multi-Optioned field data type
     *     )
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be the 
     * personalisation tag of the newly created custom field
     */
    function create_custom_field($custom_field_details) {
        return $this->post_request($this->_lists_base_route.'customfields.json', $custom_field_details);
    }

    /**
     * Updates the optios for the given multi-optioned custom field
     * @param string $key The personalisation tag of the field to update
     * @param array<string> $new_options The set of options to add to the custom field
     * @param boolean $keep_existing Whether to remove any existing options not contained in $new_options
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be empty
     */
    function update_field_options($key, $new_options, $keep_existing) {
        $options = array(
            'KeepExistingOptions' => $keep_existing,
            'Options' => $new_options
        );
        
        return $this->put_request($this->_lists_base_route.'customfields/'.rawurlencode($key).'/options.json', 
            $options);
    }

    /**
     * Deletes an existing list from the system
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be empty
     */
    function delete() {
        return $this->delete_request(trim($this->_lists_base_route, '/').'.json');
    }

    /**
     * Deletes an existing custom field from the system
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be empty
     */
    function delete_custom_field($key) {
        return $this->delete_request($this->_lists_base_route.'customfields/'.rawurlencode($key).'.json');
    }

    /**
     * Gets a list of all custom fields defined for the current list
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be an object of the form
     * array(
     *     {
     *         'FieldName' => The name of the custom field
     *         'Key' => The personalisation tag of the custom field
     *         'DataType' => The data type of the custom field
     *         'FieldOptions' => Valid options for a multi-optioned custom field
     *     }
     * )
     */
    function get_custom_fields() {
        return $this->get_request($this->_lists_base_route.'customfields.json');
    }

    /**
     * Gets a list of all segments defined for the current list
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be an object of the form
     * array(
     *     {
     *         'ListID' => The current list id
     *         'SegmentID' => The id of this segment
     *         'Title' => The title of this segment
     *     }
     * )
     */
    function get_segments() {
        return $this->get_request($this->_lists_base_route.'segments.json');
    }

    /**
     * Gets all active subscribers added since the given date
     * @param string $added_since The date to start getting subscribers from
     * @param int $page_number The page number to get
     * @param int $page_size The number of records per page
     * @param string $order_field The field to order the record set by ('EMAIL', 'NAME', 'DATE')
     * @param string $order_direction The direction to order the record set ('ASC', 'DESC')
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be an object of the form
     * {
     *     'ResultsOrderedBy' => The field the results are ordered by
     *     'OrderDirection' => The order direction
     *     'PageNumber' => The page number for the result set
     *     'PageSize' => The page size used
     *     'RecordsOnThisPage' => The number of records returned
     *     'TotalNumberOfRecords' => The total number of records available
     *     'NumberOfPages' => The total number of pages for this collection
     *     'Results' => array(
     *         {
     *             'EmailAddress' => The email address of the subscriber
     *             'Name' => The name of the subscriber
     *             'Date' => The date that the subscriber was added to the list
     *             'State' => The current state of the subscriber, will be 'Active'
     *             'CustomFields' => array (
     *                 {
     *                     'Key' => The personalisation tag of the custom field
     *                     'Value' => The value of the custom field for this subscriber
     *                 }
     *             )
     *         }
     *     )
     * }
     */
    function get_active_subscribers($added_since, $page_number = NULL, 
        $page_size = NULL, $order_field = NULL, $order_direction = NULL) {
            
        return $this->get_request_paged($this->_lists_base_route.'active.json?date='.urlencode($added_since), 
            $page_number, $page_size, $order_field, $order_direction);
    }

    /**
     * Gets all bounced subscribers who have bounced out since the given date
     * @param string $added_since The date to start getting subscribers from
     * @param int $page_number The page number to get
     * @param int $page_size The number of records per page
     * @param string $order_field The field to order the record set by ('EMAIL', 'NAME', 'DATE')
     * @param string $order_direction The direction to order the record set ('ASC', 'DESC')
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be an object of the form
     * {
     *     'ResultsOrderedBy' => The field the results are ordered by
     *     'OrderDirection' => The order direction
     *     'PageNumber' => The page number for the result set
     *     'PageSize' => The page size used
     *     'RecordsOnThisPage' => The number of records returned
     *     'TotalNumberOfRecords' => The total number of records available
     *     'NumberOfPages' => The total number of pages for this collection
     *     'Results' => array(
     *         {
     *             'EmailAddress' => The email address of the subscriber
     *             'Name' => The name of the subscriber
     *             'Date' => The date that the subscriber bounced out of the list
     *             'State' => The current state of the subscriber, will be 'Bounced'
     *             'CustomFields' => array (
     *                 {
     *                     'Key' => The personalisation tag of the custom field
     *                     'Value' => The value of the custom field for this subscriber
     *                 }
     *             )
     *         }
     *     )
     * }
     */
    function get_bounced_subscribers($bounced_since, $page_number = NULL, 
        $page_size = NULL, $order_field = NULL, $order_direction = NULL) {
            
        return $this->get_request_paged($this->_lists_base_route.'bounced.json?date='.urlencode($bounced_since), 
            $page_number, $page_size, $order_field, $order_direction);
    }

    /**
     * Gets all unsubscribed subscribers who have unsubscribed since the given date
     * @param string $added_since The date to start getting subscribers from
     * @param int $page_number The page number to get
     * @param int $page_size The number of records per page
     * @param string $order_field The field to order the record set by ('EMAIL', 'NAME', 'DATE')
     * @param string $order_direction The direction to order the record set ('ASC', 'DESC')
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be an object of the form
     * {
     *     'ResultsOrderedBy' => The field the results are ordered by
     *     'OrderDirection' => The order direction
     *     'PageNumber' => The page number for the result set
     *     'PageSize' => The page size used
     *     'RecordsOnThisPage' => The number of records returned
     *     'TotalNumberOfRecords' => The total number of records available
     *     'NumberOfPages' => The total number of pages for this collection
     *     'Results' => array(
     *         {
     *             'EmailAddress' => The email address of the subscriber
     *             'Name' => The name of the subscriber
     *             'Date' => The date that the subscriber was unsubscribed from the list
     *             'State' => The current state of the subscriber, will be 'Unsubscribed'
     *             'CustomFields' => array (
     *                 {
     *                     'Key' => The personalisation tag of the custom field
     *                     'Value' => The value of the custom field for this subscriber
     *                 }
     *             )
     *         }
     *     )
     * }
     */
    function get_unsubscribed_subscribers($unsubscribed_since, $page_number = NULL, 
        $page_size = NULL, $order_field = NULL, $order_direction = NULL) {
            
        return $this->get_request_paged($this->_lists_base_route.'unsubscribed.json?date='.urlencode($unsubscribed_since), 
            $page_number, $page_size, $order_field, $order_direction);
    }

    /**
     * Gets the basic details of the current list
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be an object of the form
     * {
     *     'ListID' => The id of the list
     *     'Title' => The title of the list
     *     'UnsubscribePage' => The page which subscribers are redirected to upon unsubscribing
     *     'ConfirmedOptIn' => Whether the list is Double-Opt In
     *     'ConfirmationSuccessPage' => The page which subscribers are
     *         redirected to upon confirming their subscription
     * }
     */
    function get() {
        return $this->get_request(trim($this->_lists_base_route, '/').'.json');
    }

    /**
     * Gets statistics for list subscriptions, deletions, bounces and unsubscriptions
     * @param $call_options
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be an object of the form
     * {
     *     'TotalActiveSubscribers'
     *     'NewActiveSubscribersToday'
     *     'NewActiveSubscribersYesterday'
     *     'NewActiveSubscribersThisWeek'
     *     'NewActiveSubscribersThisMonth'
     *     'NewActiveSubscribersThisYeay'
     *     'TotalUnsubscribes'
     *     'UnsubscribesToday'
     *     'UnsubscribesYesterday'
     *     'UnsubscribesThisWeek'
     *     'UnsubscribesThisMonth'
     *     'UnsubscribesThisYear'
     *     'TotalDeleted'
     *     'DeletedToday'
     *     'DeletedYesterday'
     *     'DeletedThisWeek'
     *     'DeletedThisMonth'
     *     'DeletedThisYear'
     *     'TotalBounces'
     *     'BouncesToday'
     *     'BouncesYesterday'
     *     'BouncesThisWeek'
     *     'BouncesThisMonth'
     *     'BouncesThisYear'
     * }
     */
    function get_stats() {
        return $this->get_request($this->_lists_base_route.'stats.json');
    }
}