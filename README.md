# 📝 Todo List Web App

A simple, clean, and interactive **Todo List** application built using **Laravel**, **Bootstrap 5**, **jQuery**, and **AJAX**. It allows users to add, complete, and delete tasks dynamically with a real-time user interface experience.

---

## 🔥 Features

- ✅ Add tasks dynamically with AJAX
- ✅ Mark tasks as complete/incomplete
- ✅ Toggle visibility of completed tasks
- ✅ Delete tasks with confirmation modal
- ✅ Responsive and clean UI with Bootstrap
- ✅ Real-time task count

---

## 🛠️ Tech Stack

- **Backend**: Laravel 9+
- **Frontend**: Bootstrap 5, jQuery, AJAX
- **Templating**: Blade
- **Database**: MySQL or SQLite (based on your `.env`)
- **Icons**: Font Awesome 6

---

---

## 🚀 Getting Started

Follow these steps to get a local copy up and running.

### Prerequisites

- PHP 8.x
- Composer
- MySQL or SQLite
- Node.js & npm (for Laravel Mix if used)

### Installation

```bash
# Clone the repository
git clone https://github.com/yourusername/todo-list-app.git

# Navigate to the project directory
cd todo-list-app

# Install PHP dependencies
composer install

# Copy .env file and generate app key
cp .env.example .env
php artisan key:generate

# Set up your database configuration in .env
# Then run migrations
php artisan migrate

# (Optional) Seed the database
php artisan db:seed

# Run the app
php artisan serve
