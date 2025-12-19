# Quotation Form Database Schema

## Overview
This document defines the database schema for storing quotations submitted by companies for repair requests. The form is currently working in demo mode and will need to be connected to this database structure.

## Database Table: `Quotation`

```sql
CREATE TABLE Quotation (
    quotation_id INT PRIMARY KEY AUTO_INCREMENT,
    request_id INT NOT NULL,
    company_id INT NOT NULL,
    
    -- Quotation Details
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    
    -- Pricing Breakdown
    labor_cost DECIMAL(10, 2) NOT NULL,
    material_cost DECIMAL(10, 2) NOT NULL,
    transport_cost DECIMAL(10, 2) DEFAULT 0.00,
    other_cost DECIMAL(10, 2) DEFAULT 0.00,
    total_price DECIMAL(10, 2) NOT NULL,
    
    -- Timeline
    estimated_start_date DATE NOT NULL,
    estimated_completion_date DATE NOT NULL,
    estimated_duration INT NOT NULL COMMENT 'Duration in days',
    
    -- Terms & Conditions
    payment_terms ENUM('full_advance', '50_50', '30_70', 'milestone', 'on_completion') NOT NULL,
    warranty_period ENUM('no_warranty', '1_month', '3_months', '6_months', '1_year', '2_years') NOT NULL,
    terms_conditions TEXT,
    validity_period INT NOT NULL DEFAULT 30 COMMENT 'Validity in days',
    
    -- Status & Tracking
    status ENUM('pending', 'accepted', 'rejected', 'expired') DEFAULT 'pending',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Keys
    FOREIGN KEY (request_id) REFERENCES JobRequest(request_id) ON DELETE CASCADE,
    FOREIGN KEY (company_id) REFERENCES Company(company_id) ON DELETE CASCADE,
    
    -- Indexes
    INDEX idx_request (request_id),
    INDEX idx_company (company_id),
    INDEX idx_status (status),
    INDEX idx_submitted (submitted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## Form Field Mappings

| Form Field ID | Database Column | Data Type | Required | Description |
|--------------|-----------------|-----------|----------|-------------|
| `request-id` | `request_id` | INT | Yes | Hidden field - ID of the repair request |
| `quotation-title` | `title` | VARCHAR(255) | Yes | Title of the quotation |
| `service-description` | `description` | TEXT | Yes | Detailed service description |
| `labor-cost` | `labor_cost` | DECIMAL(10,2) | Yes | Labor/service charges |
| `material-cost` | `material_cost` | DECIMAL(10,2) | Yes | Cost of materials |
| `transport-cost` | `transport_cost` | DECIMAL(10,2) | No | Transportation charges |
| `other-cost` | `other_cost` | DECIMAL(10,2) | No | Miscellaneous charges |
| `total-price` | `total_price` | DECIMAL(10,2) | Yes | Auto-calculated total |
| `estimated-start-date` | `estimated_start_date` | DATE | Yes | Project start date |
| `estimated-completion-date` | `estimated_completion_date` | DATE | Yes | Project end date |
| `estimated-duration` | `estimated_duration` | INT | Yes | Duration in days |
| `payment-terms` | `payment_terms` | ENUM | Yes | Payment structure |
| `warranty-period` | `warranty_period` | ENUM | Yes | Warranty duration |
| `terms-conditions` | `terms_conditions` | TEXT | No | Additional terms |
| `validity-period` | `validity_period` | INT | Yes | Quote validity (days) |

## JavaScript Form Data Structure

```javascript
const quotationData = {
    request_id: "REQ-2025-001",           // From hidden field
    title: "HVAC - AC Repair",             // User input
    description: "Complete AC repair...",  // User input
    labor_cost: 15000.00,                  // User input
    material_cost: 8000.00,                // User input
    transport_cost: 2000.00,               // User input (optional)
    other_cost: 1000.00,                   // User input (optional)
    total_price: 26000.00,                 // Auto-calculated
    estimated_start_date: "2025-11-01",    // User input
    estimated_completion_date: "2025-11-05", // User input
    estimated_duration: 5,                 // Auto-calculated or user input
    payment_terms: "50_50",                // User selection
    warranty_period: "6_months",           // User selection
    terms_conditions: "Additional terms...", // User input (optional)
    validity_period: 30,                   // User selection
    status: "pending",                     // Default value
    submitted_at: "2025-10-23T10:30:00Z"  // Auto-generated
};
```

## Form Features

### 1. **Automatic Calculations**
- **Total Cost**: Automatically sums labor + material + transport + other costs
- **Duration**: Auto-calculated from start and end dates
- **End Date**: Auto-updated when duration changes

### 2. **Validation Rules**
- All required fields must be filled
- Total price must be > 0
- Start date cannot be in the past
- End date must be after start date
- Agreement checkbox must be checked

### 3. **Real-time Features**
- Live cost calculation as user types
- Date validation and auto-calculation
- Currency formatting (LKR)
- Form state preservation until submission

### 4. **User Experience**
- Pre-filled quotation title from request details
- Request summary displayed at top
- Clear section organization
- Loading state during submission
- Success/error notifications

## Backend Integration (To be implemented)

### API Endpoint: POST `/api/quotations/submit`

```php
<?php
// Example backend handler (to be created later)

require_once 'config/database.php';

header('Content-Type: application/json');

try {
    // Get JSON data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $required = ['request_id', 'title', 'description', 'labor_cost', 
                'material_cost', 'total_price', 'estimated_start_date', 
                'estimated_completion_date', 'estimated_duration', 
                'payment_terms', 'warranty_period', 'validity_period'];
    
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    // Get company ID from session
    session_start();
    $company_id = $_SESSION['company_id'] ?? null;
    
    if (!$company_id) {
        throw new Exception("User not authenticated");
    }
    
    // Insert into database
    $db = new Database();
    $pdo = $db->connect();
    
    $stmt = $pdo->prepare("
        INSERT INTO Quotation (
            request_id, company_id, title, description,
            labor_cost, material_cost, transport_cost, other_cost, total_price,
            estimated_start_date, estimated_completion_date, estimated_duration,
            payment_terms, warranty_period, terms_conditions, validity_period,
            status, submitted_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
    ");
    
    $result = $stmt->execute([
        $data['request_id'],
        $company_id,
        $data['title'],
        $data['description'],
        $data['labor_cost'],
        $data['material_cost'],
        $data['transport_cost'] ?? 0,
        $data['other_cost'] ?? 0,
        $data['total_price'],
        $data['estimated_start_date'],
        $data['estimated_completion_date'],
        $data['estimated_duration'],
        $data['payment_terms'],
        $data['warranty_period'],
        $data['terms_conditions'] ?? '',
        $data['validity_period']
    ]);
    
    if ($result) {
        $quotation_id = $pdo->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'message' => 'Quotation submitted successfully',
            'quotation_id' => $quotation_id
        ]);
    } else {
        throw new Exception("Failed to insert quotation");
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
```

## Frontend JavaScript (Already Implemented)

The form submission is currently in demo mode. To connect to backend:

1. Uncomment the `fetch()` code in `submitQuotation()` function
2. Update the API endpoint URL
3. Add proper error handling
4. Add session/authentication headers if needed

```javascript
// Located in: assets/javascript/company/repair-requests.js
// Function: submitQuotation()

fetch('/api/quotations/submit', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify(quotationData)
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        showNotification('Quotation submitted successfully!', 'success');
        closeQuotationModal();
        // Optional: Refresh the page or update UI
    } else {
        showNotification('Error: ' + data.message, 'error');
    }
})
.catch(error => {
    showNotification('Error submitting quotation', 'error');
    console.error('Error:', error);
});
```

## Additional Tables (Optional)

### Quotation History
Track changes to quotations:
```sql
CREATE TABLE QuotationHistory (
    history_id INT PRIMARY KEY AUTO_INCREMENT,
    quotation_id INT NOT NULL,
    action ENUM('created', 'updated', 'accepted', 'rejected', 'expired'),
    performed_by INT NOT NULL,
    performed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    changes JSON,
    FOREIGN KEY (quotation_id) REFERENCES Quotation(quotation_id) ON DELETE CASCADE
);
```

### Quotation Attachments
Store additional documents:
```sql
CREATE TABLE QuotationAttachment (
    attachment_id INT PRIMARY KEY AUTO_INCREMENT,
    quotation_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type VARCHAR(50),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (quotation_id) REFERENCES Quotation(quotation_id) ON DELETE CASCADE
);
```

## Testing Checklist

Before database integration:
- [x] Form opens with request details
- [x] All required fields validate
- [x] Cost calculations work in real-time
- [x] Date validation prevents past dates
- [x] Duration auto-calculates
- [x] Agreement checkbox required
- [x] Form submits successfully (demo mode)
- [x] Success notification displays
- [x] Form resets after submission

After database integration:
- [ ] Data saves to Quotation table
- [ ] Foreign keys validate correctly
- [ ] Company ID from session works
- [ ] Error handling for database failures
- [ ] Success/error responses work
- [ ] Quotation appears in company's quotations list
- [ ] User receives notification
- [ ] Duplicate submissions prevented

## Security Considerations

1. **SQL Injection**: Use prepared statements (already implemented in example)
2. **XSS**: Sanitize all user inputs before displaying
3. **CSRF**: Add CSRF token to form
4. **Authentication**: Verify company session before submission
5. **Authorization**: Ensure company can only submit for accessible requests
6. **Data Validation**: Server-side validation of all fields
7. **Rate Limiting**: Prevent spam quotation submissions

## Future Enhancements

1. **Draft Saving**: Allow companies to save quotations as drafts
2. **Templates**: Create reusable quotation templates
3. **Attachments**: Upload supporting documents
4. **Revisions**: Allow quotation updates before acceptance
5. **Comparison**: User can compare multiple quotations
6. **Notifications**: Email/SMS notifications on submission
7. **Analytics**: Track quotation acceptance rates
8. **PDF Export**: Generate PDF quotations

## Conclusion

The quotation form is fully functional in demo mode with realistic business logic. All fields map to a proper database schema. When ready to connect:

1. Create the `Quotation` table using the SQL above
2. Create the backend API endpoint
3. Uncomment the fetch code in JavaScript
4. Test thoroughly with real data
5. Deploy to production

The form includes automatic calculations, validation, and a professional user experience ready for real-world use.
