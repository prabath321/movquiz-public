# MovQuiz Trivia API

A Laravel-based REST API that powers the **MovQuiz Trivia** mobile application. The API retrieves movie and TV series data from TMDB, generates quiz questions, stores them in the database, and serves them to the mobile application.

## Features

* 🎬 Fetches movie data from TMDB
* 📺 Fetches TV series data from TMDB
* 🧩 Automatically generates trivia questions
* 🏆 Supports multiple quiz levels (Series 1–6)
* 🖼️ Generates home screen content
* ⏰ Designed to run daily using a cron job
* 🚀 RESTful API built with Laravel

---

## Requirements

* PHP 8.0.2+
* Laravel 9.52.16
* MySQL 8+
* Docker (recommended)
* TMDB API Token

---

## Installation

### 1. Clone the repository

```bash
git clone git@github.com:prabath321/movquiz.git
cd movquiz
```

Install PHP dependencies.

```bash
composer install
```

### 2. Configure the environment

Copy the example environment file.

```bash
cp .env.example .env
```

Add your TMDB API token to the `.env` file.

```env
TMDB_TOKEN=your_tmdb_api_token
TMDB_URL=https://api.themoviedb.org/3/
TMDB_BACKDROP_IMGPATH=http://image.tmdb.org/t/p/
```

Generate an application key.

```bash
php artisan key:generate
```

---

## Running with Docker

Start the containers.

```bash
docker compose up -d
```

Run the database migrations.

```bash
sudo docker exec -it movquiz_app php artisan migrate
```

---

## API Endpoints

### Generate Movie Home Data

Retrieves movie data from TMDB, creates home images, and stores them in the database.

This endpoint should be executed once per day (for example, at midnight using a cron job).

```
GET /api/movie/home
```

Example:

```
http://127.0.0.1:8001/api/movie/home
```

---

### Generate TV Series Home Data

Retrieves TV series data from TMDB, creates home images, and stores them in the database.

This endpoint should also be executed once per day.

```
GET /api/tvseries/home
```

Example:

```
http://127.0.0.1:8001/api/tvseries/home
```

---

### Retrieve Movie Home Data

Retrieves the stored movie home images from the database.

```
GET /api/movie/home1
```

Example:

```
http://127.0.0.1:8001/api/movie/home1
```

---

### Generate Quiz Questions

Retrieves data from TMDB, generates quiz questions and quiz levels, and stores them in the database.

```
GET /api/movie/questions/{keyword}/{category}/{field}/{series}
```

#### Parameters

| Parameter | Description                                                                                                                   |
| --------- | ----------------------------------------------------------------------------------------------------------------------------- |
| keyword   | `movie` or `tv`                                                                                                               |
| category  | **Movies:** `upcoming`, `top_rated`, `popular`, `now_playing`<br>**TV:** `airing_today`, `on_the_air`, `popular`, `top_rated` |
| field     | `title` or `original_name`                                                                                                    |
| series    | `1` to `6`                                                                                                                    |

Example:

```
http://127.0.0.1:8001/api/movie/questions/movie/upcoming/title/1
```

---

### Retrieve Quiz Data

Returns all quiz questions for a specific category and level.

```
GET /api/data/{model}/{series}
```

#### Available Models

Movies

* MovieUpcoming
* MovieTopRated
* MoviePopular
* MovieNowPlaying

TV Series

* TvAiringToday
* TvOnTheAir
* TvPopular
* TvTopRated

#### Parameters

| Parameter | Description      |
| --------- | ---------------- |
| model     | Quiz model name  |
| series    | Quiz level (1–6) |

Example:

```
http://127.0.0.1:8001/api/data/MovieUpcoming/1
```

---

### Retrieve Levels

Returns the available quiz levels.

```
GET /api/level/{model}
```

Example:

```
GET /api/level/MovieUpcoming
```

---

### Daily Cron Job

Runs the complete daily process, including:

* Fetching new TMDB data
* Generating quiz questions
* Creating quiz levels
* Preparing home page data

```
GET /api/cron/{secret}
```

It is recommended to execute this endpoint once every day using a scheduled cron job.

---

## Typical Daily Workflow

1. Generate Movie Home Data

```
GET /api/movie/home
```

2. Generate TV Series Home Data

```
GET /api/tvseries/home
```

3. Generate Quiz Questions

Example:

```
GET /api/movie/questions/movie/upcoming/title/1
```

Repeat for each required movie and TV category.

4. Retrieve Quiz Data

Example:

```
GET /api/data/MovieUpcoming/1
```

---

## Technology Stack

* Laravel 9.52.16
* PHP 8.0.2+
* MySQL
* Docker
* TMDB API

---

## Notes

* A valid TMDB API token is required.
* Generated data is stored locally in the database for faster retrieval by the mobile application.
* The API is intended to be executed daily to keep movie and TV data up to date.

---

## License

This project is released under the MIT License.

---

## Author

Pium Leevanage Full Stack Developer | Laravel + React Engineer | Software Consultant
