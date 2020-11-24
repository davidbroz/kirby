<?php

namespace Kirby\Cms;

class BlockConverter
{
    public static function builderBlock(array $params): array
    {
        if (isset($params['_key']) === false) {
            return $params;
        }

        $params['type']    = $params['_key'];
        $params['content'] = $params;
        unset($params['_uid']);

        return $params;
    }

    public static function editorBlock(array $params): array
    {
        if (static::isEditorBlock($params) === false) {
            return $params;
        }

        $method = 'editor' . $params['type'];

        if (method_exists(static::class, $method) === true) {
            $params = static::$method($params);
        } else {
            $params = static::editorParagraph($params);
        }

        return $params;
    }

    public static function editorBlocks(array $blocks = [])
    {
        if (empty($blocks) === true) {
            return $blocks;
        }

        if (static::isEditorBlock($blocks[0]) === false) {
            return $blocks;
        }

        $list = [];
        $listStart = null;

        foreach ($blocks as $index => $block) {
            if (in_array($block['type'], ['ul', 'ol']) === true) {
                $prev = $blocks[$index-1] ?? null;
                $next = $blocks[$index+1] ?? null;

                // new list starts here
                if (!$prev || $prev['type'] !== $block['type']) {
                    $listStart = $index;
                }

                // add the block to the list
                $list[] = $block;

                // list ends here
                if (!$next || $next['type'] !== $block['type']) {
                    $blocks[$listStart] = [
                        'content' => [
                            'text' =>
                                '<' . $block['type'] . '>' .
                                    implode(array_map(function ($item) {
                                        return '<li>' . $item['content'] . '</li>';
                                    }, $list)) .
                                '</' . $block['type'] . '>',
                        ],
                        'type' => 'list'
                    ];

                    for ($x = $listStart+1; $x <= $listStart + count($list); $x++) {
                        unset($blocks[$x]);
                    }

                    $listStart = null;
                    $list = [];
                }
            }
        }

        return $blocks;
    }

    public static function editorBlockquote($params)
    {
        return [
            'content' => [
                'text' => $params['content']
            ],
            'type' => 'quote'
        ];
    }

    public static function editorCode($params)
    {
        return [
            'content' => [
                'language' => $params['attrs']['language'] ?? null,
                'code'     => $params['content']
            ],
            'type' => 'code'
        ];
    }

    public static function editorH1(array $params)
    {
        return static::editorHeading($params, 'h1');
    }

    public static function editorH2(array $params)
    {
        return static::editorHeading($params, 'h2');
    }

    public static function editorH3(array $params)
    {
        return static::editorHeading($params, 'h3');
    }

    public static function editorH4(array $params)
    {
        return static::editorHeading($params, 'h4');
    }

    public static function editorH5(array $params)
    {
        return static::editorHeading($params, 'h5');
    }

    public static function editorH6(array $params)
    {
        return static::editorHeading($params, 'h6');
    }

    public static function editorHeading(array $params, string $level): array
    {
        return [
            'content' => [
                'level' => $level,
                'text'  => $params['content']
            ],
            'type' => 'heading'
        ];
    }

    public static function editorImage(array $params)
    {
        return [
            'content' => [
                'alt'     => $params['attrs']['alt'] ?? null,
                'caption' => $params['attrs']['caption'] ?? null,
                'image'   => $params['attrs']['id'] ?? $params['attrs']['src'] ?? null,
                'ratio'   => $params['attrs']['ratio'] ?? null,
            ],
            'type' => 'image'
        ];
    }

    public static function editorKirbytext($params)
    {
        return [
            'content' => [
                'text' => $params['content']
            ],
            'type' => 'markdown'
        ];
    }

    public static function editorOl($params)
    {
        return [
            'content' => [
                'text' => $params['content']
            ],
            'type' => 'list'
        ];
    }

    public static function editorParagraph($params)
    {
        return [
            'content' => [
                'text' => $params['content']
            ],
            'type' => 'text'
        ];
    }

    public static function editorUl($params)
    {
        return [
            'content' => [
                'text' => $params['content']
            ],
            'type' => 'list'
        ];
    }

    public static function editorVideo($params)
    {
        return [
            'content' => [
                'caption' => $params['attrs']['caption'] ?? null,
                'url'     => $params['attrs']['src'] ?? null
            ],
            'type' => 'video'
        ];
    }

    public static function isEditorBlock(array $params): bool
    {
        if (isset($params['attrs']) === true) {
            return true;
        }

        if (is_string($params['content'] ?? null) === true) {
            return true;
        }

        return false;
    }
}
