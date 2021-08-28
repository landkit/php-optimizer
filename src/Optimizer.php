<?php

namespace LandKit\Optimizer;

class Optimizer extends MetaTags
{
    /**
     * @param string $title
     * @param string $description
     * @param string $url
     * @param string $image
     * @param bool $follow
     * @return Optimizer
     */
    public function optimize(
        string $title,
        string $description,
        string $url,
        string $image,
        bool $follow = true
    ): Optimizer {
        $this->data($title, $description, $url, $image);

        $title = $this->filter($title);
        $description = $this->filter($description);

        $this->buildLink('canonical', $url);
        $this->buildTag('title', $title);
        $this->buildMeta('name', ['description' => $description]);
        $this->buildMeta('name', ['robots' => $follow ? 'index, follow' : 'noindex, nofollow']);

        foreach ($this->tags as $meta => $prefix) {
            $this->buildMeta($meta, [
                "{$prefix}:title" => $title,
                "{$prefix}:description" => $description,
                "{$prefix}:url" => $url,
                "{$prefix}:image" => $image
            ]);
        }

        $this->buildMeta('itemprop', [
            'title' => $title,
            'description' => $description,
            'url' => $url,
            'image' => $image
        ]);

        return $this;
    }

    /**
     * @param string $siteName
     * @param string $type
     * @param string $locale
     * @return Optimizer
     */
    public function openGraph(string $siteName, string $type = 'article', string $locale = 'pt_BR'): Optimizer
    {
        $this->buildMeta('property', [
            'og:site_name' => $siteName,
            'og:type' => $type,
            'og:locale' => $locale
        ]);

        return $this;
    }

    /**
     * @param string $creator
     * @param string $site
     * @param string $domain
     * @param string $card
     * @return Optimizer
     */
    public function twitterCard(
        string $creator,
        string $site,
        string $domain,
        string $card = 'summary_large_image'
    ): Optimizer {
        $this->buildMeta('name', [
            'twitter:creator' => $creator,
            'twitter:site' => $site,
            'twitter:domain' => $domain,
            'twitter:card' => $card
        ]);

        return $this;
    }

    /**
     * @param string $appId
     * @param array $admins
     * @return Optimizer
     */
    public function facebook(string $appId = '', array $admins = []): Optimizer
    {
        if ($appId) {
            $add = $this->meta->addChild('meta');
            $add->addAttribute('property', 'fb:app_id');
            $add->addAttribute('content', $appId);
        } elseif ($admins) {
            foreach ($admins as $admin) {
                $add = $this->meta->addChild('meta');
                $add->addAttribute('property', 'fb:admins');
                $add->addAttribute('content', $admin);
            }
        }

        return $this;
    }

    /**
     * @param string $facebookPage
     * @param string $facebookAuthor
     * @return Optimizer
     */
    public function publisher(string $facebookPage, string $facebookAuthor = ''): Optimizer
    {
        $this->buildMeta('property', ['article:publisher' => "https://www.facebook.com/{$facebookPage}"]);

        if ($facebookAuthor) {
            $this->buildMeta('property', ['article:author' => "https://www.facebook.com/{$facebookAuthor}"]);
        }

        return $this;
    }
}