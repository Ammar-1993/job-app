# Job Vacancies Platform (Job App)

<div align="center">

![Job Application Platform Interface](/root/.gemini/antigravity/brain/6bc70a5e-69d1-4f0a-a01b-289b6a7676b2/job_app_mockup_1764978848937.png)

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)](https://alpinejs.dev)

</div>


- [Live Demo](https://job-app-mbe9k.ondigitalocean.app/)

---

## 📋 Table of Contents

- [Introduction](#-introduction)
- [Key Features](#-key-features)
- [Project Interfaces](#-project-interfaces)
- [Project Structure](#-project-structure)
- [System Requirements](#-system-requirements)
- [Installation & Setup](#-installation--setup)
- [Technologies Used](#-technologies-used)
- [Contribution](#-contribution)
- [Common Issues](#-common-issues)
- [Support](#-support)

---

## 🚀 Introduction

**Job App** is the public-facing portal of the Job Vacancies Platform, designed to provide a seamless and modern experience for job seekers. It simplifies the process of finding and applying for jobs while leveraging AI for smarter application processing.

The system is built to provide:
- **Simplicity**: A clean, distraction-free interface for browsing jobs.
- **Speed**: Optimized performance for quick searches and applications.
- **Intelligence**: Integrated AI tools for resume parsing and analysis.

---

## ✨ Key Features

This platform offers a user-centric set of tools for candidates:

### 🧑‍💼 Candidate Experience
- **Smart Job Search**: Filter vacancies by category, location, and type.
- **Seamless Application**: Easy-to-use form for submitting applications.
- **Resume Parsing**: Automatically extracts details from PDF resumes using AI.
- **Responsive Design**: Fully functional on mobile and desktop devices.
- **Multilingual Support**: Ready for localization to reach a wider audience.

---

## 🖼 Project Interfaces

The interface is designed with a focus on **accessibility** and **modern aesthetics**.

> *Note: The screenshot above is a conceptual mockup. The actual interface may vary slightly as the project evolves.*

### Main Job Portal
- **Hero Search**: Prominent search bar for instant access to jobs.
- **Job Cards**: Clear, consistent display of job details and company branding.
- **Instant Apply**: Quick access to application forms directly from the listing.

---

## 📂 Project Structure

The project follows a standard scalable **Laravel** architecture:

```
job-app/
├── app/
│   ├── Http/Controllers/    # Request handling logic (Jobs, Applications)
│   ├── Models/              # Eloquent models (Job, Application)
│   └── Services/            # Business logic (ResumeParsingService)
├── resources/
│   ├── css/                 # Tailwind CSS entry points
│   ├── js/                  # Alpine.js logic and scripts
│   └── views/               # Blade templates for the UI
├── routes/
│   ├── web.php              # Web routes definition
├── database/
│   ├── migrations/          # Database schema definitions
│   └── seeders/             # Dummy data generators
└── public/                  # Publicly accessible assets
```

---

## 💻 System Requirements

Before setting up the project, ensure your environment meets the following prerequisites:

- **PHP**: >= 8.2
- **Composer**: Latest version
- **Node.js**: >= 18.x & **NPM**
- **Database**: MySQL 8.0+ or MariaDB 10+
- **Web Server**: Nginx or Apache (or Laravel Sail/Valet)

---

## ⚙️ Installation & Setup

Follow these steps to get the project running locally.

### 1. Clone the Repository
```bash
git clone https://github.com/your-username/job-app.git
cd job-app
```

### 2. Install Dependencies
Install PHP and Node.js dependencies:
```bash
composer install
npm install
```

### 3. Environment Configuration
Copy the example environment file and configure your database and API keys:
```bash
cp .env.example .env
nano .env
```
*Update `DB_DATABASE`, `OPENAI_API_KEY`, and `AWS_ACCESS_KEY_ID` (if using S3).*

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Database Setup
Run migrations to set up the schema:
```bash
php artisan migrate
```

### 6. Build Assets
Compile the frontend assets:
```bash
npm run build
```

### 7. Run the Application
Start the local development server:
```bash
php artisan serve
```
Visit `http://localhost:8000` in your browser.

---

## 🛠 Technologies Used

We chose this stack for its **reliability**, **performance**, and **innovation**.

| Technology | Purpose |
|------------|---------|
| **Laravel 12** | Robust PHP framework for backend logic and routing. |
| **Tailwind CSS** | Utility-first CSS framework for rapid, custom UI design. |
| **Alpine.js** | Lightweight JavaScript framework for interactive frontend components. |
| **OpenAI API** | AI-powered resume parsing and analysis. |
| **Spatie PDF-to-Text** | Efficient extraction of text from uploaded documents. |
| **Vite** | Next-generation frontend tooling for fast builds. |

---

## 🤝 Contribution

We welcome contributions! Please follow these steps to participate:

1. **Fork** the repository.
2. **Create a Branch** for your feature (`git checkout -b feature/AmazingFeature`).
3. **Commit** your changes (`git commit -m 'Add some AmazingFeature'`).
4. **Push** to the branch (`git push origin feature/AmazingFeature`).
5. **Open a Pull Request**.

Please ensure your code follows the project's coding standards (PSR-12).

---

## ❓ Common Issues

### 1. Permission Denied (Storage)
If you encounter permission errors:
```bash
chmod -R 775 storage bootstrap/cache
```

### 2. Database Connection Refused
- Ensure your database server is running.
- Verify credentials in `.env`.

### 3. Vite Manifest Not Found
Run `npm run build` to generate the manifest file.

---

## 💡 Feedback & Tips

- **Security**: Never commit your `.env` file or expose your API keys.
- **Performance**: Use `php artisan route:cache` and `config:cache` in production.
- **Issues**: Report bugs via the GitHub Issues tab.

---

<p align="center">Developed by ❤️ Engineer Ammar Al-Najjar</p>
