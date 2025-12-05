# Job Vacancies Platform (Job App)

![Laravel](https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-3.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)

## 1. Project Description

**Job App** is a modern, user-friendly job application portal designed to streamline the recruitment process. It serves as the public-facing interface where potential candidates can browse available job vacancies and submit their applications seamlessly.

### Objectives
- **Simplify Recruitment:** Provide a straightforward platform for candidates to apply for jobs.
- **Enhance User Experience:** Offer a clean, responsive, and intuitive interface.
- **Automate Processing:** Integrate with AI tools to assist in resume parsing and initial screening.

### Mechanism of Action
The application fetches job vacancies from the backend database and presents them to users. Candidates can view details, upload their resumes (PDF format), and submit applications. The system leverages **OpenAI** for intelligent processing and **Spatie PDF-to-Text** for extracting content from resumes, ensuring efficient data handling.

---

## 2. Project Structure

The project follows the standard Laravel directory structure, optimized for modularity and maintainability.

```
job-app/
├── app/
│   ├── Http/           # Controllers, Middleware, and Requests
│   ├── Models/         # Eloquent Models (Job, Application, etc.)
│   └── Services/       # Business logic (e.g., ResumeParsingService)
├── config/             # Application configuration files
├── database/           # Migrations, Factories, and Seeders
├── lang/               # Localization files (e.g., en/app.php)
├── public/             # Publicly accessible assets (images, build files)
├── resources/
│   ├── css/            # TailwindCSS entry points
│   ├── js/             # Alpine.js and other scripts
│   └── views/          # Blade templates (UI)
├── routes/             # Web and API route definitions
├── storage/            # Logs, compiled templates, and file uploads
└── tests/              # Feature and Unit tests
```

### Key Directories
- **`resources/views/job-vacancies`**: Contains the Blade templates for listing jobs and the application form.
- **`lang/en/app.php`**: Centralized localization file for easy text management.

---

## 3. Operating Requirements

Ensure your environment meets the following specifications before installation:

- **PHP**: Version 8.2 or higher
- **Composer**: Latest version
- **Node.js**: Version 18+ & **NPM**
- **Database**: MariaDB or MySQL
- **Web Server**: Nginx, Apache, or Caddy

### Required Extensions
- `BCMath` PHP Extension
- `Ctype` PHP Extension
- `Fileinfo` PHP Extension
- `JSON` PHP Extension
- `Mbstring` PHP Extension
- `OpenSSL` PHP Extension
- `PDO` PHP Extension
- `Tokenizer` PHP Extension
- `XML` PHP Extension

---

## 4. Installation & Commissioning

Follow these steps to set up the project from scratch.

### Step 1: Clone the Repository
```bash
git clone https://github.com/your-username/job-app.git
cd job-app
```

### Step 2: Install PHP Dependencies
```bash
composer install
```

### Step 3: Install Frontend Dependencies
```bash
npm install
```

### Step 4: Environment Configuration
Copy the example environment file and configure your database and API keys.
```bash
cp .env.example .env
```
Open `.env` and update the following:
- `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `OPENAI_API_KEY` (for AI features)
- `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY` (if using S3/DigitalOcean Spaces)

### Step 5: Generate Application Key
```bash
php artisan key:generate
```

### Step 6: Run Migrations
Create the necessary database tables.
```bash
php artisan migrate
```

### Step 7: Build Assets
Compile the TailwindCSS and JavaScript assets.
```bash
npm run build
```

### Step 8: Serve the Application
Start the local development server.
```bash
php artisan serve
```
Access the application at `http://localhost:8000`.

---

## 5. Technologies & Techniques

We utilize a modern stack to ensure performance, scalability, and developer happiness.

| Technology | Purpose | Why it was chosen |
| :--- | :--- | :--- |
| **Laravel 12** | Backend Framework | Provides a robust, expressive syntax and powerful ecosystem for rapid development. |
| **TailwindCSS** | Styling | Utility-first CSS framework that enables rapid UI development with a custom design system. |
| **Alpine.js** | Interactivity | Lightweight JavaScript framework for adding behavior to your markup without the overhead of React/Vue. |
| **OpenAI PHP** | AI Integration | seamless integration with OpenAI API for intelligent resume analysis features. |
| **Spatie PDF-to-Text** | Utility | Reliable extraction of text from PDF resumes for processing. |
| **Vite** | Build Tool | Next-generation frontend tooling for lightning-fast server start and HMR. |

---

## 6. How to Contribute

We welcome contributions! Please follow these steps:

1.  **Fork** the repository.
2.  Create a new branch (`git checkout -b feature/AmazingFeature`).
3.  Commit your changes (`git commit -m 'Add some AmazingFeature'`).
4.  Push to the branch (`git push origin feature/AmazingFeature`).
5.  Open a **Pull Request**.

### Coding Standards
- Follow **PSR-12** coding standards for PHP.
- Ensure all new features are accompanied by tests.

---

## 7. Features & Functionality

- **Job Listing**: Browse all available job openings with filtering options.
- **Detailed View**: View comprehensive job descriptions, requirements, and benefits.
- **Smart Application**: Apply for jobs with a streamlined form.
- **Resume Parsing**: Automatically extracts text from uploaded PDF resumes.
- **Multilingual Support**: Fully localized interface (default: English).
- **Responsive Design**: Optimized for mobile, tablet, and desktop devices.

---

## 8. Common Issues & Solutions

### Permission Denied for Storage
**Issue**: Error writing to `storage/logs` or `storage/framework`.
**Solution**:
```bash
chmod -R 775 storage bootstrap/cache
```

### Database Connection Refused
**Issue**: `SQLSTATE[HY000] [2002] Connection refused`.
**Solution**: Ensure your database server is running and the credentials in `.env` are correct. If using Docker, check the `DB_HOST`.

### Missing Vite Manifest
**Issue**: `Vite manifest not found`.
**Solution**: Run `npm run build` to generate the production assets.

---

## 9. Feedback & Tips

- **Security**: Never commit your `.env` file or expose your `APP_KEY` or `OPENAI_API_KEY`.
- **Optimization**: For production, always run `php artisan config:cache` and `php artisan route:cache` for better performance.
- **Feedback**: If you encounter bugs or have suggestions, please open an issue on the GitHub repository.

---

*Built with ❤️ by the Job App Team*
