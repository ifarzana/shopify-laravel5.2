<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Models as Models;

trait SearchableTrait
{
    /**
     * Default join type
     *
     * @var string
     */
    protected $joint_type = 'left';

    /**
     * Search scope
     *
     * @param $query Builder
     * @param $search_by string
     *
     * @return object
     */
    public function scopeSearch($query,  $search_by)
    {
        /*SELECT THE COLUMNS*/
        $this->selectColumns($query);

        $columns = $this->getAllColumns();

        if(!empty($columns)) {
            foreach ($columns as $column) {

                $query = $query->orWhere($column, 'like', '%'.$search_by.'%');
            }
        }

        /*MAKE THE JOINS*/
        $this->makeJoins($query);

        /*MAKE THE GROUP BY*/
        $this->makeGroupBy($query);

        return $query;
    }

    /**
     * Order by function
     *
     * @param $paginationData array
     * @return string
     */
    protected function getOrderBy($paginationData = array())
    {
        if( (isset($paginationData['order_by'])) && (isset($paginationData['order'])) ) {
            return $this->table.'.'.$paginationData['order_by'];
        }else{
            return $this->table.'.'.$this->default_order;
        }
    }

    /**
     * Return the joins from the model
     *
     * @return array
     */
    protected function getJoins()
    {
        $joins = array();

        if( (isset($this->joins)) AND (is_array($this->joins)) ) {
            $joins = $this->joins;
        }

        return $joins;
    }

    /**
     * Return the columns of the table
     *
     * @param $table string
     * @return array
     */
    protected function getColumns($table)
    {
        return DB::connection($this->connection)->getSchemaBuilder()->getColumnListing($table);
    }

    /**
     * Return an array with columns in format table_name.column_name
     *
     * @return array
     */
    protected function getAllColumns()
    {
        /*Main columns*/
        $array = $this->getColumns($this->table);

        if(!empty($array))
        {
            foreach ($array as $key => $value) {
                $array[$key] = $this->table.'.'.$value;
            }

        }

        /*Concat*/
        if(!empty($this->concat)) {

            foreach ($this->concat as $concat) {

                if(count($concat['columns']) > 0) {

                    $c = '';

                    $count = 0;

                    $concat_count = count($concat['columns']);

                    foreach ($concat['columns'] as $column) {

                        $count++;

                        if ($count == $concat_count) {
                            $c.= $column;
                        } else {
                            $c.= $column . $concat['delimiter'];
                        }

                    }

                    $array[] = DB::raw("CONCAT(".$c.")");

                }

            }

        }

        /*Joins columns*/
        $used_tables = array();

        if( (isset($this->joins)) AND (is_array($this->joins)) AND (!empty($this->joins)) ) {

            foreach ($this->joins as $join) {

                if(isset($join['table'])) {

                    $table_name = $join['table'];

                    $columns = $this->getColumns($table_name);

                    if(isset($used_tables[$table_name])) {
                        $table_name = 'alias_'.$table_name;
                    }

                    foreach ($columns as $column) {
                        $array[] = $table_name.'.'.$column;
                    }

                    $used_tables[$table_name] = $table_name;
                }

            }

        }

        return $array;
    }

    /**
     * Perform an select with all the columns from the model's table + the columns from the joins
     *
     * @param $query Builder
     * @return void
     */
    protected function selectColumns(Builder $query)
    {
        $array = $this->getColumns($this->table);

        foreach ($array as $key => $value) {
            $array[$key] = $this->table.'.'.$value;
        }

        $columns_to_select_from_joins = $this->getColumnsToSelectFromJoins();

        if(!empty($columns_to_select_from_joins)) {
            foreach ($columns_to_select_from_joins as $col => $as) {

                $array[] = $col." as " . $as;
            }
        }

        /*Concat*/
        if(!empty($this->concat)) {

            foreach ($this->concat as $concat) {

                if(count($concat['columns']) > 0) {

                    $c = '';

                    $count = 0;

                    $concat_count = count($concat['columns']);

                    foreach ($concat['columns'] as $column) {

                        $count++;

                        if ($count == $concat_count) {
                            $c.= $column;
                        } else {
                            $c.= $column . $concat['delimiter'];
                        }

                    }

                    $array[] = DB::raw("CONCAT(".$c.")");

                }

            }

        }

        $query->select($array);
    }

    /**
     * Return an array with columns in format table_name.column_name from the joins
     *
     * @return array
     */
    protected function getColumnsToSelectFromJoins()
    {
        $array = array();

        $joins = $this->getJoins();

        if(!empty($joins)) {
            foreach ($joins as $join) {

                if(isset($join['table'])) {
                    $table_name = $join['table'];

                    if(isset($join['columns'])) {

                        foreach ($join['columns'] as $key => $as) {
                            $array[$table_name.'.'.$key] = $as;
                        }

                    }
                }

            }
        }

        return $array;
    }

    /**
     * Perform group by operation $group_by from the model
     *
     * @param $query Builder
     * @return void
     */
    protected function makeGroupBy(Builder $query)
    {
        if($this->group_by != null) {
           $query->groupBy($this->group_by);
        }
    }

    /**
     * Perform join operations based on the $joins array from the model
     *
     * @param $query Builder
     * @return void
     */
    protected function makeJoins(Builder $query)
    {
        $joins = $this->getJoins();

        $used_tables = array();

        if(!empty($joins)) {
            foreach ($joins as $join) {

                if(isset($join['type'])){
                    $type = $join['type'];
                }else{
                    $type = $this->joint_type;
                }

                if(isset($join['table'])) {

                    $table_name = $join['table'];

                    if( (isset($join['rel']['one'])) AND (isset($join['rel']['two'])) ) {

                        if(isset($used_tables[$table_name])) {
                            $table_name = $table_name. " as " . 'alias_'.$table_name;
                        }

                        $query->join($table_name, $join['rel']['one'], '=', $join['rel']['two'] , $type);

                    }

                    $used_tables[$table_name] = $table_name;

                }


            }
        }

    }


}