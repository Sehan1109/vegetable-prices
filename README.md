This folder contains a translated Laravel version of the React/Express Dashboard application.

## How to use:
1. You cannot run Laravel directly in the Google AI Studio environment (it only supports Node.js).
2. Download or copy this folder to your local machine.
3. Move `package.json`, `routes/`, `app/`, `resources/` and `database/` contents to a fresh Laravel installation.
4. Run `composer install` and `npm install`.
5. Run `php artisan migrate` to create the SQLite/MySQL tables.
6. Run `npm run dev` to start Vite (for Tailwind CSS and JS assets).
7. Run `php artisan serve` to start the Laravel backend.

## Translation Key Concepts:
- **Frontend / React**: We translated React `.tsx` files into Laravel `.blade.php` files utilizing **Alpine.js**. The `useState` variables in React became Alpine.js `x-data` models. 
- **Tailwind CSS**: The exact same classes are retained since Blade perfectly supports Tailwind.
- **Backend / Express**: We translated the Express routing in `server.ts` into standard Laravel Routes (`routes/web.php`) and controllers (`PriceDashboardController.php`).
- **Web Scraper (PDF parsing)**: The Node script running via `cron` was converted to a Laravel Artisan Command (`app/Console/Commands/ScrapeHartiPrices.php`). You would schedule this in Laravel's `routes/console.php`.
- **Database (db.json)**: We transformed the local file storage system into an Eloquent ORM Model (`app/Models/PriceRecord.php`) with a database migration structure.
