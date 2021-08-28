<?php

namespace LandKit\Optimizer;

use SimpleXMLIterator;
use stdClass;

class MetaTags
{
    /**
     * @var SimpleXMLIterator
     */
    protected $meta;

    /**
     * @var stdClass|null
     */
    protected $data = null;

    /**
     * @var string[]
     */
    protected $tags = [
        'property' => 'og',
        'name' => 'twitter'
    ];

    /**
     * Create new MetaTags instance.
     */
    public function __construct()
    {
        $this->meta = new SimpleXMLIterator('<meta/>');
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->data->$name ?? null;
    }

    /**
     * @param string $name
     * @param float|int|string $value
     * @return void
     */
    public function __set(string $name, $value)
    {
        if (!$this->data) {
            $this->data = new stdClass();
        }

        $this->data->$name = $value;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return isset($this->data->$name);
    }

    /**
     * @return SimpleXMLIterator
     */
    public function meta(): SimpleXMLIterator
    {
        return $this->meta;
    }

    /**
     * @return stdClass|null
     */
    public function data(string $title = '', string $description = '', string $url = '', string $image = '')
    {
        !$title ?: $this->title = $title;
        !$description ?: $this->description = $description;
        !$url ?: $this->url = $url;
        !$image ?: $this->image = $image;

        return $this->data;
    }

    /**
     * @param bool $sort
     * @return array
     */
    public function debug(bool $sort = true): array
    {
        $debug = explode("&", implode(">&<", explode("><", $this->build())));

        if ($sort) {
            rsort($debug);
        }

        return $debug;
    }

    /**
     * @return string
     */
    public function build(): string
    {
        $build = '';

        for ($this->meta->rewind(); $this->meta->valid(); $this->meta->next()) {
            $build .= $this->meta->current()->asXML();
        }

        return urldecode($build);
    }

    /**
     * @param string $meta
     * @param array $attributes
     * @return void
     */
    protected function buildMeta(string $meta, array $attributes)
    {
        foreach ($attributes as $name => $content) {
            $add = $this->meta->addChild('meta');
            $add->addAttribute($meta, $name);
            $add->addAttribute('content', $content);
        }
    }

    /**
     * @param string $name
     * @param string $content
     * @return void
     */
    protected function buildTag(string $name, string $content)
    {
        $this->meta->addChild($name, $content);
    }

    /**
     * @param string $rel
     * @param string $href
     * @return void
     */
    protected function buildLink(string $rel, string $href)
    {
        $add = $this->meta->addChild('link');
        $add->addAttribute('rel', $rel);
        $add->addAttribute('href', $href);
    }

    /**
     * @param string $value
     * @return string
     */
    protected function filter(string $value): string
    {
        return urlencode(filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    }
}