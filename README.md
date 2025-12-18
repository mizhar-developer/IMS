# Image Management System (IMS)

## Overview

IMS is a Laravel-based academic project demonstrating service-layered architecture, the repository pattern, and interface-driven design. The app implements a simplified medical imaging workflow: patients, staff, image uploads, diagnoses, automated billing and invoice generation, payments, and role-based access control.

This README gives detailed instructions to deploy the project locally (Windows, macOS, Linux) for development.

**Requirements**

-   PHP 8.1+ (with PDO, mbstring, OpenSSL, JSON, BCMath, zip)
-   Composer
-   MySQL or PostgreSQL (or SQLite for quick tests)
-   Git (optional)

**Recommended (Windows)**

-   Install PHP

**Local setup (step-by-step)**

1. Clone the repository and install PHP dependencies

    ```bash
    git clone https://github.com/mizhar-developer/IMS ims
    cd ims
    composer install --no-interaction --prefer-dist
    ```

Quick setup (copy-paste)

```bash
# clone
git clone https://github.com/mizhar-developer/IMS ims && cd ims
# install PHP deps
composer install
# copy env
cp .env.example .env
# generate key
php artisan key:generate
# run migrations
php artisan migrate --seed
# create storage symlink
php artisan storage:link
# start dev server
php artisan serve
```

3. Environment variables

    - Copy the example env and edit database and storage settings. This step is required — the application reads configuration from `.env`.

    ```bash
    # Unix / macOS
    cp .env.example .env
    # Windows PowerShell
    copy .env.example .env
    ```

    - After copying, open `.env` and set DB and other connection values as described below.

    - Set DB connection values in `.env`:

    - For MySQL

        ```env
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=ims_db
        DB_USERNAME=ims_user
        DB_PASSWORD=secret
        ```

    - For PostgreSQL

        ```env
        DB_CONNECTION=pgsql
        DB_HOST=127.0.0.1
        DB_PORT=5432
        DB_DATABASE=ims_db
        DB_USERNAME=ims_user
        DB_PASSWORD=secret
        ```

    - If you use S3 for storage, set `FILESYSTEM_DRIVER=s3` and provide AWS keys.

4. Generate app key

    ```bash
    php artisan key:generate
    ```

5. Create database and run migrations

    - Create the database used in `.env` (e.g., `ims_db`).

    - Run migrations (this project includes a migration to add the `adjustment` column):

    ```bash
    php artisan migrate
    ```

    - If you want seeded sample data (if provided):

    ```bash
    php artisan db:seed
    ```

6. Storage and file permissions

    - Create storage symlink so uploaded files are publicly accessible:

    ```bash
    php artisan storage:link
    ```

7. Run the application locally

    ```bash
    # Development server
    php artisan serve --host=127.0.0.1 --port=8000

    # Visit http://127.0.0.1:8000
    ```

Optional: enable S3 storage

-   Set these in `.env` when using S3:

    ```env
    FILESYSTEM_DRIVER=s3
    AWS_ACCESS_KEY_ID=your_key
    AWS_SECRET_ACCESS_KEY=your_secret
    AWS_DEFAULT_REGION=your_region
    AWS_BUCKET=your_bucket
    AWS_URL=https://your-bucket.s3.amazonaws.com
    ```

Security & caution

-   This project is for academic use. Do not expose development credentials or use production secrets in `.env.example`.

---

Thank you for checking my IMS-System — ask if you want any of the optional features scaffolded.
