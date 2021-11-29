<?php

declare(strict_types=1);

namespace App\Query\CraftCMS\PressReleases;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Query\GraphQLQuery;
use Strata\Data\Transform\Data\CallableData;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Filters extends GraphQLQuery
{
    private RouterInterface $router;
    private TranslatorInterface $translator;

    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    /**
     * Set up query
     *
     * @param int $siteId        Site ID of page content
     * @param int $cacheLifetime Cache lifetime to store HTTP response for, defaults to 1 hour
     *
     * @throws GraphQLQueryException
     */
    public function __construct(
        RouterInterface $router,
        TranslatorInterface $translator,
        int $siteId,
        int $cacheLifetime = CacheLifetime::HOUR
    ) {
        $this->router     = $router;
        $this->translator = $translator;
        $this->setGraphQLFromFile(__DIR__ . '/../graphql/press-releases/filters.graphql')
            ->addVariable('siteId', $siteId)
            ->cache($cacheLifetime)
        ;
    }

    public function getMapping()
    {
        return [
            '[archives]' => new CallableData([$this, 'transformArchives'], '[first][year]', '[last][year]')
        ];
    }

    public function transformArchives(string $first, string $last): array
    {
        $archives = [
            [
                'title' => $this->translator->trans(
                    'listing.press_releases.filters.all',
                    [],
                    'w3c_website_templates_bundle'
                ),
                'url'   => $this->router->generate('app_pressreleases_index')
            ]
        ];
        for ($year = $first; $year <= $last; $year++) {
            $archives[] = [
                'title' => $year,
                'url'   => $this->router->generate('app_pressreleases_archive', ['year' => $year])
            ];
        }

        return $archives;
    }
}
