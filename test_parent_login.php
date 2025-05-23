<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test parent login flow
$user = DB::table('users')->where('email', 'ehabpar@gmail.com')->first();
if ($user) {
    echo "✅ User found: {$user->name} (ID: {$user->user_id})" . PHP_EOL;
    
    // Check roles
    $roles = DB::table('user_roles')->where('user_id', $user->user_id)->pluck('role')->toArray();
    echo "✅ Roles: " . implode(', ', $roles) . PHP_EOL;
    
    // Check if has parent role
    $hasParentRole = in_array('parent', $roles);
    echo ($hasParentRole ? "✅" : "❌") . " Has parent role: " . ($hasParentRole ? "Yes" : "No") . PHP_EOL;
    
    // Check parent-student relations
    $relations = DB::table('parent_student_relations')->where('parent_id', $user->user_id)->get();
    echo "📊 Parent relations count: " . count($relations) . PHP_EOL;
    
    // Check if has verified students
    $hasVerifiedStudents = DB::table('parent_student_relations')
        ->where('parent_id', $user->user_id)
        ->where('verification_status', 'approved')
        ->exists();
    echo ($hasVerifiedStudents ? "✅" : "⚠️") . " Has verified students: " . ($hasVerifiedStudents ? "Yes" : "No") . PHP_EOL;
    
    // Expected redirect
    if ($hasVerifiedStudents) {
        echo "🎯 Expected redirect: parent.dashboard" . PHP_EOL;
    } else {
        echo "🎯 Expected redirect: parent.waiting-approval" . PHP_EOL;
    }
    
} else {
    echo "❌ User not found" . PHP_EOL;
}
