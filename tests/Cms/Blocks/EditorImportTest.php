<?php

namespace Kirby\Cms;

use Kirby\Data\Data;
use PHPUnit\Framework\TestCase;

class EditorImportTest extends TestCase
{
    public function setUp(): void
    {
        $this->import = Data::read(__DIR__ . '/fixtures/editor.json');
    }

    public function testImportList()
    {
        $blocks = [
            [
                'type' => 'ul',
                'content' => 'A'
            ],
            [
                'type' => 'ul',
                'content' => 'B'
            ],
            [
                'type' => 'ul',
                'content' => 'C'
            ]
        ];

        $blocks = BlockConverter::editorBlocks($blocks);

        $this->assertCount(1, $blocks);
        $this->assertSame('<ul><li>A</li><li>B</li><li>C</li></ul>', $blocks[0]['content']['text']);
    }

    public function testImportSingle()
    {
        foreach ($this->import as $block) {
            $method = 'import' . $block['type'];

            if (method_exists($this, $method) === true) {
                $this->$method($block);
            }
        }
    }

    public function importBlockquote($params)
    {
        $block = new Block($params);

        $this->assertSame('quote', $block->type());
        $this->assertEquals($params['content'], $block->text());
        $this->assertEquals('', $block->citation());
    }

    public function importCode($params)
    {
        $block = new Block($params);

        $this->assertSame('code', $block->type());
        $this->assertEquals($params['content'], $block->code());
        $this->assertEquals('php', $block->language());
    }

    public function importH1($params)
    {
        $block = new Block($params);

        $this->assertSame('heading', $block->type());
        $this->assertEquals($params['content'], $block->text());
        $this->assertEquals('h1', $block->level());
    }

    public function importH2($params)
    {
        $block = new Block($params);

        $this->assertSame('heading', $block->type());
        $this->assertEquals($params['content'], $block->text());
        $this->assertEquals('h2', $block->level());
    }

    public function importH3($params)
    {
        $block = new Block($params);

        $this->assertSame('heading', $block->type());
        $this->assertEquals($params['content'], $block->text());
        $this->assertEquals('h3', $block->level());
    }

    public function importImage($params)
    {
        $block = new Block($params);

        $this->assertSame('image', $block->type());
        $this->assertEquals($params['attrs']['alt'], $block->alt());
        $this->assertEquals($params['attrs']['caption'], $block->caption());
        $this->assertEquals($params['attrs']['ratio'], $block->ratio()->value());
    }

    public function importKirbytext($params)
    {
        $block = new Block($params);

        $this->assertSame('markdown', $block->type());
        $this->assertEquals($params['content'], $block->text());
    }

    public function importOl($params)
    {
        $block = new Block($params);

        $this->assertSame('list', $block->type());
        $this->assertEquals($params['content'], $block->text());
    }

    public function importParagraph($params)
    {
        $block = new Block($params);

        $this->assertSame('text', $block->type());
        $this->assertEquals($params['content'], $block->text());
    }

    public function importUl($params)
    {
        $block = new Block($params);

        $this->assertSame('list', $block->type());
        $this->assertEquals($params['content'], $block->text());
    }

    public function importVideo($params)
    {
        $block = new Block($params);

        $this->assertSame('video', $block->type());
        $this->assertEquals($params['attrs']['caption'], $block->caption());
        $this->assertEquals($params['attrs']['src'], $block->url());
    }
}
