<?php

class Made_S3_Block_System_Config_Form_Field_Type
    extends Mage_Core_Block_Html_Select
{
    public function _toHtml()
    {
        $options = array(
            '' => '-- Please Select --',
            'product' => 'Product Image',
            'cms' => 'CMS Image',
        );

        foreach ($options as $value => $label) {
            $this->addOption($value, $label);
        }

        return parent::_toHtml();
    }

    public function getExtraParams()
    {
        return 'style="width:auto"';
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }
}