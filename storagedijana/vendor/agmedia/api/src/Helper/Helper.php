<?php

namespace Agmedia\Api\Helper;

use Illuminate\Support\Str;

class Helper
{

    /**
     *
     * @param array $product
     *
     * @return array
     */
    public static function resolveDescription(string $title, string $description): array
    {
        $description = static::setDescription($description);

        $response[agconf('import.default_language')] = [
            'name' => static::setText($title),
            'description' => static::setText($description),
            'tag' => '',
            'meta_title' => static::setText($title),
            'meta_description' => strip_tags(static::setText($description)),
            'meta_keyword' => static::setText(str_replace(' ', ',', $title)),
        ];

        return $response;
    }


    /**
     * @param string $slug
     *
     * @return array
     */
    public static function resolveSeoUrl(string $slug): array
    {
        $slug = Str::slug($slug);

        return [
            agconf('import.default_language') => $slug
        ];
    }


    /**
     * @param string|null $text
     *
     * @return string
     */
    public static function setText(string $text = null): string
    {
        if ($text) {
            $text = str_replace("'", "", $text);

            return str_replace('"', 'in', $text);
        }

        return '';
    }


    /**
     * @param string|null $text
     *
     * @return string
     */
    public static function setDescription(string $text = null): string
    {
        if ($text) {
            $text = str_replace("\n", '<br>', $text);
            $text = str_replace("\r", '<br>', $text);

            return str_replace("\t", '<tab>', $text);
        }

        return '';
    }


    /**
     * @param string $base64_string
     * @param string $output_file
     *
     * @return string
     */
    public static function base64_to_jpeg(string $base64_string, string $output_file): string
    {
        file_put_contents(DIR_IMAGE . $output_file, base64_decode($base64_string));

        return $output_file;
    }


    public static function validate(array $request, string $type): bool
    {
        if ($type == 'sendOrder') {
            $params = ['order_id', 'type'];
        }

        foreach ($params as $param) {
            if ( ! isset($request[$param]) && empty($request[$param])) {
                return false;
            }
        }

        return true;
    }

}