<?php

/*
 * DeskPRO (r) has been developed by DeskPRO Ltd. https://www.deskpro.com/
 * a British company located in London, England.
 *
 * All source code and content Copyright (c) 2017, DeskPRO Ltd.
 *
 * The license agreement under which this software is released
 * can be found at https://www.deskpro.com/eula/
 *
 * By using this software, you acknowledge having read the license
 * and agree to be bound thereby.
 *
 * Please note that DeskPRO is not free software. We release the full
 * source code for our software because we trust our users to pay us for
 * the huge investment in time and energy that has gone into both creating
 * this software and supporting our customers. By providing the source code
 * we preserve our customers' ability to modify, audit and learn from our
 * work. We have been developing DeskPRO since 2001, please help us make it
 * another decade.
 *
 * Like the work you see? Think you could make it better? We are always
 * looking for great developers to join us: http://www.deskpro.com/jobs/
 *
 * ~ Thanks, Everyone at Team DeskPRO
 */

namespace Deskpro\API;

/**
 * Stores the response from the API.
 */
class APIResponse implements APIResponseInterface
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * @var array
     */
    protected $linked = [];

    /**
     * @var int
     */
    protected $it = 0;

    /**
     * @var int
     */
    protected $count = 0;

    /**
     * Constructor
     * 
     * @param array $data
     * @param array $meta
     * @param array $linked
     */
    public function __construct(array $data, array $meta, array $linked)
    {
        $this->data   = $data;
        $this->meta   = $meta;
        $this->linked = $linked;
        if (is_array($data)) {
            $this->count = count($data);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinked()
    {
        return $this->linked;
    }

    /**
     * Implements ArrayAccess::offsetSet
     * 
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    /**
     * Implements ArrayAccess::offsetExists
     * 
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * Implements ArrayAccess::offsetUnset
     * 
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * Implements ArrayAccess::offsetGet
     * 
     * @param mixed $offset
     * @return null
     */
    public function offsetGet($offset)
    {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

    /**
     * Implements Iterator::rewind
     */
    public function rewind()
    {
        $this->it = 0;
    }

    /**
     * Implements Iterator::current
     * 
     * @return mixed
     */
    public function current()
    {
        return $this->data[$this->it];
    }

    /**
     * Implements Iterator::key
     * 
     * @return int
     */
    public function key()
    {
        return $this->it;
    }

    /**
     * Implements Iterator::next
     */
    public function next()
    {
        ++$this->it;
    }

    /**
     * Implements Iterator::valid
     * 
     * @return bool
     */
    public function valid()
    {
        return isset($this->data[$this->it]);
    }

    /**
     * Implements Countable::count
     * 
     * @return int
     */
    public function count()
    {
        return $this->count;
    }
}
