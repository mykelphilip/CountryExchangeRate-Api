
🧾 README.md 

````markdown
# Country Exchange Rate API

A Laravel 12 RESTful API that fetches countries and exchange rates from external APIs, stores them in a MySQL database, and provides endpoints to view, refresh, and manage the data.

---

🌍 APIs Used

- Countries API: [https://restcountries.com/v2/all?fields=name,capital,region,population,flag,currencies](https://restcountries.com/v2/all?fields=name,capital,region,population,flag,currencies)  
- Exchange Rate API: [https://open.er-api.com/v6/latest/USD](https://open.er-api.com/v6/latest/USD)

---

⚙️ Installation

```bash
# Clone the repository
git clone https://github.com/mykelphilip/CountryExchangeRate-Api.git
cd CountryExchangeRate-Api

# Install dependencies
composer install

# Copy environment file and configure database
cp .env.example .env

# Generate app key
php artisan key:generate

# Update .env file with your MySQL credentials
DB_DATABASE=country_api
DB_USERNAME=root
DB_PASSWORD=

# Run database migrations
php artisan migrate

# Start the development server
php artisan serve
````

---

🔁 Refreshing Data

The API fetches data from both external APIs and stores it locally.
Run this endpoint to refresh and cache all countries:

```
POST /api/countries/refresh
```

---

📡 API Endpoints

| Method   | Endpoint                 | Description                                   |
| -------- | ------------------------ | --------------------------------------------- |
| `POST`   | `/api/countries/refresh` | Fetch & cache all countries and rates         |
| `GET`    | `/api/countries`         | List countries (filter & sort supported)      |
| `GET`    | `/api/countries/{name}`  | Get a single country by name                  |
| `DELETE` | `/api/countries/{name}`  | Delete a country record                       |
| `GET`    | `/api/status`            | Show total countries & last refresh timestamp |
| `GET`    | `/api/countries/image`   | Get generated summary image                   |

---

🔍 Filters & Sorting

Example queries:

```
GET /api/countries?region=Africa
GET /api/countries?currency=USD
GET /api/countries?sort=gdp_desc
```

---

🧠 Error Responses

| Code | Example                                           |
| ---- | ------------------------------------------------- |
| 400  | `{ "error": "Validation failed" }`                |
| 404  | `{ "error": "Country not found" }`                |
| 503  | `{ "error": "External data source unavailable" }` |
| 500  | `{ "error": "Internal server error" }`            |

---

🖼️ Summary Image

After each refresh, the API generates an image summary showing:

* Total countries
* Top 5 by estimated GDP
* Last refresh time

Access it via:

```
GET /api/countries/image
```

If no image exists:

```
{ "error": "Summary image not found" }
```

---

🧪 Postman Testing Guide

Step 1: Import the Collection

You can test all API endpoints instantly using Postman.

1. Open Postman
2. Click **Import** → **Raw Text**
3. Paste this JSON below and click **Import**

```json
{
  "info": {
    "name": "Country Exchange Rate API",
    "_postman_id": "e54a28d5-94b7-41a9-8b50-7b4c24d2c7e1",
    "description": "Postman collection for testing the Country Exchange Rate API",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "item": [
    {
      "name": "Refresh Countries",
      "request": { "method": "POST", "url": "{{base_url}}/api/countries/refresh" }
    },
    {
      "name": "List All Countries",
      "request": { "method": "GET", "url": "{{base_url}}/api/countries" }
    },
    {
      "name": "Get Country by Name",
      "request": { "method": "GET", "url": "{{base_url}}/api/countries/Nigeria" }
    },
    {
      "name": "Delete Country",
      "request": { "method": "DELETE", "url": "{{base_url}}/api/countries/Nigeria" }
    },
    {
      "name": "Check API Status",
      "request": { "method": "GET", "url": "{{base_url}}/api/status" }
    },
    {
      "name": "Get Summary Image",
      "request": { "method": "GET", "url": "{{base_url}}/api/countries/image" }
    }
  ],
  "variable": [
    { "key": "base_url", "value": "http://127.0.0.1:8000" }
  ]
}
```

Step 2: Set Environment Variable

* In Postman, go to Environment → Add new variable:

  * `base_url = http://127.0.0.1:8000`

Step 3: Run Tests

Click **Send** on each request after starting the Laravel server:

```bash
php artisan serve
```

You should see JSON responses confirming each endpoint works.

---

👨‍💻 Author

Mykel Philip
GitHub: [@mykelphilip](https://github.com/mykelphilip)

---

🪪 License

MIT License

```
---

Would you like me to generate the actual `.json` Postman file (so you can download and import it instantly instead of copy-pasting)?
```
