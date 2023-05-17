<?php

/**
 * Created by PhpStorm.
 * Date:  2021/11/2
 * Time:  9:44 下午
 */
declare (strict_types=1);

namespace Lengbin\Hyperf\Common\Helpers;

use Hyperf\Di\Annotation\Inject;
use Hyperf\Support\Filesystem\Filesystem;
use Lengbin\Hyperf\ErrorCode\BaseEnum;

class ConstantOutputHelper
{
    #[Inject]
    protected Filesystem $filesystem;

    /**
     * enums:
     *   $path = BASE_PATH . '/app/Constants';
     *   $this->scan($path, ['Errors'], ['Error']);
     *
     * errors:
     *  $path = BASE_PATH . '/app/Constants/Errors';
     *  $this->scan($path);
     *
     * @param string $dir
     * @param array $excludeDir
     * @param array $excludeFile
     * @param callable|null $call
     * @return array
     */
    public function scan(string $dir, array $excludeDir = [], array $excludeFile = [], ?callable $call = null): array
    {
        $data = [];
        $directories = $this->filesystem->directories($dir);
        if (empty($directories)) {
            $directories[] = $dir;
        }
        foreach ($directories as $directory) {
            $directoryName = $this->filesystem->basename($directory);
            if (in_array($directoryName, $excludeDir)) {
                continue;
            }

            $files = $this->filesystem->allFiles($directory);
            foreach ($files as $file) {
                $fileName = $file->getBasename('.php');
                if (in_array($fileName, $excludeFile)) {
                    continue;
                }
                $filePath = substr($file->getRealPath(), 0, -4);
                /**
                 * @var BaseEnum $classname
                 */
                $classname = implode('\\', array_map(function ($str) {
                    return ucfirst($str);
                }, explode('/', str_replace(BASE_PATH, '', $filePath))));

                if (!is_subclass_of($classname, BaseEnum::class)) {
                    continue;
                }
                $maps = $classname::getMapJson();
                $data[lcfirst($directoryName)][lcfirst($fileName)] = $call ? call_user_func($call, $maps) : $maps;
            }
        }
        return $data;
    }
}