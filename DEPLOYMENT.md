# PitWall Deployment Guide

## Railway + Supabase Deployment

### Prerequisites
- GitHub account
- Railway account (sign up at [railway.app](https://railway.app))
- Supabase account (sign up at [supabase.com](https://supabase.com))

### Step 1: Setup Supabase Database

1. Go to [supabase.com](https://supabase.com) and create a new project
2. Wait for database to be provisioned (2-3 minutes)
3. Go to **Project Settings** → **Database**
4. Copy the **Connection String** (URI format)
   - Example: `postgresql://postgres:[YOUR-PASSWORD]@db.xxx.supabase.co:5432/postgres`
5. Note down:
   - Host: `db.xxx.supabase.co`
   - Database: `postgres`
   - User: `postgres`
   - Password: (your password)
   - Port: `5432`

### Step 2: Deploy to Railway

1. Go to [railway.app](https://railway.app) and sign in with GitHub
2. Click **New Project** → **Deploy from GitHub repo**
3. Select your `F1-pitwall-data` repository
4. Railway will automatically detect Laravel and start deployment

### Step 3: Configure Environment Variables

In Railway, go to your project → **Variables** tab and add:

```env
# Application
APP_NAME=PitWall
APP_ENV=production
APP_KEY=              # Will be generated - see below
APP_DEBUG=false
APP_URL=https://your-app.up.railway.app

# Database (from Supabase)
DB_CONNECTION=pgsql
DB_HOST=db.xxx.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=your-supabase-password
DB_SSLMODE=require

# Session & Cache (database-backed for free tier)
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# OpenF1 API
OPENF1_BASE_URL=https://api.openf1.org/v1
OPENF1_CACHE_TTL=300
OPENF1_RATE_LIMIT_SLEEP_US=350000
```

### Step 4: Generate APP_KEY

In Railway, go to your project → **Settings** → **Deploy** and add a custom start command temporarily:

```bash
php artisan key:generate --show
```

Copy the generated key, then:
1. Add it to environment variables as `APP_KEY`
2. Remove the custom start command (Railway will use Procfile)
3. Redeploy

### Step 5: Run Migrations

After deployment is successful:

1. Go to Railway project → Click on your service
2. Go to **Settings** → **Deploy** → Add one-time deploy command:

```bash
php artisan migrate --force
```

3. Trigger a new deployment or run via Railway CLI:

```bash
railway run php artisan migrate --force
```

### Step 6: Sync F1 Data

SSH into Railway or use Railway CLI to sync initial data:

```bash
# Sync current season meetings and sessions
railway run php artisan openf1:sync

# Or sync specific data types
railway run php artisan openf1:sync meetings
railway run php artisan openf1:sync sessions
railway run php artisan openf1:sync drivers --session=11280
railway run php artisan openf1:sync laps --session=11280
```

### Step 7: Access Your Application

Your application will be available at:
```
https://your-app.up.railway.app
```

Railway will provide the URL in the deployment details.

---

## Alternative: Railway CLI Deployment

### Install Railway CLI

```bash
# macOS
brew install railway

# Linux/Windows
npm install -g @railway/cli
```

### Deploy via CLI

```bash
# Login to Railway
railway login

# Link to your project (or create new)
railway link

# Add environment variables
railway variables set APP_NAME=PitWall
railway variables set APP_ENV=production
railway variables set DB_CONNECTION=pgsql
railway variables set DB_HOST=db.xxx.supabase.co
# ... add all other variables

# Generate APP_KEY
railway run php artisan key:generate

# Deploy
railway up

# Run migrations
railway run php artisan migrate --force

# Sync F1 data
railway run php artisan openf1:sync
```

---

## Queue Worker (Optional)

For background job processing, Railway can run a separate worker service:

1. In Railway, add a new service to your project
2. Link it to the same GitHub repository
3. Set the start command to:
```bash
php artisan queue:work --tries=3 --timeout=90
```
4. Add the same environment variables as the web service

---

## Troubleshooting

### Database Connection Issues

If you get database connection errors:
1. Verify Supabase credentials are correct
2. Ensure `DB_SSLMODE=require` is set
3. Check that your Supabase project is not paused (free tier pauses after inactivity)

### Migration Errors

If migrations fail:
```bash
railway run php artisan migrate:fresh --force
```

### Clear Cache

If you encounter caching issues:
```bash
railway run php artisan config:clear
railway run php artisan cache:clear
railway run php artisan route:clear
railway run php artisan view:clear
```

### View Logs

```bash
railway logs
```

---

## Cost Breakdown

- **Railway**: $5/month free credit (enough for small apps)
- **Supabase**: Free tier (500MB database, 2GB bandwidth)
- **Total**: $0/month for hobby projects

Free tier limitations:
- Railway: $5 credit = ~500 hours of execution time
- Supabase: Pauses after 1 week of inactivity (restarts automatically)

---

## Production Checklist

- [ ] APP_DEBUG set to `false`
- [ ] APP_ENV set to `production`
- [ ] Strong APP_KEY generated
- [ ] Database credentials configured
- [ ] SSL enabled for database (DB_SSLMODE=require)
- [ ] Migrations run successfully
- [ ] Initial F1 data synced
- [ ] Application URL updated in APP_URL
- [ ] Test all pages load correctly
- [ ] Test API endpoints respond correctly

---

## Updating the Application

When you push changes to GitHub:
1. Railway automatically detects the push
2. Builds and deploys the new version
3. Zero-downtime deployment

Manual redeploy:
```bash
railway up
```

---

## Scheduled Data Sync

To keep F1 data up-to-date, set up a cron job or use Railway's scheduled tasks:

1. Install [Railway Cron](https://docs.railway.app/reference/cron-jobs) or use external service like [cron-job.org](https://cron-job.org)
2. Schedule endpoint calls:

```bash
# Daily sync at 00:00 UTC
curl -X POST https://your-app.up.railway.app/api/sync/meetings
curl -X POST https://your-app.up.railway.app/api/sync/sessions
```

Or run via Railway CLI scheduled task:
```bash
0 0 * * * railway run php artisan openf1:sync
```
