<?php

namespace App\DTO;

class SearchQueryDto {

    /**
     * Terme de recherche principal (ex: mot-clé, nom, etc.)
     */
    private ?string $query = null;

    /**
     * Numéro de page pour la pagination
     */
    private int $page = 1;

    /**
     * Nombre d’éléments par page
     */
    private int $limit = 10;

    /**
     * Tri (ex: "name", "createdAt")
     */
    private ?string $sortBy = null;

    /**
     * Ordre du tri ("asc" ou "desc")
     */
    private string $order = 'asc';

    // --- Getters & Setters ---

    public function getQuery(): ?string
    {
        return $this->query;
    }

    public function setQuery(?string $query): self
    {
        $this->query = $query;
        return $this;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): self
    {
        $this->page = max(1, $page);
        return $this;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): self
    {
        $this->limit = max(1, $limit);
        return $this;
    }

    public function getSortBy(): ?string
    {
        return $this->sortBy;
    }

    public function setSortBy(?string $sortBy): self
    {
        $this->sortBy = $sortBy;
        return $this;
    }

    public function getOrder(): string
    {
        return $this->order;
    }

    public function setOrder(string $order): self
    {
        $this->order = strtolower($order) === 'desc' ? 'desc' : 'asc';
        return $this;
    }

}