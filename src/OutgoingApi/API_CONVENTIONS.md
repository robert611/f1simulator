# Założenia przyjęte podczas budowy API

## 1. Reprezentacja dat typu DateTime

Wszystkie daty zwracane przez REST API są reprezentowane jako ciągi znaków (*string*) zgodne ze standardem **RFC 3339**, w strefie czasowej UTC.

Przyjęty format daty:

`YYYY-MM-DDTHH:mm:ssZ`

Przykład:

```json
{
    "createdAt": "2026-04-12T11:31:53Z",
    "updatedAt": "2026-04-12T11:31:53Z"
}
```

## 2. Negocjacja formatu odpowiedzi (Content Negotiation)

Format odpowiedzi jest wybierany na podstawie nagłówka **`Accept`** (RFC 9110). Obecnie obsługiwany jest wyłącznie format JSON (`application/json`).

- Brak nagłówka `Accept`, `*/*` lub `application/*` – zwracany jest JSON (format domyślny).
- Uwzględniane są wagi `q`, np. `Accept: text/html, application/json;q=0.5` zwróci JSON.
- Jeżeli żaden z obsługiwanych formatów nie jest akceptowany przez klienta, API zwraca **406 Not Acceptable** (`application/problem+json`):

```json
{
    "title": "Not Acceptable",
    "status": 406,
    "detail": "Supported media types: application/json."
}
```

- Każda odpowiedź API zawiera nagłówek `Vary: Accept`.
