Ja. Zuerst würde ich die Begriffe etwas auseinanderziehen, weil Dinge wie **Closure**, **Model**, **OOP**, **Dependency Injection** usw. häufig in einen Topf geworfen werden, technisch aber zu unterschiedlichen Ebenen gehören.

Bei PHP kann man grob zwischen folgenden Ebenen unterscheiden:

1. **Programmierparadigmen** – Wie strukturiere ich grundsätzlich Programme?
2. **Sprachkonzepte** – Welche Mechanismen bietet PHP?
3. **Entwurfsprinzipien und Patterns** – Wie löse ich wiederkehrende Architekturprobleme?
4. **Architekturkonzepte** – Wie zerlege ich eine größere Anwendung?
5. **Framework-Konzepte** – Wie setzt z. B. Laravel diese Dinge konkret um?

Gerade bei Laravel begegnen dir alle fünf Ebenen permanent.

---

# 1. Programmierparadigmen

Ein **Programmierparadigma** ist zunächst eine grundsätzliche Denkweise darüber, wie ein Programm aufgebaut wird.

PHP ist eine **Multi-Paradigmen-Sprache**. Man kann also mehrere Paradigmen gleichzeitig verwenden.

## 1.1 Prozedurale Programmierung

Das ist die klassische Form:

```php
$user = findUser(42);

if ($user !== null) {
    sendEmail($user);
}
```

Man denkt hauptsächlich in:

> Daten → Funktion → Ergebnis

Zum Beispiel:

```php
function calculateGrossPrice(float $net, float $taxRate): float
{
    return $net * (1 + $taxRate);
}

$gross = calculateGrossPrice(100, 0.19);
```

Die Funktion bekommt Werte und verarbeitet sie.

### Typisch dafür

* Funktionen
* Variablen
* Bedingungen
* Schleifen
* Funktionsaufrufe

Frühes PHP war sehr stark prozedural geprägt.

Auch modernes PHP enthält weiterhin sehr viel prozedurale Programmierung.

Zum Beispiel:

```php
$array = [1, 2, 3, 4];

foreach ($array as $value) {
    echo $value;
}
```

Das ist völlig legitim.

---

# 2. Objektorientierte Programmierung – OOP

Heute ist PHP insbesondere bei Frameworks wie Laravel stark objektorientiert.

Hier denkt man nicht primär in Funktionen, sondern in:

> **Objekte mit Zustand und Verhalten**

Beispiel:

```php
class User
{
    public function __construct(
        public string $name,
        public string $email,
    ) {}

    public function changeEmail(string $email): void
    {
        $this->email = $email;
    }
}
```

Dann:

```php
$user = new User(
    name: 'Gunter',
    email: 'gunter@example.com',
);

$user->changeEmail('neu@example.com');
```

Ein Objekt enthält hier:

* Zustand
* Verhalten

Zustand:

```php
$user->name
$user->email
```

Verhalten:

```php
$user->changeEmail(...)
```

---

# 3. Klasse und Objekt

Diese beiden Begriffe sollte man sauber unterscheiden.

Eine **Klasse** ist eine Beschreibung:

```php
class Car
{
    public string $brand;

    public function drive(): void
    {
        // ...
    }
}
```

Ein **Objekt** ist eine konkrete Instanz dieser Klasse:

```php
$car = new Car();
```

Oder:

```php
$car1 = new Car();
$car2 = new Car();
```

Beide sind Instanzen derselben Klasse, aber unterschiedliche Objekte.

Man kann es ungefähr so betrachten:

```text
Klasse
  ↓
Bauplan

Car
 ├── $brand
 └── drive()

       ↓ new Car()

Objekt
  ↓
konkrete Instanz
```

---

# 4. Encapsulation – Kapselung

Eines der wichtigsten OOP-Prinzipien.

Ein Objekt soll möglichst selbst darüber bestimmen, wie sein interner Zustand verändert wird.

Schlecht:

```php
class BankAccount
{
    public float $balance = 0;
}
```

Dann kann überall passieren:

```php
$account->balance = -1000000;
```

Das Objekt hat keinerlei Kontrolle.

Besser:

```php
class BankAccount
{
    private float $balance = 0;

    public function deposit(float $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException();
        }

        $this->balance += $amount;
    }

    public function balance(): float
    {
        return $this->balance;
    }
}
```

Jetzt kontrolliert das Objekt seine eigenen Regeln.

Das bezeichnet man als **Encapsulation/Kapselung**.

---

# 5. Vererbung – Inheritance

Eine Klasse kann Eigenschaften einer anderen Klasse übernehmen.

```php
class Animal
{
    public function breathe(): void
    {
        //
    }
}

class Dog extends Animal
{
    public function bark(): void
    {
        //
    }
}
```

Ein `Dog` kann damit:

```php
$dog->breathe();
$dog->bark();
```

Das klassische Verhältnis lautet:

> Dog **is an** Animal.

Vererbung wird allerdings heute etwas zurückhaltender eingesetzt als früher.

Oft gilt:

> **Composition over inheritance**

also:

> Zusammensetzen von Objekten ist häufig besser als tiefe Vererbungshierarchien.

Darauf kommen wir gleich.

---

# 6. Polymorphismus

Der sperrige Begriff bedeutet im Wesentlichen:

> Unterschiedliche Objekte können über dieselbe Schnittstelle angesprochen werden.

Zum Beispiel:

```php
interface PaymentProvider
{
    public function pay(float $amount): void;
}
```

Dann:

```php
class PayPalProvider implements PaymentProvider
{
    public function pay(float $amount): void
    {
        // PayPal
    }
}
```

und:

```php
class StripeProvider implements PaymentProvider
{
    public function pay(float $amount): void
    {
        // Stripe
    }
}
```

Nun kann eine andere Klasse einfach sagen:

```php
class Checkout
{
    public function __construct(
        private PaymentProvider $paymentProvider,
    ) {}

    public function pay(float $amount): void
    {
        $this->paymentProvider->pay($amount);
    }
}
```

`Checkout` interessiert nicht, ob dahinter Stripe oder PayPal steckt.

Das ist Polymorphismus.

---

# 7. Interface

Ein Interface definiert einen **Vertrag**.

```php
interface Logger
{
    public function log(string $message): void;
}
```

Eine Implementierung:

```php
class FileLogger implements Logger
{
    public function log(string $message): void
    {
        // Datei schreiben
    }
}
```

Eine andere:

```php
class DatabaseLogger implements Logger
{
    public function log(string $message): void
    {
        // DB schreiben
    }
}
```

Das Interface sagt nur:

> Wer `Logger` implementiert, muss `log()` bereitstellen.

Es sagt nicht, **wie**.

Das ist ein fundamentaler Unterschied.

---

# 8. Abstraktion

Abstraktion bedeutet:

> Details verstecken und nur die relevante Schnittstelle zeigen.

Wenn du schreibst:

```php
Mail::to($user)->send($mail);
```

interessiert dich an dieser Stelle normalerweise nicht:

* wie SMTP funktioniert,
* wie MIME erzeugt wird,
* wie TLS aufgebaut wird,
* wie Header kodiert werden.

Die Details liegen hinter einer Abstraktion.

Laravel besteht zu einem erheblichen Teil aus solchen Abstraktionen.

---

# 9. Composition

Composition bedeutet:

> Ein Objekt benutzt andere Objekte, statt deren Funktionalität durch Vererbung zu übernehmen.

Zum Beispiel:

```php
class OrderService
{
    public function __construct(
        private PaymentProvider $payment,
        private Logger $logger,
    ) {}
}
```

Der `OrderService` **ist kein**

```text
PaymentProvider
Logger
```

sondern er **hat**

```text
PaymentProvider
Logger
```

Das Verhältnis lautet also:

```text
is-a      → Vererbung
has-a     → Composition
```

In modernen PHP-Anwendungen ist Composition extrem wichtig.

---

# 10. Dependency Injection

Damit kommen wir zu einem der wichtigsten Konzepte moderner PHP-Anwendungen.

Stell dir vor:

```php
class OrderService
{
    private StripeProvider $stripe;

    public function __construct()
    {
        $this->stripe = new StripeProvider();
    }
}
```

Problem:

`OrderService` entscheidet selbst, welche Implementierung verwendet wird.

Er ist hart an Stripe gekoppelt.

Besser:

```php
class OrderService
{
    public function __construct(
        private PaymentProvider $paymentProvider,
    ) {}
}
```

Jetzt wird die Abhängigkeit **von außen hineingegeben**.

Das ist:

> **Dependency Injection**

Also:

```text
OrderService
     ↓ benötigt
PaymentProvider

aber

OrderService erzeugt ihn nicht selbst.
```

Laravel macht das permanent über seinen Service Container.

---

# 11. Dependency Inversion

Das ist eng damit verwandt, aber nicht dasselbe.

Statt:

```php
class Checkout
{
    public function __construct(
        private StripeProvider $stripe,
    ) {}
}
```

verwendet man:

```php
class Checkout
{
    public function __construct(
        private PaymentProvider $paymentProvider,
    ) {}
}
```

Die Anwendung hängt damit nicht von einer konkreten Implementierung ab:

```text
StripeProvider
```

sondern von einer Abstraktion:

```text
PaymentProvider
```

Das ist das **Dependency Inversion Principle**, das `D` aus **SOLID**.

---

# 12. Funktionale Programmierung

PHP ist keine rein funktionale Sprache, unterstützt aber viele funktionale Konzepte.

Statt beispielsweise:

```php
$result = [];

foreach ($users as $user) {
    if ($user->active) {
        $result[] = $user->email;
    }
}
```

kann man stärker funktional denken:

```php
$result = array_map(
    fn ($user) => $user->email,
    array_filter(
        $users,
        fn ($user) => $user->active,
    ),
);
```

Oder in Laravel:

```php
$emails = $users
    ->filter(fn ($user) => $user->active)
    ->map(fn ($user) => $user->email);
```

Hier wird beschrieben:

```text
Users
 ↓
filter
 ↓
aktive Users
 ↓
map
 ↓
E-Mail-Adressen
```

Anstatt jeden einzelnen Verarbeitungsschritt imperativ auszuprogrammieren.

---

# 13. First-Class Functions

PHP behandelt Funktionen inzwischen weitgehend wie normale Werte.

Beispielsweise:

```php
$double = function (int $number): int {
    return $number * 2;
};
```

Nun befindet sich eine Funktion in einer Variable:

```php
$double
```

und kann ausgeführt werden:

```php
echo $double(5);
```

Ergebnis:

```text
10
```

Das ist eine wichtige Grundlage für Closures und Callbacks.

---

# 14. Anonymous Function

Eine anonyme Funktion ist eine Funktion **ohne Namen**.

Normal:

```php
function double(int $number): int
{
    return $number * 2;
}
```

Anonym:

```php
function (int $number): int {
    return $number * 2;
}
```

Meist weist man sie einer Variablen zu:

```php
$double = function (int $number): int {
    return $number * 2;
};
```

---

# 15. Closure

Jetzt kommen wir zu deinem Beispiel.

**Closure** wird im PHP-Alltag häufig praktisch synonym mit anonymer Funktion verwendet. Technisch steckt aber ein wichtiges Konzept dahinter.

Eine Closure kann auf einen Kontext außerhalb ihrer eigenen Funktion zugreifen.

Beispiel:

```php
$factor = 10;

$multiply = function (int $number) use ($factor): int {
    return $number * $factor;
};
```

Dann:

```php
$multiply(5);
```

ergibt:

```text
50
```

Entscheidend ist:

```php
use ($factor)
```

Die Funktion **schließt** den äußeren Wert `$factor` ein.

Daher:

> Closure – geschlossener Kontext.

Man kann sich das vereinfacht so vorstellen:

```text
äußerer Scope

$factor = 10
     │
     │ capture
     ▼
┌─────────────────────────┐
│ Closure                 │
│                         │
│ number * $factor        │
│          ↑              │
│          10             │
└─────────────────────────┘
```

---

# 16. Arrow Functions

PHP hat zusätzlich Arrow Functions:

```php
fn ($number) => $number * 2
```

Statt:

```php
function ($number) {
    return $number * 2;
}
```

Ein wichtiger Unterschied:

Arrow Functions übernehmen Variablen aus dem äußeren Scope automatisch.

```php
$factor = 10;

$multiply = fn ($number) => $number * $factor;
```

Kein:

```php
use ($factor)
```

notwendig.

Gerade in Laravel sieht man das ständig:

```php
$users->filter(
    fn ($user) => $user->active
);
```

---

# 17. Callback

Ein Callback ist nicht unbedingt eine Closure.

Ein Callback ist allgemein:

> Eine Funktion, die einer anderen Funktion übergeben wird, damit diese sie später aufruft.

Beispiel:

```php
$numbers = [1, 2, 3];

$result = array_map(
    fn ($number) => $number * 2,
    $numbers,
);
```

Hier ist:

```php
fn ($number) => $number * 2
```

der Callback.

`array_map()` führt ihn aus.

Konzeptionell:

```text
array_map
    │
    ├── 1 → callback → 2
    ├── 2 → callback → 4
    └── 3 → callback → 6
```

---

# 18. Higher-Order Function

Eine Funktion wird als **Higher-Order Function** bezeichnet, wenn sie:

* eine Funktion als Parameter bekommt
* oder eine Funktion zurückgibt.

Beispiel:

```php
function calculate(array $numbers, callable $operation): array
{
    return array_map($operation, $numbers);
}
```

Dann:

```php
$result = calculate(
    [1, 2, 3],
    fn ($number) => $number * 2,
);
```

`calculate()` ist hier eine Higher-Order Function.

Laravel Collections basieren sehr stark auf diesem Prinzip.

---

# 19. Callable

PHP hat dafür einen eigenen Typ:

```php
callable
```

Zum Beispiel:

```php
function execute(callable $callback): void
{
    $callback();
}
```

Aufruf:

```php
execute(function () {
    echo 'Hallo';
});
```

oder:

```php
execute(fn () => print 'Hallo');
```

Ein `callable` kann aber auch eine normale Methode sein.

---

# 20. Model

Jetzt zu deinem zweiten Beispiel.

**Model ist kein Programmierparadigma.**

Ein Model ist normalerweise ein Bestandteil einer Architektur bzw. eines Domain-Modells.

Zum Beispiel MVC:

```text
Model
View
Controller
```

Das Model repräsentiert Daten und häufig auch deren fachliches Verhalten.

Laravel:

```php
class User extends Model
{
    //
}
```

Ein Laravel-Eloquent-Model repräsentiert typischerweise eine Datenbankentität.

Zum Beispiel:

```php
$user = User::find(42);
```

Dann repräsentiert:

```php
$user
```

einen Datensatz aus:

```text
users
```

---

# 21. Active Record

Hier wird es interessant.

Laravel Eloquent benutzt hauptsächlich das **Active Record Pattern**.

Das bedeutet:

Das Objekt repräsentiert nicht nur die Daten:

```php
$user->name
$user->email
```

sondern kann sich auch selbst speichern:

```php
$user->save();
```

Also:

```php
$user = new User();

$user->name = 'Gunter';

$user->save();
```

Das Model enthält damit sowohl:

```text
Daten
+
Persistenz-Verhalten
```

Das ist charakteristisch für Active Record.

---

# 22. Data Mapper

Eine Alternative wäre das **Data Mapper Pattern**.

Dann wäre die Domain-Klasse unabhängig von der Datenbank:

```php
class User
{
    public function __construct(
        public string $name,
    ) {}
}
```

Und ein separater Mapper kümmert sich um die DB:

```php
$userMapper->save($user);
```

Also:

```text
Active Record

User
 ├── Daten
 └── save()


Data Mapper

User
 └── Daten

UserMapper
 └── save(User)
```

Laravel/Eloquent bevorzugt Active Record.

Doctrine beispielsweise orientiert sich stärker am Data-Mapper-Ansatz.

---

# 23. MVC

Das berühmte:

> Model – View – Controller

### Model

Daten und Domain:

```php
User
Order
Invoice
```

### View

Darstellung:

```blade
<h1>{{ $user->name }}</h1>
```

### Controller

koordiniert einen Request:

```php
class UserController
{
    public function show(User $user)
    {
        return view('users.show', [
            'user' => $user,
        ]);
    }
}
```

Vereinfacht:

```text
Browser
   ↓
Controller
   ↓
Model
   ↓
Controller
   ↓
View
   ↓
Browser
```

Laravel geht inzwischen natürlich weit über simples MVC hinaus.

---

# 24. Repository Pattern

Das Repository kapselt Datenzugriffe.

Ohne Repository:

```php
$user = User::query()
    ->where('email', $email)
    ->first();
```

Mit Repository:

```php
$user = $userRepository->findByEmail($email);
```

Der Aufrufer muss nicht wissen, ob die Daten aus:

* MySQL
* PostgreSQL
* API
* Cache
* Elasticsearch

kommen.

Interface:

```php
interface UserRepository
{
    public function findByEmail(string $email): ?User;
}
```

Implementierung:

```php
class EloquentUserRepository implements UserRepository
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}
```

In Laravel sollte man dieses Pattern allerdings nicht reflexartig für jedes Model verwenden. Eloquent selbst abstrahiert bereits sehr viel.

---

# 25. Service Pattern

Ein Service bündelt Geschäftslogik.

Beispielsweise:

```php
class RegisterUser
{
    public function execute(array $data): User
    {
        $user = User::create($data);

        // weitere Geschäftslogik

        return $user;
    }
}
```

Dann beispielsweise im Controller:

```php
$user = $registerUser->execute($request->validated());
```

Dadurch muss der Controller nicht die gesamte Fachlogik enthalten.

---

# 26. Single Responsibility Principle

Das ist das `S` aus SOLID.

Eine Klasse sollte eine klar definierte Verantwortung besitzen.

Problematisch:

```php
class UserController
{
    public function store()
    {
        // validieren
        // User anlegen
        // Rechnung erzeugen
        // Mail versenden
        // PDF erstellen
        // Activity log schreiben
        // Statistik aktualisieren
    }
}
```

Solche Methoden wachsen schnell auf hunderte Zeilen.

Besser könnte die Verantwortung verteilt werden:

```text
UserController
      │
      ▼
RegisterUser
      │
      ├── UserRepository
      ├── EventDispatcher
      └── ...
```

---

# 27. Command / Action Pattern

Gerade bei Laravel begegnet man häufig sogenannten Actions:

```php
class CreateUser
{
    public function __invoke(array $data): User
    {
        return User::create($data);
    }
}
```

Dann:

```php
$user = $createUser($data);
```

Oder:

```php
$user = app(CreateUser::class)($data);
```

Die Klasse repräsentiert eine einzelne Operation:

> Create User.

Das kann sehr angenehm sein, weil eine fachliche Aktion genau einen Ort bekommt.

---

# 28. `__invoke()`

Das ist ein interessantes PHP-Sprachfeature.

Eine Klasse:

```php
class CalculateTax
{
    public function __invoke(float $amount): float
    {
        return $amount * 0.19;
    }
}
```

Dann:

```php
$calculator = new CalculateTax();

$tax = $calculator(100);
```

Das Objekt verhält sich also wie eine Funktion.

Solche Objekte nennt man häufig:

> Invokable Objects.

Laravel benutzt dieses Konzept ebenfalls.

---

# 29. Factory Pattern

Eine Factory erzeugt Objekte.

Statt überall:

```php
new SomeComplicatedObject(...)
```

zu schreiben, wird die Erzeugung zentralisiert.

```php
class PaymentProviderFactory
{
    public function create(string $type): PaymentProvider
    {
        return match ($type) {
            'stripe' => new StripeProvider(),
            'paypal' => new PayPalProvider(),
            default => throw new InvalidArgumentException(),
        };
    }
}
```

Aufruf:

```php
$provider = $factory->create('stripe');
```

Laravel Model Factories sind ein verwandtes Konzept, insbesondere für Tests:

```php
User::factory()->create();
```

---

# 30. Strategy Pattern

Beim Strategy Pattern werden unterschiedliche Algorithmen austauschbar gemacht.

Zum Beispiel:

```php
interface DiscountStrategy
{
    public function calculate(float $price): float;
}
```

Dann:

```php
class RegularDiscount implements DiscountStrategy
{
    public function calculate(float $price): float
    {
        return $price;
    }
}
```

und:

```php
class PremiumDiscount implements DiscountStrategy
{
    public function calculate(float $price): float
    {
        return $price * 0.9;
    }
}
```

Die Geschäftslogik arbeitet nur gegen:

```php
DiscountStrategy
```

und kennt die konkrete Strategie nicht.

---

# 31. Observer / Event Pattern

Laravel verwendet Events sehr intensiv.

Zum Beispiel:

```php
UserRegistered::dispatch($user);
```

Dann können mehrere Listener reagieren:

```text
UserRegistered
      │
      ├── SendWelcomeEmail
      ├── CreateDefaultSettings
      ├── WriteAuditLog
      └── NotifyAdmin
```

Das erzeugende System muss diese Komponenten nicht direkt kennen.

Das ist eine Form von **Loose Coupling**, also loser Kopplung.

---

# 32. Middleware Pattern

Laravel Middleware sitzt zwischen Request und Anwendung.

```text
HTTP Request
     ↓
Middleware
     ↓
Middleware
     ↓
Controller
     ↓
HTTP Response
```

Zum Beispiel:

```php
public function handle($request, Closure $next)
{
    if (! auth()->check()) {
        abort(403);
    }

    return $next($request);
}
```

Hier siehst du übrigens wieder eine **Closure**:

```php
Closure $next
```

`$next` repräsentiert den nächsten Schritt der Pipeline.

---

# 33. Pipeline Pattern

Laravel benutzt häufig Pipelines.

Konzeptionell:

```text
Input
 ↓
Step A
 ↓
Step B
 ↓
Step C
 ↓
Output
```

Beispielsweise könnte man Daten durch mehrere Transformationen schicken:

```text
Request
 ↓
Authenticate
 ↓
Authorize
 ↓
Validate
 ↓
Controller
```

Middleware ist ein gutes reales Beispiel dafür.

---

# 34. Fluent Interface

Laravel verwendet extrem häufig **Fluent Interfaces**.

Beispiel:

```php
User::query()
    ->where('active', true)
    ->where('age', '>=', 18)
    ->orderBy('name')
    ->get();
```

Eine Methode gibt dabei wieder ein Objekt zurück, auf dem die nächste Methode aufgerufen werden kann.

Daher:

```text
query()
  ↓
where()
  ↓
where()
  ↓
orderBy()
  ↓
get()
```

Man nennt das auch:

> Method Chaining.

---

# 35. Builder Pattern

Der Query Builder ist zugleich ein sehr anschauliches Beispiel für das **Builder Pattern**.

Du konstruierst Schritt für Schritt eine komplexere Sache:

```php
$query = User::query();

$query->where('active', true);

$query->where('role', 'admin');

$users = $query->get();
```

Die SQL-Abfrage wird dabei nach und nach aufgebaut.

---

# 36. Declarative vs. Imperative Programming

Das ist eine sehr hilfreiche Unterscheidung.

## Imperativ

Du sagst dem Computer detailliert, **wie** etwas gemacht werden soll.

```php
$result = [];

foreach ($users as $user) {
    if ($user->active) {
        $result[] = $user;
    }
}
```

## Deklarativ

Du beschreibst eher, **was** du möchtest:

```php
$users
    ->where('active', true)
    ->get();
```

Oder:

```php
$users->filter(
    fn ($user) => $user->active
);
```

Laravel ist an vielen Stellen sehr deklarativ.

---

# 37. Immutable vs. Mutable

Ein weiteres grundlegendes Konzept.

## Mutable

Ein Objekt verändert seinen eigenen Zustand:

```php
$date->addDay();
```

## Immutable

Die ursprüngliche Instanz bleibt unverändert:

```php
$newDate = $date->addDay();
```

wobei intern ein neues Objekt erzeugt wird.

Beispielsweise gibt es bei Carbon:

```php
Carbon
```

und:

```php
CarbonImmutable
```

Immutability ist insbesondere bei komplexeren Systemen interessant, weil Zustandsänderungen besser nachvollziehbar werden.

---

# 38. Value Object

Ein Value Object repräsentiert einen fachlichen Wert.

Statt:

```php
function pay(float $amount, string $currency)
```

könnte man schreiben:

```php
final class Money
{
    public function __construct(
        public readonly int $amount,
        public readonly string $currency,
    ) {}
}
```

Dann:

```php
$price = new Money(
    amount: 1999,
    currency: 'EUR',
);
```

Hier gehören:

```text
1999
EUR
```

fachlich zusammen.

Typische Value Objects:

```text
Money
EmailAddress
PostalAddress
DateRange
Coordinates
Percentage
```

---

# 39. Entity

Eine Entity wird dagegen hauptsächlich durch ihre **Identität** definiert.

Beispiel:

```text
User #42
```

Der User kann seinen Namen ändern:

```text
Gunter
→
anderer Name
```

aber bleibt weiterhin:

```text
User ID 42
```

Das ist ein wichtiger Begriff aus Domain-Driven Design.

---

# 40. DTO – Data Transfer Object

Ein DTO transportiert strukturierte Daten.

Zum Beispiel:

```php
final readonly class CreateUserData
{
    public function __construct(
        public string $name,
        public string $email,
    ) {}
}
```

Dann:

```php
$data = new CreateUserData(
    name: 'Gunter',
    email: 'gunter@example.com',
);
```

Statt überall unstrukturierte Arrays herumzureichen:

```php
[
    'name' => 'Gunter',
    'email' => 'gunter@example.com',
]
```

Vorteil:

Der Typ ist eindeutig definiert.

---

# 41. Enum

PHP unterstützt inzwischen native Enums.

```php
enum UserStatus: string
{
    case Active = 'active';
    case Disabled = 'disabled';
}
```

Dann:

```php
$status = UserStatus::Active;
```

Das ist wesentlich sicherer als:

```php
$status = 'actve';
```

mit einem Tippfehler.

Enums modellieren eine begrenzte Menge erlaubter Zustände.

---

# 42. Trait

Traits sind ein PHP-spezifisches Mittel zur Wiederverwendung von Verhalten.

```php
trait HasUuid
{
    public function generateUuid(): string
    {
        // ...
    }
}
```

Dann:

```php
class User
{
    use HasUuid;
}
```

Traits sind weder Vererbung noch Interfaces.

Ein Trait fügt konkreten Code in eine Klasse ein.

Laravel benutzt Traits sehr intensiv:

```php
use HasFactory;
use Notifiable;
use SoftDeletes;
```

---

# 43. Facade

Laravel Facades sehen beispielsweise so aus:

```php
Cache::get('key');

Log::info('Test');

Mail::to(...);
```

Das wirkt wie ein statischer Methodenaufruf.

Laravel Facades sind aber im Wesentlichen Proxies zum Service Container.

Vereinfacht:

```text
Cache::get()
    │
    ▼
Facade
    │
    ▼
Service Container
    │
    ▼
Cache Manager
```

Das unterscheidet Laravel-Facades von einfachen statischen Utility-Klassen.

---

# 44. Service Container / IoC Container

Einer der zentralsten Bestandteile von Laravel.

Angenommen:

```php
class ReportController
{
    public function __construct(
        private ReportService $reportService,
    ) {}
}
```

Du musst nicht manuell schreiben:

```php
$controller = new ReportController(
    new ReportService(...)
);
```

Laravel analysiert den Constructor und erstellt die Abhängigkeiten.

Konzeptionell:

```text
Laravel Container

ReportController
      ↓ braucht
ReportService
      ↓ braucht
Repository
      ↓ braucht
DatabaseConnection
```

Der Container baut diesen Objektgraphen.

Das bezeichnet man als:

> **Inversion of Control – IoC**

---

# 45. Inversion of Control

Normalerweise sagt dein Code:

```php
$service = new Service();
$service->execute();
```

Bei IoC dreht sich die Kontrolle teilweise um:

```text
Framework
    ↓
erzeugt deine Klasse
    ↓
liefert Dependencies
    ↓
ruft deinen Code auf
```

Das ist ein wesentliches Grundprinzip von Frameworks wie Laravel.

Man könnte etwas überspitzt sagen:

Bei klassischem PHP:

> Du rufst das Framework auf.

Bei IoC:

> Das Framework ruft deinen Code auf.

---

# 46. Convention over Configuration

Laravel verfolgt an vielen Stellen dieses Prinzip.

Beispiel:

```php
class User extends Model
{
}
```

Laravel nimmt automatisch an:

```text
Model: User
Tabelle: users
Primary Key: id
```

Du musst also nicht jedes Detail konfigurieren.

Das ist:

> Convention over Configuration.

Wenn du die Konvention einhältst, brauchst du weniger Konfiguration.

---

# 47. Separation of Concerns

Ein extrem wichtiges Architekturprinzip:

> Unterschiedliche Verantwortlichkeiten sollten getrennt werden.

Zum Beispiel:

```text
Controller
    Request/Response

Model
    Daten

Service
    Geschäftslogik

View
    Darstellung

Repository
    Datenzugriff

Policy
    Autorisierung
```

Wenn alles im Controller landet, bekommst du irgendwann:

```text
Fat Controller
```

Wenn alles im Model landet:

```text
Fat Model
```

Beides kann problematisch werden.

---

# 48. SOLID

Die fünf bekannten OOP-Prinzipien:

```text
S  Single Responsibility Principle
O  Open/Closed Principle
L  Liskov Substitution Principle
I  Interface Segregation Principle
D  Dependency Inversion Principle
```

Sehr verkürzt:

### S

Eine Klasse → klar definierte Verantwortung.

### O

Erweiterbar, ohne bestehenden Code ständig verändern zu müssen.

### L

Eine Unterklasse muss sinnvoll als Ersatz für ihre Basisklasse funktionieren.

### I

Lieber mehrere kleine Interfaces als ein riesiges Interface.

### D

Von Abstraktionen abhängen, nicht von konkreten Implementierungen.

---

# 49. Domain-Driven Design

DDD ist noch einmal eine höhere Ebene.

Dabei versucht man, die Softwarearchitektur an der fachlichen Domäne auszurichten.

Beispielsweise statt:

```text
Controllers/
Models/
Services/
Repositories/
```

könnte man stärker fachlich strukturieren:

```text
Orders/
    Order.php
    CreateOrder.php
    OrderRepository.php
    OrderStatus.php

Customers/
    Customer.php
    Address.php

Billing/
    Invoice.php
    Money.php
```

Dann spiegelt die Software stärker das fachliche Problem wider.

DDD ist allerdings kein einzelnes Pattern, sondern eine ganze Sammlung von Konzepten.

---

# 50. Wie diese Begriffe zusammenhängen

Das ist wahrscheinlich die wichtigste Übersicht.

Nehmen wir eine typische Laravel-Anwendung:

```text
PHP
│
├── Programmierparadigmen
│   ├── prozedural
│   ├── objektorientiert
│   ├── funktional
│   └── deklarativ
│
├── Sprachkonzepte
│   ├── Klasse
│   ├── Objekt
│   ├── Interface
│   ├── Trait
│   ├── Enum
│   ├── Closure
│   ├── Arrow Function
│   ├── Callable
│   └── readonly
│
├── OOP-/Designprinzipien
│   ├── Encapsulation
│   ├── Abstraction
│   ├── Polymorphism
│   ├── Composition
│   ├── Dependency Injection
│   └── SOLID
│
├── Design Patterns
│   ├── Factory
│   ├── Strategy
│   ├── Observer
│   ├── Repository
│   ├── Builder
│   ├── Pipeline
│   └── Active Record
│
├── Architektur
│   ├── MVC
│   ├── Services
│   ├── Domain Model
│   └── DDD
│
└── Laravel
    ├── Eloquent Model
    ├── Service Container
    ├── Middleware
    ├── Events/Listener
    ├── Jobs
    ├── Policies
    ├── Facades
    └── Collections
```

Das zeigt auch, warum zum Beispiel

```text
Closure
```

und

```text
Model
```

nicht derselben Begriffskategorie angehören.

---

# 51. Ein konkretes Laravel-Beispiel mit mehreren Konzepten

Nehmen wir:

```php
class ActivityLogService
{
    public function __construct(
        private ActivityRepository $repository,
    ) {}

    public function latestForUser(User $user): Collection
    {
        return $this->repository
            ->forUser($user)
            ->filter(fn (Activity $activity) => $activity->isVisible())
            ->sortByDesc(fn (Activity $activity) => $activity->created_at);
    }
}
```

Da stecken bereits eine Menge Konzepte drin.

### `class ActivityLogService`

Objektorientierung.

### Constructor

```php
public function __construct(...)
```

Objektinitialisierung.

### `ActivityRepository`

```php
private ActivityRepository $repository
```

Abstraktion bzw. Repository-Konzept.

### Constructor Injection

```php
public function __construct(
    private ActivityRepository $repository,
)
```

Dependency Injection.

### `User $user`

```php
latestForUser(User $user)
```

Type Hinting und wahrscheinlich Eloquent Model.

### `Collection`

```php
: Collection
```

Return Type.

### `filter()`

```php
->filter(...)
```

Higher-Order Function / Collection API.

### Closure beziehungsweise Arrow Function

```php
fn (Activity $activity) => $activity->isVisible()
```

funktionales Programmierkonzept.

### Fluent Interface

```php
$this->repository
    ->forUser(...)
    ->filter(...)
    ->sortByDesc(...)
```

Method Chaining / Fluent Interface.

### Model

```php
User
Activity
```

wahrscheinlich Eloquent Models bzw. Domain Entities.

Das ist der entscheidende Punkt:

**Moderner PHP-Code verwendet diese Konzepte nicht getrennt, sondern kombiniert sie.**

---

# 52. Was du bei Laravel tatsächlich permanent benutzt

Wenn ich es auf die Konzepte reduziere, die bei typischer Laravel-/Livewire-Entwicklung am wichtigsten sind, würde ich ungefähr diese Reihenfolge empfehlen:

```text
1. Klasse / Objekt
2. Properties / Methoden
3. Encapsulation
4. Interface
5. Trait
6. Closure / Arrow Function
7. Callable / Callback
8. Collections / Higher-Order Functions
9. Dependency Injection
10. Service Container
11. Model / Active Record
12. Relationships
13. MVC
14. Composition
15. Events / Listener
16. Middleware
17. Services / Actions
18. DTO / Value Object
19. SOLID
20. Domain-Modell / DDD
```

Gerade **Closure**, **Dependency Injection**, **Service Container**, **Eloquent Model**, **Collection**, **Callback** und **Composition** sind Begriffe, die man wirklich verstanden haben sollte, weil man sie in Laravel praktisch jeden Tag verwendet.

Und eine vielleicht wichtige Erkenntnis dabei ist:

> Ein Laravel-Projekt ist nicht einfach „objektorientiert“.

Es kombiniert **OOP**, funktionale Elemente, deklarative APIs, Active Record, Dependency Injection, IoC, Event-basierte Architektur, Pipelines und diverse Design Patterns miteinander.

Das ist auch der Grund, warum Laravel-Code auf den ersten Blick manchmal sehr „magisch“ aussieht. Wenn man die zugrunde liegenden Konzepte einzeln versteht, verschwindet ein großer Teil dieser Magie.

Ich kann dir dazu direkt eine **visuelle PHP/Laravel-Landkarte** erzeugen, die Paradigmen → Sprachkonzepte → Patterns → Laravel-Beispiele miteinander verbindet – lieber als **technisches Diagramm**, **Mindmap** oder **Schichtenmodell**?
