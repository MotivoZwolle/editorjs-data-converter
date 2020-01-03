<?php

namespace Motivo\EditorJsDataConverter\Tests\Converters;

use Illuminate\Support\Arr;
use PHPUnit\Framework\TestCase;
use Motivo\EditorJsDataConverter\Converters\ImageConverter;

class ImageConverterTest extends TestCase
{
    private $expectedModifiers = [
        'caption' => ['tag' => 'alt'],
        'withBorder' => ['tag' => 'class', 'text' => 'with-border'],
        'stretched' => ['tag' => 'class', 'text' => 'stretched-image'],
        'withBackground' => ['tag' => 'class', 'text' => 'with-background'],
    ];

    public function imageDataProvider(): array
    {
        return [
            'image wihtout any options' => [
                [
                    'file' => [
                        'url' => '/storage/uploads/content/B6bdOPrECcVcmEF74QcX703Hdt0NlYvY.png',
                    ],
                    'caption' => null,
                    'withBorder' => false,
                    'stretched' => false,
                    'withBackground' => false,
                ]
            ],
            'image with caption' => [
                [
                    'file' => [
                        'url' => '/storage/uploads/content/B6bdOPrECcVcmEF74QcX703Hdt0NlYvY.png',
                    ],
                    'caption' => 'this is my caption',
                    'withBorder' => false,
                    'stretched' => false,
                    'withBackground' => false,
                ]
            ],
            'image with border class' => [
                [
                    'file' => [
                        'url' => '/storage/uploads/content/B6bdOPrECcVcmEF74QcX703Hdt0NlYvY.png',
                    ],
                    'caption' => null,
                    'withBorder' => true,
                    'stretched' => false,
                    'withBackground' => false,
                ]
            ],
            'image with stretched class' => [
                [
                    'file' => [
                        'url' => '/storage/uploads/content/B6bdOPrECcVcmEF74QcX703Hdt0NlYvY.png',
                    ],
                    'caption' => null,
                    'withBorder' => false,
                    'stretched' => true,
                    'withBackground' => false,
                ]
            ],
            'image with background class' => [
                [
                    'file' => [
                        'url' => '/storage/uploads/content/B6bdOPrECcVcmEF74QcX703Hdt0NlYvY.png',
                    ],
                    'caption' => null,
                    'withBorder' => false,
                    'stretched' => false,
                    'withBackground' => true,
                ]
            ],
            'image with all options' => [
                [
                    'file' => [
                        'url' => '/storage/uploads/content/B6bdOPrECcVcmEF74QcX703Hdt0NlYvY.png',
                    ],
                    'caption' => 'image caption',
                    'withBorder' => true,
                    'stretched' => true,
                    'withBackground' => true,
                ]
            ],
            'Expecting an expception when no url is passed through' => [
                [
                    'file' => [],
                ]
            ]
        ];
    }

    /**
     * @dataProvider imageDataProvider
     * @test
     * @param array $data
     */
    public function to_html_method_returns_correct_html(array $data): void
    {
        $imageConverter = new ImageConverter();

        $imageString = $imageConverter->toHtml($data);

        if (! Arr::has($data, 'file.url')) {
//            $this->expectException(InvalidEditorDataException::class);
//            $this->expectExceptionMessage('No image url found');

            $this->assertStringContainsString(Arr::get($data, 'file.url', ''), $imageString);

            return;
        }

        $this->createExpectedResult($data, $imageString);

        $this->assertStringContainsString(Arr::get($data, 'file.url'), $imageString);
    }

    private function createExpectedResult(array $data, string $imageString): void
    {
        foreach ($this->expectedModifiers as $expectedResultModifier => $modifierData) {
            $tagData = $this->getModifierData($data, $expectedResultModifier);

            $expectedResultModifierValue = Arr::get($data, $expectedResultModifier);

            if (is_bool($expectedResultModifierValue) && $expectedResultModifierValue !== false) {
                $this->assertStringContainsString('class="', $imageString);
            }

            $this->assertStringContainsString($tagData, $imageString);
        }
    }

    private function getModifierData(array $data, string $key): string
    {
        if (Arr::has($data, $key)) {
            $modifierKey = Arr::get($this->expectedModifiers, $key);

            $tag = Arr::get($modifierKey, 'tag');

            $value = Arr::get($data, $key);

            if (is_string($value)) {
                return sprintf('%s="%s"', $tag, $value);
            }

            if (is_bool($value) && $value !== false) {
                $booleanData = Arr::get($modifierKey, 'text');

                return sprintf('%s', $tag, $booleanData);
            }

            return '';
        }

        return '';
    }
}
