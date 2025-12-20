<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HealthPlanController;
use App\Http\Controllers\AIConsultationController;
use App\Http\Controllers\NutritionController;
use App\Http\Controllers\HealthTrackingController;
use App\Http\Controllers\HealthJournalController;
use App\Http\Controllers\ProfileController;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/contact', [HomeController::class, 'contact'])->name('contact.submit');

// User authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password reset routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Health Plans routes (authenticated users only)
Route::middleware('auth')->group(function () {
    Route::get('/health-plans', [HealthPlanController::class, 'index'])->name('health-plans.index');
    Route::get('/health-plans/profile', [HealthPlanController::class, 'createProfile'])->name('health-plans.profile');
    Route::post('/health-plans/profile', [HealthPlanController::class, 'storeProfile'])->name('health-plans.profile.store');
    Route::post('/health-plans/generate', [HealthPlanController::class, 'generatePlan'])->name('health-plans.generate');
    Route::get('/health-plans/{id}', [HealthPlanController::class, 'show'])->name('health-plans.show');
    Route::post('/health-plans/{id}/progress', [HealthPlanController::class, 'updateProgress'])->name('health-plans.progress');
    Route::post('/health-plans/{id}/status', [HealthPlanController::class, 'updateStatus'])->name('health-plans.status');
    
    // AI Consultation routes
    Route::get('/ai-consultation', [AIConsultationController::class, 'index'])->name('ai-consultation.index');
    Route::post('/ai-consultation/start', [AIConsultationController::class, 'startSession'])->name('ai-consultation.start');
    Route::post('/ai-consultation/send', [AIConsultationController::class, 'sendMessage'])->name('ai-consultation.send');
    Route::get('/ai-consultation/history/{sessionId}', [AIConsultationController::class, 'getHistory'])->name('ai-consultation.history');
    Route::get('/ai-consultation/stats', [AIConsultationController::class, 'getStats'])->name('ai-consultation.stats');
    
    // Nutrition routes
    Route::get('/nutrition', [NutritionController::class, 'index'])->name('nutrition.index');
    Route::post('/nutrition/generate', [NutritionController::class, 'generatePlan'])->name('nutrition.generate');
    Route::get('/nutrition/{id}', [NutritionController::class, 'show'])->name('nutrition.show');
    
    // Health Tracking routes
    Route::get('/health-tracking', [HealthTrackingController::class, 'index'])->name('health-tracking.index');
    Route::post('/health-tracking/metric', [HealthTrackingController::class, 'storeMetric'])->name('health-tracking.metric.store');
    Route::get('/health-tracking/metrics/data', [HealthTrackingController::class, 'getMetricsData'])->name('health-tracking.metrics.data');
    Route::post('/health-tracking/reminder', [HealthTrackingController::class, 'storeReminder'])->name('health-tracking.reminder.store');
    Route::post('/health-tracking/reminder/{id}', [HealthTrackingController::class, 'updateReminder'])->name('health-tracking.reminder.update');
    Route::delete('/health-tracking/reminder/{id}', [HealthTrackingController::class, 'deleteReminder'])->name('health-tracking.reminder.delete');
    
    // Health Journal routes
    Route::get('/health-journal', [HealthJournalController::class, 'index'])->name('health-journal.index');
    Route::post('/health-journal', [HealthJournalController::class, 'store'])->name('health-journal.store');
    Route::get('/health-journal/{id}', [HealthJournalController::class, 'show'])->name('health-journal.show');
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::put('/profile/settings', [ProfileController::class, 'updateSettings'])->name('profile.settings.update');
});

// Admin authentication routes
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Admin routes
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
Route::get('/admin/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
Route::get('/admin/users/{id}', [AdminController::class, 'showUser'])->name('admin.users.show');
Route::get('/admin/users/{id}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
Route::put('/admin/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
Route::post('/admin/users/{id}/toggle-lock', [AdminController::class, 'toggleLockUser'])->name('admin.users.toggle-lock');
Route::get('/admin/medical-content', [AdminController::class, 'medicalContent'])->name('admin.medical-content');
Route::get('/admin/medical-content/knowledge-base/create', [AdminController::class, 'createKnowledgeBase'])->name('admin.medical-content.knowledge-base.create');
Route::post('/admin/medical-content/knowledge-base', [AdminController::class, 'storeKnowledgeBase'])->name('admin.medical-content.knowledge-base.store');
Route::get('/admin/medical-content/knowledge-base/{id}/edit', [AdminController::class, 'editKnowledgeBase'])->name('admin.medical-content.knowledge-base.edit');
Route::put('/admin/medical-content/knowledge-base/{id}', [AdminController::class, 'updateKnowledgeBase'])->name('admin.medical-content.knowledge-base.update');
Route::delete('/admin/medical-content/knowledge-base/{id}', [AdminController::class, 'deleteKnowledgeBase'])->name('admin.medical-content.knowledge-base.delete');
Route::get('/admin/medical-content/faq/create', [AdminController::class, 'createFAQ'])->name('admin.medical-content.faq.create');
Route::post('/admin/medical-content/faq', [AdminController::class, 'storeFAQ'])->name('admin.medical-content.faq.store');
Route::get('/admin/medical-content/faq/{id}/edit', [AdminController::class, 'editFAQ'])->name('admin.medical-content.faq.edit');
Route::put('/admin/medical-content/faq/{id}', [AdminController::class, 'updateFAQ'])->name('admin.medical-content.faq.update');
Route::delete('/admin/medical-content/faq/{id}', [AdminController::class, 'deleteFAQ'])->name('admin.medical-content.faq.delete');
Route::get('/admin/medical-content/template/create', [AdminController::class, 'createTemplate'])->name('admin.medical-content.template.create');
Route::post('/admin/medical-content/template', [AdminController::class, 'storeTemplate'])->name('admin.medical-content.template.store');
Route::get('/admin/medical-content/template/{id}/edit', [AdminController::class, 'editTemplate'])->name('admin.medical-content.template.edit');
Route::put('/admin/medical-content/template/{id}', [AdminController::class, 'updateTemplate'])->name('admin.medical-content.template.update');
Route::delete('/admin/medical-content/template/{id}', [AdminController::class, 'deleteTemplate'])->name('admin.medical-content.template.delete');
Route::get('/admin/medical-content/chat-logs/{id}', [AdminController::class, 'getChatLogDetails'])->name('admin.medical-content.chat-logs.details');
Route::get('/admin/medical-content/chat-logs', [AdminController::class, 'chatLogs'])->name('admin.medical-content.chat-logs');
Route::get('/admin/ai-management', [AdminController::class, 'aiManagement'])->name('admin.ai-management');
Route::post('/admin/ai-management/config', [AdminController::class, 'updateAIConfig'])->name('admin.ai-management.config.update');
Route::get('/admin/ai-management/scenarios/create', [AdminController::class, 'createTrainingScenario'])->name('admin.ai-management.scenarios.create');
Route::post('/admin/ai-management/scenarios', [AdminController::class, 'storeTrainingScenario'])->name('admin.ai-management.scenarios.store');
Route::get('/admin/ai-management/scenarios/{id}/edit', [AdminController::class, 'editTrainingScenario'])->name('admin.ai-management.scenarios.edit');
Route::put('/admin/ai-management/scenarios/{id}', [AdminController::class, 'updateTrainingScenario'])->name('admin.ai-management.scenarios.update');
Route::delete('/admin/ai-management/scenarios/{id}', [AdminController::class, 'deleteTrainingScenario'])->name('admin.ai-management.scenarios.delete');
Route::get('/admin/ai-management/metrics', [AdminController::class, 'viewMetrics'])->name('admin.ai-management.metrics');
Route::get('/admin/ai-management/feedback', [AdminController::class, 'reviewFeedback'])->name('admin.ai-management.feedback');
Route::get('/admin/analytics', [AdminController::class, 'analytics'])->name('admin.analytics');
Route::get('/admin/security', [AdminController::class, 'security'])->name('admin.security');
Route::get('/admin/system', [AdminController::class, 'system'])->name('admin.system');
Route::post('/admin/system/restart-api', [AdminController::class, 'restartApi'])->name('admin.system.restart-api');
Route::post('/admin/system/notifications', [AdminController::class, 'updateNotifications'])->name('admin.system.notifications.update');
Route::get('/admin/system/logs', [AdminController::class, 'systemLogs'])->name('admin.system.logs');