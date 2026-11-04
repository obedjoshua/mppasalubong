# Capstone Project Technical Summary & Deployment Guide

## Project Title
**AI-Driven E-Commerce Platform with Visual Product Recognition and Conversational Support for Masaganang Pagpapala Pasalubong**

## Capstone Development Team
* **Joshua, Obed Daniel**
* **Briones, Mark Justine L.**
* **Caño, Aaron DC.**

---

## System Overview & Architecture

The **Masaganang Pagpapala Pasalubong AI-Driven E-Commerce Platform** is an enterprise-grade multi-branch web application engineered to digitize store operations and streamline customer transactions across the Salacot and San Jose physical store branches. 

The application utilizes a modular Model-View-Controller (MVC) architecture powered by Laravel and MariaDB. It integrates visual product parsing via Vision-Language Models (VLM), real-time conversational assistance, cloud object storage, digital payment processing, and web-based QR ticket verification.

```
+--------------------------------------------------------------------------------------------------+
|                                    SYSTEM ARCHITECTURE                                           |
+--------------------------------------------------------------------------------------------------+
|                                                                                                  |
|   +-------------------+    +--------------------+    +--------------------+    +-----------------+   |
|   |  Customer Device  |    |  Groq Vision API   |    |  OpenRouter AI     |    | Cloudflare R2   |   |
|   | (Web Browser UI)  |    |     (Qwen VLM)     |    |   (Nemotron 3)     |    | (Bucket Store)  |   |
|   +---------+---------+    +---------+----------+    +---------+----------+    +--------+--------+   |
|             │                        │                         │                        │        |
|             ▼                        ▼                         ▼                        ▼        |
|   +-------------------------------------------------------------------------------------------+  |
|   |                              Laravel MVC Backend Engine                                   |  |
|   |   (Authentication, Order Intake, Inventory Engine, QR Tokenizer, Distance Calculator)      |  |
|   +---------------------------------------------+---------------------------------------------+  |
|                                                 │                                                |
|                                                 ▼                                                |
|                                +----------------------------------+                              |
|                                |   MariaDB Relational Database    |                              |
|                                +----------------------------------+                              |
|                                                                                                  |
+--------------------------------------------------------------------------------------------------+
```

---

## Comprehensive System Features

### 1. Customer-Facing Portal

* **AI Visual Custom Cake Parser:**
  * Allows customers to upload cake inspiration photos directly to Cloudflare R2 object storage.
  * Leverages the **Groq Vision API (Qwen VLM)** to analyze uploaded images, extracting design parameters, estimated tiers, color palettes, and theme options into structured JSON data to auto-fill ticket requests.

* **Conversational AI Customer Support:**
  * 24/7 automated assistant powered by **Nvidia Nemotron 3 Ultra** via OpenRouter.
  * Handles natural language inquiries regarding store products, operating hours, custom cake policies, and branch locations.
  * Provides seamless escalation links to Facebook Messenger when human staff intervention is required.

* **Store Locator & Branch Selector:**
  * Integrated with **MapLibre GL** and **OpenFreeMap** to render interactive store location maps.
  * Calculates distance between customer coordinates and selected pickup branches using an internal Haversine distance engine.

* **Digital Checkout & Payment Gateway:**
  * Shopping cart management for standard bakery catalog items and approved custom cake tickets.
  * Direct payment authorization integration via **PayMongo**, supporting GCash, Maya, and credit/debit cards.

* **Encrypted QR Order Claim Tickets:**
  * Generates unique Base64-encoded digital order tickets and encrypted SVG QR code tokens upon checkout completion.
  * Serves as proof of payment and claim ticket for in-store pickup.

* **Community Engagement Feed:**
  * Interactive customer feed allowing users to share custom cake reviews and post photos.
  * Features emoji reaction handling (`Reaction`), comments (`Comment`), and an automated milestone badge unlock system (`Badge`).

---

### 2. Store & Branch Operations Module

* **Multi-Branch Inventory Management:**
  * Real-time inventory tracking synchronized across physical store branches (Salacot and San Jose).
  * System-logged inter-branch stock transfers (`BRANCH_STOCK_TRANSFERS`) and automated item deductions upon successful online or walk-in purchases.

* **Kitchen Scheduling & Production Board:**
  * Production dashboard for bakery staff to review AI-extracted custom cake design specifications.
  * Enables staff to adjust tier pricing, send modification notes to customers, lock kitchen preparation time slots, and update fulfillment states (`In Production` $\rightarrow$ `Ready for Pickup`).

* **Counter Pickup & QR Verification Web Tool:**
  * Integrated web scanner tool allowing store staff to scan customer digital QR tickets using device cameras.
  * Instantly verifies QR token validity, confirms payment status, updates order fulfillment to `Completed`, and generates point-of-sale (POS) receipt records.

* **Daily Staff & Shift Cash Drawer Reconciliation:**
  * Enforces operational logging for employee clock-ins, shift balances, and opening/closing cash drawer reconciliations (`DAILY_STAFF_LOGS`).

* **Administrative Management & Analytics:**
  * Access-controlled administrative portal for catalog modification, tier pricing management, notification API token configuration (Brevo SMS and Mailjet Email), and multi-branch sales analytics.

---

## Technical Stack

| Domain | Technology / Tool |
| :--- | :--- |
| **Backend Architecture** | Laravel (PHP MVC Framework) |
| **Database Management** | MariaDB / MySQL |
| **Frontend UI** | Blade Templates, Tailwind CSS, JavaScript (ES6) |
| **Visual AI Recognition** | Groq Vision API (Qwen VLM Model) |
| **Conversational AI** | OpenRouter API (Nvidia Nemotron 3 Ultra Model) |
| **Object Cloud Storage** | Cloudflare R2 Storage |
| **Payment Gateway** | PayMongo API (GCash, Maya, Cards) |
| **Mapping Engine** | MapLibre GL / OpenFreeMap |
| **Notification Services** | Brevo SMS API, Mailjet Email API |
| **Local Offline Caching** | IndexedDB Service Workers |

---

## System Setup & Installation Guide

### Prerequisites
Ensure your local development environment or server has the following installed:
* **PHP** $\ge 8.2$ (with `pdo`, `mbstring`, `openssl`, `curl`, `gd`, `xml` extensions enabled)
* **Composer** (PHP Dependency Manager)
* **Node.js** ($\ge 18.x$) & **NPM**
* **MariaDB** ($\ge 10.4$) or **MySQL** ($\ge 8.0$)

---

### Step-by-Step Installation Instructions

#### 1. Repository Clone & Dependency Installation
Clone the repository and install PHP and Node.js dependencies:

```bash
# Clone the repository
git clone https://github.com/your-repo/masaganang-pagpapala-platform.git
cd masaganang-pagpapala-platform

# Install PHP dependencies via Composer
composer install

# Install Node modules
npm install
```

#### 2. Environment Configuration
Create your environment variable configuration file:

```bash
# Copy the example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

Configure your `.env` file credentials:

```ini
APP_NAME="Masaganang Pagpapala E-Commerce"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=masaganang_pagpapala_db
DB_USERNAME=root
DB_PASSWORD=

# Cloudflare R2 Object Storage Credentials
AWS_ACCESS_KEY_ID=your_cloudflare_r2_access_key
AWS_SECRET_ACCESS_KEY=your_cloudflare_r2_secret_key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=masaganang-media-bucket
AWS_ENDPOINT=https://<account-id>.r2.cloudflarestorage.com
AWS_USE_PATH_STYLE_ENDPOINT=true

# AI Service API Keys
GROQ_VISION_API_KEY=gsk_your_groq_vision_key
OPENROUTER_API_KEY=sk-or-v1-your_openrouter_key

# PayMongo Payment Credentials
PAYMONGO_PUBLIC_KEY=pk_test_your_public_key
PAYMONGO_SECRET_KEY=sk_test_your_secret_key

# Third-Party Notification APIs
BREVO_SMS_API_KEY=your_brevo_key
MAILJET_API_KEY=your_mailjet_key
MAILJET_SECRET_KEY=your_mailjet_secret
```

#### 3. Database Migration & Seeding
Create the database in MariaDB/MySQL (`masaganang_pagpapala_db`), then execute database migrations and seed initial administrative accounts and branch locations:

```bash
# Run database migrations and seeders
php artisan migrate --seed
```

#### 4. Storage Link Symlink
Create the public storage symlink for uploaded assets:

```bash
php artisan storage:link
```

#### 5. Compile Assets & Run Server
Run the Vite development server alongside the Laravel local development server:

```bash
# Compile frontend assets
npm run dev

# Start Laravel development server (in a separate terminal tab)
php artisan serve
```

Access the application at `http://localhost:8000`.

---

## Verification & Deployment Commands

To run quality and environment verification checks prior to cloud deployment:

```bash
# Run application route and configuration caching
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear cached configuration during debugging
php artisan optimize:clear
```
