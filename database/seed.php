<?php
/**
 * Creates the schema and inserts sample data.
 * Called automatically from config/database.php the first time
 * data/store.db doesn't exist yet.
 */

function seedDatabase(PDO $pdo) {

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            slug TEXT NOT NULL UNIQUE,
            icon TEXT
        );

        CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            category_id INTEGER NOT NULL,
            name TEXT NOT NULL,
            slug TEXT NOT NULL UNIQUE,
            brand TEXT,
            description TEXT,
            price REAL NOT NULL,
            mrp REAL NOT NULL,
            discount_percent INTEGER DEFAULT 0,
            rating REAL DEFAULT 4.0,
            rating_count INTEGER DEFAULT 0,
            stock INTEGER DEFAULT 100,
            image_seed TEXT,
            is_bestseller INTEGER DEFAULT 0,
            is_assured INTEGER DEFAULT 0,
            FOREIGN KEY (category_id) REFERENCES categories(id)
        );

        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_number TEXT NOT NULL UNIQUE,
            customer_name TEXT,
            address TEXT,
            total REAL NOT NULL,
            status TEXT DEFAULT 'Placed',
            created_at TEXT DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS order_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_id INTEGER NOT NULL,
            product_id INTEGER NOT NULL,
            quantity INTEGER NOT NULL,
            price REAL NOT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(id),
            FOREIGN KEY (product_id) REFERENCES products(id)
        );
    ");

    // ---------------- Categories ----------------
    $categories = [
        ['Mobiles',        'mobiles',        '📱'],
        ['Electronics',    'electronics',    '💻'],
        ['Fashion',        'fashion',        '👕'],
        ['Home & Kitchen', 'home-kitchen',   '🏠'],
        ['Books',          'books',          '📚'],
        ['Beauty',         'beauty',         '💄'],
        ['Toys & Games',   'toys-games',     '🧸'],
        ['Grocery',        'grocery',        '🛒'],
        ['Sports',         'sports',         '🏸'],
        ['Appliances',     'appliances',     '🔌'],
    ];

    $insCat = $pdo->prepare('INSERT INTO categories (name, slug, icon) VALUES (?, ?, ?)');
    foreach ($categories as $c) {
        $insCat->execute($c);
    }

    $catId = [];
    foreach ($pdo->query('SELECT id, slug FROM categories') as $row) {
        $catId[$row['slug']] = $row['id'];
    }

    // ---------------- Products ----------------
    // [category_slug, name, brand, price, mrp, rating, rating_count, bestseller, assured, description]
    $products = [
        ['mobiles', 'Galaxy Vega 5G (128GB)', 'Samsang', 18999, 24999, 4.3, 15243, 1, 1,
            '6.5" FHD+ display, 5000mAh battery, 50MP triple camera, 5G ready, 8GB RAM.'],
        ['mobiles', 'OnePace Nord X 5G (256GB)', 'OnePace', 27999, 32999, 4.4, 9871, 1, 1,
            'Snapdragon processor, 120Hz AMOLED display, 67W fast charging, 12GB RAM.'],
        ['mobiles', 'Redme Note 14 Pro', 'Redme', 21499, 25999, 4.2, 21032, 0, 1,
            'Curved AMOLED display, 200MP camera, IP68 rated, 5000mAh battery.'],
        ['mobiles', 'iPhone 15 Lite (Renewed)', 'Appel', 54999, 64999, 4.5, 5321, 0, 1,
            'A16 chip, Super Retina display, dual camera system, iOS 18.'],
        ['mobiles', 'Realmy Narzox 70', 'Realmy', 15999, 19999, 4.0, 7654, 0, 0,
            'Budget 5G phone with 90Hz display and 5000mAh battery.'],

        ['electronics', 'NoiseFit Buds Pro (ANC)', 'BoAst', 1799, 3999, 4.1, 34521, 1, 0,
            'Active noise cancellation, 40hr playback, IPX5 water resistant.'],
        ['electronics', '43" 4K Smart LED TV', 'MicroVision', 22999, 39999, 4.2, 12043, 1, 1,
            'Ultra HD resolution, Dolby Audio, built-in streaming apps, 3 HDMI ports.'],
        ['electronics', 'Portable SSD 1TB USB-C', 'SunDisk', 6499, 8999, 4.6, 8760, 0, 1,
            'Read speeds up to 1050MB/s, shock resistant, plug and play.'],
        ['electronics', 'Wireless Mechanical Keyboard', 'Logi-Tech', 3299, 4999, 4.4, 3021, 0, 0,
            'Hot-swappable switches, RGB backlight, connects to 3 devices.'],
        ['electronics', 'Smartwatch Pro Series 3', 'FireBoltt', 2499, 6999, 4.0, 41230, 1, 0,
            '1.96" AMOLED display, Bluetooth calling, SpO2 and heart rate monitor.'],
        ['electronics', '65W GaN Fast Charger', 'AnkerLine', 1899, 2999, 4.5, 6543, 0, 1,
            'Compact 3-port charger, fast-charges laptop and phone together.'],

        ['fashion', "Men's Slim Fit Casual Shirt", 'Allen Solley', 899, 1999, 4.1, 4521, 0, 0,
            '100% cotton, machine washable, available in 5 colours.'],
        ['fashion', "Women's Ethnic Kurta Set", 'Biba Style', 1299, 2599, 4.3, 7890, 1, 0,
            'Rayon fabric, printed design, comes with matching dupatta.'],
        ['fashion', 'Running Shoes Air Cushion', 'Sparx-X', 1499, 2999, 4.2, 15632, 1, 0,
            'Lightweight mesh upper, breathable, ideal for daily running.'],
        ['fashion', 'Leather Analog Watch', 'Fasttrack', 1699, 2995, 4.0, 6210, 0, 0,
            'Genuine leather strap, water resistant up to 30m, 2-year warranty.'],
        ['fashion', 'Denim Jacket Unisex', 'RoadRunner', 1899, 3499, 3.9, 2145, 0, 0,
            'Classic blue denim, regular fit, durable stitching.'],

        ['home-kitchen', 'Non-Stick Cookware Set (5 Pcs)', 'Prestige+', 1999, 3499, 4.3, 9821, 1, 0,
            'Induction friendly, PFOA free coating, includes 2 lids.'],
        ['home-kitchen', 'Memory Foam Pillow (Set of 2)', 'SleepWell', 899, 1799, 4.2, 5632, 0, 0,
            'Orthopedic support, breathable cover, machine washable.'],
        ['home-kitchen', '750W Mixer Grinder', 'Bajaji', 2499, 3999, 4.1, 12340, 1, 0,
            '3 stainless steel jars, powerful copper motor, 2-year warranty.'],
        ['home-kitchen', 'Cotton Bedsheet with 2 Pillow Covers', 'Storiya Home', 799, 1599, 4.0, 8123, 0, 0,
            '300 thread count, king size, fade-resistant print.'],
        ['home-kitchen', 'Air Purifier HEPA Filter', 'Ozne', 6999, 11999, 4.3, 3421, 0, 1,
            'Removes 99.9% allergens, covers up to 400 sq ft, quiet mode.'],

        ['books', 'The Silent Ledger (Thriller Novel)', 'Ink & Page', 299, 499, 4.5, 3210, 0, 0,
            'A gripping financial-crime thriller set in Mumbai. Paperback, 320 pages.'],
        ['books', 'Learn Python the Practical Way', 'CodeCraft Press', 549, 899, 4.6, 2765, 1, 0,
            'Beginner to advanced Python guide with hands-on projects.'],
        ['books', 'Atomic Focus: Habits for Deep Work', 'Mindset Books', 399, 699, 4.4, 5432, 1, 0,
            'Practical techniques to build focus and productivity habits.'],
        ['books', "Grandma's Kitchen: Indian Recipes", 'Home Table Press', 449, 799, 4.3, 1892, 0, 0,
            'Traditional regional recipes passed down through generations.'],

        ['beauty', 'Vitamin C Face Serum 30ml', 'GlowRoot', 549, 999, 4.2, 18234, 1, 0,
            'Brightening serum with hyaluronic acid, suitable for all skin types.'],
        ['beauty', 'Matte Lipstick Combo (Pack of 3)', 'ColorPop Beauty', 499, 999, 4.0, 6543, 0, 0,
            'Long-lasting matte finish, transferproof, cruelty-free.'],
        ['beauty', 'Hair Growth Oil 200ml', 'Herbolix', 349, 599, 4.1, 9821, 0, 0,
            'Blend of onion, argan and coconut oil for stronger hair.'],
        ['beauty', 'Sunscreen SPF 50 PA+++', 'DermaCare', 449, 699, 4.4, 12043, 1, 0,
            'Lightweight, non-greasy, water resistant, broad spectrum protection.'],

        ['toys-games', 'Building Blocks Set (350 Pcs)', 'BrickWorld', 999, 1999, 4.5, 4321, 1, 0,
            'Compatible with major brick brands, boosts creativity, ages 6+.'],
        ['toys-games', 'Remote Control Racing Car', 'TurboToy', 1499, 2999, 4.2, 3210, 0, 0,
            'Rechargeable battery, 20km/h top speed, all-terrain tyres.'],
        ['toys-games', 'Classic Wooden Chess Set', 'MindGames', 799, 1299, 4.4, 2109, 0, 0,
            'Handcrafted wooden pieces, foldable board, storage for pieces.'],

        ['grocery', 'Basmati Rice 5kg', 'Farm Fresh', 549, 699, 4.3, 8765, 1, 0,
            'Long grain aged basmati rice, aromatic and fluffy when cooked.'],
        ['grocery', 'Cold Pressed Groundnut Oil 1L', 'PureVilla', 299, 399, 4.2, 5432, 0, 0,
            'Chemical-free extraction, rich in natural flavour and nutrients.'],
        ['grocery', 'Mixed Dry Fruits Pack 500g', 'NutriHarvest', 649, 999, 4.5, 6789, 1, 0,
            'Almonds, cashews, raisins and pistachios, vacuum sealed for freshness.'],

        ['sports', 'Yoga Mat with Carry Strap (6mm)', 'FitLife', 599, 1199, 4.3, 7654, 0, 0,
            'Non-slip texture, extra cushioning, lightweight and portable.'],
        ['sports', 'Badminton Racket Set (2 Pcs)', 'YonaPro', 899, 1599, 4.1, 3456, 0, 0,
            'Carbon fibre shaft, includes 2 shuttlecocks and cover bag.'],
        ['sports', 'Adjustable Dumbbell Set 10kg', 'PowerMax', 1799, 2999, 4.4, 2987, 1, 0,
            'Rubber coated plates, adjustable weight, ideal for home workouts.'],

        ['appliances', 'Inverter Split AC 1.5 Ton', 'Volatas', 32999, 45999, 4.2, 5643, 1, 1,
            '5-star energy rating, copper condenser, fast cooling technology.'],
        ['appliances', 'Front Load Washing Machine 7kg', 'Bosh', 24999, 34999, 4.3, 3421, 0, 1,
            '1200 RPM spin speed, in-built heater, 15 wash programs.'],
        ['appliances', 'Double Door Refrigerator 265L', 'LGnix', 27999, 35999, 4.1, 4210, 0, 1,
            'Frost-free, toughened glass shelves, stabiliser-free operation.'],
    ];

    $insProd = $pdo->prepare('
        INSERT INTO products
            (category_id, name, slug, brand, description, price, mrp, discount_percent,
             rating, rating_count, stock, image_seed, is_bestseller, is_assured)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');

    $seedCounter = 1;
    foreach ($products as $p) {
        [$slugCat, $name, $brand, $price, $mrp, $rating, $ratingCount, $bestseller, $assured, $desc] = $p;
        $discount = $mrp > 0 ? round((($mrp - $price) / $mrp) * 100) : 0;
        $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $name), '-')) . '-' . $seedCounter;
        $stock = rand(0, 1) === 0 ? rand(5, 200) : rand(0, 4);

        $insProd->execute([
            $catId[$slugCat],
            $name,
            $slug,
            $brand,
            $desc,
            $price,
            $mrp,
            $discount,
            $rating,
            $ratingCount,
            $stock,
            $seedCounter, // used to build a stable placeholder image URL
            $bestseller,
            $assured,
        ]);
        $seedCounter++;
    }
}
