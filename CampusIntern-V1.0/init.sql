DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS internships;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    role TEXT NOT NULL CHECK (role IN ('student', 'admin')),
    department TEXT,
    year TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Internships table
CREATE TABLE IF NOT EXISTS internships (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    student_id INTEGER NOT NULL,
    company TEXT NOT NULL,
    position TEXT NOT NULL,
    duration TEXT NOT NULL,
    mode TEXT CHECK (mode IN ('Work from Home', 'Work from Office', 'Hybrid')),
    interview_mode TEXT CHECK (interview_mode IN ('Online', 'Offline')),
    interview_questions TEXT,
    stipend TEXT CHECK (stipend IN ('Yes', 'No')),
    skills TEXT,
    company_website TEXT,
    offer_letter TEXT,
    completion_certificate TEXT,
    feedback TEXT,
    status TEXT DEFAULT 'pending' CHECK (status IN ('pending', 'approved', 'rejected')),
    notified BOOLEAN DEFAULT 0,
    feedback_notified BOOLEAN DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id)
);

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_internships_student ON internships(student_id);
CREATE INDEX IF NOT EXISTS idx_internships_status ON internships(status);


