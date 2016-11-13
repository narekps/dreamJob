<?php

namespace App\View\Helper;

use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\View\Helper\AbstractHelper;

class MaterialForm extends AbstractHelper
{

    const FORM_GROUP_CLASS = 'form-group';
    const LABEL_CLASS = 'control-label';
    const LABEL_FLOATING_CLASS = 'label-floating';
    const INPUT_CLASS = 'form-control';
    const HELP_BLOCK_CLASS = 'help-block';

    const BUTTON_CLASS = 'btn';
    const BUTTON_BLOCK_CLASS = 'btn-block';
    const BUTTON_RAISED_CLASS = 'btn-raised';
    const BUTTON_PRIMARY_CLASS = 'btn-primary';

    //const SUBMIT_BUTTON_CLASS = self::BUTTON_CLASS . ' ' . self::BUTTON_BLOCK_CLASS . ' ' . self::BUTTON_RAISED_CLASS . ' ' . self::BUTTON_PRIMARY_CLASS;
    const SUBMIT_BUTTON_CLASS = self::BUTTON_CLASS . ' ' . self::BUTTON_RAISED_CLASS . ' ' . self::BUTTON_PRIMARY_CLASS;

    const HAS_ERROR_CLASS = 'has-error';

    const NOT_LABEL_ELEMENTS = [
        Element\Button::class,
        Element\Submit::class,
        Element\Csrf::class,
    ];

    /**
     * @var bool
     */
    protected $elementAsCol = false;

    /**
     * @var string
     */
    protected $labelAfterText = ':';

    /**
     * @var string
     */
    protected $submitButtonClass = self::SUBMIT_BUTTON_CLASS;

    /**
     * @return boolean
     */
    public function isElementAsCol()
    {
        return $this->elementAsCol;
    }

    /**
     * @param boolean $elementAsCol
     *
     * @return MaterialForm
     */
    public function setElementAsCol($elementAsCol)
    {
        $this->elementAsCol = (boolean)$elementAsCol;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabelAfterText()
    {
        return $this->labelAfterText;
    }

    /**
     * @param string $labelAfterText
     *
     * @return MaterialForm
     */
    public function setLabelAfterText($labelAfterText)
    {
        $this->labelAfterText = $labelAfterText;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubmitButtonClass()
    {
        return $this->submitButtonClass;
    }

    /**
     * @param string $submitButtonClass
     *
     * @return MaterialForm
     */
    public function setSubmitButtonClass($submitButtonClass)
    {
        $this->submitButtonClass = $submitButtonClass;

        return $this;
    }

    /**
     * @param Form|null $form
     *
     * @return $this|void
     */
    public function __invoke(Form $form = null)
    {
        if (!is_null($form)) {
            return $this->doRender($form);
        }

        return $this;
    }

    /**
     * @param Form $form
     */
    public function render(Form $form)
    {
        $this->doRender($form);
    }

    /**
     * @param Form $form
     */
    protected function doRender(Form $form)
    {
        /** @var \Zend\View\Renderer\PhpRenderer $view */
        $view = $this->getView();
        $form->prepare();
        if ($this->isElementAsCol()) {
            $form->setAttribute('class', $form->getAttribute('class') . ' form-horizontal');
        }
        $output = $view->form()->openTag($form);
        foreach ($form->getElements() as $elementOrFieldset) {
            if ($elementOrFieldset instanceof Fieldset) {
                $output .= $this->renderFieldSet($elementOrFieldset);
            } elseif ($elementOrFieldset instanceof Element) {
                $output .= $this->renderElement($elementOrFieldset);
            }
        }
        $output .= $view->form()->closeTag();
        echo $output;
    }

    protected function renderFieldSet(Fieldset $elementOrFieldset)
    {
        $output = '<fieldset>';
        $output .= '<legend>abc</legend>';

        foreach ($elementOrFieldset->getElements() as $element) {
            $output .= $this->renderElement($element);
        }

        $output .= '</fieldset>';

        return $output;
    }

    protected function renderElement(Element $element)
    {
        $output = '';
        if (!$element->getAttribute('id')) {
            $element->setAttribute('id', $element->getName());
        }

        if ($element instanceof Element\Hidden || $element instanceof Element\Csrf) {
            $output .= $this->getView()->formElement($element);

            return $output;
        }

        if ($element->getLabel()) {
            $element->setLabel($this->view->translate($element->getLabel()));
        }

        $hasError = false;
        $groupClass = [self::FORM_GROUP_CLASS];
        if (!empty($element->getMessages())) {
            $groupClass[] = self::HAS_ERROR_CLASS;
            $hasError = true;
        }
        if ((bool)$element->getOption('label_floating') === true) {
            $groupClass[] = self::LABEL_FLOATING_CLASS;
        }

        if ($element instanceof Element\Button) {
            $groupClass[] = 'text-centered';
        }

        if ($hasError) {
            $output .= '<div class="' . join(' ', $groupClass) . '" data-toggle="popover" data-trigger="focus" data-content="' . $this->getView()->formElementErrors($element) . '">';
        } else {
            $output .= '<div class="' . join(' ', $groupClass) . '">';
        }

        $labelAttributes = $element->getLabelAttributes();
        if (!array_key_exists('class', $labelAttributes)) {
            $labelAttributes['class'] = '';
        }
        $labelAttributes['class'] .= ' ' . self::LABEL_CLASS;
        if ($this->isElementAsCol()) {
            $labelAttributes['class'] .= ' col-md-3';
        }
        $element->setLabelAttributes($labelAttributes);

        if (!in_array(get_class($element), self::NOT_LABEL_ELEMENTS)) {
            $element->setLabel($element->getLabel() . $this->getLabelAfterText());
            $output .= $this->getView()->formLabel($element);
        }

        $afterBtnOut = '';
        if ($element->getOption('after_button')) {
            $afterBtn = $element->getOption('after_button');
            $afterBtnClass = '';
            $afterBtnText = $this->getView()->translate('Button');
            $afterBtnAttrs = '';
            if (isset($afterBtn['class'])) {
                $afterBtnClass = $afterBtn['class'];
            }
            if (isset($afterBtn['text'])) {
                $afterBtnText = $this->getView()->translate($afterBtn['text']);
            }
            if (isset($afterBtn['data-target'])) {
                $afterBtnAttrs .= 'data-target="' . $afterBtn['data-target'] . '"';
            }
            $afterBtnOut .= '<span class="input-group-btn">';
            $afterBtnOut .= '<button class="' . $afterBtnClass . '" type="button" ' . $afterBtnAttrs . '>' . $afterBtnText . '</button>';
            $afterBtnOut .= '</span>';
        }

        if ($this->isElementAsCol() && !$element instanceof Element\Button) {
            $output .= '<div class="col-md-9 ' . (!$afterBtnOut ?: 'input-group') . '">';
        }
        switch (get_class($element)) {
            case Element\Button::class:
                $element->setAttribute('class', $element->getAttribute('class') . ' ' . self::SUBMIT_BUTTON_CLASS);
                $output .= $this->getView()->formElement($element);
                break;
            case Element\Radio::class:
                $output .= '<div class="radio radio-primary">';
                unset($labelAttributes['class']);
                $element->setLabelAttributes($labelAttributes);
                foreach ($element->getOptions()['value_options'] as &$val) {
                    $val = $this->view->translate($val);
                }
                $output .= $this->getView()->formElement($element);
                $output .= '</div>';
                break;
            default:
                $element->setAttribute('class', $element->getAttribute('class') . ' ' . self::INPUT_CLASS);
                $output .= $this->getView()->formElement($element);

                if ($element->getOption('helpText')) {
                    $output .= '<p class="' . self::HELP_BLOCK_CLASS . '">' . $this->view->translate($element->getOption('helpText')) . '</p>';
                }
                break;
        }

        $output .= $afterBtnOut;

        if ($this->isElementAsCol() && !$element instanceof Element\Button) {
            $output .= '</div>';
        }

        $output .= '</div>';

        return $output;
    }
}
