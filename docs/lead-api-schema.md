# Lead API Schema

## POST /api/leads

### Endpoint
```
POST /api/leads
Content-Type: application/json
```

### Request Body

#### Required Fields
- `practice_id` (integer) - The ID of the practice this lead belongs to
- `data` (object) - Flexible object containing lead information

#### Optional Fields
- `source` (string) - Where the lead came from. Valid values: `website`, `form`, `api`, `manual`, `referral`, `other`
- `lead_type` (string) - Custom label for categorizing the lead (e.g., "Website Inquiry", "Phone Call", "Referral"). Can also be passed inside the `data` object.

### Data Object Field Mapping

The `data` object should contain lead information. The system will automatically extract and normalize the following fields:

#### Name Fields (any of these formats will work)
- `first_name` / `firstName` / `fname` → Normalized to `first_name`
- `last_name` / `lastName` / `lname` → Normalized to `last_name`

#### Contact Fields (any of these formats will work)
- `email` / `email_address` / `emailAddress` → Normalized to `email`
- `phone` / `phone_number` / `phoneNumber` / `mobile` → Normalized and formatted to E.164 format (e.g., +15551234567)

#### Timestamp Fields (any of these formats will work)
- `created_at` / `createdAt` / `lead_created_at` → When the lead was created in the source system (defaults to current time if not provided)

#### Lead Type Field (any of these formats will work)
- `lead_type` / `leadType` / `type` → Custom label for categorizing the lead (e.g., "Website Inquiry", "Phone Call", "Referral")

#### Additional Data
Any other fields in the `data` object will be stored as-is and displayed in the "Additional Information" section of the lead details.

### Phone Number Formatting
- Phone numbers are automatically formatted to E.164 format (+15551234567) if they are valid US numbers
- Invalid phone numbers are stored as-is without errors
- Country code is assumed to be US

### Example Request

```json
{
  "practice_id": 123,
  "source": "website",
  "lead_type": "Website Inquiry",
  "data": {
    "first_name": "John",
    "last_name": "Doe",
    "email": "john.doe@example.com",
    "phone": "(555) 123-4567",
    "message": "I'm interested in hormone therapy",
    "interests": ["weight loss", "hormone therapy"],
    "preferred_contact_method": "email",
    "created_at": "2025-10-06T10:30:00Z"
  }
}
```

### Alternative Format (different field names)
```json
{
  "practice_id": 123,
  "source": "form",
  "data": {
    "firstName": "Jane",
    "lastName": "Smith",
    "emailAddress": "jane.smith@example.com",
    "phoneNumber": "555-987-6543",
    "comments": "Looking for wellness consultation",
    "age": 45,
    "insurance": true
  }
}
```

### Response

#### Success (201 Created)
```json
{
  "id": 456,
  "practice_id": 123,
  "first_name": "John",
  "last_name": "Doe",
  "email": "john.doe@example.com",
  "phone": "+15551234567",
  "source": "website",
  "status": "new",
  "lead_created_at": "2025-10-06T10:30:00.000000Z",
  "contacted_at": null,
  "created_at": "2025-10-06T15:45:23.000000Z",
  "updated_at": "2025-10-06T15:45:23.000000Z",
  "data": {
    "first_name": "John",
    "last_name": "Doe",
    "email": "john.doe@example.com",
    "phone": "(555) 123-4567",
    "message": "I'm interested in hormone therapy",
    "interests": ["weight loss", "hormone therapy"],
    "preferred_contact_method": "email",
    "created_at": "2025-10-06T10:30:00Z"
  },
  "practice": {
    "id": 123,
    "name": "ABC Wellness Clinic",
    "address": "123 Main St, Denver, CO 80202",
    ...
  }
}
```

#### Validation Error (422 Unprocessable Entity)
```json
{
  "message": "Validation failed",
  "errors": {
    "email": ["The email must be a valid email address."],
    "first_name": ["The first name must not be greater than 255 characters."]
  }
}
```

#### Error (400 Bad Request)
```json
{
  "message": "The practice_id field is required.",
  "errors": {
    "practice_id": ["The practice_id field is required."]
  }
}
```

### Validation Rules

Fields extracted from `data` object are validated as follows:
- `email` - Must be a valid email format (nullable)
- `phone` - Must be a string (nullable)
- `first_name` - String, max 255 characters (nullable)
- `last_name` - String, max 255 characters (nullable)

### Automatic Email Notifications

When a lead is created successfully:
- Emails are automatically sent to all marketing emails associated with the practice
- Email delivery is logged and can be viewed in the lead details
- In non-production environments, emails are sent to the configured test email address instead of real marketing emails

### Lead Status Flow

Default lead status is `new`. Available statuses:
- `new` - Initial state
- `contacted` - Lead has been contacted
- `qualified` - Lead is qualified
- `converted` - Lead became a customer
- `rejected` - Lead was rejected

### Notes

- The entire `data` object is stored for reference, so you can include any additional fields needed
- Fields not in the standard mapping (first_name, last_name, email, phone, created_at) will appear in the "Additional Information" section
- Arrays are displayed as comma-separated values
- Booleans are displayed as "Yes/No"
- Lead creation will succeed even if email notifications fail
