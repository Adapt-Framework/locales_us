<?php


/* Prevent Direct Access */
defined('ADAPT_STARTED') or die;

$adapt = $GLOBALS['adapt'];

$sql = $adapt->data_source->sql;

/* Add the new data types */
$data_types = array(
    /*
     * Phone numbers
     */
    array(
        'bundle_name' => 'locales_us',
        'name' => 'us_phone',
        'based_on_data_type' => 'varchar(24)',
        'validator' => 'uk_phone',
        'formatter' => 'uk_phone',
        'unformatter' => 'phone',
        'datetime_format' => null,
        'max_length' => null,
        'date_created' => null
    ),
    /*
     * Dates and Times
     */
    array(
        'bundle_name' => 'locales_us',
        'name' => 'us_date',
        'based_on_data_type' => 'date',
        'validator' => 'date',
        'formatter' => null,
        'unformatter' => null,
        'datetime_format' => 'm/d/Y',
        'max_length' => null,
        'date_created' => null
    ),
    array(
        'bundle_name' => 'locales_us',
        'name' => 'us_time',
        'based_on_data_type' => 'time',
        'validator' => 'time',
        'formatter' => null,
        'unformatter' => null,
        'datetime_format' => 'g:ia',
        'max_length' => null,
        'date_created' => null
    ),
    array(
        'bundle_name' => 'locales_us',
        'name' => 'us_datetime',
        'based_on_data_type' => 'datetime',
        'validator' => 'datetime',
        'formatter' => null,
        'unformatter' => null,
        'datetime_format' => 'm/d/Y g:ia',
        'max_length' => null,
        'date_created' => null
    ),
    /*
     * Address types
     */
    array(
        'bundle_name' => 'locales_us',
        'name' => 'us_zipcode',
        'based_on_data_type' => 'varchar(12)',
        'validator' => 'us_zipcode',
        'formatter' => 'us_zipcode',
        'unformatter' => 'us_zipcode',
        'datetime_format' => null,
        'max_length' => 12,
        'date_created' => null
    ),
    array(
        'bundle_name' => 'locales_us',
        'name' => 'us_state',
        'based_on_data_type' => "enum('Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont','Virginia','Washington','West Virginia','Wisconsin','Wyoming')",
        'validator' => 'us_state',
        'formatter' => 'us_state',
        'unformatter' => 'us_state',
        'datetime_format' => null,
        'max_length' => 64,
        'date_created' => null
    )
);

/* Set the data types */
$adapt->data_source->data_types = array_merge($adapt->data_source->data_types, $data_types);

/* Add the new types to the data_type table */
foreach($data_types as &$data_type){
    $keys = array_keys($data_type);
    foreach($keys as $key){
        if ($key == 'date_created'){
            $data_type['date_created'] = new \frameworks\adapt\sql('now()');
        }elseif(is_null($data_type[$key])){
            $data_type[$key] = new \frameworks\adapt\sql('null');
        }
    }
}

$sql->insert_into('data_type', array_keys($data_types[0]));
foreach($data_types as $type) $sql->values(array_values($type));
$sql->execute();

/*
 * We need to set the data sources' data types to
 * null to force it to reload them because the
 * current copy is missing the ids
 */
$this->data_source->data_types = null;

/*
 * Get the data types from the data source
 */

$uk_date = $adapt->data_source->get_data_type('uk_date');
$uk_datetime = $adapt->data_source->get_data_type('uk_datetime');
$uk_time = $adapt->data_source->get_data_type('uk_time');

/*
 * Add the country
 */

$country_id = $sql->insert_into(
    'country',
    array(
        'bundle_name',
        'label',
        'date_data_type_id',
        'time_data_type_id',
        'datetime_data_type_id',
        'date_created',
        'date_modified'
    )
)->values(
    array(
        'locales_uk',
        'United Kingdom',
        $uk_date['data_type_id'],
        $uk_time['data_type_id'],
        $uk_datetime['data_type_id'],
        $adapt->data_source->sql('now()'),
        $adapt->data_source->sql('now()')
    )
)->execute()
->id();


/*
 * Add the phone types
 */
$home = $adapt->data_source->get_data_type('uk_phone_home');
$mobile = $adapt->data_source->get_data_type('uk_phone_mobile');
$work = $adapt->data_source->get_data_type('uk_phone');


$sql->insert_into('country_phone_data_type',
    array(
        'country_id',
        'data_type_id',
        'label',
        'date_created',
        'date_modified'
    )
)->values(
    array(
        $country_id,
        $home['data_type_id'],
        'Home',
        $adapt->data_source->sql('now()'),
        $adapt->data_source->sql('now()')
    )
)->values(
    array(
        $country_id,
        $mobile['data_type_id'],
        'Mobile',
        $adapt->data_source->sql('now()'),
        $adapt->data_source->sql('now()')
    )
)->values(
    array(
        $country_id,
        $home['data_type_id'],
        'Home',
        $adapt->data_source->sql('now()'),
        $adapt->data_source->sql('now()')
    )
)->execute();

/*
 * Add the address format
 */

$sql->insert_into('country_address_format',
    array(
        'country_id',
        'priority',
        'name',
        'label',
        'data_type_id',
        'max_length',
        'date_created',
        'date_modified'
    )
)->values(
    array(
        $country_id,
        1,
        'flat_details',
        'Flat details',
        $adapt->data_source->get_data_type_id('varchar'),
        16,
        $adapt->data_source->sql('now()'),
        $adapt->data_source->sql('now()')
    )
)->values(
    array(
        $country_id,
        2,
        'buliding_name',
        'House / Building name',
        $adapt->data_source->get_data_type_id('varchar'),
        32,
        $adapt->data_source->sql('now()'),
        $adapt->data_source->sql('now()')
    )
)->values(
    array(
        $country_id,
        3,
        'building_number',
        'Building number',
        $adapt->data_source->get_data_type_id('varchar'),
        16,
        $adapt->data_source->sql('now()'),
        $adapt->data_source->sql('now()')
    )
)->values(
    array(
        $country_id,
        4,
        'street',
        'Street',
        $adapt->data_source->get_data_type_id('varchar'),
        64,
        $adapt->data_source->sql('now()'),
        $adapt->data_source->sql('now()')
    )
)->values(
    array(
        $country_id,
        5,
        'district',
        'District',
        $adapt->data_source->get_data_type_id('varchar'),
        64,
        $adapt->data_source->sql('now()'),
        $adapt->data_source->sql('now()')
    )
)->values(
    array(
        $country_id,
        6,
        'city',
        'Town / City',
        $adapt->data_source->get_data_type_id('varchar'),
        64,
        $adapt->data_source->sql('now()'),
        $adapt->data_source->sql('now()')
    )
)->values(
    array(
        $country_id,
        7,
        'county',
        'county',
        $adapt->data_source->get_data_type_id('varchar'),
        64,
        $adapt->data_source->sql('now()'),
        $adapt->data_source->sql('now()')
    )
)->values(
    array(
        $country_id,
        8,
        'postcode',
        'Postcode',
        $adapt->data_source->get_data_type_id('uk_postcode'),
        12,
        $adapt->data_source->sql('now()'),
        $adapt->data_source->sql('now()')
    )
);


//TODO: If this is the only locales file installed then we need to set the default settings in the 'locales' bundle




?>