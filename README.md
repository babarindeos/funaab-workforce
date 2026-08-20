
# FUNAAB Workforce Staff Portal

A Laravel-based Human Resource Management System built for the Federal University of Agriculture, Abeokuta (FUNAAB), designed to digitize staff administration — from leave requests and staff records to performance evaluation and career progression.

## 📋 Overview

The Workforce Portal centralizes core HR operations for FUNAAB staff across **two dedicated portals**, replacing manual, paper-based processes with structured digital workflows.

- **Admin Portal** — For HR administrators to manage staff records, departments/divisions, leave, progression, and APER evaluations across the entire university (currently tracking 1,247+ staff across 48 departments and 9 colleges)
- **Staff Portal** — A self-service space for individual staff members to view their profile, apply for leave, submit APER forms, track promotion status, manage documents, and browse colleagues in their department ("Circles")

**Core modules:**

- **Staff Management** (Admin) — Comprehensive staff records with tabbed sections (Personal Info, Employment, Qualifications, Next of Kin, Contact, Deployments, Off Employments, Profile)
- **Leave Management** — Digital leave request, approval, and tracking workflow, with balances and pending status visible on both portals
- **Progression Module** — Tracks staff career progression and advancement (e.g. grade level step increases) through the university's structure
- **APER (Annual Performance Evaluation Report)** — Multi-step performance evaluation covering Junior and Senior Non-Teaching staff, with role-based assessment stages (Supervisor → HOD → Chairman DAPC) and automated scoring logic
- **Circles** — A directory of colleagues within a staff member's own department
- **Reference Data Management** — Configurable reference data (departments, divisions, cadres, grade levels, etc.) that power dropdowns and validation across the system
- **Analytics** — Admin-facing reporting on staff distribution, categories, and activity

## ✨ Key Features

- Two dedicated portals (Admin / Staff) with role-based access control and a quick-switch toggle between them
- UUID-based route model binding for secure, non-enumerable URLs
- Multi-step forms with save-and-resume support (APER evaluations)
- Tabbed staff detail pages for organized record-keeping
- Dashboard summaries (total staff, on leave, pending promotions, staff-by-category breakdowns) for admins; personalized quick actions for staff
- Class-based Blade components for consistent, reusable UI
- Clean `Admin_` / `Staff_` controller separation for maintainability

## 🖼️ Screenshots

<details>
<summary><b>🔐 Staff Login</b> — click to expand</summary>
<br>

![Staff Login](Workforce/Screenshot%202026-08-20%20120108.jpg)

</details>

<details>
<summary><b>📊 Admin Dashboard</b> — click to expand</summary>
<br>

![Staff Self-Service Dashboard](Workforce/Screenshot%202026-08-20%20120323.jpg)

</details>

<details>
<summary><b>👥 Staff Management (Admin)</b> — click to expand</summary>
<br>

![Staff Management](docs/screenshots/staff-management.jpg)

</details>

<details>
<summary><b>🏠 Staff Self-Service Dashboard</b> — click to expand</summary>
<br>

![Staff Self-Service Dashboard](docs/screenshots/staff-portal-dashboard.jpg)

</details>

<details>
<summary><b>👤 Circles (Department Directory)</b> — click to expand</summary>
<br>

![Circles](docs/screenshots/staff-circles.jpg)

</details>

> Screenshots live in `docs/screenshots/`. Add new ones there and reference them the same way as above.

## 🛠️ Tech Stack

- **Backend:** Laravel (PHP)
- **Frontend:** TailwindCSS, Alpine.js, Blade
- **Database:** MySQL
- **Local Environment:** XAMPP

## 🚀 Development Workflow

### Prerequisites

- PHP >= 8.1
- Composer
- Node.js & npm
- MySQL (via XAMPP or standalone)

### Local Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/babarindeos/funaab-workforce.git
   cd funaab-workforce
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install frontend dependencies**
   ```bash
   npm install
   ```

4. **Set up environment file**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Update `.env` with your local database credentials (e.g. XAMPP MySQL: `DB_HOST=127.0.0.1`, `DB_DATABASE=funaab_workforce`, `DB_USERNAME=root`).

5. **Run migrations and seeders**
   ```bash
   php artisan migrate --seed
   ```

6. **Compile frontend assets**
   ```bash
   npm run dev
   ```

7. **Serve the application**
   ```bash
   php artisan serve
   ```
   The app will be available at `http://127.0.0.1:8000`.

### Git Branch Workflow

- `main` — production-ready, stable code
- `develop` — active integration branch for ongoing work
- `feature/<name>` — individual features or modules (e.g. `feature/aper-scoring`)
- `fix/<name>` — bug fixes (e.g. `fix/leave-approval-validation`)

**Typical flow:**

```bash
# Start a new feature off develop
git checkout develop
git pull origin develop
git checkout -b feature/your-feature-name

# Work, commit, and push
git add .
git commit -m "Add: short description of change"
git push origin feature/your-feature-name

# Open a Pull Request into develop for review
```

Commit messages follow a simple prefix convention: `Add:`, `Fix:`, `Update:`, `Refactor:`.

## 📄 License

_Add license information here._

## 🤝 Contributing

_Add contribution guidelines here, if this project accepts external contributions._
