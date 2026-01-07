<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctors = [
            [
                'name' => 'Dr. Sarah Johnson',
                'specialization' => 'Cardiologist',
                'email' => 'sarah.johnson@healthcare.com',
                'phone' => '+1-555-0101',
                'bio' => 'Dr. Sarah Johnson is a board-certified cardiologist with over 15 years of experience in treating cardiovascular diseases. She specializes in preventive cardiology, heart failure management, and interventional cardiology. Dr. Johnson is committed to providing personalized care and helping patients achieve optimal heart health.',
                'education' => 'MD, Harvard Medical School',
                'certifications' => ['Board Certified Cardiologist', 'American Heart Association'],
                'specialties' => ['Preventive Cardiology', 'Heart Failure Management', 'Interventional Cardiology', 'Cardiac Rehabilitation'],
                'hospital_affiliations' => ['City General Hospital', 'Heart Care Center'],
                'languages' => ['English', 'Spanish'],
                'years_of_experience' => 15,
                'consultation_fee' => 150.00,
                'available_hours' => 'Monday - Friday: 9:00 AM - 5:00 PM',
                'status' => 'active',
            ],
            [
                'name' => 'Dr. Michael Chen',
                'specialization' => 'General Practitioner',
                'email' => 'michael.chen@healthcare.com',
                'phone' => '+1-555-0102',
                'bio' => 'Dr. Michael Chen is a dedicated general practitioner with 12 years of experience in family medicine. He provides comprehensive primary care services for patients of all ages, focusing on preventive care, chronic disease management, and health promotion.',
                'education' => 'MD, Stanford University School of Medicine',
                'certifications' => ['Board Certified Family Medicine', 'American Board of Family Medicine'],
                'specialties' => ['Family Medicine', 'Preventive Care', 'Chronic Disease Management', 'Health Promotion'],
                'hospital_affiliations' => ['Community Health Center', 'Family Care Clinic'],
                'languages' => ['English', 'Mandarin', 'Cantonese'],
                'years_of_experience' => 12,
                'consultation_fee' => 100.00,
                'available_hours' => 'Monday - Saturday: 8:00 AM - 6:00 PM',
                'status' => 'active',
            ],
            [
                'name' => 'Dr. Emily Rodriguez',
                'specialization' => 'Dermatologist',
                'email' => 'emily.rodriguez@healthcare.com',
                'phone' => '+1-555-0103',
                'bio' => 'Dr. Emily Rodriguez is a board-certified dermatologist specializing in medical and cosmetic dermatology. With 10 years of experience, she treats various skin conditions including acne, eczema, psoriasis, and skin cancer. She also offers cosmetic procedures and anti-aging treatments.',
                'education' => 'MD, Johns Hopkins University School of Medicine',
                'certifications' => ['Board Certified Dermatologist', 'American Academy of Dermatology'],
                'specialties' => ['Medical Dermatology', 'Cosmetic Dermatology', 'Skin Cancer Treatment', 'Anti-Aging Treatments'],
                'hospital_affiliations' => ['Skin Care Center', 'Dermatology Associates'],
                'languages' => ['English', 'Spanish', 'Portuguese'],
                'years_of_experience' => 10,
                'consultation_fee' => 120.00,
                'available_hours' => 'Tuesday - Friday: 10:00 AM - 6:00 PM',
                'status' => 'active',
            ],
            [
                'name' => 'Dr. James Wilson',
                'specialization' => 'Pediatrician',
                'email' => 'james.wilson@healthcare.com',
                'phone' => '+1-555-0104',
                'bio' => 'Dr. James Wilson is a highly experienced pediatrician with 18 years of dedicated service to children\'s health. He specializes in pediatric care from infancy through adolescence, focusing on growth and development, immunizations, and treating childhood illnesses.',
                'education' => 'MD, Boston Children\'s Hospital, Harvard Medical School',
                'certifications' => ['Board Certified Pediatrician', 'American Academy of Pediatrics'],
                'specialties' => ['Pediatric Care', 'Child Development', 'Immunizations', 'Adolescent Medicine'],
                'hospital_affiliations' => ['Children\'s Hospital', 'Pediatric Care Center'],
                'languages' => ['English', 'French'],
                'years_of_experience' => 18,
                'consultation_fee' => 110.00,
                'available_hours' => 'Monday - Friday: 8:00 AM - 5:00 PM, Saturday: 9:00 AM - 1:00 PM',
                'status' => 'active',
            ],
            [
                'name' => 'Dr. Lisa Anderson',
                'specialization' => 'Neurologist',
                'email' => 'lisa.anderson@healthcare.com',
                'phone' => '+1-555-0105',
                'bio' => 'Dr. Lisa Anderson is a board-certified neurologist with 14 years of experience in diagnosing and treating neurological disorders. She specializes in headaches, epilepsy, movement disorders, and neurodegenerative diseases. Dr. Anderson is known for her compassionate approach and thorough diagnostic evaluations.',
                'education' => 'MD, Mayo Clinic School of Medicine',
                'certifications' => ['Board Certified Neurologist', 'American Board of Psychiatry and Neurology'],
                'specialties' => ['Headache Management', 'Epilepsy', 'Movement Disorders', 'Neurodegenerative Diseases'],
                'hospital_affiliations' => ['Neurology Center', 'Brain Health Institute'],
                'languages' => ['English', 'German'],
                'years_of_experience' => 14,
                'consultation_fee' => 180.00,
                'available_hours' => 'Monday - Thursday: 9:00 AM - 5:00 PM',
                'status' => 'active',
            ],
            [
                'name' => 'Dr. Robert Taylor',
                'specialization' => 'Orthopedic Surgeon',
                'email' => 'robert.taylor@healthcare.com',
                'phone' => '+1-555-0106',
                'bio' => 'Dr. Robert Taylor is a renowned orthopedic surgeon with 20 years of experience in treating musculoskeletal conditions. He specializes in joint replacement, sports medicine, and trauma surgery. Dr. Taylor is committed to helping patients regain mobility and return to their active lifestyles.',
                'education' => 'MD, University of Pennsylvania School of Medicine',
                'certifications' => ['Board Certified Orthopedic Surgeon', 'American Board of Orthopedic Surgery'],
                'specialties' => ['Joint Replacement', 'Sports Medicine', 'Trauma Surgery', 'Arthroscopic Surgery'],
                'hospital_affiliations' => ['Orthopedic Center', 'Sports Medicine Clinic'],
                'languages' => ['English'],
                'years_of_experience' => 20,
                'consultation_fee' => 200.00,
                'available_hours' => 'Monday - Friday: 8:00 AM - 6:00 PM',
                'status' => 'active',
            ],
        ];

        foreach ($doctors as $doctor) {
            Doctor::create($doctor);
        }
    }
}



