<?php

return array(
      'navigation' => array(
            'main' => array(
                  array(
                        'href' => '#dashboard',
                        'text' => __("Dashboard")
                  ),
                  array(
                        'href' => '#profile',
                        'text' => __("System profile"),
                        '_acl' => 'xyz'
                  )
            ),
            'meta' => array(
                  
            )
      )
);