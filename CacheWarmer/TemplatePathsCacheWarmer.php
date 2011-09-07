<?php

/*
 * This file is part of the Liip/ThemeBundle
 *
 * (c) Liip AG
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */


namespace Liip\ThemeBundle\CacheWarmer;

use Symfony\Bundle\FrameworkBundle\CacheWarmer\TemplatePathsCacheWarmer as BaseTemplatePathsCacheWarmer,
    Symfony\Component\HttpKernel\CacheWarmer\CacheWarmer,
    Symfony\Bundle\FrameworkBundle\Templating\Loader\TemplateLocator,
    Symfony\Bundle\FrameworkBundle\CacheWarmer\TemplateFinderInterface;

use Liip\ThemeBundle\ActiveTheme;

class TemplatePathsCacheWarmer extends BaseTemplatePathsCacheWarmer
{
    protected $theme;

    /**
     * Constructor.
     *
     * @param TemplateFinderInterface   $finder  A template finder
     * @param TemplateLocator           $locator The template locator
     */
    public function __construct(TemplateFinderInterface $finder, TemplateLocator $locator, ActiveTheme $theme)
    {
        $this->theme = $theme;

        parent::__construct($finder, $locator);
    }

    /**
     * Warms up the cache.
     *
     * @param string $cacheDir The cache directory
     */
    public function warmUp($cacheDir)
    {
        $locator = $this->locator->getLocator();

        $allTemplates = $this->finder->findAllTemplates();

        $templates = array();
        foreach ($this->theme->getThemes() as $theme) {
            $this->theme->setName($theme);
            foreach ($allTemplates as $template) {
if ($template->getPath() === '@LiipHelloBundle/Resources/views/Hello/index.html.twig' && 'phone' === $this->theme->getName()) {
//    var_dump($template->getPath(), $this->theme->getName(), $locator->locate($template->getPath())); die;
}

                $templates[$template->getLogicalName().'|'.$theme] = $locator->locate($template->getPath());
            }
        }

        $this->writeCacheFile($cacheDir.'/templates.php', sprintf('<?php return %s;', var_export($templates, true)));
    }

    /**
     * Checks whether this warmer is optional or not.
     *
     * @return Boolean always true
     */
    public function isOptional()
    {
        return true;
    }
}