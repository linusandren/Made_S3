<?php

// This is a bit on the lol side, but it does the work and the S3Client needs
// to be globally available anyway
require_once dirname(__FILE__) . '/../aws/aws-autoloader.php';
use Aws\S3\S3Client;

/**
 * Use a request processor to set a custom media directory very early
 *
 * @author jonathan@madepeople.se
 */
class Made_S3_Model_Processor
{

    /**
     * Uses information found in local.xml in order to bootstrap the S3 setup
     *
     * @param $content
     */
    public function extractContent($content)
    {
        $config = Mage::getConfig();
        $s3 = $config->getNode('global/s3');
        if ($s3 !== false) {
            $active = (int)$s3->active;
            if ($active === 1) {
                $accessKeyId = (string)$s3->access_key_id;
                $accessSecret = (string)$s3->access_secret;
                $bucketName = (string)$s3->bucket_name;

                $client = S3Client::factory(array(
                    'key' => $accessKeyId,
                    'secret' => $accessSecret,
                ));
                $client->registerStreamWrapper();
//                mkdir('s3://asselejlkfskjdfg/lolasdf/');
//                var_dump(is_dir('s3://asselejlkfskjdfg/lolapeoridsf'));
//                exit;
                Made_S3_Helper_Data::setClient($client);

                $appRoot = Mage::getRoot();
                $root = dirname($appRoot);

                $options = $config->getOptions();
                $currentMediadir = $options->getMediaDir();

                $mediaDir = 's3://' . $bucketName
                    . preg_replace("#^$root#", '', $currentMediadir);

                $uploadDir = $mediaDir . DS . 'upload';

                $options->setData('media_dir', $mediaDir);
                $options->setData('upload_dir', $uploadDir);
            }
        }
    }
}