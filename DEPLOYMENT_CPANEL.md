# Chefchaouen.info — cPanel Terminal & GitHub Deployment Guide

This guide covers:
1. **First-time deployment** on cPanel using **cPanel Terminal + GitHub**
2. **Deploying future updates** in one command using [`./deploy.sh`](./deploy.sh)

---

## Requirements on cPanel
- **PHP**: `8.3` or `8.4` (enable in **MultiPHP Manager** / **Select PHP Version**)
- **Required PHP extensions**: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `tokenizer`, `xml`, `ctype`, `curl`, `bcmath`
- **Database**: MySQL / MariaDB
- **Git & Composer** available in **cPanel Terminal**

---

## Part 1: First-Time Deployment (cPanel Terminal)

### 1. Create MySQL Database in cPanel
1. Go to **cPanel → MySQL Databases** (or **Manage MySQL Databases**).
2. Create a database (e.g. `cpaneluser_chefchaouen`).
3. Create a database user with a strong password, and **Add User to Database** with **All Privileges**.

### 2. Clone from GitHub in cPanel Terminal
Open **cPanel → Advanced → Terminal** and clone into a folder outside `public_html` (recommended for Laravel security so `.env` is never web-exposed):

```bash
cd ~
git clone https://github.com/alindevx00x/chefchaouen-info.git
cd ~/chefchaouen-info
```

### 3. Configure `.env`
```bash
cp .env.example .env
nano .env
```

Update these production values in `.env`:
```env
APP_NAME="Chefchaouen.info"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cpaneluser_chefchaouen
DB_USERNAME=cpaneluser_dbuser
DB_PASSWORD=YOUR_STRONG_PASSWORD
```
*(In `nano`, press `Ctrl+O`, `Enter`, then `Ctrl+X` to save and exit.)*

### 4. Install Dependencies, Migrate, Seed & Storage Link
Run in cPanel Terminal inside `~/chefchaouen-info`:

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan storage:link
chmod -R 775 storage bootstrap/cache
php artisan migrate --seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Point Your Domain to `public/`
Choose **one** of these options:

- **Option A (Recommended — cPanel Domains):**
  Go to **cPanel → Domains → Manage**, and set your domain's **Document Root** to:
  `/home/YOUR_CPANEL_USER/chefchaouen-info/public`

- **Option B (Symlink `public_html` in Terminal):**
  If your host doesn't allow changing `public_html`'s document root for your primary domain:
  ```bash
  cd ~
  mv public_html public_html_backup
  ln -s ~/chefchaouen-info/public ~/public_html
  ```

---

## Part 2: Deploying Future Updates (Local → GitHub → cPanel)

### Step 1: On Your Local Machine
Push any new changes to GitHub:
```bash
git add .
git commit -m "Describe your update"
git push origin main
```

### Step 2: In cPanel Terminal
Run the included deployment script inside `~/chefchaouen-info`:
```bash
cd ~/chefchaouen-info
./deploy.sh
```

`deploy.sh` automatically runs:
1. `git pull origin main`
2. `composer install --no-dev --optimize-autoloader`
3. Creates `public/storage` symlink if missing & fixes `storage/` / `bootstrap/cache/` permissions
4. `php artisan migrate --force`
5. Clears and rebuilds `config:cache`, `route:cache`, and `view:cache`

*(Tip: Do **not** run `--seed` on future updates unless you specifically want to reset default seeded records; `deploy.sh` intentionally only runs `migrate --force` so your live admin changes and uploaded icons/listings remain intact.)*
