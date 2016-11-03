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
                $region = (string)$s3->region;
                if (empty($region)) {
                    // This is the default one according to documentation
                    $region = 'us-east-1';
                }

                $s3client = Aws\S3\S3Client::factory(array(
                    'credentials' => [
                        'key' => $accessKeyId,
                        'secret' => $accessSecret,
                    ],
                    'region' => $region,
                    'version' => 'latest'

                ));
                Made_S3_Helper_Data::setClient($s3client);

                $krakenKey = (string)$s3->kraken_key;
                $krakenSecret = (string)$s3->kraken_secret;
                if (!empty($krakenKey) && !empty($krakenSecret)) {
                    $kraken = new Kraken_Client($krakenKey, $krakenSecret);
                    Kraken_StreamWrapper::register($kraken, $s3client, array(
                        's3_key' => $accessKeyId,
                        's3_secret' => $accessSecret,
                        's3_bucket' => $bucketName,
                        's3_region' => $region,
                    ));
                }

                $appRoot = Mage::getRoot();
                $root = dirname($appRoot);

                $options = $config->getOptions();
                $currentMediadir = $options->getMediaDir();

                $mediaDir = 's3://' . $bucketName
                    . preg_replace("#^$root#", '', $currentMediadir);

                $uploadDir = $mediaDir . DS . 'upload';

                Mage::register('made_s3_original_media_dir', $options->getData('media_dir'));
                $options->setData('media_dir', $mediaDir);
                $options->setData('upload_dir', $uploadDir);
            }
        }

        // Don't change other request processor's results
        return $content;
    }
}