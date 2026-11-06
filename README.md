# Taskflow

## Run with Docker

You need Docker Desktop with Docker Compose enabled.

1. Copy `.env.example` to `.env`.
2. Build the app image and start MySQL:

   ```sh
   docker compose up -d --build db
   docker compose build app
   ```

3. Generate the application key and create the database tables:

   ```sh
   docker compose run --rm app php artisan key:generate
   docker compose run --rm app php artisan migrate --force
   ```

4. Start the app:

   ```sh
   docker compose up -d app
   ```

Open [http://localhost:8000](http://localhost:8000). Set `APP_PORT` in `.env` to use a different host port. The MySQL database and Laravel storage use Docker volumes and persist across container restarts.

The Compose file uses local development database credentials by default. Set `DB_PASSWORD` and `DB_ROOT_PASSWORD` in `.env` to override them; do not use the defaults outside local development.

To stop the containers, run `docker compose down`. To also delete the database and uploaded files, run `docker compose down --volumes`.
