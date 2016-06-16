## PESEL
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
