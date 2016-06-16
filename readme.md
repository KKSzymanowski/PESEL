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
Powyższe metody są równoważne.

##### Sprawdzenie poprawności:
```php
Pesel::create($pesel)->isValid();
```

##### Sprawdzenie daty urodzenia:

Parametr `$birthDate` jest instancją klasy [Carbon](https://github.com/briannesbitt/Carbon)
```php
Pesel::create($pesel)->hasBirthDate($birthDate);
```

##### Sprawdzenie płci
```php
Pesel::create($pesel)->hasGender(Pesel::GENDER_MALE);

Pesel::create($pesel)->hasGender(Pesel::GENDER_FEMALE);
```
Akceptowane formaty płci:
- Męska: `Pesel::GENDER_MALE`, `1`, `'M'`, `'m'`
- Żeńska: `Pesel::GENDER_FEMALE`, `0`, `'F'`, `'f'`, `'K'`, `'k'`, `'W'`, `'w'`
