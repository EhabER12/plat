# Admin Notification System Documentation

## Banned Word Detection and Notification System

The platform includes an automatic system to detect banned words in messages exchanged between students and instructors. When potentially problematic content is detected, administrators are automatically notified.

### How It Works

1. **Message Sending**: When a student or instructor sends a message, the system automatically checks the content for banned words.

2. **Content Filtering**: The `ContentFilterService` processes the message content and detects any banned words based on predefined categories and severity levels.

3. **Administrator Notification**: If banned words are detected, an automatic notification is sent to the admin notification panel with details about the flagged content.

4. **Message Filtering**: Depending on the configuration, messages with banned content may be filtered automatically, replacing the banned words with asterisks or custom replacement text.

### Banned Word Categories

The system supports different categories of banned words:

- **Profanity**: Offensive language or swear words
- **Contact Information**: Phone numbers, email addresses, external social media links
- **Platform Bypass**: Attempts to communicate outside the platform or arrange direct payments
- **General**: Any other content that should be restricted

### Severity Levels

Each banned word is assigned a severity level from 1 to 5:

- **Level 1**: Minor concern
- **Level 2**: Moderate concern
- **Level 3**: Significant concern
- **Level 4**: Major concern
- **Level 5**: Critical issue requiring immediate attention

Administrators can filter notifications based on severity level to prioritize the most critical issues.

### Viewing Notifications

Administrators can view all notifications in the Admin Notification Panel:

1. Navigate to **Admin Dashboard > Notifications**
2. Use the filter options to show specific types of notifications or severity levels
3. Click on a notification to view its details, including the full message content and the flagged words

### Managing Banned Words

Administrators can manage the list of banned words:

1. Navigate to **Admin Dashboard > Settings > Banned Words**
2. Add, edit, or remove banned words
3. Set severity levels and replacement text for each banned word
4. Enable/disable specific banned words

### Testing the System

To test the banned word detection system:

1. Run the seeder command to populate the database with sample banned words:
   ```
   php artisan banned-words:seed
   ```

2. Log in as a student or instructor and send a message containing one of the banned words
3. Log in as an administrator and check the notification panel for the new notification

### Technical Implementation

The banned word detection system is implemented through several components:

- `ContentFilterService`: Processes message content and detects banned words
- `AdminNotificationService`: Creates notifications for administrators
- `BannedWord` model: Stores the list of banned words with their properties
- `AdminNotification` model: Stores notifications for administrators
- `MessagesController`: Integrates with the content filtering system during message sending

Messages with banned content are automatically flagged in the database, making it easy to track and monitor potentially problematic conversations. 