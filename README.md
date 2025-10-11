# AIRewebCMS

A lightweight PHP + MySQL CMS powering the AIRewardrop agent website. The system replaces the former React front-end with server-rendered views, an admin dashboard, WalletConnect-based authentication, and database-driven content modules for products, blog posts, roadmap milestones, partners, social proof, and more.

## Features
- **Wallet-based admin login** using WalletConnect v2 with nonce validation and session tracking.
- **Modular admin dashboard** covering Products, Agents, Partners, Team, Blog Posts, Social Proof, Roadmap (phases + tracks), and global Settings.
- **Media library explorer & optimizer** with previews, quick URL copy actions, plus dedicated actions to mirror external assets locally and convert the entire library to WebP under `public/media/`.
- **Public site pages** rendered through PHP templates that mirror the original Tailwind-styled marketing content.
- **Installer seeding**: the setup wizard (`public/install.php`) runs the schema and populates starter content from `database/seed-data.php` one time.
- **MySQL migrations** in `database/schema.sql` aligned with the CMS features (admins, sessions, content tables, etc.).
- **Extensible service layer** (`app/Services`) for additional content modules and integrations.
- **Inline admin editing**: authenticated admins can toggle an in-page editing mode, adjust texts/URLs/images, and upload media directly from the frontend.

## Project Structure
```
AIRewebCMS/
├── app/                # Core framework (router, controllers, services, middleware, views)
├── database/           # Schema + seed data
├── public/             # Web root, router front controllers, assets
├── scripts/            # CLI tools (seed importer)
├── storage/            # Runtime storage (logs, cache)
├── .env.example.php    # Environment configuration template
└── README.md
```

## Requirements
- PHP 8.1 or newer with PDO MySQL extension
- MySQL 8.0 (or compatible) database
- OpenSSL extension (for random bytes) and GMP extension (for signature verification)
- Imagick **or** GD (with WebP support) for media optimisation
- Web server capable of serving `public/` as document root (Apache/Nginx) or PHP built-in server for local use

## Configuration (`.env.php`)
Duplicate `.env.example.php` and tailor the following keys:

| Section | Key | Notes |
| --- | --- | --- |
| `app` | `name`, `url`, `timezone`, `key`, `session_name` | `key` must be a 64‑char base64 string (`php -r "echo base64_encode(random_bytes(32));"`). |
| `database` | `host`, `port`, `database`, `username`, `password`, `charset`, `collation`, `socket` | Provide credentials for a blank MySQL schema. |
| `wallet` | `allowed_addresses`, `project_id`, `rpc_url` | Addresses authorised for admin login and WalletConnect project details. |
| `mail` | SMTP parameters | Optional, used for outbound mail. |

## Installation
1. **Clone or copy** this directory to your server.
2. **Create the environment file:**
   ```bash
   cp .env.example.php .env.php
   ```
   Edit the new file with your production database credentials, WalletConnect settings, and app metadata.
3. **Set writable permissions** on the storage directories:
   ```bash
   chmod -R 775 storage
   ```
   (Adjust owners/groups to match your web server user.)
4. **Create the database (and user, if needed)** and run the schema migrations. Example MySQL session:
   ```sql
   CREATE DATABASE aireweb DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'aire_user'@'localhost' IDENTIFIED BY 'change_me';
   GRANT ALL PRIVILEGES ON aireweb.* TO 'aire_user'@'localhost';
   FLUSH PRIVILEGES;
   ```
   Then load the schema:
   ```bash
   mysql -u aire_user -p aireweb < database/schema.sql
   ```
5. (Facoltativo) per importare i seed legacy TypeScript esegui: `php scripts/import.php`.
6. **Wizard di installazione (una sola volta)**
   - Carica il progetto sul server e assicurati che `public/` sia la document root.
   - Visita `https://tuodominio/install.php` e clicca “Avvia installazione”. Lo script crea tutte le tabelle, importa i seed (`database/seed-data.php`) e scrive `storage/install.lock`.
   - Al termine elimina o rinomina `public/install.php` e, se desideri reinstallare, rimuovi `storage/install.lock`.

7. **Serve the site**:
   - Local testing: `php -S 127.0.0.1:8000 -t public public/router.php`
   - Production: point your web server’s document root to the `public/` directory and ensure `public/router.php` handles requests.

## Inline Admin Editing
Authenticated admins see a toolbar on every public page:

1. **Toggle Modalità Admin** – enables/disables the inline editing overlay (state stored in session).
2. **Editable elements** – when active, blocks marked with `data-model`, `data-key`, and optional `data-id` expose action buttons:
   - **Edit / Save / Cancel** for plain text.
   - **Edit HTML** opens a modal editor for rich text.
   - **Replace** triggers the secure upload flow for images (png, jpg, jpeg, webp, svg, ico – 5 MB max) saved under `public/media/YYYY/MM/`. SVG uploads are sanitised and rasterised to PNG automatically.
3. **API behind the scenes**
   - `POST /admin/api/update-field` with JSON payload `{ model, key, id?, value, csrf }`.
   - `POST /admin/api/upload-image` accepts multipart data with the same metadata.
   - Responses always include an updated CSRF token. Every change is logged in the `audit_log` table with the admin wallet address.
4. **Admin toolbar updates**
   - The toolbar now sits above the site header, keeping page content offset automatically on desktop and mobile.
   - A dedicated “Dashboard” shortcut replaces the previous preview link. “Logout” prompts to confirm exiting admin mode before redirecting to `/auth/logout`.

5. **Extending inline editing**
   - Wrap new fields with `<?= \App\Support\AdminMode::dataAttrs('model', 'field', $id); ?>`.
   - Whitelist the pair in `App\Controllers\Admin\AdminInlineController::$modelMap`.
   - Admin assets are loaded only when the user is authenticated, so regular visitors see the original markup untouched.

### Admin Media Library & Upload Fields
- Every dashboard form that accepts logos, avatars, hero images, or social graphics now includes a **media field** with live previews, manual URL entry, and optional file upload.
- Uploaded assets are stored beneath `public/media/YYYY/MM/<slug>-<hash>.<ext>` via `App\Support\Uploads`. SVGs are sanitised and converted into transparent PNGs when ImageMagick is available, and every path is returned with a leading `/` so you can drop it straight into inline editing or templates.
- From **Admin → Media Library** trigger `Local Mirror Images` to download any remote assets referenced across settings or content tables, then `Optimize to WebP` to convert the local library while tracking progress. The optimizer uses Imagick when available and falls back to GD (with WebP support) otherwise.
- The default favicon lives at `public/favicon.ico` (referenced via the `settings.favicon_path` key). Replace it in Media Library or by dropping a new ICO file into `public/`.
- Visit **Dashboard → Media Library** to browse all uploaded files, open them in a new tab, or copy absolute URLs to your clipboard.

## Usage
- Visit `/login` to access the admin area. Configure the allowed wallet addresses in `.env.php`.
- The dashboard provides CRUD interfaces for all public-facing modules. Saving changes updates the MySQL tables consumed by the public pages.
- Public routes (e.g., `/products`, `/roadmap`, `/tokenomics`, `/press`) automatically reflect database content.

## Deployment Notes
- Ensure HTTPS is enforced so session cookies use the `Secure` flag.
- Rotate and secure the application key and WalletConnect credentials in `.env.php`.
- Monitor `storage/logs/app.log` for runtime errors.
- Regularly back up the MySQL database and storage directory.

Happy shipping!
