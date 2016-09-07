<?php

/**
 * The purpose of the callback is to "unlock" resized product images so that
 * they can appear in product galleries without being broken links
 *
 * @author jonathan@madepeople.se
 */
class Made_S3_KrakenController extends Mage_Core_Controller_Front_Action
{
    /**
     * The callback payload can look something like:
     *
     *  {
     *    "results": {
     *    "1": {
     *          "file_name": "file.jpg",
     *          "original_size": 81289,
     *          "kraked_size": 24920,
     *          "saved_bytes": 56369,
     *          "kraked_url": "https://js-magento-media.s3.amazonaws.com/media/catalog/resize/product/thumb/200/100/auto/1/file.jpg",
     *          "original_width": 1280,
     *          "original_height": 744,
     *          "kraked_width": 172,
     *          "kraked_height": 100
     *      }
     *    },
     *    "success": true,
     *    "file_name": "file.jpg",
     *    "id": "755bd553334eed2581ae70432aa3287d",
     *    "timestamp": 1461268916
     *  }
     */
    public function callbackAction()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $this->getResponse()
                ->setHttpResponseCode(400)
                ->setBody('Invalid Request');
            return;
        }

        $body = $request->getRawBody();
        $data = @json_decode($body, true);
        if (!$data) {
            $this->getResponse()
                ->setHttpResponseCode(400)
                ->setBody('Invalid Request Body, expected JSON as string');
        }

        if ($data['success'] !== true) {
            // Callback is actually 200 OK, but we might need to try again later?
            return;
        }

        // This query should be optimized
        $query = "SELECT kraken_status FROM catalog_product_entity_media_gallery
WHERE value LIKE '%/{$data['file_name']}'";

    }
}