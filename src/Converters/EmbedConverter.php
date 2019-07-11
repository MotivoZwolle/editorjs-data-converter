<?php

namespace Motivo\EditorJsDataConverter\Converters;

use Illuminate\Support\Arr;
use Motivo\EditorJsDataConverter\Converters\Contracts\Converter;
use Motivo\EditorJsDataConverter\Exceptions\InvalidEditorDataException;
use Motivo\EditorJsDataConverter\Traits\WithHtml;
use Spatie\Html\Elements\Element;

class EmbedConverter implements Converter
{
    use WithHtml;

    public function toHtml(array $itemData): string
    {
        $this->validate($itemData);

        $container = $this->getContainer();

        $iframe = $this->getIframe(Arr::get($itemData, 'service'), Arr::get($itemData, 'embed'));

        $caption = $this->getCaption(Arr::get($itemData, 'caption'));

        return $container->child([$iframe, $caption]);
    }

    private function getContainer(): Element
    {
        return $this->html->element('div')->class('embed-container');
    }

    private function getIframeContainer(): Element
    {
        return $this->html->element('div')->class('embed-responsive embed-responsive-16by9');
    }

    private function getCaption(?string $caption): ?Element
    {
        if ($caption) {
            return $this->html->element('p')->html($caption)->class('caption');
        }

        return null;
    }

    private function getIframe(string $service, string $url): Element
    {
        $container = $this->getIframeContainer();
        $iframe = $this->{'get' . ucfirst($service)}($url);

        return $container->child($iframe);
    }

    private function getYoutube(string $url): Element
    {
        $attrs = [
            'src' => $url,
            'frameborder' => '0',
            'allowFullscreen' => 'true',
        ];

        return $this->html->element('iframe')->attributes($attrs)->class('embed-responsive-item');
    }

    private function getVimeo(string $url): Element
    {
        $attrs = [
            'src' => $url,
        ];

        return $this->html->element('iframe')->attributes($attrs)->class('embed-responsive-item');
    }

    private function validate(array $itemData): void
    {
        if (! Arr::has($itemData, 'service')) {
            throw InvalidEditorDataException::noEmbedServiceFound('No embed service found');
        }

        if (! Arr::has($itemData, 'embed')) {
            throw InvalidEditorDataException::noEmbedUrlFound('No embed url found');
        }

        if (! method_exists(EmbedConverter::class, 'get' . ucfirst(Arr::get($itemData, 'service')))) {
            throw InvalidEditorDataException::embedServiceNotSupported(sprintf('Embed service %s not supported', Arr::get($itemData, 'service')));
        }
    }
}
