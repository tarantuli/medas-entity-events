# medas-entity-events

Part of the [Medas framework](https://github.com/tarantuli/medas-core).

## Description

A bridge between `medas-entity-manager` and the framework event system. Annotating an entity class with one of the six dispatch attributes causes the entity manager to automatically call `dispatch()` with the specified event class whenever that entity is created, modified, or deleted.

Six attributes are provided — one before-flush and one after-flush variant for each lifecycle stage:

| Attribute                                          | Fires                                |
|----------------------------------------------------|--------------------------------------|
| `#[DispatchBeforeCreation(EventClass::class)]`     | Before a new entity is flushed       |
| `#[DispatchAfterCreation(EventClass::class)]`      | After a new entity is flushed        |
| `#[DispatchBeforeModification(EventClass::class)]` | Before a modified entity is flushed  |
| `#[DispatchAfterModification(EventClass::class)]`  | After a modified entity is flushed   |
| `#[DispatchBeforeDeletion(EventClass::class)]`     | Before an entity deletion is flushed |
| `#[DispatchAfterDeletion(EventClass::class)]`      | After an entity deletion is flushed  |

The mapping between entity classes and their event classes is discovered once on first use by `EntityEventsManager` and cached for the lifetime of the request. `BeforeChangeHandler` and `AfterChangeHandler` implement `BeforeFlushHandler` and `AfterFlushHandler` respectively, so they are called automatically by the entity manager during every flush.

The dispatched event is instantiated as `new EventClass($entity)`, so every event class must accept the entity as its first constructor argument.

## Usage

### Package developer context

Register the package:

```php
use Medas\EntityEvents\EntityEventsPackage;

EntityEventsPackage::instance();
```

**Defining an event class:**

```php
readonly class InvoiceCreated
{
    public function __construct(
        public Invoice $invoice,
    ) {}
}
```

**Annotating an entity to dispatch events:**

```php
use Medas\EntityEvents\Attributes\{DispatchAfterCreation, DispatchAfterModification, DispatchBeforeDeletion};
use Medas\EntityManager\Attributes\{Entity, Id};
use Medas\Core\Interfaces\{HasId, Uuid};

#[Entity]
#[DispatchAfterCreation(InvoiceCreated::class)]
#[DispatchAfterModification(InvoiceUpdated::class)]
#[DispatchBeforeDeletion(InvoiceDeletionRequested::class)]
class Invoice implements HasId
{
    #[Id]
    public Uuid $id;

    public string $status;

    public function id(): Uuid
    {
        return $this->id;
    }
}
```

When the entity manager flushes an `Invoice` creation, `dispatch(new InvoiceCreated($invoice))` is called automatically after the flush completes. No additional wiring is required.

**Listening to the dispatched event:**

```php
use Medas\Core\Attributes\{EventListener, Service};

#[Service]
readonly class InvoiceNotifier
{
    public function __construct(
        private Mailer $mailer,
    ) {}

    #[EventListener]
    public function onCreated(InvoiceCreated $event): void
    {
        $this->mailer->sendInvoiceConfirmation($event->invoice);
    }
}
```

**Before-flush events** — use the `Before*` variants when the handler needs to run within the same transaction or needs to abort the flush:

```php
use Medas\EntityEvents\Attributes\DispatchBeforeModification;

#[Entity]
#[DispatchBeforeModification(InvoiceAboutToChange::class)]
class Invoice implements HasId { /* ... */ }
```

```php
#[EventListener]
public function onBeforeChange(InvoiceAboutToChange $event): void
{
    // Validate or mutate the entity before it is written to storage
    if ($event->invoice->status === 'paid') {
        throw new \DomainException('Cannot modify a paid invoice.');
    }
}
```

**Multiple attributes on one entity** — all six attributes can be combined freely. Each fires independently for its lifecycle stage:

```php
#[Entity]
#[DispatchAfterCreation(InvoiceCreated::class)]
#[DispatchAfterModification(InvoiceUpdated::class)]
#[DispatchAfterDeletion(InvoiceDeleted::class)]
#[DispatchBeforeDeletion(InvoiceDeletionRequested::class)]
class Invoice implements HasId { /* ... */ }
```

### Backend user context

Once entities are annotated and the package is registered, event dispatch is fully automatic — it requires no manual calls. The events flow through the same `dispatch()` / `#[EventListener]` mechanism used everywhere else in the framework, so standard listener patterns apply.

If no listener is registered for a dispatched event class, the dispatch is a no-op and has no performance impact beyond the initial entity-events discovery scan (which is cached after the first flush).
