<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Laravel Starter

**Laravel Starter** is a comprehensive boilerplate designed for building modern web applications using the following technologies:

- **Laravel**: A powerful backend framework.
- **Vue.js**: A progressive JavaScript framework for building user interfaces.
- **Inertia.js**: A modern stack for building single-page apps using classic server-side routing.
- **Tailwind CSS**: A utility-first CSS framework for rapid UI development.
- **Shadcn-vue**: UI components library for Vue.js.

### Features

- **Pre-configured Authentication**: Get started with user authentication out of the box.
- **Responsive Design**: Built with Tailwind CSS to ensure your application looks great on all devices.
- **Modern Frontend**: Leverages Vue.js and Inertia.js for a seamless single-page application experience.
- **UI Components**: Includes Shadcn-vue for a cohesive and attractive user interface.

### Installation

Follow these steps to get your development environment set up:

1. **Clone the repository**:
   ```bash
   git clone https://github.com/yourusername/laravel-starter.git
    ```
2. **Navigate to the project directory**:
   ```bash
    cd laravel-starter
    ```
3. **Install dependencies**:
   ```bash
   composer install
   npm install
    ```

4. **Set up the environment file**:
    ```bash
    cp .env.example .env
    php artisan key:generate
     ```
5. **Run the migrations**:
   ```bash
   php artisan migrate
    ```
6. **Start the development server**:
    ```bash
    php artisan serve
    npm run dev
     ```
7. **Visit the application**:
   Visit `http://localhost:8000` in your browser.

### Contributing
We welcome contributions to enhance this starter kit! Please fork the repository and submit pull requests.

