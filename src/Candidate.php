<?php

namespace Palto;

class Candidate
{
    private array $candidate;

    public function __construct(array $candidate)
    {
        $this->candidate = $candidate;
    }

    public function getDonorUrl()
    {
        return $this->candidate['donor_url'];
    }

    public function getTitle()
    {
        return $this->candidate['title'];
    }
}