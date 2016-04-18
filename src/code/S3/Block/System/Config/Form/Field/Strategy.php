<?php

class Made_S3_Block_System_Config_Form_Field_Strategy
    extends Mage_Core_Block_Html_Select
{
    public function _toHtml()
    {
        $options = array(
            'auto' => 'Auto',
            'exact' => 'Exact',
            'portrait' => 'Portrait',
            'landscape' => 'Landscape',
            'fit' => 'Fit',
            'crop' => 'Crop',
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