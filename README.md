# Dubai Garments AI

## Project Overview
Dubai Garments AI is a garment sales automation platform for bulk and custom clothing orders. It combines customer quote requests, AI-assisted lead processing, sales pipeline management, quote generation, and automated follow-ups in one Laravel application.

## Development Stack (HostGator Compatible)
### Backend
- Laravel 11
- PHP 8.3

### Frontend
- Blade + Alpine.js
- Or Blade + Vue
- TailwindCSS

### Database
- MySQL (HostGator default)

### Queue / Jobs
- Laravel Queue with `database` driver

### Automation
- Laravel Scheduled Commands via cron

### AI
- OpenAI API integration

### Email
- SMTP (HostGator SMTP or SendGrid)

### PDF Generation
- DomPDF or Snappy

### File Storage
- Laravel Storage (`public` disk)

### Deployment
- Git or FTP upload
- cPanel cron jobs

## Why Laravel Fits This Project
Laravel includes all core building blocks needed for this system:
- Queues
- Job workers
- Scheduler
- Events
- API routes
- Auth system
- Blade frontend
- Mail system
- Storage system

This keeps the solution inside one framework without external workflow services.

## Hosting Architecture
Browser (Customer)

↓

Laravel Routes

↓

Controllers

↓

Service Layer

↓

Database

↓

AI API

↓

Laravel Jobs

↓

Email / Automation

## Customer Side Application
Customer storefront pages:
- Home
- Products
- Product Details
- Request Quote
- Customer Portal
- Contact

## Customer Flow
1. Customer visits the site.
2. Customer browses garments (hoodies, t-shirts, caps, uniforms).
3. Customer opens a product page.
4. Customer clicks `Request Bulk Quote`.
5. Customer submits form data:
- Name
- Company
- Email
- Phone
- Quantity
- Logo upload
- Message
6. Customer request is submitted for sales processing.

## Backend Lead Processing Structure
Customer submits quote request

↓

Laravel Controller (`QuoteRequestController`)

↓

Save lead in database (`status = NEW`)

↓

Fire event (`LeadCreated`)

↓

Queue job (`ProcessLeadAI`)

↓

AI extraction

↓

Lead scoring

↓

Create deal

↓

Notify sales

↓

Start automation sequence

## AI Processing Architecture
Primary service: `LeadAIService`

Responsibilities:
- Extract data from customer message
- Identify product type
- Detect quantity
- Detect urgency
- Estimate complexity

Example input:
`We need 500 custom hoodies for conference.`

Example structured output:
- Product: hoodie
- Quantity: 500
- Urgency: event
- LeadScore: 82

## Sales Dashboard
Admin routes:
- `/admin/dashboard`
- `/admin/leads`
- `/admin/deals`
- `/admin/quotes`
- `/admin/automation`
- `/admin/analytics`

### Dashboard Features
- Total leads
- Hot leads
- Quotes sent
- Deals won
- Lead pipeline chart
- Conversion rate chart
- Sales performance chart

### Leads Module
Columns:
- Lead name
- Company
- Score
- Status
- Created date

Actions:
- View
- Qualify
- Generate quote

### Lead Detail Page
Shows:
- Customer message
- AI extracted data
- Product
- Quantity
- Urgency
- Lead score
- Activity timeline

Actions:
- Create deal
- Generate quote
- Send message

### Deals Module
Pipeline stages:
- New
- Qualified
- Quoted
- Negotiation
- Won
- Lost

Deals move between stages as sales progress.

### Quote Module
- Select products
- Add pricing
- Generate quote PDF
- Send quote email

## Automation Engine
Shared hosting automation is powered by:
- Laravel Jobs
- Laravel Scheduler
- Cron jobs

## Automation Workflows
### Workflow 1: New Lead Processing
Trigger:
- `LeadCreated` event

Job:
- `ProcessLeadAI`

Steps:
- Call OpenAI
- Extract data
- Score lead
- Update lead
- Create deal

### Workflow 2: Quote Sent Follow-up
Trigger:
- `QuoteSent` event

Sequence:
- Day 2 email
- Day 5 reminder
- Day 10 final message

### Workflow 3: Follow-up Runner
- Laravel scheduled command
- Runs every hour
- Checks pending follow-ups
- Sends emails

### Workflow 4: Customer Reply Detection
- Incoming email webhook
- On reply detection:
- Pause automation
- Notify admin

### Workflow 5: Daily Sales Digest
- Daily cron trigger
- Generates stats
- Sends summary by email or Slack

## Database Structure
### `users`
- id
- name
- email
- role

### `leads`
- id
- source
- customer_name
- email
- phone
- company
- message
- ai_score
- classification
- status
- created_at

### `deals`
- id
- lead_id
- stage
- priority
- value_estimate

### `quotes`
- id
- deal_id
- quote_number
- items_json
- total_price
- status

### `activities`
- id
- deal_id
- type
- message
- created_at

### `followups`
- id
- deal_id
- step
- next_run
- status

### `communications`
- id
- deal_id
- direction
- message
- status

### `automation_runs`
- id
- workflow
- status
- logs

## Frontend Structure
For lightweight shared hosting compatibility:
- Blade templates
- TailwindCSS
- Alpine.js

Everything runs inside Laravel without separate Node runtime services.

## File Uploads
Customer logo uploads are stored in:
- `storage/app/public/uploads`

Served from:
- `public/storage`

## Quote PDF Generation
Generated quote/proposal includes:
- Product
- Quantity
- Pricing
- Timeline
- Terms

PDF is downloadable and can be sent via email.

## Deployment on HostGator
- Upload via Git or FTP
- Configure environment variables
- Create MySQL database in cPanel
- Run migrations
- Set storage permissions
- Configure cron job

### Cron Command
Run every minute:
`php artisan schedule:run`

This triggers scheduled automation tasks.

## Monitoring
Logs location:
- `storage/logs`

Admin monitoring scope:
- Automation failures
- Email errors
- AI errors

## Final Production Architecture
Customer Browser

↓

Apache Server

↓

Laravel Application

↓

MySQL Database

↓

OpenAI API

↓

Laravel Jobs

↓

Email System

## Why This Works on Shared Hosting
- No background servers required
- No Docker required
- No Redis required
- No Node servers required

The system runs with:
- PHP
- MySQL
- Cron

## Project Demonstration Value
This project demonstrates a complete business-ready automation system for:
- AI sales automation
- CRM automation
- Quote automation
- Lead scoring
- Workflow automation
- Full SaaS-style architecture in a real garment sales use case
