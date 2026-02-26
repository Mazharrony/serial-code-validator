# Serial Validator - Installation Guide

## Quick Start

1. **Upload Plugin**
   - Upload the `serial-validator` folder to `/wp-content/plugins/`
   - Or install via WordPress admin: Plugins > Add New > Upload Plugin

2. **Activate**
   - Go to Plugins in WordPress admin
   - Click "Activate" under Serial Validator

3. **Configure Settings**
   - Navigate to **Serial Validator > Settings**
   - Configure form fields, security, and messages
   - Save settings

4. **Import Codes**
   - Go to **Serial Validator > Import Codes**
   - Upload a CSV file with your serial codes
   - Or add codes individually in **Serial Validator > Codes**

5. **Add to Your Site**
   - **Shortcode**: Add `[serial_validator]` to any page/post
   - **Elementor**: Drag "Serial Validator" widget onto your page
   - Create a page at `/verify/` for QR codes

## Detailed Setup

### Step 1: Plugin Installation

**Via WordPress Admin:**
1. Go to Plugins > Add New
2. Click "Upload Plugin"
3. Choose the `serial-validator.zip` file
4. Click "Install Now"
5. Click "Activate Plugin"

**Via FTP:**
1. Extract the plugin zip file
2. Upload the `serial-validator` folder to `/wp-content/plugins/`
3. Go to Plugins in WordPress admin
4. Find "Serial Validator" and click "Activate"

### Step 2: Initial Configuration

After activation, go to **Serial Validator > Settings**:

**Form Settings:**
- ✅ Enable Name field (recommended)
- ⬜ Enable Email field (optional)
- ⬜ Enable Phone field (optional)

**Security Settings:**
- ✅ Enable rate limiting
- Set to 5 attempts per hour
- Optionally enable reCAPTCHA (get keys from Google)

**Messages:**
- Review and customize default messages
- Use clear, brand-appropriate language

**Leads:**
- Choose: Create leads on "valid only" or "all attempts"
- Recommended: Valid only (to reduce noise)

### Step 3: Adding Serial Codes

**Option A: CSV Import (Bulk)**

1. Prepare CSV file with columns:
   - `code` (required) - Your serial code
   - `product_name` (required) - Product name
   - `batch` (optional) - Batch identifier
   - `expiry_date` (optional) - YYYY-MM-DD format
   - `warranty_months` (optional) - Number
   - `status` (optional) - "active" or "blocked"

2. Go to **Serial Validator > Import Codes**
3. Upload your CSV file
4. Review import results
5. Check imported codes in **Serial Validator > Codes**

**Option B: Add Individually**

1. Go to **Serial Validator > Codes**
2. Fill in the "Add New Code" form:
   - Code (required)
   - Product Name (required)
   - Batch, Expiry Date, Warranty (optional)
   - Status: Active or Blocked
   - One-Time Use: Check for single verification
3. Click "Add Code"

### Step 4: Adding Form to Your Site

**Method 1: Shortcode (Works with any theme)**

1. Edit or create a page (e.g., "Verify Product")
2. Add the shortcode: `[serial_validator]`
3. Publish the page
4. Note the URL for QR codes

**Method 2: Elementor Widget (Requires Elementor)**

1. Edit a page with Elementor
2. Search for "Serial Validator" in widget panel
3. Drag widget onto page
4. Customize in widget settings:
   - Choose which fields to show
   - Adjust button colors
   - Set border radius
   - Customize message colors
5. Update the page

**Method 3: PHP Template (For developers)**

```php
<?php echo do_shortcode('[serial_validator]'); ?>
```

### Step 5: QR Code Setup (Optional)

1. Create a dedicated page at `/verify/` with the form
2. Generate QR codes pointing to:
   ```
   https://yourdomain.com/verify/?code=ABC123
   ```
3. Use any QR code generator (free online tools available)
4. Print QR codes on products or packaging
5. When scanned, the form auto-fills with the code

**Recommended QR Generators:**
- QR Code Generator (qr-code-generator.com)
- QR Code Monkey (qrcode-monkey.com)
- Any QR API with dynamic data

### Step 6: Testing

1. Go to your verification page
2. Test with a valid code from your database
3. Test with an invalid code
4. Test with a used code (if one-time use enabled)
5. Test rate limiting (attempt 6+ verifications quickly)
6. Check **Serial Validator > Dashboard** for statistics
7. Check **Serial Validator > Leads** for collected data

## Advanced Configuration

### reCAPTCHA Setup

1. Go to [Google reCAPTCHA](https://www.google.com/recaptcha/admin)
2. Register your site
3. Choose reCAPTCHA v2 "I'm not a robot" checkbox
4. Add your domain
5. Copy Site Key and Secret Key
6. Paste in **Serial Validator > Settings > Security**
7. Enable reCAPTCHA
8. Save settings

### Customizing Messages

Go to **Serial Validator > Settings > Messages**:

- **Valid Code**: "Authentic Product! This code is valid."
- **Invalid Code**: "Code not found. Please check and try again."
- **Already Used**: "This code has already been verified."
- **Blocked Code**: "This code is not valid. Please contact support."
- **Rate Limit**: "Too many attempts. Please try again later."

### Custom Styling

Add custom CSS in Appearance > Customize > Additional CSS:

```css
/* Customize button */
.sv-submit-btn {
    background: #ff6600 !important;
    font-size: 18px !important;
}

/* Customize success message */
.sv-result.sv-success {
    background: #e8f5e9 !important;
    border-color: #4caf50 !important;
}
```

## Troubleshooting

**Codes not importing:**
- Check CSV format matches example
- Ensure no duplicate codes in file
- Verify columns are named correctly
- Check file encoding (UTF-8 recommended)

**Form not displaying:**
- Check shortcode spelling: `[serial_validator]`
- Verify plugin is activated
- Clear cache if using caching plugin
- Check for JavaScript errors in browser console

**reCAPTCHA not showing:**
- Verify Site Key is correct
- Check domain is registered with Google
- Ensure reCAPTCHA is enabled in settings
- Clear browser cache

**Statistics not updating:**
- Verifications are recorded in real-time
- Refresh dashboard page
- Check database tables exist: `wp_sv_verifications`

**Elementor widget missing:**
- Ensure Elementor is installed and activated
- Try deactivating and reactivating Serial Validator
- Clear Elementor cache: Elementor > Tools > Regenerate CSS

## Multisite Installation

For WordPress Multisite:

1. Network activate or per-site activate
2. Each site has its own codes and verifications
3. Settings are per-site
4. Database tables use site-specific prefix

## Uninstallation

**To remove all data:**
1. Deactivate the plugin
2. Delete the plugin through WordPress admin
3. This will:
   - Drop all database tables
   - Delete all settings
   - Remove all transients

**To preserve data:**
- Just deactivate (don't delete)
- Data remains in database for reactivation

## Next Steps

After setup:
1. ✅ Import or add serial codes
2. ✅ Add form to website
3. ✅ Generate QR codes (optional)
4. ✅ Configure custom messages
5. ✅ Enable security features
6. ✅ Test verification process
7. ✅ Monitor dashboard for insights
8. ✅ Export leads as needed

## Support

Need help? Check:
- README.md for technical details
- Settings tooltips in admin
- WordPress support forum
- Plugin documentation

---

**Ready to go!** Your Serial Validator plugin is now installed and configured.
