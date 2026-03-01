# Importmap w Symfony – przewodnik

## 1. Czym jest Importmap?

**Importmap** to sposób zarządzania zależnościami JavaScript bez użycia klasycznego bundlera (Webpack, Vite, etc.). 
Zamiast budować jeden duży plik JS, przeglądarka ładuje moduły ES (ESM) bezpośrednio z serwera, 
a **importmap.php** decyduje, pod jakim adresem dostępny jest każdy moduł.

W praktyce:

- piszemy moduły JS z `import ... from 'nazwa-pakietu'`,
- `importmap.php` trzyma informację, skąd je wczytać (np. z CDN.),
- Symfony generuje `<script type="importmap">` oraz `<script type="module">` w szablonach Twig,
- nie potrzebujemy osobnego procesu budowania frontendu.

Daje to:

- prostszą konfigurację,
- mniejszy narzut narzędzi (brak bundlera),
- lepszą czytelność kodu (czyste moduły ESM).

---

## 1.1 Opis Importmap z developer.mozilla.org

### Description

When importing a JavaScript module, both the import statement and import() operator have a "module specifier" that 
indicates the module to be imported. A browser must be able to resolve this specifier to an absolute URL
to import the module.

For example, the following statements import elements from the module specifier "https://example.com/shapes/circle.js",
which is an absolute URL, and the module specifier "./modules/shapes/square.js", 
which is a path relative to the base URL of the document.

```js
import { name as circleName } from "https://example.com/shapes/circle.js";
import { name as squareName, draw } from "./modules/shapes/square.js";
```

Import maps allow developers to specify (almost) any text they want in the module specifier; 
the map provides a corresponding value that will replace the text when the module specifier is resolved.

### Bare modules

The import map below defines an `imports` key that has a "module specifier map" with properties `circle` and `square`.

```html
<script type="importmap">
  {
    "imports": {
      "circle": "https://example.com/shapes/circle.js",
      "square": "./modules/shapes/square.js"
    }
  }
</script>
```

With this import map we can import the same modules as above, but using "bare modules" in our module specifiers:

```js
import { name as circleName } from "circle";
import { name as squareName, draw } from "square";
```

## 2. Instalacja Importmap w Symfony

```bash
composer require symfony/importmap-php
```

Po instalacji:

- tworzony jest plik `importmap.php` w katalogu głównym projektu zawiera mapę zależności,
- w katalogu `assets/` mamy m.in. plik główny (np. `app.js`),
- plik `app.js` odpowiada entrypointowi `app`, możemy w nim importować inne pliki js oraz css
- w szablonie Twig możemy załadować entrypointy za pomocą funkcji `importmap()`:

```js
// assets/app.js

// Bootstrap CSS imported by importmap
import 'bootstrap/dist/css/bootstrap.min.css';

// Bootstrap JS (should include popperjs as well) - alias is configured in importmap.php
import 'bootstrap';

// Font Awesome icons
import '@fortawesome/fontawesome-free';
import '@fortawesome/fontawesome-free/css/fontawesome.min.css';
import '@fortawesome/fontawesome-free/css/all.min.css';

// Bootstrap Icons
import 'bootstrap-icons/font/bootstrap-icons.css';

// CSS scripts
import './styles/app.css';
import './styles/utils.css';

// JS scripts
import './js/app/BlockButton.js';
import './js/app/Dropdown.js';
```

```twig
{# templates/base.html.twig #}
 
{% block importmap %}
    {{ importmap('app') }}
{% endblock %}
```
---

## 3. Dodawanie bibliotek przez Importmap

### 3.1. Dodanie pakietu z CDN. (np. Axios)

Dołącz bibliotekę:

```bash
php bin/console importmap:require axios
```

To polecenie zaktualizuje plik `importmap.php`, w folderze `assets/vendor` zostanie utworzony folder `axios` 
z którego pliki możemy importować np.: w `assets/app.js`.

### 3.2. Dodanie pakietu z określonej wersji / źródła

Możesz wskazać konkretny URL (np. jsDelivr, Skypack, UNPKG):

```bash
php bin/console importmap:require axios@1.6.8
```

### 3.3 Sprawdzenie, które pakiety są przestarzałe

```bash
php bin/console importmap:outdated
```

### 3.4 Update przestarzałych pakietów

```bash
php bin/console importmap:update
```

### 3.5 Usuwanie bibliotek z Importmap

```bash
php bin/console importmap:remove axios
```
---

## 4. Dodanie nowej mapy

Potrzeba dodania nowej mapy poza mapą główną `app` może pojawić się, wtedy gdy mamy pliki CSS i JS o wąskiej specjalizacji,
np. tylko dla strony logowania i rejestracji i nie chcemy ich importować dla każdej podstrony w aplikacji.

## 4.1 Tworzymy plik wejścia (entrypoint), który odpowiada nazwie mapy

```bash
cd assets/
touch auth.js
```

```js
// assets/auth.js

// CSS scripts
import './styles/auth.css';

// JS scripts
import './js/app/TogglePassword.js';
```

## 4.2 Rejestrujemy nową mapę w pliku importmap.php

```php
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    'auth' => [
        'path' => './assets/auth.js',
        'entrypoint' => true,
    ],
    // ... reszta kodu
];
```
