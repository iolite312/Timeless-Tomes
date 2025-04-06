# Timeless Tomes

A modern e-commerce platform for buying and selling books, built with Laravel and Nuxt.js.

## Features

- User authentication and authorization
- Book listing and search functionality
- Shopping cart and checkout process
- Seller account management
- Admin dashboard
- Stripe payment integration
- Meilisearch for fast and relevant search results

## Prerequisites

- Docker and Docker Compose
- Node.js (v16 or higher)
- PHP 8.2
- Composer
- pnpm or npm

## Getting Started

1. Clone the repository:

```bash
git clone [repository-url]
cd Timeless-Tomes
```

2. Copy the environment file:

```bash
cp .env.example .env
```

3. Configure your environment variables in `.env`:

### Database Configuration

```env
MYSQL_ROOT_PASSWORD=your_secure_password
MYSQL_USER=your_mysql_user
MYSQL_PASSWORD=your_mysql_password
MYSQL_DATABASE=eindopdracht
MYSQL_HOST=mysql
MYSQL_PORT=3306
PHP_DB_HOST=mysql
```

### JWT Configuration

```env
JWT_SECRET=your_base64_encoded_secret_longer_than_256_bits
JWT_ISSUER=localhost
JWT_AUDIENCE=localhost
```

### API Configuration

```env
BASE_URL=http://localhost:8080
```

### Stripe Configuration

```env
STRIPE_SECRET_KEY=your_stripe_secret_key
STRIPE_PUBLIC_KEY=your_stripe_public_key
STRIPE_WEBHOOK_SECRET=your_stripe_webhook_secret
```

## Setting up Stripe Webhooks

1. Log in to your Stripe Dashboard
2. Go to Developers → Webhooks
3. Click "Add endpoint"
4. Set the endpoint URL to: `https://your-domain.com/stripe/webhook`
5. Select the following events to listen for:
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
   - `checkout.session.completed`
6. Copy the "Signing secret" and add it to your `.env` as `STRIPE_WEBHOOK_SECRET`

For more information about Stripe webhooks, visit the [Stripe Webhook Documentation](https://stripe.com/docs/webhooks).

### Meilisearch Configuration

```env
MEILI_HOST_URL=your_meilisearch_host_url
MEILI_MASTER_KEY=your_meilisearch_master_key
MEILI_SEARCH_KEY=your_meilisearch_search_key
```

4. Start the Docker containers:

```bash
docker-compose up -d
```

5. Install Node.js dependencies:

```bash
cd vue
pnpm install or npm install
```

6. Start the development server:

```bash
# In the vue directory
pnpm dev or npm run dev
```

The application should now be running at:

- Frontend: http://localhost:3000
- Backend API: http://localhost:8080

## Accounts

```
admin: admin@gmail.com, 123456789
seller: seller@gmail.com, 123456789
```

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
