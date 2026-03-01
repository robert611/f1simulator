# f1simulator

Ten plik opisuje **jak postawić aplikację lokalnie po sklonowaniu repozytorium**.

---

## 1. Wymagania wstępne

Na swojej maszynie potrzebujesz:

- **Docker** (zalecana najnowsza stabilna wersja)
- **Docker Compose** (wbudowany w nowsze wersje Dockera jako `docker compose`)
- (opcjonalnie) **Make** – jeśli chcesz używać gotowych komend z `Makefile`

---

### 2. Uruchom kontenery Dockera (z budowaniem obrazów)

```bash
docker compose up -d --build
```

### 3. Zainstaluj zależności PHP (Composer)

```bash
docker compose exec php composer install --no-interaction --prefer-dist
```

### 4. Wykonaj migracje bazy danych

```bash
docker compose exec php php bin/console doctrine:migrations:migrate
docker compose exec php php bin/console doctrine:migrations:migrate --env="test"
```

### 5. Załaduj fixtures do bazy deweloperskiej

```bash
docker compose exec php php bin/console doctrine:fixtures:load -n
```

### 6. Zainstaluj Importmap (assets / JavaScript)

```bash
docker compose exec php php bin/console importmap:install
```

### 7. Sprawdź działanie aplikacji w przeglądarc pod adresem:

```text
http://localhost:8000
```

### 8. Wykonaj testy

```bash
docker compose exec php composer code-check
```
