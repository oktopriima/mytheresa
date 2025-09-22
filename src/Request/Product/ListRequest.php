<?php

namespace App\Request\Product;

use App\Request\Pagination\PaginationRequest;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

class ListRequest
{
    #[Assert\Positive(message: "Price must be greater than 0")]
    #[Assert\LessThan(value: 1000000, message: "Price must be less than 1.000.000")]
    #[Context([AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false])]
    public ?int $priceLessThan = null;

    #[Assert\Valid]
    public PaginationRequest $pagination;

    public function __construct()
    {
        $this->pagination = new PaginationRequest();
    }

    public function getPriceLessThan(): ?int
    {
        return $this->priceLessThan;
    }

    public function setPriceLessThan(?string $priceLessThan): void
    {
        $this->priceLessThan = $priceLessThan !== null ? (int)$priceLessThan : null;
    }

    public function getLimit(): ?int
    {
        return $this->pagination->limit;
    }

    public function setLimit(?string $limit): void
    {
        $this->pagination->limit = $limit !== null ? (int)$limit : 20;
    }

    public function getPage(): ?int
    {
        return $this->pagination->page;
    }

    public function setPage(?string $page): void
    {
        $this->pagination->page = $page !== null ? (int)$page : 1;
    }
}
