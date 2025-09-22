<?php

namespace App\Request\Pagination;

use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

use Symfony\Component\Validator\Constraints as Assert;

class PaginationRequest
{
    #[Assert\Positive(message: "Page must be greater than 0")]
    #[Assert\LessThan(value: 1000, message: "Page must be less than 1000")]
    #[Context([AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false])]
    public ?int $page = null;

    #[Assert\Positive(message: "Limit must be greater than 0")]
    #[Assert\LessThanOrEqual(value: 20, message: "Maximum limit is 20")]
    #[Context([AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false])]
    public ?int $limit = null;

    public function __construct(int $page = 1, int $limit = 20)
    {
        $this->page = $page;
        $this->limit = $limit;
    }
}
