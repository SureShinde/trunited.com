<?php
class Magestore_OneStepCheckout_Model_Source_Deliveryday {

    public function toOptionArray() {
        $options = array(
            array(
                'label' => __('Monday'),
                'value' => 1,
            ),
            array(
                'label' => __('Tuesday'),
                'value' => 2,
            ),
            array(
                'label' => __('Wednesday'),
                'value' => 3,
            ),
            array(
                'label' => __('Thursday'),
                'value' => 4,
            ),
            array(
                'label' => __('Friday'),
                'value' => 5,
            ),
            array(
                'label' => __('Saturday'),
                'value' => 6,
            ),
            array(
                'label' => __('Sunday'),
                'value' => 0,
            )
        );
        return $options;
    }

}