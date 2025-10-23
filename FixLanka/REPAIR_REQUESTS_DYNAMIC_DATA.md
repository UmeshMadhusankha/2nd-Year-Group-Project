# Repair Requests Dynamic Data Integration

## Summary
Successfully integrated real database data into the repair requests page, replacing all hardcoded dummy data with dynamic content fetched from the `JobRequest` table.

## Changes Made

### 1. **Backend PHP Integration** (`repair-requests.php`)

#### Database Connection
```php
- Added database connection at the top of the file
- Initialized JobRequestModel
- Fetch public and direct requests from database
```

#### Dynamic Statistics
- **Public Requests Count**: Real count from database
- **Direct Requests Count**: Real count from database  
- **Pending Response**: Count of pending requests
- **This Month**: Count of requests created this month

#### Helper Functions Added
```php
- timeAgo(): Converts timestamps to "2 hours ago" format
- getInitials(): Generates initials from first and last name
```

### 2. **Public Requests Cards** (Dynamic)

Now displays actual data from database:
- **Title**: From `JobRequest.title`
- **Category**: From `Category.name` (e.g., "Plumbing", "Electrical")
- **District**: From `JobRequest.district` (NOT "Colombo 07" - shows actual district)
- **Urgency**: From `JobRequest.urgency` ("urgent" or "medium")
- **Customer Name**: From `User.f_name` and `User.l_name`
- **Description**: From `JobRequest.description`
- **Date Needed**: From `JobRequest.finish_date`
- **Posted**: Calculated from `JobRequest.dateCreated`
- **Attachments**: From `JobRequest.photos` (comma-separated)

### 3. **Direct Requests Table** (Dynamic)

Now displays actual data with:
- Request ID, Title, Category
- Customer name and email
- Date received
- Dynamic status badges (pending, accepted, in_progress, completed, cancelled)
- Conditional action buttons based on status:
  - **Pending**: View, Accept, Reject buttons
  - **Accepted/In Progress**: View Contract link
  - **Other statuses**: View button only

### 4. **Database Model Enhancements** (`JobRequestModel.php`)

Added two new methods:

#### `getAllPublicRequests($filters)`
```php
- Fetches all pending requests available to companies
- Filters by service_provider_type ('company' or 'both')
- Supports filtering by category, urgency, district
- Returns with user and category information
- Ordered by urgency and date created
```

#### `getDirectRequestsForCompany($companyId)`
```php
- Fetches direct requests for specific company
- Includes all request statuses
- Returns with user and category information
```

### 5. **CSS Updates** (`repair-requests.css`)

#### Priority Badges
- `.priority-badge.urgent` - Red/orange gradient (same as high)
- `.priority-badge.medium` - Yellow/warning color
- `.priority-badge.low` - Blue gradient

#### Status Badges
- `.status-badge.pending` - Yellow/warning
- `.status-badge.accepted` - Green
- `.status-badge.in_progress` - Blue
- `.status-badge.completed` - Green
- `.status-badge.cancelled` - Red
- `.status-badge.rejected` - Red

### 6. **JavaScript Updates** (`repair-requests.js`)

#### Enhanced `getRequestData()` Function
- Now extracts data from DOM elements first
- Falls back to API call if needed
- Uses actual data from the page instead of hardcoded values

## Data Flow

```
Database (JobRequest table)
    ↓
JobRequestModel::getAllPublicRequests()
    ↓
repair-requests.php (PHP variables)
    ↓
HTML rendering with foreach loops
    ↓
JavaScript interaction (quotations, view details)
```

## Key Improvements

### ✅ Real Data Display
- All information comes from database
- No hardcoded dummy data
- District names show actual districts (not "Colombo 07")
- Category shows job category from Category table

### ✅ Dynamic Filtering
- Filter by category (from database)
- Filter by urgency (urgent/medium)
- Filter by district (actual districts)
- Filters apply to real data

### ✅ Empty States
- Shows message when no requests available
- Graceful handling of missing data
- Professional empty state design

### ✅ Security
- All output uses `htmlspecialchars()` to prevent XSS
- Prepared statements in model prevent SQL injection
- Proper error handling

### ✅ User Experience
- Real customer initials from actual names
- Accurate time ago calculations
- Dynamic attachment counts
- Proper status indicators

## Database Schema Used

### Tables
1. **JobRequest** - Main repair requests
2. **User** - Customer information
3. **Category** - Service categories (Plumbing, Electrical, etc.)

### Key Fields
```sql
JobRequest:
- request_id (PK)
- title
- description
- district (shows actual district name)
- urgency ('urgent' or 'medium')
- category_id (FK to Category)
- user_id (FK to User)
- finish_date
- dateCreated
- photos (comma-separated)
- status (pending, accepted, in_progress, completed, cancelled)
```

## Testing Checklist

- [x] Public requests load from database
- [x] Direct requests load from database
- [x] Statistics show real counts
- [x] District names display correctly (not "Colombo 07")
- [x] Category names show from Category table
- [x] Customer names and emails display correctly
- [x] Initials generate correctly
- [x] Time ago calculations work
- [x] Attachment counts display properly
- [x] Empty states show when no data
- [x] Urgency badges styled correctly (urgent/medium)
- [x] Status badges show all states
- [x] Filter functionality works with real data
- [x] Quotation modal opens with request data
- [x] View details extracts data from DOM

## Future Enhancements

1. **Pagination**: Add pagination for large datasets
2. **Real-time Updates**: WebSocket for live request updates
3. **Image Previews**: Show actual photos from uploads folder
4. **Distance Calculation**: Show distance from company location
5. **Company Matching**: Filter requests by company specialization
6. **Quotation Submission**: Save quotations to database
7. **Notification System**: Alert company of new requests

## Files Modified

1. **repair-requests.php** (Lines 1-280)
   - Added PHP backend logic
   - Dynamic data fetching
   - Loop through requests

2. **JobRequestModel.php** (Lines 107-235)
   - Added `getAllPublicRequests()`
   - Added `getDirectRequestsForCompany()`

3. **repair-requests.css** (Lines 638-990)
   - Updated priority badges
   - Added status badge styles

4. **repair-requests.js** (Lines 310-430)
   - Enhanced `getRequestData()` function

## Conclusion

The repair requests page now displays **100% real data** from the database. All dummy data has been removed. The page shows:
- Actual district names (not fake addresses)
- Real job categories from the Category table
- Live customer information
- Dynamic status and urgency indicators
- Proper time calculations
- Actual attachment information

The system is now ready for production use with real user data.
