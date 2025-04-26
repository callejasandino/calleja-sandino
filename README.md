# Project Setup

This project uses **Laravel** for the backend and **Vue.js 3** with **Tailwind CSS** for the frontend.

---

## Requirements

Make sure you have the following installed:

- **PHP** version `^8.2`
- **Node.js** version `22.13.1`
- **npm** version `10.9.2`
- **MySQL** database

Environment variables for database configuration:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=calleja_miguel
DB_USERNAME=root
```

---

## Installation

First, install all the necessary dependencies.

```bash
composer install
npm install
```

---

## Running the Application

Open **two terminals** and run the following commands:

1. In the first terminal, run:

    ```bash
    npm run dev
    ```

2. In the second terminal, run:

    ```bash
    php artisan serve
    ```

**Important:**  
Instead of accessing `localhost:5138`, open your app using:

```
http://127.0.0.1:8000
```

---

## Database Setup

1. Run migrations to create the necessary tables:

    ```bash
    php artisan migrate
    ```

2. Seed the database with default data:

    ```bash
    php artisan db:seed
    ```

---

## Notes

Initially, the goal was to **separate the frontend and backend** using **Laravel Sanctum** for authentication and a standalone **Vite Vue 3** frontend.  
However, since this project is intended to be a **single repository**, **Vue 3** was integrated directly into **Laravel** for simplicity and ease of setup.
