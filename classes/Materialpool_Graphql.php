<?php

class Materialpool_Graphql
{
    function __construct()
    {
        add_action('graphql_register_types', [$this, 'register_types']);
        add_filter('graphql_connection_query_args', [$this, 'connection_query_args'], 10, 3 );

    }

    public function connection_query_args( $query_args, \WPGraphQL\Data\Connection\AbstractConnectionResolver $connection, $input_args )
    {


        $field_selection = $connection->getInfo()->getFieldSelection( 2 );

        if ( ! isset( $field_selection['pageInfo']['total'] ) ) {
            return $query_args;
        }

        if ( $connection->get_query() instanceof \WP_Query ) {
            $query_args['no_found_rows'] = false;
        }

        return  $query_args;
    }


    public function register_types()
    {
        register_graphql_field( 'PageInfo', 'total', [
            'type' => 'Int',
            'description' => __( 'The total number of records found for the connection', 'wp-graphql' ),
        ]);
    }
}
new Materialpool_Graphql();
