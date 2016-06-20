## PESEL
[![Build Status](https://travis-ci.org/KKSzymanowski/PESEL.svg?branch=master)](https://travis-ci.org/KKSzymanowski/PESEL)
[![Coverage Status](https://coveralls.io/repos/github/KKSzymanowski/PESEL/badge.svg?branch=master)](https://coveralls.io/github/KKSzymanowski/PESEL?branch=master)
[![StyleCI](https://styleci.io/repos/61304979/shield)](https://styleci.io/repos/61304979)
[![Latest Stable Version](https://poser.pugx.org/kkszymanowski/pesel/v/stable)](https://packagist.org/packages/kkszymanowski/pesel)
[![License](https://poser.pugx.org/kkszymanowski/pesel/license)](https://packagist.org/packages/kkszymanowski/pesel)


Paczka do łatwej walidacji numeru **PESEL**.

### Instalacja
[Composer](https://getcomposer.org/):
```
composer require kkszymanowski/pesel
```

### Użycie
##### Tworzenie obiektu
```php
$pesel = new Pesel($number);
```
lub
```php
$pesel = Pesel::create($number);
```
**Powyższe metody są równoważne.**

Podczas tworzenia obiektu sprawdzana jest poprawność numeru PESEL.
- Powinien mieć 11 znaków
- Powinien zawierać wyłącznie cyfry
- Suma kontrolna powinna być poprawna.

Jeżeli przynajmniej jeden z tych warunków nie zostanie spełniony, zostanie rzucony wyjątek typu `InvalidArgumentException` z domyślną treścią. Treść wyjątku można nadpisać następująco:
```php
Pesel::create($number, [
    'invalidLength'     => 'Tekst 1',
    'invalidCharacters' => 'Tekst 2',
    'invalidChecksum'   => 'Tekst 3'
]);
```
Pozwala to na elastyczne przekazanie treści do użytkownika:
```php
try {
    Pesel::create($number, [
       'invalidLength'     => 'Tekst 1',
       'invalidCharacters' => 'Tekst 2',
       'invalidChecksum'   => 'Tekst 3'
    ]);

    echo("Numer PESEL jest poprawny");
} catch(InvalidArgumentException $e) {
    echo($e->getMessage());
}
```
Brak któregokolwiek z wymienionych pól skutkuje rzuceniem wyjątku z domyślną treścią:
```php
'invalidLength'     => 'Nieprawidłowa długość numeru PESEL.',
'invalidCharacters' => 'Numer PESEL może zawierać tylko cyfry.',
'invalidChecksum'   => 'Numer PESEL posiada niepoprawną sumę kontrolną.'
```
##### Pobieranie zawartości numeru PESEL:
```php
$pesel = new Pesel($number);

$pesel->getNumber();    // Zwraca string

$pesel->getBirthDate(); // Zwraca DateTime

$pesel->getGender();    // Zwraca Pesel::GENDER_MALE lub Pesel::GENDER_FEMALE
```
##### Sprawdzenie poprawności:
```php
Pesel::isValid($pesel); // Zwraca bool, nie rzuca wyjątku
```

##### Sprawdzenie daty urodzenia:

Parametr `$birthDate` jest instancją wbudowanej w PHP klasy DateTime
```php
PeselValidator::hasBirthDate(Pesel::create($pesel), $birthDate);
```

##### Sprawdzenie płci
```php
PeselValidator::hasGender(Pesel::create($pesel), Pesel::GENDER_MALE);

PeselValidator::hasGender(Pesel::create($pesel), Pesel::GENDER_FEMALE);
```