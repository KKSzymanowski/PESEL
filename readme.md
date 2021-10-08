## PESEL
[![Build Status](https://app.travis-ci.com/KKSzymanowski/PESEL.svg?branch=master)](https://app.travis-ci.com/KKSzymanowski/PESEL)
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

### Upgrade guide
#### 2.* -> 3.*
Wersja 3 jest wstecznie kompatybilna z wersją 2, ale wymaga PHP >=7.3.
Poza podniesieniem wersji PHP żadne zmiany nie są konieczne.

Zalecana jest natomiast zmiana łapanego wyjątku `InvalidArgumentException` na wyjątki odpowiadające konkretnym błędom walidacji:
- `Pesel\Exceptions\InvalidLengthException` - Błędna długość
- `Pesel\Exceptions\InvalidCharactersException` - Znaki inne niż cyfry
- `Pesel\Exceptions\InvalidChecksumException` - Błędna suma kontrolna

lub po prostu na `Pesel\Exceptions\PeselValidationException` aby złapać wszystkie błędy walidacji.

#### 3.* -> 4.*
W wersji 4 zmieniona została walidacja numeru PESEL, aby dobrze identyfikować numery takie jak `00000000000` i `44444444444` jako niepoprawne.
Wersja 4 jest zatem niekompatybilna z wersją 3 w tych przypadkach brzegowych, ale nie powinno być problemów z kompatybilnością w większości przypadków.

Od wersji 4 w przypadku nieprawidłowej daty urodzenia w numerze PESEL rzucany jest nowy wyjątek - `Pesel\Exceptions\InvalidBirthDateException`.

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
- Powinien mieć 11 znaków.
- Powinien zawierać wyłącznie cyfry.
- Suma kontrolna powinna być poprawna.
- Powinien zawierać prawidłową datę urodzenia:
    - rok >= 1800 i < 2300.
    - miesiąc >= 1 i <= 12.
    - dzień >= 1 i nie większy niż liczba dni w danym miesiącu i danym roku.
    
Jeżeli przynajmniej jeden z tych warunków nie zostanie spełniony, zostanie rzucony odpowiedni wyjątek:
- `Pesel\Exceptions\InvalidLengthException` - Błędna długość
- `Pesel\Exceptions\InvalidCharactersException` - Znaki inne niż cyfry
- `Pesel\Exceptions\InvalidChecksumException` - Błędna suma kontrolna
- `Pesel\Exceptions\InvalidChecksumException` - Błędna data urodzenia

```php
try {
    Pesel::create($number);

    echo('Numer PESEL jest poprawny');
} catch(Pesel\Exceptions\InvalidLengthException $e) {
    echo('Numer PESEL ma nieprawidłową długość');
} catch(Pesel\Exceptions\InvalidCharactersException $e) {
    echo('Numer PESEL zawiera nieprawidłowe znaki');
} catch(Pesel\Exceptions\InvalidChecksumException $e) {
    echo('Numer PESEL zawiera błędną sumę kontrolną');
} catch(Pesel\Exceptions\InvalidBirthDateException $e) {
    echo('Numer PESEL zawiera nieprawdiłową datę urodzenia');
}
```

Wszystkie powyższe wyjątki dziedziczą z `Pesel\Exceptions\PeselValidationException` więc jeżeli niepotrzebne jest 
rozróżnienie na konkretne błędy walidacji można zastosować jedną klauzulę `catch`:
```php
try {
    Pesel::create($number);

    echo('Numer PESEL jest poprawny');
} catch(\Pesel\Exceptions\PeselValidationException $e) {
    echo('Numer PESEL jest błędny');
}
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
