<?php

// Orden correcto de migraciones
return [
    // Tablas base
    'database/migrations/2025_03_28_054427_create_users_table.php',
    'database/migrations/2025_03_28_054511_create_user_roles_table.php',
    'database/migrations/2025_03_28_054545_create_categories_table.php',
    
    // Tablas que dependen de users
    'database/migrations/2025_03_28_054624_create_courses_table.php',
    'database/migrations/2025_03_28_054656_create_course_videos_table.php',
    'database/migrations/2025_03_28_054737_create_course_materials_table.php',
    'database/migrations/2025_03_28_054926_create_enrollments_table.php',
    'database/migrations/2025_03_28_055010_create_ratings_table.php',
    'database/migrations/2025_03_28_055959_create_payments_table.php',
    'database/migrations/2025_03_28_060038_create_coupons_table.php',
    
    // Chat y mensajería
    'database/migrations/2025_03_28_065540_create_chat_tables.php',
    'database/migrations/2025_04_04_155339_create_messages_table.php',
    
    // Tablas adicionales
    'database/migrations/2025_03_28_120210_create_instructor_verifications_table.php',
    'database/migrations/2025_05_04_205524_create_parent_student_relations_table.php',
    'database/migrations/2025_05_14_085925_add_verified_by_to_parent_student_relations.php',
];
?> 