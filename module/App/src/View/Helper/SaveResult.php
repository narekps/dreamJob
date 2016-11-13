<?php

namespace App\View\Helper;

use Zend\View\Helper\AbstractHelper;
use App\Form\SaveResult as FormSaveResult;

/**
 * Class SaveResult
 * @package App\View\Helper
 */
class SaveResult extends AbstractHelper
{

    /**
     * @param FormSaveResult $saveResult
     */
    public function __invoke(FormSaveResult $saveResult)
    {
        return $this->render($saveResult);
    }

    /**
     * @param FormSaveResult $saveResult
     */
    protected function render(FormSaveResult $saveResult)
    {
        $output = '<div class="edit_result">';
        $output .= '<div class="msg ' . ($saveResult->isSuccess() ? 'ok_msg' : 'error_msg') . '">';
        $output .= '<div class="msg_text">';
        if ($saveResult->getTitle()) {
            $output .= "<b>" . $this->view->translate($saveResult->getTitle()) . "</b><br/>";
        }
        if ($saveResult->getMessage()) {
            $output .= $this->view->translate($saveResult->getMessage());
        }
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
        echo $output;
    }
}
