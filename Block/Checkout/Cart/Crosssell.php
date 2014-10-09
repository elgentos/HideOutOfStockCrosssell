<?php

class Elgentos_HideOutOfStockCrosssell_Block_Checkout_Cart_Crosssell extends Mage_Checkout_Block_Cart_Crosssell
{

    public function getItems()
    {
        $items = $this->getData('items');
        if (is_null($items)) {
            $items = array();
            $ninProductIds = $this->_getCartProductIds();
            if ($ninProductIds) {
                $lastAdded = (int) $this->_getLastAddedProductId();
                if ($lastAdded) {
                    $collection = $this->_getCollection()
                        ->addProductFilter($lastAdded);
                    if (!empty($ninProductIds)) {
                        $collection->addExcludeProductFilter($ninProductIds);
                    }
                    $collection->setPositionOrder()->load();

                    foreach ($collection as $item) {
                        $ninProductIds[] = $item->getId();
                        $items[] = $item;
                    }
                }

                if (count($items) < $this->_maxItemCount) {
                    $filterProductIds = array_merge($this->_getCartProductIds(), $this->_getCartProductIdsRel());
                    $collection = $this->_getCollection()
                        ->addProductFilter($filterProductIds)
                        ->addExcludeProductFilter($ninProductIds)
                        ->setPageSize($this->_maxItemCount-count($items))
                        ->setGroupBy()
                        ->setPositionOrder()
                        ->load();
                    foreach ($collection as $item) {
                        $items[] = $item;
                    }
                }

            }

            foreach($items as $key=>$item) {
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($item);
                if(!(int)$stockItem->getQty()) unset($items[$key]);
            }

            $this->setData('items', $items);
        }
        return $items;
    }

}