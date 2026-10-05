# Job Vacancies Platform — Job App & Autonomous AI Job Hunter

<div align="center">

![Job Application Platform Interface](https://ammar-1993.github.io/portfolio/images/portfolio/job-app-1.webp)

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)](https://alpinejs.dev)
[![OpenAI](https://img.shields.io/badge/OpenAI-Vector_Embeddings_%26_GPT--4o-412991?style=for-the-badge&logo=openai&logoColor=white)](https://openai.com)
[![FrankenPHP](https://img.shields.io/badge/FrankenPHP-Caddy_Engine-00ADD8?style=for-the-badge&logo=caddy&logoColor=white)](https://frankenphp.dev)

</div>

- **Live Production URL**: [hireme-platform.online](https://hireme-platform.online/)

---

## 📋 Table of Contents

- [Introduction](#-introduction)
- [Key Features](#-key-features)
- [Autonomous AI Job Hunter (`/hunter`)](#-autonomous-ai-job-hunter-hunter)
- [Hybrid Matching Engine](#-hybrid-matching-engine)
- [Developer CLI Commands](#-developer-cli-commands)
- [Project Interfaces](#-project-interfaces)
- [Architecture & Directory Structure](#-architecture--directory-structure)
- [System Requirements](#-system-requirements)
- [Installation & Local Setup](#-installation--local-setup)
- [Automated CI/CD Deployment](#-automated-cicd-deployment)
- [Technologies Used](#-technologies-used)
- [Contribution](#-contribution)
- [Support & Security](#-support--security)

---

## 🚀 Introduction

**Job App** is the candidate portal and personal career automation engine of the **Job Vacancies Platform**. It combines an intuitive public job marketplace with an **Autonomous AI Job Hunter agent** that continuously analyzes external job openings, evaluates candidate-job fit using vector embeddings, and generates hyper-personalized application materials with a single click.

---

## ✨ Key Features

### 🧑‍💼 1. Candidate Portal & Job Marketplace
- **Smart Vacancy Discovery**: Filter jobs by category, location, and workplace mode (Remote, Hybrid, On-site).
- **Automated Resume Parsing**: Extracts structured skills, experience, and education from PDF resumes using AI.
- **Dynamic Application Workflow**: Guided multi-step application submission with immediate feedback.
- **My Applications Dashboard**: Real-time status tracking for all submitted job applications.

### 🎯 2. Autonomous AI Job Hunter (`/hunter`)
- **Direct Feed of Ingested Global & Gulf Roles**: Browses jobs aggregated from Greenhouse boards and WeWorkRemotely.
- **Circular SVG Match Gauge**: Visual real-time indicator of candidate-to-job compatibility (0% - 100%).
- **One-Click Tailored AI Application Generation**: Produces a customized Cover Letter, Key Selling Points, and Email Subject Line tailored specifically to the job description and candidate resume.
- **Idempotent Storage**: Generated applications are saved immediately as `is_personal = true` drafts in the database, avoiding redundant OpenAI API calls.
- **Interactive Copy Modal**: One-click clipboard copy for cover letters and direct external application URLs.

---

## 🧠 Hybrid Matching Engine

The matching score is computed through `SkillMatcher` via a multi-dimensional formula:

$$\text{Composite Score} = (0.70 \times \text{Cosine Similarity}) + (0.30 \times \text{Keyword Overlap}) - \text{Seniority Penalty} - \text{Stack Penalty}$$

1. **Semantic Vector Similarity (70%)**: Vector embeddings generated via OpenAI (`text-embedding-3-small`) measuring conceptual and contextual fit.
2. **Strict Skill Keyword Matching (30%)**: Word-boundary matching across core languages, frameworks, and technical toolsets.
3. **Experience & Seniority Calibration**: Detects Junior, Mid, Senior, and Lead expectations, flagging gaps or overqualification.
4. **Stack Incompatibility Gate**: Detects and penalizes fundamental technology stack mismatches (e.g., C++ embedded, legacy Java, mobile-only requirements).

---

## 💻 Developer CLI Commands

Manage the autonomous job hunter and AI matching directly from the command line:

```bash
# 1. Match a candidate resume against a job vacancy and view AI output
php artisan job:match {job_id} {resume_id}

# 2. Match, generate materials, and save directly to the Hunter pipeline
php artisan job:match {job_id} {resume_id} --save --channel=LinkedIn --notes="High priority target"

# 3. List all personal job hunter applications and follow-up timelines
php artisan hunter:list

# 4. Update application status, append interview logs, and set next follow-up
php artisan hunter:status {application_id} interviewing --notes="Screening interview scheduled with Hiring Manager" --follow-up="2026-10-15"
```

---

## 🖼 Project Interfaces

The user interface is designed using **Tailwind CSS** and **Alpine.js** with full Dark Mode support and responsive layouts:

| Interface | Description | Preview |
| :--- | :--- | :--- |
| **Landing Page** | High-conversion entry point with prominent search and categories | ![Landing Page](https://ammar-1993.github.io/portfolio/images/portfolio/job-app-1.webp) |
| **Job Hunter Hub** | Dedicated candidate review dashboard with circular match gauges | `/hunter` |
| **Candidate Dashboard** | Unified dashboard displaying active searches and applications | ![Dashboard](https://ammar-1993.github.io/portfolio/images/portfolio/job-app-4.webp) |
| **Job Details View** | Comprehensive role specifications with salary and company profiles | ![Details](https://ammar-1993.github.io/portfolio/images/portfolio/job-app-5.webp) |
| **Application Tracker** | Personal status board tracking review stages and feedback | ![Applications](https://ammar-1993.github.io/portfolio/images/portfolio/job-app-8.webp) |

---

## 📂 Architecture & Directory Structure

```
job-app/
├── app/
│   ├── Console/Commands/
│   │   ├── MatchJobCommand.php     # CLI matching and AI application generator (job:match)
│   │   ├── HunterListCommand.php    # CLI personal applications viewer (hunter:list)
│   │   └── HunterStatusCommand.php  # CLI stage and notes updater (hunter:status)
│   ├── Http/Controllers/
│   │   ├── HunterController.php     # Job Hunter review dashboard & one-click generation
│   │   ├── JobApplicationController.php # Candidate applications handler
│   │   └── JobVacancyController.php # Public vacancy browsing and search
│   ├── Models/                      # Eloquent models (extended from job-shared)
│   ├── Observers/                   # Resume and vacancy lifecycle observers
│   ├── Services/
│   │   └── ResumeAnalysisService.php # AI parsing, embeddings, and tailored application generation
│   └── Support/
│       ├── JobFilter.php            # Negative keyword filter for roles
│       └── SkillMatcher.php         # Hybrid matching calculation engine
├── resources/
│   ├── css/app.css                  # Tailwind CSS styling tokens
│   ├── js/app.js                    # Alpine.js logic
│   └── views/
│       ├── hunter/index.blade.php   # Autonomous Job Hunter review dashboard & modal
│       ├── job-applications/        # Application tracking and details views
│       └── vacancies/               # Public job catalog templates
├── routes/
│   └── web.php                      # Application routes
├── .github/workflows/
│   └── deploy.yml                   # Smart zero-downtime deployment workflow
└── Dockerfile                       # Multi-stage production container with FrankenPHP
```

---

## 💻 System Requirements

- **PHP**: >= 8.2 (extensions: `pdo_mysql`, `curl`, `mbstring`, `openssl`, `tokenizer`)
- **Composer**: >= 2.x
- **Node.js**: >= 18.x & **NPM**
- **Database**: MySQL 8.0+ or MariaDB 10+
- **PDF Parser**: `pdftotext` (poppler-utils)
- **OpenAI API Key**: For vector embeddings and application generation

---

## ⚙️ Installation & Local Setup

### 1. Clone the Repository
```bash
git clone https://github.com/Ammar-1993/job-app.git
cd job-app
```

### 2. Install PHP & Node Dependencies
```bash
composer install
npm install
```

### 3. Configure Environment
```bash
cp .env.example .env
nano .env
```
Ensure the following variables are configured:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jobs_db
DB_USERNAME=root
DB_PASSWORD=your_password

OPENAI_API_KEY=sk-proj-...
```

### 4. Generate Application Key & Build Assets
```bash
php artisan key:generate
npm run build
```

### 5. Launch the Server
```bash
php artisan serve
```
Visit `http://localhost:8000` (or `http://localhost:8080` if running via Docker).

---

## 🚀 Automated CI/CD Deployment

The repository includes a modern GitHub Actions deployment pipeline ([`deploy.yml`](.github/workflows/deploy.yml)):

```
Push to main
     │
     ▼
[Pre-sync job-shared] ──► Ensures shared classes are synchronized
     │
     ▼
[Change Impact Check] ──► Uses git diff to inspect modified files
     ├── Dependency/Asset Changes? ──► Rebuilds Docker container with --no-deps
     └── PHP/Blade/Route Changes?  ──► Instant In-Place Hot Sync (< 3 seconds)
     │
     ▼
[Cache Optimization]  ──► optimize:clear && optimize && queue:restart
     │
     ▼
[Zero-Downtime Live]  ──► Changes active on production instantly
```

---

## 🛠 Technologies Used

| Technology | Purpose |
| :--- | :--- |
| **Laravel 12** | Core PHP enterprise backend framework. |
| **OpenAI API** | GPT-4o application tailoring and `text-embedding-3-small` vector embeddings. |
| **FrankenPHP / Caddy** | High-performance application server with HTTP/3 and automated HTTPS. |
| **Tailwind CSS 3.x** | Modern styling system with Dark Mode support. |
| **Alpine.js** | Lightweight declarative reactive framework for UI modals and interactions. |
| **Job Shared Library** | Centralized domain logic, enums, and models. |
| **Spatie PDF-to-Text** | Binary text extraction from uploaded resume files. |
| **Vite** | Frontend module bundler and asset pipeline. |

---

<p align="center">Developed with ❤️ by Eng. Ammar Al-Najjar</p>
