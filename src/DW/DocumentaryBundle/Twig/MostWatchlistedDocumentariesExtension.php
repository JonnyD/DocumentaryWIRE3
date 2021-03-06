<?php

namespace DW\DocumentaryBundle\Twig;

use Doctrine\Common\Collections\ArrayCollection;
use DW\DocumentaryBundle\Entity\Documentary;
use DW\DocumentaryBundle\Service\DocumentaryService;

class MostWatchlistedDocumentariesExtension extends \Twig_Extension
{
    /**
     * @var DocumentaryService
     */
    private $documentaryService;

    /**
     * @param DocumentaryService $documentaryService
     */
    public function __construct(DocumentaryService $documentaryService)
    {
        $this->documentaryService = $documentaryService;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction("mostWatchListedDocumentaries", [$this, "mostWatchListedDocumentaries"])
        ];
    }

    /**
     * @param int $limit
     * @return ArrayCollection|Documentary[]
     */
    public function mostWatchListedDocumentaries(int $limit)
    {
        return $this->documentaryService->getMostWatchlistedDocumentaries($limit);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'most_watchlisted_documentaries_extension';
    }
}