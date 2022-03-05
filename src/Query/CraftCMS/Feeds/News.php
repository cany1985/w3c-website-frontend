<?php

declare(strict_types=1);

namespace App\Query\CraftCMS\Feeds;

use App\Service\CraftCMS;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Query\GraphQLQuery;

class News extends GraphQLQuery
{

    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    /**
     * Query to retrieve
     *
     * @param string $siteHandle Site ID of page content
     *
     * @throws GraphQLQueryException
     */
    public function __construct(string $siteHandle, int $limit)
    {
        $this->setGraphQLFromFile(__DIR__ . '/../graphql/feeds/news.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/defaultFlexibleComponents.graphql')
            ->setRootPropertyPath('[entries]')
            ->addVariable('site', $siteHandle)
            ->addVariable('limit', $limit)
        ;
    }
}
