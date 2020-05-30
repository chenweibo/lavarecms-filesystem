<?php

namespace chenweibo\LaravelCmsFile;

use Illuminate\Support\Fluent;
use Illuminate\Http\Request;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;


/**
 * Class Upload.
 */
class Upload
{
    /**
     * @var string
     */
    protected $disk;

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var array
     */
    protected $mimes = [];

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var int
     */
    protected $maxSize = 0;

    /**
     * @var string
     */
    protected $filenameType;

    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;


    /**
     * Upload constructor.
     *
     * @param array $config
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(array $config, Request $request, $path)
    {
        $config = new Fluent($config);
        $this->path = $path;

        $this->request = $request;
        $this->mimes = $config->get('mimes', ['*']);
        $this->name = $config->get('name', 'file');
        $this->directory = $config->get('directory');
        $this->maxSize = $config->get('max_size', 0);
        $this->filenameType = $config->get('filename_type', 'md5_file');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getDisk()
    {
        return $this->disk;
    }

    /**
     * @return array|mixed
     */
    public function getMimes()
    {
        return $this->mimes;
    }

    /**
     * @return int
     */
    public function getMaxSize()
    {
        return $this->maxSize;
    }

    public function getFile()
    {
        return $this->request->file($this->name);
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        switch ($this->filenameType) {
            case 'original':
                return $this->getFile()->getClientOriginalName();
            case 'md5_file':
                return md5_file($this->getFile()->getRealPath()) . '.' . $this->getFile()->getClientOriginalExtension();

                break;
            case 'random':
            default:
                return $this->getFile()->hashName();
        }

    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @return bool
     */
    public function isValidMime()
    {
        return $this->mimes === ['*'] || \in_array($this->getFile()->getClientMimeType(), $this->mimes);
    }

    /**
     * @return bool
     */
    public function isValidSize()
    {
        $maxSize = $this->filesize2bytes($this->maxSize);

        return $this->getFile()->getSize() <= $maxSize || 0 === $maxSize;
    }

    public function validate()
    {
        if (!$this->request->hasFile($this->getName())) {
            \abort(422, 'no file found.');
        }

        $file = $this->getFile();

        if (!$this->isValidMime($file->getClientMimeType())) {
            \abort(422, \sprintf('Invalid mime "%s".', $file->getClientMimeType()));
        }

        if (!$this->isValidSize($file->getSize())) {
            \abort(422, \sprintf('File has too large size("%s").', $file->getSize()));
        }
    }

    /**
     * @param string $humanFileSize
     *
     * @return int
     */
    protected function filesize2bytes($humanFileSize)
    {
        $bytes = 0;

        $bytesUnits = array(
            'K' => 1024,
            'M' => 1024 * 1024,
            'G' => 1024 * 1024 * 1024,
            'T' => 1024 * 1024 * 1024 * 1024,
            'P' => 1024 * 1024 * 1024 * 1024 * 1024,
        );

        $bytes = floatval($humanFileSize);

        if (preg_match('~([KMGTP])$~si', rtrim($humanFileSize, 'B'), $matches) && !empty($bytesUnits[\strtoupper($matches[1])])) {
            $bytes *= $bytesUnits[\strtoupper($matches[1])];
        }

        return intval(round($bytes, 2));
    }

    /**
     *
     * @return Filesystem
     */
    protected function filesystem()
    {
        $adapter = new Local(base_path());
        $filesystem = new Filesystem($adapter, ['visibility' => 'public']);
        return $filesystem;
    }

    /**
     *  upload file anyway
     * @param array $options
     *
     * @return array
     */
    public function upload(array $options = [])
    {
        $this->validate();

        $file = $this->getFile();


        $path = \sprintf('%s/%s', \rtrim($this->path, '/'), $this->getFilename($file));

        $stream = fopen($file->getRealPath(), 'r+');
        $filesystem = $this->filesystem();
        $filesystem->put(
            $path,
            $stream
        );
        if (is_resource($stream)) {
            fclose($stream);
        }

        return ['fullPath' => base_path() . $path, 'path' => $path];
    }

    /**
     * Rename Files
     * @param string $from
     * @param string $to
     * @return boolean $response
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    public function rename($from, $to)
    {
        $filesystem = $this->filesystem();
        return $filesystem->rename($from, $to);
    }

    /**
     * Create Directories
     * @param string $path
     * @return boolean $response
     */
    public function createDir($path)
    {
        $filesystem = $this->filesystem();

        return $filesystem->createDir($path);
    }

    /**
     * Copy Files
     * @param string $from
     * @param string $to
     * @return bool $response
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    public function copy($from, $to)
    {
        $filesystem = $this->filesystem();
        return $filesystem->copy($from, $to);
    }

    /**
     * Delete Directories
     * @param string $path
     * @return boolean $response
     */
    public function deleteDir($path)
    {
        $filesystem = $this->filesystem();
        return $filesystem->deleteDir($path);
    }

    /**
     * Delete Files or Directories
     * @param string $path
     * @return boolean
     * @throws FileNotFoundException
     */
    public function delete($path)
    {
        $filesystem = $this->filesystem();
        return $filesystem->delete($path);
    }

}
