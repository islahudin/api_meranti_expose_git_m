<?php
class FetchResult
{
    public $count;
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
        $this->count = count($data);
    }
}
