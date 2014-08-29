<?php
class Omx_Hooks_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function noXSS($input = '', $allowed = '')
    {
        $input = trim(strip_tags($input, $allowed));
        return $input;
    }

    public function log($request = '', $response = '', $comment = '')
    {
        $data = array();
        if (is_array($request)) {
            $data['request']  = (array_key_exists('request', $request)) ? $request['request'] : '';
            $data['response'] = (array_key_exists('response', $request)) ? $request['response'] : '';
            $data['comment']  = (array_key_exists('comment', $request)) ? $request['comment'] : '';
        } else {
            $data['request']  = $request;
            $data['response'] = $response;
            $data['comment']  = $comment;
        }
        $logdate = Zend_Date::now();
        $logdate->setTimezone('America/New_York');
        $data['timestamp'] = $logdate->toString("yyyy-MM-dd HH:mm:ss");
        $log               = Mage::getModel('hooks/logs')->setData($data);
        try {
            $log->save();
            return $log->getId();
        }
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            return false;
        }
    }

    public function getSettings($key = 'all')
    {
        $model      = Mage::getModel('hooks/settings');
        $collection = $model->getCollection()->load();
        $results    = array();
        foreach ($collection->getItems() as $value) {
            $tempValue = $value->toArray();
            if ($value->isEditable()) {
                $results[$tempValue['name']] = $tempValue['value'];
            }
        }
        if ($key == 'all') {
            return $results;
        } elseif (array_key_exists($key, $results)) {
            return $results[$key];
        } else {
            return false;
        }
    }

    public function setSettings($key = null, $newValue = null)
    {
        if ($key === null || $newValue === null) {
            Mage::throwException(Mage::helper('core')->__('Unable to save OMX Connector settings: Incomplete parameters'));
        }
        $rows = Mage::getModel('hooks/settings')->getCollection()->addFilter('name', $key)->load();
        if ($rows->getSize() >= 1) {
            foreach ($rows as $row) {
                $row->setValue($newValue);
                $row->save();
            }
            return true;
        }
        Mage::throwException(Mage::helper('core')->__('Unable to save OMX Connector settings: %s is not a valid setting name', $key));
        return false;
    }

    public function evalOmxBoolean($bool = null)
    {
        $ret = false;
        if ($bool == true || mb_strtoupper($bool) == 'Y' || mb_strtoupper($bool) == 'YES' || mb_strtoupper($bool) == 'TRUE' || $bool == 1 || $bool == '1' || $bool == -1 || $bool == '-1') {
            $ret = true;
        }
        return $ret;
    }

    // Added by Vonnda/Mark Sanborn
    public function addTrackingDataFromOmx($orders)
    {
        foreach ($orders as $order) {
            $omxOrder = Mage::getModel('hooks/order')->loadFromOrder($order);
            if (!$omxOrder) {
                continue;
            }

            if (!isset($omxOrder->trackingNumbers)) {
                continue;
            }

            if (empty($omxOrder->trackingNumbers)) {
                continue;
            }

            $this->shipAndTrack($order, $omxOrder->trackingNumbers);
        }
    }

    // Added by Vonnda/Mark Sanborn
    public function shipAndTrack($order, $trackNos)
    {
        if (!$order->canShip()) {
            return;
        }

        // No tracking, no shippy
        if (empty($trackNos)) {
            return;
        }

        $itemQty =  $order->getItemsCollection()->count();
        $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($itemQty);
        $shipment = new Mage_Sales_Model_Order_Shipment_Api();
        $shipmentId = $shipment->create($order->getIncrementId());

        $shipment = Mage::getModel('sales/order_shipment')->loadByIncrementId($shipmentId);

        foreach ($trackNos as $trackNo) {
            $track = Mage::getModel('sales/order_shipment_track')
             ->setShipment($shipment)
             ->setData('title', 'FedEx') // TODO: Change this
             ->setData('number', $trackNo)
             ->setData('carrier_code', 'fedex') // TODO: Change this
             ->setData('order_id', $shipment->getData('order_id'))
             ->save();

            $shipment->addTrack($track);
        }
        //$order->setStatus('complete');
        $order->save();
    }
}

