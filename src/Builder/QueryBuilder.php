<?php
namespace Midnite81\Salesforce\Builder;

use Midnite81\Salesforce\Model\Model;

class QueryBuilder
{
    /**
     * Select columns
     *
     * @var array
     */
    protected $select = [];

    /**
     * Table From
     * @var string
     */
    protected $from = '';

    /**
     * WHERE conditions
     *
     * @var array
     */
    protected $wheres = [];

    /**
     * @var string
     */
    protected $limit = '';

    /**
     * @var null
     */
    protected $model;

    public function __construct($model = null)
    {
        $this->model = $model;
        if (is_object($model)) {
            $this->setFrom($model->getObjectName());
        }
    }

    public function get()
    {
        if ($this->model instanceof Model) {
            return $this->model->executeQuery($this, false);
        }
    }

    public function first()
    {
        if ($this->model instanceof Model) {
            return $this->model->executeQuery($this, true);
        }
    }

    /**
     * Get Selects
     *
     * @return string
     */
    public function getSelect(): string
    {
        return implode(', ', $this->select);
    }

    /**
     * @param array $select
     * @return QueryBuilder
     */
    public function setSelect(array $select)
    {
        $this->select = $select;
        return $this;
    }

    /**
     * Alias for setSelect
     *
     * @param array $select
     * @return QueryBuilder
     */
    public function select(array $select)
    {
        return $this->setSelect($select);
    }

    /**
     * Get From
     *
     * @return string
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * @param string $from
     * @return QueryBuilder
     */
    public function setFrom($from)
    {
        $this->from = $from;
        return $this;
    }

    /**
     * Alias for setFrom
     *
     * @param string $from
     * @return QueryBuilder
     */
    public function from(string $from)
    {
        return $this->setFrom($from);
    }

    /**
     * Get Where
     *
     * @return string
     */
    public function getWhere(): string
    {
        $output = [];

        if (! empty($this->wheres)) {
            foreach($this->wheres as $where) {
                $output[] = $where['boolean'] . ' ' . $where['column'] . ' ' . $where['operator'] . ' ' . $this->formatValue($where['value']);
            }
        }

        return $this->removeLeadingBoolean(implode(' ', $output));
    }

    /**
     * @param        $column
     * @param null   $operator
     * @param null   $value
     * @param string $boolean
     * @return QueryBuilder
     */
    public function setWhere($column, $operator = null, $value = null, $boolean = 'and')
    {
        if (is_array($column)) {
            return $this->formatWhereArray($column);
        }

        if (func_num_args() == 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = compact(
            'column', 'operator', 'value', 'boolean'
        );

        return $this;
    }

    /**
     * Alias for where
     *
     * @return QueryBuilder
     */
    public function where()
    {
        return $this->setWhere(...func_get_args());
    }


    /**
     * Return the query builder to SQL
     *
     * @return string
     */
    public function toSql()
    {
        $buildQuery = [
            'SELECT ' . $this->getSelect(),
            'FROM ' . $this->getFrom(),
        ];

        if (! empty($this->getWhere())) {
            $buildQuery[] = 'WHERE ' . $this->getWhere();
        }
        if (! empty($this->getLimit())) {
            $buildQuery[] = 'LIMIT ' . $this->getLimit();
        }

        return implode(" ", $buildQuery);
    }

    /**
     * @return string
     */
    public function getLimit(): string
    {
        return $this->limit;
    }

    /**
     * @param string $limit
     * @return QueryBuilder
     */
    public function setLimit(string $limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Limit alias
     *
     * @param string $limit
     * @return QueryBuilder
     */
    public function limit(string $limit)
    {
        return $this->setLimit($limit);
    }

    /**
     * Format the value
     *
     * @param $value
     * @return string
     */
    protected function formatValue($value)
    {

        if (is_bool($value)) {
            return ($value) ? 'true' : 'false';
        }
        if (is_string($value)) {
            return "'" . $value . "'";
        }
        return $value;
    }

    /**
     * Remove the leading boolean from a statement.
     *
     * @param  string  $value
     * @return string
     */
    protected function removeLeadingBoolean($value)
    {
        return preg_replace('/and |or /i', '', $value, 1);
    }

    protected function formatWhereArray($array)
    {
        if (! empty($array)) {
            foreach($array as $key=>$value) {
                $this->where($key, '=', $value);
            }
        }

        return $this;
    }
}