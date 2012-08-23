<?php
defined('SYSPATH') or die('No direct script access.');


/**
 * Description of Model_Data
 *
 * @author Martin Zoellner <ragchuck at gmail.com>
 */
class Model_Data extends ORM
{

    protected $_table_name = 'data_actual';

    protected $_table_columns = array(
        'id' => 'int(11)',
        'ch_date' => 'varchar(8)',
        'ch_datetime' => 'datetime',
        'ch_list' => 'varchar(50)',
        'ch_serial' => 'int(12)',
        'ch_key' => 'varchar(50)',
        'ch_period' => 'int(11)',
        'mean' => 'double',
        'min' => 'double',
        'max' => 'double',
        'first' => 'double',
        'last' => 'double',
    );


    public function to_array()
    {
        return array(
            (double)strtotime($this->ch_datetime) * 1000,
            (float)$this->mean,
        );
    }

    public function to_array_ohlc()
    {
        return array(
            (double)strtotime($this->ch_datetime) * 1000,
            (float)$this->first, //open
            (float)$this->max, //high
            (float)$this->min, //low
            (float)$this->last, //close
        );
    }

    public function rules() //rules
    {
        return array(
            'ch_key' => array(
                array('Valid::not_empty'),
                array('Model_Data::check_measures', array(':field', ':value', ':model', ':validation'))
            ),
            'ch_datetime' => array(
                array('Valid::Date')
            )
        );
    }

    /**
     * @static
     * @param string $field
     * @param string $value
     * @param Model $model
     * @param Validation $validation
     */
    public static function check_measures($field, $value, $model, $validation)
    {
        if ($value == 'E-Total' AND $model->min == 0) {
            $validation->error($field, "E-Total's MIN cannot be zero.");
        }
    }


    /*
    public function insert_query() {
        // get the columns
        $columns = array_keys($this->_table_columns);
        // build the insert sql
        return DB::insert($this->_table_name, $columns)->values($this->colonized_columns());
    }

    /**
     * returns colonized columns  ( :column1, :column2, ...)
     * @return array
     *//*
    public function colonized_columns() {
        $columns = array_keys($this->_table_columns);
        Arr::colonize($columns);
        return $columns;
    }
*/


}