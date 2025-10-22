# Advertisement Management System Documentation

## Overview
The Advertisement Management System allows companies to create, submit, and manage advertising campaigns on the FixLanka platform. All advertisements must be approved by moderators before going live.

## Features Implemented

### 1. **Advertisement Dashboard**
- **Stats Overview**
  - Active Ads count
  - Pending Approval count
  - Scheduled Ads count
  - Total Views tracking

- **Filter System**
  - All Ads
  - Active
  - Pending Approval
  - Scheduled
  - Rejected
  - Expired

### 2. **Advertisement Types**

#### Banner Ads (Recommended)
- Static image advertisements
- Supported formats: JPG, PNG, GIF
- Recommended size: 1200x628px
- Maximum file size: 5MB
- Base rate: LKR 500/day

#### Video Ads (Premium)
- Video advertisements
- Supported formats: MP4, WebM
- Maximum duration: 30 seconds
- Maximum file size: 50MB
- Rate: Base + LKR 300/day

#### Carousel Ads
- Multiple images in slideshow format
- Up to 5 images per carousel
- Same specifications as banner ads
- Rate: Base + LKR 150/day

### 3. **5-Step Creation Process**

#### Step 1: Ad Type Selection
- Choose between Banner, Video, or Carousel
- Visual card-based selection
- Clear pricing indication

#### Step 2: Upload Media
- Drag & drop file upload
- Click to browse option
- Live preview of uploaded media
- Title and description input
- Media specifications guide

#### Step 3: Target & Details
- Service category selection
- Target audience specification
- Call-to-action URL (optional)
- CTA button text customization

#### Step 4: Schedule
Two scheduling options:
1. **Publish Immediately**
   - Goes live after moderator approval
   - Starts as soon as approved

2. **Schedule for Later**
   - Set specific start date/time
   - Set specific end date/time
   - Campaign duration selector
   - Pre-built duration options (1 week, 2 weeks, 1 month, etc.)

#### Step 5: Budget & Payment
- Cost breakdown
- Priority placement option (+LKR 200/day)
- Total cost calculation
- Payment method selection
- Terms & conditions agreement

### 4. **Advertisement Status System**

#### Active
- Currently running advertisements
- Displaying to users
- Tracking views and clicks

#### Pending Approval
- Submitted to moderators
- Awaiting review (24-48 hours)
- Can be edited or cancelled

#### Scheduled
- Approved by moderators
- Set to start on future date
- Can be edited before start date

#### Rejected
- Not approved by moderators
- Reason for rejection provided
- Can be edited and resubmitted

#### Expired
- Campaign end date reached
- Archived for reference
- Performance metrics available

### 5. **Moderator Approval Workflow**

```
Company Submission → Moderator Queue → Review Process → Decision

Approval Path:
✓ Approved → Scheduled/Active (based on schedule)

Rejection Path:
✗ Rejected → Company notified → Can edit & resubmit
```

#### Moderator Review Criteria:
1. Content appropriateness
2. Compliance with platform policies
3. Accurate service representation
4. No misleading claims
5. Professional media quality
6. Proper grammar and spelling

### 6. **Ad Card Information Display**

Each advertisement card shows:
- Media preview (image/video thumbnail)
- Media type badge
- Title and description
- Status badge
- Start and end dates
- Performance metrics (views, clicks)
- Action buttons (View, Edit, Pause, Delete)

### 7. **Pricing Structure**

| Component | Cost |
|-----------|------|
| Base Rate | LKR 500/day |
| Video Premium | +LKR 300/day |
| Carousel Add-on | +LKR 150/day |
| Priority Placement | +LKR 200/day |

**Example Calculations:**

1. Basic Banner (30 days):
   - 30 days × LKR 500 = LKR 15,000

2. Video Ad with Priority (30 days):
   - Base: 30 × LKR 500 = LKR 15,000
   - Video: 30 × LKR 300 = LKR 9,000
   - Priority: 30 × LKR 200 = LKR 6,000
   - **Total: LKR 30,000**

### 8. **Advertisement Actions**

#### For Companies:
- **Create** - Submit new advertisement
- **Edit** - Modify pending/scheduled ads
- **View Details** - See full ad information
- **Pause** - Temporarily stop active ad
- **Resume** - Restart paused ad
- **Delete** - Remove advertisement
- **Cancel** - Cancel pending approval
- **Save Draft** - Save work in progress

#### For Moderators:
- **Review** - Examine submitted ads
- **Approve** - Allow ad to go live
- **Reject** - Deny ad with reason
- **Request Changes** - Ask for modifications
- **View History** - See company's ad history

### 9. **Analytics & Metrics**

Track important KPIs:
- **Total Views** - How many times ad was seen
- **Click-Through Rate** - Percentage of viewers who clicked
- **Engagement Rate** - Overall interaction metrics
- **Cost per View** - ROI calculation
- **Cost per Click** - CPC metric
- **Conversion Rate** - If conversion tracking enabled

### 10. **Save as Draft Feature**

Benefits:
- Save incomplete submissions
- Return later to complete
- No time pressure
- Automatically saved periodically
- Draft expiration: 30 days

## Technical Implementation

### Files Created:

1. **PHP Page**
   - `FixLanka/views/company/advertisements.php`
   - Main advertisement management interface
   - Lists all ads with filtering
   - Create/edit modal interface

2. **CSS Stylesheet**
   - `FixLanka/assets/css/company/advertisements.css`
   - Complete styling for ad management
   - Responsive design
   - Modal and form styles

3. **Sidebar Integration**
   - Updated `FixLanka/views/company/sidebar.php`
   - Added "Advertisements" menu item
   - Icon: fa-bullhorn
   - Positioned between Contracts and Support

## User Flow Diagrams

### Creating an Advertisement:
```
1. Click "Create New Advertisement"
   ↓
2. Select Ad Type (Banner/Video/Carousel)
   ↓
3. Upload Media & Enter Details
   ↓
4. Set Target Audience & CTA
   ↓
5. Choose Schedule (Immediate/Scheduled)
   ↓
6. Review Cost & Select Payment
   ↓
7. Submit for Approval
   ↓
8. Moderator Reviews (24-48 hrs)
   ↓
9. Approved → Goes Live (or scheduled)
   ↓
10. Monitor Performance
```

### Moderator Review Process:
```
1. View Pending Ads Queue
   ↓
2. Select Ad to Review
   ↓
3. Check Content & Media
   ↓
4. Verify Compliance
   ↓
5. Decision:
   - Approve → Ad goes live/scheduled
   - Reject → Company notified with reason
   - Request Changes → Company can modify
```

## Form Validation Rules

### Required Fields:
- Ad Type (radio selection)
- Media File (image/video)
- Ad Title (min 10 characters)
- Description (min 50 characters)
- Service Category
- Schedule Type
- Payment Method
- Terms Agreement (checkbox)

### Optional Fields:
- Target Audience (defaults to "All Users")
- CTA Link URL
- CTA Button Text
- Priority Placement
- Custom duration dates

## Payment Integration

### Payment Methods Supported:
1. **Credit/Debit Card**
   - Visa, Mastercard, Amex
   - Instant processing
   
2. **Bank Transfer**
   - Manual verification required
   - 1-2 business days
   
3. **Digital Wallet**
   - eZ Cash, mCash
   - Instant processing

### Payment Processing:
- Payment captured upon submission
- Refund policy: 7 days if not approved
- Auto-renewal option available
- Invoice generated automatically

## Notifications System

### Company Notifications:
- Ad submitted successfully
- Ad approved by moderator
- Ad rejected (with reason)
- Ad is now live
- Ad performance alerts
- Budget threshold warnings
- Campaign ending soon (3 days before)

### Moderator Notifications:
- New ad pending review
- Ad modified after rejection
- Priority ad submissions
- Flagged content alerts

## Best Practices for Companies

### Creating Effective Ads:
1. **Use High-Quality Images**
   - Clear, professional photos
   - Proper lighting and composition
   - No pixelation or blur

2. **Write Compelling Copy**
   - Clear value proposition
   - Strong call-to-action
   - Benefit-focused messaging

3. **Target Appropriately**
   - Match service to audience
   - Consider location/demographics
   - Use relevant categories

4. **Schedule Strategically**
   - Peak season timing
   - Avoid over-saturation
   - Test different durations

5. **Monitor & Optimize**
   - Check performance regularly
   - Pause underperforming ads
   - Adjust based on data

## Moderation Guidelines

### Approval Criteria:
✓ Clear, professional media
✓ Accurate service description
✓ Appropriate content
✓ No prohibited items/services
✓ Proper grammar and spelling
✓ Valid contact information
✓ Reasonable pricing claims

### Rejection Reasons:
✗ Low-quality images
✗ Misleading information
✗ Inappropriate content
✗ Policy violations
✗ Plagiarized content
✗ Excessive text in images
✗ Prohibited services

## Future Enhancements

### Planned Features:
1. **A/B Testing**
   - Test multiple ad variations
   - Automatic winner selection
   - Performance comparison

2. **Advanced Analytics**
   - Demographic breakdowns
   - Geographic heat maps
   - Time-based performance
   - Competitor analysis

3. **Automated Optimization**
   - Auto-adjust bidding
   - Smart scheduling
   - Budget pacing

4. **Retargeting**
   - Show ads to previous visitors
   - Conversion tracking
   - Custom audiences

5. **Bulk Upload**
   - Upload multiple ads at once
   - CSV import for batch campaigns
   - Template library

6. **Creative Studio**
   - Built-in image editor
   - Template gallery
   - Stock photo library
   - Video trimming tools

## Support & Help

### For Companies:
- Tutorial videos available
- Help documentation
- Live chat support
- Email: ads@fixlanka.com
- Phone: +94 11 234 5678

### For Moderators:
- Moderation guidelines manual
- Training videos
- Internal wiki
- Team lead support
- Slack channel: #ad-moderation

## Compliance & Legal

### Terms & Conditions:
- Advertisers must have rights to all content
- No copyright infringement
- No false advertising
- Comply with local advertising laws
- Platform reserves right to reject any ad
- Refund policy applies

### Data Privacy:
- Analytics data anonymized
- GDPR compliant
- User targeting restrictions
- No personal data selling
- Transparent data usage

## Performance Benchmarks

### Industry Averages:
- **Click-Through Rate (CTR)**: 2-5%
- **View Completion (Video)**: 60-80%
- **Conversion Rate**: 1-3%
- **Cost per Acquisition**: LKR 200-500

### Optimization Tips:
- CTR below 1%? → Update creative
- High views, low clicks? → Improve CTA
- High cost per click? → Refine targeting
- Low conversion? → Check landing page

---

**Last Updated:** October 22, 2025  
**Version:** 1.0  
**Maintained By:** FixLanka Development Team
