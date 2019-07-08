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

    const BREAK    = 'break';
    const CONTINUE = 'continue';

    const KEY_NOTATION      = '/^[^.:]+$/';
    const DOT_NOTATION      = '/(?<=\.|^)(?<key>[^.:]+)(?=\.|$)/';
    const INTERVAL_NOTATION = '/^(?<start>\d+):(?<end>\d+)$/';

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
    private function parseObject($object): array
    {
        if ($object instanceof static) {
            return $object->get();
        }

        if (!is_object($object)) {
            throw new \InvalidArgumentException('Invalid source type [' . gettype($object) . ']');
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
    public function setByReference(array &$array = null): self
    {
        $array = $array ?? [];
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
    public function count(int $mode = COUNT_NORMAL): int
    {
        if ($mode === COUNT_RECURSIVE) {
            return count($this->content, $mode);
        }

        $this->length = count($this->content);
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
    public function unshift(...$values): self
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
    public function push(...$values): self
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
    public function for(int $i, int $add, callable $function)
    {
        $keys   = array_keys($this->content);
        $count  = count($this->content);

        for ($i; ($add >= 0 ? $i < $count : $i >= 0); $i += $add) {
            $return = $function($keys[$i], $this->content[$keys[$i]]);
            switch ($return) {
                case self::BREAK: break 2;
                case self::CONTINUE; continue 2;
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
    public function each(callable $function)
    {
        foreach ($this->content as $key => $value) {
            $return = $function($key, $value);
            switch ($return) {
                case self::BREAK: break 2;
                case self::CONTINUE; continue 2;
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
    public function contains($value): bool
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
    public function map(callable $handle): self
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
    public function filter(callable $handle): self
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
    public function reduce(callable $handle)
    {
        $content = $this->content;
        $initial = array_shift($content);
        $result  = array_reduce($content, $handle, $initial);
        return $this->return($result);
    }

    /**
     * Determine if a key is set and it's value is not null
     *
     * @param  mixed $key
     *
     * @return bool
     */
    public function isset($key): bool
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
    public function noset($key): bool
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
    public function empty($key): bool
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
    public function filled($key): bool
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
    public function unset($key)
    {
        unset($this[$key]);
    }

    /**
     * Remove the first element from an array, and return the removed value
     *
     * @return mixed
     */
    public function shift()
    {
        $value = array_shift($this->content);
        $this->decrement();
        return $this->return($value);
    }

    /**
     * Remove the last element from an array, and return the removed value
     *
     * @return mixed
     */
    public function pop()
    {
        $value = array_pop($this->content);
        $this->decrement();
        return $this->return($value);
    }

    /**
     * Join array elements into string
     *
     * @return string
     */
    public function join(string $glue)
    {
        return implode($glue, $this->content);
    }

    /**
     * Exchange all keys with their associated values
     *
     * @return self
     */
    public function flip(): self
    {
        $this->content = array_flip($this->content);
        return $this;
    }

    /**
     * Return a object with all the keys of the array
     *
     * @return self
     */
    public function keys(): self
    {
        return new static(array_keys($this->content));
    }

    /**
     * Return a object with all the values of the array
     *
     * @return self
     */
    public function values(): self
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
    public function column($key, $index = null): self
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
    public function chunk(int $size, bool $preserve_keys = false): self
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
    public function unique(): self
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
    public function merge(): self
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
    public function reverse($preserve_keys = null): self
    {
        $this->content = array_reverse($this->content, $preserve_keys);
        return $this;
    }

    /**
     * Return a key from a value in array if it exists
     */
    public function search($value, bool $strict = null)
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
        return $this->return($value);
    }

    /**
     * Change the case of all keys in the array to lower case
     *
     * @return self
     */
    public function lower(): self
    {
        $lower = function(&$array) use (&$lower) {
            $array = array_change_key_case($array, CASE_LOWER);
            foreach ($array as $key => $value)
                if (is_array($value)) $lower($array[$key]);
        };
        $lower($this->content);
        return $this;
    }

    /**
     * Change the case of all keys in the array to upper case
     *
     * @return self
     */
    public function upper(): self
    {
        $upper = function(&$array) use (&$upper) {
            $array = array_change_key_case($array, CASE_UPPER);
            foreach ($array as $key => $value)
                if (is_array($value)) $upper($array[$key]);
        };
        $upper($this->content);
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
            return $this[$keys[0]] ?? null;
        }

        $last = function($key) {
            return array_slice(explode('.', $key), -1, 1)[0];
        };

        $return = new static();
        foreach ($keys as $key) {
            $return[$last($key)] = $this[$key] ?? null;
        }
        return $return;
    }

    /**
     * Insert a value in a associated key
     *
     * @param  string $key
     * $param  mixed $value
     *
     * @return mixed
     */
    public function set($value, string $key)
    {
        $this[$key] = $value;
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
     * Return a new object if value is an object, else return the value
     *
     * @return mixed
     */
    private function return($content) {
        return is_array($content) ? new static($content) : $content;
    }

    /**
     * Increment the length property
     *
     * @return void
     */
    private function increment(int $value = 1) {
        $this->length += $value;
    }

    /**
     * Decrement the length property
     *
     * @return void
     */
    private function decrement(int $value = 1) {
        $this->length -= $value;
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
    public static function isArrays($object): bool
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
    public static function combine($keys, $values): self
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
