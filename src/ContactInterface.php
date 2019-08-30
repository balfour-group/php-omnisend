<?php

namespace Balfour\Omnisend;

use Carbon\CarbonInterface;

interface ContactInterface
{
    /**
     * @return CarbonInterface|null
     */
    public function getCreateDate(): ?CarbonInterface;

    /**
     * @return string
     */
    public function getEmail(): string;

    /**
     * @return string|null
     */
    public function getFirstName(): ?string;

    /**
     * @return string|null
     */
    public function getLastName(): ?string;

    /**
     * @return string|null
     */
    public function getCountry(): ?string;

    /**
     * @return string|null
     */
    public function getCountryCode(): ?string;

    /**
     * @return string|null
     */
    public function getState(): ?string;

    /**
     * @return string|null
     */
    public function getCity(): ?string;

    /**
     * @return string|null
     */
    public function getAddress(): ?string;

    /**
     * @return string|null
     */
    public function getPostalCode(): ?string;

    /**
     * @return string|null
     */
    public function getGender(): ?string;

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string;

    /**
     * @return CarbonInterface|null
     */
    public function getDateOfBirth(): ?CarbonInterface;

    /**
     * @return mixed[]
     */
    public function getCustomProperties(): array;

    /**
     * @return string[]
     */
    public function getTags(): array;
}
