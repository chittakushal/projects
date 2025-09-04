README.md 

# E-Hackathon  

## 📌 Project Overview  
E-Hackathon is a web-based platform that enables the seamless management of hackathons. Admins, organizers, and students can interact, manage events, and participate in hackathons efficiently.  

## 🔥 Features  
### Admin Features:  
- View and manage all hackathons.  
- Oversee participants and event progress.  

### Organizer Features:  
- Create and manage hackathon events.  
- Define rounds (quiz, document submission, technical tasks).  
- Monitor registrations and review submissions.  

### Student Features:  
- Register for hackathons individually or in teams.  
- Submit projects and participate in challenges.  
- Chat with team members.  
- Download certificates.  

## 🛠️ Technologies Used  
- Frontend: HTML, CSS, JavaScript  
- Backend: PHP (with Laravel/Plain PHP)  
- Database: MySQL (via XAMPP)  
- Server: Apache (via XAMPP)  

## 📂 Database Schema  
- Users: Stores user details (admins, organizers, participants).  
- Hackathons: Stores hackathon details (name, description, dates, rounds).  
- Teams & Participants: Manages team formations.  
- Submissions & Scores: Stores project submissions and evaluations.  
- Chat & Messaging: Enables communication between team members.  
- Certificates: Automates certificate generation.  

## 🚀 Installation Guide (Using XAMPP)  
1. **Download and Install XAMPP**:  
   - Install XAMPP from [Apache Friends](https://www.apachefriends.org/index.html).  
   - Start Apache and MySQL from the XAMPP Control Panel.  

2. **Clone the Repository**  
   git clone https://github.com/your-username/e-hackathon.git
   

3. **Move the Project to XAMPP’s 'htdocs' Folder**  
   - Place the project folder inside:  
     
     C:\xampp\htdocs\e-hackathon
    

4. **Create the Database**  
   - Open **phpMyAdmin** (`http://localhost/phpmyadmin`).  
   - Create a database (e.g., `e_hackathon`).  
   - Import the provided SQL file (`e_hackathon.sql`).  

5. **Configure Database Connection**  
   - Open `config.php` (or `.env` if using Laravel).  
   - Set up the database connection:  
     ```php
     $host = "localhost";
     $user = "root";
     $password = "";
     $database = "e_hackathon";
     ```

6. **Run the Project**  
   - Open a web browser and go to:  
     http://localhost/e-hackathon
    

## 🏆 Future Enhancements  
- Implement AI-powered submission scoring.  
- Add live streaming for hackathon events.  
- Improve team collaboration tools.  

## 🤝 Contributing  
Contributions are welcome! Feel free to submit pull requests or report issues.  
