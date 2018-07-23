<?php

namespace App\Handlers;

use OSS\OssClient;
use OSS\Core\OssException;

class OssHandler
{
    protected $config;
    protected $ossClient;

    public function __construct()
    {
        $this->config = config('filesystems.disks.oss');
        $this->ossClient = new OssClient($this->config['access_key_id'], $this->config['access_key_secret'], $this->config['endpoint']);
    }

    public function uploadImageToOss($file)
    {
        try {
            $dir = date('Y') . '/' . date('m');
            $file_name =
            $res = $this->ossClient->uploadFile($this->config['bucket'], '201806/a.png', $file);
            dd($res);
        } catch (OssException $e) {
            print $e->getMessage();
        }

    }


    /**
     * 列出Bucket内所有目录和文件， 根据返回的nextMarker循环调用listObjects接口得到所有文件和目录
     *
     * @param OssClient $ossClient OssClient实例
     * @param string $bucket 存储空间名称
     * @return null
     */
    function listAllObjects()
    {
        $bucket = $this->config('bucket');
        //构造dir下的文件和虚拟目录
        for ($i = 0; $i < 100; $i += 1) {
            $this->ossClient->putObject($bucket, "dir/obj" . strval($i), "hi");
            $this->ossClient->createObjectDir($bucket, "dir/obj" . strval($i));
        }
        $prefix = 'dir/';
        $delimiter = '/';
        $nextMarker = '';
        $maxkeys = 30;
        while (true) {
            $options = array(
                'delimiter' => $delimiter,
                'prefix' => $prefix,
                'max-keys' => $maxkeys,
                'marker' => $nextMarker,
            );
            var_dump($options);
            try {
                $listObjectInfo = $this->ossClient->listObjects($bucket, $options);
            } catch (OssException $e) {
                printf(__FUNCTION__ . ": FAILED\n");
                printf($e->getMessage() . "\n");
                return;
            }
            // 得到nextMarker，从上一次listObjects读到的最后一个文件的下一个文件开始继续获取文件列表
            $nextMarker = $listObjectInfo->getNextMarker();
            $listObject = $listObjectInfo->getObjectList();
            $listPrefix = $listObjectInfo->getPrefixList();
            var_dump(count($listObject));
            var_dump(count($listPrefix));
            if ($nextMarker === '') {
                break;
            }
        }
    }

}
