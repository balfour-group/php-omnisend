# php-omnisend

A library for interacting with the Omnisend API.

*This library is in early release and is pending unit tests.*

## Table of Contents

* [Installation](#installation)
* [Usage](#usage)
    * [Creating a Client](#creating-a-client)
    * [Contacts](#contacts)
        * [Create Contact](#create-contact)
        * [Update Contact](#update-contact)
        * [Retrieve Contact](#retrieve-contact)
        * [List Contacts](#list-contacts)
        * [Subscribe Contact](#subscribe-contact)
        * [Unsubscribe Contact](#unsubscribe-contact)
    * [Events](#events)
        * [List Events](#list-events)
        * [Retrieve Event](#retrieve-event)
        * [Trigger Event](#trigger-event)
    * [Misc Calls](#misc-calls)
* [Laravel Integration](#laravel-integration)
    * [Configuration](#configuration)
    * [Job Handlers](#job-handlers)
        * [CreateContact](#createcontact)
        * [UpdateContact](#updatecontact)
        * [TriggerEvent](#triggerevent)

## Installation

```bash
composer require balfour/php-omnisend
```
    
## Usage

Please see https://api-docs.omnisend.com/v3 for full API documentation.

### Creating a Client

```php
use GuzzleHttp\Client;
use Balfour\Omnisend\Omnisend;

$client = new Client();
$omnisend = new Omnisend($client, 'your-api-key');
```

### Contacts

#### Create Contact

```php
use Carbon\Carbon;
use Balfour\Omnisend\ContactGender;
use Balfour\Omnisend\ContactStatus;

$response = $omnisend->createContact([
    'email' => 'foo@bar.com',
    'firstName' => 'John',
    'lastName' => 'Doe',
    'tags' => [
        'source: Test',
    ],
    'gender' => ContactGender::MALE,
    'phone' => '+27721111111',
    'birthdate' => '1987-03-28',
    'customProperties' => [
        'group' => 'ADMIN',
    ],
]);

// you can also pass an implementation of ContactInterface instead of an array
// of attributes
// eg: assuming $contact is a model which implements ContactInterface
$response = $omnisend->createContact($contact);

// specifying opt-in status & optional params
$response = $omnisend->createContact(
    [
        'email' => 'foo@bar.com',
        'firstName' => 'John',
        'lastName' => 'Doe',
        'tags' => [
            'source: Test',
        ],
        'gender' => ContactGender::MALE,
        'phone' => '+27721111111',
        'birthdate' => '1987-03-28',
        'customProperties' => [
            'group' => 'ADMIN',
        ],
    ],
    ContactStatus::UNSUBSCRIBED,
    Carbon::now(), // status date
    true, // send welcome email
    '127.0.0.1', // opt-in ip address (ipv4 or ipv6)
    Carbon::now(), // opt-in date & time
);
```

#### Update Contact

```php
$response = $omnisend->updateContact('5d5be5635c3762bd644e947c', [
    'firstName' => 'John',
    'lastName' => 'Doe',
]);

// you can also pass an implementation of ContactInterface instead of an array
// of attributes
// eg: assuming $contact is a model which implements ContactInterface
$response = $omnisend->updateContact('5d5be5635c3762bd644e947c', $contact);

// update contact using email address as identifier
$response = $omnisend->updateContactByEmail('matthew@masterstart.com', [
    'firstName' => 'John',
    'lastName' => 'Doe',
]);
```

#### Retrieve Contact

```php
$response = $omnisend->getContact('5d5be5635c3762bd644e947c');
```

#### List Contacts

```php
use Balfour\Omnisend\ContactStatus;

$response = $omnisend->listContacts();

// list contacts from offset with limit
$response = $omnisend->listContacts(
    50, // starting from offset 50
    100 // limit to 100 results
);

// list contacts by status
$response = $omnisend->listContactsByStatus(ContactStatus::SUBSCRIBED);
```

#### Subscribe Contact

```php
$response = $omnisend->subscribeContact('5d5be5635c3762bd644e947c');

// or via email address
$response = $omnisend->subscribeContactByEmail('matthew@masterstart.com');
```

#### Unsubscribe Contact

```php
$response = $omnisend->unsubscribeContact('5d5be5635c3762bd644e947c');

// or via email address
$response = $omnisend->unsubscribeContactByEmail('matthew@masterstart.com');
```

### Events

#### List Events

```php
$response = $omnisend->listEvents();
```

#### Retrieve Event

``php
$response = $omnisend->getEvent('5d5cf4d98653ed49cd7f1bd2');

// you can also retrieve an event by name
// the function will return 'null' if no matching event is found
$response = $omnisend->getEventByName('Payment Complete');
``

#### Trigger Event

```php
use Balfour\Omnisend\Event;

$omnisend->triggerEvent(
    '5d5cf4d98653ed49cd7f1bd2',
    'matthew@masterstart.com',
    [
        'payment_method' => 'CREDIT CARD',
        'amount' => 'R1200.00',
    ]
);

// you can also pass in an implementation of EventInterface
$paymentCompleteEvent = new Event(
    '5d5cf4d98653ed49cd7f1bd2',
    [
        'payment_method' => 'CREDIT CARD',
        'amount' => 'R1200.00',
    ]
);

$omnisend->triggerEvent($paymentCompleteEvent, 'matthew@masterstart.com');
```

### Misc Calls

For any other API calls which don't have class functions, you can call the following
methods directly on the client.

```php
// examples:
$omnisend->get('products');
$omnisend->get('products', ['sort' => 'createdAt']);

$omnisend->post('products', [
    'productId' => '1234',
    'title' => 'My Product',
    // .....
]);

$omnisend->put('products', [
    'productId' => '1234',
    'title' => 'My Product',
    // .....
]);

$omnisend->delete('products/1234');
```

## Laravel Integration

This package comes bundled with a Laravel ServiceProvider & utility classes for easy
integration into a Laravel project.

```php
use Balfour\Omnisend\Laravel\Event;
use Balfour\Omnisend\Laravel\NamedEvent;
use Balfour\Omnisend\Omnisend;

// resolving client from ioc container
$omnisend = app(Omnisend::class);

// triggering an event
$paymentCompleteEvent = new Event(
    '5d5cf4d98653ed49cd7f1bd2',
    [
        'payment_method' => 'CREDIT CARD',
        'amount' => 'R1200.00',
    ]
);
$paymentCompleteEvent->fire();

// queue an event
// this will use the configured queue (omnisend by default)
$paymentCompleteEvent->enqueue('matthew@masterstart.com');

// the queue name can be overridden
$paymentCompleteEvent->enqueue('matthew@masterstart.com', 'my_queue');

// the laravel integration also comes with a NamedEvent class, where can event
// can be triggered by name instead of id
// the event id is resolved at trigger time from the name, and is cached for subsequent
// triggers
$paymentCompleteEvent = new NamedEvent(
    'Payment Complete',
    [
        'payment_method' => 'CREDIT CARD',
        'amount' => 'R1200.00',
    ]
);
$paymentCompleteEvent->fire();
```

### Configuration

The config can be published using `php artisan vendor:publish`.

The following environment variables are supported:

`OMNISEND_ENABLED` - Enable or disable Omnisend integration (defaults to `false`)

`OMNISEND_API_KEY` - Your Omnisend API key

`OMNISEND_QUEUE` - The queue on which jobs will be processed (defaults to `omnisend`)

`OMNISEND_SEND_WELCOME_EMAIL` - If true, a welcome email will be sent to a contact upon creation (defaults to `false`)

`OMNISEND_DEFAULT_CONTACT_STATUS` - The default status when a contact is created. (defaults to `subscribed`)

### Job Handlers

The following job handlers are included:

#### CreateContact

```php
use Balfour\Omnisend\ContactStatus;
use Balfour\Omnisend\Laravel\Jobs\CreateContact;
use Carbon\Carbon;

// eg: assuming $contact is a model which implements ContactInterface
CreateContact::enqueue($contact);

// or
CreateContact::enqueue(
    $contact,
    ContactStatus::SUBSCRIBED,
    true, // send welcome email
    '127.0.0.1', // opt-in ip
    Carbon::now() // opt-in date
);
```

#### UpdateContact

```php
use Balfour\Omnisend\Laravel\Jobs\UpdateContact;

// eg: assuming $contact is a model which implements ContactInterface
UpdateContact::enqueue($contact);
```

#### TriggerEvent

```php
use Balfour\Omnisend\Laravel\Event;
use Balfour\Omnisend\Laravel\Jobs\TriggerEvent;

$paymentCompleteEvent = new Event(
    '5d5cf4d98653ed49cd7f1bd2',
    [
        'payment_method' => 'CREDIT CARD',
        'amount' => 'R1200.00',
    ]
);

TriggerEvent::enqueue($event, 'matthew@masterstart.com');
```
