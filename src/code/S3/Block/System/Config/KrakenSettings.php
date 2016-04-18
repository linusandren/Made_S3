<?php

/**
 * Frontend model for the Kraken Processing Settings
 *
 * @author jonathan@madepeople.se
 */
class Made_S3_Block_System_Config_KrakenSettings
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $_itemRenderers = array();

    /**
     * Renders a row of Kraken settings
     */
    public function _prepareToRender()
    {
        $this->addColumn('type', array(
            'renderer' => $this->_getRenderer('type'),
            'label' => 'Type',
            'style' => 'width:100px',
        ));
        $this->addColumn('key', array(
            'label' => 'Key or Folder Pattern',
            'style' => 'width:100px',
        ));
        $this->addColumn('resize_width', array(
            'label' => 'Resize Width',
            'style' => 'width:70px',
        ));
        $this->addColumn('resize_height', array(
            'label' => 'Resize Height',
            'style' => 'width:70px',
        ));
        $this->addColumn('resize_strategy', array(
            'renderer' => $this->_getRenderer('strategy'),
            'label' => 'Resize Strategy',
            'style' => 'width:100px',
        ));
        $this->addColumn('crop_mode', array(
            'renderer' => $this->_getRenderer('crop'),
            'label' => 'Crop Mode',
            'style' => 'width:100px',
        ));
        $this->addColumn('enhance', array(
            'renderer' => $this->_getRenderer('enhance'),
            'label' => 'Enhance',
            'style' => 'width:100px',
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = 'Add Setting';
    }

    /**
     * Get item renderer by type
     *
     * @param $type
     * @return mixed
     */
    protected function _getRenderer($type)
    {
        if (!isset($this->_itemRenderers[$type])) {
            $this->_itemRenderers[$type] = $this->getLayout()->createBlock(
                'made_s3/system_config_form_field_' . $type, '',
                array('is_render_to_js_template' => true)
            );
        }
        return $this->_itemRenderers[$type];
    }

    /**
     * Used to select the correct values for each row
     *
     * @param Varien_Object $row
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        foreach (array(
                     'type' => 'type',
                     'resize_strategy' => 'strategy',
                     'crop_mode' => 'crop',
                     'enhance' => 'enhance'
                 ) as $field => $renderer) {
            $row->setData(
                'option_extra_attr_' . $this->_getRenderer($renderer)
                    ->calcOptionHash($row->getData($field)),
                'selected="selected"'
            );
        }
    }
}