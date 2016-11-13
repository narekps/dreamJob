<?php

namespace App\Form;

/**
 * Interface SaveResultInterface
 * @package App\Form
 */
interface SaveResultInterface
{

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     *
     * @return self
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param string $msg
     *
     * @return self
     */
    public function setMessage($msg);

    /**
     * @return bool
     */
    public function isSuccess();
}
