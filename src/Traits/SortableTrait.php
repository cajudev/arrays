<?php

namespace Cajudev\Traits;

use Cajudev\Arrays;

trait SortableTrait
{
    /**
     * Sort the array
     *
     * @return self
     */
    public function sort()
    {
        sort($this->content);
        return $this;
    }

    /**
     * Sort the array in reverse order
     *
     * @return self
     */
    public function rsort()
    {
        rsort($this->content);
        return $this;
    }

    /**
     * Sort the array and maintain index association
     *
     * @return self
     */
    public function asort()
    {
        asort($this->content);
        return $this;
    }

    /**
     * Sort the array in reverse order and maintain index association
     *
     * @return self
     */
    public function arsort()
    {
        arsort($this->content);
        return $this;
    }
    
    /**
     * Sort the array by key
     *
     * @return self
     */
    public function ksort()
    {
        ksort($this->content);
        return $this;
    }

    /**
     * Sort the array by key in reverse order
     *
     * @return self
     */
    public function krsort()
    {
        krsort($this->content);
        return $this;
    }
}