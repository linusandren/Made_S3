<?php

/**
 * Kraken streamwrapper implementation that plays with S3
 *
 * @author jonathan@madepeople.se
 */
class Kraken_StreamWrapper
{

    // Kraken client, that does the uploading and stuff
    protected static $client;

    // S3 Client, where we get credentials from and might also do other stuff
    protected static $s3Client;

    protected static $s3StreamWrapper;

    protected $_handle;
    protected $_path;

    /**
     * Register the 'kraken://' stream wrapper
     *
     * @param Kraken $client Client to use with the stream wrapper
     */
    public static function register(Kraken_Kraken $client, \Aws\S3\S3Client $s3Client)
    {
        if (!in_array('kraken', stream_get_wrappers())) {
            stream_wrapper_register('kraken', __CLASS__, STREAM_IS_URL);
        }

        self::$client = $client;
        self::$s3Client = $s3Client;

        $s3StreamWrapper = new Aws\S3\StreamWrapper;
        $s3StreamWrapper::register($s3Client);
        self::$s3StreamWrapper = $s3StreamWrapper;
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
     * Proxy this to the S3 client because it knows more than we do
     *
     * @param $path
     * @param $flags
     * @return mixed
     */
    public function url_stat($path, $flags)
    {
        $path = preg_replace('#^kraken:#', 's3:', $path);
        return self::$s3StreamWrapper->url_stat($path, $flags);
    }

    /**
     * Open a temporary (inmemory + disk) file handle
     *
     * @param $path
     * @param $mode
     * @param $options
     * @param $opened_path
     */
    public function stream_open($path, $mode, $options, &$opened_path)
    {
//        $this->_handle = fopen('php://temp', $mode);
//        $this->_path = $path;
        return true;
    }

    /**
     * Write to the temporary stream
     *
     * @param $data
     * @return int
     */
    public function stream_write($data)
    {
//        $bytes = fwrite($this->_handle, $data);
        self::$client->request();
        $bytes = strlen($data);
        return $bytes;
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
}