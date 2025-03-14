# Parking Management API

## 📌 Project Overview
This project aims to develop a REST API for managing parking spaces. Users can search for available parking spots, make reservations, modify or cancel them, and track their reservation history. Administrators can manage parking locations and monitor parking statistics. The API is built with Laravel Sanctum for authentication.

## 🚀 Features

### User Features
- 🔒 **Authentication**: Users can authenticate using Laravel Sanctum.
- 🚗 **Search for Parking**: Users can search for available parking spots in a specific geographic area with real-time availability updates.
- 📅 **Reserve a Parking Spot**: Users can book a parking space for a specific time period.
- 🔄 **Modify Reservations**: Users can update their arrival/departure times or cancel their reservations.
- ❌ **Cancel Reservations**: Users can cancel reservations if needed.
- 📊 **View Reservation History**: Users can see past and current reservations.

### Admin Features
- 🔒 **Manage Parkings**: Admins can add, update, or delete parking locations.
- 🔍 **View Statistics**: Admins can monitor parking-related statistics.

### Developer Features
- 🧪 **Unit Testing**: Each feature is tested with unit tests.
- 📝 **API Documentation**: Endpoints are documented using tools like Postman or Swagger.

---

## ⚙️ Installation

### Prerequisites
- PHP 8.x
- Composer
- Laravel 10.x
- PostgreSQL or MySQL
- Node.js & npm (for frontend integration, if applicable)

### Steps to Set Up the Project
```sh
# Clone the repository
git clone https://github.com/asma828/Park
cd park

# Install dependencies
composer install

# Copy the environment file
cp .env.example .env

# Configure database settings in .env

# Generate application key
php artisan key:generate

# Run migrations and seed the database
php artisan migrate --seed

# Start the development server
php artisan serve
```

---

## 🔑 Authentication
The API uses Laravel Sanctum for authentication. To obtain an access token:
```sh
POST /api/login
{
  "email": "user@example.com",
  "password": "password"
}
```
Response:
```json
{
  "token": "your-access-token"
}
```
Use this token in API requests by including it in the `Authorization` header:
```
Authorization: Bearer your-access-token
```

---

## 📚 API Endpoints

### **User Endpoints**
| Method | Endpoint                 | Description |
|--------|--------------------------|-------------|
| POST   | `/api/register`          | Register a new user |
| POST   | `/api/login`             | Login and get an access token |
| GET    | `/api/parkings`          | Get available parking spots |
| POST   | `/api/reservations`      | Create a new reservation |
| PUT    | `/api/reservations/{id}` | Update a reservation |
| DELETE | `/api/reservations/{id}` | Cancel a reservation |
| GET    | `/api/reservations`      | View reservation history |

### **Admin Endpoints**
| Method | Endpoint             | Description |
|--------|----------------------|-------------|
| POST   | `/api/parkings`      | Add a new parking lot |
| PUT    | `/api/parkings/{id}` | Update parking details |
| DELETE | `/api/parkings/{id}` | Remove a parking lot |
| GET    | `/api/stats`         | View parking statistics |

---

## 🧪 Running Tests
To run unit tests:
```sh
php artisan test
```
To test API endpoints using Postman:
- Import the Postman collection (provided in the `docs` folder).

---

## 📄 API Documentation
This API is documented using Swagger/Postman. The documentation can be accessed at:
```
http://localhost:8000/api/documentation
```

---

## 🛠 Technologies Used
- **Laravel 10** - PHP framework
- **Laravel Sanctum** - API authentication
- **PostgreSQL/MySQL** - Database
- **Swagger/Postman** - API documentation
- **PHPUnit** - Unit testing

---

## 📌 Future Improvements
- Implement payment integration for parking reservations.
- Add notifications for reservation reminders.
- Improve real-time updates with WebSockets.

---

**🚀 Happy Coding!**