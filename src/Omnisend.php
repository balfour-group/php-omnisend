<?php

namespace Balfour\Omnisend;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class Omnisend
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string|null
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $uri;

    /**
     * @param Client $client
     * @param string|null $apiKey
     * @param string|null $uri
     */
    public function __construct(
        Client $client,
        ?string $apiKey = null,
        ?string $uri = null
    ) {
        $this->client = $client;
        $this->apiKey = $apiKey;
        $this->uri = $uri ?? 'https://api.omnisend.com/v3';
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @param string $uri
     */
    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @param string $apiKey
     */
    public function setAPIKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return string|null
     */
    public function getAPIKey(): ?string
    {
        return $this->apiKey;
    }

    /**
     * @param string $endpoint
     * @param mixed[] $params
     * @return string
     */
    protected function getBaseUri(string $endpoint, array $params = []): string
    {
        $uri = $this->uri;
        $uri = rtrim($uri, '/');
        $uri .= '/' . ltrim($endpoint, '/');

        if (count($params) > 0) {
            $uri .= '?' . http_build_query($params);
        }

        return $uri;
    }

    /**
     * @return mixed[]
     */
    protected function getDefaultRequestOptions(): array
    {
        return [
            'connect_timeout' => 2000,
            'timeout' => 6000,
            'headers' => [
                'X-API-KEY' => $this->apiKey,
            ],
        ];
    }

    /**
     * @param Request $request
     * @return mixed[]|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function sendRequest(Request $request): ?array
    {
        $options = $this->getDefaultRequestOptions();
        $response = $this->client->send($request, $options);
        $body = (string) $response->getBody();
        return json_decode($body, true);
    }

    /**
     * @param string $endpoint
     * @param mixed[] $params
     * @return mixed[]|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $endpoint, array $params = []): ?array
    {
        $request = new Request('GET', $this->getBaseUri($endpoint, $params));
        return $this->sendRequest($request);
    }

    /**
     * @param string $endpoint
     * @param mixed[] $payload
     * @param mixed[] $params
     * @return mixed[]|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post(string $endpoint, array $payload = [], array $params = []): ?array
    {
        $request = new Request(
            'POST',
            $this->getBaseUri($endpoint, $params),
            [
                'Content-type' => 'application/json',
            ],
            json_encode($payload)
        );
        return $this->sendRequest($request);
    }

    /**
     * @param string $endpoint
     * @param mixed[] $payload
     * @param mixed[] $params
     * @return mixed[]|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function patch(string $endpoint, array $payload = [], array $params = []): ?array
    {
        $request = new Request(
            'PATCH',
            $this->getBaseUri($endpoint, $params),
            [
                'Content-type' => 'application/json',
            ],
            json_encode($payload)
        );
        return $this->sendRequest($request);
    }

    /**
     * @param string $endpoint
     * @param mixed[] $payload
     * @param mixed[] $params
     * @return mixed[]|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function put(string $endpoint, array $payload = [], array $params = []): ?array
    {
        $request = new Request(
            'PUT',
            $this->getBaseUri($endpoint, $params),
            [
                'Content-type' => 'application/json',
            ],
            json_encode($payload)
        );
        return $this->sendRequest($request);
    }

    /**
     * @param string $endpoint
     * @param mixed[] $params
     * @return mixed[]|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(string $endpoint, array $params = []): ?array
    {
        $request = new Request('DELETE', $this->getBaseUri($endpoint, $params));
        return $this->sendRequest($request);
    }

    /**
     * @param string $id
     * @return mixed[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getContact(string $id): array
    {
        return $this->get(sprintf('contacts/%s', $id));
    }

    /**
     * @param int $offset
     * @param int $limit
     * @param mixed[] $filters
     * @return mixed[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function listContacts(int $offset = 0, int $limit = 100, array $filters = []): array
    {
        $filters['offset'] = $offset;
        $filters['limit'] = $limit;

        return $this->get('contacts', $filters);
    }

    /**
     * @param string $status
     * @param int $offset
     * @param int $limit
     * @return mixed[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function listContactsByStatus(string $status, int $offset = 0, int $limit = 100): array
    {
        return $this->listContacts($offset, $limit, ['status' => $status]);
    }

    /**
     * @param ContactInterface|mixed[] $attributes
     * @param string $status
     * @param CarbonInterface|null $statusDate
     * @param bool $sendWelcomeEmail
     * @param string|null $optInIp
     * @param CarbonInterface|null $optInDate
     * @return mixed[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createContact(
        $attributes,
        string $status = ContactStatus::SUBSCRIBED,
        ?CarbonInterface $statusDate = null,
        bool $sendWelcomeEmail = false,
        ?string $optInIp = null,
        ?CarbonInterface $optInDate = null
    ): array {
        if ($attributes instanceof ContactInterface) {
            $attributes = $this->makeAttributesFromContact($attributes);
        }

        $statusDate = $statusDate ?? Carbon::now();

        $attributes['status'] = $status;
        $attributes['statusDate'] = $statusDate->toIso8601String();
        $attributes['sendWelcomeEmail'] = $sendWelcomeEmail;

        if ($optInIp) {
            $attributes['optInIp'] = $optInIp;

            $optInDate = $optInDate ?? Carbon::now();
            $attributes['optInDate'] = $optInDate->toIso8601String();
        }

        return $this->post('contacts', $attributes);
    }

    /**
     * @param string $id
     * @param ContactInterface|mixed[] $attributes
     * @return mixed[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateContact(string $id, $attributes): array
    {
        if ($attributes instanceof ContactInterface) {
            $attributes = $this->makeAttributesFromContact($attributes);
        }

        return $this->patch(sprintf('contacts/%s', $id), $attributes);
    }

    /**
     * @param string $email
     * @param ContactInterface|mixed[] $attributes
     * @return mixed[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateContactByEmail(string $email, $attributes): array
    {
        if ($attributes instanceof ContactInterface) {
            $attributes = $this->makeAttributesFromContact($attributes);
        }

        return $this->patch('contacts', $attributes, ['email' => $email]);
    }

    /**
     * @param ContactInterface $contact
     * @return array
     */
    protected function makeAttributesFromContact(ContactInterface $contact): array
    {
        // see https://api-docs.omnisend.com/v3/contacts/create-contacts for documented attributes
        $dob = $contact->getDateOfBirth();

        return [
            'email' => $contact->getEmail(),
            'createdAt' => $contact->getCreateDate()->toIso8601String(),
            'firstName' => $contact->getFirstName(),
            'lastName' => $contact->getLastName(),
            'tags' => $contact->getTags(),
            'country' => $contact->getCountry(),
            'countryCode' => $contact->getCountryCode(),
            'state' => $contact->getState(),
            'city' => $contact->getCity(),
            'address' => $contact->getAddress(),
            'postalCode' => $contact->getPostalCode(),
            'gender' => $contact->getGender(),
            'phone' => $contact->getPhoneNumber(),
            'birthdate' => $dob ? $dob->toDateString() : null,
            'customProperties' => $contact->getCustomProperties(),
        ];
    }

    /**
     * @param string $id
     * @return mixed[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function subscribeContact(string $id): array
    {
        $attributes = [
            'status' => ContactStatus::SUBSCRIBED,
            'statusDate' => Carbon::now()->toIso8601String(),
        ];

        return $this->updateContact($id, $attributes);
    }

    /**
     * @param string $email
     * @return mixed[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function subscribeContactByEmail(string $email): array
    {
        $attributes = [
            'status' => ContactStatus::SUBSCRIBED,
            'statusDate' => Carbon::now()->toIso8601String(),
        ];

        return $this->updateContactByEmail($email, $attributes);
    }

    /**
     * @param string $id
     * @return mixed[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function unsubscribeContact(string $id): array
    {
        $attributes = [
            'status' => ContactStatus::UNSUBSCRIBED,
            'statusDate' => Carbon::now()->toIso8601String(),
        ];

        return $this->updateContact($id, $attributes);
    }

    /**
     * @param string $email
     * @return mixed[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function unsubscribeContactByEmail(string $email): array
    {
        $attributes = [
            'status' => ContactStatus::UNSUBSCRIBED,
            'statusDate' => Carbon::now()->toIso8601String(),
        ];

        return $this->updateContactByEmail($email, $attributes);
    }

    /**
     * @return mixed[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function listEvents(): array
    {
        return $this->get('events');
    }

    /**
     * @param string $id
     * @return mixed[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEvent(string $id): array
    {
        return $this->get(sprintf('events/%s', $id));
    }

    /**
     * @param string $name
     * @return mixed[]|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEventByName(string $name): ?array
    {
        $events = $this->listEvents();

        foreach ($events as $event) {
            if (strcasecmp($event['name'], $name) === 0) {
                return $event;
            }
        }

        return null;
    }

    /**
     * @param EventInterface|string $id
     * @param string $email
     * @param array $fields
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function triggerEvent($id, string $email, array $fields = []): void
    {
        if ($id instanceof EventInterface) {
            $fields = $id->getFields();
            $id = $id->getID();
        }

        $payload = [
            'email' => $email,
        ];

        // api does not support null field values
        // omnisend returns a 502 - bad gateway
        $fields = array_filter($fields, function ($value) {
            return $value !== null;
        });

        if (count($fields) > 0) {
            $payload['fields'] = $fields;
        }

        $this->post(sprintf('events/%s', $id), $payload);
    }
}
