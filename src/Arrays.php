<?php

namespace Cajudev;

use Cajudev\Interfaces\Set;
use Cajudev\Interfaces\Mixed;
use Cajudev\Interfaces\Sortable;

class Arrays implements \ArrayAccess, \IteratorAggregate, \Countable, Sortable, Set
{
    use \Cajudev\Traits\SetTrait;
    use \Cajudev\Traits\SortableTrait;
    use \Cajudev\Traits\ArrayAccessTrait;

    const LOOP_BREAK    = 'break';
    const LOOP_CONTINUE = 'continue';

    const KEY_NOTATION      = '/^\w+$/';
    const DOT_NOTATION      = '/(?<=\.|^)(?<key>\w+)(?=\.|$)/';
    const INTERVAL_NOTATION = '/^(?<start>\w+):(?<end>\w+)$/';

    protected $content;
    protected $length;
    protected $backup;

    /**
     * @param  mixed $content
     *
     * @return void
     */
    public function __construct($content = [])
    {
        $this->content = is_array($content) ? $content : $this->parseObject($content);
        $this->count();
    }

    /**
     * Transform all properties of a object into an associative array
     *
     * @param object $object
     *
     * @return array
     */
    private function parseObject($object)
    {
        if ($object instanceof static) {
            return $object->get();
        }

        if (!is_object($object)) {
            throw new \InvalidArgumentException('Argument must be an array or object');
        }

        $vars = (array) $object;
        return array_column(array_map(function($key, $value) {
            return [preg_replace('/.*\0(.*)/', '\1', $key), $value];
        }, array_keys($vars), $vars), 1, 0);
    }

    /**
     * Set the content of the array by reference
     *
     * @param  array $array
     *
     * @return self
     */
    public function setByReference(&$array = null)
    {
        $array = isset($array) ? $array : [];
        $this->content =& $array;
        $this->count();
        return $this;
    }

    /**
     * Count all elements of the array
     * 
     * @param int $mode
     *
     * @return int
     */
    public function count($mode = COUNT_NORMAL)
    {
        $this->length = count($this->content, $mode);
        return $this->length;
    }

    /**
     * Return the object iterator
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->content);
    }

    /**
     * Insert the values on the beginning of the array
     *
     * @param  mixed $values
     *
     * @return self
     */
    public function unshift(...$values)
    {
        array_unshift($this->content, ...$values);
        $this->increment(count($values));
        return $this;
    }

    /**
     * Insert the values on the final of the array
     *
     * @param  mixed $values
     *
     * @return self
     */
    public function push(...$values)
    {
        array_push($this->content, ...$values);
        $this->increment(count($values));
        return $this;
    }

    /**
     * Perform a simplified for loop
     *
     * @param  int     $i
     * @param  int     $add
     * @param  callable $function
     *
     * @return void
     */
    public function _for($i, $add, $function)
    {
        $keys   = array_keys($this->content);
        $count  = count($this->content);

        for ($i; ($add >= 0 ? $i < $count : $i >= 0); $i += $add) {
            $return = $function($keys[$i], $this->content[$keys[$i]]);
            switch ($return) {
                case self::LOOP_BREAK: break 2;
                case self::LOOP_CONTINUE; continue 2;
            }
        }
    }

    /**
     * Perform a foreach loop
     *
     * @param  callable $function
     *
     * @return void
     */
    public function each($function)
    {
        foreach ($this->content as $key => $value) {
            $return = $function($key, $value);
            switch ($return) {
                case self::LOOP_BREAK: break 2;
                case self::LOOP_CONTINUE; continue 2;
            }
        }
    }

    /**
     * Sum all values in the array
     *
     * @return mixed
     */
    public function sum()
    {
        return array_sum($this->content);
    }

    /**
     * Check if the value exist in the array
     * 
     * @param mixed $value
     *
     * @return bool
     */
    public function contains($value)
    {
        return in_array($value, $this->content);
    }

    /**
     * Applies the callback to all elements
     *
     * @param  callable $handle
     *
     * @return self
     */
    public function map($handle)
    {
        $keys = array_keys($this->content);
        $this->content = array_column(array_map($handle, $keys, $this->content), 1, 0);
        return $this;
    }

    /**
     * Filter the array using a callable function
     *
     * @param  callable $handle
     *
     * @return self
     */
    public function filter($handle)
    {
        $this->content = array_filter($this->content, $handle, ARRAY_FILTER_USE_BOTH);
        $this->count();
        return $this;
    }

    /**
     * Reduce the array to a single value
     *
     * @param  callable $handle
     *
     * @return self
     */
    public function reduce($handle)
    {
        $content = $this->content;
        $initial = array_shift($content);
        $result  = array_reduce($content, $handle, $initial);
        if (is_array($result)) {
            $result = new static($result);
        }
        return $result;
    }

    /**
     * Determine if a key is set and it's value is not null
     *
     * @param  mixed $key
     *
     * @return bool
     */
    public function _isset($key)
    {
        return isset($this[$key]);
    }

    /**
     * Determine if a key is not set
     *
     * @param  mixed $key
     *
     * @return bool
     */
    public function noset($key)
    {
        return !isset($this[$key]);
    }

    /**
     * Determine wheter a variable is empty
     *
     * @param  mixed $key
     *
     * @return bool
     */
    public function _empty($key)
    {
        return empty($this[$key]);
    }

    /**
     * Determine wheter a variable is not empty
     *
     * @param  mixed $key
     *
     * @return bool
     */
    public function filled($key)
    {
        return !empty($this[$key]);
    }

    /**
     * Unset a given variable
     *
     * @param  mixed $key
     *
     * @return void
     */
    public function _unset($key)
    {
        unset($this[$key]);
    }

    /**
     * Remove the first element from an array
     *
     * @return self
     */
    public function shift()
    {
        array_shift($this->content);
        $this->decrement();
        return $this;
    }

    /**
     * Remove the last element from an array
     *
     * @return self
     */
    public function pop()
    {
        array_pop($this->content);
        $this->decrement();
        return $this;
    }

    /**
     * Join array elements into string
     *
     * @return string
     */
    public function join($glue)
    {
        return implode($glue, $this->content);
    }

    /**
     * Exchange all keys with their associated values
     *
     * @return self
     */
    public function flip()
    {
        $this->content = array_flip($this->content);
        return $this;
    }

    /**
     * Return a object with all the keys of the array
     *
     * @return self
     */
    public function keys()
    {
        return new static(array_keys($this->content));
    }

    /**
     * Return a object with all the values of the array
     *
     * @return self
     */
    public function values()
    {
        return new static(array_values($this->content));
    }

    /**
     * Return the values from a single column
     *
     * @param  mixed $key
     * @param  mixed $index
     *
     * @return self
     */
    public function column($key, $index = null)
    {
        return new static(array_column($this->content, $key, $index));
    }
    
    /**
     * Split the array into chunks
     *
     * @param  int $size
     * @param  bool $preserve_keys
     *
     * @return self
     */
    public function chunk($size, $preserve_keys = null)
    {
        $this->content = array_chunk($this->content, $size, $preserve_keys);
        $this->count();
        return $this;
    }

    /**
     * Remove duplicated values
     *
     * @return self
     */
    public function unique()
    {
        $this->content = array_unique($this->content);
        $this->count();
        return $this;
    }

    /**
     * Merge all sublevels of the array into one
     *
     * @return self
     */
    public function merge()
    {
        $this->content = $this->reduce('array_merge')->get();
        $this->count();
        return $this;
    }

    /**
     * Reverse the order of the array
     *
     * @return self
     */
    public function reverse($preserve_keys = null)
    {
        $this->content = array_reverse($this->content, $preserve_keys);
        return $this;
    }

    /**
     * Return a key from a value in array if it exists
     */
    public function search($value, $strict = null)
    {
        return array_search($value, $this->content, $strict);
    }

    /**
     * Return the last element of the array
     *
     * @return void
     */
    public function last()
    {
        $value = end($this->content);
        reset($this->content);
        return is_array($value) ? new static($value) : $value;
    }

    /**
     * Change the case of all keys in the array to lower case
     *
     * @return self
     */
    public function lower()
    {
        $this->content = array_change_key_case($this->content, CASE_LOWER);
        return $this;
    }

    /**
     * Change the case of all keys in the array to upper case
     *
     * @return self
     */
    public function upper()
    {
        $this->content = array_change_key_case($this->content, CASE_UPPER);
        return $this;
    }

    /**
     * Get the value associated a given key or keys
     *
     * @param  mixed $keys
     *
     * @return mixed
     */
    public function get(...$keys)
    {
        $count = count($keys);

        if ($count === 0) {
            return $this->content;
        }

        if ($count === 1) {
            return isset($this[$keys[0]]) ? $this[$keys[0]] : null;
        }

        $return = [];
        foreach ($keys as $key) {
            $return[] = isset($this[$key]) ? $this[$key] : null;
        }
        return $return;
    }

    /**
     * Insert a value in a associated key or keys
     *
     * @param  mixed $keys
     *
     * @return mixed
     */
    public function set($value, ...$keys)
    {
        $count = count($keys);

        if ($count === 0) {
            $this[] = $value;
            return $this;
        }

        if ($count === 1) {
            $this[$keys[0]] = $value;
            return $this;
        }

        foreach ($keys as $key) {
            $this[$key] = $value;
        }

        return $this;
    }

    /**
     * Create a backup of the content of array
     */   
    public function backup() {
        $this->backup = $this->content;
    }

    /**
     * Restore the data of the array
     */
    public function restore() {
        $this->content = $this->backup;
        $this->backup  = null;
        $this->count();
    }

    /**
     * Increment the length property
     *
     * @return void
     */
    private function increment($value = null) {
        $this->length += isset($value) ? $value : 1;
    }

    /**
     * Decrement the length property
     *
     * @return void
     */
    private function decrement($value = null) {
        $this->length -= isset($value) ? $value : 1;
    }

 // ================================================================= //
    // ======================= MAGIC METHODS =========================== //
    // ================================================================= //

    public function __get($property) {
        if ($property === 'length') {
            return $this->length;
        }
        return $this[$property] ?? null;
    }

    public function __set($property, $value) {
        if ($property === 'length') {
            throw new \InvalidArgumentException('length property is readonly');
        }
        $this[$property] = $value;
    }

    public function __isset($property) {
        return isset($this->content[$property]);
    }

    public function __unset($property) {
        unset($this->content[$property]);
    }

    public function __toString()
    {
        return json_encode($this->content, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    // ================================================================= //
    // ======================= STATIC METHODS =========================== //
    // ================================================================= //

    /**
     * Verify whether a element is an Arrays object
     *
     * @param  mixed $array
     *
     * @return bool
    */
    public static function isArrays($object)
    {
        return $object instanceof static;
    }

    /**
     * Combine two arrays, using the first for keys and the second for values
     *
     * @param  mixed $keys
     * @param  mixed $values
     *
     * @return self
     */
    public static function combine($keys, $values)
    {
        if ($keys instanceof static) {
            $keys = $keys->get();
        }

        if ($values instanceof static) {
            $values = $values->get();
        }
        
        return new static(array_combine($keys, $values));
    }
}
