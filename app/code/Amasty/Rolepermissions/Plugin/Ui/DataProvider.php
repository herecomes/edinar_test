<?php

namespace Amasty\Rolepermissions\Plugin\Ui;

use Magento\Framework\Api\Search\SearchResultInterface;

class DataProvider
{
    public function afterGetSearchResult(
        \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider $subject,
        SearchResultInterface $result
    ) {
        $result->getItems(); // Force collection load before getTotalCount() call

        return $result;
    }
}
