<?php

/**
 * Kraken stream wrapper implementation that plays with S3
 *
 * @author jonathan@madepeople.se
 */
class Kraken_StreamWrapper
{

    // Kraken client, that does the uploading and stuff
    protected static $client;

    // S3 Client, where we get credentials from and might also do other stuff
    protected static $s3Client;

    // An object of the s3 stream wrapper that we might use to proxy some
    // S3 calls that we don't want to manage on our own
    protected static $s3StreamWrapper;

    // Kraken + S3 options
    protected static $options;

    // The path of the file to be kraked
    protected $_path;
    protected $_body;

    /**
     * Register the 'kraken://' stream wrapper
     *
     * @param Kraken_Client $client
     * @param \Aws\S3\S3Client $s3Client
     * @param array $options
     */
    public static function register(Kraken_Client $client, \Aws\S3\S3Client $s3Client,
                                    Aws\S3\StreamWrapper $s3StreamWrapper, array $options)
    {
        if (!in_array('kraken', stream_get_wrappers())) {
            stream_wrapper_register('kraken', __CLASS__, STREAM_IS_URL);
        }

        self::$client = $client;
        self::$s3Client = $s3Client;
        self::$options = $options;
        self::$s3StreamWrapper = $s3StreamWrapper;
    }

    /**
     * Fallback everything to the S3 Stream Wrapper that we don't implement
     * ourselves
     *
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments)
    {
        foreach ($arguments as $i => $argument) {
            if (is_string($argument)) {
                $arguments[$i] = preg_replace('#^kraken:#', 's3:', $argument);
            }
        }
        return call_user_func_array(array(
            self::$s3StreamWrapper,
            $name
        ), $arguments);
    }

    /**
     * The directory concept doesn't exist, and we do not support automatic
     * creation of buckets
     *
     * @param $path
     * @param $mode
     * @param $options
     */
    public function mkdir($path, $mode, $options)
    {
        return true;
    }

    /**
     * We don't need to work with streams here
     *
     * @param $path
     * @param $mode
     * @param $options
     * @param $opened_path
     */
    public function stream_open($path, $mode, $options, &$opened_path)
    {
        $this->_path = $path;
        $this->_body = '';
        return true;
    }

    /**
     * Keep the whole image in memory, we don't need anything more advanced at
     * this point
     *
     * @param $data
     * @return int
     */
    public function stream_write($data)
    {
        $bytes = strlen($data);
        $this->_body = $this->_body . $data;
        return $bytes;
    }

    /**
     * Flushing the stream means sending it through kraken. At this point we
     * use a temporary local image because it's the best we can do with the
     * official libcurl kraken client.
     *
     * @return bool
     */
    public function stream_flush()
    {
        $filename = tempnam(Mage::getBaseDir('tmp'), 'kraken_');
        $s3Path = preg_replace('#^kraken://' . self::$options['s3_bucket'] . '/#', '', $this->_path);
        file_put_contents($filename, $this->_body);
        $result = $this->_request(array(
            'file' => $filename,
            'wait' => true,
            's3_store' => array(
                'path' => $s3Path,
            )
        ), 'upload');
        unlink($filename);
        if ($result['success'] === false) {
            trigger_error('Error uploading image to Kraken: ' . $result['message'], E_USER_WARNING);
            return false;
        }
        return true;
    }

    /**
     * Closing the stream = freeing memory
     */
    public function stream_close()
    {
        $this->_body = null;
    }

    /**
     * A quick fix to allow chmod, touch and friends to work. Access level
     * stuff works differently in S3 so it doesn't make sense at this point to
     * try to achieve a complete implementation.
     *
     * @param $path
     * @param $option
     * @param $value
     * @return bool
     */
    public function stream_metadata($path, $option, $value)
    {
        // Return true for all cases
        return true;
    }

    /**
     * A little generalized Kraken upload function
     *
     * @param $options
     */
    protected function _request($options, $method)
    {
        $result = self::$client->$method(array_merge_recursive(array(
            'lossy' => true,
            's3_store' => array(
                'key' => self::$options['s3_key'],
                'secret' => self::$options['s3_secret'],
                'bucket' => self::$options['s3_bucket'],
                'region' => self::$options['s3_region'],
                'headers' => array(
                    'Cache-Control' => 'max-age=2592000000',
                    'Expires' => '2026-04-04T12:06:11+00:00'
                )
            )
        ), $options));
        return $result;
    }
}