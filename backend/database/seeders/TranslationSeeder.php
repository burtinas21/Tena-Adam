<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;
use App\Models\Translation;
use App\Models\TranslationKey;

class TranslationSeeder extends Seeder
{
    public function run(): void
    {
        $translations = [

            // ── Dashboard ────────────────────────────────────────────────
            'dashboard.title' => [
                'en' => 'Dashboard',
                'am' => 'ዳሽቦርድ',
                'om' => 'Daashboordii',            
                'ti' => 'ዳሽቦርድ',
            ],
            'dashboard.overview' => [          
                'en' => 'Dashboard Overview',
                'am' => 'የዳሽቦርድ አጠቃላይ እይታ',
                'om' => 'Cuunfaa Daashboordii',
                'ti' => 'ጠቕላላ ዳሽቦርድ',
            ],
            'dashboard.subtitle' => [
                'en' => 'Real-time hospital operations and performance metrics.',
                'am' => 'የሆስፒታሉ ቀጥተኛ ሥራ እና የአፈጻጸም መለኪያዎች።',
                'om' => 'Hojii hospitaalaa fi safartuuwwan raawwii yeroo dhihoo.',
                'ti' => 'ቀጥታዊ ምስራሓት ሆስፒታልን ንዕኡ ዝምልከቱ ስርሓት ዓቐናትን።',
            ],
            'dashboard.refresh' => [
                'en' => 'Refresh',
                'am' => 'አድስ',            
                'om' => 'Haaromsi',
                'ti' => 'ኣሐድስ',
            ],
            'dashboard.export_report' => [
                'en' => 'Export Report',
                'am' => 'ሪፖርት ላክ',
                'om' => 'Gabaasa Ergii',
                'ti' => 'ጸብጻብ ኣውጻእ',
            ],

            'dashboard.total_patients' => [
                'en' => 'Total Patients',
                'am' => 'ጠቅላላ ታካሚዎች',
                'om' => 'Dhukkubsattoota Walii Galaa',
                'ti' => 'ጠቕላላ ሕሙማት',
            ],
            'dashboard.total_doctors' => [
                'en' => 'Total Doctors',
                'am' => 'ጠቅላላ ሐኪሞች',
                'om' => 'Doktoroota Walii Galaa',
                'ti' => 'ጠቕላላ ሓካይም',
            ],
            'dashboard.departments' => [
                'en' => 'Departments',
                'am' => 'ክፍሎች',
                'om' => 'Kutaalee',
                'ti' => 'ኣሃዱታት',
            ],
            'dashboard.total_appointments' => [
                'en' => 'Total Appointments',
                'am' => 'ጠቅላላ ቀጠሮዎች',
                'om' => 'Beellama Walii Galaa',
                'ti' => 'ጠቕላላ ቆጸራታት',
            ],
            'dashboard.completed' => [
                'en' => 'Completed',
                'am' => 'የተጠናቀቀ',
                'om' => 'Xumurame',
                'ti' => 'ዝተወድአ',
            ],
            'dashboard.pending' => [
                'en' => 'Pending',
                'am' => 'በመጠባበቅ ላይ',
                'om' => 'Eegaa Jiru',
                'ti' => 'ዝጽበ ዘሎ',
            ],
            'dashboard.cancelled' => [
                'en' => 'Cancelled',
                'am' => 'የተሰረዘ',
                'om' => 'Haqame',
                'ti' => 'ዝተሰረዘ',
            ],
            'dashboard.active_telemed' => [
                'en' => 'Active Telemed',
                'am' => 'ንቁ የርቀት ህክምና',
                'om' => 'Telemediiksi Hojjechaa Jiru',
                'ti' => 'ንጡፍ ርሑቕ ሕክምና',
            ],
            'dashboard.new_patients' => [
                'en' => 'New Patients',
                'am' => 'አዲስ ታካሚዎች',
                'om' => 'Dhukkubsattoonni Haaraa',
                'ti' => 'ሓደሽቲ ሕሙማት',
            ],
            'dashboard.this_month' => [
                'en' => 'This month',
                'am' => 'ይህ ወር',
                'om' => 'Baatii kana',
                'ti' => 'እዚ ወርሒ',
            ],
            'dashboard.appointment_overview' => [
                'en' => 'Appointment Overview',
                'am' => 'አጠቃላይ የቀጠሮ እይታ',
                'om' => 'Cuunfaa Beellama',
                'ti' => 'ጠቕላላ ዕዮ ቆጸራ',
            ],
            'dashboard.welcome' => [
                'en' => 'Welcome',
                'am' => 'እንኳን ደህና መጡ',
                'om' => 'Baga nagaan dhufte',
                'ti' => 'ብደሓን መጻእካ',
            ],
            'dashboard.upcoming' => [
                'en' => 'Upcoming',
                'am' => 'መጪ',
                'om' => 'Dhufaa Jiru',
                'ti' => 'ዝመጽእ',
            ],
            'dashboard.active_rx' => [
                'en' => 'Active Rx',
                'am' => 'ንቁ ፕሬስክሪፕሽን',
                'om' => 'Faarmaasii Hojjechaa Jiru',
                'ti' => 'ንጡፍ መድሃኒት ትእዛዝ',
            ],
            'dashboard.records' => [
                'en' => 'Records',
                'am' => 'መዝገቦች',
                'om' => 'Galmeewwan',
                'ti' => 'መዛግብቲ',
            ],
            'dashboard.retry' => [
                'en' => 'Retry',
                'am' => 'እንደገና ሞክር',
                'om' => 'Irra Deebi\'i',
                'ti' => 'ደጊምካ ፈትን',
            ],

            // ── Quick Actions ────────────────────────────────────────────
            'action.book_appointment' => [
                'en' => 'Book Appointment',
                'am' => 'ቀጠሮ ያዝ',
                'om' => 'Beellama Qabadhu',
                'ti' => 'ቆጸራ ያዝ',
            ],
            'action.join_telemedicine' => [
                'en' => 'Join Telemedicine',
                'am' => 'የርቀት ህክምና ተቀላቀል',
                'om' => 'Telemediiksiitti Makamu',
                'ti' => 'ናብ ርሑቕ ሕክምና እቶ',
            ],
            'action.search_doctors' => [
                'en' => 'Search Doctors',
                'am' => 'ሐኪሞችን ፈልግ',
                'om' => 'Doktoroota Barbaadi',
                'ti' => 'ሓካይም ድለ',
            ],
            'action.view_records' => [
                'en' => 'View Records',
                'am' => 'መዝገቦችን ተመልከት',
                'om' => 'Galmeewwan Ilaali',
                'ti' => 'መዛግብቲ ርኤ',
            ],

            // ── Common Buttons ───────────────────────────────────────────
            'button.save' => [
                'en' => 'Save',
                'am' => 'አስቀምጥ',
                'om' => 'Kuusii',
                'ti' => 'ኣቐምጥ',
            ],
            'button.cancel' => [
                'en' => 'Cancel',
                'am' => 'ሰርዝ',
                'om' => 'Haqi',
                'ti' => 'ሰርዝ',
            ],
            'button.submit' => [
                'en' => 'Submit',
                'am' => 'አስገባ',
                'om' => 'Galchi',
                'ti' => 'ኣእቱ',
            ],
            'button.delete' => [
                'en' => 'Delete',
                'am' => 'ሰርዝ',
                'om' => 'Haqi',
                'ti' => 'ሰርዝ',
            ],
            'button.edit' => [
                'en' => 'Edit',
                'am' => 'አርም',
                'om' => 'Gulaali',
                'ti' => 'ኣስተኻኽል',
            ],
            'button.add' => [
                'en' => 'Add',
                'am' => 'ጨምር',
                'om' => 'Dabali',
                'ti' => 'ወስኽ',
            ],
            'button.close' => [
                'en' => 'Close',
                'am' => 'ዝጋ',
                'om' => 'Cufii',
                'ti' => 'ዕጸ',
            ],
            'button.confirm' => [
                'en' => 'Confirm',
                'am' => 'አረጋግጥ',
                'om' => 'Mirkaneessi',
                'ti' => 'ኣረጋግጽ',
            ],
            'button.back' => [
                'en' => 'Back',
                'am' => 'ተመለስ',
                'om' => 'Deebi\'i',
                'ti' => 'ተመለስ',
            ],
            'button.search' => [
                'en' => 'Search',
                'am' => 'ፈልግ',
                'om' => 'Barbaadi',
                'ti' => 'ድለ',
            ],
            'button.filter' => [
                'en' => 'Filter',
                'am' => 'ፈልጥ',
                'om' => 'Calalii',
                'ti' => 'ኣጣቕዕ',
            ],
            'button.export' => [
                'en' => 'Export',
                'am' => 'ላክ',
                'om' => 'Ergii',
                'ti' => 'ኣውጻእ',
            ],
            'button.loading' => [
                'en' => 'Loading...',
                'am' => 'እየጫነ ነው...',
                'om' => 'Fe\'aa jira...',
                'ti' => 'ይጻዓን ኣሎ...',
            ],
            'button.view_all' => [
                'en' => 'View all',
                'am' => 'ሁሉንም ተመልከት',
                'om' => 'Hunda Ilaali',
                'ti' => 'ኩሉ ርኤ',
            ],
            'button.mark_all_read' => [
                'en' => 'Mark all read',
                'am' => 'ሁሉንም እንዳነበቡ ምልክት ያድርጉ',
                'om' => 'Hunda Dubbifame Godhii',
                'ti' => 'ኩሉ ከምዝተነብበ ምልክት ግበር',
            ],

            // ── Authentication ───────────────────────────────────────────
            'login' => [
                'en' => 'Login',
                'am' => 'ግባ',
                'om' => 'Seeni',
                'ti' => 'እቶ',
            ],
            'logout' => [
                'en' => 'Logout',
                'am' => 'ውጣ',
                'om' => 'Bai',
                'ti' => 'ውጻ',
            ],
            'auth.sign_in' => [
                'en' => 'Sign In',
                'am' => 'ግባ',
                'om' => 'Seeni',
                'ti' => 'ኣቲ',
            ],
            'auth.sign_in_subtitle' => [
                'en' => 'Enter your credentials to access your healthcare portal.',
                'am' => 'ወደ የጤና መፍትሄ ፖርታልዎ ለመግባት ምስክርነቶቾን ያስገቡ።',
                'om' => 'Portaala fayyaa keessanitti seenuuf ragaalee keessan galchaa.',
                'ti' => 'ናብ ፖርታልካ ሕክምና ንምእታው ምስክርነትካ ኣእቱ።',
            ],
            'auth.email' => [
                'en' => 'Email Address',
                'am' => 'ኢሜይል አድራሻ',
                'om' => 'Teessoo Imeelii',
                'ti' => 'ኣድራሻ ኢሜይል',
            ],
            'auth.password' => [
                'en' => 'Password',
                'am' => 'የሚስጥር ቁጥር',
                'om' => 'Jecha Icciitii',
                'ti' => 'ስዉር ቁጽሪ',
            ],
            'auth.remember_me' => [
                'en' => 'Remember Me',
                'am' => 'አስታውሰኝ',
                'om' => 'Nana\'i',
                'ti' => 'ዘክረኒ',
            ],
            'auth.forgot_password' => [
                'en' => 'Forgot Password?',
                'am' => 'የሚስጥር ቁጥርዎን ረሱ?',
                'om' => 'Jecha icciitii dagatte?',
                'ti' => 'ስዉር ቁጽሪ ረሲዕካ?',
            ],
            'auth.no_account' => [
                'en' => "Don't have an account?",
                'am' => 'መለያ የለዎትም?',
                'om' => 'Herrega hin qabduu?',
                'ti' => 'ሕሳብ የብልካን?',
            ],
            'auth.create_account' => [
                'en' => 'Create Account',
                'am' => 'መለያ ፍጠር',
                'om' => 'Herrega Banuu',
                'ti' => 'ሕሳብ ፍጠር',
            ],
            'auth.secure_auth' => [
                'en' => 'Secure Authentication',
                'am' => 'ደህንነቱ የተጠበቀ ምዝገባ',
                'om' => 'Mirkaneessa Nageenya',
                'ti' => 'ውሑስ ምስክርነት',
            ],
            'auth.login_success' => [
                'en' => 'Login successful!',
                'am' => 'ስኬታማ ምዝገባ!',
                'om' => 'Seenuu milkaa\'e!',
                'ti' => 'ምእታው ተዓዊቱ!',
            ],
            'auth.invalid_credentials' => [
                'en' => 'Invalid email or password',
                'am' => 'ልክ ያልሆነ ኢሜይል ወይም የሚስጥር ቁጥር',
                'om' => 'Imeelii ykn jecha icciitii dogoggoraa',
                'ti' => 'ዘይቅኑዕ ኢሜይል ወይ ስዉር ቁጽሪ',
            ],

            // ── Sidebar navigation ───────────────────────────────────────
            'nav.dashboard' => [
                'en' => 'Dashboard',
                'am' => 'ዳሽቦርድ',
                'om' => 'Daashboordii',
                'ti' => 'ዳሽቦርድ',
            ],
            'nav.departments' => [
                'en' => 'Departments',
                'am' => 'ክፍሎች',
                'om' => 'Kutaalee',
                'ti' => 'ኣሃዱታት',
            ],
            'nav.facilities' => [
                'en' => 'Facilities',
                'am' => 'አገልግሎቶች',
                'om' => 'Tajaajilaalee',
                'ti' => 'ምቹ ቦታታት',
            ],
            'nav.operating_hours' => [
                'en' => 'Operating Hours',
                'am' => 'የስራ ሰዓቶች',
                'om' => 'Saatii Hojii',
                'ti' => 'ሰዓታት ስራሕ',
            ],
            'nav.doctors_staff' => [
                'en' => 'Doctors & Staff',
                'am' => 'ሐኪሞችና ሠራተኞች',
                'om' => 'Doktoroota fi Hojjettoonni',
                'ti' => 'ሓካይምን ሰራሕተኛታትን',
            ],
            'nav.appointments' => [
                'en' => 'Appointments',
                'am' => 'ቀጠሮዎች',
                'om' => 'Beellamoota',
                'ti' => 'ቆጸራታት',
            ],
            'nav.queue_management' => [
                'en' => 'Queue Management',
                'am' => 'የወረፋ አስተዳደር',
                'om' => 'Bulchiinsa Sarara Eeguu',
                'ti' => 'ምስምሳ ተሰሊፍቲ',
            ],
            'nav.telemedicine' => [
                'en' => 'Telemedicine',
                'am' => 'የርቀት ህክምና',
                'om' => 'Telemediiksi',
                'ti' => 'ርሑቕ ሕክምና',
            ],
            'nav.doctor_leaves' => [
                'en' => 'Doctor Leaves',
                'am' => 'የሐኪም ፈቃድ',
                'om' => 'Boqonnaa Doktoraa',
                'ti' => 'ፍቓድ ሓካይም',
            ],
            'nav.notifications' => [
                'en' => 'Notifications',
                'am' => 'ማሳወቂያዎች',
                'om' => 'Beeksisaalee',
                'ti' => 'ምልክታታት',
            ],
            'nav.settings' => [
                'en' => 'Settings',
                'am' => 'ቅንብሮች',
                'om' => 'Qindaa\'ina',
                'ti' => 'ምስሪሕ',
            ],
            'nav.reports_analytics' => [
                'en' => 'Reports & Analytics',
                'am' => 'ሪፖርቶች እና ትንታኔ',
                'om' => 'Gabaasaa fi Xiinxala',
                'ti' => 'ጸብጻባትን ትንተናን',
            ],
            'nav.symptoms' => [
                'en' => 'Symptoms',
                'am' => 'ምልክቶች',
                'om' => 'Mallattoolee',
                'ti' => 'ምልክታት ሕማም',
            ],
            'nav.schedule' => [
                'en' => 'Schedule',
                'am' => 'መርሃ ግብር',
                'om' => 'Karoora',
                'ti' => 'ሰዓት መደብ',
            ],
            'nav.queue' => [
                'en' => 'Queue',
                'am' => 'ወረፋ',
                'om' => 'Sarara Eeguu',
                'ti' => 'ሰሪዐ',
            ],
            'nav.medical_encounter' => [
                'en' => 'Medical Encounter',
                'am' => 'የህክምና ግንኙነት',
                'om' => 'Qunnamtii Fayyaa',
                'ti' => 'ምርምር ሕክምና',
            ],
            'nav.vitals' => [
                'en' => 'Vitals',
                'am' => 'ወሳኝ ምልክቶች',
                'om' => 'Mallattolee Bu\'uraa',
                'ti' => 'ኣምሰሉ ምልክታት',
            ],
            'nav.prescriptions' => [
                'en' => 'Prescriptions',
                'am' => 'ፕሬስክሪፕሽኖች',
                'om' => 'Barreeffama Faarmaa',
                'ti' => 'ትእዛዝ መድሃኒት',
            ],
            'nav.documents' => [
                'en' => 'Documents',
                'am' => 'ሰነዶች',
                'om' => 'Galmee',
                'ti' => 'ሰነዳት',
            ],
            'nav.profile' => [
                'en' => 'Profile',
                'am' => 'መገለጫ',
                'om' => 'Profaayilii',
                'ti' => 'ፕሮፋይል',
            ],
            'nav.hospitals' => [
                'en' => 'Hospitals',
                'am' => 'ሆስፒታሎች',
                'om' => 'Hospitaalota',
                'ti' => 'ሆስፒታላት',
            ],
            'nav.doctors' => [
                'en' => 'Doctors',
                'am' => 'ሐኪሞች',
                'om' => 'Doktoroota',
                'ti' => 'ሓካይም',
            ],
            'nav.telehealth' => [
                'en' => 'TeleHealth',
                'am' => 'የርቀት ጤና',
                'om' => 'Telehealth',
                'ti' => 'ርሑቕ ጥዕና',
            ],
            'nav.symptom_checker' => [
                'en' => 'Symptom Checker',
                'am' => 'የምልክት ምርመራ',
                'om' => 'Mirkaneessa Mallattoo',
                'ti' => 'መርማሪ ምልክት',
            ],
            'nav.medical_history' => [
                'en' => 'Medical History',
                'am' => 'የህክምና ታሪክ',
                'om' => 'Seenaa Fayyaa',
                'ti' => 'ታሪኽ ሕክምና',
            ],
            'nav.hospital_network' => [
                'en' => 'Hospital Network',
                'am' => 'የሆስፒታል ኔትወርክ',
                'om' => 'Networki Hospitaalaa',
                'ti' => 'ኔትወርክ ሆስፒታል',
            ],
            'nav.hospital_admins' => [
                'en' => 'Hospital Admins',
                'am' => 'የሆስፒታል አስተዳዳሪዎች',
                'om' => 'Bulchitoonni Hospitaalaa',
                'ti' => 'ኣካያዲ ሆስፒታል',
            ],
            'nav.analytics' => [
                'en' => 'Analytics',
                'am' => 'ትንታኔ',
                'om' => 'Xiinxala',
                'ti' => 'ትንተና',
            ],
            'nav.audit_logs' => [
                'en' => 'Audit Logs',
                'am' => 'የምርመራ ምዝገባዎች',
                'om' => 'Galmeewwan Mirkaneessa',
                'ti' => 'መዛግብቲ ኦዲት',
            ],
            'nav.reports' => [
                'en' => 'Reports',
                'am' => 'ሪፖርቶች',
                'om' => 'Gabaasaalee',
                'ti' => 'ጸብጻባት',
            ],
            'nav.registration' => [
                'en' => 'Registration',
                'am' => 'ምዝገባ',
                'om' => 'Galmaa\'uu',
                'ti' => 'ምምዝጋብ',
            ],
            'nav.notification' => [
                'en' => 'Notification',
                'am' => 'ማሳወቂያ',
                'om' => 'Beeksisa',
                'ti' => 'ምልክታ',
            ],

            // ── Healthcare entities ──────────────────────────────────────
            'patient' => [
                'en' => 'Patient',
                'am' => 'ታካሚ',
                'om' => 'Dhukkubsataa',
                'ti' => 'ሕሙም',
            ],
            'doctor' => [
                'en' => 'Doctor',
                'am' => 'ሐኪም',
                'om' => 'Doktara',
                'ti' => 'ሓኪም',
            ],
            'appointment' => [
                'en' => 'Appointment',
                'am' => 'ቀጠሮ',
                'om' => 'Beellama',
                'ti' => 'ቆጸራ',
            ],
            'telehealth' => [
                'en' => 'Telehealth',
                'am' => 'የርቀት ህክምና',
                'om' => 'Telehealth',
                'ti' => 'ርሑቕ ሕክምና',
            ],
            'hospital' => [
                'en' => 'Hospital',
                'am' => 'ሆስፒታል',
                'om' => 'Hospitaala',
                'ti' => 'ሆስፒታል',
            ],
            'department' => [
                'en' => 'Department',
                'am' => 'ክፍል',
                'om' => 'Kutaa',
                'ti' => 'ኣሃዱ',
            ],
            'facility' => [
                'en' => 'Facility',
                'am' => 'አገልግሎት',
                'om' => 'Tajaajila',
                'ti' => 'ቦታ ምቹ',
            ],
            'prescription' => [
                'en' => 'Prescription',
                'am' => 'ፕሬስክሪፕሽን',
                'om' => 'Barreeffama Faarmaa',
                'ti' => 'ትእዛዝ መድሃኒት',
            ],
            'queue' => [
                'en' => 'Queue',
                'am' => 'ወረፋ',
                'om' => 'Sarara Eeguu',
                'ti' => 'ተሰሊፍቲ',
            ],
            'schedule' => [
                'en' => 'Schedule',
                'am' => 'መርሃ ግብር',
                'om' => 'Karoora',
                'ti' => 'መደብ',
            ],
            'leave' => [
                'en' => 'Leave',
                'am' => 'ፈቃድ',
                'om' => 'Boqonnaa',
                'ti' => 'ፍቓድ',
            ],
            'vital' => [
                'en' => 'Vital',
                'am' => 'ወሳኝ ምልክት',
                'om' => 'Mallattoo Bu\'uraa',
                'ti' => 'ኣምሰሉ ምልክት',
            ],

            // ── Appointment Status ───────────────────────────────────────
            'status.pending' => [
                'en' => 'Pending',
                'am' => 'በመጠባበቅ ላይ',
                'om' => 'Eegaa Jiru',
                'ti' => 'ዝጽበ ዘሎ',
            ],
            'status.confirmed' => [
                'en' => 'Confirmed',
                'am' => 'የተረጋገጠ',
                'om' => 'Mirkana\'e',
                'ti' => 'ዝተረጋገጸ',
            ],
            'status.completed' => [
                'en' => 'Completed',
                'am' => 'የተጠናቀቀ',
                'om' => 'Xumurame',
                'ti' => 'ዝተወድአ',
            ],
            'status.cancelled' => [
                'en' => 'Cancelled',
                'am' => 'የተሰረዘ',
                'om' => 'Haqame',
                'ti' => 'ዝተሰረዘ',
            ],
            'status.no_show' => [
                'en' => 'No Show',
                'am' => 'አልቀረበም',
                'om' => 'Hin Dhufne',
                'ti' => 'ዘይቀረበ',
            ],
            'status.active' => [
                'en' => 'Active',
                'am' => 'ንቁ',
                'om' => 'Hojjechaa Jiru',
                'ti' => 'ንጡፍ',
            ],
            'status.inactive' => [
                'en' => 'Inactive',
                'am' => 'ንቁ ያልሆነ',
                'om' => 'Hojii Hin Jirre',
                'ti' => 'ዘይንጡፍ',
            ],
            'status.approved' => [
                'en' => 'Approved',
                'am' => 'የጸደቀ',
                'om' => 'Raggaasifame',
                'ti' => 'ዝጸደቐ',
            ],
            'status.rejected' => [
                'en' => 'Rejected',
                'am' => 'የተቀነሰ',
                'om' => 'Diddame',
                'ti' => 'ዝተነጸገ',
            ],

            // ── Notifications ────────────────────────────────────────────
            'notification.title' => [
                'en' => 'Notifications',
                'am' => 'ማሳወቂያዎች',
                'om' => 'Beeksisaalee',
                'ti' => 'ምልክታታት',
            ],
            'notification.empty' => [
                'en' => 'No notifications yet',
                'am' => 'እስካሁን ምንም ማሳወቂያ የለም',
                'om' => 'Beeksisa hanga ammaatti hin jiru',
                'ti' => 'ምልክታ ገና የለን',
            ],
            'notification.view_all' => [
                'en' => 'View all notifications →',
                'am' => 'ሁሉንም ማሳወቂያዎች ተመልከት →',
                'om' => 'Beeksisaalee hunda ilaali →',
                'ti' => 'ኩሉ ምልክታታት ርኤ →',
            ],
            'notification.just_now' => [
                'en' => 'just now',
                'am' => 'ልክ አሁን',
                'om' => 'amma',
                'ti' => 'ሕጂ ቁሩብ',
            ],

            // ── Search ───────────────────────────────────────────────────
            'search.placeholder' => [
                'en' => 'Search...',
                'am' => 'ፈልግ...',
                'om' => 'Barbaadi...',
                'ti' => 'ድለ...',
            ],
            'search.doctors' => [
                'en' => 'Search Doctor Name, Specialization...',
                'am' => 'የሐኪም ስም ወይም ልዩ ሙያ ፈልግ...',
                'om' => 'Maqaa Doktora, Ogumaa Barbaadi...',
                'ti' => 'ሽም ሓኪም፡ ፍሉይ ሙያ ድለ...',
            ],
            'search.hospitals' => [
                'en' => 'Search hospitals...',
                'am' => 'ሆስፒታሎችን ፈልግ...',
                'om' => 'Hospitaalota Barbaadi...',
                'ti' => 'ሆስፒታላት ድለ...',
            ],
            'search.appointments' => [
                'en' => 'Search appointments...',
                'am' => 'ቀጠሮዎችን ፈልግ...',
                'om' => 'Beellamoota Barbaadi...',
                'ti' => 'ቆጸራታት ድለ...',
            ],

            // ── Reschedule ───────────────────────────────────────────────
            'reschedule.title' => [
                'en' => 'Reschedule Appointment',
                'am' => 'ቀጠሮ እንደገና ያዝ',
                'om' => 'Beellama Irra Deebi\'ii Qabadhu',
                'ti' => 'ቆጸራ ደጊምካ ያዝ',
            ],
            'reschedule.subtitle' => [
                'en' => 'Pick a new date and time slot',
                'am' => 'አዲስ ቀን እና ሰዓት ምረጥ',
                'om' => 'Guyyaa fi Yeroo Haaraa Filadhu',
                'ti' => 'ሓዲሽ ዕለትን ሰዓትን ምረጽ',
            ],
            'reschedule.new_date' => [
                'en' => 'New Date',
                'am' => 'አዲስ ቀን',
                'om' => 'Guyyaa Haaraa',
                'ti' => 'ሓዲሽ ዕለት',
            ],
            'reschedule.available_slots' => [
                'en' => 'Available Slots',
                'am' => 'የሚገኙ ጊዜዎች',
                'om' => 'Yeroolee Argamuu',
                'ti' => 'ዝርከቡ ሰዓታት',
            ],
            'reschedule.no_slots' => [
                'en' => 'No available slots on this date.',
                'am' => 'በዚህ ቀን ምንም ሰዓት አልተገኘም።',
                'om' => 'Guyyaa kana yeroo argamu hin jiru.',
                'ti' => 'ኣብዚ ዕለት ዝርከቡ ሰዓታት የለዉን።',
            ],
            'reschedule.loading_slots' => [
                'en' => 'Loading slots...',
                'am' => 'ሰዓቶችን እየጫነ ነው...',
                'om' => 'Yeroolee fe\'aa jira...',
                'ti' => 'ሰዓታት ይጻዓን ኣሎ...',
            ],
            'reschedule.confirm' => [
                'en' => 'Confirm Reschedule',
                'am' => 'ዳግም ቀጠሮ አረጋግጥ',
                'om' => 'Irra Deebi\'ii Qabachuu Mirkaneessi',
                'ti' => 'ዳግማይ ቆጸራ ኣረጋግጽ',
            ],

            // ── Table / List common ──────────────────────────────────────
            'table.no_data' => [
                'en' => 'No data found',
                'am' => 'ምንም ውሂብ አልተገኘም',
                'om' => 'Odeeffannoo hin argamne',
                'ti' => 'ዳታ ኣይተረኽበን',
            ],
            'table.loading' => [
                'en' => 'Loading...',
                'am' => 'እየጫነ ነው...',
                'om' => 'Fe\'aa jira...',
                'ti' => 'ይጻዓን ኣሎ...',
            ],
            'table.actions' => [
                'en' => 'Actions',
                'am' => 'ድርጊቶች',
                'om' => 'Tarkaanfiiwwan',
                'ti' => 'ስጉምቲታት',
            ],
            'table.name' => [
                'en' => 'Name',
                'am' => 'ስም',
                'om' => 'Maqaa',
                'ti' => 'ሽም',
            ],
            'table.email' => [
                'en' => 'Email',
                'am' => 'ኢሜይል',
                'om' => 'Imeelii',
                'ti' => 'ኢሜይል',
            ],
            'table.phone' => [
                'en' => 'Phone',
                'am' => 'ስልክ',
                'om' => 'Bilbila',
                'ti' => 'ስልኪ',
            ],
            'table.status' => [
                'en' => 'Status',
                'am' => 'ሁኔታ',
                'om' => 'Haala',
                'ti' => 'ሁኔታ',
            ],
            'table.date' => [
                'en' => 'Date',
                'am' => 'ቀን',
                'om' => 'Guyyaa',
                'ti' => 'ዕለት',
            ],

            // ── Days of week ─────────────────────────────────────────────
            'day.monday' => [
                'en' => 'Monday',
                'am' => 'ሰኞ',
                'om' => 'Wiixata',
                'ti' => 'ሰኑይ',
            ],
            'day.tuesday' => [
                'en' => 'Tuesday',
                'am' => 'ማክሰኞ',
                'om' => 'Kibxata',
                'ti' => 'ሰሉስ',
            ],
            'day.wednesday' => [
                'en' => 'Wednesday',
                'am' => 'ረቡዕ',
                'om' => 'Roobii',
                'ti' => 'ረቡዕ',
            ],
            'day.thursday' => [
                'en' => 'Thursday',
                'am' => 'ሐሙስ',
                'om' => 'Kamisa',
                'ti' => 'ሓሙስ',
            ],
            'day.friday' => [
                'en' => 'Friday',
                'am' => 'አርብ',
                'om' => 'Jimaata',
                'ti' => 'ዓርቢ',
            ],
            'day.saturday' => [
                'en' => 'Saturday',
                'am' => 'ቅዳሜ',
                'om' => 'Sanbata',
                'ti' => 'ቀዳም',
            ],
            'day.sunday' => [
                'en' => 'Sunday',
                'am' => 'እሁድ',
                'om' => 'Dilbata',
                'ti' => 'ሰንበት',
            ],

            // ── Error / empty states ─────────────────────────────────────
            'error.generic' => [
                'en' => 'Something went wrong. Please try again.',
                'am' => 'ሆነ አንድ ስህተት። እባክዎ እንደገና ይሞክሩ።',
                'om' => 'Wanti tokko dogoggore. Maaloo irra deebi\'i.',
                'ti' => 'ሓደ ጸገም ሰሊፉ። በጃኻ ደጊምካ ፈትን።',
            ],
            'error.not_found' => [
                'en' => 'Not found',
                'am' => 'አልተገኘም',
                'om' => 'Hin argamne',
                'ti' => 'ኣይተረኽበን',
            ],
            'error.unauthorized' => [
                'en' => 'Unauthorized',
                'am' => 'ፈቃድ የለዎትም',
                'om' => 'Hayyama Hin Qabdu',
                'ti' => 'ፍቓድ የብልካን',
            ],
            'empty.no_appointments' => [
                'en' => 'No appointments found',
                'am' => 'ምንም ቀጠሮ አልተገኘም',
                'om' => 'Beellama hin argamne',
                'ti' => 'ቆጸራ ኣይተረኽበን',
            ],
            'empty.no_results' => [
                'en' => 'No results found',
                'am' => 'ምንም ውጤት አልተገኘም',
                'om' => 'Bu\'aa hin argamne',
                'ti' => 'ውጽኢት ኣይተረኽበን',
            ],
        ];

        foreach ($translations as $key => $languages) {
            $translationKey = TranslationKey::where('key', $key)->first();

            if (!$translationKey) {
                continue;
            }

            foreach ($languages as $code => $value) {
                $language = Language::where('code', $code)->first();

                if ($language) {
                    Translation::updateOrCreate(
                        [
                            'translation_key_id' => $translationKey->id,
                            'language_id'        => $language->id,
                        ],
                        ['value' => $value]
                    );
                }
            }
        }
    }
}
