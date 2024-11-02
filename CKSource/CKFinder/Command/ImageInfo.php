<?php

/*
 * CKFinder
 * ========
 * https://ckeditor.com/ckfinder/
 * Copyright (c) 2007-2023, CKSource Holding sp. z o.o. All rights reserved.
 *
 * The software, this file and its contents are subject to the CKFinder
 * License. Please read the license.txt file before using, installing, copying,
 * modifying or distribute this file or part of its contents. The contents of
 * this file is part of the Source Code of CKFinder.
 */

namespace CKSource\CKFinder\Command;

use CKSource\CKFinder\{Acl\Permission,
    Cache\CacheManager,
    Config,
    Exception\FileNotFoundException,
    Exception\InvalidNameException,
    Exception\InvalidRequestException,
    Image};
use Exception;
use CKSource\CKFinder\Filesystem\{File\DownloadedFile, File\File, Folder\WorkingFolder, Path};
use Symfony\Component\HttpFoundation\Request;

class ImageInfo extends CommandAbstract
{
    protected array $requires = [Permission::FILE_VIEW];

    /**
     * @throws FileNotFoundException
     * @throws InvalidNameException
     * @throws InvalidRequestException
     * @throws Exception
     */
    public function execute(Request $request, WorkingFolder $workingFolder, Config $config, CacheManager $cache): array
    {
        $fileName = (string)$request->get('fileName');

        if (empty($fileName) || !File::isValidName($fileName, $config->get('disallowUnsafeCharacters'))) {
            throw new InvalidRequestException('Invalid file name');
        }

        if (!Image::isSupportedExtension(pathinfo($fileName, PATHINFO_EXTENSION))) {
            throw new InvalidNameException('Invalid source file name');
        }

        if (!$workingFolder->containsFile($fileName)) {
            throw new FileNotFoundException();
        }

        $cachePath = Path::combine(
            $workingFolder->getResourceType()->getName(),
            $workingFolder->getClientCurrentFolder(),
            $fileName
        );

        $imageInfo = [];

        $cachedInfo = $cache->get($cachePath);

        if ($cachedInfo && isset($cachedInfo['width'], $cachedInfo['height'])) {
            $imageInfo = $cachedInfo;
        } else {
            $file = new DownloadedFile($fileName, $this->app);

            if ($file->isValid()) {
                $image = Image::create($file->getContents());
                $imageInfo = $image->getInfo();
                $cache->set($cachePath, $imageInfo);
            }
        }

        return $imageInfo;
    }
}