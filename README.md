# Sown Overflow - Backend
### Overview
Sown Overflow is a Q&A platform designed to help users ask questions, share knowledge, and collaborate. This `README` provides detailed information about the backend implementation, including setup instructions, key features, and technical details.

### Features
* `JWT Authentication: `Secure user authentication using JSON Web Tokens.
* `Role-Based Access Control:` Different access levels for users, including guest, authenticated user, and admin roles.
* `Category Management: `Create and manage categories for organizing questions.
* `Question and Answer System:` Users can post questions and answers,  and filtering by category or user.
* `User-Friendly Responses:`API responses are formatted in JSON for easy integration with frontend applications.
#### Technologies Used
* `Framework: `Yii2 (PHP)
* `Database:` PostgreSQL
* `Authentication:` Firebase JWT
* `Other Tools:` Composer, Yii Migrations
## Setup Instructions
### Prerequisites
* PHP 7.4+
* Composer
* PostgreSQL
### Installation
#### Clone the Repository:
```
git clone https://github.com/your-username/sown-overflow-backend.git
cd sown-overflow-backend
```
#### composer install
```
composer install
```
#### Configure the Database:
Update the config/db.php file with your MySQL database credentials.
#### Apply Migrations:
```
php yii migrate
```
#### Set Up Environment Variables:
```
DB_DSN=mysql:host=localhost;dbname=sown_overflow
DB_USERNAME=root
DB_PASSWORD=your-password
JWT_SECRET_KEY=your-secret-key
```
#### Run the Application:
```
php yii serve
```
The backend will be available at http://localhost:8080.
## API Endpoints
### Authentication
* POST `/user/login ` User login.
* POST `/user/signup ` User registration.
### Categories
* GET `/show/categories:` Retrieve all categories.
* POST `/add/categories:` Add a new category (Admin only).
### Questions
* GET `/show/questions `Retrieve all questions with pagination.
* POST `/post/question` Post questions
* POST `/edit/question` Edit a question.
* POST `/post/answer` Post an answer.
* GET `/show/answers` Retrieve all answers.
* POST `/answer/edit` Edit and Answer.
* GET `/answer/delete` Delete a paticular answer.
* GET `/show/questions/byCategory?categoryname={categoryName}` Retrieve question by category name.
* GET `/show/user/questions` Retrive questions posted by a particular user.
* GET `/show/user/answers` Retrive answers answered by a praticular user.
## Authentication
JWT (JSON Web Token) is used for securing API endpoints. After successful login, the server returns a JWT token, which must be included in the Authorization header of subsequent requests:
```
Authorization: Bearer <your-token>
```
## Contributing
We welcome contributions! Please fork the repository, create a new branch, and submit a pull request. Make sure to include tests for any new features or bug fixes.



