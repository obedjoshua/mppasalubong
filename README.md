<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
# Admin and developer portals

Portal access is granted only by the user's `role` (`admin`, `developer`, or `staff`), never by email domain. Administrators and developers have equal portal and staff-operation privileges: both can manage user roles at `/admin/users`, inspect instrumented API request metrics and traffic, moderate community posts, and view database/cache health and read-only Git status.

API metrics currently cover the instrumented color-extraction endpoint only. No API-key management system or external connection integration exists in this application, so the portal does not claim to manage those. Request bodies and client IP addresses are not retained. Git support runs only the fixed, read-only `git status --short --branch` command.

Set `PORTAL_ADMIN_SEED_PASSWORD` and `PORTAL_DEVELOPER_SEED_PASSWORD` in the ignored local `.env` file; the emails default to `admin@demo.local` and `developer@demo.local`. The portal seeder creates missing accounts but never resets the password of an existing account when seeding again, so their credentials remain stable. Keep these unique passwords in a password manager. If either account does not exist and its password is unset, the seeder skips it and writes a warning. Run `php artisan db:seed --class="Database\Seeders\PortalAccountSeeder"` to create missing portal accounts.

## Shop catalog and promotions

Administrators and developers can manage public products and promotional banners at `/admin/store` (also linked from either portal dashboard). Product changes use the existing `products` table and retain its slug, category, price, description, stock, and attributes fields; removed products are soft-deleted. Product highlights and extra gallery image paths are stored in the existing `attributes` JSON field. Banner copy is plain text, with validated image, link, color, alignment, and layout controls; editor content is never interpreted as HTML, CSS, or JavaScript.

Apply the banner table migration with `php artisan migrate`. Uploaded product and banner images use Laravel's `public` storage disk. For local/public URL serving, run `php artisan storage:link` once after deployment and ensure `storage/app/public` is writable. Existing product image URLs remain supported.

Public product detail pages use `/shop/{slug}`. Chatbot integrations can query `GET /shop/products/lookup?q=...&category=...`; the response includes public catalog details, availability, stable slug, and canonical product URL, and never includes internal stock records or user data.
