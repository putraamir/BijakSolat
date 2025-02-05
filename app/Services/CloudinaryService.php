<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    protected $cloudinary;

    public function __construct()
    {
        try {
            Configuration::instance([
                'cloud' => [
                    'cloud_name' => config('services.cloudinary.cloud_name'),
                    'api_key' => config('services.cloudinary.api_key'),
                    'api_secret' => config('services.cloudinary.api_secret')
                ],
                'url' => [
                    'secure' => true
                ]
            ]);

            $this->cloudinary = new Cloudinary();
            Log::info('Cloudinary initialized successfully');
        } catch (\Exception $e) {
            Log::error('Cloudinary initialization failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function upload($filePath, array $options = [])
    {
        try {
            if (!file_exists($filePath)) {
                throw new \Exception('File does not exist at path: ' . $filePath);
            }

            Log::debug('Uploading file to Cloudinary', [
                'file_path' => $filePath,
                'options' => $options
            ]);

            $defaultOptions = [
                'resource_type' => 'auto',
                'use_filename' => true,
                'unique_filename' => true
            ];

            $uploadOptions = array_merge($defaultOptions, $options);

            $result = $this->cloudinary->uploadApi()->upload($filePath, $uploadOptions);

            Log::debug('Cloudinary upload successful', [
                'result' => $result
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Cloudinary upload error', [
                'message' => $e->getMessage(),
                'file_path' => $filePath
            ]);
            throw $e;
        }
    }
}
