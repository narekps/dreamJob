<?php

namespace App\Form;

/**
 * Class SaveResult
 * @package App\Form
 */
class SaveResult implements SaveResultInterface
{

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $msg;

    /**
     * @var boolean
     */
    protected $success = false;

    public function __construct($success = false, $title = '', $msg = '')
    {
        $this->setSuccess($success);

        if (!empty($title)) {
            $this->setTitle($title);
        }
        if (!empty($msg)) {
            $this->setMessage($msg);
        }
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->msg;
    }

    /**
     * @param string $msg
     *
     * @return self
     */
    public function setMessage($msg)
    {
        $this->msg = $msg;

        return $this;
    }

    /**
     * @param $success
     *
     * @return $this
     */
    public function setSuccess($success)
    {
        $this->success = (boolean)$success;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->success;
    }
}
