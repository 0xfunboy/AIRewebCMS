<div align="center">

<img src="https://capsule-render.vercel.app/api?type=waving&color=gradient&customColorList=6,11,20&height=160&section=header&text=AIRewebCMS&fontSize=50&fontColor=fff&animation=fadeIn&fontAlignY=38&desc=CMS%20for%20AI%20Agents%20and%20Crypto%20Project%20Business%20Agency&descAlignY=60&descSize=14" width="100%"/>

<img src="https://skillicons.dev/icons?i=php,js,css,mysql&theme=dark" alt="Tech stack"/>

</div>

# AIRewebCMS

A lightweight PHP + MySQL CMS powering the AIRewardrop agent website. The system replaces the former React front-end with server-rendered views, an admin dashboard, WalletConnect-based authentication, and database-driven content modules for products, blog posts, roadmap milestones, partners, social proof, and more.

## Features
- **Wallet-based admin login** using WalletConnect v2 with nonce validation and session tracking.
- **Modular admin dashboard** covering Products, Agents, Partners, Team, Blog Posts, Social Proof, Roadmap (phases + tracks), and global Settings.
- **Media library explorer & optimizer** with filters, in-use badges, WebP conversion logs, safe same-URL replace/delete actions, and a reusable media picker that powers every upload field.
- **English-first admin UI** with consistent copy and helper controls (read-only media URLs, Copy/Upload image/Select from media buttons).
- **Public site pages** rendered through PHP templates that mirror the original Tailwind-styled marketing content.
- **MySQL migrations** in `database/schema.sql` aligned with the CMS features (admins, sessions, content tables, etc.).
- **Extensible service layer** (`app/Services`) for additional content modules and integrations.
- **Inline admin editing**: authenticated admins can toggle an in-page editing mode, adjust texts/URLs/images, and upload media directly from the frontend.

## Production and recovery

Serve only the `public/` directory through PHP-FPM. The deployed CMS uses MySQL, the schema and seed helpers in `database/`, and the PHP application and templates in `app/`. The production installation is already initialized and has no web installer.

Provide private credentials in `.env.php` outside the document root. Use `.env.example.php` to identify the settings. Configure a unique application key and database credentials before starting a new instance. Restore the database, accounts, CMS uploads and writable storage from a verified private backup. The dated uploads are installation data. The SVG assets in `public/media/svg/` are part of the application.

`sha256sum -c production.sha256` checks the distributed PHP and static application files. To test a recovery, point a separate PHP-FPM pool at the restored copy, use a separate MySQL instance and verify both the public site and authenticated CMS access before switching traffic.

## Licensing

Licensing scope and preserved third-party permissions are documented in [LICENSING.md](LICENSING.md). The [0xfunboy Non-Commercial License](LICENSE.md) covers eligible original material only.
