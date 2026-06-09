## SharingIsCaring - Equipment Rental System
## Group Information

**Group Name**: SharingIsCaring
**Section**: 1

**Group Members**:
AINUL MARDHIAH BINTI MUSA - 2419056
NUR SHAMILA BINTI SHAYFUL AZAM - 2416222
ALYA AZ ZAHRA BINTI IZMAL - 2410802
NURIN KHAIRINA SOFEA BINTI AHMAD SAIFUDDIN - 2411320

## Project Overview
Introduction: 
SharingIsCaring is a web-based equipment rental system developed using the Laravel framework. The application connects students and club administrators within a university setting, allowing members to rent equipment from various student clubs. Club administrators can manage their equipment inventory, process rental requests, and track equipment availability through an intuitive dashboard. Super administrators have overarching control to approve new club registrations and manage all clubs on the platform.

The system promotes a sharing economy within the university community, reducing costs for students while helping clubs generate revenue from underutilized equipment.

## Project Objectives
- Primary Goal: Create a functional equipment rental platform that connects students (borrowers) with club equipment providers
- Technical Goal: Implement Laravel MVC architecture with full CRUD operations, eloquent relationships, and middleware-based authentication
- User Experience Goal: Provide an intuitive, responsive interface for both borrowers and club administrators with real-time equipment availability
- Business Goal: Enable efficient equipment management, rental tracking, and club registration approval workflow

## Target Users
- Students (Borrowers): University students looking to rent equipment for academic or personal projects
- Club Administrators: Student club leaders who manage equipment inventory and rental requests

##  Features and Functionalities

**Student (Borrower) Features** 
- User Registration & Login: Secure account creation using student email (@student.iium.edu.my) with password confirmation
- Equipment Browsing: View all available equipment with search, category filtering, and price range filters
- Equipment Details: View detailed equipment information including photos, condition, pricing, pick-up location and availability calendar
- Rental Request: Submit rental requests with start date, end date, and purpose
- My Rentals: Track active rentals, view rental history, and cancel pending requests
- Profile Management: Update personal information and change password

**Club Admin Features**
- Club Registration: Register as a club by providing club name, email, and contact information
- Equipment Management: Add, edit, delete equipment with images, pricing, condition, and location
- Rental Management: View and manage rental requests for club equipment
- Dashboard Analytics: View equipment statistics (total, available, rented, maintenance) and recent rental activity
- Profile Management: Update club information and password

**Super Admin Features**
- Club Approval System: Review and approve/reject pending club registrations with rejection reasons
- Club Management: View all registered clubs with equipment counts and active rentals
- Club Moderation: Suspend or activate club accounts with proper justification
- Cross-Club Oversight: View and manage equipment across all clubs (when necessary)

## Technical Implementation

**Technology Stack**
- Backend Framework: Laravel 10.x (PHP 8.1+)
- Frontend: Blade Templates with CSS
- Database: MySQL 8.0
- Authentication: Laravel's native authentication system
- Image Storage: Laravel File Storage (public disk)
- Development Environment: XAMPP 
- Version Control: Git with GitHub

**Database Design**
Database Schema Overview
Our database consists of 5 main tables designed to handle users, equipment, categories, rentals, and administrative functions:
Core Tables:

- users - Student, club admin, and super admin accounts with role-based access
- categories - Equipment categories for organization and filtering
- equipment - Equipment items with description, pricing, condition, availability status, pick-up, location and images
- rentals - Rental transaction records with dates, status, pick-up location and pricing
- password_reset_tokens - Password reset functionality

### Entity Relationship Diagram (ERD)

Key Relationships:
- Users can have multiple Equipment items as club admin (One-to-Many)
- Users can have multiple Rentals as borrower (One-to-Many)
- Equipment belongs to one Club (User) (Many-to-One)
- Equipment belongs to one Category (Many-to-One)
- Equipment has multiple Rentals (One-to-Many)
- Rentals belong to one Equipment (Many-to-One)
- Rentals belong to one Borrower (User) (Many-to-One)
- Categories have multiple Equipment items (One-to-Many)

**Laravel Components Implementation**
Routes (web.php)
```
<?php
use App\Http\Controllers\AdminClubController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\RentalController;
use App\Models\Equipment;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Support\Facades\Route;
 // Public routes
Route::get('/', function () {
    $featuredEquipment = Equipment::with(['club', 'category'])
        ->inRandomOrder()
        ->limit(4)
        ->get();
    $stats = [
        'clubs' => User::whereNotNull('club_name')->count(),
        'equipment' => Equipment::count(),
        'rentals' => Rental::count(),
    ];
    return view('home', compact('featuredEquipment', 'stats'));
})->name('home');
Route::get('/equipment', [EquipmentController::class, 'index'])->name('equipment.index');

// Guest routes (auth)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
   
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
   
    // Profile
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Super Admin Club Approvals
    Route::get('/admin/pending-clubs', [AuthController::class, 'pendingClubs'])->name('admin.pending-clubs');
    Route::post('/admin/pending-clubs/{user}/approve', [AuthController::class, 'approveClub'])->name('admin.pending-clubs.approve');
    Route::post('/admin/pending-clubs/{user}/reject', [AuthController::class, 'rejectClub'])->name('admin.pending-clubs.reject');
    Route::get('/admin/clubs', [AdminClubController::class, 'index'])->name('admin.clubs.index');
    Route::post('/admin/clubs/{user}/approve', [AdminClubController::class, 'approve'])->name('admin.clubs.approve');
    Route::post('/admin/clubs/{user}/reject', [AdminClubController::class, 'reject'])->name('admin.clubs.reject');
    Route::post('/admin/clubs/{user}/suspend', [AdminClubController::class, 'suspend'])->name('admin.clubs.suspend');
    Route::post('/admin/clubs/{user}/activate', [AdminClubController::class, 'activate'])->name('admin.clubs.activate');
   
    // Equipment Management (CRUD)
    Route::resource('equipment', EquipmentController::class)->except(['index', 'show']);
   
    // Rentals
    Route::post('/equipment/{equipment}/rent', [RentalController::class, 'store'])->name('rentals.store');
    Route::get('/my-rentals', [RentalController::class, 'myRentals'])->name('rentals.my-rentals');
    Route::post('/rentals/{rental}/complete', [RentalController::class, 'complete'])->name('rentals.complete');
    Route::post('/rentals/{rental}/cancel', [RentalController::class, 'cancel'])->name('rentals.cancel');
    Route::post('/rentals/{rental}/approve', [RentalController::class, 'approve'])->name('rentals.approve');
    Route::post('/rentals/{rental}/reject', [RentalController::class, 'reject'])->name('rentals.reject');
});

// Fallback wildcard route for single equipment view
Route::get('/equipment/{equipment}', [EquipmentController::class, 'show'])->name('equipment.show');
```


- Controllers

Main Controllers Implemented:
AuthController: Handles user registration (student/club), login, logout, profile management, and club approval views
DashboardController: Manages role-specific dashboards with equipment statistics and rental summaries
EquipmentController: Full CRUD operations for equipment with search, filter, and sort capabilities
RentalController: Processes rental requests, manages rental status updates, and handles cancellations
AdminClubController: Super admin functionality for club approval, suspension, and management
Models and Relationships
```
<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'club_name', 'student_id',
        'club_status', 'rejection_reason', 'suspended_at',
    ];
    protected $hidden = [
        'password', 'remember_token'
    ];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'suspended_at' => 'datetime',
        ];
    }
    /**
     * Get the equipment owned by this club admin.
     */
    public function equipment()
    {
        return $this->hasMany(Equipment::class, 'club_id');
    }
    /**
     * Get all rentals made by this user as a borrower.
     */
    public function rentalsAsBorrower()
    {
        return $this->hasMany(Rental::class, 'borrower_id');
    }
    /**
     * Get rentals for equipment owned by this club.
     */
    public function clubRentals()
    {
        return $this->hasManyThrough(Rental::class, Equipment::class, 'club_id', 'equipment_id');
    }
    /**
     * Check if user is a club admin.
     */
    public function isClubAdmin()
    {
        return $this->role === 'club_admin'
            && in_array($this->club_status, [null, 'approved'], true);
    }
    /**
     * Check if user is a super admin.
     */
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }
    /**
     * Check if user is waiting for club admin approval.
     */
    public function isPendingClub()
    {
        return $this->role === 'pending_club'
            && in_array($this->club_status, [null, 'pending'], true);
    }
    public function isRejectedClub()
    {
        return $this->club_status === 'rejected';
    }
    public function isSuspendedClub()
    {
        return $this->club_status === 'suspended';
    }
    /**
     * Get user's initials for avatar.
     */
    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->name);
        $initials = '';
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        return substr($initials, 0, 2);
    }
}
}
```

- Views and User Interface
*Blade Templates Structure:*
- layouts/app.blade.php - Main application layout with navigation
- home.blade.php - Landing page with featured equipment and statistics
- equipment/index.blade.php - Equipment browsing with filters and search
- equipment/show.blade.php - Detailed equipment view with availability calendar
- equipment/create.blade.php - Equipment addition form (club admin)
- equipment/edit.blade.php - Equipment editing form
- dashboard/admin.blade.php - Club admin/super admin dashboard
- dashboard/member.blade.php - Student borrower dashboard
- auth/login.blade.php - Login page with role selection
- auth/register.blade.php - Registration page (student/club selection)
- auth/profile.blade.php - Profile management page
- rentals/my-rentals.blade.php - User's rental history and active rentals
- admin/clubs.blade.php - Super admin club management page
- admin/pending-clubs.blade.php - Pending club approval page

*Design Features:*
- Responsive Design: CSS for mobile-first responsive layout
- Color Scheme: Vibrant purple and magenta gradient theme representing energy, community and sharing.
- Navigation: Role-based menu items (students see different options than club admins)
- Interactive Elements: Dynamic date picker for rental availability, real-time filtering
- Status Badges: Color-coded badges for equipment availability and rental status

## User Authentication System
### ** Authentication Features**
- Registration System: Email validation with @student.iium.edu.my domain enforcement for students, password confirmation, role selection (student/club)
- Login System: Secure authentication with role-based redirection and "Remember Me" functionality
- Club Registration Flow: Separate registration for clubs that requires super admin approval before login access
- Role-Based Access: Different dashboards and permissions for students, club admins, and super admins
- Profile Management: Users can update their name and password
- Club Status Checks: Login blocked for pending, rejected, or suspended clubs with appropriate error messages

### **Security Measures**
- Password encryption using Laravel's built-in hashing (bcrypt)
- CSRF protection on all forms and state-changing requests
- Input validation and sanitization using Laravel Form Requests
- Middleware protection for authenticated and role-specific routes
- SQL injection prevention through Eloquent ORM
- XSS protection through Blade's automatic escaping
- Route model binding for secure parameter handling

## Installation and Setup Instructions
### Prerequisites
- PHP >= 8.1
- Composer
- MySQL 8.0
- XAMPP 
- Node.js 

### Step-by-Step Installation
1. Clone the Repository
bash
git clone https://github.com/nkhaisofea/sharingiscaring.git
cd SharingIsCaring

2. Install PHP Dependencies
bash
composer install

3. Install NPM Dependencies (if using frontend assets)
bash
npm install
npm run build

4. Environment Configuration
bash
cp .env.example .env
php artisan key:generate

5. Configure Database in .env file
env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sharingiscaring
DB_USERNAME=root
DB_PASSWORD=

6. Run Database Migrations
bash
php artisan migrate

7. Seed Database (Optional - for development)
bash
php artisan migrate:fresh --seed

8. Create Storage Link for Images
bash
php artisan storage:link

9. Start Development Server
bash
php artisan serve

10. Access Application
Open browser to http://localhost:8000
Default super admin may need to be created manually in database

## Default Test Accounts (if seeded)
- Student Account: ainul@student.iium.edu.my / 12345678
- Club Admin: sports.club@iium.edu.my / 12345678 
- Super Admin: super.admin@gmail.com / password

## Testing and Quality Assurance
### Functionality Testing
- User registration and login with role validation
- Student email domain restriction (@student.iium.edu.my)
- Club registration and approval workflow
- Equipment browsing with category and price filters
- Equipment CRUD operations (club admin only)
- Rental request submission with date validation
- Date conflict detection for equipment bookings
- Rental cancellation and completion workflows
- Super admin club management (approve/reject/suspend/activate)
- Profile update functionality
- Dashboard statistics accuracy
- Role-based access control (students can't access admin routes)

### Browser Compatibility
- Google Chrome
- Mozilla Firefox 
- Safari
- Microsoft Edge

### Performance Testing
- Page load times maintained under 3 seconds
- Database queries optimized with eager loading (using with())
- Image compression implemented for equipment photos
- Responsive design tested on desktop, tablet, and mobile viewports
- Pagination implemented for equipment listing 
- Security Testing
- Password hashing verified
- CSRF protection confirmed on all forms
- Unauthorized route access blocked via middleware
- Role-based permissions prevent privilege escalation
- Input validation prevents malformed data submission

## Challenges Faced and Solutions
### Challenge 1: Club Registration Approval Workflow
Problem: Creating a seamless registration process where clubs could register but not log in until approved by super admin
Solution: Implemented a pending_club role with club_status field, added status checks during login, and created super admin approval interface with rejection reasons

### Challenge 2: Date Conflict Detection for Rentals
Problem: Preventing double-booking of equipment when multiple rental requests overlap
Solution: Created hasDateConflict() method in Equipment model that checks for overlapping date ranges in pending and approved rentals, used in rental request validation

### Challenge 3: Role-Based Dashboard and Navigation
Problem: Different user types (student, club admin, super admin) need completely different interfaces and permissions
Solution: Implemented role-check methods in User model, created separate dashboard views (admin/member), and used middleware to protect routes based on roles

### Challenge 4: Equipment Availability Status Management
Problem: Equipment status (available/rented/maintenance) needs to update automatically when rentals are approved or completed
Solution: Used database transactions to update both rental status and equipment availability simultaneously, ensuring data consistency

### Challenge 5: Search and Filter Performance
Problem: Equipment search with multiple filters was causing slow queries
Solution: Optimized with proper database indexing, used query scopes, and implemented pagination with query string preservation

## Future Enhancements
### Phase 2 Features (Potential Improvements)
- Email Notifications: Send confirmation emails for registration, rental approval, and return reminders
- Payment Integration: Online payment processing for rental fees with security deposit handling
- Ratings & Reviews: Allow borrowers to rate equipment and clubs after rental completion
- Equipment Wishlist: Save favorite equipment for future rental
- Advanced Analytics: Detailed rental reports, popular equipment statistics, and revenue tracking for clubs
- Mobile Application: Native mobile app for iOS and Android with push notifications
- QR Code Checkout: Generate QR codes for equipment pickup and return verification
- Late Fee Calculation: Automatic late fee calculation for overdue returns

### Scalability Considerations
- Database indexing for frequently queried columns (equipment status, rental dates, user roles)
- Caching implementation for equipment listings and dashboard stats
- Queue jobs for email notifications and report generation
- CDN integration for equipment images
- API development for future mobile app integration
= Load balancing for high traffic during peak academic periods

## Learning Outcomes
### Technical Skills Gained
- Laravel Framework: Deep understanding of MVC architecture, Eloquent ORM, middleware, service providers, and Blade templating
- Database Design: Creating efficient database schemas with proper relationships (one-to-many, many-to-one, has-many-through)
- Authentication: Implementing secure multi-role authentication with custom registration flows and login restrictions
- Authorization: Building role-based access control with middleware and policy-based permissions
- Frontend Development: Building responsive interfaces with CSS and interactive elements with JavaScript
- Date/Time Handling: Managing date ranges, conflict detection, and availability calendars using Carbon
- Version Control: Using Git and GitHub for collaborative development and feature branching

### Soft Skills Developed
- Team Collaboration: Working effectively in a group environment using GitHub for code sharing and conflict resolution
- Project Management: Planning and executing a complex web application with multiple user roles and workflows
- Problem Solving: Debugging complex issues like date conflicts, role-based redirection, and transaction management
- Documentation: Creating comprehensive project documentation including ERD, installation guides, and user manuals
- Communication: Presenting technical concepts to team members and documenting requirements clearly

## References
- Laravel Documentation. (2024). Laravel 10.x Documentation. Retrieved from https://laravel.com/docs/10.x
- Tailwind CSS Documentation. (2024). Tailwind CSS v3 Documentation. Retrieved from https://tailwindcss.com/docs
- MySQL Documentation. (2024). MySQL 8.0 Reference Manual. Retrieved from https://dev.mysql.com/doc/refman/8.0/en/
- Carbon Documentation. (2024). Carbon - DateTime API Extension. Retrieved from https://carbon.nesbot.com/docs/
- MDN Web Docs. (2024). Web Development Resources. Retrieved from https://developer.mozilla.org/
- Stack Overflow. (2024). Programming Q&A Platform. Retrieved from https://stackoverflow.com/

## Conclusion
SharingIsCaring successfully demonstrates the implementation of a comprehensive equipment rental system using the Laravel framework. The project showcases proficiency in web development fundamentals including MVC architecture, database design with complex relationships, multi-role user authentication, and responsive web design.






