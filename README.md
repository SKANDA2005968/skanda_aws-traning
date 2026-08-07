# Amaazon.in — Sample Amazon-style Store (PHP)

A mini e-commerce site inspired by Amazon.in, built in plain PHP + SQLite.
No Node, no Composer, no external database server needed — it just needs PHP.

## Features
- Homepage with banner, category tiles, deals, bestsellers
- Category pages with sort (price/rating/discount) and price filter
- Product detail page with ratings, stock status, related products
- Search across name / brand / description, optionally scoped to a category
- Session-based cart: add, update quantity, remove
- Checkout form (name, address, phone, payment method) that creates a real order
- Order confirmation page + "Your Orders" history page
- ~40 sample products across 10 categories (Mobiles, Electronics, Fashion,
  Home & Kitchen, Books, Beauty, Toys & Games, Grocery, Sports, Appliances)
- Auto-seeds itself: the SQLite database and sample data are created
  automatically the first time you load the site

## Requirements
- PHP 8.0 or newer, with the `pdo_sqlite` extension (bundled in PHP by default
  on almost every install/hosting plan)

## How to run it

1. Unzip the project.
2. From inside the project folder, start PHP's built-in server:

   ```bash
   cd amazon-clone
   php -S localhost:8000
   ```

3. Open **http://localhost:8000** in your browser.

That's it — on first load, `data/store.db` is created automatically and
filled with the sample categories and products.

### Running on XAMPP / WAMP / a real web server
Just copy the whole `amazon-clone` folder into your server's document root
(e.g. `htdocs/amazon-clone`) and visit `http://localhost/amazon-clone/`.
Make sure the `data/` folder is writable by PHP (it needs to create
`store.db` there).

### Resetting the sample data
Delete `data/store.db` and reload the site — it will be recreated fresh.

## Project structure
```
amazon-clone/
├── index.php              Homepage
├── category.php           Category listing + sort/filter
├── product.php            Product detail page
├── search.php              Search results
├── cart.php                Cart page
├── cart_action.php         Handles add/update/remove (POST)
├── checkout.php            Address + payment form, places the order
├── order_success.php       Order confirmation screen
├── orders.php               Order history
├── config/database.php      DB connection (SQLite by default)
├── database/seed.php        Creates tables + inserts sample data
├── database/schema.sql       Schema reference
├── includes/                 Shared header, footer, helper functions
├── assets/css/style.css      All styling
└── data/store.db              Auto-created SQLite database file
```

## Switching to MySQL (optional)
If you'd rather use MySQL: create a database, run the queries in
`database/schema.sql` (MySQL-compatible SQL, minus the `AUTOINCREMENT`
keyword differences are noted inline), then swap the `getDB()` function
in `config/database.php` for the commented-out MySQL version at the
bottom of that file, filling in your host/user/password. You'll also
want to change `RANDOM()` to `RAND()` in the `ORDER BY` clauses used on
the homepage and product pages.

## Notes
- This is a learning/demo project: payment is simulated (no real
  gateway), and there's no login/authentication system — "Your Orders"
  shows every order placed on the server.
- Product images are placeholder photos loaded from picsum.photos, so
  an internet connection is needed to see images (everything else
  works fully offline).
- Not affiliated with, endorsed by, or connected to Amazon.com, Inc. in
  any way — brand names, product names and prices are fictional.
